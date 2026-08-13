<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoadDataController extends Controller
{
    public function index(Request $request)
    {

        // get data from datatable
        $model = $request->model;

        $start = $request->start ?? 0;

        if ($request->length > 0) {
            $length = $request->length;
        } elseif ($request->length < 0) {
            $length = 1000;
        } else {
            $length = 10;
        }

        $search = $request->search['value'] ? $request->search['value'] : '';
        if (isset($request->order)) {
            $order = $request->columns[$request->order[0]['column']]['data'];
        } else {
            $order = in_array($model, ['Category', 'Service', 'Platform']) ? 'sort_order' : 'id';
        }
        if (isset($request->order)) {
            $dir = $request->order[0]['dir'];
        } else {
            $dir = 'asc';
        }

        $Model = 'App\\Models\\' . $model;
        $table = (new $Model)->getTable();
        
        // Safeguard for orderBy: only allow existing columns in the table
        if (!\Schema::hasColumn($table, $order)) {
            $order = 'id';
        }

        switch ($model) {
            case 'Platform':
                $data = $Model::search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.platforms.edit', $item->id);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Category':
                $data = $Model::search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->orderBy('id', 'desc')
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.categories.edit', $item->id);
                    $item->platform = $item->platform;
                    $item->services = $item->services()->orderBy('sort_order', 'asc')->get()->map(function($service) {
                        $service->edit_url = route('admin.services.edit', $service->id);
                        return $service;
                    });
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'ApiProvider':
                $data = $Model::filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->balance_format = number_format($item->balance * $item->exchange_rate, 0, '.', ',') . ' đ';
                    $item->edit_url = route('admin.providers.edit', $item->id);
                    $item->exchange_rate_format = convert_currency($item->exchange_rate, Auth::user()->currency, false);
                    
                    // Statistics
                    $item->count_services = $item->services()->count();
                    $item->count_orders = \App\Models\Order::where('provider', $item->id)->count();
                    $item->count_categories = \App\Models\Category::whereHas('services', function($q) use ($item) {
                        $q->where('provider', $item->id);
                    })->count();

                    // Revenue and Profit
                    $completedOrders = \App\Models\Order::where('provider', $item->id)
                        ->where('status', '!=', 'Canceled'); // Assuming canceled doesn't count
                        
                    // This is approximate, real logic involves complex loop_quantity and original rate check
                    // For list view performance, we might want to keep it simple or cache it. 
                    // But for now let's try to sum it up dynamically.
                    
                    $item->revenue = convert_currency(\App\Models\Order::where('provider', $item->id)->where('status', '!=', 'Canceled')->sum(\DB::raw('total * IFNULL(loop_quantity, 1)')), Auth::user()->currency, false);
                    
                    // Profit is harder without loop, let's just use 0 for now or a simple estimate to avoid N+1 slow query on list
                    // Or we can leave it 0 if it slows down. I will add a placeholder for now.
                    $item->profit = convert_currency(0, Auth::user()->currency, false); 

                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;

            case 'Service':
                if ($request->getHost() == env("MAIN_SITE")) {
                    $data = $Model::with('apiProvider')->where('domain', request()->getHost())->search($search)
                        ->filter($request->all())
                        ->orderBy($order, $dir)
                        ->offset($start)
                        ->limit($length)
                        ->get();

                    $data = $data->map(function ($item) {
                        $rate = $item->rate;
                        $item->edit_url = route('admin.services.edit', $item->id);
                        $item->edit_rate_url = route('admin.services.edit-rate', $item->id);
                        $item->category = $item->category;
                        $item->provider = $item->mode == "option" ? $item->apiProvider : null;
                        $item->rate = convert_currency($rate, Auth::user()->currency, false);
                        $item->rate_silver = convert_currency(load_rate($item, 'silver'), Auth::user()->currency, false);
                        $item->rate_gold = convert_currency(load_rate($item, 'gold'), Auth::user()->currency, false);
                        $item->rate_platinum = convert_currency(load_rate($item, 'platinum'), Auth::user()->currency, false);
                        $item->rate_diamond = convert_currency(load_rate($item, 'diamond'), Auth::user()->currency, false);

                        return $item;
                    });

                    $total = $data->count();
                    $filtered = $Model::where('domain', request()->getHost())->search($search)->filter($request->all())->count();
                } else {
                    $data = $Model::with('apiProvider')->where('domain', request()->getHost())->search($search)
                        ->filter($request->all())
                        ->orderBy($order, $dir)
                        ->offset($start)
                        ->limit($length)
                        ->get();

                    $data = $data->map(function ($item) {
                        $rate = $item->rate;
                        $item->edit_url = route('admin.services.edit', $item->id);
                        $item->category = $item->category;
                        $item->edit_rate_url = route('admin.services.edit-rate', $item->id);

                        $item->category = $item->category;
                        $item->provider = $item->mode == "option" ? $item->apiProvider : null;
                        $item->rate = convert_currency($rate, Auth::user()->currency, false);
                        $item->rate_silver = convert_currency(load_rate($item, 'silver'), Auth::user()->currency, false);
                        $item->rate_gold = convert_currency(load_rate($item, 'gold'), Auth::user()->currency, false);
                        $item->rate_platinum = convert_currency(load_rate($item, 'platinum'), Auth::user()->currency, false);
                        $item->rate_diamond = convert_currency(load_rate($item, 'diamond'), Auth::user()->currency, false);
                        return $item;
                    });

                    $total = $data->count();
                    $filtered = $Model::where('domain', request()->getHost())->search($search)->filter($request->all())->count();
                }
                break;
            case "AccountBank":
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.banks.accounts.edit', $item->id);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case "User":
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.users.edit', $item->id);
                    $item->balance_format = convert_currency($item->balance, Auth::user()->currency, false);
                    $item->total_deposit_format = convert_currency($item->total_deposit, Auth::user()->currency, false);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case "RechargeBonus":
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.banks.bonus.edit', $item->id);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Post':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.posts.edit', $item->id);
                    $item->user = $item->user;
                    return $item;
                });
                $total = $Model::where('domain', request()->getHost())->count();
                $filtered = $Model::where('domain', request()->getHost())->search($search)->filter($request->all())->count();
                break;
            case 'RateUpdate':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy($order, $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->service = $item->service;
                    $item->rate_old_format = convert_currency($item->rate_old, Auth::user()->currency, false);
                    $item->rate_new_format = convert_currency($item->rate_new, Auth::user()->currency, false);
                    return $item;
                });
                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Order':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.data.orders.detail', $item->id);
                    if (env("MAIN_SITE") == request()->getHost() && $item->provider) {
                        $provider = ApiProvider::find($item->provider);
                        if ($provider) {
                            $item->provider = $provider;
                        }
                    }
                    if ($item->service) {
                        $item->service = $item->service;
                    }
                    if ($item->user) {
                        $item->user = $item->user;
                    }

                    $item->rate_format = convert_currency($item->rate, Auth::user()->currency, false);
                    $item->rate_usd = (convert_currency($item->rate, 'USD', false));
                    $item->total_usd = (convert_currency($item->total * ($item->loop_quantity ?? 1), 'USD', false));
                    $item->total_format = convert_currency($item->total * ($item->loop_quantity ?? 1), Auth::user()->currency, false);
                    // lợi nhuận
                    $qty = $item->quantity * ($item->loop_quantity ?? 1);
                    // $rate_original = load_rate($item->service->rate_original, $item->user->level);
                    $rate_original = $item->service->rate_original;
                    $cost = ($rate_original * $qty) / 1000; // Chi phí gốc
                    $revenue = $item->payment * ($item->loop_quantity ?? 1); // Doanh thu (nếu payment là tổng)
                    $profit = $revenue - $cost; // Lợi nhuận
                    $item->profit = convert_currency($profit, Auth::user()->currency, false);

                    // id main
                    $item->main_id = Order::where('id', $item->root_id)->orWhere('root_id', $item->root_id)->where('domain', s('domain_main'))->orderBy('id', 'desc')->first()?->id ?? null;

                    return $item;
                });
                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();

                // Calculate summary statistics
                $query_stats = $Model::where('orders.domain', request()->getHost())
                    ->search($search)
                    ->filter($request->all());

                $total_revenue = $query_stats->sum(\DB::raw('orders.total * IFNULL(orders.loop_quantity, 1)'));

                $total_profit = $Model::where('orders.domain', request()->getHost())
                    ->search($search)
                    ->filter($request->all())
                    ->where('orders.status', '!=', 'Canceled')
                    ->join('services', 'orders.service_id', '=', 'services.id')
                    ->selectRaw('SUM((orders.payment * IFNULL(orders.loop_quantity, 1)) - (services.rate_original * orders.quantity * IFNULL(orders.loop_quantity, 1) / 1000)) as total_profit')
                    ->first()->total_profit ?? 0;

                $meta = [
                    'total_orders' => number_format($filtered),
                    'total_revenue' => convert_currency($total_revenue, Auth::user()->currency, false),
                    'total_profit' => convert_currency($total_profit, Auth::user()->currency, false),
                ];

                break;
            case 'ChildPanel':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();
                $data = $data->map(function ($item) {
                    $item->user = $item->user;
                    return $item;
                });
                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Recharge':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->user = $item->user;
                    $item->amount_format = convert_currency($item->amount, Auth::user()->currency, false);
                    $item->real_amount_format = convert_currency($item->real_amount, Auth::user()->currency, false);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Transaction':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->user = $item->user;
                    $item->amount_format = convert_currency($item->amount, Auth::user()->currency, false);
                    $item->balance_before_format = convert_currency($item->balance_before, Auth::user()->currency, false);
                    $item->balance_after_format = convert_currency($item->balance_after, Auth::user()->currency, false);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'ProductCategory':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.products.categories.edit', $item->id);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Product':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->edit_url = route('admin.products.edit', $item->id);
                    $item->stock_url = route('admin.products.stock', $item->id);
                    $item->stock_count = $item->stocks()->where('status', 'available')->count();
                    $item->price_format = convert_currency($item->price, Auth::user()->currency, false);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'ProductStock':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->product = $item->product;
                    $item->user = $item->buyer ? $item->buyer : null;
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'OrderProduct':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();

                $data = $data->map(function ($item) {
                    $item->product = $item->product;
                    $item->user = $item->user;
                    $item->price_format = convert_currency($item->price, Auth::user()->currency, false);
                    $item->total_format = convert_currency($item->total, Auth::user()->currency, false);
                    return $item;
                });

                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            case 'Ticket':
                $data = $Model::where('domain', request()->getHost())->search($search)
                    ->filter($request->all())
                    ->orderBy("id", $dir)
                    ->offset($start)
                    ->limit($length)
                    ->get();
                $data = $data->map(function ($item) {
                    $item->user = $item->user;
                    $item->edit_url = route('admin.tickets.edit', $item->id);
                    $item->detail_url = route('admin.tickets.detail', $item->id);
                    return $item;
                });
                $total = $Model::count();
                $filtered = $Model::search($search)->filter($request->all())->count();
                break;
            default:
                break;
        }

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
            'meta' => $meta ?? null
        ]);
    }

    public function updateField(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $request->validate([
            'id' => 'required',
            'model' => 'required',
            'field' => 'required',
            'value' => 'required'
        ]);

        $Model = 'App\\Models\\' . $request->model;
        $data = $Model::find($request->id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu!'
            ]);
        }

        try {
            $data->{$request->field} = $request->value;
            $data->save();

            // Clear cache if Platform or Category is updated
            if (in_array($request->model, ['Platform', 'Category', 'Service'])) {
                \Artisan::call('cache:clear');
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function sort(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $request->validate([
            'model' => 'required',
            'data' => 'required|array'
        ]);

        $Model = 'App\\Models\\' . $request->model;

        try {
            foreach ($request->data as $index => $item) {
                // Support both {id, sort_order} and just id
                $id = is_array($item) ? $item['id'] : $item;
                $sort_order = is_array($item) ? ($item['sort_order'] ?? $index) : $index;

                $data = $Model::find($id);
                if ($data) {
                    $data->sort_order = $sort_order;
                    $data->save();
                }
            }

            // Clear cache if relevant
            if (in_array($request->model, ['Platform', 'Category', 'Service'])) {
                \Artisan::call('cache:clear');
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật thứ tự sắp xếp!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $ids = $request->ids; // array of ids
        $model = $request->model;
        $type = $request->type ?? 'ids';

        $Model = 'App\\Models\\' . $model;

        if ($type == 'all') {
            $domain = request()->getHost();
            // Check if model has domain field
            $instance = new $Model;
            $query = $Model::query();
            
            // Check if domain column exists in the table (basic check)
            if (\Schema::hasColumn($instance->getTable(), 'domain')) {
                $query->where('domain', $domain);
            }

            $count = $query->count();
            if ($count > 0) {
                // To trigger model events (like file unlinking if needed, though usually not for bulk)
                // We'll use chunking to avoid memory issues and still trigger events
                $query->chunkById(100, function($items) {
                    foreach($items as $item) {
                        if ($item->icon || $item->image) {
                            try {
                                $path = $item->icon ? $item->icon : $item->image;
                                if (file_exists(public_path($path))) {
                                    unlink(public_path($path));
                                }
                            } catch (\Exception $e) {}
                        }
                        $item->delete();
                    }
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Xóa tất cả thành công!'
                ]);
            }
        } else {
            $data = $Model::whereIn('id', $ids)->get();
            if ($data->count() > 0) {
                foreach ($data as $item) {
                    if ($item->icon || $item->image) {
                        try {
                            $path = $item->icon ? $item->icon : $item->image;
                            if (file_exists(public_path($path))) {
                                unlink(public_path($path));
                            }
                        } catch (\Exception $e) {}
                    }

                    $item->delete();
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa thành công!'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy dữ liệu!'
        ]);
    }

    public function removeCache(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        Artisan::call('cache:clear');

        return response()->json([
            'success' => true,
            'message' => 'Xóa cache thành công!'
        ]);
    }
}
