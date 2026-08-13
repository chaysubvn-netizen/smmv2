<?php

namespace App\Http\Controllers\Cron\System;

use App\Http\Controllers\Controller;
use App\Models\FbToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FbTokenCronController extends Controller
{
    public function index(Request $request)
    {
        // Cho phép chạy không giới hạn thời gian và bộ nhớ
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $processed = 0;
        $concurrency = 100; // Số luồng chạy song song

        // Sử dụng chunk để tránh tràn bộ nhớ
        FbToken::where('status', '!=', 'die')
            ->orderBy('updated_at', 'asc')
            ->chunk(100, function ($tokens) use (&$processed, $concurrency) {
                $tokensBatch = $tokens->all();
                
                // Chia nhỏ batch thành các nhóm để chạy pool
                $chunks = array_chunk($tokensBatch, $concurrency);

                foreach ($chunks as $chunk) {
                    $responses = Http::pool(function ($pool) use ($chunk) {
                        $requests = [];
                        foreach ($chunk as $tokenModel) {
                            $accessToken = $this->extractToken($tokenModel->data);
                            if ($accessToken) {
                                $requests[$tokenModel->id] = $pool->as($tokenModel->id)
                                    ->timeout(10)
                                    ->get("https://graph.facebook.com/v19.0/me?fields=id,name&access_token={$accessToken}");
                            } else {
                                // Token không hợp lệ ngay từ đầu
                                $tokenModel->update(['status' => 'die']);
                            }
                        }
                        return $requests;
                    });

                    // Xử lý kết quả trả về từ pool
                    foreach ($responses as $tokenId => $response) {
                        $tokenModel = FbToken::find($tokenId);
                        if (!$tokenModel) continue;

                        if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                            $data = $response->json();
                            $tokenModel->update([
                                'status' => 'live',
                                'uid' => $data['id'] ?? $tokenModel->uid,
                                'name' => $data['name'] ?? $tokenModel->name,
                            ]);
                        } else {
                            $tokenModel->update(['status' => 'die']);
                        }
                        $processed++;
                    }
                }
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Đã hoàn thành kiểm tra đa luồng ' . $processed . ' tokens.'
        ]);
    }

    /**
     * Hàm kiểm tra 1 token duy nhất (Dùng cho nút Refresh trong Admin)
     */
    public function checkToken(FbToken $tokenModel)
    {
        $accessToken = $this->extractToken($tokenModel->data);

        if (!$accessToken) {
            $tokenModel->update(['status' => 'die']);
            return false;
        }

        try {
            $response = Http::timeout(10)->get("https://graph.facebook.com/v19.0/me", [
                'fields' => 'id,name',
                'access_token' => $accessToken
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $tokenModel->update([
                    'status' => 'live',
                    'uid' => $data['id'] ?? $tokenModel->uid,
                    'name' => $data['name'] ?? $tokenModel->name,
                ]);
                return true;
            } else {
                $tokenModel->update(['status' => 'die']);
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private function extractToken($data)
    {
        // Nếu data chứa |, lấy phần có vẻ là token
        if (strpos($data, '|') !== false) {
            $parts = explode('|', $data);
            foreach ($parts as $part) {
                $part = trim($part);
                if (strlen($part) > 30 && (str_starts_with($part, 'EAAG') || str_starts_with($part, 'EAA'))) {
                    return $part;
                }
            }
        }
        
        $data = trim($data);
        // Kiểm tra định dạng token phổ biến
        if (strlen($data) > 30 && (str_starts_with($data, 'EAAG') || str_starts_with($data, 'EAA'))) {
            return $data;
        }

        return null;
    }
}
