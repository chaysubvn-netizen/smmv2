<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Library\SmmApiCustom;
use App\Library\TelegramCustom;
use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderClientController extends Controller
{
    protected function validateRequest(Request $request, User $user, Service $service)
    {
        $rules = [];
        $caculate = true;
        switch ($service->type) {
            case "Default":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
            case "Package":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
            case "SEO":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'keywords' => 'required|string',
                ];
                break;
            case "Custom Comments":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'comments' => 'required|string',
                ];
                break;
            case "Mentions with Hashtags":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'usernames' => 'required|string',
                    'hashtags' => 'nullable|string',
                ];
                break;
            case "Mentions Custom List":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'usernames' => 'required|string',
                ];
                break;
            case "Mentions Hashtag":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'hashtag' => 'nullable|string',
                ];
                break;
            case "Mentions User Followers":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                ];
                break;
            case "Mentions Media Likers":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'media' => 'nullable|string',
                ];
                break;
            case "Custom Comments Package":
                $rules = [
                    'link' => 'required|url',
                    'comments' => 'required|string',
                ];
                break;
            case "Comment Likes":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                ];
                break;
            case "Poll":
                $rules = [
                    // Thêm quy tắc xác thực
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'answer_number' => 'required|integer|min:1',
                ];
                break;
            case "Comment Replies":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                    'comments' => 'required|string',
                ];
                break;
            case "Invites from Groups":
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                    'groups' => 'required|string',
                ];
                break;
            case "Subscriptions":
                $rules = [
                    'username' => 'required|string',
                    'posts' => 'nullable|integer|min:0',
                    'old_posts' => 'nullable|integer|min:0',
                    'delay' => 'nullable|integer|min:0',
                    'expiry' => 'nullable|min:0',
                ];
                $caculate = false;
                break;
            default:
                $rules = [
                    'link' => 'required|url',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
        }
        $rate = load_rate($service->rate, $user->level);
        $total_payment = $rate * 1;
        $loop_quantity = 1;

        // $validator = Validator::make($request->all(), $rules);
        if (count($rules) > 0) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => $validator->errors()->first()
                ];
            }
            // Xử lý comments nếu là Custom Comments
            if ($service->type == "Custom Comments") {
                $comments = $request->input('comments');
                $comments = explode(PHP_EOL, $comments);
                $comments = array_map('trim', $comments);
                $comments = array_filter($comments);

                // Thêm kiểm tra số lượng comments
                $commentCount = count($comments);
                if ($commentCount < $service->min || $commentCount > $service->max) {
                    return [
                        'success' => false,
                        'message' => 'Số lượng comments phải từ ' . $service->min . ' đến ' . $service->max
                    ];
                }

                $request->merge(['quantity' => $commentCount]);
            }


            // check min/max
            if ($service->min > $request->quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng tối thiểu là ' . number_format($service->min)
                ];
            }
            if ($service->max < $request->quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng tối đa là ' . number_format($service->max)
                ];
            }

            if ($caculate) {
                // Kiểm tra số dư
                $total_payment = ($rate * $request->quantity) / 1000;
            } else {
                $total_payment = 0;
            }
            $loop_quantity = $request->input('loop_quantity', 1);
            if ($request->has('loop')) {
                $total_payment = $total_payment * $loop_quantity;
            }

            if ($user->balance < $total_payment) {
                return [
                    'success' => false,
                    'message' => 'Số dư không đủ để thực hiện giao dịch này'
                ];
            }

            return [
                'success' => true,
                'rate' => $rate,
                'total_payment' => $total_payment,
                'loop_quantity' => $loop_quantity,
                'caculate' => $caculate,
            ];
        } else {
            return [
                'success' => true,
                'rate' => $rate,
                'total_payment' => $total_payment,
                'loop_quantity' => $loop_quantity,
                'caculate' => $caculate,
            ];
        }
    }

    protected function createOrderRecord(User $user, Service $service, Request $request, $data, $rootId = null, $responseData = [])
    {

        if (!$request->quantity) {
            $request->merge(['quantity' => 1]);
        }

        $order = new Order();
        $order->user_id = $user->id;
        $order->service_id = $service->id;
        $order->link = $request->input('link');
        $order->quantity = $request->quantity ?? 1;
        $order->rate = $data['rate'];
        $order->payment = $data['total_payment'];
        $order->root_id = $rootId ?? $order->id;
        $order->total = $data['total_payment'];
        $order->input_data = $request->all();
        $order->response_data = $responseData;
        $order->discount = 0;
        $order->refill = $service->refill;
        $order->cancel = $service->cancel;
        $order->dripfeed = $service->dripfeed;
        $order->provider = $service->provider ?? null;
        $order->start_count = 0;
        $order->remains = $service->mode == "option" ? ($request->quantity ?? 0) : 0;
        $order->domain = $request->getHost();

        if (isset($request->loop)) {
            $order->loop_quantity = $request->input('loop_quantity');
            $order->loop_spacing = $request->input('loop_spacing');
            $order->run = 1;
            $order->dripfeed_status = $service->dripfeed ? "Processing" : null;
        }

        if ($request->has('is_schedule_time') && $request->input('is_schedule_time') == true) {
            $order->schedule_time = $request->input('schedule_time');
            $order->root_id = $order->id;
        } else {
            $order->schedule_time = null;
        }
        $order->save();

        return $order;
    }

    protected function processPayment(User $user, $totalPayment, $order, $service)
    {
        $this->transaction([
            'user_id' => $user->id,
            'transaction_code' => $order->id,
            'type' => 'sub',
            'balance_before' => $user->balance,
            'balance_after' => $user->balance - $totalPayment,
            'amount' => $totalPayment,
            'description' => "Đơn hàng #" . $order->id,
            'status' => 'success',
            'domain' => request()->getHost()
        ]);

        $user->balance -= $totalPayment;
        $user->save();
    }

    protected function sendOrderNotification(User $user, Service $service, Request $request, $order, $isMainSite)
    {
        $text = "🛒 <b>THÔNG TIN ĐƠN HÀNG</b> 🛒\n";
        $text .= "▫️ <b>Website:</b> " . $request->getHost() . "\n";
        $text .= "👤 <b>Người dùng:</b> " . $user->username . "\n";
        $text .= "🛍️ <b>Dịch vụ:</b> [" . $service->id . "] " . $service->name . "\n";

        if ($isMainSite && $service->mode == "option") {
            $provider = ApiProvider::find($service->provider);
            $text .= "🏢 <b>Nguồn:</b> [" . $service->provider_id . "] " . $provider->name . "\n";
        } elseif (!$isMainSite) {
            $text .= "🏢 <b>Nguồn:</b> [" . $service->provider_id . "] " . $service->name . "\n";
        }

        $text .= "🔗 <b>Link:</b> " . $request->input('link') . "\n";
        $text .= "🔢 <b>Số lượng:</b> " . number_format($request->quantity ?? 1) . "\n";
        $text .= "💰 <b>Giá:</b> " . number_format($order->total) . " VND\n";
        $text .= "⏰ <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n";
        $text .= "🌐 <b>IP:</b> " . $request->ip() . "\n\n";
        $text .= "📝 <b>Thông tin bổ sung:</b>\n<pre>" . json_encode($request->all()) . "</pre>\n\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💖 Cảm ơn quý khách đã sử dụng dịch vụ!";

        $this->sendTelegram($text);
    }

    protected function handleMainSiteOrder(User $user, Service $service, Request $request, $data)
    {
        if ($service->mode == "option") {
            $provider = ApiProvider::where('id', $service->provider)
                ->where('status', 'active')
                ->first();

            if (!$provider) {
                return [
                    'success' => false,
                    'message' => 'Dịch vụ này không hỗ trợ'
                ];
            }

            $dataRequest = $request->all();
            // $dataRequest['key'] = $provider->api_key;
            $dataRequest['service'] = $service->provider_id;

            unset($dataRequest['key']);

            // nếu có is_schedule_time và is_schedule_time = true thì không cần order api mà chỉ cần tạo đơn hàng
            if ($request->has('is_schedule_time') && $request->input('is_schedule_time') == true) {
                $dataRequest['schedule_time'] = $request->input('schedule_time');
                $result = [
                    'order' => null,
                    'error' => null
                ];
            } else {
                $api = new SmmApiCustom($provider->api_url, $provider->api_key);
                // dd($dataRequest);
                $result = $api->order($dataRequest);
                if (!isset($result['order'])) {

                    return [
                        'success' => false,
                        'message' => $result['error'] ?? 'Đã có lỗi xảy ra trong quá trình xử lý'
                    ];
                }
            }

            $order = $this->createOrderRecord($user, $service, $request, $data, $result['order'], $result);
            $this->processPayment($user, $data['total_payment'], $order, $service);
            $this->sendOrderNotification($user, $service, $request, $order, true);

            return [
                'success' => true,
                'order' => $order,
                'data' => $data
            ];
        }

        // Xử lý cho các loại dịch vụ khác
        $order = $this->createOrderRecord($user, $service, $request, $data);
        $this->processPayment($user, $data['total_payment'], $order, $service);
        $this->sendOrderNotification($user, $service, $request, $order, true);

        return [
            'success' => true,
            'order' => $order,
            'data' => $data
        ];
    }

    protected function handleExternalSiteOrder(User $user, Service $service, Request $request, $data)
    {
        $adminSite = User::find(s('user_id'));
        if (!$adminSite) {
            return [
                'success' => false,
                'message' => 'Đã có lỗi xảy ra trong quá trình xử lý'
            ];
        }

        $serviceMain = Service::where('id', $service->provider_id)
            ->where('status', 'active')
            ->where('domain', s("domain_main"))
            ->first();

        if (!$serviceMain) {
            return [
                'success' => false,
                'message' => 'Dịch vụ không tồn tại'
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://' . $adminSite->domain . '/api/request-order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($request->all()),
            CURLOPT_HTTPHEADER => [
                'Api-key: ' . $adminSite->api_key,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối đến hệ thống chính'
            ];
        }

        $result = json_decode($response, true);
        if (!isset($result['success']) || !$result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Đã có lỗi xảy ra trong quá trình xử lý'
            ];
        }

        $order = $this->createOrderRecord($user, $service, $request, $data, $result['data']['root_id'] ?? null, $result);
        $this->processPayment($user, $data['total_payment'], $order, $service);
        $this->sendOrderNotification($user, $service, $request, $order, false);

        return [
            'success' => true,
            'order' => $order,
            'data' => $data
        ];
    }

    public function requestOrder(Request $request)
    {
        try {
            // Xác thực API key
            $apiKey = $request->header('Api-Key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            $user = User::where('api_key', $apiKey)
                ->where('domain', $request->getHost())
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            if ($user->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa'
                ], 403);
            }

            // Kiểm tra dịch vụ
            $serviceId = $request->input('service');
            $service = Service::where('id', $serviceId)
                ->where('status', 'active')
                ->where('domain', $request->getHost())
                ->first();

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dịch vụ không tồn tại'
                ], 404);
            }

            // Validate request
            $validation = $this->validateRequest($request, $user, $service);
            if (!$validation['success']) {
                return response()->json($validation, 400);
            }

            $isMainSite = env("MAIN_SITE") == $request->getHost();

            if ($isMainSite) {
                $result = $this->handleMainSiteOrder($user, $service, $request, $validation);
            } else {
                $result = $this->handleExternalSiteOrder($user, $service, $request, $validation);
            }

            if (!$result['success']) {
                return response()->json($result, 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order_id' => $result['order']->id,
                    'service_id' => $service->id,
                    'rate' => $validation['rate'],
                    'payment' => $validation['total_payment'],
                    'root_id' => $result['order']->root_id,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error('Order Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $th->getMessage() ?? 'Đã xảy ra lỗi hệ thống',
                'data' => null
            ], 500);
        }
    }

    public function requestProduct(Request $request)
    {
        try {
            $apiKey = $request->header('Api-Key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            $user = User::where('api_key', $apiKey)
                ->where('domain', $request->getHost())
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            if ($user->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa'
                ], 403);
            }

            $valid = Validator::make($request->all(), [
                'id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'data_input' => 'nullable',
                'coupon' => 'nullable',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ], 400);
            }

            $product = Product::where('id', $request->input('id'))
                ->where('status', 'active')
                ->where('domain', request()->getHost())
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ], 404);
            }

            if ($product->data_input != null && $request->input('data_input') == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập ' . $product->data_input
                ], 400);
            }

            $stocks = $product->stocks->where('status', 'available');
            if ($stocks->count() < $request->input('quantity')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ để thực hiện giao dịch này'
                ], 400);
            }

            $price = $product->price;
            $total_payment = $price * $request->input('quantity');
            if ($user->balance < $total_payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số dư không đủ để thực hiện giao dịch này'
                ], 400);
            }

            // lấy random stock
            $stockRandom = ProductStock::where('product_id', $product->id)
                ->where('status', 'available')
                ->inRandomOrder()
                ->take($request->input('quantity'))
                ->get();

            $data = [];
            foreach ($stockRandom as $stock) {
                $data[] = [
                    'id' => $stock->id,
                    'data' => $stock->data,
                ];
                $stock->status = 'sold';
                $stock->buyer_id = $user->id;
                $stock->domain = $request->getHost();
                $stock->save();
            }
            $data = json_encode($data);

            $order_product = new OrderProduct();
            $order_product->user_id = $user->id;
            $order_product->product_id = $product->id;
            $order_product->quantity = $request->input('quantity');
            $order_product->price = $price;
            $order_product->total = $total_payment;
            $order_product->discount = 0;
            $order_product->coupon = $request->input('coupon');
            $order_product->status = 'success';
            $order_product->data = $data;
            $order_product->data_input = $request->input('data_input');
            $order_product->domain = $request->getHost();
            $order_product->save();

            $this->transaction([
                'user_id' => $user->id,
                'transaction_code' => 'order_product_' . $order_product->id,
                'type' => 'sub',
                'balance_before' => $user->balance,
                'balance_after' => $user->balance - $total_payment,
                'amount' => $total_payment,
                'description' => "Đơn hàng sản phẩm #" . $order_product->id . " - " . $product->name,
                'status' => 'success',
                'domain' => request()->getHost()
            ]);

            $user->balance -= $total_payment;
            $user->save();
            $text = "🛒 <b>THÔNG TIN ĐƠN HÀNG SẢN PHẨM</b> 🛒\n
            ▫️ <b>Website:</b> " . $request->getHost() . "\n";
            $text .= "👤 <b>Người dùng:</b> " . $user->username . "\n";
            $text .= "🛍️ <b>Sản phẩm:</b> [" . $product->id . "] " . $product->name . "\n"
                . "🔢 <b>Số lượng:</b> " . number_format($request->input('quantity')) . "\n"
                . "💰 <b>Giá:</b> " . number_format($order_product->total) . " VND\n"
                . "⏰ <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n"
                . "🌐 <b>IP:</b> " . $request->ip() . "\n\n"
                . "📝 <b>Thông tin bổ sung:</b>\n<pre>" . json_encode($request->all()) . "</pre>\n\n"
                . "━━━━━━━━━━━━━━━━━━━━\n"
                . "💖 Cảm ơn quý khách đã sử dụng dịch vụ!";

            $this->sendTelegram($text);
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order_id' => $order_product->id,
                    'product_id' => $product->id,
                    'payment' => $total_payment,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?? 'Đã xảy ra lỗi hệ thống',
                'data' => null
            ], 500);
        }
    }

    public function requestAction(Request $request)
    {
        try {
            $apiKey = $request->header('Api-Key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            $user = User::where('api_key', $apiKey)
                ->where('domain', $request->getHost())
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            if ($user->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa'
                ], 403);
            }

            // Validate request
            $valid = Validator::make($request->all(), [
                'action' => 'required|string',
                'ids' => 'required|array',
                'ids.*' => 'required|integer',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first()
                ], 400);
            }

            $action = $request->input('action');
            $ids = $request->input('ids');
            $orders = Order::whereIn('id', $ids)
                ->where('user_id', $user->id)
                ->where('domain', $request->getHost())
                ->get();
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng không tồn tại'
                ], 404);
            }

            if ($action == 'cancel') {
                foreach ($orders as $order) {
                    if ($order->cancel != true) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Đơn hàng không hỗ trợ hủy'
                        ], 400);
                    }

                    if ($order->provider) {
                        $provider = ApiProvider::where('id', $order->provider)
                            ->where('status', 'active')
                            ->first();
                        if (!$provider) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Dịch vụ này không hỗ trợ'
                            ], 400);
                        }

                        $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                        $result = $smm->cancel([$order->root_id]);
                        if (isset($result)) {
                            foreach ($result as $key => $value) {
                                $order_id = $value['order'];
                                $order = Order::where('root_id', $order_id)->first();
                                if ($order && !is_array($value['cancel']) && $value['cancel'] == true) {
                                    $order->cancel = 0;
                                    return response()->json([
                                        'success' => true,
                                        'message' => 'Hủy đơn hàng thành công',
                                    ]);
                                } else {
                                    return response()->json([
                                        'success' => false,
                                        'message' => isset($value['cancel']['error']) ? $value['cancel']['error'] : 'Đơn hàng không hỗ trợ hủy'
                                    ], 400);
                                }
                            }
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Đơn hàng không hỗ trợ hủy'
                        ], 400);
                    }
                }
            }

            if ($action == 'refill') {
                foreach ($orders as $order) {
                    if ($order->refill != true) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Đơn hàng không hỗ trợ bảo hành'
                        ], 400);
                    }

                    $provider = ApiProvider::where('id', $order->provider)
                        ->where('status', 'active')
                        ->first();
                    if (!$provider) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Dịch vụ này không hỗ trợ'
                        ], 400);
                    }
                    $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                    $result = $smm->refill($order->root_id);
                    if (isset($result) && !$result['error']) {
                        $order->status_refill = 'Pending';
                        $order->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Đã gửi lệnh bảo hành',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => $result['error'] ?? 'Đơn hàng không hỗ trợ bảo hành'
                        ], 400);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Thao tác không hợp lệ',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function transaction($data = [])
    {
        try {
            Transaction::create($data);
        } catch (\Throwable $th) {
            Log::error('Transaction Error: ' . $th->getMessage());
        }
    }

    public function sendTelegram($message)
    {
        try {
            if (s("telegram_status") == 'active') {
                $bot = new TelegramCustom(s("telegram_chat_id"), s("telegram_bot"));
                $bot->sendMessage($message);
            }
        } catch (\Throwable $th) {
            Log::error('Telegram Error: ' . $th->getMessage());
        }
    }
}
