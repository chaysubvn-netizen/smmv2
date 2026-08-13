<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FbToken;
use Illuminate\Http\Request;

class FbTokenApiController extends Controller
{
    /**
     * Get a list of live Facebook tokens/cookies
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveTokens(Request $request)
    {
        // Lấy tất cả (hoặc giới hạn rất lớn để đảm bảo lấy hết)
        $limit = $request->get('limit', 100000);
        $type = $request->get('type'); // optional: cookie, token, cookie_token
        
        $query = FbToken::where('domain', request()->getHost())
            ->where('status', 'live');

        if ($type) {
            $query->where('type', $type);
        }

        // Ưu tiên lấy các token vừa mới được Cron kiểm tra xong
        $tokens = $query->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $tokens->count(),
            'data' => $tokens->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'data' => $item->data,
                    'uid' => $item->uid,
                    'name' => $item->name,
                    'note' => $item->note,
                    'updated_at' => $item->updated_at->toDateTimeString(),
                ];
            })
        ]);
    }
}
