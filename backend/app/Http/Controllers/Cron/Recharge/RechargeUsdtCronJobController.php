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

class RechargeUsdtCronJobController extends Controller
{
    public function index(Request $request)
    {
        try {

            $requiredParams = ['request_id', 'merchant_id', 'api_key', 'received', 'status'];
            if (array_diff($requiredParams, array_keys($request->all()))) {
                return $this->errorResponse('Invalid request');
            }

            $data = $request->only($requiredParams + ['amount', 'from_address', 'transaction_id']);
            $config = $this->getUsdtConfig();

            if (!$this->validateMerchant($data['merchant_id'], $data['api_key'], $config)) {
                return $this->errorResponse('Invalid merchant ID or API key', 400);
            }

            $recharge = Recharge::where('request_id', $data['request_id'])
                ->where('status', 'waiting')
                ->first();

            if (!$recharge) {
                return $this->errorResponse('Recharge not found or already processed', 404);
            }

            $amountUsd = convert_currency($recharge->amount, "USD");

            // if ($data['amount'] != $amountUsd) {
            //     return $this->errorResponse('Received amount does not match. ' . $amountUsd . ' ' . $data['amount'], 400);
            //     // return $this->errorResponse('Số lượng USDT thực nhận không khớp với số lượng USDT lúc tạo hóa đơn', 400);
            // }

            return $this->processStatus($data['status'], $data, $recharge);
        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi: ' . $e->getMessage(), 500);
        }
    }

    protected function getUsdtConfig()
    {
        return [
            'merchant_id' => s("usdt_wallet") ?? "",
            'api_key' => s("usdt_wallet_token")
        ];
    }

    protected function validateMerchant($merchantId, $apiKey, $config)
    {
        return $merchantId === $config['merchant_id'] && $apiKey === $config['api_key'];
    }

    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }

    protected function processStatus($status, $data, $recharge)
    {
        switch ($status) {
            case 'waiting':
                $recharge->update(['status' => $status]);
                break;
            case 'expired':
                $recharge->update(['status' => $status]);
                break;
            case 'completed':
                $this->processCompletedTransaction($data, $recharge);
                break;
            default:
                $recharge->update(['status' => 'failed']);
                return $this->errorResponse('Invalid status', 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Giao dịch đã được xử lý thành công.'
        ]);
    }

    protected function processCompletedTransaction($data, $recharge)
    {
        $recharge->update([
            'status' => 'completed',
            'transaction_id' => $data['transaction_id'] ?? null
        ]);

        $user = $recharge->user;
        if (!$user) return;

        $currencyVnd = Currency::where('code', 'VND')->first();
        if (!$currencyVnd) return;

        $amountVnd = $data['received'] * $currencyVnd->exchange_rate;

        // Xử lý bonus
        $bonusAmount = $this->processBonus($user, $data['received'], $currencyVnd);

        $recharge->update([
            'real_amount' => $amountVnd + $bonusAmount,
            'bonus' => $bonusAmount,
        ]);

        // Cộng tiền chính
        $this->addUserBalance($user, $amountVnd, $data, $bonusAmount);

        // Xử lý affiliate
        $this->processAffiliate($user, $amountVnd, $data['received']);

        // Gửi thông báo
        $this->sendNotification($user, $data, $recharge);
    }

    protected function processBonus($user, $receivedUsdt, $currencyVnd)
    {
        $bonus = RechargeBonus::where('domain', $user->domain)
            ->where('type', 'usdt')
            ->where('min_amount', '<=', $receivedUsdt)
            ->where('status', 'active')
            ->orderBy('min_amount', 'desc')
            ->first();

        if (!$bonus) return 0;

        $bonusAmount = $receivedUsdt * $bonus->bonus / 100;
        $bonusAmountVnd = $bonusAmount * $currencyVnd->exchange_rate;

        $user->increment('balance', $bonusAmountVnd);
        $user->increment('total_deposit', $bonusAmountVnd);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => "USDT_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $user->balance - $bonusAmountVnd,
            'balance_after' => $user->balance,
            'amount' => $bonusAmountVnd,
            'description' => 'Thưởng nạp tiền từ ví USDT: ' . $receivedUsdt . ' USDT',
            'status' => 'success',
            'domain' => $user->domain,
        ]);

        return $bonusAmount;
    }

    protected function addUserBalance($user, $amountVnd, $data, $bonusAmount)
    {
        $user->increment('balance', $amountVnd);
        $user->increment('total_deposit', $amountVnd);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => "USDT_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $user->balance - $amountVnd,
            'balance_after' => $user->balance,
            'amount' => $amountVnd,
            'description' => 'Nạp tiền từ ví USDT: ' . $data['received'] . ' USDT',
            'status' => 'success',
            'domain' => $user->domain,
        ]);
    }

    protected function processAffiliate($user, $amountVnd, $receivedUsdt)
    {
        $affiliatePercent = s('affiliate_percent');
        if ($affiliatePercent === null || $affiliatePercent <= 0) return;

        $userRef = AffiliateRef::where('user_id', $user->id)
            ->with('refUser')
            ->first();

        if (!$userRef || !$userRef->refUser) return;

        $commission = ($amountVnd * $affiliatePercent) / 100;
        $referralUser = $userRef->refUser;

        $referralUser->increment('commission', $commission);
        $userRef->increment('commission', $commission);
        $userRef->increment('total_deposit', $amountVnd);

        Transaction::create([
            'user_id' => $referralUser->id,
            'transaction_code' => "USDT_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $referralUser->commission - $commission,
            'balance_after' => $referralUser->commission,
            'amount' => $commission,
            'description' => 'Hoa hồng từ nạp tiền USDT: ' . $receivedUsdt . ' USDT',
            'status' => 'success',
            'domain' => $user->domain,
        ]);
    }

    protected function sendNotification($user, $data, $recharge)
    {
        $text = "<b>💳 Nạp tiền thành công từ ví USDT</b>\n";
        $text .= "👤 <b>Người dùng:</b> " . htmlspecialchars($user->username) . "\n";
        $text .= "💰 <b>Số tiền:</b> " . htmlspecialchars($data['received']) . " USDT\n";
        $text .= "🏦 <b>Địa chỉ ví:</b> <code>" . htmlspecialchars($data['from_address'] ?? '') . "</code>\n";
        $text .= "🆔 <b>Mã giao dịch:</b> <code>" . htmlspecialchars($data['transaction_id'] ?? '') . "</code>\n";
        $text .= "⏰ <b>Thời gian:</b> " . now() . "\n";
        $text .= "✅ <b>Trạng thái:</b> Thành công\n";
        $text .= "────────────────────\n";

        send_telegram($text, $user->domain);
    }
}
