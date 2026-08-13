import re

# 1. Update AppAdminController
with open('d:/smm_panel_v3/app/Http\Controllers/Admin/AppAdminController.php', 'r', encoding='utf-8') as f:
    controller_content = f.read()

extra_data_code = """
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
            
            $rev = Order::where('domain', request()->getHost())->where('status', 'Completed')->whereDate('created_at', $d)->sum('total');
            $ordersCount = Order::where('domain', request()->getHost())->where('status', 'Completed')->whereDate('created_at', $d)->count();
            $newUsers = User::where('domain', request()->getHost())->whereDate('created_at', $d)->count();
            
            // profit
            $dayOrders = Order::where('domain', request()->getHost())->where('status', 'Completed')->whereDate('created_at', $d)->with('service')->get();
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
            ->where('status', 'Completed')
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
        
        // Pass to view
"""

# Find return view('admin.dashboard', [...])
controller_content = controller_content.replace(
    "return view('admin.dashboard', [",
    extra_data_code + "\n        return view('admin.dashboard', [\n            'paymentMethodsStats' => $paymentMethodsStats,\n            'revenueTableStats' => $revenueTableStats,\n            'topServicesStats' => $topServicesStats,"
)

with open('d:/smm_panel_v3/app/Http/Controllers/Admin/AppAdminController.php', 'w', encoding='utf-8') as f:
    f.write(controller_content)

# 2. Update dashboard.blade.php
with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    blade_content = f.read()

# Replace hardcoded payment methods HTML
payment_html_old = """<div class="col-md-6 position-relative d-flex justify-content-center">
                        <div id="chart_payment_methods" style="height: 250px; width: 100%;"></div>
                        <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                            <h4 class="mb-0 fw-bold">{{ convert_currency($statics['users']['total_deposit'] ?? 0, Auth::user()->currency, false) }}</h4>
                            <span class="text-muted small">Tổng</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #6366F1;"></span>
                                <span class="text-muted" style="font-size: 14px;">Số dư tài khoản</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6">65.2%</div>
                                <div class="text-muted small">344,450,000 đ</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                                <span class="text-muted" style="font-size: 14px;">Ngân hàng</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6">20.1%</div>
                                <div class="text-muted small">106,250,000 đ</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #3B82F6;"></span>
                                <span class="text-muted" style="font-size: 14px;">Ví điện tử</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6">10.3%</div>
                                <div class="text-muted small">54,350,000 đ</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B;"></span>
                                <span class="text-muted" style="font-size: 14px;">Thẻ cào</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6">4.4%</div>
                                <div class="text-muted small">23,400,000 đ</div>
                            </div>
                        </div>
                    </div>"""

payment_html_new = """<div class="col-md-6 position-relative d-flex justify-content-center">
                        <div id="chart_payment_methods" style="height: 250px; width: 100%;"></div>
                        <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                            <h4 class="mb-0 fw-bold fs-5">{{ convert_currency($statics['users']['total_deposit'] ?? 0, Auth::user()->currency, false) }}</h4>
                            <span class="text-muted small">Tổng</span>
                        </div>
                    </div>
                    <div class="col-md-6" style="max-height: 250px; overflow-y: auto;">
                        @forelse($paymentMethodsStats['details'] as $pdetail)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $pdetail['color'] }};"></span>
                                <span class="text-muted text-truncate" style="font-size: 14px; max-width: 120px;" title="{{ $pdetail['name'] }}">{{ $pdetail['name'] }}</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6">{{ $pdetail['percent'] }}%</div>
                                <div class="text-muted" style="font-size: 12px;">{{ convert_currency($pdetail['amount'], Auth::user()->currency, false) }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted">Chưa có dữ liệu thanh toán</div>
                        @endforelse
                    </div>"""
blade_content = blade_content.replace(payment_html_old, payment_html_new)

# Replace hardcoded table HTML
table_html_old = """<tbody>
                            <tr>
                                <td>31/05/2024</td>
                                <td class="fw-bold text-dark">18,450,000 đ</td>
                                <td class="fw-bold text-dark">13,850,000 đ</td>
                                <td class="text-center">456</td>
                                <td class="text-center">128</td>
                            </tr>
                            <tr>
                                <td>30/05/2024</td>
                                <td class="fw-bold text-dark">22,750,000 đ</td>
                                <td class="fw-bold text-dark">16,900,000 đ</td>
                                <td class="text-center">512</td>
                                <td class="text-center">142</td>
                            </tr>
                            <tr>
                                <td>29/05/2024</td>
                                <td class="fw-bold text-dark">19,850,000 đ</td>
                                <td class="fw-bold text-dark">14,550,000 đ</td>
                                <td class="text-center">478</td>
                                <td class="text-center">115</td>
                            </tr>
                            <tr>
                                <td>28/05/2024</td>
                                <td class="fw-bold text-dark">17,650,000 đ</td>
                                <td class="fw-bold text-dark">12,950,000 đ</td>
                                <td class="text-center">398</td>
                                <td class="text-center">98</td>
                            </tr>
                            <tr>
                                <td>27/05/2024</td>
                                <td class="fw-bold text-dark">20,550,000 đ</td>
                                <td class="fw-bold text-dark">15,300,000 đ</td>
                                <td class="text-center">467</td>
                                <td class="text-center">129</td>
                            </tr>
                        </tbody>"""

table_html_new = """<tbody>
                            @forelse($revenueTableStats as $tStat)
                            <tr>
                                <td>{{ $tStat['date'] }}</td>
                                <td class="fw-bold text-dark">{{ convert_currency($tStat['revenue'], Auth::user()->currency, false) }}</td>
                                <td class="fw-bold text-dark">{{ convert_currency($tStat['profit'], Auth::user()->currency, false) }}</td>
                                <td class="text-center">{{ number_format($tStat['orders']) }}</td>
                                <td class="text-center">{{ number_format($tStat['new_users']) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>"""
blade_content = blade_content.replace(table_html_old, table_html_new)

# Replace Top Services HTML
top_services_html_old = """<div class="service-rank-item">
                    <div class="service-rank-number">1</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name">Facebook Like</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">152,450,000 đ</span>
                                <span class="service-rank-percent">28.8%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 28.8%; background-color: #6366F1;"></div>
                        </div>
                    </div>
                </div>

                <div class="service-rank-item">
                    <div class="service-rank-number">2</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name">TikTok View</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">98,750,000 đ</span>
                                <span class="service-rank-percent">18.7%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 18.7%; background-color: #10B981;"></div>
                        </div>
                    </div>
                </div>

                <div class="service-rank-item">
                    <div class="service-rank-number">3</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name">Instagram Follow</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">76,300,000 đ</span>
                                <span class="service-rank-percent">14.4%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 14.4%; background-color: #F59E0B;"></div>
                        </div>
                    </div>
                </div>

                <div class="service-rank-item">
                    <div class="service-rank-number">4</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name">YouTube Watch Time</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">65,200,000 đ</span>
                                <span class="service-rank-percent">12.3%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 12.3%; background-color: #F59E0B;"></div>
                        </div>
                    </div>
                </div>

                <div class="service-rank-item">
                    <div class="service-rank-number">5</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name">Telegram Member</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">45,600,000 đ</span>
                                <span class="service-rank-percent">8.6%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: 8.6%; background-color: #6366F1;"></div>
                        </div>
                    </div>
                </div>"""
                
top_services_html_new = """@forelse($topServicesStats as $index => $ts)
                <div class="service-rank-item">
                    <div class="service-rank-number">{{ $index + 1 }}</div>
                    <div class="service-rank-info">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="service-rank-name text-truncate" style="max-width: 180px;" title="{{ $ts['name'] }}">{{ $ts['name'] }}</div>
                            <div class="service-rank-stats">
                                <span class="service-rank-revenue">{{ convert_currency($ts['revenue'], Auth::user()->currency, false) }}</span>
                                <span class="service-rank-percent">{{ $ts['percent'] }}%</span>
                            </div>
                        </div>
                        <div class="progress progress-custom">
                            <div class="progress-bar" style="width: {{ $ts['percent'] }}%; background-color: {{ ['#6366F1', '#10B981', '#F59E0B', '#3B82F6', '#EC4899'][$index % 5] }};"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">Chưa có dữ liệu dịch vụ</div>
                @endforelse"""
blade_content = blade_content.replace(top_services_html_old, top_services_html_new)

# Fix JS Charts Data
js_chart_revenue_old = """    var optionsRevenue = {
        series: [{
            name: 'Doanh thu',
            type: 'line',
            data: [45, 52, 53, 56, 62, 61, 66, 52, 45, 46, 57, 55, 61, 66, 53, 56, 64, 71, 66, 68, 57, 61, 67, 60, 58, 68, 79]
        }, {
            name: 'Lợi nhuận',
            type: 'line',
            data: [22, 28, 29, 34, 33, 36, 37, 27, 22, 23, 29, 29, 33, 36, 27, 28, 34, 39, 36, 36, 31, 33, 36, 32, 31, 36, 42]
        }],"""
        
js_chart_revenue_new = """    var optionsRevenue = {
        series: @json($chartRevenueProfitLabels),"""
blade_content = blade_content.replace(js_chart_revenue_old, js_chart_revenue_new)

js_chart_xaxis_old = """        xaxis: {
            categories: ['01/05', '', '', '06/05', '', '', '', '11/05', '', '', '', '16/05', '', '', '', '21/05', '', '', '', '26/05', '', '', '', '31/05'],"""
js_chart_xaxis_new = """        xaxis: {
            categories: @json($chartDays),"""
blade_content = blade_content.replace(js_chart_xaxis_old, js_chart_xaxis_new)

js_chart_payment_old = """    var optionsPayment = {
        series: [65.2, 20.1, 10.3, 4.4],"""
js_chart_payment_new = """    var optionsPayment = {
        series: @json($paymentMethodsStats['series']),"""
blade_content = blade_content.replace(js_chart_payment_old, js_chart_payment_new)

js_chart_payment_labels_old = """        labels: ['Số dư tài khoản', 'Ngân hàng', 'Ví điện tử', 'Thẻ cào'],
        colors: ['#6366F1', '#10B981', '#3B82F6', '#F59E0B'],"""
js_chart_payment_labels_new = """        labels: @json($paymentMethodsStats['labels']),
        colors: ['#6366F1', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'],"""
blade_content = blade_content.replace(js_chart_payment_labels_old, js_chart_payment_labels_new)

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(blade_content)

print("Dashboard v4 data binding completed.")
