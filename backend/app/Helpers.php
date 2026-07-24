<?php

use AmrShawky\Currency\Facade\Currency;
use App\Library\TelegramCustom;
use App\Models\Config;
use App\Models\Currency as ModelsCurrency;
use Illuminate\Support\Facades\Auth;

if (!function_exists('s')) {
    function s($column)
    {
        $request = request();
        $domain = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $domain = preg_replace('/:\d+$/', '', $domain) ?: $request->getHost();
        if (str_starts_with($domain, 'api.')) {
            $domain = substr($domain, 4);
        }
        $site = Config::where('domain', $domain)->first();
        if ($site) {
            return $site->$column;
        } else {
            return null;
        }
    }
}

if (!function_exists('str_random')) {
    function str_random($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('format_currency')) {
    function format_currency($number, $code = "VND")
    {
        $currency = ModelsCurrency::where('code', $code)->first();
        if ($currency) {
            $thousand_separator = $currency->thousand_separator;
            $decimal_separator = $currency->decimal_separator;
            $decimal_places = $currency->decimal_places;
            $position = $currency->position;

            if ($position == 'left') {
                return $currency->symbol . number_format($number, $decimal_places, $decimal_separator, $thousand_separator);
            } else {
                return number_format($number, $decimal_places, $decimal_separator, $thousand_separator) . '' . $currency->symbol;
            }
        } else {
            return number_format($number, 0, '.', ',');
        }
    }
}

// if (!function_exists('get_currency')) {
//     function get_currency($code = 'VND')
//     {
//         $apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD';
//         $response = file_get_contents($apiUrl);
//         $data = json_decode($response, true);
//         return $data['rates'][$code] ?? 0;
//     }
// }

if (!function_exists('convert_currency')) {
    function convert_currency($amount, $to = null, $no_format = true)
    {
        static $currencyCache = [];

        // Mặc định là USD
        $defaultCurrency = 'USD';
        $user = Auth::user();
        $to = $to ?? ($user ? $user->currency : $defaultCurrency);

        // Lấy thông tin currency đích và VND
        if (!array_key_exists($to, $currencyCache)) {
            $currencyCache[$to] = ModelsCurrency::where('code', $to)->first();
        }
        if (!array_key_exists('VND', $currencyCache)) {
            $currencyCache['VND'] = ModelsCurrency::where('code', 'VND')->first();
        }
        $targetCurrency = $currencyCache[$to];
        $vndCurrency = $currencyCache['VND'];

        // Fallback nếu không có currency
        if (!$targetCurrency) {
            if (!array_key_exists($defaultCurrency, $currencyCache)) {
                $currencyCache[$defaultCurrency] = ModelsCurrency::where('code', $defaultCurrency)->first();
            }
            $targetCurrency = $currencyCache[$defaultCurrency];
            if (!$targetCurrency) {
                return $no_format ? $amount : number_format($amount, 0, '.', ',');
            }
        }

        // TH1: Chuyển từ VND sang USD (hoặc currency khác)
        if ($targetCurrency->code != "VND") {
            $exchangeRate = $vndCurrency->exchange_rate;
            $convertedAmount = $amount / $exchangeRate;

            // Làm tròn 3 chữ số thập phân cho USD
            if ($targetCurrency->code == "USD") {
                $convertedAmount = $convertedAmount;
            }

            // Áp dụng tối thiểu 1$ nếu là USD
            if ($targetCurrency->code == "USD" && $convertedAmount < 1) {
                $convertedAmount = $convertedAmount; // Tối thiểu 0.001$
            }
        }
        // TH2: Chuyển từ USD (hoặc currency khác) sang VND
        elseif ($vndCurrency) {
            $exchangeRate = $vndCurrency->exchange_rate;
            $convertedAmount = $amount;
        }
        // TH3: Giữ nguyên nếu là VND → VND
        else {
            $convertedAmount = $amount;
        }

        // Nếu không cần định dạng
        if ($no_format) {
            return $convertedAmount;
        }

        // Định dạng số tiền (USD luôn hiển thị 3 số thập phân)
        $decimal_places = ($targetCurrency->code == "USD") ? 5 : $targetCurrency->decimal_places;
        $thousand_separator = $targetCurrency->thousand_separator == 'dot' ? '.' : ',';
        $decimal_separator = $targetCurrency->decimal_separator == 'dot' ? '.' : ',';

        $formattedAmount = number_format(
            $convertedAmount,
            $decimal_places,
            $decimal_separator,
            $thousand_separator
        );

        if ($targetCurrency->code == 'VND') {
            return $formattedAmount . '₫';
        }
        return $formattedAmount . ' ' . $targetCurrency->code;
    }
}

if (!function_exists('level_user')) {
    function level_user($level, $html = false)
    {
        $levels = [
            'member' => ["Thành viên", "bg-primary"],
            'silver' => ["Bạc", "bg-silver"],
            'gold' => ["Vàng", "bg-gold"],
            'platinum' => ["Bạch kim", "bg-platinum"],
            'diamond' => ["Kim cương", "bg-diamond"],
        ];
        if (array_key_exists($level, $levels)) {
            if ($html) {
                return '<span class="badge ' . $levels[$level][1] . '">' . $levels[$level][0] . '</span>';
            } else {
                return $levels[$level][0];
            }
        } else {
            return "Thành viên";
        }
    }
}

if (!function_exists('send_telegram')) {
    function send_telegram($message, $domain = null)
    {
        static $siteCache = [];
        try {
            $currentDomain = $domain ?? request()->getHost();
            if (!isset($siteCache[$currentDomain])) {
                $siteCache[$currentDomain] = Config::where('domain', $currentDomain)->first();
            }
            $site = $siteCache[$currentDomain];
            
            if (!$site) {
                return;
            }
            if ($site->telegram_status == 'active') {
                $bot = new TelegramCustom($site->telegram_chat_id, $site->telegram_bot);
                $bot->sendMessage($message);
            }
        } catch (\Throwable $th) {
            // Handle exception
        }
    }
}

if (!function_exists('json_currency')) {
    function json_currency($code = null)
    {
        // $code = 'VND';
        if (!$code) {
            $code = Auth::check() ? Auth::user()->currency : 'VND';
        }
        $currency = ModelsCurrency::where('code', $code)->first();
        if ($currency) {
            return [
                'code' => $currency->code,
                'symbol' => $currency->symbol,
                'exchange_rate' => $currency->exchange_rate,
                'thousand_separator' => $currency->thousand_separator,
                'decimal_separator' => $currency->decimal_separator,
                'decimal_places' => $currency->decimal_places,
                'position' => $currency->position,
            ];
        } else {
            // usd
            return [
                'code' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 1,
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'decimal_places' => 2,
                'position' => 'left',
            ];
        }
    }
}

if (!function_exists('load_rate')) {
    function load_rate($service_or_rate, $level)
    {
        $rate = 0;
        
        if (is_object($service_or_rate)) {
            $service = $service_or_rate;
            $rate = $service->rate;

            if ($level == 'silver' && isset($service->price_collaborator) && $service->price_collaborator > 0) {
                return round($service->price_collaborator, 4);
            }
            if ($level == 'gold' && isset($service->price_agency) && $service->price_agency > 0) {
                return round($service->price_agency, 4);
            }
            if (($level == 'platinum' || $level == 'diamond') && isset($service->price_distributor) && $service->price_distributor > 0) {
                return round($service->price_distributor, 4);
            }
        } else {
            $rate = $service_or_rate;
        }

        $percent = 0;
        switch ($level) {
            case 'silver':
                $percent = s('silver_rank');
                break;
            case 'gold':
                $percent = s('gold_rank');
                break;
            case 'platinum':
                $percent = s('platinum_rank');
                break;
            case 'diamond':
                $percent = s('diamond_rank');
                break;
            default:
                $percent = 0;
                break;
        }

        // giảm giá theo % theo cấp bậc
        if ($percent > 0) {
            $rate = $rate - ($rate * $percent / 100);
        }
        return round($rate, 4);
    }
}

if (!function_exists('attribute_views')) {
    function attribute_views($code = null, $html = false, $is_array = false)
    {
        /* "best_seller": "Bán chạy nhất",
            "new": "Mới nhất",
            "run_now": "Chạy ngay lập tức",
            "fast": "Nhanh nhất",
            "slow": "Chậm nhất",
            "cancel_button": "Huỷ",
            "refill_30_days": "Bảo hành 30 ngày",
            "refill_7_days": "Bảo hành 7 ngày",
            "refill_15_days": "Bảo hành 15 ngày",
            "refill_90_days": "Bảo hành 90 ngày",
            "refill_1_year": "Bảo hành 1 năm",
            "refill_lifetime": "Bảo hành trọn đời",
            "exclusive": "Độc quyền",
            "suggested": "Đề xuất sử dụng",
            "real_user": "Người dùng thật",
            "self_produced": "Tự sản xuất",
             */
            $attributes = [
                'best_seller'      => [__('Bán chạy nhất'), "success"],
                'new'              => [__('Mới nhất'), "info"],
                'run_now'          => [__('Chạy ngay lập tức'), "primary"],
                'fast'             => [__('Nhanh nhất'), "warning"],
                'slow'             => [__('Chậm nhất'), "danger"],
                'cancel_button'    => [__('Huỷ'), "danger"],
                'no_refill'        => [__('Không bảo hành'), "danger"],
                'refill'           => [__('Bảo hành'), "success"],
            
                'refill_30_days'   => [__('Bảo hành 30 ngày'), "success"],
                'refill_7_days'    => [__('Bảo hành 7 ngày'), "success"],
                'refill_60_days'   => [__('Bảo hành 60 ngày'), "success"],
                'refill_15_days'   => [__('Bảo hành 15 ngày'), "success"],
                'refill_90_days'   => [__('Bảo hành 90 ngày'), "success"],
                'refill_365_days'  => [__('Bảo hành 365 ngày'), "success"],
                'refill_1_year'    => [__('Bảo hành 1 năm'), "success"],
                'refill_lifetime'  => [__('Bảo hành trọn đời'), "success"],
            
                'exclusive'        => [__('Độc quyền'), "primary"],
                'suggested'        => [__('Đề xuất sử dụng'), "info"],
                'real_user'        => [__('Người dùng thật'), "warning"],
                'self_produced'    => [__('Tự sản xuất'), "danger"],
            ];

        // nếu key no_refill thì không
        unset($attributes['no_refill']);

        if ($is_array == true) {
            foreach ($attributes as $key => $value) {
                $attributes[$key][0] = __($value[0]);
            }
            return $attributes;
        }

        if (array_key_exists($code, $attributes)) {
            if ($html) {
                return '<span class="badge bg-' . $attributes[$code][1] . ' fw-bold">' . __($attributes[$code][0]) . '</span>';
            } else {
                return __($attributes[$code][0]);
            }
        } else {
            return "";
        }
    }
}

if (!function_exists('warrantyMappingsFC')) {
    function warrantyMappingsFC()
    {
        $warrantyMappings = [
            'refill_7_days' => ['Bảo hành 7 ngày', '7 Days Guaranteed', '7 Days refill', '7D Refill'],
            'refill_15_days' => ['Bảo hành 15 ngày', '15 Days Guaranteed', '15 Days refill', '15D Refill'],
            'refill_30_days' => ['Bảo hành 30 ngày', '30 Days Guaranteed', '30 Days refill', 'REFILL 30D', '30D Refill', '30 Day Refill'],
            'refill_60_days' => ['Bảo hành 60 ngày', '60 Days Guaranteed', '60 Days refill', '60D Refill'],
            'refill_90_days' => ['Bảo hành 90 ngày', '90 Days Guaranteed', '90 Days refill', '90D Refill'],
            'refill_365_days' => ['Bảo hành 365 ngày', '365 Days Guaranteed', '365 Days refill', '365 Days', '365D Refill'],
            'refill_1_year' => ['Bảo hành 1 năm', '1 Year Guaranteed', '1 Year refill'],
            'refill_lifetime' => ['Bảo hành trọn đời', 'Bảo hành vĩnh viễn', 'Lifetime Guaranteed', 'Lifetime refill']
        ];

        return $warrantyMappings;
    }
}

if (!function_exists('status_order')) {
    function status_order($status, $html = false)
    {
        // Đặt TEXT tiếng Việt để Laravel có thể dịch qua __('...')
        $statusKey = [
            'Pending' => [__('Chờ xử lý'), "bg-warning"],
            'Processing' => [__('Đang xử lý'), "bg-primary"],
            'Active' => [__('Đang hoạt động'), "bg-primary"],
            'In progress' => [__('Đang chạy'), "bg-info"],
            'Completed' => [__('Hoàn thành'), "bg-success"],
            'Partial' => [__('Hoàn tiền một phần'), "bg-danger"],
            'Canceled' => [__('Huỷ'), "bg-danger"],
        ];

        if (array_key_exists($status, $statusKey)) {
            $text = $statusKey[$status][0];
            $badge = $statusKey[$status][1];

            return $html
                ? '<span class="badge ' . $badge . '">' . $text . '</span>'
                : $text;
        }

        return "";
    }
}

if (!function_exists('getListMenuItems')) {
    function getListMenuItems()
    {

        $services = [
            [
                'name' => "Trang thống kê",
                'icon' => "ki-chart-line-up",
                'breadcrumb' => false,
                'route' => route('statistics'),
            ],
            [
                'name' => "Dịch vụ",
                'breadcrumb' => true,
            ],
            [
                'name' => "Đặt hàng",
                'icon' => "ki-purchase",
                'breadcrumb' => false,
                'route' => route('new'),
            ],
            [
                'name' => "Đặt hàng SLL",
                'icon' => "ki-basket",
                'breadcrumb' => false,
                'route' => route('mass'),
            ],
            [
                'name' => "Danh sách",
                'icon' => "ki-burger-menu",
                'breadcrumb' => false,
                'route' => route('services'),
            ],
            [
                'name' => "Đơn hàng",
                'icon' => "ki-cheque",
                'breadcrumb' => false,
                'route' => route('orders'),
            ],
            [
                'name' => "Subscriptions",
                'icon' => "ki-colors-square",
                'breadcrumb' => false,
                'route' => route('subscriptions'),
            ],
            [
                'name' => "Đơn lặp lại",
                'icon' => "ki-scroll",
                'breadcrumb' => false,
                'route' => route('drip-feed'),
            ],
            [
                'name' => "Bảo hành",
                'icon' => "ki-filter-tick",
                'breadcrumb' => false,
                'route' => route('refill'),
            ],
            [
                'name' => "Sản phẩm",
                'breadcrumb' => true,
            ],
            [
                'name' => "Danh sách",
                'icon' => "ki-package",
                'breadcrumb' => false,
                'route' => route('products'),
            ],
            [
                'name' => "Đơn hàng",
                'icon' => "ki-basket",
                'breadcrumb' => false,
                'route' => route('products_orders'),
            ],
        ];

        // tài chính
        $finances = [
            [
                'name' => "Tài chính",
                'breadcrumb' => true,
            ],
            [
                'name' => "Nạp tiền",
                'icon' => "ki-two-credit-cart",
                'breadcrumb' => false,
                'route' => route('addfuns'),
            ],
            [
                'name' => "Dòng tiền",
                'icon' => "ki-wallet",
                'breadcrumb' => false,
                'route' => route('cashflow'),
            ]
        ];

        //Hỗ trợ & Cấu hình
        $support = [
            [
                'name' => "Hỗ trợ & Cấu hình",
                'breadcrumb' => true,
            ],
            [
                'name' => "Hỗ trợ",
                'icon' => "ki-support-24",
                'breadcrumb' => false,
                'route' => route('tickets'),
            ],
            [
                'name' => "Cấu hình",
                'icon' => "ki-like-shapes",
                'breadcrumb' => false,
                'route' => route('settings'),
            ]
        ];

        // Khác
        $other = [
            [
                'name' => "Khác",
                'breadcrumb' => true,
            ],
            [
                'name' => "Bài viết",
                'icon' => "ki-menu",
                'breadcrumb' => false,
                'route' => route('posts'),
            ],
            [
                'name' => "Cập nhật",
                'icon' => "ki-element-plus",
                'breadcrumb' => false,
                'route' => route('update'),
            ],
            [
                'name' => "Kiếm tiền",
                'icon' => "ki-bill",
                'breadcrumb' => false,
                'route' => route('affiliate'),
            ],
            [
                'name' => "Tạo website riêng",
                'icon' => "ki-switch",
                'breadcrumb' => false,
                'route' => route('childpanel'),
            ],
            [
                'name' => "API",
                'icon' => "ki-code",
                'breadcrumb' => false,
                'route' => route('apidoc'),
            ]
        ];
        $ListMenuItems = array_merge($services, $finances, $support, $other);
        foreach ($ListMenuItems as $key => $item) {
            if (isset($item['name'])) {
                $ListMenuItems[$key]['name'] = __($item['name']);
            }
        }
        return $ListMenuItems;
    }
}

if (!function_exists('status_demo')) {
    function status_demo()
    {
        // Lấy config theo domain hiện tại
        $site = \App\Models\Config::where('domain', request()->getHost())->first();

        // Nếu không có config, coi là website hoạt động bình thường
        if (!$site) {
            return false;
        }

        // Nếu status_demo = 1 → demo mode
        // Nếu status_demo = 0 → website hoạt động bình thường
        return (int) $site->status_demo === 1;
    }
}
