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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\Discount;

class OrderClientController extends Controller
{
    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        return str_starts_with($host, 'api.') ? substr($host, 4) : $host;
    }

    public function checkDiscount(Request $request)
    {
        $code = $request->input('code');
        $amount = $request->input('amount');

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá'
            ]);
        }

        $discount = Discount::where('code', $code)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
            })
            ->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'
            ]);
        }

        if ($discount->usage_limit > 0 && $discount->used_count >= $discount->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng'
            ]);
        }

        if ($discount->min_order_amount > 0 && $amount < $discount->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng phải từ ' . number_format($discount->min_order_amount) . 'đ mới có thể sử dụng mã này'
            ]);
        }

        $discountValue = 0;
        if ($discount->type == 'percent') {
            $discountValue = ($amount * $discount->amount) / 100;
        } else {
            $discountValue = $discount->amount;
        }

        if ($discount->max_discount_amount > 0 && $discountValue > $discount->max_discount_amount) {
            $discountValue = $discount->max_discount_amount;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => $discountValue,
            'code' => $code
        ]);
    }
    
    protected function validateRequest(Request $request, User $user, Service $service)
    {
     
        $rules = [];
        $caculate = true;
        switch ($service->type) {
            case "Default":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
            case "Package":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
            case "SEO":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'keywords' => 'required|string',
                ];
                break;
            case "Custom Comments":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'comments' => 'required|string',
                ];
                break;
            case "Mentions with Hashtags":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'usernames' => 'required|string',
                    'hashtags' => 'nullable|string',
                ];
                break;
            case "Mentions Custom List":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'usernames' => 'required|string',
                ];
                break;
            case "Mentions Hashtag":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'hashtag' => 'nullable|string',
                ];
                break;
            case "Mentions User Followers":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                ];
                break;
            case "Mentions Media Likers":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'media' => 'nullable|string',
                ];
                break;
            case "Custom Comments Package":
                $rules = [
                    'link' => 'required',
                    'comments' => 'required|string',
                ];
                break;
            case "Comment Likes":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                ];
                break;
            case "Poll":
                $rules = [
                    // Thêm quy tắc xác thực
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'answer_number' => 'required|integer|min:1',
                ];
                break;
            case "Comment Replies":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'username' => 'nullable|string',
                    'comments' => 'required|string',
                ];
                break;
            case "Invites from Groups":
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                    'groups' => 'required|string',
                ];
                break;
            case "Subscriptions":
                $rules = [
                    'username' => 'required|string',
                    'min' => 'required|integer|min:1',
                    'max' => 'required|integer|gte:min',
                    'posts' => 'nullable|integer|min:0',
                    'old_posts' => 'nullable|integer|min:0',
                    'delay' => 'nullable|integer|min:0',
                    'expiry' => 'nullable|date',
                ];
                $caculate = false;
                break;
            default:
                $rules = [
                    'link' => 'required',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
        }
        $rate = load_rate($service, $user->level);
        $total_payment = $rate * 1;
        $loop_quantity = 1;

        // Valid for both Link and UID
        if (isset($rules['link'])) {
             $rules['link'] = 'required';
        }

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
                $comments = preg_split('/\r\n|\r|\n/', $comments) ?: [];
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
            if ($service->type !== 'Subscriptions' && $service->min > $request->quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng tối thiểu là ' . number_format($service->min)
                ];
            }
            if ($service->type !== 'Subscriptions' && $service->max < $request->quantity) {
                return [
                    'success' => false,
                    'message' => 'Số lượng tối đa là ' . number_format($service->max)
                ];
            }

            // Chống tạo đơn trùng link khi chưa xử lý xong
            $linkCheck = $request->input('link') ?? $request->input('username');
            if ($linkCheck) {
                $duplicate = Order::where('service_id', $service->id)
                    ->where('link', $linkCheck)
                    ->whereIn('status', ['Pending', 'Processing', 'In Progress'])
                    ->where('domain', $this->siteHost($request))
                    ->first();

                if ($duplicate) {
                    return [
                        'success' => false,
                        'message' => 'Link hoặc ID này đang có một đơn hàng khác đang xử lý tại dịch vụ này. Vui lòng chờ đơn hàng cũ hoàn tất.'
                    ];
                }
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
        $order->response_data = $responseData;
        $order->discount = $data['discount'] ?? 0;
        $order->coupon = $data['coupon'] ?? null;
        $order->refill = $service->refill;
        $order->cancel = $service->cancel;
        $order->dripfeed = $service->dripfeed;
        $order->provider = $service->provider ?? null;
        $order->start_count = 0;
        $order->remains = $request->quantity ?? 0;
        $order->domain = $this->siteHost($request);

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
        $service->increment('sold');

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
            'domain' => $order->domain
        ]);

        $user->balance -= $totalPayment;
        $user->save();
    }

    protected function sendOrderNotification(User $user, Service $service, Request $request, $order, $isMainSite)
    {
        $text = "🛒 <b>THÔNG TIN ĐƠN HÀNG</b> 🛒\n";
        $text .= "▫️ <b>Website:</b> " . $this->siteHost($request) . "\n";
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

            // Smart Convert UID to URL
            if (isset($dataRequest['link'])) {
                $dataRequest['link'] = $this->convertUidToUrl($dataRequest['link'], $service);
            }

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
                    Log::warning('Provider did not return an order id', [
                        'service_id' => $service->id,
                        'provider_id' => $provider->id,
                        'provider_service_id' => $service->provider_id,
                        'provider_response' => $result,
                    ]);
                    $providerMessage = is_array($result)
                        ? ($result['error'] ?? $result['message'] ?? $result['status'] ?? null)
                        : null;
                    if (is_array($providerMessage) || is_object($providerMessage)) {
                        $providerMessage = json_encode($providerMessage, JSON_UNESCAPED_UNICODE);
                    }
                    $providerStatusCode = 400;
                    if (is_string($providerMessage)) {
                        $decodedProviderMessage = json_decode($providerMessage, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedProviderMessage)) {
                            $providerStatusCode = (int) ($decodedProviderMessage['code'] ?? 400);
                            $providerMessage = $decodedProviderMessage['message'] ?? $providerMessage;
                        }
                    }
                    if ((string) $providerMessage === 'neworder.error.link_duplicate') {
                        $providerStatusCode = 409;
                        $providerMessage = 'Link hoặc ID này đang có một đơn hàng được xử lý tại nhà cung cấp. Vui lòng chờ đơn cũ hoàn tất rồi thử lại.';
                    } elseif ($providerStatusCode === 429 || stripos((string) $providerMessage, 'Too Many Requests') !== false) {
                        $providerStatusCode = 429;
                        $providerMessage = 'Nhà cung cấp đang giới hạn số lượng yêu cầu. Vui lòng chờ ít phút rồi thử lại.';
                    }
                    return [
                        'success' => false,
                        'message' => $providerMessage ?: "Nhà cung cấp không trả mã đơn cho dịch vụ nguồn #{$service->provider_id}. Vui lòng đồng bộ hoặc kiểm tra lại dịch vụ.",
                        'status_code' => $providerStatusCode,
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

    private function convertUidToUrl($uid, $service)
    {
        // If not numeric, convert to string and check if it's already a URL
        if (!is_numeric($uid)) {
            // Basic check if it looks like a URL
            if (filter_var($uid, FILTER_VALIDATE_URL)) {
                return $uid;
            }
            // If not numeric and not a URL, it might be a username (e.g. tiktok/twitter), still try to convert?
            // For now, let's treat numeric UIDs as the primary use case for conversion.
            // But user might input "myusername" for instagram.
            // Let's stick to the request: "mua bằng uid" (numeric).
            // However, "username" is also often allowed on these sites.
            // If we assume everything that isn't a URL should be wrapped, we might break some specific formats.
            // But mostly, SMM panels take Link.
            // Let's wrap anything that doesn't start with http/https.
            if (!str_starts_with($uid, 'http')) {
                // fall through to logic
            } else {
                 return $uid;
            }
        }

        $platformCode = null;

        // Try to get code from Platform model
        if ($service->category && $service->category->platform) {
            $platformCode = strtolower($service->category->platform->code);
        } else if ($service->category) {
            // Fallback to category name
            $name = strtolower($service->category->name);
            if (str_contains($name, 'facebook') || str_contains($name, 'fb')) $platformCode = 'facebook';
            else if (str_contains($name, 'instagram') || str_contains($name, 'ig')) $platformCode = 'instagram';
            else if (str_contains($name, 'tiktok') || str_contains($name, 'tt')) $platformCode = 'tiktok';
            else if (str_contains($name, 'shopee')) $platformCode = 'shopee';
            else if (str_contains($name, 'telegram') || str_contains($name, 'tg')) $platformCode = 'telegram';
            else if (str_contains($name, 'youtube') || str_contains($name, 'yt')) $platformCode = 'youtube';
            else if (str_contains($name, 'twitter') || str_contains($name, 'x.com')) $platformCode = 'twitter';
            else if (str_contains($name, 'threads')) $platformCode = 'threads';
        }

        switch ($platformCode) {
            case 'facebook':
                return 'https://facebook.com/' . $uid;
            case 'instagram':
                return 'https://instagram.com/' . $uid;
            case 'tiktok':
                // TikTok is tricky with video ID vs Username.
                // If it's pure numbers, could be video ID (19 digits)
                // If username, usually text.
                // Link format for video: https://www.tiktok.com/@username/video/VIDEO_ID
                // Link format for user: https://www.tiktok.com/@username
                // If we get just numbers, we can't be 100% sure.
                // But mostly users buy ID for Profile or Video.
                // Assuming Profile for now as safest generic wrapper if input is short.
                // But users might paste video ID.
                return 'https://tiktok.com/@' . $uid; 
            case 'shopee':
                return 'https://shopee.vn/shop/' . $uid;
            case 'telegram':
                return 'https://t.me/' . $uid;
            case 'youtube':
                return 'https://youtube.com/' . $uid;
            case 'twitter':
                return 'https://twitter.com/' . $uid;
            case 'threads':
                return 'https://www.threads.net/@' . $uid;
            default:
                return $uid;
        }
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
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
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
                ->where('domain', $this->siteHost($request))
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            // Fallback for session-based auth if API key is not in header but user is logged in via session (for web usage)
            // But here we query by API key.
            // If we want to support Web UI which might not send header but is logged in:
            // But requestOrder uses API Key auth logic.
            // The Web UI 'new.blade.php' should send API Key in header or use session route.
            // Assuming Web UI uses this route and master.blade.php provides API Key.

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
                ->where(function ($query) use ($request) {
                    $query->where('domain', $this->siteHost($request))
                        ->orWhere('domain', env('MAIN_SITE', $this->siteHost($request)));
                })
                ->first();

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dịch vụ không tồn tại'
                ], 404);
            }

            // Atomic Lock để chống lặp đơn (Double submission)
            $lockKey = 'order_lock_' . $user->id . '_' . md5($request->input('link', '') . $request->input('service', ''));
            $lock = Cache::lock($lockKey, 10);

            if (!$lock->get()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hệ thống đang xử lý một đơn hàng khác của bạn, vui lòng đợi trong giây lát.'
                ], 429);
            }

            try {
                // Validate request
                $validation = $this->validateRequest($request, $user, $service);
                if (!$validation['success']) {
                    return response()->json($validation, 400);
                }

            // Handle Discount Application
            $couponCode = $request->input('coupon');
            if ($couponCode) {
                $checkReq = new Request();
                $checkReq->replace(['code' => $couponCode, 'amount' => $validation['total_payment']]);
                $discountCheck = $this->checkDiscount($checkReq)->getData();

                if ($discountCheck->success) {
                    $validation['total_payment'] -= $discountCheck->discount;
                    if ($validation['total_payment'] < 0) $validation['total_payment'] = 0;
                    $validation['discount'] = $discountCheck->discount;
                    $validation['coupon'] = $couponCode;
                    
                    // Increment usage
                    Discount::where('code', $couponCode)->increment('used_count');
                } else {
                    return response()->json(['success' => false, 'message' => $discountCheck->message], 400);
                }
            }

            $hasLocalProvider = $service->mode === 'option'
                && ApiProvider::whereKey($service->provider)->where('status', 'active')->exists();
            $isMainSite = env('MAIN_SITE') === $this->siteHost($request) || $hasLocalProvider;

            if ($isMainSite) {
                $result = $this->handleMainSiteOrder($user, $service, $request, $validation);
            } else {
                $result = $this->handleExternalSiteOrder($user, $service, $request, $validation);
            }




            if (!$result['success']) {
                $statusCode = (int) ($result['status_code'] ?? 400);
                if ($statusCode < 400 || $statusCode > 599) {
                    $statusCode = 400;
                }
                unset($result['status_code']);
                return response()->json($result, $statusCode);
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
        } finally {
            $lock->release();
        }
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
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        try {
            $user = null;
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $apiKey = $request->header('Api-Key');
                if (!$apiKey) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Thao tác không hợp lệ'
                    ], 401);
                }

                $user = User::where('api_key', $apiKey)
                    ->where('domain', $this->siteHost($request))
                    ->first();
            }

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
                ->where('domain', $this->siteHost($request))
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại'
                ], 404);
            }

            // Atomic Lock cho sản phẩm
            $lockKey = 'product_lock_' . $user->id . '_' . $product->id;
            $lock = Cache::lock($lockKey, 10);
            if (!$lock->get()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hệ thống đang xử lý đơn hàng của bạn, vui lòng đợi.'
                ], 429);
            }

            try {
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
            $discount = 0;
            
            // Check Coupon
            if ($request->filled('coupon')) {
                 $couponCode = $request->input('coupon');
                 $discountObj = Discount::where('code', $couponCode)
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
                    })
                    ->first();
                
                if ($discountObj) {
                    $canUse = true;
                    // Check limit
                    if ($discountObj->usage_limit > 0 && $discountObj->used_count >= $discountObj->usage_limit) {
                        $canUse = false;
                    }
                    // Check min amount
                    if ($discountObj->min_order_amount > 0 && $total_payment < $discountObj->min_order_amount) {
                        $canUse = false;
                    }

                    if ($canUse) {
                         if ($discountObj->type == 'percent') {
                             $discount = ($total_payment * $discountObj->amount) / 100;
                         } else {
                             $discount = $discountObj->amount;
                         }
                         
                         if ($discountObj->max_discount_amount > 0 && $discount > $discountObj->max_discount_amount) {
                             $discount = $discountObj->max_discount_amount;
                         }
                         
                         // Update usage
                         $discountObj->increment('used_count');
                    }
                }
            }

            $total_payment -= $discount;
            if ($total_payment < 0) $total_payment = 0;

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
                $stock->domain = $this->siteHost($request);
                $stock->save();
            }
            $data = json_encode($data);

            $order_product = new OrderProduct();
            $order_product->user_id = $user->id;
            $order_product->product_id = $product->id;
            $order_product->quantity = $request->input('quantity');
            $order_product->price = $price;
            $order_product->total = $total_payment;
            $order_product->discount = $discount;
            $order_product->coupon = $request->input('coupon');
            $order_product->status = 'success';
            $order_product->data = $data;
            $order_product->data_input = $request->input('data_input');
            $order_product->domain = $this->siteHost($request);
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
                'domain' => $this->siteHost($request)
            ]);

            $user->balance -= $total_payment;
            $user->save();
            $text = "🛒 <b>THÔNG TIN ĐƠN HÀNG SẢN PHẨM</b> 🛒\n
            ▫️ <b>Website:</b> " . $this->siteHost($request) . "\n";
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
        } finally {
            $lock->release();
        }
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
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        try {
            $apiKey = $request->header('Api-Key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thao tác không hợp lệ'
                ], 401);
            }

            $user = User::where('api_key', $apiKey)
                ->where('domain', $this->siteHost($request))
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
                ->where('domain', $this->siteHost($request))
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
    public function getContent(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'prompt' => 'required_if:type,custom|nullable|string',
                'count' => 'required|integer|min:1|max:50',
                'type' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ]);
            }

            $count = $request->input('count', 5);
            $type = $request->input('type');
            $prompt = $request->input('prompt');

            $geminiKey = env('GEMINI_API_KEY');
            $openAiKey = env('OPENAI_API_KEY');
            
            // Function to generate simulation content (Reusable)
            $getSimulationContent = function($type, $count, $prompt) {
                // Helper to shuffle and pick unique
                $pickUnique = function($array, $limit) {
                    if (empty($array)) return [];
                    // Shuffle to randomize order
                    shuffle($array);
                    
                    // If we need more than we have, we loop/repeat with variations
                    $result = [];
                    while (count($result) < $limit) {
                        foreach ($array as $item) {
                            if (count($result) >= $limit) break;
                            
                            // Add variation for duplicates if expanding beyond array size
                            $suffix = (count($result) >= count($array)) ? "." : ""; 
                            $result[] = $item . $suffix;
                        }
                        shuffle($array); // Re-shuffle for next pass if needed
                    }
                    return $result;
                };

                $comments = [];
                if ($type == 'seeding') {
                    $seeds = [
                        "Dịch vụ rất tốt, sẽ ủng hộ dài dài.",
                        "Support nhiệt tình, 10 điểm.", 
                        "Giao hàng nhanh hơn người yêu cũ trở mặt :v",
                        "Uy tín, chất lượng.",
                        "Giá rẻ mà ngon.",
                        "Tuyệt vời.",
                        "Đã mua lần 2, vẫn ưng ý.",
                        "Shop làm ăn có tâm.",
                        "Rất hài lòng.",
                        "Xịn xò con bò.",
                        "Mới test thử, thấy ok phết.",
                        "Có ai mua ở đây chưa? Cho xin review với.",
                        "Dùng ổn định nha, ít tụt.",
                        "Chủ shop tv nhiệt tình.",
                        "Hàng ngon bổ rẻ.",
                        "Nhanh gọn lẹ, like.",
                        "Sẽ giới thiệu bạn bè.",
                        "Web mượt, dễ dùng.",
                        "Dịch vụ tốt nhất từng dùng.",
                        "Ổn áp đó bác.",
                        "Mọi người nên thử nha.",
                        "Giao diện thân thiện, dễ order.",
                        "Nạp tiền nhanh, auto duyệt.",
                        "Like thật, comment thật, ok.",
                        "Uy tín tạo nên thương hiệu.",
                        "Chất lượng đi đôi với giá tiền.",
                        "Hỗ trợ 24/7 nhiệt tình.",
                        "Sẽ quay lại lần sau.",
                        "Quá ưng, không chê vào đâu được.",
                        "Duyệt đơn siêu tốc.",
                        "Giá cả cạnh tranh nhất thị trường.",
                        "Làm ăn uy tín, ko scam.",
                        "Đã giới thiệu cho hội bạn thân.",
                        "Service good, support nhanh.",
                        "Ổn áp, anh em cứ múc đi.",
                        "Chưa thấy lỗi lầm gì.",
                        "Tốc độ bàn thờ thật.",
                        "Rẻ mà chất lượng.",
                        "Shop này làm ăn đàng hoàng.",
                        "Triệu like cho shop.",
                        "Quá đã shop ơi.",
                        "Không uổng công tin tưởng.",
                        "Best choice trong phân khúc.",
                        "Sẽ ủng hộ dài lâu.",
                        "Đáng để trải nghiệm.",
                        "Hàng chuẩn như shop cam kết.",
                        "Vừa nạp thử, lên đơn vèo vèo.",
                        "Good job shop.",
                        "Uy tín là trên hết.",
                        "Like mạnh.",
                    ];
                    $comments = $pickUnique($seeds, $count);
                } elseif ($type == 'khen_ngoi') {
                    $praises = [
                        "Sản phẩm quá đẹp.",
                        "Chất lượng miễn bàn.",
                        "Đóng gói cẩn thận, giao nhanh.",
                        "Hàng chuẩn auth, check legit.",
                        "Xinh xỉu luôn á trời.",
                        "Đáng đồng tiền bát gạo.",
                        "Mê chữ ê kéo dài.",
                        "Chuẩn không cần chỉnh.",
                        "Siêu phẩm, everyone must have.",
                        "Đẹp hơn ảnh mẫu.",
                        "Mặc lên tôn dáng dã man.",
                        "Chất vải mát, mịn, thích lắm.",
                        "Giao hàng thần tốc, mới đặt hôm qua nay đã có.",
                        "Shop gói quà siêu cute.",
                        "Đánh giá 5 sao cho chất lượng.",
                        "Nhìn là mê, sờ là phê.",
                        "Không chê vào đâu được.",
                        "Đúng là tiền nào của nấy.",
                        "Sẽ ủng hộ shop dài dài.",
                        "Quá ưng ý lun.",
                        "Chất lượng vượt mong đợi.",
                        "Chủ shop tư vấn có tâm.",
                        "Ship nhanh như một cơn gió.",
                        "Sản phẩm y hình 100%.",
                        "Rất hài lòng về cách phục vụ.",
                        "Mua lần đầu mà ưng ý quá.",
                        "Sẽ giới thiệu người nhà mua cùng.",
                        "Đẹp xuất sắc shop ơi.",
                        "Chất liệu xịn sò.",
                        "Form dáng cực chuẩn.",
                        "Màu sắc nét, không phai.",
                        "Dùng thích mê ly.",
                        "Hàng đẹp, giá tốt.",
                        "Shop uy tín số 1.",
                        "Đóng gói chắc chắn, kỹ lưỡng.",
                        "Tặng shop 1000 sao.",
                        "Yêu shop quá đi.",
                        "Mua là nghiện đấy.",
                        "Đã nhận hàng, rất ưng.",
                        "Sẽ mua thêm màu khác.",
                        "Đáng đồng tiền.",
                        "Lần sau sẽ ghé tiếp.",
                        "Chất hơn nước cất.",
                        "Tuyệt vời ông mặt trời.",
                        "Hàng new seal xịn đét.",
                        "Mở hộp ra thơm phức.",
                        "Shop dễ thương xỉu.",
                        "Chăm sóc khách hàng tốt.",
                        "Tuyệt vời.",
                        "Nice.",
                    ];
                    $comments = $pickUnique($praises, $count);
                } elseif ($type == 'quang_cao') {
                    $ads = [
                        "Sản phẩm này đang hot lắm, mọi người tham khảo nhé.",
                        "Bên em đang có chương trình khuyến mãi siêu hời.",
                        "Inbox ngay để nhận ưu đãi độc quyền.",
                        "Hàng về ngập kho, giá sập sàn.",
                        "Cam kết chất lượng, bảo hành 1 đổi 1.",
                        "Mua ngay kẻo lỡ deal hời cả nhà ơi.",
                        "Dịch vụ uy tín số 1 thị trường.",
                        "Giải pháp tiết kiệm chi phí cho bạn.",
                        "Liên hệ ngay để được tư vấn miễn phí.",
                        "Cơ hội sở hữu sản phẩm giá tốt nhất năm.",
                        "Xả hàng tồn kho giá rẻ như cho.",
                        "Sỉ lẻ toàn quốc giá tốt nhất.",
                        "Tuyển CTV chiết khấu cao.",
                        "Hàng mới về, số lượng có hạn.",
                        "Giảm giá 50% duy nhất hôm nay.",
                        "Freeship toàn quốc cho đơn hàng đầu tiên.",
                        "Quà tặng hấp dẫn đang chờ đón bạn.",
                        "Click ngay để xem chi tiết.",
                        "Đừng bỏ lỡ cơ hội vàng.",
                        "Siêu sale cuối tháng, săn ngay kẻo hết.",
                        "Mua 1 tặng 1, nhanh tay nào.",
                        "Giá rẻ bất ngờ, chốt đơn ngay.",
                        "Hàng chính hãng, full box.",
                        "Bảo hành dài hạn, yên tâm sử dụng.",
                        "Hỗ trợ trả góp 0%.",
                        "Ship COD toàn quốc, kiểm hàng thanh toán.",
                        "Duy nhất tại livestream này.",
                        "Comment SĐT để được tư vấn.",
                        "Sản phẩm đang làm mưa làm gió.",
                        "Trend mới nhất năm nay.",
                        "Đừng để lỡ mất cơ hội này.",
                        "Ưu đãi có hạn, nhanh tay kẻo hết.",
                        "Giá gốc tại xưởng, không qua trung gian.",
                        "Nhập sỉ giá tốt nhất Vịnh Bắc Bộ.",
                        "Hàng thiết kế độc quyền.",
                        "Chất lượng khẳng định thương hiệu.",
                        "Hàng triệu khách hàng đã tin dùng.",
                        "Giải pháp làm đẹp toàn diện.",
                        "Bí quyết sống khỏe mỗi ngày.",
                        "Đầu tư thông minh, sinh lời hiệu quả.",
                        "Nâng tầm đẳng cấp của bạn.",
                        "Phong cách thời thượng.",
                        "Công nghệ đột phá mới nhất.",
                        "Tiện ích vượt trội.",
                        "An toàn tuyệt đối.",
                        "Tiết kiệm điện năng tối đa.",
                        "Thân thiện với môi trường.",
                        "Dễ dàng sử dụng.",
                        "Hiệu quả tức thì.",
                        "Chính sách hậu mãi cực tốt.",
                    ];
                    $comments = $pickUnique($ads, $count);
                } else {
                     // Custom
                     for ($i = 0; $i < $count; $i++) {
                        $comments[] = "AI Simulation " . ($i + 1) . ": " . ($prompt ?? "Nội dung ngẫu nhiên");
                     }
                }
                return implode("\n", $comments);
            };

            // --- 1. Priority: Google Gemini ---
            if ($geminiKey) {
                try {
                    $client = new Client();
                    // Gemini Prompt Structure
                    $systemPart = "Bạn là trợ lý AI tạo seeding comment mạng xã hội Việt Nam tự nhiên, đời thường. ";
                    $userPart = "Tạo $count comment ngắn (1 dòng/comment). ";
                    
                    if ($type == 'seeding') $userPart .= "Chủ đề: Seeding tự nhiên. ";
                    elseif ($type == 'quang_cao') $userPart .= "Chủ đề: Quảng cáo khéo léo. ";
                    elseif ($type == 'khen_ngoi') $userPart .= "Chủ đề: Khen ngợi 5 sao. ";
                    
                    if ($prompt) $userPart .= "Yêu cầu thêm: $prompt.";
                    $userPart .= " Chỉ trả về danh sách text thuần, không Markdown, không số thứ tự.";

                    $fullPrompt = $systemPart . $userPart;

                    $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$geminiKey}", [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'contents' => [
                                ['parts' => [['text' => $fullPrompt]]]
                            ]
                        ]
                    ]);

                    $body = json_decode($response->getBody(), true);
                    // Extract text from Gemini response structure
                    $aiText = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    if (!empty($aiText)) {
                         // Clean up
                        $lines = explode("\n", $aiText);
                        $cleaned = array_filter(array_map(function($l) {
                            return preg_replace('/^[\d\-\*\•]+\.?\s*/', '', trim($l));
                        }, $lines));

                        return response()->json([
                            'status' => 'success',
                            'data' => implode("\n", $cleaned),
                            'mode' => 'gemini'
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Gemini Error: " . $e->getMessage());
                    // Fall through to OpenAI or Simulation
                }
            }

            // --- 2. Fallback: OpenAI ---
            if ($openAiKey) {
                try {
                    $client = new Client();
                    $systemContent = "Bạn là một trợ lý AI chuyên tạo nội dung bình luận (seeding) cho mạng xã hội Việt Nam. Nhiệm vụ của bạn là tạo ra các bình luận tự nhiên, đời thường, giống người thật nhất có thể. Không dùng văn mẫu, không lặp lại y hệt nhau.";
                    $userContent = "Hãy tạo danh sách $count bình luận ngắn (mỗi bình luận 1 dòng). ";
                    
                    if ($type == 'seeding') {
                        $userContent .= "Chủ đề: Seeding tự nhiên, tương tác đời thường. ";
                    } elseif ($type == 'quang_cao') {
                        $userContent .= "Chủ đề: Quảng cáo, PR sản phẩm/dịch vụ một cách khéo léo. ";
                    } elseif ($type == 'khen_ngoi') {
                        $userContent .= "Chủ đề: Khen ngợi, đánh giá tích cực 5 sao. ";
                    }
                    
                    if ($prompt) {
                        $userContent .= "Yêu cầu chi tiết thêm: $prompt.";
                    }
    
                    $userContent .= " Chỉ trả về danh sách bình luận, ngăn cách bởi xuống dòng, không có số thứ tự đầu dòng.";
    
                    $response = $client->post('https://api.openai.com/v1/chat/completions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ],
                        'json' => [
                            'model' => 'gpt-3.5-turbo',
                            'messages' => [
                                ['role' => 'system', 'content' => $systemContent],
                                ['role' => 'user', 'content' => $userContent],
                            ],
                            'temperature' => 0.7,
                            'max_tokens' => 1000,
                        ],
                    ]);
    
                    $body = json_decode($response->getBody(), true);
                    $aiContent = $body['choices'][0]['message']['content'] ?? '';
                    
                    $lines = explode("\n", $aiContent);
                    $cleanedLines = array_map(function($line) {
                        return preg_replace('/^\d+[\.\)]\s*/', '', trim($line));
                    }, $lines);
                    $cleanedLines = array_filter($cleanedLines);
    
                    return response()->json([
                         'status' => 'success',
                         'data' => implode("\n", $cleanedLines),
                         'mode' => 'openai'
                    ]);
    
                } catch (\Exception $e) {
                     Log::error("OpenAI Error: " . $e->getMessage());
                     // Fall through
                }
            }

            // --- 3. Ultimate Fallback: Simulation ---
            return response()->json([
                'status' => 'success',
                'data' => $getSimulationContent($type, $count, $prompt),
                'mode' => 'simulation_fallback',
                'message' => 'AI đang bảo trì, sử dụng chế độ dự phòng.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
}
