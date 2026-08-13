<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CronServer;
use App\Models\UserCron;
use Illuminate\Support\Facades\Auth;
use App\Models\Order; // Assuming we log purchase in Order or similar? Using simplified deduction for now.

class CronClientController extends Controller
{
    public function index(Request $request)
    {
        $servers = CronServer::where('status', 1)->orderBy('price', 'asc')->get();
        
        $query = UserCron::where('user_id', Auth::id());

        // Filters
        if ($request->filled('keyword')) {
            $query->where('url', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Sorting
        if ($request->filled('sort_by') && $request->sort_by == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $limit = $request->input('limit', 10);
        $crons = $query->paginate($limit)->appends($request->all());

        return view_client('cron.index', compact('servers', 'crons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'server_id' => 'required|exists:cron_servers,id',
            'loop_time' => 'required|integer|min:30', // Minimum 30s
            'method' => 'required|in:GET,POST',
            'duration' => 'required|integer|min:1', // Months
        ]);

        $server = CronServer::findOrFail($request->server_id);
        $user = Auth::user();
        
        // Calculate Price
        $pricePerMonth = $server->price;
        $months = (int) $request->duration;
        $totalPrice = $pricePerMonth * $months;

        if ($user->balance < $totalPrice) {
            return redirect()->back()->with('error', 'Số dư không đủ. Vui lòng nạp thêm.');
        }

        // Deduct Balance
        $user->balance -= $totalPrice;
        $user->save();

        // Create Cron
        UserCron::create([
            'user_id' => $user->id,
            'server_id' => $server->id,
            'url' => $request->url,
            'method' => $request->method,
            'loop_time' => $request->loop_time,
            'status' => 'active',
            'price_paid' => $totalPrice,
            'expired_at' => now()->addMonths($months),
        ]);

        return redirect()->back()->with('success', 'Mua gói Cron thành công!');
    }

    public function destroy($id)
    {
        $cron = UserCron::where('user_id', Auth::id())->findOrFail($id);
        $cron->delete();
        return redirect()->back()->with('success', 'Đã xóa Cron Job.');
    }

    public function status($id, $status)
    {
        $cron = UserCron::where('user_id', Auth::id())->findOrFail($id);
        
        if (!in_array($status, ['active', 'stopped'])) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }

        $cron->status = $status;
        $cron->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'url' => 'required|url',
            'loop_time' => 'required|integer|min:30',
            'method' => 'required|in:GET,POST',
        ]);

        $cron = UserCron::where('user_id', Auth::id())->findOrFail($id);
        
        $cron->update([
            'url' => $request->url,
            'loop_time' => $request->loop_time,
            'method' => $request->method,
        ]);

        return redirect()->back()->with('success', 'Cập nhật cấu hình thành công.');
    }

    public function renew(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|integer|min:1',
        ]);

        $cron = UserCron::where('user_id', Auth::id())->findOrFail($id);
        $server = $cron->server; 
        
        $pricePerMonth = $server->price;
        $months = (int) $request->duration;
        $totalPrice = $pricePerMonth * $months;

        $user = Auth::user();
        if ($user->balance < $totalPrice) {
            return redirect()->back()->with('error', 'Số dư không đủ để gia hạn.');
        }

        // Deduct Balance
        $user->balance -= $totalPrice;
        $user->save();

        // Calculate new expiry
        $newExpiry = $cron->expired_at->isPast() ? now()->addMonths($months) : $cron->expired_at->addMonths($months);
        
        $cron->update([
            'expired_at' => $newExpiry,
            'status' => 'active', 
        ]);

        return redirect()->back()->with('success', 'Gia hạn thành công! Hết hạn: ' . $newExpiry->format('Y-m-d H:i:s'));
    }

    public function logs($id)
    {
        $cron = UserCron::where('user_id', Auth::id())->findOrFail($id);
        $logs = \Illuminate\Support\Facades\DB::table('cron_logs')
            ->where('user_cron_id', $id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();
            
        return response()->json([
            'status' => true,
            'data' => $logs
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'status' => 'required|in:active,stopped',
        ]);

        $count = UserCron::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => "Đã cập nhật trạng thái cho {$count} Cron Job."
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $count = UserCron::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => "Đã xóa {$count} Cron Job."
        ]);
    }
}
