<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChildPanel;
use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\Recharge;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class ApiAdminController extends Controller
{
    public function cronLink(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $secret = (string) config('cron.secret', '');
        $host = $request->getSchemeAndHttpHost();
        if (str_starts_with($request->getHost(), 'api.')) {
            $host = $request->getScheme() . '://' . substr($request->getHost(), 4) . ($request->getPort() && !in_array($request->getPort(), [80, 443], true) ? ':' . $request->getPort() : '');
        }
        $path = '/CronJobs/RunAll?secret=' . rawurlencode($secret);
        return response()->json([
            'status' => true,
            'data' => ['url' => $host . $path, 'path' => $path],
        ]);
    }

    private const RESOURCES = [
        'platforms' => \App\Models\Platform::class,
        'categories' => \App\Models\Category::class,
        'services' => \App\Models\Service::class,
        'discounts' => \App\Models\Discount::class,
        'providers' => \App\Models\ApiProvider::class,
        'rate-updates' => \App\Models\RateUpdate::class,
        'orders' => \App\Models\Order::class,
        'products' => \App\Models\Product::class,
        'product-categories' => \App\Models\ProductCategory::class,
        'product-orders' => \App\Models\OrderProduct::class,
        'banks' => \App\Models\AccountBank::class,
        'deposits' => \App\Models\Recharge::class,
        'bonuses' => \App\Models\RechargeBonus::class,
        'posts' => \App\Models\Post::class,
        'affiliates' => \App\Models\AffiliateRef::class,
        'tickets' => \App\Models\Ticket::class,
        'childpanels' => \App\Models\ChildPanel::class,
        'users' => \App\Models\User::class,
        'transactions' => \App\Models\Transaction::class,
        'currencies' => \App\Models\Currency::class,
        'facebook-tokens' => \App\Models\FbToken::class,
        'notifications' => \App\Models\Config::class,
        'settings' => \App\Models\Config::class,
        'telegram' => \App\Models\Config::class,
        'api-keys' => \App\Models\Config::class,
        'system' => \App\Models\Config::class,
    ];

    private const READ_ONLY = ['affiliates', 'transactions', 'product-orders', 'system'];
    private const SINGLETON = ['notifications', 'settings', 'telegram', 'api-keys'];
    private const HIDDEN_COLUMNS = [
        'password', 'remember_token', 'two_factor_secret', 'telegram_chat_id', 'api_key',
        'token', 'access_token', 'secret', 'secret_key', 'api_secret', 'response_data', 'input_data',
    ];

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Bạn không có quyền quản trị.');
    }

    private function domain(Request $request): string
    {
        // Admin Blade cũ luôn lọc theo host hiện tại, nên API admin phải dùng cùng nguồn.
        // Không ưu tiên user.domain vì tài khoản chủ có thể mang domain cũ/khác alias.
        $host = $request->getHost();
        return str_starts_with($host, 'api.') ? substr($host, 4) : $host;
    }

    private function serviceDomains(Request $request): array
    {
        $domain = $this->domain($request);
        $configured = trim((string) env('MAIN_SITE', ''));
        $configuredHost = parse_url(
            str_contains($configured, '://') ? $configured : 'https://' . ltrim($configured, '/'),
            PHP_URL_HOST
        );

        return array_values(array_unique(array_filter([
            $domain,
            'api.' . $domain,
            $configured,
            $configuredHost,
            $configuredHost && str_starts_with($configuredHost, 'api.') ? substr($configuredHost, 4) : $configuredHost,
        ])));
    }

    public function dashboard(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domain = $this->domain($request);
        $orders = Order::where('domain', $domain);
        $users = User::where('domain', $domain);
        $deposits = Recharge::where('domain', $domain)->whereRaw('LOWER(status) = ?', ['completed']);

        $statusCounts = Order::where('domain', $domain)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentOrders = Order::where('domain', $domain)
            ->with(['user:id,username', 'service:id,name'])
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'user_id', 'service_id', 'link', 'quantity', 'total', 'status', 'created_at']);

        $revenueStatuses = ['Completed', 'Processing', 'In Progress', 'In progress', 'Partial', 'Active'];
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $applyPeriod = function ($query, string $period) use ($customStart, $customEnd) {
            if ($period === 'month') $query->whereMonth('created_at', now()->month);
            if ($period === 'week') $query->whereDate('created_at', '>=', now()->startOfWeek());
            if ($period === 'today') $query->whereDate('created_at', now());
            if ($period === 'yesterday') $query->whereDate('created_at', now()->subDay());
            if ($period === 'year') $query->whereYear('created_at', now()->year);
            if ($period === 'custom' && is_string($customStart) && is_string($customEnd)
                && preg_match('/^\d{4}-\d{2}-\d{2}$/', $customStart)
                && preg_match('/^\d{4}-\d{2}-\d{2}$/', $customEnd)) {
                $query->whereBetween('created_at', [$customStart . ' 00:00:00', $customEnd . ' 23:59:59']);
            }
            return $query;
        };
        $profitFor = function (string $period) use ($domain, $applyPeriod): float {
            // Giữ nguyên công thức dashboard Laravel: chỉ đơn Completed mới tính lợi nhuận.
            $query = Order::where('domain', $domain)->where('status', 'Completed')->with('service:id,rate_original');
            $applyPeriod($query, $period);
            return (float) $query->get()->sum(function (Order $order) {
                $cost = ((float) ($order->service?->rate_original ?? 0) * (int) $order->quantity) / 1000;
                return (float) $order->total - $cost;
            });
        };
        $periodStats = [];
        foreach (['all', 'custom', 'year', 'month', 'week', 'yesterday', 'today'] as $key) {
            $userQuery = User::where('domain', $domain);
            $orderQuery = Order::where('domain', $domain);
            $depositQuery = Recharge::where('domain', $domain)->whereRaw('LOWER(status) = ?', ['completed']);
            $pendingOrderQuery = Order::where('domain', $domain)->whereIn('status', ['Pending', 'Processing', 'In Progress', 'In progress']);
            $applyPeriod($userQuery, $key); $applyPeriod($orderQuery, $key); $applyPeriod($depositQuery, $key); $applyPeriod($pendingOrderQuery, $key);
            $periodStats[$key] = [
                'users' => $userQuery->count(),
                'orders' => $orderQuery->count(),
                'revenue' => (float) $orderQuery->sum('total'),
                'profit' => $profitFor($key),
                'deposits' => (float) $depositQuery->sum('real_amount'),
                'pending_orders' => $pendingOrderQuery->count(),
            ];
        }
        $daily = [];
        for ($offset = 29; $offset >= 0; $offset--) {
            $date = now()->subDays($offset);
            $dayOrders = Order::where('domain', $domain)->whereDate('created_at', $date)->with('service:id,rate_original')->get();
            $valid = $dayOrders->whereIn('status', $revenueStatuses);
            $daily[] = [
                'date' => $date->format('d/m'),
                'revenue' => (float) $valid->sum('total'),
                'profit' => (float) $valid->sum(fn (Order $order) => (float) $order->total - (((float) ($order->service?->rate_original ?? $order->rate) * (int) $order->quantity) / 1000)),
                'orders' => $dayOrders->count(),
                'new_users' => User::where('domain', $domain)->whereDate('created_at', $date)->count(),
            ];
        }
        $paymentMethods = Recharge::where('domain', $domain)->whereRaw('LOWER(status) = ?', ['completed'])
            ->selectRaw('method, SUM(real_amount) as amount')->groupBy('method')->orderByDesc('amount')->get();
        $topServices = Order::where('orders.domain', $domain)->whereIn('orders.status', $revenueStatuses)
            ->leftJoin('services', 'services.id', '=', 'orders.service_id')
            ->selectRaw('orders.service_id, COALESCE(services.name, ?) as name, SUM(orders.total) as revenue, COUNT(*) as orders_count', ['Unknown'])
            ->groupBy('orders.service_id', 'services.name')->orderByDesc('revenue')->limit(5)->get();
        $topTotal = max(1, (float) $topServices->sum('revenue'));
        $topServices->each(fn ($item) => $item->percent = round(((float) $item->revenue / $topTotal) * 100, 1));
        $recentDeposits = Recharge::where('domain', $domain)->with('user:id,username')->orderByDesc('id')->limit(8)->get();
        $leaderboard = Order::where('orders.domain', $domain)->whereDate('orders.created_at', today())
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->selectRaw('orders.user_id, users.username, users.email, SUM(orders.total) as spending, COUNT(*) as orders_count')
            ->groupBy('orders.user_id', 'users.username', 'users.email')->orderByDesc('spending')->limit(50)->get();
        $providers = Order::where('orders.domain', $domain)
            ->leftJoin('api_providers', 'api_providers.id', '=', 'orders.provider')
            ->leftJoin('services', 'services.id', '=', 'orders.service_id')
            ->selectRaw('orders.provider as provider_id, COALESCE(api_providers.name, ?) as name, SUM(orders.total) as revenue, SUM((COALESCE(services.rate_original, orders.rate) * orders.quantity) / 1000) as cost, COUNT(*) as orders_count', ['Tools'])
            ->groupBy('orders.provider', 'api_providers.name')->orderByDesc('revenue')->limit(50)->get();
        $providers->each(function ($provider) { $provider->profit = (float) $provider->revenue - (float) $provider->cost; $provider->margin = (float) $provider->revenue > 0 ? round($provider->profit / (float) $provider->revenue * 100, 1) : 0; });

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => [
                    'users' => (clone $users)->count(),
                    'users_today' => (clone $users)->whereDate('created_at', today())->count(),
                    'balance' => (float) (clone $users)->sum('balance'),
                    'orders' => (clone $orders)->count(),
                    'orders_today' => (clone $orders)->whereDate('created_at', today())->count(),
                    'orders_pending' => (clone $orders)->whereIn('status', ['Pending', 'Processing', 'In Progress', 'In progress'])->count(),
                    'order_payments' => (float) (clone $orders)->sum('total'),
                    'revenue' => (float) (clone $orders)->sum('total'),
                    'revenue_today' => (float) (clone $orders)->whereDate('created_at', today())->sum('total'),
                    'deposits' => (float) (clone $deposits)->sum('real_amount'),
                    'deposits_today' => (float) (clone $deposits)->whereDate('created_at', today())->sum('real_amount'),
                    'deposits_month' => (float) (clone $deposits)->whereMonth('created_at', now()->month)->sum('real_amount'),
                    'tickets_pending' => Ticket::where('domain', $domain)->where('status', 'Pending')->count(),
                    'websites_pending' => ChildPanel::where('domain', $domain)->where('status', 'Pending')->count(),
                ],
                'order_statuses' => $statusCounts,
                'recent_orders' => $recentOrders,
                'recent_deposits' => $recentDeposits,
                'periods' => $periodStats,
                'daily' => $daily,
                'payment_methods' => $paymentMethods,
                'top_services' => $topServices,
                'leaderboard' => $leaderboard,
                'providers' => $providers,
            ],
        ]);
    }

    private function resourceModel(string $resource): Model
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404, 'Module quản trị không tồn tại.');
        $class = self::RESOURCES[$resource];
        return new $class();
    }

    private function resourceColumns(Model $model): array
    {
        return array_values(array_filter(
            Schema::getColumnListing($model->getTable()),
            fn (string $column) => !in_array($column, self::HIDDEN_COLUMNS, true)
        ));
    }

    public function resourceIndex(Request $request, string $resource): JsonResponse
    {
        $this->authorizeAdmin($request);
        $model = $this->resourceModel($resource);
        $columns = $this->resourceColumns($model);
        $query = $model->newQuery();
        if (in_array('domain', $columns, true)) $query->where('domain', $this->domain($request));
        if ($resource === 'rate-updates') $query->with('service:id,name');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $searchable = array_values(array_intersect($columns, ['id', 'name', 'title', 'code', 'username', 'email', 'link', 'slug', 'status', 'transaction_id', 'domain']));
            $query->where(function ($builder) use ($searchable, $search) {
                foreach ($searchable as $index => $column) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $builder->{$method}($column, 'like', "%{$search}%");
                }
            });
        }

        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $data = ($resource === 'platforms' ? $query->orderBy('sort_order')->orderBy($model->getKeyName()) : $query->orderByDesc($model->getKeyName()))
            ->paginate($perPage, $columns);
        if ($resource === 'rate-updates') {
            $currency = (string) ($request->user()->currency ?: 'VND');
            $data->getCollection()->transform(function ($item) use ($currency) {
                $item->rate_old_format = convert_currency($item->rate_old, $currency, false);
                $item->rate_new_format = convert_currency($item->rate_new, $currency, false);
                return $item;
            });
        }
        $editable = array_values(array_diff(array_intersect($columns, $model->getFillable()), ['id', 'domain', 'created_at', 'updated_at']));

        return response()->json(['status' => true, 'data' => $data, 'meta' => [
            'resource' => $resource,
            'columns' => $columns,
            'editable' => $editable,
            'read_only' => in_array($resource, self::READ_ONLY, true),
            'can_create' => !in_array($resource, array_merge(self::READ_ONLY, self::SINGLETON), true),
            'can_delete' => !in_array($resource, array_merge(self::READ_ONLY, self::SINGLETON), true),
        ]]);
    }

    public function resourceStore(Request $request, string $resource): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if(in_array($resource, self::READ_ONLY, true), 405, 'Module này chỉ cho phép xem.');
        abort_if(in_array($resource, self::SINGLETON, true), 405, 'Cấu hình hệ thống chỉ cho phép cập nhật.');
        $model = $this->resourceModel($resource);
        $data = $request->validate(['data' => ['required', 'array']])['data'];
        $data = array_intersect_key($data, array_flip($model->getFillable()));
        if (Schema::hasColumn($model->getTable(), 'domain')) $data['domain'] = $this->domain($request);
        if ($resource === 'posts' && Schema::hasColumn($model->getTable(), 'user_id')) $data['user_id'] = $request->user()->id;
        if ($resource === 'users' && isset($data['password'])) $data['password'] = Hash::make((string) $data['password']);

        try {
            $item = $model->newQuery()->create($data);
            return response()->json(['status' => true, 'message' => 'Đã tạo dữ liệu.', 'data' => $item], 201);
        } catch (Throwable $error) {
            report($error);
            return response()->json(['status' => false, 'message' => 'Dữ liệu chưa hợp lệ hoặc còn thiếu trường bắt buộc.'], 422);
        }
    }

    public function resourceUpdate(Request $request, string $resource, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if(in_array($resource, self::READ_ONLY, true), 405, 'Module này chỉ cho phép xem.');
        $model = $this->resourceModel($resource);
        $query = $model->newQuery();
        if (Schema::hasColumn($model->getTable(), 'domain')) $query->where('domain', $this->domain($request));
        $item = $query->findOrFail($id);
        $data = $request->validate(['data' => ['required', 'array']])['data'];
        $data = array_intersect_key($data, array_flip($model->getFillable()));
        unset($data['domain'], $data['user_id']);
        if ($resource === 'users' && isset($data['password'])) $data['password'] = Hash::make((string) $data['password']);
        $item->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật dữ liệu.', 'data' => $item->fresh()]);
    }

    public function updateDemoMode(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();
        $config->status_demo = $data['enabled'] ? 1 : 0;
        $config->save();

        return response()->json([
            'status' => true,
            'message' => $data['enabled'] ? 'Đã bật chế độ Demo cho toàn hệ thống.' : 'Đã tắt chế độ Demo.',
            'data' => ['status_demo' => (string) $config->status_demo],
        ]);
    }

    public function uploadSettingImage(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'field' => ['required', 'in:logo,favicon,og_image,twitter_image'],
            'image' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,webp,svg,ico', 'max:4096'],
        ]);
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();
        $directory = public_path('uploads');
        if (!is_dir($directory)) mkdir($directory, 0777, true);

        $file = $request->file('image');
        $filename = now()->format('YmdHis') . '-' . Str::random(10) . '.' . $file->extension();
        $file->move($directory, $filename);
        $path = '/uploads/' . $filename;

        $field = $data['field'];
        $oldPath = (string) $config->getRawOriginal($field);
        $config->{$field} = $path;
        $config->save();

        if (str_starts_with($oldPath, '/uploads/')) {
            $oldFile = public_path('uploads/' . basename($oldPath));
            if (is_file($oldFile) && $oldFile !== $directory . DIRECTORY_SEPARATOR . $filename) @unlink($oldFile);
        }

        return response()->json(['status' => true, 'message' => 'Đã tải ảnh lên.', 'data' => ['field' => $field, 'path' => $path]]);
    }

    public function resourceDestroy(Request $request, string $resource, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if(in_array($resource, self::READ_ONLY, true), 405, 'Module này chỉ cho phép xem.');
        abort_if(in_array($resource, self::SINGLETON, true), 405, 'Không thể xóa cấu hình hệ thống.');
        $model = $this->resourceModel($resource);
        $query = $model->newQuery();
        if (Schema::hasColumn($model->getTable(), 'domain')) $query->where('domain', $this->domain($request));
        $item = $query->findOrFail($id);
        $item->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa dữ liệu.']);
    }

    public function productCategories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\ProductCategory::where('domain', $this->domain($request))->withCount('products');
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        $data = $query->orderBy('sort_order')->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function storeProductCategory(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request); $data = $this->validateProductCategory($request);
        if ($request->hasFile('image')) $data['image'] = $this->storeProductImage($request);
        $data['slug'] = Str::slug($data['name'] . '-' . time()); $data['domain'] = $this->domain($request);
        $item = \App\Models\ProductCategory::create($data);
        return response()->json(['status' => true, 'message' => 'Đã tạo danh mục sản phẩm.', 'data' => $item], 201);
    }

    public function updateProductCategory(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request); $item = \App\Models\ProductCategory::where('domain', $this->domain($request))->findOrFail($id); $data = $this->validateProductCategory($request);
        if ($request->hasFile('image')) { $this->deleteProductCategoryImage($item); $data['image'] = $this->storeProductImage($request); }
        $data['slug'] = Str::slug($data['name'] . '-' . time()); $item->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật danh mục sản phẩm.', 'data' => $item->fresh()]);
    }

    public function destroyProductCategory(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request); $item = \App\Models\ProductCategory::where('domain', $this->domain($request))->findOrFail($id); $this->deleteProductCategoryImage($item); $item->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa danh mục sản phẩm.']);
    }

    public function destroyProductCategories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request); $ids = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']])['ids'];
        $items = \App\Models\ProductCategory::where('domain', $this->domain($request))->whereIn('id', $ids)->get();
        foreach ($items as $item) { $this->deleteProductCategoryImage($item); $item->delete(); }
        return response()->json(['status' => true, 'message' => 'Đã xóa ' . $items->count() . ' danh mục sản phẩm.']);
    }

    public function productOrders(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\OrderProduct::where('domain', $this->domain($request))->with(['user:id,username,email', 'product:id,name,image']);
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') $query->where(function ($builder) use ($search) { $builder->where('id', 'like', "%{$search}%")->orWhere('coupon', 'like', "%{$search}%")->orWhereHas('user', fn ($user) => $user->where('username', 'like', "%{$search}%"))->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$search}%")); });
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('product_id')) $query->where('product_id', $request->integer('product_id'));
        $currency = (string) ($request->user()->currency ?: 'VND'); $data = $query->latest('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $data->getCollection()->transform(function ($item) use ($currency) { $item->price_format = convert_currency($item->price, $currency, false); $item->total_format = convert_currency($item->total, $currency, false); return $item; });
        $products = \App\Models\Product::where('domain', $this->domain($request))->orderBy('name')->get(['id', 'name']);
        return response()->json(['status' => true, 'data' => $data, 'products' => $products]);
    }

    private function validateProductCategory(Request $request): array
    {
        return $request->validate(['name' => ['required', 'string'], 'sort_order' => ['required', 'integer'], 'description' => ['nullable', 'string'], 'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], 'status' => ['required', 'in:active,inactive']]);
    }

    private function deleteProductCategoryImage(\App\Models\ProductCategory $item): void
    {
        $filename = basename((string) $item->getRawOriginal('image')); if ($filename === '') return; $path = public_path('uploads/images/' . $filename); if (is_file($path)) @unlink($path);
    }

    public function products(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\Product::query()->where('domain', $this->domain($request))->with('category:id,name')
            ->withCount(['stocks', 'stocks as available_stock_count' => fn ($stock) => $stock->where('status', 'available'), 'stocks as sold_stock_count' => fn ($stock) => $stock->where('status', 'sold')]);
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        if ($request->filled('category_id')) $query->where('category_id', $request->integer('category_id'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $currency = (string) ($request->user()->currency ?: 'VND');
        $data = $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage);
        $data->getCollection()->transform(function ($item) use ($currency) { $item->price_format = convert_currency($item->price, $currency, false); return $item; });
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function productCategoryOptions(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $items = \App\Models\ProductCategory::where('domain', $this->domain($request))->where('status', 'active')->orderBy('sort_order')->get(['id', 'name']);
        return response()->json(['status' => true, 'data' => $items]);
    }

    public function storeProduct(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $this->validateProduct($request);
        $this->ensureProductCategory($request, (int) $data['category_id']);
        if ($request->hasFile('image')) $data['image'] = $this->storeProductImage($request);
        $data['slug'] = Str::slug($data['name'] . '-' . time()); $data['domain'] = $this->domain($request);
        $data['description'] = $this->sanitizeProductHtml($data['description'] ?? '');
        $data['short_description'] = $this->sanitizeProductHtml($data['short_description'] ?? '', true);
        $product = \App\Models\Product::create($data);
        return response()->json(['status' => true, 'message' => 'Đã thêm sản phẩm.', 'data' => $product], 201);
    }

    public function updateProduct(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $product = \App\Models\Product::where('domain', $this->domain($request))->findOrFail($id);
        $data = $this->validateProduct($request);
        $this->ensureProductCategory($request, (int) $data['category_id']);
        if ($request->hasFile('image')) { $this->deleteProductImage($product); $data['image'] = $this->storeProductImage($request); }
        $data['slug'] = Str::slug($data['name'] . '-' . time());
        $data['description'] = $this->sanitizeProductHtml($data['description'] ?? '');
        $data['short_description'] = $this->sanitizeProductHtml($data['short_description'] ?? '', true);
        $product->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật sản phẩm.', 'data' => $product->fresh()]);
    }

    public function destroyProduct(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $product = \App\Models\Product::where('domain', $this->domain($request))->findOrFail($id);
        $this->deleteProductImage($product); $product->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa sản phẩm.']);
    }

    public function productStocks(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $product = \App\Models\Product::where('domain', $this->domain($request))->findOrFail($id);
        $stats = ['available' => $product->stocks()->where('status', 'available')->count(), 'sold' => $product->stocks()->where('status', 'sold')->count()];
        $query = $product->stocks()->with('buyer:id,username');
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        return response()->json(['status' => true, 'data' => $query->latest('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20)))), 'stats' => $stats]);
    }

    public function storeProductStocks(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $product = \App\Models\Product::where('domain', $this->domain($request))->findOrFail($id);
        $data = $request->validate(['data' => ['required', 'string', 'max:1000000']])['data'];
        $lines = array_values(array_unique(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data) ?: [])))); $count = 0;
        foreach ($lines as $line) if (!$product->stocks()->where('data', $line)->exists()) { $product->stocks()->create(['data' => $line, 'status' => 'available', 'domain' => $this->domain($request)]); $count++; }
        return response()->json(['status' => true, 'message' => "Đã thêm {$count} dữ liệu kho.", 'count' => $count], 201);
    }

    public function destroyProductStocks(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $product = \App\Models\Product::where('domain', $this->domain($request))->findOrFail($id);
        $ids = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']])['ids'];
        $count = $product->stocks()->whereIn('id', $ids)->delete();
        return response()->json(['status' => true, 'message' => "Đã xóa {$count} dữ liệu kho."]);
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate(['category_id' => ['required', 'integer'], 'sort_order' => ['required', 'integer'], 'name' => ['required', 'string'], 'price' => ['required', 'numeric', 'min:0'], 'status' => ['required', 'in:active,inactive'], 'description' => ['nullable', 'string'], 'short_description' => ['nullable', 'string'], 'data_input' => ['nullable', 'string', 'max:255'], 'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']]);
    }

    private function ensureProductCategory(Request $request, int $id): void
    {
        abort_unless(\App\Models\ProductCategory::where('domain', $this->domain($request))->whereKey($id)->exists(), 422, 'Danh mục không tồn tại.');
    }

    private function storeProductImage(Request $request): string
    {
        $file = $request->file('image'); $filename = time() . '-' . Str::random(8) . '.' . $file->extension(); $file->move(public_path('uploads/images'), $filename); return $filename;
    }

    private function deleteProductImage(\App\Models\Product $product): void
    {
        $filename = basename((string) $product->getRawOriginal('image')); if ($filename === '') return; $path = public_path('uploads/images/' . $filename); if (is_file($path)) @unlink($path);
    }

    private function sanitizeProductHtml(string $content, bool $short = false): string
    {
        $allowed = $short ? '<a><b><i><u><strong><em><p><br><ul><li><ol><img>' : '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>';
        return strip_tags($content, $allowed);
    }

    public function notificationConfig(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $config = \App\Models\Config::where('domain', $this->domain($request))->first() ?? \App\Models\Config::firstOrFail();
        return response()->json(['status' => true, 'data' => $config->only(['notice_modal', 'notice_service', 'notice_recharge'])]);
    }

    public function updateNotificationConfig(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'notice_modal' => ['sometimes', 'nullable', 'string', 'max:100000'],
            'notice_service' => ['sometimes', 'nullable', 'string', 'max:100000'],
            'notice_recharge' => ['sometimes', 'nullable', 'string', 'max:100000'],
        ]);
        abort_if($data === [], 422, 'Không có nội dung thông báo để cập nhật.');
        $allowed = '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td><blockquote><hr>';
        foreach ($data as $key => $value) $data[$key] = $value === null ? null : strip_tags($value, $allowed);
        $config = \App\Models\Config::where('domain', $this->domain($request))->first() ?? \App\Models\Config::firstOrFail();
        $config->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật thông báo.', 'data' => $config->only(array_keys($data))]);
    }

    public function telegramConfig(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $config = \App\Models\Config::where('domain', $this->domain($request))->first() ?? \App\Models\Config::firstOrFail();
        $defaultWebhookUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/api/telegram/webhook';
        return response()->json(['status' => true, 'data' => [
            'tele_bot_username' => $config->tele_bot_username,
            'telegram_link' => $config->telegram_link,
            'telegram_status' => $config->telegram_status ?: 'inactive',
            'telegram_webhook_url' => $config->telegram_webhook_url ?: $defaultWebhookUrl,
            'bot_token_configured' => (bool) ($config->telegram_bot ?: $config->tele_bot_token),
            'default_webhook_url' => $defaultWebhookUrl,
        ]]);
    }

    public function updateTelegramConfig(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'tele_bot_username' => ['nullable', 'string', 'max:100'],
            'telegram_link' => ['nullable', 'url', 'max:255'],
            'telegram_bot' => ['nullable', 'string', 'max:255'],
            'telegram_status' => ['required', 'in:active,inactive'],
            'telegram_webhook_url' => ['nullable', 'url', 'max:500'],
        ]);
        $config = \App\Models\Config::where('domain', $this->domain($request))->first() ?? \App\Models\Config::firstOrFail();
        $validated['tele_bot_username'] = ltrim((string) ($validated['tele_bot_username'] ?? ''), '@');
        if (empty($validated['telegram_bot'])) unset($validated['telegram_bot']);
        abort_if(!isset($validated['telegram_bot']) && !($config->telegram_bot ?: $config->tele_bot_token), 422, 'Vui lòng nhập Bot Token.');
        if (empty($validated['telegram_link']) && $validated['tele_bot_username']) $validated['telegram_link'] = 'https://t.me/' . $validated['tele_bot_username'];
        $config->fill($validated)->save();
        return response()->json(['status' => true, 'message' => 'Đã lưu cấu hình Telegram Link.', 'data' => [
            'tele_bot_username' => $config->tele_bot_username, 'telegram_link' => $config->telegram_link,
            'telegram_status' => $config->telegram_status, 'telegram_webhook_url' => $config->telegram_webhook_url,
            'bot_token_configured' => (bool) ($config->telegram_bot ?: $config->tele_bot_token),
        ]]);
    }

    public function registerTelegramWebhook(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['telegram_webhook_url' => ['required', 'url', 'max:500']]);
        $config = \App\Models\Config::where('domain', $this->domain($request))->first() ?? \App\Models\Config::firstOrFail();
        $token = $config->telegram_bot ?: $config->tele_bot_token;
        abort_if(!$token, 422, 'Thiếu Bot Token.');
        try {
            $botInfo = \Illuminate\Support\Facades\Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getMe")->json();
            abort_if(!data_get($botInfo, 'ok'), 422, data_get($botInfo, 'description', 'Bot Token không hợp lệ.'));
            $result = \Illuminate\Support\Facades\Http::timeout(15)->post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $data['telegram_webhook_url']])->json();
            abort_if(!data_get($result, 'ok'), 422, data_get($result, 'description', 'Không thể đăng ký webhook.'));
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $error) {
            throw $error;
        } catch (Throwable $error) {
            report($error);
            return response()->json(['status' => false, 'message' => 'Không thể kết nối Telegram API.'], 503);
        }
        $username = data_get($botInfo, 'result.username');
        $config->fill(['tele_bot_username' => $username, 'telegram_link' => $username ? 'https://t.me/' . $username : $config->telegram_link, 'telegram_webhook_url' => $data['telegram_webhook_url'], 'telegram_status' => 'active'])->save();
        return response()->json(['status' => true, 'message' => 'Đăng ký webhook Telegram thành công.', 'data' => ['username' => $username, 'webhook_url' => $data['telegram_webhook_url']]]);
    }

    public function facebookTokens(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $base = \App\Models\FbToken::query()->where('domain', $this->domain($request));
        $stats = ['all' => (clone $base)->count()];
        foreach (['live', 'processing', 'block', 'die'] as $status) $stats[$status] = (clone $base)->where('status', $status)->count();
        $query = clone $base;
        if ($request->filled('status') && $request->input('status') !== 'all') $query->where('status', $request->string('status'));
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('uid', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }
        $perPage = min(100, max(10, (int) $request->input('per_page', 50)));
        return response()->json(['status' => true, 'data' => $query->latest('id')->paginate($perPage), 'stats' => $stats]);
    }

    public function storeFacebookTokens(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['list' => ['required', 'string'], 'type' => ['required', 'in:cookie_token,cookie,token'], 'note' => ['nullable', 'string', 'max:255']]);
        $count = 0;
        foreach (preg_split('/\r\n|\r|\n/', $data['list']) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') continue;
            preg_match('/c_user=(\d+)/', $line, $matches);
            $parts = explode('|', $line);
            $uid = $matches[1] ?? (isset($parts[0]) && ctype_digit(trim($parts[0])) ? trim($parts[0]) : null);
            \App\Models\FbToken::create(['type' => $data['type'], 'data' => $line, 'uid' => $uid, 'note' => $data['note'] ?? null, 'status' => 'live', 'domain' => $this->domain($request)]);
            $count++;
        }
        return response()->json(['status' => true, 'message' => "Đã thêm thành công {$count} tài nguyên.", 'count' => $count], 201);
    }

    public function updateFacebookTokenStatus(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['status' => ['required', 'in:live,processing,block,die']]);
        $token = \App\Models\FbToken::where('domain', $this->domain($request))->findOrFail($id);
        $token->update($data);
        return response()->json(['status' => true, 'message' => 'Đã cập nhật trạng thái.', 'data' => $token]);
    }

    public function refreshFacebookToken(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $token = \App\Models\FbToken::where('domain', $this->domain($request))->findOrFail($id);
        $isLive = (new \App\Http\Controllers\Cron\System\FbTokenCronController())->checkToken($token);
        $token->refresh();
        return response()->json(['status' => true, 'is_live' => $isLive, 'data' => $token, 'message' => $isLive ? 'Tài nguyên đang Live!' : 'Tài nguyên đã Die!']);
    }

    public function destroyFacebookToken(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        \App\Models\FbToken::where('domain', $this->domain($request))->findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa tài nguyên.']);
    }

    public function destroyDeadFacebookTokens(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $count = \App\Models\FbToken::where('domain', $this->domain($request))->where('status', 'die')->delete();
        return response()->json(['status' => true, 'message' => "Đã xóa {$count} tài nguyên Die."]);
    }

    public function destroyAllFacebookTokens(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $count = \App\Models\FbToken::where('domain', $this->domain($request))->delete();
        return response()->json(['status' => true, 'message' => "Đã xóa toàn bộ {$count} tài nguyên."]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = Transaction::query()->where('domain', $this->domain($request))->with('user:id,username');
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('username', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('type')) $query->where('type', $request->string('type'));
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->date('start_date'));
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->date('end_date'));
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        $currency = (string) ($request->user()->currency ?: 'VND');
        $data = $query->latest('id')->paginate($perPage);
        $data->getCollection()->transform(function (Transaction $item) use ($currency) {
            $item->amount_format = convert_currency($item->amount, $currency, false);
            $item->balance_before_format = convert_currency($item->balance_before, $currency, false);
            $item->balance_after_format = convert_currency($item->balance_after, $currency, false);
            return $item;
        });
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function posts(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\Post::query()->where('domain', $this->domain($request))->with('user:id,username');
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        $perPage = min(100, max(10, (int) $request->input('per_page', 20)));
        return response()->json(['status' => true, 'data' => $query->latest('id')->paginate($perPage)]);
    }

    public function storePost(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'title' => ['required', 'string'], 'content' => ['required', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $file = $request->file('image');
        $filename = time() . '-' . Str::random(8) . '.' . $file->extension();
        $file->move(public_path('uploads/images'), $filename);
        $post = \App\Models\Post::create([
            'title' => $data['title'], 'slug' => Str::slug($data['title'] . '-' . time()),
            'content' => $this->sanitizePostContent($data['content']), 'image' => $filename,
            'status' => $data['status'], 'user_id' => $request->user()->id, 'domain' => $this->domain($request),
        ]);
        return response()->json(['status' => true, 'message' => 'Đã thêm bài viết.', 'data' => $post], 201);
    }

    public function updatePost(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'title' => ['required', 'string'], 'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $post = \App\Models\Post::where('domain', $this->domain($request))->findOrFail($id);
        if ($request->hasFile('image')) {
            $this->deletePostImage($post);
            $file = $request->file('image');
            $filename = time() . '-' . Str::random(8) . '.' . $file->extension();
            $file->move(public_path('uploads/images'), $filename);
            $data['image'] = $filename;
        }
        $values = [
            'title' => $data['title'], 'slug' => Str::slug($data['title'] . '-' . time()),
            'content' => $this->sanitizePostContent($data['content']), 'status' => $data['status'],
        ];
        if (isset($data['image'])) $values['image'] = $data['image'];
        $post->fill($values)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật bài viết.', 'data' => $post->fresh()->load('user:id,username')]);
    }

    public function destroyPost(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $post = \App\Models\Post::where('domain', $this->domain($request))->findOrFail($id);
        $this->deletePostImage($post);
        $post->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa bài viết.']);
    }

    private function sanitizePostContent(string $content): string
    {
        return strip_tags($content, '<a><b><i><u><strong><em><p><br><ul><li><ol><img><h1><h2><h3><h4><h5><h6><div><span><table><thead><tbody><tr><th><td>');
    }

    private function deletePostImage(\App\Models\Post $post): void
    {
        $filename = basename((string) $post->getRawOriginal('image'));
        if ($filename === '') return;
        $path = public_path('uploads/images/' . $filename);
        if (is_file($path)) @unlink($path);
    }

    public function destroyRateUpdates(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $deleted = \App\Models\RateUpdate::where('domain', $this->domain($request))->delete();
        return response()->json([
            'status' => true,
            'message' => "Đã xóa {$deleted} lịch sử cập nhật giá.",
        ]);
    }

    public function storeBank(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'icon' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'bank_name' => ['required', 'string', 'max:100'], 'bank_code' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:100'], 'account_name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'], 'status' => ['required', 'in:active,inactive'],
        ]);
        $file = $request->file('icon');
        $filename = time() . '-' . Str::random(8) . '.' . $file->extension();
        $file->move(public_path('uploads'), $filename);
        $bank = \App\Models\AccountBank::create([...$data, 'icon' => $filename, 'domain' => $this->domain($request)]);
        return response()->json(['status' => true, 'message' => 'Đã thêm tài khoản ngân hàng.', 'data' => $bank], 201);
    }

    public function updateBank(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'bank_name' => ['required', 'string', 'max:100'], 'bank_code' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:100'], 'account_name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'], 'status' => ['required', 'in:active,inactive'],
        ]);
        $bank = \App\Models\AccountBank::where('domain', $this->domain($request))->findOrFail($id);
        if ($request->hasFile('icon')) {
            $oldFile = public_path('uploads/' . basename((string) $bank->getRawOriginal('icon')));
            if (is_file($oldFile)) @unlink($oldFile);
            $file = $request->file('icon');
            $filename = time() . '-' . Str::random(8) . '.' . $file->extension();
            $file->move(public_path('uploads'), $filename);
            $data['icon'] = $filename;
        }
        $bank->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật tài khoản ngân hàng.', 'data' => $bank->fresh()]);
    }

    public function destroyBank(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $bank = \App\Models\AccountBank::where('domain', $this->domain($request))->findOrFail($id);
        $oldFile = public_path('uploads/' . basename((string) $bank->getRawOriginal('icon')));
        if (is_file($oldFile)) @unlink($oldFile);
        $bank->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa tài khoản ngân hàng.']);
    }

    public function storePlatform(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['icon' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:2048'], 'icon_preset' => ['nullable','string','in:global,facebook,instagram,tiktok,youtube,telegram,discord,google,shop,app'], 'icon_url' => ['nullable','url:http,https','max:2048'], 'clear_icon' => ['nullable','boolean'], 'sort_order' => ['required','integer'], 'name' => ['required','string','max:255'], 'status' => ['required','in:active,inactive']]);
        if ($request->boolean('clear_icon')) {
            $filename = null;
        } elseif ($request->filled('icon_url')) {
            $filename = $data['icon_url'];
        } elseif ($request->filled('icon_preset')) {
            $filename = 'preset:'.$data['icon_preset'];
        } else {
            $file = $request->file('icon');
            abort_unless($file, 422, 'Vui lòng chọn ảnh, nhập URL hoặc chọn không dùng icon.');
            $filename = time().'-'.Str::random(10).'.'.$file->extension();
            if (!is_dir(public_path('uploads/platforms'))) mkdir(public_path('uploads/platforms'), 0777, true);
            $file->move(public_path('uploads/platforms'), $filename);
        }
        $platform = \App\Models\Platform::create(['icon'=>$filename,'sort_order'=>$data['sort_order'],'code'=>Str::random(10),'name'=>$data['name'],'status'=>$data['status']]);
        return response()->json(['status'=>true,'message'=>'Đã thêm nền tảng.','data'=>$platform], 201);
    }

    public function updatePlatform(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['icon' => ['nullable','image','mimes:jpeg,png,jpg,gif,webp','max:2048'], 'icon_preset' => ['nullable','string','in:global,facebook,instagram,tiktok,youtube,telegram,discord,google,shop,app'], 'icon_url' => ['nullable','url:http,https','max:2048'], 'clear_icon' => ['nullable','boolean'], 'sort_order' => ['required','integer'], 'name' => ['required','string','max:255'], 'status' => ['required','in:active,inactive']]);
        $platform = \App\Models\Platform::findOrFail($id);
        if ($request->boolean('clear_icon') || $request->filled('icon_url')) {
            $oldFile = public_path('uploads/platforms/'.basename((string) $platform->getRawOriginal('icon')));
            if (!str_starts_with((string) $platform->getRawOriginal('icon'), 'preset:') && !filter_var($platform->getRawOriginal('icon'), FILTER_VALIDATE_URL) && is_file($oldFile)) @unlink($oldFile);
            $platform->icon = $request->boolean('clear_icon') ? null : $data['icon_url'];
        } elseif ($request->filled('icon_preset')) {
            $oldFile = public_path('uploads/platforms/'.basename((string) $platform->getRawOriginal('icon')));
            if (!str_starts_with((string) $platform->getRawOriginal('icon'), 'preset:') && is_file($oldFile)) @unlink($oldFile);
            $platform->icon = 'preset:'.$data['icon_preset'];
        } elseif ($request->hasFile('icon')) {
            $oldFile = public_path('uploads/platforms/'.basename((string) $platform->getRawOriginal('icon')));
            if (is_file($oldFile)) @unlink($oldFile);
            $file = $request->file('icon');
            $filename = time().'-'.Str::random(10).'.'.$file->extension();
            if (!is_dir(public_path('uploads/platforms'))) mkdir(public_path('uploads/platforms'), 0777, true);
            $file->move(public_path('uploads/platforms'), $filename);
            $platform->icon = $filename;
        }
        $platform->sort_order=$data['sort_order']; $platform->name=$data['name']; $platform->status=$data['status']; $platform->save();
        return response()->json(['status'=>true,'message'=>'Đã cập nhật nền tảng.','data'=>$platform->fresh()]);
    }

    public function reorderPlatforms(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['ids' => ['required','array','min:1'], 'ids.*' => ['required','integer','exists:platforms,id']]);
        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            foreach ($data['ids'] as $sortOrder => $id) {
                \App\Models\Platform::whereKey($id)->update(['sort_order' => $sortOrder]);
            }
        });
        return response()->json(['status'=>true,'message'=>'Đã cập nhật thứ tự nền tảng.']);
    }

    public function categories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\Category::with(['platform:id,name,icon','services:id,category_id,name,status']);
        if ($request->filled('search')) $query->where('name','like','%'.trim((string)$request->search).'%');
        if ($request->filled('platform_id')) $query->where('platform_id',(int)$request->platform_id);
        if ($request->filled('status')) $query->where('status',$request->status);
        $perPage=min(100,max(10,(int)$request->input('per_page',10)));
        $items=$query->orderBy('sort_order')->orderBy('id')->paginate($perPage);
        return response()->json(['status'=>true,'data'=>$items,'meta'=>[
            'total'=>(int)\App\Models\Category::count(), 'active'=>(int)\App\Models\Category::where('status','active')->count(),
            'inactive'=>(int)\App\Models\Category::where('status','inactive')->count(),
            'platforms'=>\App\Models\Platform::orderBy('sort_order')->get(['id','name','icon','status']),
        ]]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data=$request->validate(['platform_id'=>['required','integer','exists:platforms,id'],'icon'=>['nullable','image','mimes:jpeg,png,jpg,gif,svg,webp','max:2048'],'icon_url'=>['nullable','url:http,https','max:2048'],'icon_preset'=>['nullable','string','in:global,facebook,instagram,tiktok,youtube,telegram,discord,google,shop,app'],'clear_icon'=>['nullable','boolean'],'sort_order'=>['required','integer','min:0'],'name'=>['required','string','max:255'],'is_discount'=>['required','boolean'],'status'=>['required','in:active,inactive']]);
        if($request->boolean('clear_icon')){$filename=null;}elseif($request->filled('icon_url')){$filename=$data['icon_url'];}elseif($request->filled('icon_preset')){$filename='preset:'.$data['icon_preset'];}else{$file=$request->file('icon');abort_unless($file,422,'Vui lòng chọn ảnh, nhập URL, chọn icon hoặc chọn không dùng.');$filename=time().'-'.Str::random(10).'.'.$file->extension();if(!is_dir(public_path('uploads/categories')))mkdir(public_path('uploads/categories'),0777,true);$file->move(public_path('uploads/categories'),$filename);}
        $item=\App\Models\Category::create([...$data,'icon'=>$filename,'code'=>Str::random(10)]);
        return response()->json(['status'=>true,'message'=>'Đã thêm phân loại.','data'=>$item],201);
    }

    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data=$request->validate(['platform_id'=>['required','integer','exists:platforms,id'],'icon'=>['nullable','image','mimes:jpeg,png,jpg,gif,svg,webp','max:2048'],'icon_url'=>['nullable','url:http,https','max:2048'],'icon_preset'=>['nullable','string','in:global,facebook,instagram,tiktok,youtube,telegram,discord,google,shop,app'],'clear_icon'=>['nullable','boolean'],'sort_order'=>['required','integer','min:0'],'name'=>['required','string','max:255'],'is_discount'=>['required','boolean'],'status'=>['required','in:active,inactive']]);
        $item=\App\Models\Category::findOrFail($id);unset($data['icon']);
        unset($data['icon_url'],$data['icon_preset'],$data['clear_icon']);
        if($request->boolean('clear_icon')||$request->filled('icon_url')){$raw=(string)$item->getRawOriginal('icon');$old=public_path('uploads/categories/'.basename($raw));if(!filter_var($raw,FILTER_VALIDATE_URL)&&is_file($old))@unlink($old);$data['icon']=$request->boolean('clear_icon')?null:$request->input('icon_url');}
        elseif($request->filled('icon_preset')){$raw=(string)$item->getRawOriginal('icon');$old=public_path('uploads/categories/'.basename($raw));if(!str_starts_with($raw,'preset:')&&!filter_var($raw,FILTER_VALIDATE_URL)&&is_file($old))@unlink($old);$data['icon']='preset:'.$request->input('icon_preset');}
        elseif($request->hasFile('icon')){$raw=(string)$item->getRawOriginal('icon');$old=public_path('uploads/categories/'.basename($raw));if(!filter_var($raw,FILTER_VALIDATE_URL)&&is_file($old))@unlink($old);$file=$request->file('icon');$filename=time().'-'.Str::random(10).'.'.$file->extension();if(!is_dir(public_path('uploads/categories')))mkdir(public_path('uploads/categories'),0777,true);$file->move(public_path('uploads/categories'),$filename);$data['icon']=$filename;}
        $item->fill($data)->save();return response()->json(['status'=>true,'message'=>'Đã cập nhật phân loại.','data'=>$item->fresh()]);
    }

    public function reorderCategories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);$data=$request->validate(['ids'=>['required','array','min:1'],'ids.*'=>['integer','exists:categories,id']]);
        \Illuminate\Support\Facades\DB::transaction(function()use($data){foreach($data['ids'] as $order=>$id)\App\Models\Category::whereKey($id)->update(['sort_order'=>$order]);});
        return response()->json(['status'=>true,'message'=>'Đã cập nhật thứ tự phân loại.']);
    }

    public function destroyAllCategories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);\App\Models\Category::query()->delete();return response()->json(['status'=>true,'message'=>'Đã xoá tất cả phân loại.']);
    }

    public function categoryImportSource(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data=$request->validate(['platform_id'=>['required','integer','exists:platforms,id'],'provider_id'=>['required','integer','exists:api_providers,id'],'keyword'=>['required','string','max:255'],'refresh'=>['nullable','boolean']]);
        $provider = \App\Models\ApiProvider::findOrFail($data['provider_id']);
        $key = 'provider_services_' . $provider->id;
        if ($request->boolean('refresh')) Cache::forget($key);
        $services = Cache::get($key);
        if ($services === null) {
            $services = (new \App\Library\SmmApiCustom($provider->api_url, $provider->api_key))->services();
            if (!isset($services['error'])) Cache::put($key, $services, 300);
        }
        if (isset($services['error'])) {
            $providerError = $services['error'];
            $code = is_array($providerError) ? (int) ($providerError['code'] ?? 422) : 422;
            $detail = is_array($providerError) ? (string) ($providerError['message'] ?? '') : (string) $providerError;
            $message = $code === 429
                ? 'API Provider đang giới hạn lượt truy cập. Vui lòng chờ 1–5 phút rồi thử lại, không bấm Làm mới liên tục.'
                : ($detail !== '' ? $detail : 'Không thể tải dữ liệu từ API Provider.');
            return response()->json(['status' => false, 'message' => $message, 'provider_code' => $code], $code === 429 ? 429 : 422);
        }
        $rows=[];foreach($services as $service){$name=(string)($service['category']??'');if($name!==''&&stripos($name,$data['keyword'])!==false)$rows[$name]=['category_name'=>$name,'category'=>$name,'platform'=>$name];}
        return response()->json(['status'=>true,'data'=>array_values($rows),'message'=>count($rows)?'Đã tải danh mục từ nguồn.':'Không có danh mục phù hợp.']);
    }

    public function importCategories(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);$request->merge(['type'=>'categories']);
        return app(\App\Http\Controllers\Admin\Services\ImportControler::class)->add($request);
    }

    public function serviceImportSource(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data=$request->validate(['category_id'=>['required','integer','exists:categories,id'],'provider_id'=>['required','integer','exists:api_providers,id']]);
        $provider=\App\Models\ApiProvider::findOrFail($data['provider_id']);$key='provider_services_'.$provider->id;$services=Cache::get($key);
        if($services===null){$services=(new \App\Library\SmmApiCustom($provider->api_url,$provider->api_key))->services();if(!isset($services['error']))Cache::put($key,$services,300);}
        if(isset($services['error'])){$error=$services['error'];$code=is_array($error)?(int)($error['code']??422):422;$detail=is_array($error)?(string)($error['message']??''):(string)$error;return response()->json(['status'=>false,'message'=>$code===429?'API Provider đang giới hạn lượt truy cập. Vui lòng chờ 1–5 phút.':$detail],$code===429?429:422);}
        $rows=collect($services)->map(fn($item)=>['service'=>(string)($item['service']??''),'name'=>(string)($item['name']??''),'category'=>(string)($item['category']??''),'type'=>(string)($item['type']??'Default'),'rate'=>(float)($item['rate']??0),'rate_format'=>strtoupper((string)($provider->currency?:'USD')),'min'=>(int)($item['min']??0),'max'=>(int)($item['max']??0)])->values();
        return response()->json(['status'=>true,'data'=>$rows]);
    }

    public function importServices(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);$request->merge(['ids'=>$request->input('ids',$request->input('services',[]))]);
        return app(\App\Http\Controllers\Admin\Services\ImportControler::class)->addServices($request);
    }

    public function services(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domains = $this->serviceDomains($request);
        $query = Service::with(['category:id,name,icon,platform_id', 'apiProvider:id,name,currency,exchange_rate'])
            ->where(function ($builder) use ($domains) {
                $builder->whereIn('domain', $domains)->orWhereNull('domain')->orWhere('domain', '');
            });
        if ($request->filled('category_id')) $query->where('category_id', (int) $request->category_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($builder) => $builder->where('id', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhere('provider_id', 'like', "%{$search}%"));
        }
        $requestedPerPage = $request->input('per_page', 10);
        $perPage = $requestedPerPage === 'all'
            ? max(1, (clone $query)->count())
            : min(1000, max(10, (int) $requestedPerPage));
        $page = $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage);
        $base = Service::where(function ($builder) use ($domains) {
            $builder->whereIn('domain', $domains)->orWhereNull('domain')->orWhere('domain', '');
        });
        return response()->json(['status' => true, 'data' => $page, 'meta' => [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'inactive' => (clone $base)->where('status', 'inactive')->count(),
            'api' => (clone $base)->where('mode', 'option')->count(),
            'categories' => \App\Models\Category::where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'providers' => \App\Models\ApiProvider::where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ]]);
    }

    public function destroyAllServices(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        Service::where('domain', $this->domain($request))->delete();
        return response()->json(['status'=>true,'message'=>'Đã xoá tất cả dịch vụ.']);
    }

    public function bulkUpdateServices(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data=$request->validate(['ids'=>['required','string'],'action'=>['required','in:attributes,description'],'attributes'=>['nullable','array'],'attributes.*'=>['string'],'description'=>['nullable','string']]);
        $ids=array_values(array_filter(array_map('intval',explode(',',$data['ids']))));
        $query=Service::where('domain',$this->domain($request))->whereIn('id',$ids);
        if($data['action']==='attributes')$query->update(['attributes'=>json_encode($data['attributes']??[])]);else $query->update(['description'=>$data['description']??'']);
        return response()->json(['status'=>true,'message'=>$data['action']==='attributes'?'Đã cập nhật thuộc tính.':'Đã cập nhật nội dung.']);
    }

    public function orders(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domain = $this->domain($request);
        $query = Order::with(['user:id,username,email', 'service:id,name,rate_original,status,category_id'])->where('domain', $domain);
        if ($request->filled('username')) $query->whereHas('user', fn ($q) => $q->where('username', 'like', '%' . $request->username . '%'));
        if ($request->filled('user_id')) $query->where('user_id', (int) $request->user_id);
        if ($request->filled('email')) $query->whereHas('user', fn ($q) => $q->where('email', 'like', '%' . trim((string) $request->email) . '%'));
        if ($request->filled('order_id')) $query->where('id', (int) $request->order_id);
        if ($request->filled('order_ids')) {
            $ids = array_values(array_filter(array_map('intval', preg_split('/[\s,]+/', (string) $request->order_ids))));
            if ($ids) $query->whereIn('id', $ids);
        }
        if ($request->filled('link')) $query->where('link', 'like', '%' . trim((string) $request->link) . '%');
        if ($request->filled('api_order_id')) {
            $apiOrderId = trim((string) $request->api_order_id);
            $query->where(fn ($q) => $q->where('root_id', $apiOrderId)->orWhere('response_data->order', $apiOrderId));
        }
        if ($request->filled('provider_id')) $query->where('provider', (int) $request->provider_id);
        if ($request->filled('service_id')) $query->where('service_id', (int) $request->service_id);
        if ($request->filled('platform_id')) {
            $query->whereHas('service.category', fn ($q) => $q->where('platform_id', (int) $request->platform_id));
        }
        if ($request->filled('service_status')) {
            $query->whereHas('service', fn ($q) => $q->where('status', $request->service_status));
        }
        if ($request->input('source') === 'api') $query->whereNotNull('provider');
        if ($request->input('source') === 'manual') $query->whereNull('provider');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        $page = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $page->getCollection()->transform(function (Order $order) {
            $runs = max(1, (int) ($order->loop_quantity ?: 1));
            $cost = ((float) ($order->service?->rate_original ?? $order->rate) * ((int) $order->quantity * $runs)) / 1000;
            $order->profit = (float) $order->total - $cost;
            $order->rate_usd = convert_currency($order->rate, 'USD', false);
            return $order;
        });
        $base = Order::where('orders.domain', $domain);
        if ($request->filled('date_from')) $base->whereDate('orders.created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $base->whereDate('orders.created_at', '<=', $request->date_to);
        $statuses = ['pending' => 'Pending', 'completed' => 'Completed', 'partial' => 'Partial', 'canceled' => 'Canceled'];
        $counts = ['total' => (clone $base)->count()];
        foreach ($statuses as $key => $status) $counts[$key] = (clone $base)->where('status', $status)->count();
        $financial = (clone $base)
            ->leftJoin('services', 'services.id', '=', 'orders.service_id')
            ->selectRaw('COALESCE(SUM(orders.total), 0) as revenue')
            ->selectRaw('COALESCE(SUM((COALESCE(services.rate_original, orders.rate) * orders.quantity * COALESCE(NULLIF(orders.loop_quantity, 0), 1)) / 1000), 0) as cost')
            ->first();
        $counts['revenue'] = (float) ($financial->revenue ?? 0);
        $counts['cost'] = (float) ($financial->cost ?? 0);
        $counts['profit'] = $counts['revenue'] - $counts['cost'];
        return response()->json(['status' => true, 'data' => $page, 'meta' => [
            'counts' => $counts,
            'services' => Service::where('domain', $domain)->orderBy('name')->get(['id', 'name', 'category_id']),
            'providers' => ApiProvider::orderBy('name')->get(['id', 'name']),
            'platforms' => \App\Models\Platform::orderBy('name')->get(['id', 'name']),
        ]]);
    }

    public function updateOrder(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'status' => ['required', 'string'], 'start_count' => ['nullable', 'integer', 'min:0'], 'remains' => ['nullable', 'integer', 'min:0'],
        ]);
        $order = Order::where('domain', $this->domain($request))->with('user')->findOrFail($id);
        $wasPartial = $order->status === 'Partial';
        $order->fill($data)->save();
        if (!$wasPartial && $data['status'] === 'Partial' && (int) $order->remains > 0 && $order->user) {
            $refund = ((float) $order->rate * (int) $order->remains) / 1000;
            $before = (float) $order->user->balance;
            $order->user->increment('balance', $refund);
            Transaction::create(['user_id' => $order->user->id, 'transaction_code' => 'ORDER_' . $order->id, 'type' => 'Refund', 'balance_before' => $before, 'balance_after' => $before + $refund, 'amount' => $refund, 'description' => 'Hoàn tiền đơn hàng #' . $order->id, 'status' => 'Success', 'domain' => $this->domain($request)]);
        }
        return response()->json(['status' => true, 'message' => 'Đã cập nhật đơn hàng.']);
    }

    public function deposits(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = Recharge::with('user:id,username')->where('domain', $this->domain($request));
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('username', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('type')) {
            $type = (string) $request->type;
            if ($type === 'manual') $query->where('method', 'Cộng tay');
            elseif ($type === 'bank') $query->where(fn ($q) => $q->where('type', 'bank')->orWhere('method', 'banking'));
            else $query->where('type', $type);
        }
        $page = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $currency = (string) ($request->user()->currency ?: 'VND');
        $page->getCollection()->transform(function (Recharge $deposit) use ($currency) {
            $deposit->amount_format = convert_currency($deposit->amount, $currency, false);
            $deposit->bonus_format = convert_currency($deposit->bonus, $currency, false);
            $deposit->real_amount_format = convert_currency($deposit->real_amount, $currency, false);
            return $deposit;
        });
        return response()->json(['status' => true, 'data' => $page]);
    }

    public function approveDeposit(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $request->merge(['id' => $id]);
        $request->attributes->set('admin_domain', $this->domain($request));
        return app(\App\Http\Controllers\Admin\Data\DataAdminController::class)->rechargeApprove($request);
    }

    public function cancelDeposit(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $request->merge(['id' => $id]);
        $request->attributes->set('admin_domain', $this->domain($request));
        return app(\App\Http\Controllers\Admin\Data\DataAdminController::class)->rechargeCancel($request);
    }

    private function paymentApiKeyFields(): array
    {
        return [
            'mb_account_number', 'mb_api_key', 'vcb_account_number', 'vcb_api_key',
            'acb_account_number', 'acb_api_key', 'vtb_account_number', 'vtb_api_key',
            'ocb_account_number', 'ocb_username', 'ocb_password',
            'usdt_wallet', 'usdt_wallet_token', 'usdt_exchange_rate',
            'telegram_bot', 'telegram_chat_id', 'telegram_status',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
        ];
    }

    public function paymentApiKeys(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();
        return response()->json(['status' => true, 'data' => $config->only($this->paymentApiKeyFields())]);
    }

    public function updatePaymentApiKeys(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $rules = array_fill_keys($this->paymentApiKeyFields(), ['sometimes', 'nullable', 'string', 'max:2000']);
        $rules['telegram_status'] = ['sometimes', 'required', 'in:active,inactive'];
        $data = $request->validate($rules);
        abort_if($data === [], 422, 'Không có cấu hình để cập nhật.');
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();
        $config->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã lưu cấu hình API Key.', 'data' => $config->only(array_keys($data))]);
    }

    public function ocbLogin(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();
        $ocb = new \App\Library\OCB();
        $result = json_decode($ocb->login_ocb((string) $config->ocb_username, (string) $config->ocb_password), true) ?: [];
        if (($result['status'] ?? null) === 'otp' && !empty($result['action'])) {
            Cache::put('ocb:otp-action:' . $this->domain($request), $result['action'], now()->addMinutes(5));
            return response()->json(['status' => true, 'requires_otp' => true, 'message' => $result['msg'] ?? 'Nhập OTP OCB.']);
        }
        return response()->json(['status' => ($result['status'] ?? null) === 'success', 'requires_otp' => false, 'message' => $result['msg'] ?? 'Đăng nhập OCB thất bại.'], ($result['status'] ?? null) === 'success' ? 200 : 422);
    }

    public function ocbOtp(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $code = $request->validate(['otp' => ['required', 'digits:6']])['otp'];
        $key = 'ocb:otp-action:' . $this->domain($request);
        $action = Cache::get($key);
        abort_if(!$action, 422, 'Phiên OTP OCB đã hết hạn.');
        $ocb = new \App\Library\OCB();
        $result = $ocb->submit_otp($action, $code);
        if (($result['status'] ?? null) !== 'success') return response()->json(['status' => false, 'message' => $result['msg'] ?? 'OTP không hợp lệ.'], 422);
        Cache::forget($key);
        return response()->json(['status' => true, 'message' => 'Xác thực OCB thành công.']);
    }

    public function ocbTransactions(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $limit = min(max((int) $request->integer('limit', 20), 1), 100);
        $config = \App\Models\Config::where('domain', $this->domain($request))->firstOrFail();

        if (!$config->ocb_username || !$config->ocb_password || !$config->ocb_account_number) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng lưu đầy đủ cấu hình OCB trước.',
            ], 422);
        }

        $ocb = new \App\Library\OCB();
        $login = json_decode(
            $ocb->login_ocb((string) $config->ocb_username, (string) $config->ocb_password),
            true
        ) ?: [];

        if (($login['status'] ?? null) !== 'success' || empty($login['accessToken'])) {
            return response()->json([
                'status' => false,
                'requires_otp' => ($login['status'] ?? null) === 'otp',
                'message' => $login['msg'] ?? 'Không thể đăng nhập OCB.',
            ], 422);
        }

        $balance = json_decode(
            $ocb->get_balance($login['accessToken'], (string) $config->ocb_account_number),
            true
        ) ?: [];
        $history = json_decode(
            $ocb->LSGD((string) $config->ocb_account_number, $limit, $login['accessToken']),
            true
        );

        if (!is_array($history) || isset($history['code'])) {
            return response()->json([
                'status' => false,
                'message' => $history['msg'] ?? 'Không thể lấy lịch sử giao dịch OCB.',
            ], 422);
        }

        $items = $history['elements'] ?? (array_is_list($history) ? $history : []);
        $transactions = collect($items)->map(function (array $element): array {
            $attributes = $element['attributes'] ?? $element;
            return [
                'id' => $element['id'] ?? ($attributes['reference'] ?? null),
                'reference' => $attributes['reference'] ?? null,
                'description' => $attributes['description'] ?? '',
                'amount' => (float) ($attributes['transactionAmountCurrency']['amount'] ?? 0),
                'currency' => $attributes['transactionAmountCurrency']['currencyCode'] ?? 'VND',
                'type' => $attributes['creditDebitIndicator'] ?? null,
                'booking_date' => $attributes['bookingDate'] ?? null,
                'creation_time' => $attributes['creationTime'] ?? null,
                'counterparty' => $attributes['counterPartyName'] ?? null,
                'balance' => isset($attributes['runningBalance'])
                    ? (float) $attributes['runningBalance']
                    : null,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $transactions,
            'count' => $transactions->count(),
            'balance' => isset($balance['balance']) ? (float) $balance['balance'] : null,
            'account_number' => $balance['accountNo'] ?? $config->ocb_account_number,
            'currency' => 'VND',
        ]);
    }

    public function affiliates(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domain = $this->domain($request);
        $query = \App\Models\AffiliateRef::with(['user:id,username,total_deposit', 'refUser:id,username'])->where('domain', $domain);
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('referral_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('username', 'like', "%{$search}%"))
                    ->orWhereHas('refUser', fn ($user) => $user->where('username', 'like', "%{$search}%"));
            });
        }
        $page = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $base = \App\Models\AffiliateRef::where('affiliate_refs.domain', $domain);
        $totalDeposit = (clone $base)->leftJoin('users', 'users.id', '=', 'affiliate_refs.user_id')->sum('users.total_deposit');
        return response()->json(['status' => true, 'data' => $page, 'meta' => [
            'total' => (clone $base)->count(),
            'total_deposit' => (float) $totalDeposit,
            'total_commission' => (float) \App\Models\AffiliateRef::where('domain', $domain)->sum('commission'),
        ]]);
    }

    public function tickets(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domain = $this->domain($request);
        $query = Ticket::with('user:id,username')->withCount('messages')->where('domain', $domain);
        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('username', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        $page = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $base = Ticket::where('domain', $domain);
        return response()->json(['status' => true, 'data' => $page, 'meta' => [
            'total' => (clone $base)->count(), 'pending' => (clone $base)->where('status', 'pending')->count(),
            'processing' => (clone $base)->where('status', 'processing')->count(), 'completed' => (clone $base)->where('status', 'completed')->count(),
        ]]);
    }

    public function ticketDetail(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $ticket = Ticket::with(['user:id,username', 'messages' => fn ($query) => $query->orderBy('id')])
            ->where('domain', $this->domain($request))->findOrFail($id);
        return response()->json(['status' => true, 'data' => $ticket]);
    }

    public function updateTicket(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'type' => ['required', 'in:refill,cancel,other,orther,order'], 'order_id' => ['nullable', 'string', 'max:100'], 'status' => ['required', 'in:open,closed,pending,processing,completed']]);
        $ticket = Ticket::where('domain', $this->domain($request))->findOrFail($id);
        $data['title'] = strip_tags($data['title']);
        $ticket->fill($data)->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật ticket.']);
    }

    public function replyTicket(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $message = (string) $request->validate(['message' => ['required', 'string', 'max:5000']])['message'];
        $ticket = Ticket::where('domain', $this->domain($request))->findOrFail($id);
        $ticketMessage = \App\Models\TicketMessage::create(['ticket_id' => $ticket->id, 'user_id' => $ticket->user_id, 'message' => strip_tags($message), 'type' => 'admin', 'domain' => $this->domain($request)]);
        $ticket->update(['status' => 'processing']);
        return response()->json(['status' => true, 'message' => 'Đã gửi phản hồi.', 'data' => $ticketMessage]);
    }

    public function destroyTicket(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        Ticket::where('domain', $this->domain($request))->findOrFail($id)->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa ticket.']);
    }

    public function refreshOrderStatuses(Request $request)
    {
        $this->authorizeAdmin($request);
        return app(\App\Http\Controllers\Cron\Order\StatusOrderCronJobController::class)->index($request);
    }

    public function cleanOrders(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $days = (int) $request->validate(['days' => ['required', 'integer', 'min:1']])['days'];
        $deleted = Order::where('domain', $this->domain($request))->where('created_at', '<', now()->subDays($days))->delete();
        return response()->json(['status' => true, 'message' => "Đã dọn dẹp {$deleted} đơn hàng cũ."]);
    }

    public function destroyAllOrders(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $deleted = Order::where('domain', $this->domain($request))->delete();
        return response()->json(['status' => true, 'message' => "Đã xóa {$deleted} đơn hàng."]);
    }

    public function aiChat(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $message = mb_strtolower(trim((string) $request->validate(['message' => ['required', 'string', 'max:500']])['message']));
        $domain = $this->domain($request);
        if (str_contains($message, 'thành viên') || str_contains($message, 'người dùng')) {
            $answer = 'Tổng thành viên: <b>' . number_format(User::where('domain', $domain)->count()) . '</b><br>Đăng ký hôm nay: <b>' . number_format(User::where('domain', $domain)->whereDate('created_at', today())->count()) . '</b>';
        } elseif (str_contains($message, 'đơn hàng')) {
            $answer = 'Tổng đơn hàng: <b>' . number_format(Order::where('domain', $domain)->count()) . '</b><br>Đơn hôm nay: <b>' . number_format(Order::where('domain', $domain)->whereDate('created_at', today())->count()) . '</b>';
        } elseif (str_contains($message, 'doanh thu')) {
            $answer = 'Doanh thu hôm nay: <b>' . number_format((float) Order::where('domain', $domain)->whereDate('created_at', today())->sum('total')) . ' đ</b><br>Tổng doanh thu: <b>' . number_format((float) Order::where('domain', $domain)->sum('total')) . ' đ</b>';
        } else {
            $answer = 'Bạn có thể hỏi: <b>tổng thành viên</b>, <b>đơn hàng hôm nay</b> hoặc <b>doanh thu</b>.';
        }
        return response()->json(['status' => true, 'data' => ['message' => $answer]]);
    }

    public function users(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $domain = $this->domain($request);
        $query = User::where('domain', $domain);
        if ($request->filled('search')) $query->search(trim((string) $request->search));
        if ($request->filled('level')) $query->where('level', $request->level);
        if ($request->filled('status') && $request->status !== 'all') $query->where('status', $request->status);
        $users = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))), [
            'id', 'username', 'email', 'phone', 'balance', 'total_deposit', 'level', 'role', 'status', 'currency', 'last_login', 'created_at',
        ]);
        return response()->json(['status' => true, 'data' => $users, 'meta' => [
            'total_users' => User::where('domain', $domain)->count(),
            'total_balance' => (float) User::where('domain', $domain)->sum('balance'),
            'total_deposit' => (float) User::where('domain', $domain)->sum('total_deposit'),
        ]]);
    }

    public function updateUser(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'level' => ['required', 'in:member,silver,gold,platinum,diamond'],
            'status' => ['required', 'in:active,inactive,suspended,blocked'],
            'total_commission' => ['nullable', 'numeric', 'min:0'],
            'new_password' => ['nullable', 'string', 'min:6', 'max:100'],
        ]);
        $user = User::where('domain', $this->domain($request))->findOrFail($id);
        $user->level = $data['level']; $user->status = $data['status'];
        if (array_key_exists('total_commission', $data)) $user->total_commission = $data['total_commission'];
        if (!empty($data['new_password'])) $user->password = Hash::make($data['new_password']);
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật tài khoản.']);
    }

    public function userDetail(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $user = User::where('domain', $this->domain($request))->findOrFail($id, [
            'id', 'username', 'email', 'phone', 'api_key', 'balance', 'total_deposit',
            'level', 'role', 'status', 'currency', 'commission', 'total_commission',
            'last_login', 'created_at',
        ]);
        $transactions = Transaction::where('domain', $this->domain($request))
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(2000)
            ->get(['id', 'transaction_code', 'type', 'balance_before', 'amount', 'balance_after', 'description', 'status', 'created_at']);

        return response()->json(['status' => true, 'data' => ['user' => $user, 'transactions' => $transactions]]);
    }

    public function updateUserBalance(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['action' => ['required', 'in:add,sub'], 'amount' => ['required', 'numeric', 'min:0.00001'], 'reason' => ['nullable', 'string', 'max:500']]);
        $user = User::where('domain', $this->domain($request))->findOrFail($id);
        $amount = (float) $data['amount']; $before = (float) $user->balance;
        $after = $data['action'] === 'add' ? $before + $amount : $before - $amount;
        $user->balance = $after;
        if ($data['action'] === 'add') $user->total_deposit = (float) $user->total_deposit + $amount;
        $user->save();
        Transaction::create([
            'user_id' => $user->id, 'transaction_code' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(10)),
            'type' => $data['action'], 'balance_before' => $before, 'balance_after' => $after, 'amount' => $amount,
            'description' => trim((string) ($data['reason'] ?? '')) ?: ($data['action'] === 'add' ? 'Cộng tiền tài khoản' : 'Trừ tiền tài khoản'),
            'status' => 'success', 'domain' => $this->domain($request),
        ]);
        if ($data['action'] === 'add') $user->upgradeLevel($user->total_deposit);
        return response()->json(['status' => true, 'message' => 'Đã cập nhật số dư.', 'data' => ['balance' => (float) $user->fresh()->balance]]);
    }

    public function updateProviderBalance(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $provider = \App\Models\ApiProvider::findOrFail($id);
        return app(\App\Http\Controllers\Admin\Provider\ProviderController::class)->updateBalance($provider->id);
    }

    public function providerFavicon(Request $request, int $id)
    {
        $this->authorizeAdmin($request);
        $provider = \App\Models\ApiProvider::findOrFail($id);
        $cacheKey = 'provider:favicon:' . $provider->id . ':' . md5((string) $provider->api_url);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && isset($cached['data'], $cached['type'])) {
            return response(base64_decode($cached['data']), 200, [
                'Content-Type' => $cached['type'],
                'Cache-Control' => 'private, max-age=86400',
            ]);
        }
        if ($cached === false) {
            abort(404);
        }

        $parts = parse_url((string) $provider->api_url);
        $scheme = in_array($parts['scheme'] ?? '', ['http', 'https'], true) ? $parts['scheme'] : 'https';
        $host = (string) ($parts['host'] ?? '');
        abort_if($host === '', 404);

        $origins = ["{$scheme}://{$host}"];
        $labels = explode('.', $host);
        if (count($labels) > 2) {
            $rootHost = implode('.', array_slice($labels, -2));
            $origins[] = "{$scheme}://{$rootHost}";
            $origins[] = "{$scheme}://www.{$rootHost}";
        }
        $origins = array_values(array_unique($origins));
        $candidates = [];
        $client = Http::withOptions(['verify' => false, 'allow_redirects' => true])
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 SMMV2 Favicon Fetcher', 'Accept' => 'text/html,image/*'])
            ->connectTimeout(4)
            ->timeout(8);

        foreach ($origins as $origin) {
            try {
                $page = $client->get($origin);
                if ($page->successful()) {
                    $dom = new \DOMDocument;
                    @$dom->loadHTML($page->body());
                    $xpath = new \DOMXPath($dom);
                    foreach ($xpath->query('//link[contains(translate(@rel,"ICON","icon"),"icon")][@href]') ?: [] as $link) {
                        $href = trim((string) $link->getAttribute('href'));
                        if ($href === '') continue;
                        if (str_starts_with($href, '//')) $href = $scheme . ':' . $href;
                        elseif (str_starts_with($href, '/')) $href = $origin . $href;
                        elseif (!preg_match('#^https?://#i', $href)) $href = $origin . '/' . ltrim($href, '/');
                        $candidates[] = $href;
                    }
                }
            } catch (Throwable) {
                // Continue with conventional favicon paths and other origins.
            }
            $candidates[] = $origin . '/favicon.ico';
            $candidates[] = $origin . '/favicon.png';
            $candidates[] = $origin . '/apple-touch-icon.png';
        }

        foreach (array_unique($candidates) as $url) {
            try {
                $image = $client->get($url);
                $body = $image->body();
                $type = strtolower(trim(explode(';', (string) $image->header('Content-Type'))[0]));
                if (!$image->successful() || $body === '' || strlen($body) > 1024 * 1024) continue;
                if (!str_starts_with($type, 'image/') && !in_array($type, ['application/octet-stream', 'binary/octet-stream'], true)) continue;
                $type = str_starts_with($type, 'image/') ? $type : 'image/x-icon';
                Cache::put($cacheKey, ['data' => base64_encode($body), 'type' => $type], now()->addDay());
                return response($body, 200, ['Content-Type' => $type, 'Cache-Control' => 'private, max-age=86400']);
            } catch (Throwable) {
                // Try the next discovered favicon URL.
            }
        }

        Cache::put($cacheKey, false, now()->addHour());
        abort(404);
    }

    public function syncProviderBalances(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $providers = \App\Models\ApiProvider::query()->orderBy('id')->get();
        $results = [];
        $updated = 0;

        foreach ($providers as $provider) {
            try {
                $balance = (new \App\Library\SmmApiCustom($provider->api_url, $provider->api_key))->balance();
                if (!is_array($balance) || isset($balance['error']) || !array_key_exists('balance', $balance)) {
                    $error = is_array($balance) ? ($balance['error'] ?? null) : null;
                    $results[] = [
                        'id' => $provider->id,
                        'name' => $provider->name,
                        'success' => false,
                        'message' => is_array($error) ? (string) ($error['message'] ?? 'Dữ liệu số dư không hợp lệ.') : ((string) $error ?: 'Nhà cung cấp không trả về số dư.'),
                    ];
                    continue;
                }

                $provider->balance = (float) $balance['balance'];
                $provider->save();
                $updated++;
                $results[] = ['id' => $provider->id, 'name' => $provider->name, 'success' => true, 'balance' => (float) $provider->balance, 'currency' => $provider->currency];
            } catch (Throwable $error) {
                report($error);
                $results[] = ['id' => $provider->id, 'name' => $provider->name, 'success' => false, 'message' => $error->getMessage()];
            }
        }

        $freshProviders = \App\Models\ApiProvider::all();
        $totalVnd = (float) $freshProviders->sum(function ($provider) {
            $balance = (float) $provider->balance;
            return strtoupper((string) $provider->currency) === 'VND'
                ? $balance
                : $balance * max(1, (float) $provider->exchange_rate);
        });
        $failed = count($results) - $updated;

        return response()->json([
            'status' => $failed === 0,
            'message' => "Đã đồng bộ số dư {$updated}/{$providers->count()} nhà cung cấp.",
            'data' => ['updated' => $updated, 'failed' => $failed, 'total_vnd' => $totalVnd, 'providers' => $results],
        ], $failed === 0 ? 200 : 207);
    }

    public function providers(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $query = \App\Models\ApiProvider::query();
        if ($request->filled('search')) $query->search(trim((string) $request->search));
        $page = $query->orderByDesc('id')->paginate(min(100, max(10, (int) $request->input('per_page', 20))));
        $domain = $this->domain($request);
        $page->getCollection()->transform(function ($provider) use ($domain) {
            $services = Service::where('provider', $provider->id)->where('domain', $domain);
            $orders = Order::where('provider', $provider->id)->where('domain', $domain);
            $orderRows = (clone $orders)->with('service:id,rate_original')->get(['id', 'service_id', 'quantity', 'total']);
            $revenue = (float) $orderRows->sum('total');
            $cost = (float) $orderRows->sum(fn ($order) => ((float) ($order->service?->rate_original ?? 0) * (int) $order->quantity) / 1000);
            $provider->count_categories = \App\Models\Category::whereHas('services', function ($query) use ($provider) {
                $query->where('provider', $provider->id);
            })->count();
            $provider->count_services = (clone $services)->count();
            $provider->count_orders = (clone $orders)->count();
            $provider->revenue = $revenue;
            $provider->profit = $revenue - $cost;
            $provider->balance_format = number_format((float) $provider->balance, 0, ',', '.') . ' ' . ($provider->currency ?: 'đ');
            return $provider;
        });
        $allProviders = \App\Models\ApiProvider::all();
        $totalVnd = (float) $allProviders->sum(function ($provider) {
            $balance = (float) $provider->balance;
            return strtoupper((string) $provider->currency) === 'VND'
                ? $balance
                : $balance * max(1, (float) $provider->exchange_rate);
        });

        return response()->json(['status' => true, 'data' => $page, 'meta' => [
            'total' => $allProviders->count(),
            'active' => $allProviders->where('status', 'active')->count(),
            'inactive' => $allProviders->where('status', '!=', 'active')->count(),
            'total_balance_vnd' => $totalVnd,
        ]]);
    }

    public function storeProvider(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $request->merge((array) $request->input('data', []));
        return app(\App\Http\Controllers\Admin\Provider\ProviderController::class)->store($request);
    }

    public function updateProvider(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $request->merge((array) $request->input('data', []));
        return app(\App\Http\Controllers\Admin\Provider\ProviderController::class)->update($request, $id);
    }

    public function updateProviderPrices(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $provider = \App\Models\ApiProvider::findOrFail($id);
        return app(\App\Http\Controllers\Admin\Provider\ProviderController::class)->updatePrices($provider->id);
    }

    public function contactWidgets(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        return response()->json(['status' => true, 'data' => \App\Models\ContactWidget::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function storeContactWidget(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:1000'],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['image'] = $this->storeContactWidgetImage($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = ((int) \App\Models\ContactWidget::max('sort_order')) + 1;
        $widget = \App\Models\ContactWidget::create($data);
        return response()->json(['status' => true, 'message' => 'Đã thêm kênh liên hệ.', 'data' => $widget], 201);
    }

    public function updateContactWidget(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $widget = \App\Models\ContactWidget::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:1000'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        if ($request->hasFile('image')) {
            $this->deleteContactWidgetImage($widget->image);
            $data['image'] = $this->storeContactWidgetImage($request);
        }
        $data['is_active'] = $request->boolean('is_active', $widget->is_active);
        $widget->update($data);
        return response()->json(['status' => true, 'message' => 'Đã cập nhật kênh liên hệ.', 'data' => $widget->fresh()]);
    }

    public function updateContactWidgetStatus(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $widget = \App\Models\ContactWidget::findOrFail($id);
        $widget->update($data);
        return response()->json(['status' => true, 'message' => 'Đã cập nhật trạng thái.', 'data' => $widget]);
    }

    public function destroyContactWidget(Request $request, int $id): JsonResponse
    {
        $this->authorizeAdmin($request);
        $widget = \App\Models\ContactWidget::findOrFail($id);
        $this->deleteContactWidgetImage($widget->image);
        $widget->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa kênh liên hệ.']);
    }

    private function storeContactWidgetImage(Request $request): string
    {
        $file = $request->file('image');
        $directory = public_path('uploads/contact-widgets');
        if (!is_dir($directory)) mkdir($directory, 0755, true);
        $filename = \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);
        return '/uploads/contact-widgets/' . $filename;
    }

    private function deleteContactWidgetImage(?string $image): void
    {
        if (!$image || !str_starts_with($image, '/uploads/contact-widgets/')) return;
        $path = public_path(ltrim($image, '/'));
        if (is_file($path)) @unlink($path);
    }
}
