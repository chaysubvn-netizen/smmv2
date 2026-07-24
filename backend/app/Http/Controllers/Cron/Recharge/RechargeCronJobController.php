<?php

namespace App\Http\Controllers\Cron\Recharge;

use App\Http\Controllers\Controller;
use App\Models\AffiliateRef;
use App\Models\Currency;
use App\Models\Recharge;
use App\Models\RechargeBonus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RechargeCronJobController extends Controller
{
    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        return str_starts_with($host, 'api.') ? substr($host, 4) : $host;
    }

    public function bank(Request $request)
    {
        // Cleanup expired recharges
        Recharge::where('status', 'waiting')
            ->where('expired_at', '<', now())
            ->where('domain', $this->siteHost($request))
            ->update(['status' => 'failed']);

        $bank = strtolower($request->get('bank'));
        $currencyVND = Currency::where('code', 'VND')->first();
        $exchangeRateVND = $currencyVND->exchange_rate ?? 23000;

        $transferCode = s("transfer_code");
        $transferCode = $transferCode ? strtolower($transferCode) : null;
        $promotion_min = s("promotion_min") ?? 1;
        $promotion_min = convert_currency($promotion_min, "VND");

        $bankApis = [
            'acb' => [
                'url' => "https://api.spay5s.com/historyapiacb/" . (s("acb_api_key") ?? ""),
                'data_key' => 'data',
                'success_condition' => function ($res) {
                    return isset($res['codeStatus']) && $res['codeStatus'] == 200;
                },
                'method' => 'ACB',
                'amount_processor' => function ($amount) {
                    return preg_replace('/[^0-9]/', '', (string)$amount);
                }
            ],
            'mbbank' => [
                'url' => "https://api.spay5s.com/historymbbank/" . s("mb_api_key"),
                'data_key' => 'TranList',
                'success_condition' => function ($res) {
                    return isset($res['TranList']) && isset($res['status']) && $res['status'] == 'success';
                },
                'method' => 'MBBANK',
                'amount_processor' => function ($amount) {
                    return preg_replace('/[^0-9]/', '', (string)$amount);
                }
            ],
            'vcb' => [
                'url' => "https://api.spay5s.com/historyapivcb/" . s("vcb_api_key"),
                'data_key' => 'transactions',
                'success_condition' => function ($res) {
                    return isset($res['transactions']) && isset($res['des']) && $res['des'] == "success";
                },
                'method' => 'VCB',
                'amount_processor' => function ($amount) {
                    return preg_replace('/[^0-9]/', '', (string)$amount);
                }
            ],
            'viettinbank' => [
                'url' => "https://api.spay5s.com/historyapiviettin/" . s("vtb_api_key"),
                'data_key' => 'transactions',
                'success_condition' => function ($res) {
                    return isset($res['transactions']);
                },
                'method' => 'VIETTINBANK',
                'amount_processor' => function ($amount) {
                    return preg_replace('/[^0-9]/', '', (string)$amount);
                }
            ]
        ];

        // RunAll invokes this job without a bank parameter. Process every
        // configured bank so OCB (and the other banks) are actually polled.
        if (!$bank) {
            $results = [];
            foreach (array_merge(['ocb'], array_keys($bankApis)) as $bankName) {
                $bankRequest = clone $request;
                $bankRequest->merge(['bank' => $bankName]);
                $response = $this->bank($bankRequest);
                $results[$bankName] = is_object($response) && method_exists($response, 'getData')
                    ? $response->getData(true)
                    : ['status' => 'success'];
            }
            $hasError = collect($results)->contains(fn ($result) => ($result['status'] ?? null) === 'error');
            return response()->json([
                'status' => $hasError ? 'error' : 'success',
                'message' => $hasError ? 'Có ngân hàng kiểm tra thất bại.' : 'Đã kiểm tra các ngân hàng.',
                'banks' => $results,
            ]);
        }

        if ($bank === 'ocb') {
            $result = $this->handleOcb($request, $transferCode, $promotion_min);
            return response()->json($result, ($result['status'] ?? null) === 'success' ? 200 : 502);
        }

        if (!isset($bankApis[$bank])) {
            return response()->json(['status' => 'error', 'message' => 'Ngân hàng không được hỗ trợ.'], 422);
        }

        $apiConfig = $bankApis[$bank];
        $response = $this->curl($apiConfig['url']);
        if (!$apiConfig['success_condition']($response)) {
            $message = $response['msg'] ?? $response['message'] ?? 'API ngân hàng trả dữ liệu không hợp lệ.';
            Log::warning('Bank recharge API failed', ['bank' => $bank, 'message' => $message, 'keys' => is_array($response) ? array_keys($response) : []]);
            return response()->json(['status' => 'error', 'bank' => $bank, 'message' => $message], 502);
        }

        $items = $apiConfig['data_key'] === null
            ? $response
            : ($response[$apiConfig['data_key']] ?? []);

        foreach ($items as $item) {
            $this->processTransaction($request, $item, $apiConfig, $transferCode, $promotion_min);
        }
        return response()->json([
            'status' => 'success',
            'bank' => $bank,
            'message' => 'Đã kiểm tra '.$apiConfig['method'].'.',
            'transactions_received' => count($items),
        ]);
    }

    protected function handleOcb($request, $transferCode, $promotion_min)
    {
        $username = s('ocb_username');
        $password = s('ocb_password');
        $accountNo = s('ocb_account_number');

        if (!$username || !$password || !$accountNo) {
            return ['status' => 'error', 'bank' => 'ocb', 'message' => 'OCB chưa được cấu hình đầy đủ.'];
        }

        $ocb = new \App\Library\OCB();
        $loginRes = json_decode($ocb->login_ocb($username, $password), true);
        Log::info('OCB cron login result', ['status' => $loginRes['status'] ?? null, 'message' => $loginRes['msg'] ?? null, 'raw' => isset($loginRes['raw']) ? substr((string) $loginRes['raw'], 0, 500) : null]);
        if (!isset($loginRes['accessToken'])) {
            return ['status' => 'error', 'bank' => 'ocb', 'message' => $loginRes['msg'] ?? $loginRes['message'] ?? 'Đăng nhập API OCB thất bại.'];
        }

        $lsgdJson = $ocb->LSGD($accountNo, 20, $loginRes['accessToken']);
        $lsgd = json_decode($lsgdJson, true);
        if (!is_array($lsgd)) {
            return ['status' => 'error', 'bank' => 'ocb', 'message' => 'API OCB trả lịch sử không hợp lệ.'];
        }
        $elements = $this->ocbElements($lsgd);
        if ($elements === []) {
            $ocb->get_balance($loginRes['accessToken'], $accountNo);
            $retry = json_decode($ocb->LSGD($accountNo, 50, $loginRes['accessToken']), true);
            if (is_array($retry)) {
                $lsgd = $retry;
                $elements = $this->ocbElements($retry);
            }
        }

        Log::info('OCB cron history result', ['keys' => array_keys($lsgd), 'elements' => count($elements)]);
        if ($elements === [] && isset($lsgd['code'])) {
            return [
                'status' => 'error',
                'bank' => 'ocb',
                'message' => $lsgd['msg'] ?? $lsgd['message'] ?? 'OCB history request failed.',
            ];
        }
        $matchedRecharges = 0;
        $creditedRecharges = 0;
        foreach ($elements as $element) {
            $attrs = $element['attributes'] ?? $element;
            Log::info('OCB transaction received', ['element' => $element]);
            $mapped = [
                'transactionNumber' => $attrs['reference'] ?? ($element['id'] ?? null),
                'description' => $this->ocbDescription($attrs),
                'amount' => $attrs['transactionAmountCurrency']['amount'] ?? ($attrs['baseCurrencyAmount']['value'] ?? ($attrs['amount']['value'] ?? ($attrs['baseAmount']['value'] ?? 0))),
                'type' => $attrs['creditDebitIndicator'] ?? 'C'
            ];

            $apiConfig = [
                'method' => 'OCB',
                'amount_processor' => fn ($amount) => $amount,
            ];
            $pendingRecharge = null;
            if (preg_match_all('/([A-Z0-9]{8})/', strtoupper($mapped['description']), $matches)) {
                $pendingRecharge = Recharge::whereIn('transaction_id', array_unique($matches[1]))
                    ->where('status', 'waiting')
                    ->first();
                if ($pendingRecharge) {
                    $matchedRecharges++;
                }
            }
            $this->processTransaction($request, $mapped, $apiConfig, $transferCode, $promotion_min);
            if ($pendingRecharge?->fresh()?->status === 'completed') {
                $creditedRecharges++;
            }
        }

        return [
            'status' => 'success',
            'bank' => 'ocb',
            'message' => 'Đã kiểm tra API OCB gốc.',
            'transactions_received' => count($elements),
            'matched_recharges' => $matchedRecharges,
            'credited_recharges' => $creditedRecharges,
        ];
    }

    private function ocbElements(array $payload): array
    {
        if (array_is_list($payload)) {
            return $payload;
        }

        foreach (['elements', 'transactions', 'content', 'items'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
            if (isset($payload['data'][$key]) && is_array($payload['data'][$key])) {
                return $payload['data'][$key];
            }
        }

        return [];
    }

    private function ocbDescription(array $attrs): string
    {
        foreach (['description', 'remark', 'reference', 'transactionDescription', 'counterpartyDescription'] as $key) {
            $value = $attrs[$key] ?? null;
            if (is_string($value) && trim($value) !== '') return $value;
            if (is_array($value)) {
                foreach (['value', 'content', 'description'] as $nested) {
                    if (isset($value[$nested]) && is_scalar($value[$nested])) return (string) $value[$nested];
                }
            }
        }
        return '';
    }

    protected function processTransaction($request, $item, $apiConfig, $transferCode, $promotion_min)
    {
        // Lấy amount raw và transactionNumber ở nhiều trường khác nhau
        $creditAmount = $item['creditAmount'] ?? $item['CreditAmount'] ?? null;
        $debitAmount = $item['debitAmount'] ?? $item['DebitAmount'] ?? null;
        // 1. Kiểm tra debitAmount nếu có (Tiền ra)
        if ($debitAmount !== null && $this->normalizeAmount($debitAmount) > 0) {
            return;
        }

        // 2. Kiểm tra creditAmount nếu có (Tiền vào). Nếu có trường này mà nó <= 0 thì bỏ qua.
        if ($creditAmount !== null && $this->normalizeAmount($creditAmount) <= 0) {
            return;
        }

        // 3. Kiểm tra type/dorc (D: Debit, C: Credit)
        $type = $item['creditDebitIndicator'] ?? $item['type'] ?? $item['Type'] ?? $item['dorc'] ?? $item['CD'] ?? $item['transactionType'] ?? null;
        if ($type !== null) {
            $typeUpper = strtoupper(trim((string)$type));
            if (in_array($typeUpper, ['D', 'DR', 'DBIT', 'DEBIT'], true)) {
                return;
            }
            $debitKeywords = ['DEBIT', 'OUT', 'THU_PHI', 'PHI', 'PHÍ', 'TRỪ', 'TIỀN RA', '(-)'];
            foreach ($debitKeywords as $kw) {
                if (str_contains($typeUpper, $kw)) return;
            }
            if (str_contains($typeUpper, '-')) return;
        }

        // 4. Ưu tiên lấy từ creditAmount nếu có, nếu không mới lấy amount chung
        $rawAmount = $creditAmount
            ?? ($item['transactionAmountCurrency']['amount'] ?? null)
            ?? $item['amount']
            ?? $item['Amount']
            ?? $item['sotien']
            ?? null;
        
        // 5. Kiểm tra dấu trừ trong rawAmount trước khi nó bị processor "làm sạch" (dành cho ACB và các bank chỉ trả về 1 trường amount có dấu)
        if ($rawAmount !== null && str_contains((string)$rawAmount, '-')) {
            return;
        }
        $transactionNumber = $item['transactionNumber'] ?? $item['reference'] ?? $item['refNo'] ?? $item['Reference'] ?? $item['trxId'] ?? $item['TransactionNo'] ?? $item['magiaodich'] ?? null;
        $description = strtoupper($item['description'] ?? $item['Description'] ?? $item['remark'] ?? $item['Remark'] ?? $item['noidung'] ?? '');
        $descriptionLower = strtolower($description);

        if (isset($apiConfig['amount_processor']) && $rawAmount !== null) {
            $rawAmount = $apiConfig['amount_processor']($rawAmount);
        }

        // Chuẩn hóa số tiền về float (loại bỏ ký tự không phải số)
        $amount = $this->normalizeAmount($rawAmount);
        if ($amount === null || $amount <= 0) {
            return;
        }

        // Ưu tiên tìm theo mã giao dịch mới (8 ký tự in hoa/số)
        // Dùng preg_match_all để tìm tất cả các chuỗi có thể là mã giao dịch
        if (preg_match_all('/([A-Z0-9]{8})/', $description, $matches)) {
            foreach ($matches[1] as $code) {
                Log::info('OCB recharge lookup', ['code' => $code, 'host' => $this->siteHost($request), 'rows' => Recharge::where('transaction_id', $code)->get(['id', 'transaction_id', 'status', 'domain'])->toArray()]);
                $recharge = Recharge::where('transaction_id', $code)
                    ->where('status', 'waiting')
                    ->first();

                if ($recharge) {
                    Log::info('OCB recharge matched', ['code' => $code, 'recharge_id' => $recharge->id, 'domain' => $recharge->domain]);
                    $this->handleNewSystemTransaction($request, $recharge, $amount, $transactionNumber, $apiConfig['method']);
                    return;
                }
            }
        }

        // Fallback về logic cũ: Tìm User ID trong mô tả
        // Nếu có transferCode từ admin, kiểm tra xem nó có trong mô tả không (nhưng không bắt buộc nếu tìm thấy ID)
        $idUser = $this->extractUserId($descriptionLower, $transferCode);
        
        if (!$idUser || $amount < $promotion_min) {
            return;
        }

        $user = User::where('id', $idUser)->where('domain', $this->siteHost($request))->first();
        if (!$user) {
            return;
        }

        // Kiểm tra sơ bộ (fast) nếu transactionNumber trống thì bỏ qua
        if (!$transactionNumber) {
            return;
        }

        // Chuyển sang xử lý an toàn (atomic)
        $this->handleSuccessfulTransaction($request, $user, $amount, $transactionNumber, $apiConfig['method'] ?? 'BANK');
    }

    protected function normalizeAmount($raw)
    {
        if ($raw === null) return null;

        // Nếu là số dạng string có dấu . hoặc , hoặc ký tự khác -> loại bỏ
        if (!is_numeric($raw)) {
            // loại bỏ tất cả ký tự không phải số hoặc dấu chấm/dấu phẩy
            $clean = preg_replace('/[^\d\.\,]/', '', (string)$raw);
            if ($clean === '') return null;

            // nếu có cả dấu chấm và dấu phẩy, cố gắng suy đoán: thường '.' là ngăn hàng nghìn, ',' là thập phân (VN có thể ngược)
            // đơn giản nhất: loại bỏ dấu ngăn hàng nghìn ('.' hoặc ',') nếu có nhiều hơn 1 occurrence
            $dots = substr_count($clean, '.');
            $commas = substr_count($clean, ',');
            if ($dots > 1 && $commas === 0) {
                $clean = str_replace('.', '', $clean);
            } elseif ($commas > 1 && $dots === 0) {
                $clean = str_replace(',', '', $clean);
            } elseif ($dots > 0 && $commas > 0) {
                // nếu cả hai cùng tồn tại, loại bỏ dấu ngăn hàng nghìn (dấu xuất hiện bên trái nhiều hơn)
                // ví dụ "1.234,56" -> remove '.' -> "1234,56" -> replace ',' with '.'
                if (strpos($clean, ',') > strpos($clean, '.')) {
                    $clean = str_replace('.', '', $clean);
                    $clean = str_replace(',', '.', $clean);
                } else {
                    $clean = str_replace(',', '', $clean);
                }
            } else {
                // chỉ có 1 dấu (',' hoặc '.')
                $clean = str_replace(',', '.', $clean); // unify
            }

            if (!is_numeric($clean)) return null;
            return (float)$clean;
        }

        return (float)$raw;
    }

    protected function handleNewSystemTransaction($request, $recharge, $amount, $transactionNumber, $method)
    {
        DB::beginTransaction();
        try {
            // Khóa bản ghi để tránh race condition
            $recharge = Recharge::where('id', $recharge->id)->lockForUpdate()->first();
            if (!$recharge || $recharge->status !== 'waiting') {
                DB::rollBack();
                return;
            }

            // Kiểm tra xem transactionNumber này đã được xử lý chưa (tránh trùng lặp từ phía bank API)
            $txnExists = Transaction::where('description', 'like', "%$transactionNumber%")
                ->where('domain', $this->siteHost($request))
                ->exists();

            if ($txnExists) {
                DB::rollBack();
                return;
            }

            $user = User::find($recharge->user_id);
            if (!$user) {
                DB::rollBack();
                return;
            }

            $amountUsd = convert_currency($amount, 'USD');
            $bonusData = $this->calculateBonus($request, $amount, $amountUsd);

            $bonusAmount = $bonusData['bonusAmount'];
            $realAmount  = $amount + $bonusAmount;

            // Cập nhật recharge hiện tại
            $recharge->update([
                'real_amount' => $realAmount,
                'bonus' => $bonusAmount,
                'request_id' => $transactionNumber, // Lưu mã giao dịch bank vào đây để đối soát
                'status' => 'completed',
            ]);

            // Cập nhật số dư user
            $balanceBefore = $user->balance;
            $user->balance += $realAmount;
            $user->total_deposit += $realAmount;
            $user->save();
            $user->upgradeLevel($user->total_deposit);

            // Tạo transaction cho user
            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => "TRANSFER_" . Str::random(10),
                'type' => 'add',
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'amount' => $realAmount,
                'description' => "Nạp tiền hóa đơn #{$recharge->transaction_id} qua $method - Mã GD Bank: $transactionNumber",
                'status' => 'success',
                'domain' => $this->siteHost($request)
            ]);

            // Hoa hồng
            $this->processAffiliateCommissionAtomic($request, $user, $amount, $transactionNumber);

            DB::commit();

            try {
                $this->senderTelegram($request, $user, $recharge, $method);
            } catch (\Throwable $t) {
                Log::error("Telegram send failed: " . $t->getMessage());
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("New System Recharge error: " . $e->getMessage() . " -- TXN: " . $transactionNumber);
            throw $e;
        }
    }

    protected function extractUserId($description, $transferCode = null)
    {
        // 1. Nếu có transferCode từ admin, ưu tiên tìm theo nó
        if ($transferCode) {
            if (preg_match('/' . preg_quote($transferCode, '/') . '[\s:]*([0-9]+)/i', $description, $matches)) {
                return $matches[1];
            }
        }

        // 2. Thử tìm với các prefix phổ biến (SMM, NAPTIEN, NAP, ID, CK)
        if (preg_match('/(?:SMM|NAPTIEN|NAP|ID|CK|BILL)[\s:]*([0-9]+)/i', $description, $matches)) {
            return $matches[1];
        }

        // 3. Tìm chuỗi số đơn lẻ trong mô tả (thường là User ID)
        // Ưu tiên chuỗi số có độ dài từ 1-7 chữ số (UserID thường không quá lớn)
        if (preg_match('/(?:\s|^)([0-9]{1,7})(?:\s|$)/', $description, $matches)) {
            return $matches[1];
        }

        // 4. Fallback cuối cùng: lấy chuỗi số bất kỳ
        if (preg_match('/([0-9]+)/', $description, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function handleSuccessfulTransaction($request, $user, $amount, $transactionNumber, $method)
    {
        // Atomic: dùng DB transaction để đảm bảo không bị double insert/race condition
        DB::beginTransaction();
        try {
            // Kiểm tra đã tồn tại trong recharges với khóa FOR UPDATE để tránh race condition
            $exists = Recharge::where('transaction_id', $transactionNumber)
                ->where('domain', $this->siteHost($request))
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                DB::rollBack();
                return;
            }

            // Double-check trong bảng transactions (phòng trường hợp dùng transaction_code tương ứng)
            $txnExists = Transaction::where('description', 'like', "%$transactionNumber%")
                ->where('domain', $this->siteHost($request))
                ->lockForUpdate()
                ->exists();

            if ($txnExists) {
                DB::rollBack();
                return;
            }

            $amountUsd = convert_currency($amount, 'USD');
            $bonusData = $this->calculateBonus($request, $amount, $amountUsd);

            $bonusAmount = $bonusData['bonusAmount'];
            $realAmount  = $amount + $bonusAmount;

            // Tạo recharge
            $recharge = Recharge::create([
                'user_id' => $user->id,
                'transaction_id' => $transactionNumber,
                'request_id' => $transactionNumber,
                'method' => $method,
                'type' => 'bank',
                'amount' => $amount,
                'real_amount' => $realAmount,
                'bonus' => $bonusAmount,
                'description' => "Nạp tiền qua ngân hàng $method - $transactionNumber - Bonus: {$bonusAmount}",
                'status' => 'completed',
                'domain' => $this->siteHost($request)
            ]);

            // Cập nhật số dư user
            $balanceBefore = $user->balance;
            $user->balance += $realAmount;
            $user->total_deposit += $realAmount;
            $user->save();
            $user->upgradeLevel($user->total_deposit);

            // Tạo transaction cho user
            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => "TRANSFER_" . Str::random(10),
                'type' => 'add',
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance,
                'amount' => $realAmount,
                'description' => "Nạp tiền qua ngân hàng $method - $transactionNumber - Bonus: {$bonusAmount}",
                'status' => 'success',
                'domain' => $this->siteHost($request)
            ]);

            // Xử lý hoa hồng giới thiệu trong cùng transaction (atomic)
            $this->processAffiliateCommissionAtomic($request, $user, $amount, $transactionNumber);

            DB::commit();

            // Gửi Telegram (ngoài transaction để không block DB nếu sender lỗi)
            try {
                $this->senderTelegram($request, $user, $recharge, $method);
            } catch (\Throwable $t) {
                Log::error("Telegram send failed: " . $t->getMessage());
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Recharge processing error: " . $e->getMessage() . " -- TXN: " . $transactionNumber);
        }
    }

    protected function calculateBonus(Request $request, $amount, $amountUsd)
    {
        $bonuses = RechargeBonus::where('domain', $this->siteHost($request))
            ->where('type', 'banking')
            ->where('status', 'active')
            ->get();

        $selectedBonus = null;
        foreach ($bonuses as $item) {
            if ($item->min_amount <= $amountUsd) {
                if (!$selectedBonus || $item->min_amount > $selectedBonus->min_amount) {
                    $selectedBonus = $item;
                }
            }
        }

        if ($selectedBonus) {
            // Tính bonus dựa trên số tiền gốc (amount) để cùng đơn vị tiền tệ (VND)
            $bonusAmount = ($selectedBonus->bonus * $amount) / 100;
        } else {
            $bonusAmount = 0;
        }

        return [
            'bonus' => $selectedBonus,
            'bonusAmount' => $bonusAmount
        ];
    }

    /**
     * Xử lý hoa hồng giới thiệu (được thiết kế để chạy bên trong DB::transaction)
     */
    protected function processAffiliateCommissionAtomic($request, $user, $amount, $transactionNumber)
    {
        $affiliatePercent = floatval(s('affiliate_percent'));
        if (!$affiliatePercent || $affiliatePercent <= 0) {
            return;
        }

        $userRef = AffiliateRef::where('user_id', $user->id)->with('refUser')->first();
        if (!$userRef || !$userRef->refUser) {
            return;
        }

        $commission = ($amount * $affiliatePercent) / 100;
        $referralUser = $userRef->refUser;

        // Cập nhật hoa hồng và tạo transaction cho ref user
        $balanceBeforeRef = $referralUser->balance;
        $referralUser->balance += $commission;
        $referralUser->commission += $commission;
        $referralUser->save();

        Transaction::create([
            'user_id' => $referralUser->id,
            'transaction_code' => "REF_COM_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $balanceBeforeRef,
            'balance_after' => $referralUser->balance,
            'amount' => $commission,
            'description' => "Hoa hồng giới thiệu nạp tiền qua ngân hàng - $transactionNumber",
            'status' => 'success',
            'domain' => $this->siteHost($request)
        ]);

        // Cập nhật AffiliateRef thống kê (tăng total_deposit và commission)
        $userRef->total_deposit += $amount;
        $userRef->commission += $commission;
        $userRef->save();
    }

    public function senderTelegram(Request $request, $user, $recharge, $bank)
    {
        $text = "💰 <b>THÔNG BÁO NẠP TIỀN THÀNH CÔNG</b> 💰\n";
        $text .= "🌐 <b>Website:</b> " . htmlspecialchars($this->siteHost($request)) . "\n";
        $text .= "🏦 <b>Ngân hàng:</b> $bank\n";
        $text .= "👤 <b>Tên tài khoản:</b> " . htmlspecialchars($user->username) . "\n";
        $text .= "💵 <b>Số tiền:</b> " . number_format($recharge->real_amount) . " VNĐ\n";
        $text .= "📝 <b>Ghi chú:</b> " . htmlspecialchars($recharge->description) . "\n";
        $text .= "⏰ <b>Thời gian:</b> " . $recharge->created_at->format('d/m/Y H:i:s') . "\n";
        $text .= "✅ <b>Trạng thái:</b> " . htmlspecialchars($recharge->status) . "\n";
        $text .= "──────────────────────";
        send_telegram($text, $user->domain);
    }

    public function curl($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
