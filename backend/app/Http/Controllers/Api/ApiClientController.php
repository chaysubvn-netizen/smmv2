<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Service;
use App\Models\Order;
use App\Models\ApiProvider;
use App\Library\SmmApiCustom;
use App\Models\Currency;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use App\Library\TelegramCustom;
use App\Models\Config;
use App\Models\AccountBank;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\RateUpdate;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStock;
use App\Models\OrderProduct;
use App\Models\Discount;
use App\Models\User;
use App\Models\Review;
use App\Models\AffiliateRef;
use App\Models\ChildPanel;
use App\Library\CloudflareCustom;
use App\Library\CpanelCustom;
use App\Library\FpaymentCustom;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiClientController extends Controller
{
    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        return str_starts_with($host, 'api.') ? substr($host, 4) : $host;
    }

    public function getProducts(Request $request)
    {
        $user = $request->user();
        $query = Product::query()->where('domain', $request->getHost())->where('status', 'active')->with('category:id,name')
            ->withCount(['stocks as available_stock_count' => fn ($stock) => $stock->where('status', 'available')])->withSum('order_products', 'quantity')->withAvg('reviews', 'rating');
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        if ($request->filled('category_id')) $query->where('category_id', $request->integer('category_id'));
        if ($request->filled('min_price')) $query->where('price', '>=', $request->input('min_price'));
        if ($request->filled('max_price')) $query->where('price', '<=', $request->input('max_price'));
        match ($request->input('sort')) { 'price_asc' => $query->orderByRaw('CAST(price AS DECIMAL(20,2)) ASC'), 'price_desc' => $query->orderByRaw('CAST(price AS DECIMAL(20,2)) DESC'), default => $query->orderBy('sort_order')->orderByDesc('id') };
        $data = $query->paginate(min(48, max(8, (int) $request->input('per_page', 12))));
        $currency = (string) ($user->currency ?: 'VND');
        $data->getCollection()->transform(function ($item) use ($currency) { $item->price_display = convert_currency($item->price, $currency, false); return $item; });
        $categories = ProductCategory::where('domain', $request->getHost())->where('status', 'active')->orderBy('sort_order')->get(['id', 'name']);
        return response()->json(['status' => true, 'data' => $data, 'categories' => $categories]);
    }

    public function getProduct(Request $request, string $slug)
    {
        $product = Product::where('domain', $request->getHost())->where('status', 'active')->where('slug', $slug)->with('category:id,name')
            ->withCount(['stocks as available_stock_count' => fn ($stock) => $stock->where('status', 'available')])->withSum('order_products', 'quantity')->withAvg('reviews', 'rating')->firstOrFail();
        $currency = (string) ($request->user()->currency ?: 'VND'); $product->price_display = convert_currency($product->price, $currency, false);
        $related = Product::where('domain', $request->getHost())->where('status', 'active')->where('category_id', $product->category_id)->where('id', '!=', $product->id)
            ->withCount(['stocks as available_stock_count' => fn ($stock) => $stock->where('status', 'available')])->orderBy('sort_order')->limit(4)->get();
        $related->each(fn ($item) => $item->price_display = convert_currency($item->price, $currency, false));
        $reviews = Review::where('domain', $request->getHost())->where('product_id', $product->id)->where('status', 'active')->whereNull('parent_id')
            ->with(['user:id,username', 'order_product:id,product_id,quantity,created_at', 'replies.user:id,username'])->latest('id')->get();
        $ratingCounts = []; foreach (range(1, 5) as $star) $ratingCounts[$star] = $reviews->where('rating', $star)->count();
        $purchasedOrders = OrderProduct::where('domain', $request->getHost())->where('user_id', $request->user()->id)->where('product_id', $product->id)->where('status', 'success')
            ->whereDoesntHave('review')->latest('id')->get(['id', 'quantity', 'created_at']);
        return response()->json(['status' => true, 'data' => $product, 'related' => $related, 'reviews' => $reviews, 'rating_summary' => ['average' => round((float) $reviews->avg('rating'), 1), 'count' => $reviews->count(), 'stars' => $ratingCounts], 'purchased_orders' => $purchasedOrders]);
    }

    public function storeProductReview(Request $request, int $id)
    {
        $data = $request->validate(['order_product_id' => ['required', 'integer'], 'rating' => ['required', 'integer', 'min:1', 'max:5'], 'comment' => ['required', 'string', 'max:500']]);
        $product = Product::where('domain', $request->getHost())->where('status', 'active')->findOrFail($id);
        $order = OrderProduct::where('domain', $request->getHost())->where('user_id', $request->user()->id)->where('product_id', $product->id)->where('status', 'success')->findOrFail($data['order_product_id']);
        abort_if(Review::where('order_product_id', $order->id)->exists(), 422, 'Đơn hàng này đã được đánh giá.');
        Review::create(['user_id' => $request->user()->id, 'product_id' => $product->id, 'order_product_id' => $order->id, 'rating' => $data['rating'], 'comment' => strip_tags($data['comment']), 'status' => 'active', 'domain' => $request->getHost()]);
        return response()->json(['status' => true, 'message' => 'Cảm ơn bạn đã đánh giá sản phẩm.']);
    }

    public function getProductOrders(Request $request)
    {
        $query = OrderProduct::where('domain', $request->getHost())->where('user_id', $request->user()->id)->with('product:id,name,slug,image');
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') $query->where(fn ($builder) => $builder->where('id', 'like', "%{$search}%")->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%")));
        if ($request->filled('status') && $request->input('status') !== 'all') $query->where('status', $request->string('status'));
        $base = OrderProduct::where('domain', $request->getHost())->where('user_id', $request->user()->id);
        $counts = ['all' => (clone $base)->count()]; foreach (['pending', 'processing', 'success', 'cancel'] as $status) $counts[$status] = (clone $base)->where('status', $status)->count();
        $currency = (string) ($request->user()->currency ?: 'VND'); $data = $query->latest('id')->paginate(min(50, max(10, (int) $request->input('per_page', 15))));
        $data->getCollection()->transform(function ($item) use ($currency) { $item->price_display = convert_currency($item->price, $currency, false); $item->total_display = convert_currency($item->total, $currency, false); return $item; });
        return response()->json(['status' => true, 'data' => $data, 'counts' => $counts]);
    }

    public function purchaseProduct(Request $request, int $id)
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:1000'], 'data_input' => ['nullable', 'string', 'max:5000'], 'coupon' => ['nullable', 'string', 'max:100']]);
        $domain = $request->getHost();
        try {
            $order = DB::transaction(function () use ($request, $validated, $domain, $id) {
                $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
                abort_if($user->status !== 'active', 403, 'Tài khoản của bạn đã bị khóa.');
                $product = Product::where('domain', $domain)->where('status', 'active')->whereKey($id)->firstOrFail();
                if ($product->data_input && trim((string) ($validated['data_input'] ?? '')) === '') abort(422, 'Vui lòng nhập ' . $product->data_input);
                $quantity = (int) $validated['quantity']; $stocks = ProductStock::where('domain', $domain)->where('product_id', $product->id)->where('status', 'available')->orderBy('id')->lockForUpdate()->limit($quantity)->get();
                abort_if($stocks->count() < $quantity, 422, 'Số lượng sản phẩm trong kho không đủ.');
                $subtotal = (float) $product->price * $quantity; $discountAmount = 0; $couponCode = trim((string) ($validated['coupon'] ?? ''));
                if ($couponCode !== '') {
                    $coupon = Discount::where('code', $couponCode)->where('status', 'active')->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>=', now()))->lockForUpdate()->first();
                    abort_if(!$coupon, 422, 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
                    abort_if((int) $coupon->usage_limit > 0 && (int) $coupon->used_count >= (int) $coupon->usage_limit, 422, 'Mã giảm giá đã hết lượt sử dụng.');
                    abort_if((float) $coupon->min_order_amount > $subtotal, 422, 'Đơn hàng chưa đạt giá trị tối thiểu của mã giảm giá.');
                    $discountAmount = $coupon->type === 'percent' ? $subtotal * (float) $coupon->amount / 100 : (float) $coupon->amount;
                    if ((float) $coupon->max_discount_amount > 0) $discountAmount = min($discountAmount, (float) $coupon->max_discount_amount);
                    $coupon->increment('used_count');
                }
                $total = max(0, $subtotal - $discountAmount); abort_if((float) $user->balance < $total, 422, 'Số dư không đủ để thực hiện giao dịch này.');
                $delivered = $stocks->map(fn ($stock) => ['id' => $stock->id, 'data' => $stock->data])->values()->all();
                ProductStock::whereIn('id', $stocks->pluck('id'))->update(['status' => 'sold', 'buyer_id' => $user->id]);
                $order = OrderProduct::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => $quantity, 'price' => $product->price, 'total' => $total, 'discount' => $discountAmount, 'coupon' => $couponCode ?: null, 'status' => 'success', 'data' => json_encode($delivered, JSON_UNESCAPED_UNICODE), 'data_input' => $validated['data_input'] ?? null, 'domain' => $domain]);
                Transaction::create(['user_id' => $user->id, 'transaction_code' => 'order_product_' . $order->id, 'type' => 'sub', 'balance_before' => $user->balance, 'balance_after' => (float) $user->balance - $total, 'amount' => $total, 'description' => 'Đơn hàng sản phẩm #' . $order->id . ' - ' . $product->name, 'status' => 'success', 'domain' => $domain]);
                $user->decrement('balance', $total); return $order;
            }, 3);
            return response()->json(['status' => true, 'message' => 'Đặt hàng thành công.', 'data' => ['order_id' => $order->id]]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) { throw $error; }
    }

    public function getPosts(Request $request)
    {
        $posts = Post::query()
            ->where('status', 'active')
            ->where('domain', $request->getHost())
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'content', 'image', 'created_at']);

        return response()->json(['status' => true, 'data' => $posts]);
    }

    public function getPost(Request $request, string $slug)
    {
        $post = Post::query()
            ->where('status', 'active')
            ->where('domain', $request->getHost())
            ->where('slug', $slug)
            ->first(['id', 'title', 'slug', 'content', 'image', 'created_at']);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Bài viết không tồn tại',
            ], 404);
        }

        return response()->json(['status' => true, 'data' => $post]);
    }

    public function getServiceUpdates(Request $request)
    {
        $user = $request->user();
        $updates = RateUpdate::with(['service.category.platform'])
            ->where('domain', $this->siteHost($request))
            ->whereHas('service')
            ->orderByDesc('id')
            ->paginate(min(50, max(10, (int) $request->input('per_page', 20))));

        $updates->getCollection()->transform(function (RateUpdate $update) use ($user) {
            return [
                'id' => $update->id,
                'type' => $update->type,
                'rate_old' => (float) $update->rate_old,
                'rate_new' => (float) $update->rate_new,
                'rate_old_display' => convert_currency($update->rate_old, $user->currency ?: 'VND', false),
                'rate_new_display' => convert_currency($update->rate_new, $user->currency ?: 'VND', false),
                'created_at' => optional($update->created_at)->toIso8601String(),
                'service' => [
                    'id' => $update->service?->id,
                    'name' => $update->service?->name,
                    'icon' => $update->service?->category?->icon
                        ?: $update->service?->category?->platform?->icon,
                ],
            ];
        });

        return response()->json(['status' => true, 'data' => $updates]);
    }

    public function getChildPanels(Request $request)
    {
        $config = $this->paymentConfig($request);
        $currency = $request->user()->currency ?: 'VND';
        $panels = ChildPanel::where('user_id', $request->user()->id)->orderByDesc('id')->get()->map(function ($panel) {
            return [
                'id' => $panel->id, 'domain' => $panel->name, 'status' => $panel->status,
                'status_api' => $panel->status_api, 'dns_ready' => !empty($panel->data['nameserver1']),
                'nameserver1' => $panel->data['nameserver1'] ?? null,
                'nameserver2' => $panel->data['nameserver2'] ?? null,
                'expired_at' => $panel->expired_at, 'created_at' => $panel->created_at,
            ];
        });
        return response()->json(['status' => true, 'data' => [
            'panels' => $panels,
            'monthly_fee' => (float) convert_currency($config?->child_panel_monthly_price ?? 100000, $currency, true),
            'currency' => $currency,
            'default_nameservers' => ['ns1.example.com', 'ns2.example.com'],
        ]]);
    }

    public function checkDomain(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/'],
        ], [
            'domain.regex' => 'Tên miền không hợp lệ. Vui lòng nhập domain đúng định dạng (ví dụ: example.com)'
        ]);

        $domain = strtolower(trim($validated['domain']));

        if (ChildPanel::where('name', $domain)->exists() || ChildPanel::where('domain', $domain)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tên miền này đã được sử dụng.'], 422);
        }

        $freeDomains = ['tk', 'ml', 'ga', 'cf', 'gq', 'xyz', 'top', 'site', 'website'];
        $domainParts = explode('.', $domain);
        $tld = end($domainParts);

        if (in_array(strtolower($tld), $freeDomains)) {
            return response()->json(['success' => false, 'message' => 'Tên miền miễn phí không được hỗ trợ.'], 422);
        }

        return response()->json(['success' => true, 'message' => 'Tên miền hợp lệ và có thể sử dụng.', 'domain' => $domain]);
    }

    public function createChildPanel(Request $request)
    {
        $validated = $request->validate([
            'website' => ['required', 'string', 'max:255'],
        ]);
        $domain = strtolower(trim(preg_replace('#^https?://#', '', $validated['website']), '/'));
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || preg_match('/[^a-z0-9.-]/i', $domain)) {
            return response()->json(['success' => false, 'message' => 'Tên miền không hợp lệ.'], 422);
        }
        if (ChildPanel::where('name', $domain)->exists()) return response()->json(['success' => false, 'message' => 'Tên miền đã tồn tại.'], 422);
        
        $config = $this->paymentConfig($request);
        $fee = (float) ($config?->child_panel_monthly_price ?? 100000);
        $user = $request->user()->fresh();
        if ((float) $user->balance < $fee) return response()->json(['success' => false, 'message' => 'Số dư không đủ để tạo Child Panel.'], 422);

        if ($domain === 'localhost') {
            $cloudflare = ['status' => 'success', 'data' => ['nameserver1' => 'ns1.localhost', 'nameserver2' => 'ns2.localhost']];
        } else {
            $cloudflare = (new CloudflareCustom())->addDomain($domain);
        }
        if (($cloudflare['status'] ?? null) !== 'success' && ($cloudflare['code'] ?? null) != 1061) {
            return response()->json(['success' => false, 'message' => 'Không thể thêm tên miền: ' . ($cloudflare['message'] ?? 'Lỗi Cloudflare')], 422);
        }
        if (($cloudflare['code'] ?? null) == 1061) {
            $cloudflareClient = new CloudflareCustom();
            $existing = $cloudflareClient->findDomain($domain);
            if (!$existing) return response()->json(['success' => false, 'message' => 'Tên miền đang thuộc tài khoản Cloudflare khác.'], 422);
            $cloudflare['data'] = ['zone_id' => $existing['zone_id'], 'zone_name' => $existing['name'], 'zone_status' => $existing['status'], 'nameserver1' => $existing['name_servers'][0] ?? null, 'nameserver2' => $existing['name_servers'][1] ?? null];
            $dns = $cloudflareClient->provisionChildPanelDns($existing['zone_id'], $domain);
            if (!($dns['success'] ?? false)) {
                return response()->json(['success' => false, 'message' => 'Không thể cấu hình DNS: '.($dns['errors'][0]['message'] ?? 'Lỗi Cloudflare')], 422);
            }
        }

        if ($domain !== 'localhost') {
            $apiDomain = 'api.'.$domain;
            $hosting = (new CpanelCustom())->createDomain($apiDomain);
            $hostingResult = $hosting['cpanelresult']['data'][0] ?? [];
            if ((int) ($hostingResult['result'] ?? 0) !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thêm '.$apiDomain.' vào server backend: '.($hostingResult['reason'] ?? 'Lỗi hosting'),
                ], 422);
            }
        }

        $panel = ChildPanel::create([
            'user_id' => $user->id, 'name' => $domain, 'status' => $domain === 'localhost' ? 'active' : 'pending',
            'status_api' => $domain === 'localhost' ? 'active' : 'pending',
            'data' => array_merge($cloudflare['data'] ?? [], ['api_domain' => 'api.'.$domain, 'admin_username' => '', 'admin_password' => '', 'panel_name' => '']),
            'api_key' => md5($domain . microtime(true)), 'main_domain' => $config?->domain_main,
            'domain' => $user->domain ?: $config?->domain, 'expired_at' => now()->addMonth(),
        ]);
        $balanceBefore = (float) $user->balance;
        $user->balance = $balanceBefore - $fee;
        $user->save();
        Transaction::create(['user_id' => $user->id, 'transaction_code' => 'PAY_' . Str::random(10), 'type' => 'sub', 'balance_before' => $balanceBefore, 'balance_after' => $user->balance, 'amount' => $fee, 'description' => 'Tạo Child Panel: ' . $domain, 'status' => 'success', 'domain' => $domain]);
        return response()->json(['success' => true, 'message' => 'Tạo Child Panel thành công.', 'data' => $panel]);
    }

    public function getChildPanelKey(Request $request, int $id)
    {
        $request->validate(['password' => ['required', 'string']]);
        if (!Hash::check($request->input('password'), $request->user()->password)) return response()->json(['success' => false, 'message' => 'Mật khẩu không đúng.'], 422);
        $panel = ChildPanel::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$panel) return response()->json(['success' => false, 'message' => 'Child Panel không tồn tại.'], 404);
        return response()->json(['success' => true, 'data' => ['key' => $panel->api_key, 'domain' => $panel->name, 'status' => $panel->status]]);
    }

    public function renewChildPanel(Request $request, int $id)
    {
        $panel = ChildPanel::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$panel) return response()->json(['success' => false, 'message' => 'Child Panel không tồn tại.'], 404);
        $fee = max(0, (float) ($this->paymentConfig($request)?->child_panel_monthly_price ?? 0));
        $user = $request->user()->fresh();
        if ($fee > 0 && (float) $user->balance < $fee) return response()->json(['success' => false, 'message' => 'Số dư không đủ để gia hạn Child Panel.'], 422);
        $balanceBefore = (float) $user->balance;
        $panel->expired_at = $panel->expired_at && $panel->expired_at->isFuture() ? $panel->expired_at->addMonth() : now()->addMonth();
        $panel->status = 'active'; $panel->save();
        if ($fee > 0) {
            $user->balance = $balanceBefore - $fee; $user->save();
            Transaction::create(['user_id' => $user->id, 'transaction_code' => 'RENEW_' . Str::random(10), 'type' => 'sub', 'balance_before' => $balanceBefore, 'balance_after' => $user->balance, 'amount' => $fee, 'description' => 'Gia hạn Child Panel: ' . $panel->name, 'status' => 'success', 'domain' => $panel->name]);
        }
        return response()->json(['success' => true, 'message' => 'Gia hạn Child Panel thành công.', 'data' => ['expired_at' => $panel->expired_at]]);
    }

    public function getAffiliate(Request $request)
    {
        $user = $request->user()->fresh();
        $currency = $user->currency ?: 'VND';
        $config = $this->paymentConfig($request);
        $perPage = min(50, max(10, (int) $request->input('per_page', 15)));
        $referrals = AffiliateRef::where('ref_user_id', $user->id)
            ->with('user:id,username,total_deposit')
            ->orderByDesc('id')
            ->paginate($perPage);

        $referrals->getCollection()->transform(function ($referral) use ($currency) {
            $username = (string) ($referral->user?->username ?? '');
            return [
                'id' => $referral->id,
                'username' => $username === '' ? '-' : mb_substr($username, 0, 4) . '***',
                'total_deposit' => (float) convert_currency($referral->total_deposit ?: ($referral->user?->total_deposit ?? 0), $currency, true),
                'commission' => (float) convert_currency($referral->commission, $currency, true),
                'created_at' => $referral->created_at,
            ];
        });

        $domain = preg_replace('#^https?://#', '', (string) ($user->domain ?: $config?->domain ?: $request->getHost()));
        $scheme = str_contains($domain, 'localhost') || str_starts_with($domain, '127.0.0.1') ? 'http' : 'https';

        return response()->json([
            'status' => true,
            'data' => [
                'enabled' => ($config?->affiliate_status ?? 'active') === 'active',
                'percent' => (float) ($config?->affiliate_percent ?? 0),
                'minimum_payout' => (float) convert_currency($config?->affiliate_min ?? 0, $currency, true),
                'maximum_payout' => (float) convert_currency($config?->affiliate_max ?? 0, $currency, true),
                'currency' => $currency,
                'referral_url' => "{$scheme}://{$domain}/ref/{$user->username}",
                'pending_commission' => (float) convert_currency($user->commission ?? 0, $currency, true),
                'paid_commission' => (float) convert_currency($user->total_commission ?? 0, $currency, true),
                'total_commission' => (float) convert_currency(($user->commission ?? 0) + ($user->total_commission ?? 0), $currency, true),
                'referrals' => $referrals,
            ],
        ]);
    }

    public function getCashflow(Request $request)
    {
        $user = $request->user();
        $summaryQuery = Transaction::where('user_id', $user->id)
            ->whereRaw('LOWER(status) = ?', ['success']);
        $currency = $user->currency ?: 'VND';
        $summary = [
            'balance' => (float) convert_currency($user->balance ?? 0, $currency, true),
            'deposited' => (float) convert_currency((clone $summaryQuery)->whereRaw('LOWER(type) = ?', ['add'])->sum('amount'), $currency, true),
            'spent' => (float) convert_currency((clone $summaryQuery)->whereRaw('LOWER(type) = ?', ['sub'])->sum('amount'), $currency, true),
            'successful' => (int) (clone $summaryQuery)->count(),
        ];
        $query = Transaction::where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (in_array($request->input('type'), ['add', 'sub'], true)) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min(50, max(10, (int) $request->input('per_page', 15)));
        $transactions = $query->orderByDesc('id')->paginate($perPage);

        // Older manually approved bank deposits were incorrectly saved as Binance.
        $codes = $transactions->getCollection()->map(function ($transaction) {
            if (!preg_match('/qua binance\s*-\s*(\S+)$/iu', (string) $transaction->description, $matches)) return null;
            return $matches[1];
        })->filter()->values();
        $recharges = Recharge::whereIn('transaction_id', $codes)->get()->keyBy('transaction_id');
        $bankNames = AccountBank::whereIn('id', $recharges->pluck('bank_id')->filter())->pluck('bank_name', 'id');
        $transactions->getCollection()->transform(function ($transaction) use ($recharges, $bankNames, $currency) {
            if (preg_match('/qua binance\s*-\s*(\S+)$/iu', (string) $transaction->description, $matches)) {
                $recharge = $recharges->get($matches[1]);
                if ($recharge && !in_array($recharge->type, ['binance', 'usdt'], true)) {
                    $gateway = $bankNames[$recharge->bank_id] ?? 'ngân hàng';
                    $transaction->description = "Nạp thành công qua {$gateway} - {$matches[1]}";
                }
            }
            $transaction->amount_display = convert_currency($transaction->amount, $currency, true);
            $transaction->balance_before_display = convert_currency($transaction->balance_before, $currency, true);
            $transaction->balance_after_display = convert_currency($transaction->balance_after, $currency, true);
            return $transaction;
        });

        return response()->json([
            'status' => true,
            'data' => $transactions,
            'currency' => $currency,
            'summary' => $summary,
        ]);
    }

    public function getConfig(Request $request)
    {
        $domain = $request->getHost();
        if (strpos($domain, 'api.') === 0) {
            $domain = substr($domain, 4);
        }
        $config = \App\Models\Config::where('domain', $domain)->first();
        if (!$config) {
            $config = \App\Models\Config::first();
        }

        $user = auth('sanctum')->user();
        $currency = $user ? $user->currency : ($config ? $config->currency : 'VND');

        return response()->json([
            'status' => true,
            'data' => [
                'currency' => $currency,
                'title' => $config ? $config->title : 'SMM Panel',
                'description' => $config ? $config->description : '',
                'keywords' => $config ? $config->keywords : '',
                'logo' => $config ? $config->logo : null,
                'favicon' => $config ? $config->favicon : null,
                'og_image' => $config ? $config->og_image : null,
                'og_title' => $config ? $config->og_title : null,
                'og_description' => $config ? $config->og_description : null,
                'twitter_card_type' => $config ? $config->twitter_card_type : null,
                'twitter_title' => $config ? $config->twitter_title : null,
                'twitter_description' => $config ? $config->twitter_description : null,
                'twitter_image' => $config ? $config->twitter_image : null,
                'landing_page' => $config ? ($config->landing_page ?: 'default') : 'default',
                'maintenance_mode' => $config ? $config->maintenance_mode : 'off',
                'theme_color_primary' => $config ? ($config->theme_color_primary ?: '#1677ff') : '#1677ff',
                'theme_color_sidebar' => $config ? ($config->theme_color_sidebar ?: '#10131a') : '#10131a',
                'client_nav_style' => $config ? ($config->client_nav_style ?: 'nav1') : 'nav1',
                'mobile_bottom_nav_status' => $config ? (int) ($config->mobile_bottom_nav_status ?? 0) : 0,
                'google_login_status' => $config ? $config->google_login_status : 'inactive',
                'facebook_link' => $config ? $config->facebook_link : null,
                'zalo_link' => $config ? $config->zalo_link : null,
                'telegram_link' => $config ? $config->telegram_link : null,
                'email_link' => $config ? $config->email_link : null,
                'footer_text' => $config ? $config->footer_text : null,
                'system_version' => $config ? $config->system_version : null,
                'developer_name' => $config ? $config->developer_name : null,
                'developer_url' => $config ? $config->developer_url : null,
                'script_header' => $config ? $config->script_header : null,
                'script_body' => $config ? $config->script_body : null,
                'script_footer' => $config ? $config->script_footer : null,
                'contact_widgets' => \App\Models\ContactWidget::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(['id', 'name', 'url', 'image']),
                'notice_service' => $config ? $config->notice_service : null,
                'notice_modal' => $config ? $config->notice_modal : null,
                'ranks' => [
                    'silver' => convert_currency(s('up_silver') ?? 0, $currency, true),
                    'gold' => convert_currency(s('up_gold') ?? 0, $currency, true),
                    'platinum' => convert_currency(s('up_platinum') ?? 0, $currency, true),
                    'diamond' => convert_currency(s('up_diamond') ?? 0, $currency, true),
                ]
            ]
        ]);
    }

    public function getCategories(Request $request)
    {
        $categories = Category::with('platform')->where('status', 'active')->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'status' => true,
            'data' => $categories
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function getPlatforms(Request $request)
    {
        $platforms = \App\Models\Platform::where('status', 'active')->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'status' => true,
            'data' => $platforms
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function getServices(Request $request)
    {
        $domain = $this->siteHost($request);
        
        $categoryId = $request->input('category_id');
        $query = Service::with('category.platform')
            ->where('status', 'active')
            ->where('domain', $domain);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $services = $query->orderBy('sort_order', 'asc')->get();
        
        $user = auth('sanctum')->user();
        $currencyCode = $user ? $user->currency : 'VND';
        
        $services->map(function ($service) use ($user, $currencyCode) {
            $original_rate = $service->rate;
            $rate = $user ? load_rate($service, $user->level) : $original_rate;
            $service->original_rate = convert_currency($original_rate, $currencyCode, true);
            $service->rate = convert_currency($rate, $currencyCode, true);
            return $service;
        });

        return response()->json([
            'status' => true,
            'data' => $services,
            'currency' => $currencyCode,
        ]);
    }

    public function getCurrencies(Request $request)
    {
        $currencies = Currency::where('status', 'active')->orderBy('id', 'asc')->get();
        return response()->json([
            'status' => true,
            'data' => $currencies
        ]);
    }

    public function changeCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|exists:currencies,code'
        ]);

        $user = $request->user();
        $user->currency = $request->currency;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi tiền tệ thành công'
        ]);
    }

    public function getOrders(Request $request)
    {
        $user = $request->user();
        $query = Order::with('service.category')->where('user_id', $user->id);

        $counts = [
            'all' => Order::where('user_id', $user->id)->count(),
            'Pending' => Order::where('user_id', $user->id)->where('status', 'Pending')->count(),
            'Processing' => Order::where('user_id', $user->id)->where('status', 'Processing')->count(),
            'In progress' => Order::where('user_id', $user->id)->where('status', 'In progress')->count(),
            'Completed' => Order::where('user_id', $user->id)->where('status', 'Completed')->count(),
            'Partial' => Order::where('user_id', $user->id)->where('status', 'Partial')->count(),
            'Canceled' => Order::where('user_id', $user->id)->where('status', 'Canceled')->count(),
        ];

        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            if ($request->input('search_by', 'id') === 'link') {
                $query->where('link', 'like', '%' . $request->search . '%');
            } else {
                $query->where('id', 'like', '%' . $request->search . '%');
            }
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);
        
        $currencyCode = $user->currency ?? 'VND';
        $orders->getCollection()->transform(function ($order) use ($currencyCode) {
            if ($order->total) {
                $order->total = convert_currency($order->total, $currencyCode, true);
            }
            if ($order->payment) {
                $order->payment = convert_currency($order->payment, $currencyCode, true);
            }
            if ($order->rate) {
                $order->rate = convert_currency($order->rate, $currencyCode, true);
            }
            if ($order->service) {
                $order->service->rate = convert_currency($order->service->rate, $currencyCode, true);
            }
            return $order;
        });
        
        return response()->json([
            'status' => true,
            'data' => $orders,
            'counts' => $counts
        ]);
    }

    public function getOrder(Request $request, int $id)
    {
        $order = Order::with('service.category')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        $currencyCode = $request->user()->currency ?? 'VND';
        foreach (['total', 'payment', 'rate'] as $field) {
            if ($order->{$field}) {
                $order->{$field} = convert_currency($order->{$field}, $currencyCode, true);
            }
        }
        if ($order->service && $order->service->rate) {
            $order->service->rate = convert_currency($order->service->rate, $currencyCode, true);
        }

        return response()->json([
            'status' => true,
            'data' => $order,
            'currency' => $currencyCode,
        ]);
    }

    public function getStatistics(Request $request)
    {
        $validated = $request->validate([
            'days' => ['nullable', 'integer', 'in:7,30,90'],
        ]);
        $days = (int) ($validated['days'] ?? 30);
        $user = $request->user();
        $from = now()->subDays($days - 1)->startOfDay();
        $base = Order::where('orders.user_id', $user->id)->where('orders.created_at', '>=', $from);

        $totalOrders = (clone $base)->count();
        $completedOrders = (clone $base)->where('orders.status', 'Completed')->count();
        $totalSpent = (float) convert_currency((clone $base)->sum('orders.total'), $user->currency ?? 'VND', true);
        $totalQuantity = (int) (clone $base)
            ->where('orders.status', '!=', 'Canceled')
            ->sum('orders.quantity');
        $successfulTransactions = Transaction::where('user_id', $user->id)
            ->whereRaw('LOWER(status) = ?', ['success']);
        $financialSummary = [
            'balance' => (float) convert_currency($user->balance ?? 0, $user->currency ?? 'VND', true),
            'deposited' => (float) convert_currency((clone $successfulTransactions)->whereRaw('LOWER(type) = ?', ['add'])->sum('amount'), $user->currency ?? 'VND', true),
            'spent' => (float) convert_currency((clone $successfulTransactions)->whereRaw('LOWER(type) = ?', ['sub'])->sum('amount'), $user->currency ?? 'VND', true),
            'successful' => (int) (clone $successfulTransactions)->count(),
        ];

        $dailyRows = (clone $base)
            ->selectRaw('DATE(orders.created_at) as day, COUNT(*) as orders, COALESCE(SUM(CAST(orders.total AS DECIMAL(20,4))), 0) as spent')
            ->groupByRaw('DATE(orders.created_at)')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $daily = collect(range(0, $days - 1))->map(function ($offset) use ($from, $dailyRows) {
            $day = $from->copy()->addDays($offset)->toDateString();
            $row = $dailyRows->get($day);
            return [
                'date' => $day,
                'orders' => (int) ($row->orders ?? 0),
                'spent' => (float) ($row->spent ?? 0),
            ];
        })->values();

        $statuses = (clone $base)
            ->selectRaw('orders.status, COUNT(*) as total')
            ->groupBy('orders.status')
            ->pluck('total', 'orders.status')
            ->map(fn ($value) => (int) $value);

        $topServices = (clone $base)
            ->join('services', 'orders.service_id', '=', 'services.id')
            ->selectRaw('services.id, services.name, COUNT(orders.id) as orders_count, COALESCE(SUM(CAST(orders.total AS DECIMAL(20,4))), 0) as spent')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get()
            ->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'orders_count' => (int) $service->orders_count,
                'spent' => (float) convert_currency($service->spent, $user->currency ?? 'VND', true),
            ]);

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => [
                    'orders' => $totalOrders,
                    'completed' => $completedOrders,
                    'completion_rate' => $totalOrders > 0 ? round($completedOrders * 100 / $totalOrders, 1) : 0,
                    'spent' => $totalSpent,
                    'quantity' => $totalQuantity,
                ],
                'financial_summary' => $financialSummary,
                'daily' => $daily,
                'statuses' => $statuses,
                'top_services' => $topServices,
                'currency' => $user->currency ?? 'VND',
            ],
        ]);
    }

    public function getSubscriptions(Request $request)
    {
        $user = $request->user();
        $query = Order::with('service.category')
            ->where('user_id', $user->id)
            ->whereHas('service', fn ($service) => $service->whereRaw('LOWER(type) = ?', ['subscriptions']));

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('id', 'like', "%{$search}%")
                    ->orWhere('link', 'like', "%{$search}%")
                    ->orWhere('input_data->username', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'status' => true,
            'data' => $query->orderByDesc('id')->paginate(15),
        ]);
    }

    public function getDripFeeds(Request $request)
    {
        $user = $request->user();
        $query = Order::with('service.category')
            ->where('user_id', $user->id)
            ->whereNotNull('dripfeed_status');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('dripfeed_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('id', 'like', "%{$search}%")
                    ->orWhere('link', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderByDesc('id')->paginate(15);
        $currency = $user->currency ?? 'VND';
        $orders->getCollection()->transform(function ($order) use ($currency) {
            $runs = max(1, (int) ($order->loop_quantity ?? 1));
            $total = ((float) $order->rate * ((int) $order->quantity * $runs)) / 1000;
            $order->total_quantity = (int) $order->quantity * $runs;
            $order->total_format = convert_currency($total, $currency, true);
            return $order;
        });

        return response()->json(['status' => true, 'data' => $orders]);
    }

    private function paymentConfig(Request $request): ?Config
    {
        $user = $request->user();
        return Config::where('domain', $user->domain)->first()
            ?? Config::where('domain', $request->getHost())->first()
            ?? Config::first();
    }

    public function getRechargeOptions(Request $request)
    {
        $config = $this->paymentConfig($request);
        $domains = array_filter([$request->user()->domain, $config?->domain, env('MAIN_SITE')]);
        $banks = AccountBank::where('status', 'active')
            ->whereIn('domain', array_unique($domains))
            ->orderBy('id')
            ->get(['id', 'bank_name', 'bank_code', 'account_number', 'account_name', 'icon']);

        return response()->json(['status' => true, 'data' => [
            'bank_enabled' => ($config?->bank_status ?? 'active') === 'active',
            'usdt_enabled' => ($config?->usdt_status ?? 'inactive') === 'active',
            'binance_enabled' => ($config?->binance_status ?? 'inactive') === 'active'
                && filled($config?->binance_id),
            'trc20_enabled' => ($config?->trc20_status ?? 'inactive') === 'active'
                && filled($config?->trc20_wallet),
            'usdt_exchange_rate' => (float) ($config?->usdt_exchange_rate ?? 0),
            'binance_exchange_rate' => (float) (Currency::where('code', 'VND')->value('exchange_rate') ?: 23000),
            'binance_id' => $config?->binance_id,
            'binance_qr' => $config?->binance_qr,
            'transfer_code' => ($config?->transfer_code ?? '') . $request->user()->id,
            'trc20_wallet' => $config?->trc20_wallet,
            'notice_recharge' => $config?->notice_recharge,
            'banks' => $banks,
        ]]);
    }

    public function getRecharges(Request $request)
    {
        $query = Recharge::where('user_id', $request->user()->id);
        if ($request->filled('search')) $query->where('transaction_id', 'like', '%' . $request->search . '%');
        $perPage = min(50, max(10, (int) $request->input('per_page', 15)));
        $recharges = $query->orderByDesc('id')->paginate($perPage);
        $bankNames = AccountBank::whereIn('id', $recharges->getCollection()->pluck('bank_id')->filter())->pluck('bank_name', 'id');
        $currency = $request->user()->currency ?: 'VND';
        $recharges->getCollection()->transform(function ($item) use ($bankNames, $currency) {
            $item->gateway_name = $item->bank_id ? ($bankNames[$item->bank_id] ?? 'Ngân hàng') : strtoupper((string) $item->type);
            $item->amount_display = convert_currency($item->amount, $currency, true);
            return $item;
        });
        return response()->json(['status' => true, 'data' => $recharges, 'currency' => $currency]);
    }

    public function createBankRecharge(Request $request)
    {
        $validated = $request->validate(['amount' => ['required', 'numeric', 'min:10000'], 'bank_id' => ['required', 'integer']]);
        $config = $this->paymentConfig($request);
        if (($config?->bank_status ?? 'inactive') !== 'active') return response()->json(['success' => false, 'message' => 'Cổng nạp ngân hàng đang tạm đóng.'], 422);
        $domains = array_filter([$request->user()->domain, $config->domain, env('MAIN_SITE')]);
        $bank = AccountBank::where('id', $validated['bank_id'])->where('status', 'active')->whereIn('domain', array_unique($domains))->first();
        if (!$bank) return response()->json(['success' => false, 'message' => 'Tài khoản ngân hàng không hợp lệ.'], 422);

        do { $code = Str::upper(Str::random(8)); } while (Recharge::where('transaction_id', $code)->exists());
        $recharge = Recharge::create([
            'user_id' => $request->user()->id, 'transaction_id' => $code, 'request_id' => $code,
            'method' => 'banking', 'type' => 'banking', 'amount' => $validated['amount'],
            'description' => "Nạp tiền qua {$bank->bank_name} - Mã: {$code}", 'status' => 'waiting',
            'expired_at' => now()->addMinutes(60), 'domain' => $request->user()->domain ?: $config->domain,
        ]);
        $recharge->bank_id = $bank->id;
        $recharge->save();
        return response()->json(['success' => true, 'message' => 'Tạo yêu cầu nạp tiền thành công.', 'data' => $this->rechargePaymentData($recharge, $bank)]);
    }

    public function createUsdtRecharge(Request $request)
    {
        $validated = $request->validate(['amount' => ['required', 'numeric', 'min:1']]);
        $config = $this->paymentConfig($request);
        if (($config?->usdt_status ?? 'inactive') !== 'active') return response()->json(['success' => false, 'message' => 'Cổng USDT đang tạm đóng.'], 422);
        $requestId = time() . $request->user()->id;
        $origin = rtrim((string) $request->header('Origin', env('FRONTEND_URL', 'http://127.0.0.1:3000')), '/');
        $result = (new FpaymentCustom())->add_invoice($config->usdt_wallet, $config->usdt_wallet_token, 'Nạp tiền vào tài khoản', 'Nạp tiền vào tài khoản', $validated['amount'], $requestId, route('cron.recharge.usdt'), "{$origin}/addfunds?success=1", "{$origin}/addfunds?success=0");
        if (($result['status'] ?? null) !== 'success') return response()->json(['success' => false, 'message' => $result['msg'] ?? 'Không thể tạo hóa đơn USDT.'], 422);
        $data = $result['data'];
        $amountVnd = (float) $data['amount'] * ((float) ($config->usdt_exchange_rate ?: 23000));
        Recharge::create(['user_id' => $request->user()->id, 'transaction_id' => $data['trans_id'], 'request_id' => $requestId, 'method' => 'usdt', 'type' => 'usdt', 'amount' => $amountVnd, 'description' => 'Nạp tiền vào tài khoản', 'status' => $data['status'], 'expired_at' => now()->addMinutes(60), 'domain' => $request->user()->domain ?: $config->domain]);
        return response()->json(['success' => true, 'message' => 'Tạo hóa đơn USDT thành công.', 'redirect' => $data['url_payment']]);
    }

    public function createBinanceRecharge(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'binance_order_id' => ['required', 'string', 'min:5', 'max:190'],
        ]);
        $config = $this->paymentConfig($request);
        if (($config?->binance_status ?? 'inactive') !== 'active' || blank($config?->binance_id)) {
            return response()->json(['success' => false, 'message' => 'Cổng nạp Binance đang tạm đóng.'], 422);
        }

        $orderId = trim($validated['binance_order_id']);
        $domain = $request->user()->domain ?: $config->domain;
        $exists = Recharge::where('transaction_id', $orderId)
            ->where('domain', $domain)
            ->whereIn('status', ['waiting', 'pending', 'completed'])
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Mã giao dịch Binance đã được sử dụng hoặc đang chờ xác minh.'], 422);
        }

        $exchangeRate = (float) (Currency::where('code', 'VND')->value('exchange_rate') ?: 23000);
        $amountVnd = round((float) $validated['amount'] * $exchangeRate, 2);
        $transferCode = ($config->transfer_code ?? '') . $request->user()->id;
        $recharge = Recharge::create([
            'user_id' => $request->user()->id,
            'transaction_id' => $orderId,
            'request_id' => $orderId,
            'method' => 'binance',
            'type' => 'binance',
            'amount' => $amountVnd,
            'description' => "Nạp tiền qua Binance Pay - Nội dung: {$transferCode} - Mã: {$orderId}",
            'status' => 'waiting',
            'expired_at' => now()->addMinutes(60),
            'domain' => $domain,
        ]);

        try {
            $verified = app(\App\Http\Controllers\Cron\Recharge\BinanceCronJobController::class)
                ->processSingleRecharge($recharge);
        } catch (\Throwable $exception) {
            $verified = false;
        }

        if (!$verified) {
            $recharge->delete();
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán. Vui lòng kiểm tra xem mã đơn hàng có chính xác không.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác minh thanh toán thành công. Số dư đã được cập nhật.',
            'data' => $this->binancePaymentData($recharge->fresh(), $config, $exchangeRate),
        ]);
    }

    public function createTrc20Recharge(Request $request)
    {
        $validated = $request->validate(['amount' => ['required', 'numeric', 'min:1', 'max:1000000']]);
        $config = $this->paymentConfig($request);
        if (($config?->trc20_status ?? 'inactive') !== 'active' || blank($config?->trc20_wallet)) {
            return response()->json(['success' => false, 'message' => 'Cổng USDT TRC20 đang tạm đóng.'], 422);
        }

        $expectedUsdt = round((float) $validated['amount'] + random_int(1, 999) / 1000000, 6);
        while (Recharge::where('type', 'trc20')->where('status', 'waiting')->where('request_id', number_format($expectedUsdt, 6, '.', ''))->exists()) {
            $expectedUsdt = round((float) $validated['amount'] + random_int(1, 999) / 1000000, 6);
        }
        $rate = (float) (Currency::where('code', 'VND')->value('exchange_rate') ?: 23000);
        $invoice = 'TRC20_' . Str::upper(Str::random(12));
        $recharge = Recharge::create([
            'user_id' => $request->user()->id,
            'transaction_id' => $invoice,
            'request_id' => number_format($expectedUsdt, 6, '.', ''),
            'method' => 'trc20',
            'type' => 'trc20',
            'amount' => $expectedUsdt * $rate,
            'description' => "Chờ nạp chính xác {$expectedUsdt} USDT TRC20",
            'status' => 'waiting',
            'expired_at' => now()->addMinutes(60),
            'domain' => $request->user()->domain ?: $config->domain,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo hóa đơn USDT TRC20.',
            'data' => $this->trc20PaymentData($recharge, $config),
        ]);
    }

    public function getRecharge(Request $request, int $id)
    {
        $recharge = Recharge::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $bank = $recharge->bank_id ? AccountBank::find($recharge->bank_id) : null;
        if ($bank) {
            $data = $this->rechargePaymentData($recharge, $bank);
        } elseif ($recharge->type === 'binance') {
            $config = $this->paymentConfig($request);
            $rate = (float) (Currency::where('code', 'VND')->value('exchange_rate') ?: 23000);
            $data = $this->binancePaymentData($recharge, $config, $rate);
        } elseif ($recharge->type === 'trc20') {
            $data = $this->trc20PaymentData($recharge, $this->paymentConfig($request));
        } else {
            $data = $recharge;
        }
        return response()->json(['status' => true, 'data' => $data]);
    }

    private function rechargePaymentData(Recharge $recharge, AccountBank $bank): array
    {
        $qr = 'https://img.vietqr.io/image/' . ($bank->bank_code ?: $bank->bank_name) . '-' . $bank->account_number . '-compact2.png?amount=' . $recharge->amount . '&addInfo=' . urlencode($recharge->transaction_id) . '&accountName=' . urlencode($bank->account_name);
        return ['id' => $recharge->id, 'transaction_id' => $recharge->transaction_id, 'amount' => $recharge->amount, 'status' => $recharge->status, 'expired_at' => $recharge->expired_at, 'qr_url' => $qr, 'bank' => $bank];
    }

    private function binancePaymentData(Recharge $recharge, ?Config $config, float $exchangeRate): array
    {
        return [
            'id' => $recharge->id,
            'type' => 'binance',
            'transaction_id' => $recharge->transaction_id,
            'amount' => $recharge->amount,
            'amount_usdt' => $exchangeRate > 0 ? round((float) $recharge->amount / $exchangeRate, 8) : 0,
            'status' => $recharge->status,
            'expired_at' => $recharge->expired_at,
            'qr_url' => $config?->binance_qr,
            'binance_id' => $config?->binance_id,
            'transfer_code' => ($config?->transfer_code ?? '') . $recharge->user_id,
        ];
    }

    private function trc20PaymentData(Recharge $recharge, ?Config $config): array
    {
        $wallet = (string) $config?->trc20_wallet;
        return [
            'id' => $recharge->id,
            'type' => 'trc20',
            'transaction_id' => $recharge->transaction_id,
            'amount' => $recharge->amount,
            'amount_usdt' => (float) $recharge->request_id,
            'status' => $recharge->status,
            'expired_at' => $recharge->expired_at,
            'wallet' => $wallet,
            'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . rawurlencode($wallet),
            'network' => 'TRC20',
        ];
    }

    public function actionOrder(Request $request)
    {
        $user = $request->user();
        $action = $request->input('action');
        $id = $request->input('id');

        $order = Order::where('id', $id)->where('user_id', $user->id)->first();
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại'
            ]);
        }

        if ($action == 'cancel') {
            if ($order->cancel != true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không hỗ trợ hủy'
                ]);
            }

            if ($order->provider) {
                $provider = ApiProvider::where('id', $order->provider)->where('status', 'active')->first();
                if (!$provider) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Dịch vụ này không hỗ trợ'
                    ]);
                }

                $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
                $result = $smm->cancel([$order->root_id]);
                if (isset($result)) {
                    foreach ($result as $key => $value) {
                        if (is_array($value) && isset($value['order']) && $value['order'] == $order->root_id) {
                            if (!is_array($value['cancel']) && $value['cancel'] == true) {
                                $order->cancel = 0;
                                $order->save();
                                return response()->json([
                                    'status' => true,
                                    'message' => 'Hủy đơn hàng thành công',
                                ]);
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => isset($value['cancel']['error']) ? $value['cancel']['error'] : 'Đơn hàng không hỗ trợ hủy'
                                ]);
                            }
                        }
                    }
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không hỗ trợ hủy'
            ]);
        }

        if ($action == 'refill') {
            if ($order->refill != true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng không hỗ trợ bảo hành'
                ]);
            }

            $provider = ApiProvider::where('id', $order->provider)->where('status', 'active')->first();
            if (!$provider) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dịch vụ này không hỗ trợ'
                ]);
            }
            
            $smm = new SmmApiCustom($provider->api_url, $provider->api_key);
            $result = $smm->refill($order->root_id);
            if (isset($result) && !isset($result['error'])) {
                $order->status_refill = 'Pending';
                $order->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Đã gửi lệnh bảo hành',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $result['error'] ?? 'Đơn hàng không hỗ trợ bảo hành'
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Hành động không hợp lệ'
        ]);
    }

    public function getRefills(Request $request)
    {
        $user = $request->user();
        $query = Order::where('user_id', $user->id)->where('refill', 1);

        if ($request->has('status') && $request->status) {
            $query->where('status_refill', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);
        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function createOrder(Request $request)
    {
        try {
            $user = $request->user();
            $request->merge(['service' => $request->input('service_id')]);
            $request->headers->set('Api-Key', $user->api_key);
            
            $response = app(\App\Http\Controllers\Client\OrderClientController::class)->requestOrder($request);
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine()
            ], 500);
        }
    }

    public function createMassOrders(Request $request)
    {
        $validated = $request->validate([
            'orders' => ['required', 'array', 'min:1', 'max:100'],
            'orders.*.line' => ['required', 'integer', 'min:1'],
            'orders.*.service_id' => ['required', 'integer'],
            'orders.*.link' => ['required', 'string', 'max:2000'],
            'orders.*.quantity' => ['nullable', 'integer', 'min:1'],
            'orders.*.comments' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $results = [];
        foreach ($validated['orders'] as $item) {
            $payload = [
                'service_id' => $item['service_id'],
                'link' => $item['link'],
                'quantity' => $item['quantity'] ?? 1,
                'comments' => isset($item['comments']) ? str_replace('/', PHP_EOL, $item['comments']) : '',
                'note' => $validated['note'] ?? null,
            ];
            // Giữ nguyên host, Sanctum user, headers và request context giống hệt /client/orders.
            $childRequest = clone $request;
            $childRequest->replace($payload);
            $childRequest->setUserResolver(fn () => $user);
            $response = $this->createOrder($childRequest);
            $body = method_exists($response, 'getData') ? $response->getData(true) : (array) $response;
            $results[] = [
                'line' => $item['line'],
                'service_id' => $item['service_id'],
                'link' => $item['link'],
                'success' => (bool) ($body['success'] ?? false),
                'message' => $body['message'] ?? 'Không thể xử lý đơn hàng.',
                'order_id' => data_get($body, 'data.order_id'),
                'payment' => data_get($body, 'data.payment'),
            ];
        }

        $successCount = collect($results)->where('success', true)->count();
        return response()->json([
            'status' => true,
            'message' => "Đã xử lý {$successCount}/" . count($results) . ' đơn hàng.',
            'data' => [
                'total' => count($results),
                'success' => $successCount,
                'failed' => count($results) - $successCount,
                'results' => $results,
            ],
        ]);
    }

    public function getTickets(Request $request)
    {
        $query = \App\Models\Ticket::where('user_id', $request->user()->id);
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }
        if (in_array($request->input('status'), ['pending', 'processing', 'completed'], true)) $query->where('status', $request->input('status'));
        $perPage = min(50, max(10, (int) $request->input('per_page', 15)));
        return response()->json(['status' => true, 'data' => $query->orderByDesc('updated_at')->paginate($perPage)]);
    }

    public function createTicket(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:orther,order,payment,refill,cancel'],
            'order_id' => ['nullable', 'integer'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();
        if (!empty($validated['order_id']) && !Order::where('id', $validated['order_id'])->where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.',
            ], 422);
        }

        $ticket = new \App\Models\Ticket();
        $ticket->user_id = $user->id;
        $ticket->domain = $user->domain;
        $ticket->title = $validated['title'];
        $ticket->type = $validated['type'];
        $ticket->order_id = $validated['order_id'] ?? null;
        $ticket->description = strip_tags($validated['description']);
        $ticket->status = 'pending';
        $ticket->save();

        \App\Models\TicketMessage::create([
            'ticket_id' => $ticket->id, 'user_id' => $user->id,
            'message' => strip_tags($validated['description']), 'type' => 'user', 'domain' => $user->domain,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo yêu cầu hỗ trợ thành công.',
            'data' => $ticket
        ]);
    }

    public function getTicket(Request $request, int $id)
    {
        $ticket = \App\Models\Ticket::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$ticket) return response()->json(['status' => false, 'message' => 'Không tìm thấy yêu cầu hỗ trợ.'], 404);
        $ticket->setRelation('messages', \App\Models\TicketMessage::where('ticket_id', $ticket->id)->orderBy('created_at')->get());
        return response()->json(['status' => true, 'data' => $ticket]);
    }

    public function replyTicket(Request $request, int $id)
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $ticket = \App\Models\Ticket::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$ticket) return response()->json(['status' => false, 'message' => 'Không tìm thấy yêu cầu hỗ trợ.'], 404);
        if ($ticket->status === 'completed') return response()->json(['status' => false, 'message' => 'Yêu cầu này đã đóng.'], 422);
        $message = \App\Models\TicketMessage::create([
            'ticket_id' => $ticket->id, 'user_id' => $request->user()->id,
            'message' => strip_tags($validated['message']), 'type' => 'user', 'domain' => $ticket->domain,
        ]);
        $ticket->touch();
        return response()->json(['status' => true, 'message' => 'Đã gửi phản hồi.', 'data' => $message]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user()->fresh();
        $twoFactorEnabled = !empty($user->two_factor_method) || !empty($user->two_factor_secret);
        $monthlyDeposit = $user->transactions()
            ->where('type', 'add')
            ->where('status', 'success')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        return response()->json([
            'status' => true,
            'data' => array_merge($user->toArray(), [
                'monthly_deposit' => $monthlyDeposit,
                'currency' => $user->currency ?: 'VND',
                'balance_display' => convert_currency($user->balance ?? 0, $user->currency ?: 'VND', true),
                'monthly_deposit_display' => convert_currency($monthlyDeposit, $user->currency ?: 'VND', true),
                'total_deposit_display' => convert_currency($user->total_deposit ?? 0, $user->currency ?: 'VND', true),
                'commission_display' => convert_currency($user->commission ?? 0, $user->currency ?: 'VND', true),
                'current_ip' => $request->ip(),
                'two_factor_enabled' => $twoFactorEnabled,
                'two_factor_method' => $user->two_factor_method ?: ($twoFactorEnabled ? 'google' : null),
                'telegram_linked' => !empty($user->telegram_chat_id),
            ]),
        ]);
    }

    public function getTwoFactorSetup(Request $request)
    {
        $user = $request->user();
        if ($user->two_factor_method || $user->two_factor_secret) {
            return response()->json(['status' => true, 'enabled' => true]);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $otpAuthUrl = $google2fa->getQRCodeUrl(ucfirst($request->getHost()), $user->username, $secret);
        $renderer = new ImageRenderer(new RendererStyle(240), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($otpAuthUrl);

        return response()->json([
            'status' => true,
            'enabled' => false,
            'secret' => $secret,
            'qr_code' => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ]);
    }

    public function getTelegramLink(Request $request)
    {
        $user = $request->user();
        $config = Config::where('domain', $user->domain)->first() ?? Config::first();
        $username = ltrim((string) ($config?->tele_bot_username ?? ''), '@');

        if (!$username && $config?->telegram_link) {
            $path = trim((string) parse_url($config->telegram_link, PHP_URL_PATH), '/');
            $username = explode('/', $path)[0] ?? '';
        }

        if (!$username && $config) {
            $token = $config->telegram_bot ?: $config->tele_bot_token;
            if ($token) {
                try {
                    $botInfo = Cache::remember('telegram_bot_info:' . $config->id, now()->addHours(12), function () use ($token) {
                        $response = Http::timeout(8)->get("https://api.telegram.org/bot{$token}/getMe");
                        return $response->successful() && $response->json('ok') ? $response->json('result') : null;
                    });
                } catch (\Throwable) {
                    $botInfo = null;
                }
                $username = ltrim((string) data_get($botInfo, 'username', ''), '@');
                if ($username) {
                    $config->tele_bot_username = $username;
                    $config->telegram_link = "https://t.me/{$username}";
                    $config->save();
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'linked' => !empty($user->telegram_chat_id),
                'bot_username' => $username ?: null,
                'bot_url' => $username ? "https://t.me/{$username}" : null,
                'link_command' => "/lienket {$user->api_key}",
                'unlink_command' => '/huylienket',
                'two_factor_method' => $user->two_factor_method,
                'instructions' => [
                    'Truy cập bot Telegram của hệ thống.',
                    'Gửi lệnh liên kết được cung cấp bên dưới.',
                    'Sau khi liên kết, hệ thống sẽ gửi thông báo hoạt động của bạn trên website.',
                    'Dùng lệnh hủy liên kết khi không còn nhu cầu.',
                ],
            ],
        ]);
    }

    public function regenerateApiKey(Request $request)
    {
        $user = $request->user();
        $user->api_key = bin2hex(random_bytes(16));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi API Key thành công. API Key cũ đã hết hiệu lực.',
            'data' => ['api_key' => $user->api_key],
        ]);
    }

    public function enableTwoFactor(Request $request)
    {
        $validated = $request->validate([
            'secret' => ['required', 'string'],
            'code' => ['required', 'digits:6'],
        ]);
        $user = $request->user();
        if ($user->two_factor_secret) {
            return response()->json(['status' => false, 'message' => 'Xác thực hai lớp đã được bật.'], 409);
        }
        if (!(new Google2FA())->verifyKey($validated['secret'], $validated['code'])) {
            return response()->json(['status' => false, 'message' => 'Mã xác thực không hợp lệ.'], 422);
        }
        $user->two_factor_secret = $validated['secret'];
        $user->two_factor_method = 'google';
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã bật Google Authenticator thành công.']);
    }

    public function disableTwoFactor(Request $request)
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);
        $user = $request->user();
        if (!$user->two_factor_method && !$user->two_factor_secret) {
            return response()->json(['status' => false, 'message' => 'Xác thực hai lớp chưa được bật.'], 409);
        }
        $valid = $user->two_factor_method === 'telegram'
            ? $this->verifyTelegramCode($user->id, $validated['code'], 'disable')
            : (new Google2FA())->verifyKey($user->two_factor_secret, $validated['code']);
        if (!$valid) {
            return response()->json(['status' => false, 'message' => 'Mã xác thực không hợp lệ.'], 422);
        }
        $user->two_factor_secret = null;
        $user->two_factor_method = null;
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã tắt xác thực hai lớp.']);
    }

    public function sendTelegramTwoFactorCode(Request $request)
    {
        $user = $request->user();
        if (!$user->telegram_chat_id) {
            return response()->json(['status' => false, 'message' => 'Bạn chưa liên kết tài khoản Telegram.'], 422);
        }
        $purpose = $user->two_factor_method === 'telegram' ? 'disable' : 'enable';
        if (Cache::has("telegram_2fa_cooldown:{$user->id}:{$purpose}")) {
            return response()->json(['status' => false, 'message' => 'Vui lòng chờ trước khi gửi lại mã.'], 429);
        }
        $code = (string) random_int(100000, 999999);
        Cache::put("telegram_2fa_code:{$user->id}:{$purpose}", Hash::make($code), now()->addMinutes(5));
        Cache::put("telegram_2fa_cooldown:{$user->id}:{$purpose}", true, now()->addSeconds(60));
        if (!$this->sendTelegramOtp($user, $code)) {
            return response()->json(['status' => false, 'message' => 'Không thể gửi mã qua Telegram. Kiểm tra cấu hình bot.'], 503);
        }
        return response()->json(['status' => true, 'message' => 'Mã xác thực đã được gửi qua Telegram.']);
    }

    public function enableTelegramTwoFactor(Request $request)
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);
        $user = $request->user();
        if (!$user->telegram_chat_id) return response()->json(['status' => false, 'message' => 'Bạn chưa liên kết Telegram.'], 422);
        if ($user->two_factor_method) return response()->json(['status' => false, 'message' => 'Hãy tắt phương thức 2FA hiện tại trước.'], 409);
        if (!$this->verifyTelegramCode($user->id, $validated['code'], 'enable')) {
            return response()->json(['status' => false, 'message' => 'Mã xác thực không hợp lệ hoặc đã hết hạn.'], 422);
        }
        $user->two_factor_secret = null;
        $user->two_factor_method = 'telegram';
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã bật Telegram Authenticator thành công.']);
    }

    private function verifyTelegramCode(int $userId, string $code, string $purpose): bool
    {
        $key = "telegram_2fa_code:{$userId}:{$purpose}";
        $hash = Cache::get($key);
        if (!$hash || !Hash::check($code, $hash)) return false;
        Cache::forget($key);
        return true;
    }

    private function sendTelegramOtp($user, string $code): bool
    {
        $config = Config::where('domain', $user->domain)->first() ?? Config::first();
        $token = $config?->telegram_bot ?: $config?->tele_bot_token;
        if (!$token) return false;
        return (bool) (new TelegramCustom($user->telegram_chat_id, $token))->sendMessage(
            "Mã xác thực của bạn là: <b>{$code}</b>\nMã có hiệu lực trong 5 phút. Không chia sẻ mã này."
        );
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($validated)->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin tài khoản thành công.',
            'data' => $user->fresh(),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu cũ không chính xác'
            ], 400);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
