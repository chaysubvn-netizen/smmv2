<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Recharge;
use App\Models\Currency;
use App\Models\RechargeBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DataAdminController extends Controller
{
    public function orders()
    {
        $domain = request()->getHost();
        $ordersCount = [
            'total' => Order::where('domain', $domain)->count(),
            'pending' => Order::where('domain', $domain)->where('status', 'Pending')->count(),
            'processing' => Order::where('domain', $domain)->where('status', 'Processing')->count(),
            'inprogress' => Order::where('domain', $domain)->where('status', 'In progress')->count(),
            'completed' => Order::where('domain', $domain)->where('status', 'Completed')->count(),
            'partial' => Order::where('domain', $domain)->where('status', 'Partial')->count(),
            'canceled' => Order::where('domain', $domain)->where('status', 'Canceled')->count(),
        ];

        // Get all services for filter dropdown
        $services = \App\Models\Service::where('domain', $domain)
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        if($domain == env("MAIN_SITE")){
            return view('admin.data.orders.index', compact('ordersCount', 'services'));
        }else{
            return view('admin.data.orders.index_childpanel', compact('ordersCount', 'services'));
        }
    }

    public function orderDetail($id)
    {
        
        $order = Order::where('domain', request()->getHost())->with('service')->find($id);
        if (!$order) {
            return redirect()->route('admin.data.orders')->with('error', 'Đơn hàng không tồn tại');
        }
        return view('admin.data.orders.detail', compact('order'));
    }

    public function orderUpdate(Request $request, $id)
    {
        // ✅ Kiểm tra chế độ demo
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'start_count' => 'nullable|integer|min:0',
            'remains' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $order = Order::where('domain', $request->getHost())->find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại',
            ]);
        }

        $remains = $request->filled('remains') ? (int) $request->remains : (int) $order->remains;
        if ($order->status !== 'Partial' && $request->status === 'Partial' && $remains > 0) {
            $balanceBack = ($order->rate * $remains) / 1000;
            $user = $order->user;
            $user->balance += $balanceBack;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'transaction_code' => 'ORDER_' . $order->id,
                'type' => 'Refund',
                'balance_before' => $user->balance - $balanceBack,
                'balance_after' => $user->balance,
                'amount' => $balanceBack,
                'description' => 'Hoàn tiền đơn hàng #' . $order->id,
                'status' => 'Success',
                'domain' => $request->getHost(),
            ]);
        }

        $order->status = $request->status;
        $order->start_count = $request->start_count ?? $order->start_count;
        $order->remains = $request->remains ?? $order->remains;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đơn hàng thành công',
        ]);
    }

    public function exportOrders(Request $request)
    {
        $domain = request()->getHost();
        $type = $request->type ?? 'csv';
        
        // Build query with filters
        $query = Order::with(['user', 'service'])
            ->where('domain', $domain);
        
        // Apply filters
        if ($request->filter_username) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->filter_username . '%');
            });
        }
        
        if ($request->filter_order_id) {
            $query->where('id', $request->filter_order_id);
        }
        
        if ($request->filter_service) {
            $query->where('service_id', $request->filter_service);
        }
        
        if ($request->filter_status) {
            $query->where('status', $request->filter_status);
        }
        
        if ($request->filter_date_from) {
            $query->whereDate('created_at', '>=', $request->filter_date_from);
        }
        
        if ($request->filter_date_to) {
            $query->whereDate('created_at', '<=', $request->filter_date_to);
        }
        
        // Get orders
        $orders = $query->orderBy('id', 'desc')->get();
        
        if ($type == 'txt') {
            $filename = 'orders_' . date('Y-m-d_His') . '.txt';
            $content = "";
            $content .= "DANH SÁCH ĐƠN HÀNG - " . date('d/m/Y H:i:s') . "\n";
            $content .= str_repeat("-", 50) . "\n\n";

            foreach ($orders as $order) {
                // Calculate profit
                $qty = $order->quantity * ($order->loop_quantity ?? 1);
                $rate_original = $order->service->rate_original ?? $order->rate;
                $cost = ($rate_original * $qty) / 1000;
                $revenue = $order->total;
                $profit = $revenue - $cost;

                $content .= "ID: " . $order->id . "\n";
                $content .= "Username: " . ($order->user->username ?? 'N/A') . "\n";
                $content .= "Dịch vụ: " . ($order->service->name ?? 'N/A') . "\n";
                $content .= "Link: " . $order->link . "\n";
                $content .= "Số lượng: " . number_format($order->quantity) . "\n";
                $content .= "Giá/1000: " . number_format($order->rate) . " ₫\n";
                $content .= "Tổng thanh toán: " . number_format($order->total) . " ₫\n";
                $content .= "Lợi nhuận: " . number_format($profit) . " ₫\n";
                $content .= "Bắt đầu: " . number_format($order->start_count) . "\n";
                $content .= "Còn lại: " . number_format($order->remains) . "\n";
                $content .= "Trạng thái: " . $order->status . "\n";
                $content .= "Thời gian: " . $order->created_at->format('d/m/Y H:i:s') . "\n";
                $content .= str_repeat("-", 30) . "\n";
            }

            return response($content)
                ->header('Content-Type', 'text/plain; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        // Default CSV Export
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Username',
                'Dịch vụ',
                'Link',
                'Quantity',
                'Rate (VND)',
                'Charge (VND)',
                'Profit (VND)',
                'Start Count',
                'Remains',
                'Trạng thái',
                'Ngày tạo',
                'Ngày cập nhật'
            ]);
            
            // Data
            foreach ($orders as $order) {
                // Calculate profit
                $qty = $order->quantity * ($order->loop_quantity ?? 1);
                $rate_original = $order->service->rate_original ?? $order->rate;
                $cost = ($rate_original * $qty) / 1000;
                $revenue = $order->total;
                $profit = $revenue - $cost;

                fputcsv($file, [
                    $order->id,
                    $order->user->username ?? 'N/A',
                    $order->service->name ?? 'N/A',
                    $order->link,
                    $order->quantity,
                    $order->rate,
                    $order->total,
                    $profit,
                    $order->start_count,
                    $order->remains,
                    $order->status,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->updated_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function deposits(Request $request){
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        return view('admin.data.deposits.index');
    }

    public function rechargeApprove(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $domain = $request->attributes->get('admin_domain', $request->getHost());
        $recharge = Recharge::where('id', $request->id)
            ->where('domain', $domain)
            ->where('status', '!=', 'completed')
            ->first();

        if (!$recharge) {
            return response()->json([
                'success' => false,
                'message' => 'Giao dịch không tồn tại hoặc đã được duyệt',
            ]);
        }

        $user = $recharge->user;
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không tồn tại',
            ]);
        }

        // Cập nhật trạng thái recharge
        $recharge->update([
            'status' => 'completed',
        ]);

        // Duyệt nạp tiền: Số tiền đã được lưu dưới dạng VND trong database (từ AppClientController)
        // Không cần quy đổi lại nữa để tránh nhân đôi tỷ giá
        $amountVnd = $recharge->amount;
        
        // Lấy tỷ giá để tính toán bonus (nếu cần theo USD)
        $currencyVnd = Currency::where('code', 'VND')->first();
        $exchangeRate = $currencyVnd->exchange_rate ?? 25000;
        $amountUsd = $amountVnd / $exchangeRate;

        // Xử lý bonus dựa trên loại nạp tiền
        $bonusType = $recharge->type == 'binance' ? 'binance' : ($recharge->type == 'usdt' ? 'usdt' : 'banking');
        $bonusAmount = $this->calculateBonusForManual($amountVnd, $amountUsd, $bonusType);
        $realAmount = $amountVnd + $bonusAmount;

        // Cập nhật 'amount' và các thông tin liên quan
        $recharge->update([
            'amount' => $amountVnd,
            'real_amount' => $realAmount,
            'bonus' => $bonusAmount,
        ]);

        // Cộng tiền user
        $balanceBefore = $user->balance;
        $user->balance += $realAmount;
        $user->total_deposit += $realAmount;
        $user->save();
        $user->upgradeLevel($user->total_deposit);

        // Tạo transaction
        $gatewayName = match ($recharge->type) {
            'binance' => 'Binance Pay',
            'usdt' => 'USDT',
            default => $recharge->bank_id
                ? (\App\Models\AccountBank::find($recharge->bank_id)?->bank_name ?? 'ngân hàng')
                : 'ngân hàng',
        };

        Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => "RECHARGE_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
            'amount' => $realAmount,
            'description' => "Nạp thành công qua {$gatewayName} - " . ($recharge->transaction_id ?? $recharge->id),
            'status' => 'success',
            'domain' => $recharge->domain
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Duyệt nạp tiền thành công',
        ]);
    }

    public function rechargeCancel(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $domain = $request->attributes->get('admin_domain', $request->getHost());
        $recharge = Recharge::where('id', $request->id)
            ->where('domain', $domain)
            ->where('status', '!=', 'completed')
            ->first();

        if (!$recharge) {
            return response()->json([
                'success' => false,
                'message' => 'Giao dịch không tồn tại hoặc đã được duyệt',
            ]);
        }

        $recharge->update([
            'status' => 'failed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hủy nạp tiền thành công',
        ]);
    }

    protected function calculateBonusForManual($amountVnd, $amountUsd, $type = 'banking')
    {
        $bonuses = RechargeBonus::where('domain', request()->getHost())
            ->where('type', $type)
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
             return ($selectedBonus->bonus * $amountVnd) / 100;
        }

        return 0;
    }

    public function transactions(Request $request){
        return view('admin.data.transactions.index');
    }

    public function cleanOrders(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'days' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập số ngày hợp lệ',
            ]);
        }

        $days = $request->days;
        $date = now()->subDays($days);
        $domain = $request->getHost();

        $count = Order::where('domain', $domain)
            ->where('created_at', '<', $date)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã dọn dẹp thành công ' . number_format($count) . ' đơn hàng cũ',
        ]);
    }
}
