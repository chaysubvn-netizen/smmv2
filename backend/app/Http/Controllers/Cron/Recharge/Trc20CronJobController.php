<?php

namespace App\Http\Controllers\Cron\Recharge;

use App\Http\Controllers\Controller;
use App\Library\TronGrid;
use App\Models\Config;
use App\Models\Currency;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Trc20CronJobController extends Controller
{
    private const USDT_CONTRACT = 'TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj';

    public function index(Request $request)
    {
        $config = Config::where('domain', $request->getHost())->first() ?? Config::first();
        if (!$config || $config->trc20_status !== 'active' || blank($config->trc20_wallet)) {
            return response()->json(['status' => 'error', 'message' => 'USDT TRC20 chưa được cấu hình.']);
        }

        $response = (new TronGrid($config->trongrid_api_key))->incomingTrc20($config->trc20_wallet);
        if (($response['status'] ?? null) !== 'success') {
            return response()->json($response);
        }

        $pending = Recharge::where('type', 'trc20')
            ->where('status', 'waiting')
            ->where('domain', $config->domain)
            ->where('created_at', '>=', now()->subDay())
            ->get();
        $processed = 0;
        $contract = $config->trc20_contract ?: self::USDT_CONTRACT;
        foreach ($pending as $recharge) {
            $transaction = $this->match($recharge, $response['data'], $config->trc20_wallet, $contract);
            if ($transaction && $this->complete($recharge, $transaction)) $processed++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã xử lý {$processed} giao dịch TRC20.",
            'total_pending' => $pending->count(),
            'rows_fetched' => count($response['data']),
        ]);
    }

    private function match(Recharge $recharge, array $rows, string $wallet, string $contract): ?array
    {
        $expected = (float) $recharge->request_id;
        $createdAtMs = $recharge->created_at->getTimestampMs() - 120000;
        foreach ($rows as $row) {
            $token = $row['token_info'] ?? [];
            $decimals = (int) ($token['decimals'] ?? 6);
            $amount = (float) ($row['value'] ?? 0) / (10 ** $decimals);
            $hash = (string) ($row['transaction_id'] ?? '');
            if (
                $hash !== ''
                && strcasecmp((string) ($row['to'] ?? ''), $wallet) === 0
                && strcasecmp((string) ($token['address'] ?? ''), $contract) === 0
                && strtoupper((string) ($token['symbol'] ?? '')) === 'USDT'
                && strcasecmp((string) ($row['type'] ?? ''), 'Transfer') === 0
                && (int) ($row['block_timestamp'] ?? 0) >= $createdAtMs
                && abs($amount - $expected) < 0.0000005
                && !Recharge::where('transaction_id', $hash)->where('status', 'completed')->exists()
            ) {
                $row['_amount_usdt'] = $amount;
                return $row;
            }
        }
        return null;
    }

    private function complete(Recharge $recharge, array $row): bool
    {
        return DB::transaction(function () use ($recharge, $row) {
            $locked = Recharge::whereKey($recharge->id)->lockForUpdate()->first();
            if (!$locked || $locked->status !== 'waiting') return false;
            $hash = (string) $row['transaction_id'];
            if (Recharge::where('transaction_id', $hash)->where('status', 'completed')->exists()) return false;

            $user = User::whereKey($locked->user_id)->lockForUpdate()->first();
            if (!$user) return false;
            $rate = (float) (Currency::where('code', 'VND')->value('exchange_rate') ?: 23000);
            $amountVnd = (float) $row['_amount_usdt'] * $rate;
            $before = (float) $user->balance;
            $user->balance = $before + $amountVnd;
            $user->total_deposit = (float) $user->total_deposit + $amountVnd;
            $user->save();

            $locked->update([
                'transaction_id' => $hash,
                'status' => 'completed',
                'real_amount' => $amountVnd,
                'description' => 'Nạp tự động USDT TRC20 - TXID: ' . $hash,
            ]);
            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => 'TRC20_' . Str::upper(Str::random(10)),
                'type' => 'add',
                'balance_before' => $before,
                'balance_after' => $user->balance,
                'amount' => $amountVnd,
                'description' => 'Nạp tự động USDT TRC20 - TXID: ' . $hash,
                'status' => 'success',
                'domain' => $locked->domain,
            ]);
            if (method_exists($user, 'upgradeLevel')) $user->upgradeLevel($user->total_deposit);
            return true;
        });
    }
}
