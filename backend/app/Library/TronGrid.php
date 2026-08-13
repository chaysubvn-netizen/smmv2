<?php

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TronGrid
{
    public function __construct(private readonly ?string $apiKey = null)
    {
    }

    public function incomingTrc20(string $address, int $limit = 200): array
    {
        try {
            $request = Http::acceptJson()->connectTimeout(10)->timeout(30);
            if ($this->apiKey) $request = $request->withHeaders(['TRON-PRO-API-KEY' => $this->apiKey]);
            $response = $request->get(
                'https://api.trongrid.io/v1/accounts/' . rawurlencode($address) . '/transactions/trc20',
                ['limit' => min(200, max(1, $limit)), 'only_confirmed' => 'true', 'only_to' => 'true']
            );
            $result = $response->json();
            if ($response->successful() && ($result['success'] ?? false) === true && is_array($result['data'] ?? null)) {
                return ['status' => 'success', 'data' => $result['data']];
            }
            return ['status' => 'error', 'message' => 'TronGrid trả về dữ liệu không hợp lệ.'];
        } catch (\Throwable $exception) {
            Log::error('TronGrid API error: ' . $exception->getMessage());
            return ['status' => 'error', 'message' => 'Không thể kết nối TronGrid.'];
        }
    }
}
