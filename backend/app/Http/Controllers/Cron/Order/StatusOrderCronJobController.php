<?php

namespace App\Http\Controllers\Cron\Order;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatusOrderCronJobController extends Controller
{
    public function index(Request $request)
    {
        $apiProviders = ApiProvider::where('status', 'active')->when($request->filled('provider_id'), fn ($query) => $query->whereKey($request->integer('provider_id')))->get();
        foreach ($apiProviders as $apiProvider) {
            $query = Order::with(['user', 'service'])
                // Providers commonly return "In progress" (lower-case "p").
                // Keep accepting the legacy spelling too, otherwise an order is
                // updated once and then silently drops out of subsequent runs.
                ->whereIn('status', ['Pending', 'Processing', 'In Progress', 'In progress', 'Active'])
                ->where('provider', $apiProvider->id)
                ->where('status', '!=', 'Completed')
                ->where('schedule_time', null)
                ->orderBy('updated_at', 'asc')
                ->limit(500);

            $orders = $query->get();

            if ($orders->isEmpty()) {
                continue;
            }

            // Chunk orders into batches of 100 for API compatibility
            $orderChunks = $orders->chunk(100);

            foreach ($orderChunks as $chunk) {
                $listOrder = [];
                $ordersByApiId = [];
                foreach ($chunk as $order) {
                    $apiId = $order->response_data['order'] ?? null;
                    if ($apiId) {
                        $listOrder[] = $apiId;
                        $ordersByApiId[$apiId] = $order;
                    }
                }

                if (empty($listOrder)) {
                    continue;
                }

                // Pre-fetch child orders for this chunk
                $childOrdersByRootId = Order::with(['user', 'service'])
                    ->whereIn('root_id', $listOrder)
                    ->whereNotIn('id', $chunk->pluck('id'))
                    ->whereIn('status', ['Pending', 'Processing', 'In Progress', 'In progress', 'Active'])
                    ->where('status', '!=', 'Completed')
                    ->get()
                    ->groupBy('root_id');

                // Pre-fetch existing refund transactions for this chunk
                $allOrderIds = $chunk->pluck('id')->merge(
                    $childOrdersByRootId->flatten()->pluck('id')
                )->unique();
                
                $existingRefunds = Transaction::whereIn('transaction_code', $allOrderIds->map(function($id) {
                    return "Refunded Order #{$id}";
                }))
                    ->pluck('transaction_code')
                    ->flip();

                $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
                $multiStatus = $smm->multiStatus(implode(',', $listOrder));

                if (!is_array($multiStatus) || empty($multiStatus)) {
                    \Log::warning("Cron Order Status: API Provider #{$apiProvider->id} returned empty/invalid for a chunk.", [
                        'order_ids' => $listOrder
                    ]);
                    continue;
                }

                foreach ($multiStatus as $key => $value) {
                    try {
                        $order = $ordersByApiId[$key] ?? null;
                        if (!$order && is_numeric($key)) {
                            $order = $ordersByApiId[(string)$key] ?? null;
                        }

                        if (!$order || !$order->service) {
                            continue;
                        }

                    if ($order->service->type != "Subscriptions") {
                        $start_count = $value['start_count'] ?? 0;
                        $remains = $value['remains'] ?? 0;
                        $status = $value['status'] ?? 'Pending';
                        $note = $value['note'] ?? '';

                        if (in_array($status, ['Refunded', 'Partial', 'Canceled', 'Partially Refunded'])) {
                            $user = $order->user;
                            $refundCode = "Refunded Order #{$order->id}";
                            
                            if ($user && !isset($existingRefunds[$refundCode])) {
                                if ($status == "Canceled") {
                                    $remains = $order->quantity;
                                }

                                $balanceBack = ($order->rate * $remains) / 1000;
                                $user->balance += $balanceBack;
                                $user->save();

                                $order->cancel = 0;
                                $order->refill = 0;
                                $order->dripfeed = 0;

                                Transaction::create([
                                    'user_id' => $order->user_id,
                                    'transaction_code' => $refundCode,
                                    'type' => 'add',
                                    'balance_before' => $user->balance - $balanceBack,
                                    'balance_after' => $user->balance,
                                    'amount' => $balanceBack,
                                    'description' => "Hoàn tiền đơn hàng #{$order->id}",
                                    'status' => 'success',
                                    'domain' => $order->domain,
                                ]);

                                $text = "<b>Thông báo hoàn tiền</b>\n"
                                    . "<b>Người dùng:</b> {$user->username}\n"
                                    . "<b>Đơn hàng:</b> {$order->id}\n"
                                    . "<b>Trạng thái:</b> Hoàn tiền\n"
                                    . "<b>Thời gian:</b> " . now()->format('d/m/Y H:i:s') . "\n"
                                    . "<b>Số tiền:</b> " . $balanceBack . "đ\n"
                                    . "<b>Ghi chú:</b> Hoàn tiền đơn hàng #{$order->id} - {$order->service->name}\n";
                                send_telegram($text);
                            }
                        }

                        if ($status == 'Completed') {
                            $order->success_time = now();
                            $service = $order->service;
                            if ($service) {
                                $diff = $order->success_time->diff($order->created_at);
                                $average_time = '';
                                if ($diff->h > 0) $average_time .= $diff->h . 'h ';
                                if ($diff->i > 0) $average_time .= $diff->i . 'p ';
                                if ($diff->s > 0) $average_time .= $diff->s . 's';
                                $service->average_time = $average_time;
                                $service->save();
                            }
                        }

                        $order->status = $status;
                        $order->start_count = $start_count;
                        $order->remains = $remains;
                        $order->note = $note;
                        $order->save();

                        // Handle child panel orders
                        $childOrders = $childOrdersByRootId->get($key) ?? collect();
                        foreach ($childOrders as $orderChild) {
                            if (in_array($status, ['Refunded', 'Partial', 'Canceled', 'Partially Refunded'])) {
                                $user = $orderChild->user;
                                $refundCodeChild = "Refunded Order #{$orderChild->id}";

                                if ($orderChild->cancel == 1 && $user && !isset($existingRefunds[$refundCodeChild])) {
                                    $balanceBack = $orderChild->rate * $remains; 
                                    $user->balance += $balanceBack;
                                    $user->save();

                                    Transaction::create([
                                        'user_id' => $orderChild->user_id,
                                        'transaction_code' => $refundCodeChild,
                                        'type' => 'add',
                                        'balance_before' => $user->balance - $balanceBack,
                                        'balance_after' => $user->balance,
                                        'amount' => $balanceBack,
                                        'description' => "Hoàn tiền đơn hàng #{$orderChild->id} - {$orderChild->service->name}",
                                        'status' => 'success',
                                        'domain' => $orderChild->domain,
                                    ]);

                                    $text = "<b>Thông báo hoàn tiền</b>\n"
                                        . "<b>Người dùng:</b> {$user->username}\n"
                                        . "<b>Đơn hàng:</b> {$orderChild->id}\n"
                                        . "<b>Trạng thái:</b> Hoàn tiền\n"
                                        . "<b>Thời gian:</b> " . now()->format('d/m/Y H:i:s') . "\n"
                                        . "<b>Số tiền:</b> " . $balanceBack . "đ\n"
                                        . "<b>Ghi chú:</b> Hoàn tiền đơn hàng #{$orderChild->id} - {$orderChild->service->name}\n";
                                    send_telegram($text);
                                }
                            }

                            if ($status == 'Completed') {
                                $orderChild->success_time = now();
                                $service = $orderChild->service;
                                if ($service) {
                                    $diff = $orderChild->success_time->diff($orderChild->created_at);
                                    $average_time = '';
                                    if ($diff->h > 0) $average_time .= $diff->h . 'h ';
                                    if ($diff->i > 0) $average_time .= $diff->i . 'p ';
                                    if ($diff->s > 0) $average_time .= $diff->s . 's';
                                    $service->average_time = $average_time;
                                    $service->save();
                                }
                            }

                            $orderChild->status = $status;
                            $orderChild->start_count = $start_count;
                            $orderChild->remains = $remains;
                            $orderChild->note = $note;
                            $orderChild->save();
                        }
                    } else {
                        // Subscriptions logic
                        $status = $value['status'] ?? 'Active';
                        $posts = $value['posts'] ?? 0;

                        if ($posts > $order->remains) {
                            $user = $order->user;
                            $totalPayment = $order->rate * 1 / 1000;
                            $order->remains = $posts;
                            $order->status = $status;
                            $order->save();

                            $user->balance -= $totalPayment;
                            $user->save();

                            Transaction::create([
                                'user_id' => $order->user_id,
                                'transaction_code' => "Order #{$order->id}",
                                'type' => 'sub',
                                'balance_before' => $user->balance + $totalPayment,
                                'balance_after' => $user->balance,
                                'amount' => $totalPayment,
                                'description' => "Trừ tiền đơn hàng #{$order->id} - {$order->service->name}",
                                'status' => 'success',
                                'domain' => $order->domain,
                            ]);

                            $childOrders = $childOrdersByRootId->get($key) ?? collect();
                            foreach ($childOrders as $orderChild) {
                                $orderChild->remains = $posts;
                                $orderChild->status = $status;
                                $orderChild->save();

                                $userChild = $orderChild->user;
                                $totalPaymentChild = $orderChild->rate * 1 / 1000;
                                $userChild->balance -= $totalPaymentChild;
                                $userChild->save();

                                Transaction::create([
                                    'user_id' => $orderChild->user_id,
                                    'transaction_code' => "Order #{$orderChild->id}",
                                    'type' => 'sub',
                                    'balance_before' => $userChild->balance + $totalPaymentChild,
                                    'balance_after' => $userChild->balance,
                                    'amount' => $totalPaymentChild,
                                    'description' => "Trừ tiền đơn hàng #{$orderChild->id} - {$orderChild->service->name}",
                                    'status' => 'success',
                                    'domain' => $orderChild->domain,
                                ]);
                            }
                        } else {
                            $order->remains = $posts;
                            $order->status = $status;
                            $order->save();
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Cron job status order completed',
    ]);
}


    public function refill(Request $request)
    {
        $apiProviders = ApiProvider::where('status', 'active')->when($request->filled('provider_id'), fn ($query) => $query->whereKey($request->integer('provider_id')))->get();
        foreach ($apiProviders as $apiProvider) {
            $orders = Order::whereIn('status_refill', ['Pending', 'Processing', 'In Progress', 'In progress'])
                ->where('provider', $apiProvider->id)
                ->where('status_refill', '!=', 'Completed')
                ->limit(200)
                ->get();

            if ($orders->isEmpty()) {
                continue;
            }

            $listOrder = [];
            $ordersByApiId = [];
            foreach ($orders as $order) {
                $apiId = $order->response_data['order'] ?? null;
                if ($apiId) {
                    $listOrder[] = $apiId;
                    $ordersByApiId[$apiId] = $order;
                }
            }

            if (empty($listOrder)) {
                continue;
            }

            $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
            $multiStatus = $smm->multiRefillStatus($listOrder);
            if (is_array($multiStatus) && count($multiStatus) > 0) {
                foreach ($multiStatus as $key => $value) {
                    $refill_api_id = isset($value['refill']) ? $value['refill'] : null;
                    if (!$refill_api_id || !isset($ordersByApiId[$refill_api_id])) {
                        continue;
                    }

                    $status = 'Pending';
                    if (is_array($value['status']) && isset($value['status']['error'])) {
                        $status = 'Canceled';
                    } elseif (isset($value['status'])) {
                        $status = $value['status'];
                    }

                    $order = $ordersByApiId[$refill_api_id];
                    $order->status_refill = $status;
                    $order->save();
                }
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Cron job status order completed',
        ], 200);
    }


    public function dripfeed(Request $request)
    {
        $apiProviders = ApiProvider::where('status', 'active')->when($request->filled('provider_id'), fn ($query) => $query->whereKey($request->integer('provider_id')))->get();
        $mainSite = env("MAIN_SITE");

        foreach ($apiProviders as $apiProvider) {
            $orders = Order::with(['user', 'service'])
                ->where('provider', $apiProvider->id)
                ->where('dripfeed', 1)
                ->where('dripfeed_status', '=', 'Processing')
                ->where('domain', $mainSite)
                ->limit(100)
                ->get();

            if ($orders->isEmpty()) {
                continue;
            }

            $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
            
            // Batch status check for dripfeed root orders
            $rootIds = $orders->map(function($o) {
                return $o->root_id ?? $o->response_data['order'] ?? null;
            })->filter()->unique()->toArray();

            if (empty($rootIds)) {
                continue;
            }

            $multiStatus = $smm->multiStatus(implode(',', $rootIds));
            
            // Pre-fetch child dripfeed orders
            $childOrdersByRootId = Order::where('provider', $apiProvider->id)
                ->where('dripfeed', 1)
                ->where('dripfeed_status', '=', 'Processing')
                ->where('domain', '!=', $mainSite)
                ->whereIn('root_id', $rootIds)
                ->get()
                ->groupBy('root_id');

            foreach ($orders as $order) {
                $service = $order->service;
                $input_data = $order->input_data;
                $loop_quantity = $order->loop_quantity ?? 0;
                $loop_spacing = $order->loop_spacing ?? 0;
                $user = $order->user;

                if ($order->run >= $loop_quantity) {
                    $order->dripfeed = 0;
                    $order->dripfeed_status = "Completed";
                    $order->save();
                    continue;
                }

                if ($user && $service && $loop_quantity > 0 && $loop_spacing > 0) {
                    // Time spacing check
                    $success_time = $order->success_time;
                    if ($success_time) {
                        $success_time = Carbon::parse($success_time);
                        $minutes_passed = $success_time->diffInMinutes(now(), false);
                        if ($minutes_passed < $loop_spacing) {
                            continue;
                        }
                    }

                    $root_id = $order->root_id ?? $order->response_data['order'] ?? null;
                    if ($root_id && isset($multiStatus[$root_id])) {
                        $status = $multiStatus[$root_id]['status'] ?? 'Pending';
                        
                        if (in_array($status, ['Completed', 'Canceled', 'Partially Refunded', 'Refunded'])) {
                            $ordersDripfeedSub = $childOrdersByRootId->get($root_id) ?? collect();
                            
                            $input_data['service'] = $service->provider_id;
                            $orderResult = $smm->order($input_data);

                            if (!isset($orderResult['order'])) {
                                $order->dripfeed_status = "Canceled";
                                $order->save();
                                foreach ($ordersDripfeedSub as $orderChild) {
                                    $orderChild->dripfeed_status = "Canceled";
                                    $orderChild->save();
                                }
                                continue;
                            }

                            $new_root_id = $orderResult['order'];

                            // Create new instance of order for history
                            $newOrder = $order->replicate();
                            $newOrder->root_id = $new_root_id;
                            $newOrder->response_data = $orderResult;
                            $newOrder->dripfeed = 0;
                            $newOrder->dripfeed_status = null;
                            $newOrder->status = 'Pending';
                            $newOrder->start_count = 0;
                            $newOrder->remains = 0;
                            $newOrder->success_time = null;
                            $newOrder->save();

                            $order->run += 1;
                            $order->root_id = $new_root_id;
                            $order->success_time = now();

                            if ($order->run >= $loop_quantity) {
                                $order->dripfeed = 0;
                                $order->dripfeed_status = "Completed";
                            }
                            $order->save();

                            // Child panels
                            foreach ($ordersDripfeedSub as $orderChild) {
                                $orderNewChild = $orderChild->replicate();
                                $orderNewChild->root_id = $new_root_id;
                                $orderNewChild->response_data = $orderResult;
                                $orderNewChild->dripfeed = 0;
                                $orderNewChild->dripfeed_status = null;
                                $orderNewChild->status = 'Pending';
                                $orderNewChild->start_count = 0;
                                $orderNewChild->remains = 0;
                                $orderNewChild->success_time = null;
                                $orderNewChild->save();

                                $orderChild->run += 1;
                                $orderChild->root_id = $new_root_id;
                                $orderChild->success_time = now();

                                if ($orderChild->run >= $loop_quantity) {
                                    $orderChild->dripfeed = 0;
                                    $orderChild->dripfeed_status = "Completed";
                                }
                                $orderChild->save();
                            }
                        }
                    }
                } else {
                    $order->dripfeed = 0;
                    $order->save();
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cron job status order completed',
        ]);
    }




    public function schedule(Request $request)
    {
        $apiProviders = ApiProvider::where('status', 'active')->when($request->filled('provider_id'), fn ($query) => $query->whereKey($request->integer('provider_id')))->get();
        foreach ($apiProviders as $apiProvider) {
            $orders = Order::whereIn('status', ['Pending', 'Processing', 'In Progress'])
                ->where('provider', $apiProvider->id)
                ->where('status', '!=', 'Completed')
                ->where('schedule_time', '!=', null)
                ->where('domain', env("MAIN_SITE"))
                ->limit(100)
                ->get();
            if ($orders->count() > 0) {
                $smm = new SmmApiCustom($apiProvider->api_url, $apiProvider->api_key);
                foreach ($orders as $order) {
                    $service = $order->service;
                    $input_data = $order->input_data;

                    $schedule_time = $order->schedule_time ?? null; // Múi giờ do để đến thời gian đó chạy
                    $user = $order->user;
                    if ($schedule_time) {
                        $schedule_time = Carbon::parse($schedule_time);
                        $now = Carbon::now();

                        // kiểm tra đến đến thời gian chạy schedule hay không
                        if (!$schedule_time->isPast()) {
                            continue;
                        }

                        if ($user && $service && $schedule_time) {
                            $service = $order->service;
                            // tạo đơn hàng mới và cập nhật root_id từ đơn response
                            $input_data['service'] = $service->provider_id;
                            $orderResult = $smm->order($input_data);
                            if (!isset($orderResult['order'])) {
                                $order->status = "Canceled";
                                $order->save();

                                // Hoàn tiền cho người dùng
                                $user->balance += $order->total;
                                $user->save();
                                Transaction::create([
                                    'user_id' => $order->user_id,
                                    'transaction_code' => "Refunded Order #{$order->id}",
                                    'type' => 'add',
                                    'balance_before' => $user->balance - $order->total,
                                    'balance_after' => $user->balance,
                                    'amount' => $order->total,
                                    'description' => "Hoàn tiền đơn hàng #{$order->id} - {$order->service->name}",
                                    'status' => 'success',
                                    'domain' => $order->domain,
                                ]);
                                // tạo thông báo telegram html đầy đủ icon tiêu đề là THông báo hoàn tiền
                                $text = "<b>Thông báo hoàn tiền</b>\n";
                                $text .= "<b>Người dùng:</b> {$user->username}\n";
                                $text .= "<b>Đơn hàng:</b> {$order->id}\n";
                                $text .= "<b>Trạng thái:</b> Hoàn tiền\n";
                                $text .= "<b>Thời gian:</b> " . now()->format('d/m/Y H:i:s') . "\n";
                                $text .= "<b>Số tiền:</b> " . $order->total . "đ\n";
                                $text .= "<b>Ghi chú:</b> Hoàn tiền đơn hàng #{$order->id} - {$order->service->name}\n";
                                send_telegram($text);
                                continue;
                            } else {
                                $order_id = $orderResult['order'];
                                $order->response_data = $orderResult;
                                $order->root_id = $order_id;
                                $order->status = 'Pending';
                                $order->schedule_time = null;
                                $order->save();

                                $orderSub = Order::where('root_id', $order->id)->get();
                                if ($orderSub->count() > 0) {
                                    foreach ($orderSub as $orderChild) {
                                        $orderChild->response_data = $orderResult;
                                        $orderChild->root_id = $order_id;
                                        $orderChild->status = 'Pending';
                                        $orderChild->schedule_time = null;
                                        $orderChild->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cron job status order completed',
        ]);
    }
}
