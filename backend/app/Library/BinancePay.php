<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BinancePay
{
    private string $baseUrl = 'https://api.binance.com';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiSecret
    ) {
    }

    public function getPayHistory(?int $startTime = null, ?int $endTime = null, int $limit = 100): array
    {
        try {
            $params = [
                'timestamp' => $this->getServerTime(),
                'recvWindow' => 5000,
                'limit' => min(100, max(1, $limit)),
            ];
            if ($startTime !== null) $params['startTime'] = $startTime;
            if ($endTime !== null) $params['endTime'] = $endTime;

            $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            $signature = hash_hmac('sha256', $query, $this->apiSecret);
            $response = Http::withHeaders(['X-MBX-APIKEY' => $this->apiKey])
                ->connectTimeout(10)
                ->timeout(30)
                ->get($this->baseUrl . '/sapi/v1/pay/transactions?' . $query . '&signature=' . $signature);

            $result = $response->json();
            if ($response->successful() && ($result['code'] ?? null) === '000000' && is_array($result['data'] ?? null)) {
                return ['status' => 'success', 'data' => $result['data']];
            }

            return [
                'status' => 'error',
                'message' => $result['msg'] ?? $result['message'] ?? 'Binance API trả về lỗi.',
                'code' => $result['code'] ?? $response->status(),
            ];
        } catch (\Throwable $exception) {
            Log::error('Binance Pay API error: ' . $exception->getMessage());
            return ['status' => 'error', 'message' => 'Không thể kết nối Binance API.'];
        }
    }

    private function getServerTime(): int
    {
        $response = Http::connectTimeout(5)->timeout(10)->get($this->baseUrl . '/api/v3/time');
        return (int) ($response->json('serverTime') ?: round(microtime(true) * 1000));
    }
}
