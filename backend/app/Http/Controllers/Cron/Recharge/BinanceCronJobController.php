<?php

namespace App\Http\Controllers\Cron\Recharge;

use App\Http\Controllers\Controller;
use App\Library\BinancePay;
use App\Models\Currency;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BinanceCronJobController extends Controller
{
    /**
     * Tự động kiểm tra và duyệt nạp tiền qua Binance Pay API chính thức
     * URL: /cron/recharge/binance
     */
    /**
     * Tự động kiểm tra và duyệt nạp tiền qua Binance Pay API chính thức
     * URL: /cron/recharge/binance
     */
    public function index(Request $request)
    {
        $apiKey = (string) s('binance_api_key');
        $apiSecret = (string) s('binance_secret_key');
        if ($apiKey === '' || $apiSecret === '') {
            return response()->json(['status' => 'error', 'message' => 'Binance API Key hoặc Secret Key chưa được cấu hình']);
        }

        $response = (new BinancePay($apiKey, $apiSecret))
            ->getPayHistory(now()->subDay()->getTimestampMs(), null, 100);
        if (($response['status'] ?? null) !== 'success' || !is_array($response['data'] ?? null)) {
            return response()->json(['status' => 'error', 'message' => $response['message'] ?? 'Binance API response không hợp lệ']);
        }
        $rows = $response['data'];

        // Lấy danh sách các hóa đơn Binance đang chờ duyệt
        $pendingRecharges = Recharge::where('type', 'binance')
            ->where('status', 'waiting')
            ->where('domain', $request->getHost())
            ->get();

        if ($pendingRecharges->isEmpty()) {
            return response()->json(['status' => 'success', 'message' => 'No pending Binance recharges']);
        }

        $processedCount = 0;
        $debugTrace = [];
        foreach ($pendingRecharges as $recharge) {
            try {
                // Chúng ta sẽ tạm thời lưu log vào một biến để trả về kết quả JSON
                $matchingLogs = [];
                $matchingRow = $this->findMatchingTransaction($recharge->transaction_id, $rows, $matchingLogs);
                
                if ($matchingRow) {
                    if ($this->completeRecharge($recharge, $this->extractAmount($matchingRow), $this->getUsdtExchangeRate(), $matchingRow)) {
                        $processedCount++;
                        $debugTrace[] = "Recharge {$recharge->id}: Matched and completed.";
                    }
                } else {
                    $debugTrace[] = [
                        'recharge_id' => $recharge->id,
                        'target_id' => $recharge->transaction_id,
                        'matching_logs' => $matchingLogs,
                        'rows_fetched' => count($rows)
                    ];
                }
            } catch (\Throwable $e) {
                $debugTrace[] = "Error processing {$recharge->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Processed $processedCount Binance recharges",
            'total_pending' => $pendingRecharges->count(),
            'rows_fetched' => count($rows),
            'debug' => $debugTrace
        ]);
    }

    protected function extractAmount($matchingRow)
    {
        foreach (($matchingRow['fundsDetail'] ?? []) as $fund) {
            if (strtoupper((string) ($fund['currency'] ?? '')) === 'USDT') {
                return abs((float) ($fund['amount'] ?? 0));
            }
        }
        $amountRaw = $matchingRow['amount'] ?? $matchingRow['CreditAmount'] ?? $matchingRow['amount_usdt'] ?? 0;
        return abs(floatval(preg_replace('/[^0-9.]/', '', $amountRaw)));
    }

    /**
     * Xử lý kiểm tra cho một hóa đơn cụ thể
     */
    public function processSingleRecharge($recharge, $rows = null)
    {
        if (!$rows) {
            $apiKey = (string) s('binance_api_key');
            $apiSecret = (string) s('binance_secret_key');
            if ($apiKey === '' || $apiSecret === '') return false;

            $response = (new BinancePay($apiKey, $apiSecret))
                ->getPayHistory(now()->subDay()->getTimestampMs(), null, 100);
            if (($response['status'] ?? null) !== 'success' || !is_array($response['data'] ?? null)) return false;
            $rows = $response['data'];
        }

        $matchingRow = $this->findMatchingTransaction($recharge->transaction_id, $rows);

        if ($matchingRow) {
            $exchangeRate = $this->getUsdtExchangeRate();
            // $matchingRow['amount'] có thể là chuỗi "+1 USDT" hoặc số
            $apiAmount = $this->extractAmount($matchingRow);
            
            if ($apiAmount > 0) {
                return $this->completeRecharge($recharge, $apiAmount, $exchangeRate, $matchingRow);
            }
        }

        return false;
    }

    /**
     * Tìm giao dịch khớp trong danh sách API
     */
    protected function findMatchingTransaction($targetId, $rows, &$logs = [])
    {
        $targetId = trim((string)$targetId);
        $logs[] = "Searching for Transaction ID: $targetId";
        
        foreach ($rows as $row) {
            // Kiểm tra các trường mã giao dịch có thể có
            $apiId = $row['transactionId'] ?? $row['tranId'] ?? $row['transId'] ?? $row['magiaodich'] ?? $row['mã_giao_dịch'] ?? $row['tran_id'] ?? null;
            
            // Nếu không tìm thấy bằng key, thử tìm trong toàn bộ row (nếu targetId xuất hiện)
            if (!$apiId) {
                foreach ($row as $key => $val) {
                    if (is_scalar($val) && !empty($val)) {
                        $valStr = trim((string)$val);
                        // Thử cả so sánh trực tiếp và so sánh sau khi làm sạch scientific notation
                        if ($valStr === $targetId || str_contains($valStr, $targetId)) {
                            $apiId = $val;
                            $logs[] = "Found match in field '$key': $valStr";
                            break;
                        }
                        
                        // Chống scientific notation cho số lớn
                        if (is_numeric($val) && strlen($valStr) > 10) {
                            $cleanVal = trim(sprintf('%.0f', (float)$val));
                            if ($cleanVal === $targetId) {
                                $apiId = $val;
                                $logs[] = "Found scientific notation match in field '$key': $cleanVal";
                                break;
                            }
                        }
                    }
                }
            }

            // Chuẩn hóa apiId để so sánh
            if ($apiId) {
                $apiIdStr = trim((string)$apiId);
                $apiIdClean = is_numeric($apiId) ? trim(sprintf('%.0f', (float)$apiId)) : $apiIdStr;

                if ($apiIdStr === $targetId || $apiIdClean === $targetId || str_contains($apiIdStr, $targetId)) {
                    // Kiểm tra trạng thái: Thành công, Success, CONFIRMED, hoặc type là BINANCE_PAY
                    $status = strtoupper(trim($row['status'] ?? $row['trạng_thái'] ?? 'SUCCESS'));
                    $type = strtoupper(trim($row['type'] ?? $row['loại_giao_dịch'] ?? ''));
                    
                    $logs[] = "ID Match found. Status: $status, Type: $type";

                    if (in_array($status, ['CONFIRMED', 'SUCCESS', 'THÀNH CÔNG', 'THANH CONG', 'COMPLETED']) || 
                        strpos($type, 'PAY') !== false || 
                        strpos($type, 'NHẬN') !== false ||
                        strpos($type, 'SUCCESS') !== false) {
                        return $row;
                    } else {
                        $logs[] = "WARNING: ID Matched but status/type not accepted. Row: " . json_encode($row);
                    }
                }
            }
        }
        $logs[] = "No match found for $targetId";
        return null;
    }

    /**
     * Hoàn tất nạp tiền
     */
    protected function completeRecharge($recharge, $apiAmount, $exchangeRate, $matchingRow)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $recharge->user_id)->lockForUpdate()->first();
            if (!$user) {
                DB::rollBack();
                return false;
            }

            $realAmountVnd = $apiAmount * $exchangeRate;
            
            $bonusData = $this->calculateBonus($realAmountVnd);
            $bonusAmount = $bonusData['bonusAmount'];
            $totalAdd = $realAmountVnd + $bonusAmount;

            $recharge->update([
                'status' => 'completed',
                'real_amount' => $totalAdd,
                'bonus' => $bonusAmount,
                'description' => "Nạp tiền tự động qua Binance Pay - Mã: " . $recharge->transaction_id
            ]);

            $balanceBefore = $user->balance;
            $user->balance += $totalAdd;
            $user->total_deposit += $totalAdd;
            $user->save();
            
            if (method_exists($user, 'upgradeLevel')) {
                $user->upgradeLevel($user->total_deposit);
            }

            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => "BINANCE_" . strtoupper(Str::random(10)),
                'type' => 'add',
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'amount' => $totalAdd,
                'description' => "Nạp tiền tự động qua Binance Pay (USDT) - Mã: " . $recharge->transaction_id,
                'status' => 'success',
                'domain' => $recharge->domain
            ]);

            DB::commit();

            try {
                $this->senderTelegram($user, $recharge, "Binance Pay");
            } catch (\Throwable $t) {
                Log::error("Binance Telegram notify failed: " . $t->getMessage());
            }

            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Binance Recharge processing error: " . $e->getMessage() . " -- Recharge ID: " . $recharge->id);
            return false;
        }
    }

    /**
     * Lấy tỷ giá USDT -> VNĐ
     */
    protected function getUsdtExchangeRate()
    {
        $currencyVnd = Currency::where('code', 'VND')->first();
        return $currencyVnd->exchange_rate ?? s('rate_usdt') ?? 23000;
    }

    /**
     * Tính toán Bonus nạp tiền
     */
    protected function calculateBonus($amountVnd)
    {
        $bonusAmount = 0;
        $bonus = \App\Models\RechargeBonus::where('status', 'active')
            ->where('domain', request()->getHost())
            ->where('type', 'binance')
            ->orderBy('min_amount', 'desc')
            ->get();

        $amountUsd = $amountVnd / $this->getUsdtExchangeRate();

        foreach ($bonus as $item) {
            if ($amountUsd >= $item->min_amount) {
                $bonusAmount = ($amountVnd * $item->bonus) / 100;
                break;
            }
        }

        return ['bonusAmount' => $bonusAmount];
    }

    /**
     * Gửi thông báo Telegram
     */
    protected function senderTelegram($user, $recharge, $bank)
    {
        $text = "💰 <b>THÔNG BÁO NẠP TIỀN THÀNH CÔNG</b> 💰\n";
        $text .= "🌐 <b>Website:</b> " . htmlspecialchars($recharge->domain) . "\n";
        $text .= "🏦 <b>Hình thức:</b> $bank\n";
        $text .= "👤 <b>Tên tài khoản:</b> " . htmlspecialchars($user->username) . "\n";
        $text .= "💵 <b>Số tiền:</b> " . number_format($recharge->real_amount) . " VNĐ\n";
        $text .= "📝 <b>Mã giao dịch:</b> " . htmlspecialchars($recharge->transaction_id) . "\n";
        $text .= "⏰ <b>Thời gian:</b> " . now()->format('d/m/Y H:i:s') . "\n";
        $text .= "✅ <b>Trạng thái:</b> Hoàn tất tự động\n";
        $text .= "──────────────────────";
        
        if (function_exists('send_telegram')) {
            send_telegram($text, $user->domain);
        }
    }

}
