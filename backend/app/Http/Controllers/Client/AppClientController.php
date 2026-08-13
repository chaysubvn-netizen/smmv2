<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Library\FpaymentCustom;
use App\Models\AccountBank;
use App\Models\AffiliateRef;
use App\Models\ApiProvider;
use App\Models\Category;
use App\Models\ChildPanel;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Platform;
use App\Models\Post;
use App\Models\RateUpdate;
use App\Models\Recharge;
use App\Models\RechargeBonus;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AppClientController extends Controller
{

   

public function new(Request $request)
{
    
    $user = Auth::user();
    $level = optional($user)->level ?? 0;
    $currency = optional($user)->currency ?? 'VND';

    // Get currency update timestamp to invalidate cache on rate change
    $currencyObj = \App\Models\Currency::where('code', $currency)->first();
    $currencyUpdate = $currencyObj ? $currencyObj->updated_at->timestamp : '0';
    
    // Get total sold count and max update time to invalidate cache
    $serviceStats = \App\Models\Service::where('domain', $request->getHost())
        ->selectRaw('SUM(sold) as total_sold, MAX(updated_at) as last_update')
        ->first();
    $totalSold = $serviceStats->total_sold ?? 0;
    $lastUpdate = $serviceStats->last_update ? Carbon::parse($serviceStats->last_update)->timestamp : '0';

    $cacheKey = 'platforms_data_' . $request->getHost() . '_' . ($user ? $level : 'member') . '_' . $currency . '_' . $currencyUpdate . '_' . $totalSold . '_' . $lastUpdate;

    $cachedPlatforms = Cache::get($cacheKey);
    if ($cachedPlatforms) {
        $platforms = $cachedPlatforms;
    } else {
        // Optimised: Eager load everything in one go
        $platforms = Platform::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->with(['categories' => function ($q) {
                $q->where('status', 'active')
                  ->orderBy('sort_order', 'asc');
            }, 'categories.services' => function ($q) {
                $q->where('status', 'active')
                  ->where('domain', request()->getHost())
                  ->with('apiProvider'); // Eager load provider
            }])
            ->get();

        // Process data in memory (no more DB queries inside loops)
        $platforms->map(function ($platform) use ($level, $currency) {
            // Filter categories that have no services if needed, or just map them
            // Current logic didn't filter, so we keep it same.
            
            $platform->categories->map(function ($category) use ($level, $currency) {
                // Access eager loaded services
                $services = $category->services;

                $services->map(function ($service) use ($level, $currency) {
                    // Tính lại rate theo level user
                    $rateWithDiscount = load_rate($service, $level);
                    $service->rate = $rateWithDiscount;

                    // Format rate theo currency user
                    $service->rate_format = convert_currency(
                        $rateWithDiscount,
                        $currency,
                        false
                    );

                    // Lấy thông tin provider từ relation đã eager load
                    $provider = $service->apiProvider;
                    $service->per = $provider ? $provider->rate_per : 1000;

                    // Giữ attributes
                    $service->attributes = $service->attributes;

                    return $service->makeHidden([
                        'provider',
                        'provider_id',
                        'rate_original',
                        'status',
                        'main_domain',
                        'domain',
                        'updated_at',
                        'created_at',
                        'apiProvider' // Hide relation if not needed in JSON
                    ]);
                });

                // Assign processed services back (though they are objects references so it modifies in place usually)
                $category->services = $services;
                return $category;
            });

            return $platform;
        });

        // Lưu cache 60 phút
        Cache::put($cacheKey, $platforms, now()->addMinutes(60));
    }

    // ✅ Lấy service đầu tiên (active) trong domain để hiển thị mặc định
    $service = Service::where('status', 'active')
        ->where('domain', $request->getHost())
        ->orderBy('id', 'asc')
        ->first();

    // ✅ Lấy thống kê đơn hàng cho sidebar
    $stats = [
        'total'      => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->count(),
        'running'    => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->whereIn('status', ['In progress', 'Active'])->count(),
        'completed'  => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->where('status', 'Completed')->count(),
        'canceled'   => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->where('status', 'Canceled')->count(),
        'processing' => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->whereIn('status', ['Processing', 'Pending'])->count(),
        'refunded'   => Order::where('user_id', Auth::id())->where('domain', $request->getHost())->where('status', 'Partial')->count(),
    ];

    // Tính % hoàn thành
    $progress_orders = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0;

    return view('client.new', compact('platforms', 'service', 'stats', 'progress_orders'));
}


    public function getServiceStats(Request $request, $id)
    {
        $service = Service::with('category')->where('id', $id)->where('domain', $request->getHost())->first();
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }

        // Get 10 most recent completed orders for this service on this domain
        $recentOrders = Order::where('service_id', $id)
            ->where('status', 'Completed')
            ->where('domain', $request->getHost())
            ->whereNotNull('updated_at')
            ->whereNotNull('created_at')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $formatDuration = function($order) {
            $seconds = $order->updated_at->diffInSeconds($order->created_at);
            // Limit to 24h for gmdate or handle manually
            if ($seconds > 86400) {
                $days = floor($seconds / 86400);
                return $days . "d " . gmdate("H:i:s", $seconds % 86400) . " / " . number_format($order->quantity);
            }
            return gmdate("H:i:s", $seconds) . " / " . number_format($order->quantity);
        };

        $newest = $recentOrders->first();
        $highest = $recentOrders->sortByDesc('quantity')->first();
        $lowest = $recentOrders->sortBy('quantity')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $service->name,
                'icon' => $service->icon ?: ($service->category ? $service->category->icon : null),
                'status' => $service->status == 'active' ? __('Ổn định') : __('Bảo trì'),
                'cancel' => $service->cancel ? __('Có') : __('Không'),
                'refill' => $service->refill ? __('Có') : __('Không'),
                'newest' => $newest ? $formatDuration($newest) : '00:00:00 / 0',
                'highest' => $highest ? $formatDuration($highest) : '00:00:00 / 0',
                'lowest' => $lowest ? $formatDuration($lowest) : '00:00:00 / 0',
            ]
        ]);
    }

    public function statistics(Request $request)
    {
        $user = Auth::user();
        $domain = $request->getHost();

        // 1. Stats Cards
        $totalOrders = Order::where('user_id', $user->id)->where('domain', $domain)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('domain', $domain)->sum('total'); // Use 'total' (or 'payment' depending on logic, 'total' is safer for order cost)
        
        // Month Deposit
        $monthDeposit = Recharge::where('user_id', $user->id)
            ->where('domain', $domain)
            ->where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('real_amount');

        // Total Products (Approximate by unique service_id in orders)
        $productsUsed = Order::where('user_id', $user->id)
            ->where('domain', $domain)
            ->distinct('service_id')
            ->count('service_id');

        // 2. Chart Data (Last 7 Days)
        $chartData = [];
        $categories = [];
        $ordersData = [];
        $depositData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStr = $date->format('M d'); // e.g. "Feb 03"
            $categories[] = $dayStr;

            $ordersCount = Order::where('user_id', $user->id)
                ->where('domain', $domain)
                ->whereDate('created_at', $date)
                ->count();
            $ordersData[] = $ordersCount;

            // Deposit 
            $depositSum = Recharge::where('user_id', $user->id)
                ->where('domain', $domain)
                ->where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('real_amount');
            $depositData[] = $depositSum;
        }

        // 3. Recent Orders
        $recentOrders = Order::where('user_id', $user->id)
            ->where('domain', $domain)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->with(['service' => function ($q) {
                // Ensure we select necessary columns to avoid errors if some don't exist
                 
            }])
            ->get();
            
        // Map service name if needed, handle null service
        $recentOrders->map(function($order){
             if($order->service){
                 $order->service_name = $order->service->name;
             } else {
                 $order->service_name = 'Service Deleted';
             }
             return $order;
        });


        return view('client.statistics', compact(
            'user',
            'totalOrders', 
            'totalSpent', 
            'monthDeposit', 
            'productsUsed', 
            'categories', 
            'ordersData', 
            'depositData', 
            'recentOrders'
        ));
    }


    public function mass()
    {
      
        return view('client.mass');
    }

    public function settings()
    {
        $google2fa = new Google2FA;

        $secret = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            ucfirst(request()->getHost()),
            Auth::user()->username,
            $secret
        );

        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrCodeUrl);

        return view('client.settings', [
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);
    }

    public function services(Request $request)
    {
        $search   = $request->get('search');
        $platformId = $request->get('platforms');   // Changed to platform ID preference, but supports name fallback if needed
        $category = $request->get('category'); // filter theo category id
    
        $cacheKey = 'services_page_v2_' . $request->getHost() . '_' . ($platformId ?? 'all') . '_' . ($category ?? 'all') . '_' . ($search ? md5($search) : 'none');
        
        // Cache for 30 minutes
        $categories = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($platformId, $category, $search, $request) {
            $serviceFilter = function ($q) use ($search, $request) {
                    $q->where('status', 'active')
                      ->where('domain', $request->getHost());

                    if (!empty($search)) {
                        $q->where(function ($query) use ($search) {
                            $query->where('id', 'like', "%{$search}%")
                                  ->orWhere('name', 'like', "%{$search}%")
                                  ->orWhere('description', 'like', "%{$search}%");
                        });
                    }
                };

            $query = Category::where('status', 'active')
                ->whereHas('services', $serviceFilter)
                ->with(['platform', 'services' => $serviceFilter]);

            // ✅ Lọc theo nền tảng (Platform ID)
            if (!empty($platformId)) {
                // Check if input is numeric (ID) or string (Name)
                if (is_numeric($platformId)) {
                    $query->where('platform_id', $platformId);
                } else {
                     // Fallback: Tìm ID theo tên nếu URL cũ dùng tên
                     $p = Platform::where('name', 'like', "%{$platformId}%")->first();
                     if ($p) {
                         $query->where('platform_id', $p->id);
                     }
                }
            }

            // ✅ Lọc theo category
            if (!empty($category)) {
                $query->where('id', $category);
            }

            return $query->orderBy('sort_order', 'asc')->get();
        });
    
        // ✅ Lấy danh sách nền tảng chuẩn từ Database (có ảnh)
        $allPlatforms = Cache::remember('all_platforms_active_' . $request->getHost(), now()->addMinutes(60), function () {
            return Platform::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        });

        // ✅ Lấy tất cả category (để lọc tiếp theo)
        $allCategories = Cache::remember('all_categories_active_' . $request->getHost(), now()->addMinutes(60), function () use ($request) {
            return Category::where('status', 'active')
                ->whereHas('services', function ($q) use ($request) {
                    $q->where('status', 'active')->where('domain', $request->getHost());
                })
                ->get();
        });
    
        return view('client.services', [
            'categories'    => $categories,
            'search'        => $search,
            'platforms'     => $platformId,
            'category'      => $category,
            'allPlatforms'  => $allPlatforms,
            'allCategories' => $allCategories,
        ]);
    }


   public function addfuns(Request $request)
{
    $bankings = AccountBank::where('status', 'active')
        ->where('domain', request()->getHost())
        ->get();

    $bonusBankings = RechargeBonus::where('status', 'active')
        ->where('domain', request()->getHost())
        ->where('type', 'banking')
        ->get();

        $bonusBinance = RechargeBonus::where('status', 'active')
        ->where('domain', request()->getHost())
        ->where('type', 'binance')
        ->get();


    $bonusUsdt = RechargeBonus::where('status', 'active')
        ->where('domain', request()->getHost())
        ->where('type', 'usdt')
        ->get();

    // ✅ Query có điều kiện
    $query = Recharge::where('user_id', Auth::user()->id)
        ->where('domain', request()->getHost());

    // 🔍 Tìm kiếm theo ID, loại, ghi chú
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%$search%")
              ->orWhere('type', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }

    // 📅 Lọc theo ngày
    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $recharges = $query->orderBy('id', 'desc')->paginate(10);

    // Check hết hạn USDT
    $recharges->map(function ($recharge) {
        if ($recharge->status != 'completed' && $recharge->type == 'usdt') {
            if (Carbon::now()->greaterThan($recharge->expired_at)) {
                $recharge->status = 'expired';
                $recharge->save();
            }
        }
        return $recharge;
    });

    return view('client.addfuns', [
        'bankings' => $bankings,
        'bonusBankings' => $bonusBankings,
        'bonusBinance' => $bonusBinance,
        'bonusUsdt' => $bonusUsdt,
        'recharges' => $recharges,
    ]);
}

    public function posts()
    {
        $posts = Post::where('status', 'active')->where('domain', request()->getHost())->get();

        return view('client.posts', [
            'posts' => $posts,
        ]);
    }

    public function postDetail($slug)
    {
        $post = Post::where('status', 'active')->where('domain', request()->getHost())->where('slug', $slug)->first();
        if (!$post) {
            return redirect()->route('posts')->with('error', 'Bài viết không tồn tại');
        }

        return view('client.post', [
            'post' => $post,
        ]);
    }

    public function affiliate()
    {

        $usersRef = AffiliateRef::where('ref_user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('client.affiliate', [
            'usersRef' => $usersRef,
        ]);
    }

public function orders(Request $request)
{
    
    $search = $request->get('search');

    $orders = Order::where('user_id', Auth::user()->id)
        ->where('domain', $request->getHost())
        ->whereHas('service', function ($query) {
            $query->where('type', '!=', "Subscriptions");
        })
        ->search($search)
        ->filter($request->only(['status']));

    // 👉 Lọc theo ngày (nếu có)
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $orders->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59',
        ]);
    }

    // Sorting
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');

    if (!in_array($sortOrder, ['asc', 'desc'])) {
        $sortOrder = 'desc';
    }

    if ($sortBy === 'total_payment') {
        $orders->orderBy('payment', $sortOrder);
    } elseif (in_array($sortBy, ['created_at', 'updated_at'])) {
        $orders->orderBy($sortBy, $sortOrder);
    } else {
        $orders->orderBy('id', 'desc');
    }

    $orders = $orders->with('service')->paginate(15);

    $orders->map(function ($order) {
        // Service is eager loaded, mapped to 'service' relation
        if ($order->service) {
            $order->service->icon = $order->service->icon;
            $order->service->rate = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency);
            $order->service->rate_format = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency, true);
        }
        return $order;
    });

    return view('client.orders', [
        'orders' => $orders,
    ]);
}

    public function dripfeed(Request $request)
    {
        $search = $request->get('search');
        $orders = Order::where('user_id', Auth::user()->id)
            ->where('domain', $request->getHost())
            ->where('dripfeed_status', '!=', null)
            ->search($search)
            ->filter($request->only(['status']))
            ->orderBy('id', 'desc')
            ->with('service')
            ->paginate(15);

        $orders->map(function ($order) {
            // Service is eager loaded
            if ($order->service) {
                $order->service->icon = $order->service->icon;
                $order->service->rate = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency);
                $order->service->rate_format = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency, true);
            }
            return $order;
        });

        return view('client.drip-feed', [
            'orders' => $orders,
        ]);
    }

    public function refill(Request $request)
    {
        $search = $request->get('search');
        $orders = Order::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->where('refill', 1)
            ->where('status_refill', '!=', null)
            ->where('domain', $request->getHost())
            ->search($search)
            ->filter($request->only(['status_refill']))
            ->with('service')
            ->paginate(15);

        $orders->map(function ($order) {
            // Service is eager loaded
            if ($order->service) {
                $order->service->icon = $order->service->icon;
                $order->service->rate = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency);
                $order->service->rate_format = convert_currency(load_rate($order->service, Auth::user()->level), Auth::user()->currency, true);
            }
            return $order;
        });

        return view('client.refill', [
            'orders' => $orders,
        ]);
    }



    public function update()
    {

        $updates = RateUpdate::where('domain', request()->getHost())->orderBy('id', 'desc')->limit(100)->get();

        return view('client.update', [
            'updates' => $updates,
        ]);
    }

    public function addfunsPost(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $method = $request->method;

        if ($method === 'binance') {
            $valid = Validator::make($request->all(), [
                'binance_order_id' => 'required|string|min:5',
                'amount' => 'required|numeric|min:1',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ]);
            }

            $fpayaz_key = s("fpayaz_key");
            if ($fpayaz_key) {
                $currencyVnd = Currency::where('code', 'VND')->first();
                $exchangeRate = $currencyVnd->exchange_rate ?? 23000;
                $amountVnd = $request->amount * $exchangeRate;

                // FPAYAZ Auto Checkout
                $recharge = new Recharge();
                $recharge->user_id = Auth::user()->id;
                $recharge->transaction_id = 'FPAYAZ_' . time() . Str::random(5);
                $recharge->request_id = $recharge->transaction_id;
                $recharge->method = 'binance';
                $recharge->type = 'binance';
                $recharge->amount = $amountVnd; // Store as VND (converted from user USDT input)
                $recharge->description = 'Nạp tiền qua Binance Pay (FPAYAZ)';
                $recharge->status = 'waiting';
                $recharge->domain = request()->getHost();
                $recharge->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Đang chuyển hướng đến trang thanh toán...',
                    'redirect_fpayaz' => [
                        'url' => 'https://fpayaz.com/checkout',
                        'data' => [
                            'KEY' => $fpayaz_key,
                            'AMOUNT' => $request->amount,
                            'BAGGAGE_FIELDS' => 'ACCOUNT_ID ACCOUNT_USER',
                            'ACCOUNT_ID' => $recharge->id,
                            'ACCOUNT_USER' => Auth::user()->username,
                            'NOTE' => $recharge->transaction_id,
                            'REDIRECT_URL' => route('addfuns', ['success' => 1]),
                        ]
                    ]
                ]);
            }

            // Fallback: Manual check Spay5s (Previous logic)
            // Kiểm tra xem mã này đã được sử dụng chưa
            $exists = Recharge::where('transaction_id', $request->binance_order_id)
                ->where('domain', $request->getHost())
                ->whereIn('status', ['waiting', 'completed'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giao dịch này đã được sử dụng hoặc đang chờ xử lý',
                ]);
            }

            $currencyVnd = Currency::where('code', 'VND')->first();
            $exchangeRate = $currencyVnd->exchange_rate ?? 23000;
            $amountVnd = $request->amount * $exchangeRate;

            // Tạo hóa đơn Binance (Chờ Admin duyệt hoặc Cron)
            $recharge = new Recharge();
            $recharge->user_id = Auth::user()->id;
            $recharge->transaction_id = $request->binance_order_id;
            $recharge->request_id = $request->binance_order_id;
            $recharge->method = 'binance';
            $recharge->type = 'binance';
            $recharge->amount = $amountVnd; // Store as VND (converted from user USDT input)
            $recharge->description = 'Nạp tiền qua Binance Pay - ND: ' . s('transfer_code') . Auth::id() . ' - Mã: ' . $request->binance_order_id;
            $recharge->status = 'waiting';
            $recharge->expired_at = now()->addMinutes(60);
            $recharge->domain = request()->getHost();
            $recharge->save();

            // Thử kiểm tra ngay lập tức
            try {
                $cron = new \App\Http\Controllers\Cron\Recharge\BinanceCronJobController();
                $isProcessed = $cron->processSingleRecharge($recharge);
                
                if ($isProcessed) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Xác minh thành công! Tiền đã được cộng vào tài khoản.',
                        'reload' => true
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore errors here, let the cron handle it later
            }

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu xác minh thành công. Hệ thống sẽ tự động cộng tiền khi tìm thấy giao dịch (thường mất 1-3 phút).',
                'reload' => true
            ]);
        }

        // Mặc định xử lý USDT FPayment
        $valid = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $merchant_id = s("usdt_wallet") ?? ""; 
        $api_key = s("usdt_wallet_token"); 

        $fpayment = new FpaymentCustom();
        $request_id = time() . Auth::user()->id;
        $callback_url = route('cron.recharge.usdt');
        $success_url = route('addfuns', ['success' => 1]);
        $cancel_url = route('addfuns', ['success' => 0]);
        $name = 'Nạp tiền vào tài khoản';
        $description = 'Nạp tiền vào tài khoản';
        $amount = $request->amount;
        $result = $fpayment->add_invoice($merchant_id, $api_key, $name, $description, $amount, $request_id, $callback_url, $success_url, $cancel_url);
        if ($result['status'] == 'success') {
            $trans_id       = $result['data']['trans_id'];
            $amount         = $result['data']['amount'];
            $status         = $result['data']['status'];
            $url_payment    = $result['data']['url_payment'];
            $currencyVnd = Currency::where('code', 'VND')->first();
            $amount = $amount * ($currencyVnd->exchange_rate ?? 23000);

            $recharge = new Recharge();
            $recharge->user_id = Auth::user()->id;
            $recharge->transaction_id = $trans_id;
            $recharge->request_id = $request_id;
            $recharge->method = 'usdt';
            $recharge->type = 'usdt';
            $recharge->amount = $amount;
            $recharge->description = $name;
            $recharge->status = $status;
            $recharge->expired_at = now()->addMinutes(60);
            $recharge->domain = request()->getHost();
            $recharge->save();

            return response()->json([
                'success' => true,
                'message' => 'Tạo hóa đơn thành công',
                'redirect' => $url_payment,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['msg'] ?? 'Lỗi không xác định',
            ]);
        }
    }



    public function childpanel()
    {

        $childs = ChildPanel::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('client.childpanel', [
            'childs' => $childs,
        ]);
    }

    public function apidoc()
    {
        return view('client.apidoc');
    }

    public function cashflow()
    {

        $transactions = Transaction::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);

        return view('client.cashflow', [
            'transactions' => $transactions,
        ]);
    }

    public function subscriptions()
    {
        $subscriptions = Order::where('user_id', Auth::user()->id)
            ->where('domain', request()->getHost())
            ->whereHas('service', function ($query) {
                $query->where('type', 'subscriptions');
            })
            ->orderBy('id', 'desc')
            ->with('service')
            ->paginate(10);

        $subscriptions->map(function ($subscription) {
            // Service is eager loaded
            if ($subscription->service) {
                $subscription->service->icon = $subscription->service->icon;
                $subscription->service->rate = convert_currency(load_rate($subscription->service, Auth::user()->level), Auth::user()->currency);
                $subscription->service->rate_format = convert_currency(load_rate($subscription->service, Auth::user()->level), Auth::user()->currency, true);
            }
            return $subscription;
        });

        return view('client.subscriptions', [
            'orders' => $subscriptions,
        ]);
    }
    public function webhookFpayaz(Request $request)
    {
        $secretKey = s('fpayaz_secret');
        if (!$secretKey) {
            return response('Secret key not found', 400);
        }

        if (!$request->has(['TRANS_ID', 'AMOUNT', 'TIMESTAMPGMT', 'HASH'])) {
            return response('Missing parameters', 400);
        }

        $trans_id = $request->TRANS_ID;
        $amount = $request->AMOUNT;
        $description = $request->DESCRIPTION ?? '';
        $timestamp = $request->TIMESTAMPGMT;
        $hash = $request->HASH;
        $account_id = $request->ACCOUNT_ID;

        $expected_hash = hash_hmac('sha256', $trans_id . $amount . $description . $timestamp, $secretKey);
        
        if ($hash != $expected_hash) {
            return response('Invalid hash', 400);
        }

        $recharge = Recharge::where('id', $account_id)
            ->where('status', 'waiting')
            ->first();

        if (!$recharge) {
            return response('Transaction already exists or not found', 200);
        }

        $user = $recharge->user;
        if (!$user) {
            return response('User not found', 404);
        }

        $recharge->update([
            'status' => 'completed',
            'transaction_id' => $trans_id,
        ]);

        $currencyVnd = Currency::where('code', 'VND')->first();
        $exchangeRate = $currencyVnd->exchange_rate ?? 23000;
        $amountVnd = $amount * $exchangeRate;

        $bonusAmount = $this->calculateBonusForFpayaz($amountVnd, $amount); 
        $realAmount = $amountVnd + $bonusAmount;

        $recharge->update([
            'real_amount' => $realAmount,
            'bonus' => $bonusAmount,
        ]);

        $balanceBefore = $user->balance;
        $user->balance += $realAmount;
        $user->total_deposit += $realAmount;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'transaction_code' => "BINANCE_" . Str::random(10),
            'type' => 'add',
            'balance_before' => $balanceBefore,
            'balance_after' => $user->balance,
            'amount' => $realAmount,
            'description' => "Nạp tiền qua Binance Pay (FPAYAZ) - $trans_id",
            'status' => 'success',
            'domain' => $recharge->domain
        ]);

        return response('OK', 200);
    }

    protected function calculateBonusForFpayaz($amountVnd, $amountUsd)
    {
        $bonuses = RechargeBonus::where('domain', request()->getHost())
            ->where('type', 'banking')
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

    public function depositCreate(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $valid = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10000',
            'bank_id' => 'required|exists:account_banks,id',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $bank = AccountBank::find($request->bank_id);
        if (!$bank || $bank->status != 'active' || $bank->domain != $request->getHost()) {
            return response()->json([
                'success' => false,
                'message' => 'Ngân hàng không hợp lệ hoặc đã ngừng hoạt động',
            ]);
        }

        // Tạo mã giao dịch duy nhất
        $transaction_id = Str::upper(Str::random(8));
        while (Recharge::where('transaction_id', $transaction_id)->where('domain', $request->getHost())->exists()) {
            $transaction_id = Str::upper(Str::random(8));
        }
        
        $recharge = new Recharge();
        $recharge->user_id = Auth::user()->id;
        $recharge->bank_id = $bank->id;
        $recharge->transaction_id = $transaction_id;
        $recharge->request_id = $transaction_id;
        $recharge->method = 'banking';
        $recharge->type = 'banking';
        $recharge->amount = $request->amount;
        $recharge->description = "Nạp tiền qua " . $bank->bank_name . " - Mã: " . $transaction_id;
        $recharge->status = 'waiting';
        $recharge->expired_at = now()->addMinutes(60);
        $recharge->domain = $request->getHost();
        $recharge->save();

        return response()->json([
            'success' => true,
            'message' => 'Tạo hóa đơn thành công. Đang chuyển hướng...',
            'redirect' => route('deposit.pay', $recharge->id),
        ]);
    }

    public function depositPay($id)
    {
        $recharge = Recharge::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('domain', request()->getHost())
            ->first();

        if (!$recharge) {
            return redirect()->route('addfuns')->with('error', 'Hóa đơn không tồn tại');
        }

        if ($recharge->status == 'completed') {
            return redirect()->route('addfuns')->with('success', 'Hóa đơn đã được thanh toán thành công');
        }

        if (now()->greaterThan($recharge->expired_at) && $recharge->status == 'waiting') {
            $recharge->status = 'expired';
            $recharge->save();
            return redirect()->route('addfuns')->with('error', 'Hóa đơn đã hết hạn');
        }

        $bank = AccountBank::find($recharge->bank_id);
        if (!$bank) {
             return redirect()->route('addfuns')->with('error', 'Không tìm thấy thông tin ngân hàng');
        }

        return view('client.payment.banking', compact('recharge', 'bank'));
    }

    public function rechargeStatus($id)
    {
        $recharge = Recharge::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('domain', request()->getHost())
            ->first();

        if (!$recharge) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json(['status' => $recharge->status]);
    }
}
