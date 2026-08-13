<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Recharge;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Ticket;
use App\Models\ChildPanel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LicenseService;

class AppAdminController extends Controller
{
    public function index(Request $request)
    {
        $revenueStatuses = ['Completed', 'Processing', 'In Progress', 'In progress', 'Partial', 'Active'];

        $statics = [];

        // User Statis
        $statics['users'] = [
            'total' => User::where("domain", request()->getHost())->count(),
            'today' => User::where("domain", request()->getHost())->whereDate('created_at', now())->count(),
            'week' => User::where("domain", request()->getHost())->whereDate('created_at', '>=', now()->startOfWeek())->count(),
            'month' => User::where("domain", request()->getHost())->whereMonth('created_at', now()->month)->count(),
            'balance' => User::where("domain", request()->getHost())->sum('balance'),
            'total_deposit' => Recharge::where("domain", request()->getHost())->where('status', 'completed')->sum('real_amount'),
            'total_deposit_today' => Recharge::where("domain", request()->getHost())->where('status', 'completed')->whereDate('created_at', now())->sum('real_amount'),
            'total_deposit_week' => Recharge::where("domain", request()->getHost())->where('status', 'completed')->whereDate('created_at', '>=', now()->startOfWeek())->sum('real_amount'),
            'total_deposit_month' => Recharge::where("domain", request()->getHost())->where('status', 'completed')->whereMonth('created_at', now()->month)->sum('real_amount'),
        ];

        // Orders static
        $statics['orders'] = [
            'total' => Order::where("domain", request()->getHost())->count(),
            'total_payment' => Order::where("domain", request()->getHost())->sum('total'),
            'total_month' => Order::where("domain", request()->getHost())->whereMonth('created_at', now()->month)->count(),
            'total_week' => Order::where("domain", request()->getHost())->whereDate('created_at', '>=', now()->startOfWeek())->count(),
            'total_today' => Order::where("domain", request()->getHost())->whereDate('created_at', now())->count(),
            
            // Status counts
            'pending' => Order::where("domain", request()->getHost())->where('status', 'Pending')->count(),
            'processing' => Order::where("domain", request()->getHost())->where('status', 'Processing')->count(),
            'inprogress' => Order::where("domain", request()->getHost())->where('status', 'In Progress')->count(),
            'completed' => Order::where("domain", request()->getHost())->where('status', 'Completed')->count(),
            'partial' => Order::where("domain", request()->getHost())->where('status', 'Partial')->count(),
            'canceled' => Order::where("domain", request()->getHost())->where('status', 'Canceled')->count(),
        ];

        // System statics
        $statics['system'] = [
            'tickets_pending' => Ticket::where("domain", request()->getHost())->where('status', 'Pending')->count(),
            'child_panels_pending' => ChildPanel::where("domain", request()->getHost())->where('status', 'Pending')->count(),
            'withdrawals_pending' => 0, // Placeholder
        ];

        // profit orders
        // Optimized: Eager load service to avoid N+1
        $ods = Order::where("domain", request()->getHost())
            ->whereIn('status', $revenueStatuses)
            ->with('service') 
            ->get();
        $totalProfit = 0;
        $totalProfitToday = 0;
        $totalProfitWeek = 0;
        $totalProfitMonth = 0;
        foreach ($ods as $od) {
            $qty = $od->quantity;
            $payment = $od->total;
            $rate_original = $od->service?->rate_original ?? 0;

            $cost = ($rate_original * $qty) / 1000;
            $profit = $payment - $cost;

            // tính toán lợi nhuận
            $createdAt = Carbon::parse($od->created_at);
            $time_today = $createdAt->isToday();
            $time_week = $createdAt->isAfter(now()->startOfWeek()->subSecond());
            $time_month = $createdAt->isCurrentMonth();

            if ($time_today) {
                $totalProfitToday += $profit;
            }
            if ($time_week) {
                $totalProfitWeek += $profit;
            }
            if ($time_month) {
                $totalProfitMonth += $profit;
            }
            $totalProfit += $profit;
        }

        //order revenue/profit static
        $statics['revenue_profit'] = [
            'total_revenue' => Order::where("domain", request()->getHost())->sum('total'),
            'total_profit' => $totalProfit,
            'total_revenue_month' => Order::where("domain", request()->getHost())->whereMonth('created_at', now()->month)->sum('total'),
            'total_profit_month' => $totalProfitMonth,
            'total_revenue_week' => Order::where("domain", request()->getHost())->whereDate('created_at', '>=', now()->startOfWeek())->sum('total'),
            'total_profit_week' => $totalProfitWeek,
            'total_revenue_today' => Order::where("domain", request()->getHost())->whereDate('created_at', now())->sum('total'),
            'total_profit_today' => $totalProfitToday,
        ];

        //orders pie chart
        $chartPieLabels = ["Pending", "Processing", "Active", "In Progress", "Completed", "Partial", "Canceled"];
        $chartPieColors = ["#FF6384", "#36A2EB", "#FFCE56", "#FF6384", "#36A2EB", "#FFCE56", "#FF6384"];
        $chartPieData = [
            Order::where("domain", request()->getHost())->where('status', 'Pending')->count(),
            Order::where("domain", request()->getHost())->where('status', 'Processing')->count(),
            Order::where("domain", request()->getHost())->where('status', 'Active')->count(),
            Order::where("domain", request()->getHost())->where('status', 'In Progress')->count(),
            Order::where("domain", request()->getHost())->where('status', 'Completed')->count(),
            Order::where("domain", request()->getHost())->where('status', 'Partial')->count(),
            Order::where("domain", request()->getHost())->where('status', 'Canceled')->count(),
        ];
        $chartPieData = [];
        foreach ($chartPieLabels as $label) {
            $chartPieData[] = Order::where("domain", request()->getHost())->where('status', $label)->count();
            // $chartPieData[] = rand(10, 1000);
        }

        // orders line chart
        // text chart month
        $daysInMonth = now()->daysInMonth;
        $chartDays = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $chartDays[] = now()->startOfMonth()->addDays($i - 1)->format('d/m/Y');
        }

        $chartOrders = [
            ["name" => "Pending", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "Processing", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "Active", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "In Progress", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "Completed", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "Partial", "data" => array_fill(0, $daysInMonth, 0)],
            ["name" => "Canceled", "data" => array_fill(0, $daysInMonth, 0)]
        ];

        
        $chartOrdersColors = ["#FF6384", "#36A2EB", "#FFCE56", "#FF6384", "#36A2EB", "#FFCE56", "#FF6384"];

        // Optimized: Single Query Group By Date & Status
        $ordersByDateRaw = Order::selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->where("domain", request()->getHost())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('date', 'status')
            ->get();

        // Process raw data into chart structure
        foreach ($ordersByDateRaw as $row) {
            $day = (int)date('d', strtotime($row->date)); // 1-31
            $index = $day - 1; // 0-30
            // Find correct chart series by name
            foreach ($chartOrders as $key => $series) {
                if ($series['name'] == $row->status) {
                     $chartOrders[$key]['data'][$index] = $row->count;
                     break;
                }
            }
        }

        // revenue/profit line chart
        // Filter Logic
        $filter = request()->input('date_filter', 'month');
        $chartDays = [];
        
        // Initialize Chart Data
        $chartRevenueProfit = [
            ["name" => "Doanh thu", "data" => []],
            ["name" => "Lợi nhuận", "data" => []]
        ];
        $chartRevenueProfitColors = ["#FF6384", "#36A2EB"];

        $chartDeposits = [
            ["name" => "Tổng nạp", "data" => []]
        ];
        $chartDepositsColors = ["#007bff"];

        // Pre-fetch Data Logic to avoid query in loop
        if ($filter == 'week') {
             // Generate X-Axis Labels
             for ($i = 6; $i >= 0; $i--) {
                $chartDays[] = now()->subDays($i)->format('d/m');
            }

            // Query Revenue (Completed Orders only for revenue usually? Code used Completed)
            $revenues = Order::selectRaw('DATE(created_at) as date, SUM(total) as total_revenue')
                ->where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('date')
                ->get()
                ->keyBy('date');
            
            // Query Profit - Hard to do purely SQL if cost is in related table. 
            // We fetch orders with services for the week eagerly loaded.
            $profitOrders = Order::where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->with('service')
                ->get()
                ->groupBy(function($item) {
                     return $item->created_at->format('Y-m-d');
                });
            
            // Query Deposits
            $deposits = Recharge::selectRaw('DATE(created_at) as date, SUM(real_amount) as total_deposit')
                ->where("domain", request()->getHost())
                ->where('status', 'Completed')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            // Build Arrays
            for ($i = 6; $i >= 0; $i--) {
                $dateKey = now()->subDays($i)->format('Y-m-d');
                
                // Revenue
                $rev = $revenues[$dateKey]->total_revenue ?? 0;
                $chartRevenueProfit[0]['data'][] = round($rev, 2);

                // Profit (In-memory calc)
                $dayProfit = 0;
                if (isset($profitOrders[$dateKey])) {
                    foreach ($profitOrders[$dateKey] as $od) {
                        $cost = ($od->service->rate_original * $od->quantity) / 1000;
                        $dayProfit += ($od->total - $cost);
                    }
                }
                $chartRevenueProfit[1]['data'][] = round($dayProfit, 2);

                // Deposit
                $dep = $deposits[$dateKey]->total_deposit ?? 0;
                $chartDeposits[0]['data'][] = round($dep, 2);
            }

        } elseif ($filter == 'year') {
             // Labels
             for ($i = 1; $i <= 12; $i++) {
                $chartDays[] = "Tháng $i";
            }
             // Query Month Stats
             $revenues = Order::selectRaw('MONTH(created_at) as month, SUM(total) as total_revenue')
                ->where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->get()
                ->keyBy('month');
             
             // Profit Orders
             $profitOrders = Order::where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereYear('created_at', now()->year)
                ->with('service')
                ->get()
                ->groupBy(function($item) {
                     return $item->created_at->month; // 1-12
                });

             $deposits = Recharge::selectRaw('MONTH(created_at) as month, SUM(real_amount) as total_deposit')
                ->where("domain", request()->getHost())
                ->where('status', 'Completed')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->get()
                ->keyBy('month');

             for ($i = 1; $i <= 12; $i++) {
                $chartRevenueProfit[0]['data'][] = round($revenues[$i]->total_revenue ?? 0, 2);
                
                $monthProfit = 0;
                if (isset($profitOrders[$i])) {
                    foreach ($profitOrders[$i] as $od) {
                         $cost = ($od->service->rate_original * $od->quantity) / 1000;
                         $monthProfit += ($od->total - $cost);
                    }
                }
                $chartRevenueProfit[1]['data'][] = round($monthProfit, 2);
                $chartDeposits[0]['data'][] = round($deposits[$i]->total_deposit ?? 0, 2);
             }

        } else { // Month (Default)
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartDays[] = $i;
            }
            
            // Query Single Month Daily Stats
             $revenues = Order::selectRaw('DAY(created_at) as day, SUM(total) as total_revenue')
                ->where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->groupBy('day')
                ->get()
                ->keyBy('day');

             $profitOrders = Order::where("domain", request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->with('service')
                ->get()
                ->groupBy(function($item) {
                     return $item->created_at->day; // 1-31
                });

             $deposits = Recharge::selectRaw('DAY(created_at) as day, SUM(real_amount) as total_deposit')
                ->where("domain", request()->getHost())
                ->where('status', 'Completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->groupBy('day')
                ->get()
                ->keyBy('day');
            
             for ($i = 1; $i <= $daysInMonth; $i++) {
                 // Revenue
                 $rev = $revenues[$i]->total_revenue ?? 0;
                 $chartRevenueProfit[0]['data'][] = round($rev, 2);
                 
                 // Profit
                 $dayProfit = 0;
                 if (isset($profitOrders[$i])) {
                     foreach ($profitOrders[$i] as $od) {
                          $cost = ($od->service->rate_original * $od->quantity) / 1000;
                          $dayProfit += ($od->total - $cost);
                     }
                 }
                 $chartRevenueProfit[1]['data'][] = round($dayProfit, 2);
                 
                 // Deposit
                 $dep = $deposits[$i]->total_deposit ?? 0;
                 $chartDeposits[0]['data'][] = round($dep, 2);
             }
        }


        // Recent Orders
        $recentOrders = Order::where("domain", request()->getHost())
            ->with(['user', 'service'])
            ->latest()
            ->take(10)
            ->get();

        // Recent Deposits
        $recentDeposits = Recharge::where("domain", request()->getHost())
            ->with(['user'])
            ->where('status', '!=', 'pending') // Optional: show only completed/processed? Or all? Let's show all for now or maybe just completed
            ->latest()
            ->take(10)
            ->get();

        
        // 1. Payment Methods Stats
        $paymentStatsRaw = Recharge::selectRaw('method, SUM(real_amount) as total')
            ->where('domain', request()->getHost())
            ->where('status', 'Completed')
            ->groupBy('method')
            ->get();
            
        $paymentMethodsStats = [
            'labels' => [],
            'series' => [],
            'details' => []
        ];
        $totalPayment = $statics['users']['total_deposit'] ?? 0;
        
        $colors = ['#6366F1', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'];
        $cIndex = 0;
        
        foreach ($paymentStatsRaw as $pStat) {
            $methodName = $pStat->method ?? 'Khác';
            $amount = $pStat->total;
            $percent = $totalPayment > 0 ? round(($amount / $totalPayment) * 100, 1) : 0;
            
            $paymentMethodsStats['labels'][] = $methodName;
            $paymentMethodsStats['series'][] = $amount; // ApexCharts will calc percentages if series are numbers
            $paymentMethodsStats['details'][] = [
                'name' => $methodName,
                'amount' => $amount,
                'percent' => $percent,
                'color' => $colors[$cIndex % count($colors)]
            ];
            $cIndex++;
        }

        // 2. Revenue Table Stats (Last 7 days)
        $revenueTableStats = [];
        
        for ($i = 0; $i < 7; $i++) {
            $d = now()->subDays($i)->format('Y-m-d');
            $dDisplay = now()->subDays($i)->format('d/m/Y');
            
            // Doanh thu tính từ các đơn đã thanh toán (không tính Canceled/Pending nếu chưa trừ tiền, nhưng thường SMM là trừ tiền luôn)
            // Ở đây ta tính các đơn có trạng thái đang chạy hoặc đã xong
            $rev = Order::where('domain', request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereDate('created_at', $d)
                ->sum('total');
                
            // Đếm TẤT CẢ đơn hàng trong ngày
            $ordersCount = Order::where('domain', request()->getHost())
                ->whereDate('created_at', $d)
                ->count();
                
            $newUsers = User::where('domain', request()->getHost())
                ->whereDate('created_at', $d)
                ->count();
            
            // Lợi nhuận
            $dayOrders = Order::where('domain', request()->getHost())
                ->whereIn('status', $revenueStatuses)
                ->whereDate('created_at', $d)
                ->with('service')
                ->get();
                
            $profit = 0;
            foreach ($dayOrders as $od) {
                $cost = ($od->service?->rate_original * $od->quantity) / 1000;
                $profit += ($od->total - $cost);
            }
            
            $revenueTableStats[] = [
                'date' => $dDisplay,
                'revenue' => $rev,
                'profit' => $profit,
                'orders' => $ordersCount,
                'new_users' => $newUsers
            ];
        }

        // 3. Top Services Stats (Top 5)
        $topServicesQuery = Order::where('domain', request()->getHost())
            ->whereIn('status', $revenueStatuses)
            ->with(['service'])
            ->get();
            
        $topServicesStats = $topServicesQuery->groupBy('service_id')->map(function ($row) {
            $revenue = $row->sum('total');
            return [
                'name' => $row->first()->service ? $row->first()->service->name : 'Unknown Service',
                'revenue' => $revenue
            ];
        })->sortByDesc('revenue')->take(5)->values()->toArray();
        
        $topServicesTotal = array_sum(array_column($topServicesStats, 'revenue'));
        foreach ($topServicesStats as &$ts) {
            $ts['percent'] = $topServicesTotal > 0 ? round(($ts['revenue'] / $topServicesTotal) * 100, 1) : 0;
        }
        
        // Next.js admin dùng chính dữ liệu của dashboard Blade để không lệch công thức.
        if ($request->expectsJson()) {
            abort_unless($request->user()?->role === 'admin', 403);
            $leaderboardData = $this->leaderboard()->getData(true)['data'] ?? [];
            $providerData = $this->providerStatistic()->getData(true)['data'] ?? [];
            $daily = [];
            foreach ($chartDays as $index => $date) {
                $daily[] = [
                    'date' => $date,
                    'revenue' => (float) ($chartRevenueProfit[0]['data'][$index] ?? 0),
                    'profit' => (float) ($chartRevenueProfit[1]['data'][$index] ?? 0),
                    'orders' => 0,
                    'new_users' => 0,
                ];
            }
            foreach ($revenueTableStats as $row) {
                foreach ($daily as &$day) {
                    if (str_starts_with($row['date'], $day['date'])) {
                        $day['orders'] = $row['orders'];
                        $day['new_users'] = $row['new_users'];
                    }
                }
                unset($day);
            }
            $domain = request()->getHost();
            $customStart = $request->input('start_date');
            $customEnd = $request->input('end_date');
            $applyPeriod = function ($query, string $period, string $column = 'created_at') use ($customStart, $customEnd) {
                if ($period === 'today') $query->whereDate($column, now());
                if ($period === 'yesterday') $query->whereDate($column, now()->subDay());
                if ($period === 'week') $query->whereDate($column, '>=', now()->startOfWeek());
                if ($period === 'month') $query->whereYear($column, now()->year)->whereMonth($column, now()->month);
                if ($period === 'year') $query->whereYear($column, now()->year);
                if ($period === 'custom' && is_string($customStart) && is_string($customEnd)
                    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $customStart)
                    && preg_match('/^\d{4}-\d{2}-\d{2}$/', $customEnd)) {
                    $query->whereBetween($column, [$customStart . ' 00:00:00', $customEnd . ' 23:59:59']);
                }
                return $query;
            };
            $periodData = [];
            foreach (['all', 'custom', 'year', 'month', 'week', 'yesterday', 'today'] as $periodKey) {
                $userQuery = User::where('domain', $domain);
                $orderQuery = Order::where('domain', $domain);
                $profitQuery = Order::where('domain', $domain)->whereIn('status', $revenueStatuses)->with('service');
                $depositQuery = Recharge::where('domain', $domain)->where('status', 'completed');
                $pendingQuery = Order::where('domain', $domain)->whereIn('status', ['Pending', 'Processing', 'Active', 'In Progress', 'In progress']);
                $applyPeriod($userQuery, $periodKey);
                $applyPeriod($orderQuery, $periodKey);
                $applyPeriod($profitQuery, $periodKey);
                $applyPeriod($depositQuery, $periodKey);
                $applyPeriod($pendingQuery, $periodKey);
                $profit = (float) $profitQuery->get()->sum(function ($order) {
                    $cost = ((float) ($order->service?->rate_original ?? $order->rate ?? 0) * (int) $order->quantity) / 1000;
                    return (float) $order->total - $cost;
                });
                $periodData[$periodKey] = [
                    'users' => $userQuery->count(),
                    'orders' => $orderQuery->count(),
                    'revenue' => (float) $orderQuery->sum('total'),
                    'profit' => $profit,
                    'deposits' => (float) $depositQuery->sum('real_amount'),
                    'pending_orders' => $pendingQuery->count(),
                ];
            }
            $selectedPeriod = in_array($request->input('period'), ['all', 'custom', 'year', 'month', 'week', 'yesterday', 'today'], true)
                ? $request->input('period')
                : 'month';
            $filteredTopServices = Order::where('orders.domain', $domain)->whereIn('orders.status', $revenueStatuses)
                ->leftJoin('services', 'services.id', '=', 'orders.service_id');
            $applyPeriod($filteredTopServices, $selectedPeriod, 'orders.created_at');
            $filteredTopServices = $filteredTopServices
                ->selectRaw('orders.service_id, COALESCE(services.name, ?) as name, SUM(orders.total) as revenue, COUNT(*) as orders_count', ['Unknown'])
                ->groupBy('orders.service_id', 'services.name')->orderByDesc('revenue')->limit(50)->get();
            $filteredTopTotal = max(1, (float) $filteredTopServices->sum('revenue'));
            $filteredTopServices->each(fn ($item) => $item->percent = round(((float) $item->revenue / $filteredTopTotal) * 100, 1));

            $filteredProviders = Order::where('orders.domain', $domain)->whereIn('orders.status', $revenueStatuses)
                ->leftJoin('api_providers', 'api_providers.id', '=', 'orders.provider')
                ->leftJoin('services', 'services.id', '=', 'orders.service_id');
            $applyPeriod($filteredProviders, $selectedPeriod, 'orders.created_at');
            $filteredProviders = $filteredProviders
                ->selectRaw('orders.provider as provider_id, COALESCE(api_providers.name, ?) as name, SUM(orders.total) as revenue, SUM((COALESCE(services.rate_original, orders.rate) * orders.quantity) / 1000) as cost, COUNT(*) as orders_count', ['Tools'])
                ->groupBy('orders.provider', 'api_providers.name')->orderByDesc('revenue')->limit(50)->get();
            $filteredProviders->each(function ($provider) {
                $provider->profit = (float) $provider->revenue - (float) $provider->cost;
                $provider->margin = (float) $provider->revenue > 0 ? round((float) $provider->profit / (float) $provider->revenue * 100, 2) : 0;
            });

            $filteredLeaderboard = Order::where('orders.domain', $domain)->whereIn('orders.status', $revenueStatuses)
                ->leftJoin('users', 'users.id', '=', 'orders.user_id');
            $applyPeriod($filteredLeaderboard, $selectedPeriod, 'orders.created_at');
            $filteredLeaderboard = $filteredLeaderboard
                ->selectRaw('orders.user_id, users.username, users.email, SUM(orders.total) as spending, COUNT(*) as orders_count')
                ->groupBy('orders.user_id', 'users.username', 'users.email')->orderByDesc('spending')->limit(50)->get();
            return response()->json(['status' => true, 'data' => [
                'summary' => [
                    'deposits' => (float) ($statics['users']['total_deposit'] ?? 0),
                    'balance' => (float) ($statics['users']['balance'] ?? 0),
                    'deposits_today' => (float) ($statics['users']['total_deposit_today'] ?? 0),
                    'deposits_month' => (float) ($statics['users']['total_deposit_month'] ?? 0),
                    'order_payments' => (float) ($statics['orders']['total_payment'] ?? 0),
                    'orders_pending' => (int) Order::where('domain', request()->getHost())
                        ->whereIn('status', ['Pending', 'Processing', 'Active', 'In Progress', 'In progress'])
                        ->count(),
                    'tickets_pending' => (int) ($statics['system']['tickets_pending'] ?? 0),
                    'websites_pending' => (int) ($statics['system']['child_panels_pending'] ?? 0),
                ],
                'periods' => $periodData,
                'daily' => $daily,
                'revenue_table' => collect($revenueTableStats)->map(fn ($row) => [
                    'date' => $row['date'],
                    'revenue' => (float) $row['revenue'],
                    'profit' => (float) $row['profit'],
                    'orders' => (int) $row['orders'],
                    'new_users' => (int) $row['new_users'],
                ])->values(),
                'payment_methods' => collect($paymentMethodsStats['details'])->map(fn ($item) => ['method' => $item['name'], 'amount' => (float) $item['amount']])->values(),
                'top_services' => $filteredTopServices,
                'order_statuses' => collect(['Pending' => 'pending', 'Processing' => 'processing', 'In progress' => 'inprogress', 'Completed' => 'completed', 'Partial' => 'partial', 'Canceled' => 'canceled'])->mapWithKeys(fn ($key, $label) => [$label => (int) ($statics['orders'][$key] ?? 0)]),
                'recent_orders' => $recentOrders,
                'recent_deposits' => $recentDeposits,
                'leaderboard' => $filteredLeaderboard,
                'providers' => $filteredProviders,
            ]]);
        }

        // Pass to view

        return view('admin.dashboard', [
            'paymentMethodsStats' => $paymentMethodsStats,
            'revenueTableStats' => $revenueTableStats,
            'topServicesStats' => $topServicesStats,
            'statics' => $statics,
            'recentOrders' => $recentOrders,
            'recentDeposits' => $recentDeposits,
            'chartPieLabels' => $chartPieLabels,
            'chartPieColors' => $chartPieColors,
            'chartPieData' => $chartPieData,
            'chartDays' => $chartDays,
            'chartOrdersColors' => $chartOrdersColors,
            'chartOrdersLabels' => $chartOrders,
            'chartOrdersData' => $chartOrders,
            'chartRevenueProfitColors' => $chartRevenueProfitColors,
            'chartRevenueProfitLabels' => $chartRevenueProfit,
            'chartDeposits' => $chartDeposits,
            'chartDepositsColors' => $chartDepositsColors,
            'currentVersion' => \App\VersionHelper::get(),
        ]);
    }
    public function providerStatistic()
    {
        try {
            // Count total for Domain
            $totalForDomain = Order::where('domain', request()->getHost())->count();

            $query = Order::where('domain', request()->getHost());
                
            // Debug info
            $totalFound = $query->count();
                
            $orders = $query->with(['service', 'provider'])->get();

            // Use closure to group by attribute 'provider' (ID) to avoid conflict with relation 'provider'
            $stats = $orders->groupBy(function($item) {
                return $item->getAttribute('provider');
            })->map(function ($row) use ($orders) {
                // $row is a collection of orders for this provider
                $revenue = $row->sum('total');
                
                // Calculate Cost and Profit
                $cost = $row->reduce(function ($carry, $order) {
                    $rate_original = $order->service?->rate_original ?? 0;
                    // Cost formula: (rate_original * quantity) / 1000
                    $orderCost = ($rate_original * $order->quantity) / 1000;
                    return $carry + $orderCost;
                }, 0);

                $profit = $revenue - $cost;
                $count = $row->count();
                
                // Calculate Profit Rate (Margin)
                $profit_rate = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;

                // Get Provider Name
                 $providerName = 'Tools';
                 $providerId = $row->first()->getAttribute('provider');
                 if ($providerId) { 
                     $provider = \App\Models\ApiProvider::find($providerId);
                     if ($provider) {
                         $providerName = $provider->name;
                     }
                 }

                return [
                    'name' => $providerName,
                    'count' => $count,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'profit_rate' => $profit_rate
                ];
            })->sortByDesc('revenue')->values();

            return response()->json([
                'status' => 'success',
                'data' => $stats,
                'debug_total_domain' => $totalForDomain,
                'debug_total_filtered' => $totalFound,
                'debug_host' => request()->getHost()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' at ' . $e->getLine()
            ]);
        }
    }
    public function topServices()
    {
        try {
            $query = Order::where('domain', request()->getHost());
            
            $debugTotal = $query->count();
            $orders = $query->with(['service'])->get();

            $stats = $orders->groupBy('service_id')->map(function ($row) {
                 // $row is collection of orders for one service
                 $service = $row->first()->service;
                 $serviceName = $service ? $service->name : 'Unknown Service (' . $row->first()->service_id . ')';
                 $serviceId = $row->first()->service_id;
                 
                 $revenue = $row->sum('total');
                 // Calculate Cost and Profit
                 $cost = $row->reduce(function ($carry, $order) {
                    // Determine rate_original. In case service was deleted, use safe navigation
                    $rate_original = $order->service?->rate_original ?? 0;
                    // Fix: Order Quantity vs Rate. Usually rate is per 1000.
                    $val = ($rate_original * $order->quantity) / 1000;
                    return $carry + $val;
                 }, 0);
                 
                 $profit = $revenue - $cost;
                 $count = $row->count();
                 $avgPrice = $count > 0 ? $revenue / $count : 0;
                 
                 return [
                     'id' => $serviceId,
                     'name' => $serviceName,
                     'revenue' => $revenue,
                     'cost' => $cost,
                     'profit' => $profit,
                     'count' => $count,
                     'avg_price' => $avgPrice
                 ];
            })->sortByDesc('revenue')->take(50)->values();

            return response()->json([
                'status' => 'success',
                'data' => $stats,
                'debug_count' => $debugTotal
            ]);
        } catch (\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function leaderboard()
    {
        try {
            $query = Order::where('domain', request()->getHost());
            
            $debugCount = $query->count();
            $orders = $query->with(['user'])->get(); // Assuming Order belongsTo User
                
            $stats = $orders->groupBy('user_id')->map(function ($row) {
                $user = $row->first()->user; // User model
                $username = $user ? $user->username : 'Unknown';
                $email = $user ? $user->email : 'N/A';
                $userId = $row->first()->user_id;
                
                $totalSpend = $row->sum('total');
                $count = $row->count();
                
                return [
                    'id' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'total_spend' => $totalSpend,
                    'count' => $count
                ];
            })->sortByDesc('total_spend')->take(50)->values();

            return response()->json([
                'status' => 'success',
                'data' => $stats,
                'debug_count' => $debugCount
            ]);
        } catch (\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function aiChat(Request $request) 
    {
        $message = strtolower(trim($request->input('message')));
        $response = "";
        
        try {
            // 1. Raw SQL functionality
            if (str_starts_with($message, 'sql:')) {
                $query = trim(substr($request->input('message'), 4));
                $isSelect = stripos($query, 'select') === 0 || stripos($query, 'show') === 0;
                
                if ($isSelect) {
                     $results = \DB::select($query);
                     $response = $this->formatTableResponse($results, "Kết quả SQL");
                } else {
                    $affected = \DB::statement($query);
                    $response = "Đã thực thi thành công. Số dòng bị ảnh hưởng: $affected";
                }
            } 
            // 2. Natural Language Mapping (Manual NLP)
            else {
                // Users / Members
                if (str_contains($message, 'thành viên') || str_contains($message, 'người dùng') || str_contains($message, 'users') || str_contains($message, 'khách hàng')) {
                    if (str_contains($message, 'tổng') || str_contains($message, 'bao nhiêu') || str_contains($message, 'count')) {
                        $count = \App\Models\User::count();
                        $newToday = \App\Models\User::whereDate('created_at', now())->count();
                        $response = "Tổng số thành viên: <b>" . number_format($count) . "</b><br>";
                        $response .= "Đăng ký hôm nay: <b>" . number_format($newToday) . "</b>";
                    } elseif (str_contains($message, 'mới') || str_contains($message, 'new')) {
                        $users = \App\Models\User::latest()->take(5)->get();
                        $response = "Danh sách 5 thành viên mới nhất:<br>";
                        $response .= $this->formatTableResponse($users->toArray());
                    } else {
                         // Default to listing top users if just "users" asked
                         $users = \App\Models\User::take(5)->get();
                         $response = "Hiển thị 5 thành viên (gõ 'tổng thành viên' để xem số lượng):<br>" . $this->formatTableResponse($users->toArray());
                    }
                }
                // Orders
                elseif (str_contains($message, 'đơn hàng') || str_contains($message, 'orders')) {
                     if (str_contains($message, 'thất bại') || str_contains($message, 'lỗi') || str_contains($message, 'failed')) {
                         $count = Order::where('status', 'Failed')->count();
                         $response = "Tổng đơn hàng thất bại: <b>$count</b>";
                     } else {
                         $count = Order::where('domain', request()->getHost())->count();
                         $today = Order::where('domain', request()->getHost())->whereDate('created_at', now())->count();
                         $response = "Tổng số đơn hàng: <b>" . number_format($count) . "</b><br>";
                         $response .= "Đơn hàng hôm nay: <b>" . number_format($today) . "</b>";
                     }
                }
                // Revenue
                elseif (str_contains($message, 'doanh thu') || str_contains($message, 'revenue')) {
                    if (str_contains($message, 'tháng này') || str_contains($message, 'month')) {
                        $revenue = Order::where('domain', request()->getHost())->whereMonth('created_at', now()->month)->sum('total');
                        $response = "Doanh thu tháng " . now()->month . ": <b>" . number_format($revenue) . " đ</b>";
                    } else {
                        $revenue = Order::where('domain', request()->getHost())->whereDate('created_at', now())->sum('total');
                        $total = Order::where('domain', request()->getHost())->sum('total');
                        $response = "Doanh thu hôm nay: <b>" . number_format($revenue) . " đ</b><br>";
                        $response .= "Tổng doanh thu toàn thời gian: <b>" . number_format($total) . " đ</b>";
                    }
                }
                // Profit
                elseif (str_contains($message, 'lợi nhuận') || str_contains($message, 'profit')) {
                     $profit = 0;
                     $orders = Order::where('domain', request()->getHost())->whereDate('created_at', now())->with('service')->get();
                     foreach($orders as $order) {
                        $rate_original = $order->service?->rate_original ?? 0;
                         $cost = ($rate_original * $order->quantity) / 1000;
                         $profit += $order->total - $cost;
                     }
                     $response = "Lợi nhuận ước tính hôm nay: <b>" . number_format($profit) . " đ</b>";
                }
                // Help
                elseif (str_contains($message, 'trợ giúp') || str_contains($message, 'help')) {
                    $response = "<b>Chào bạn! Tôi có thể giúp gì?</b><br>";
                    $response .= "- 'Tổng thành viên', 'Thành viên mới'<br>";
                    $response .= "- 'Tổng đơn hàng', 'Đơn hàng thất bại'<br>";
                    $response .= "- 'Doanh thu hôm nay', 'Doanh thu tháng này'<br>";
                    $response .= "- 'SQL: SELECT * FROM users' (Truy vấn trực tiếp)";
                }
                // Smart Table Detection (Fallback)
                elseif (\Illuminate\Support\Facades\Schema::hasTable($message)) {
                    $count = \DB::table($message)->count();
                    $results = \DB::table($message)->limit(10)->get();
                    $response = "Bảng dữ liệu <b>$message</b> ($count dòng):<br>" . $this->formatTableResponse($results->toArray());
                }
                else {
                    $response = "Xin lỗi, tôi chưa hiểu ý bạn. Hãy thử hỏi về 'thành viên', 'đơn hàng', 'doanh thu' hoặc tên bảng dữ liệu.";
                }
            }
        } catch (\Exception $e) {
            $response = "Lỗi xử lý: " . $e->getMessage();
        }

        return response()->json([
            'status' => 'success',
            'message' => $response
        ]);
    }

    private function formatTableResponse($data, $title = "") {
        if (empty($data)) return "Không có dữ liệu.";
        $count = count($data);
        $html = $title ? "<div>$title ($count kết quả):</div>" : "";
        $html .= "<div class='table-responsive' style='max-height: 400px; overflow: auto;'><table class='table table-bordered table-sm text-nowrap'><thead><tr>";
        
        $firstRow = (array)$data[0];
        foreach(array_keys($firstRow) as $key) {
            $html .= "<th>$key</th>";
        }
        $html .= "</tr></thead><tbody>";
        
        foreach (array_slice($data, 0, 50) as $row) {
            $html .= "<tr>";
            foreach((array)$row as $val) {
                if (is_array($val) || is_object($val)) $val = json_encode($val);
                $html .= "<td>" . htmlspecialchars((string)$val) . "</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody></table></div>";
        if ($count > 50) $html .= "<small class='text-muted'>...và còn " . ($count - 50) . " dòng khác.</small>";
        
        return $html;
    }
}
