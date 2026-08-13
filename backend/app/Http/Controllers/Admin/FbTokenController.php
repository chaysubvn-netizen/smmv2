<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cron\System\FbTokenCronController;
use App\Models\FbToken;
use Illuminate\Http\Request;

class FbTokenController extends Controller
{
    public function index(Request $request)
    {
        $query = FbToken::where('domain', request()->getHost());

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('uid', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('note', 'like', '%' . $request->search . '%');
            });
        }

        $tokens = $query->latest()->paginate(50);

        $stats = [
            'all' => FbToken::where('domain', request()->getHost())->count(),
            'live' => FbToken::where('domain', request()->getHost())->where('status', 'live')->count(),
            'die' => FbToken::where('domain', request()->getHost())->where('status', 'die')->count(),
            'block' => FbToken::where('domain', request()->getHost())->where('status', 'block')->count(),
            'processing' => FbToken::where('domain', request()->getHost())->where('status', 'processing')->count(),
        ];

        return view('admin.fb-tokens.index', compact('tokens', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'list' => 'required|string',
            'type' => 'required|in:cookie_token,cookie,token',
            'note' => 'nullable|string',
        ]);

        $lines = explode("\n", str_replace("\r", "", $request->list));
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            FbToken::create([
                'type' => $request->type,
                'data' => $line,
                'note' => $request->note,
                'status' => 'live',
                'domain' => request()->getHost(),
                // Extracting UID from data if possible (basic logic)
                'uid' => $this->extractUid($line),
            ]);
            $count++;
        }

        return back()->with('success', "Đã thêm thành công $count tài nguyên.");
    }

    public function destroy($id)
    {
        $token = FbToken::where('domain', request()->getHost())->findOrFail($id);
        $token->delete();
        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        FbToken::where('domain', request()->getHost())->delete();
        return back()->with('success', 'Đã xóa tất cả tài nguyên.');
    }

    public function deleteDie()
    {
        FbToken::where('domain', request()->getHost())->where('status', 'die')->delete();
        return back()->with('success', 'Đã xóa tất cả tài nguyên đã chết.');
    }

    public function updateStatus(Request $request, $id)
    {
        $token = FbToken::where('domain', request()->getHost())->findOrFail($id);
        $token->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }

    public function refresh($id)
    {
        $token = FbToken::where('domain', request()->getHost())->findOrFail($id);
        $cron = new FbTokenCronController();
        $isLive = $cron->checkToken($token);
        
        return response()->json([
            'success' => true, 
            'is_live' => $isLive,
            'status' => $token->status,
            'message' => $isLive ? 'Tài nguyên đang Live!' : 'Tài nguyên đã Die!'
        ]);
    }

    private function extractUid($data)
    {
        // Simple regex to find UID in cookie or token
        // Usually it's c_user=1000... or similar
        if (preg_match('/c_user=(\d+)/', $data, $matches)) {
            return $matches[1];
        }
        // Or if it's just the first part of a token separated by |
        if (strpos($data, '|') !== false) {
            $parts = explode('|', $data);
            if (is_numeric($parts[0])) return $parts[0];
        }
        return null;
    }
}
