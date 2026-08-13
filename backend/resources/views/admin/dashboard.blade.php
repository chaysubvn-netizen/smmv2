@extends('admin.layouts.app')
@section('title', 'Quản lý doanh thu')

@section('styles')
<style>
    body {
        background-color: #F8F9FA;
    }
    .page-header {
        display: none !important;
    }
    .custom-dashboard-header {
        margin-bottom: 24px;
    }
    .custom-dashboard-header h2 {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }
    .custom-breadcrumb {
        font-size: 14px;
        color: #6B7280;
    }
    .custom-breadcrumb a {
        color: #6B7280;
        text-decoration: none;
    }
    .custom-breadcrumb a:hover {
        color: #374151;
    }
    
    .btn-export {
        background-color: #5A67D8;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-export:hover {
        background-color: #4C51BF;
        color: white;
    }
    
    .date-picker-wrap {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }

    .stat-card-modern {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #F3F4F6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        height: 100%;
    }
    .stat-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .stat-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .stat-icon-dark { background: #EEF2FF; color: #4F46E5; }
    .stat-icon-blue { background: #E0E7FF; color: #6366F1; }
    .stat-icon-green { background: #D1FAE5; color: #10B981; }
    .stat-icon-orange { background: #FEF3C7; color: #F59E0B; }

    .stat-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .stat-list-item:last-child {
        margin-bottom: 0;
    }
    .stat-list-label {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #4B5563;
    }
    .stat-list-label i {
        color: #9CA3AF;
        width: 16px;
        text-align: center;
    }
    .stat-list-value {
        font-weight: 600;
        color: #111827;
    }

    /* Charts and Tables Cards */
    .dashboard-panel {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #F3F4F6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        height: 100%;
    }
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .panel-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title i {
        color: #9CA3AF;
        font-size: 14px;
    }
    
    .table-custom {
        width: 100%;
        margin: 0;
    }
    .table-custom th {
        background: transparent;
        color: #111827;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 8px;
        border-bottom: 1px solid #E5E7EB;
    }
    .table-custom td {
        padding: 16px 8px;
        color: #374151;
        font-size: 14px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    .service-rank-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .service-rank-item:last-child {
        margin-bottom: 0;
    }
    .service-rank-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #EEF2FF;
        color: #4F46E5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .service-rank-info {
        flex-grow: 1;
    }
    .service-rank-name {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
        margin-bottom: 8px;
    }
    .service-rank-stats {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 16px;
        font-size: 14px;
    }
    .service-rank-revenue {
        font-weight: 600;
        color: #111827;
    }
    .service-rank-percent {
        color: #6B7280;
    }
    .progress-custom {
        height: 4px;
        background: #F3F4F6;
        border-radius: 2px;
        margin-top: 6px;
    }
    .progress-custom .progress-bar {
        border-radius: 2px;
    }
    /* Dark Mode Optimization */
    [data-theme-mode="dark"] body {
        background-color: transparent !important;
    }
    [data-theme-mode="dark"] .custom-dashboard-header h2,
    [data-theme-mode="dark"] .panel-title,
    [data-theme-mode="dark"] .stat-list-value,
    [data-theme-mode="dark"] .table-custom th,
    [data-theme-mode="dark"] .service-rank-revenue {
        color: #f8f9fa;
    }
    [data-theme-mode="dark"] .custom-breadcrumb,
    [data-theme-mode="dark"] .custom-breadcrumb a,
    [data-theme-mode="dark"] .stat-list-label,
    [data-theme-mode="dark"] .table-custom td,
    [data-theme-mode="dark"] .service-rank-percent,
    [data-theme-mode="dark"] .service-rank-name {
        color: #a0aabf;
    }
    [data-theme-mode="dark"] .custom-breadcrumb a:hover {
        color: #e2e8f0;
    }
    [data-theme-mode="dark"] .date-picker-wrap,
    [data-theme-mode="dark"] .stat-card-modern,
    [data-theme-mode="dark"] .dashboard-panel {
        background: #111c43;
        border-color: #2b3b64;
        color: #f8f9fa;
    }
    [data-theme-mode="dark"] .table-custom th,
    [data-theme-mode="dark"] .table-custom td {
        border-color: #2b3b64;
    }
    [data-theme-mode="dark"] .progress-custom {
        background: #2b3b64;
    }
    [data-theme-mode="dark"] .stat-icon-dark { background: #1e293b; color: #818cf8; }
    [data-theme-mode="dark"] .stat-icon-blue { background: #1e293b; color: #60a5fa; }
    [data-theme-mode="dark"] .stat-icon-green { background: #064e3b; color: #34d399; }
    [data-theme-mode="dark"] .stat-icon-orange { background: #78350f; color: #fbbf24; }
    [data-theme-mode="dark"] .service-rank-number {
        background: #1e293b;
        color: #818cf8;
    }
    [data-theme-mode="dark"] .text-dark {
        color: #f8f9fa !important;
    }
</style>
@endsection

@section('content')
    <div class="custom-dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2>Quản lý doanh thu</h2>
            <div class="custom-breadcrumb">
                <a href="#">Trang chủ</a> <i class="fas fa-chevron-right mx-1" style="font-size: 10px;"></i> Quản lý doanh thu
            </div>
        </div>
        <div class="d-flex gap-3">
            <div class="date-picker-wrap" id="reportrange" style="cursor: pointer;">
                <span>{{ now()->startOfMonth()->format('d/m/Y') }} - {{ now()->endOfMonth()->format('d/m/Y') }}</span> <i class="far fa-calendar-alt text-muted ms-2"></i>
            </div>
            <button class="btn-export" onclick="exportToCSV()">
                <i class="fas fa-download"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- 4 Cards -->
    <div class="row g-4 mb-4">
        <!-- Card 1 -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-card-header">
                    <div class="stat-icon-wrapper stat-icon-dark">
                        <i class="fas fa-infinity"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-dark">Toàn thời gian</h6>
                </div>
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="far fa-user"></i> Thành viên</span>
                        <span class="stat-list-value">{{ number_format($statics['users']['total'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-shopping-cart"></i> Đơn hàng</span>
                        <span class="stat-list-value">{{ number_format($statics['orders']['total'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-dollar-sign"></i> Doanh thu</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_revenue'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-chart-line"></i> Lợi nhuận</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_profit'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-card-header">
                    <div class="stat-icon-wrapper stat-icon-blue">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-dark">Tháng {{ now()->format('m') }}</h6>
                </div>
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="far fa-user"></i> Thành viên</span>
                        <span class="stat-list-value">{{ number_format($statics['users']['month'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-shopping-cart"></i> Đơn hàng</span>
                        <span class="stat-list-value">{{ number_format($statics['orders']['total_month'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-dollar-sign"></i> Doanh thu</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_revenue_month'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-chart-line"></i> Lợi nhuận</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_profit_month'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-card-header">
                    <div class="stat-icon-wrapper stat-icon-green">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-dark">Tuần này</h6>
                </div>
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="far fa-user"></i> Thành viên</span>
                        <span class="stat-list-value">{{ number_format($statics['users']['week'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-shopping-cart"></i> Đơn hàng</span>
                        <span class="stat-list-value">{{ number_format($statics['orders']['total_week'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-dollar-sign"></i> Doanh thu</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_revenue_week'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-chart-line"></i> Lợi nhuận</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_profit_week'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-card-header">
                    <div class="stat-icon-wrapper stat-icon-orange">
                        <i class="far fa-calendar-day"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-dark">Hôm nay</h6>
                </div>
                <div class="stat-list">
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="far fa-user"></i> Thành viên</span>
                        <span class="stat-list-value">{{ number_format($statics['users']['today'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-shopping-cart"></i> Đơn hàng</span>
                        <span class="stat-list-value">{{ number_format($statics['orders']['total_today'] ?? 0) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-dollar-sign"></i> Doanh thu</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_revenue_today'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-list-item">
                        <span class="stat-list-label"><i class="fas fa-chart-line"></i> Lợi nhuận</span>
                        <span class="stat-list-value">{{ convert_currency($statics['revenue_profit']['total_profit_today'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts -->
    <div class="row g-4 mb-4">
        <!-- Biểu đồ doanh thu -->
        <div class="col-xl-7">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Biểu đồ doanh thu <i class="far fa-question-circle" data-bs-toggle="tooltip" title="Biểu đồ thể hiện doanh thu và lợi nhuận"></i></h3>
                    <select class="form-select form-select-sm" style="width: auto; border-color: #E5E7EB;">
                        <option>Theo ngày</option>
                        <option>Theo tuần</option>
                        <option>Theo tháng</option>
                    </select>
                </div>
                <div id="chart_revenue" style="height: 350px;"></div>
            </div>
        </div>
        
        <!-- Doanh thu theo phương thức thanh toán -->
        <div class="col-xl-5">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Doanh thu theo phương thức thanh toán</h3>
                </div>
                <div class="row align-items-center h-100" style="min-height: 350px;">
                    <div class="col-md-6 position-relative d-flex justify-content-center">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Tables -->
    <div class="row g-4 mb-4">
        <!-- Bảng thống kê doanh thu -->
        <div class="col-xl-7">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Bảng thống kê doanh thu <i class="far fa-question-circle" data-bs-toggle="tooltip" title="Chi tiết doanh thu theo từng ngày"></i></h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Doanh thu</th>
                                <th>Lợi nhuận</th>
                                <th class="text-center">Đơn hàng</th>
                                <th class="text-center">Khách hàng mới</th>
                            </tr>
                        </thead>
                        <tbody>
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
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none" style="color: #6366F1; font-weight: 500; font-size: 14px;">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Top dịch vụ doanh thu cao -->
        <div class="col-xl-5">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <h3 class="panel-title">Top dịch vụ doanh thu cao <i class="far fa-question-circle" data-bs-toggle="tooltip" title="Các dịch vụ mang lại doanh thu lớn nhất"></i></h3>
                    <a href="#" class="text-decoration-none" style="color: #6366F1; font-size: 14px;">Xem tất cả</a>
                </div>
                
                @forelse($topServicesStats as $index => $ts)
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
                @endforelse

            </div>
    <!-- Modal Bảng xếp hạng -->
        <div class="modal fade" id="leaderboardModal" tabindex="-1" aria-labelledby="leaderboardModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leaderboardModalLabel">
                            <i class="fas fa-trophy text-warning me-2"></i>Bảng xếp hạng ngày <span id="leaderboard-date"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="leaderboard-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <div class="mt-2">Đang tải bảng xếp hạng...</div>
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Top 50 khách hàng chi tiêu nhiều nhất hôm nay</strong><br/>
                                <small>Danh sách được cập nhật theo thời gian thực</small>
                            </div>
                        </div>
                        
                        <div id="leaderboard-content" class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-primary sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 80px;">Hạng</th>
                                        <th style="width: 170px;">ID - Tên đăng nhập</th>
                                        <th>Địa chỉ Email</th>
                                        <th class="text-end" style="width: 140px;">Tổng chi tiêu</th>
                                        <th class="text-center" style="width: 100px;">Số đơn hàng</th>
                                    </tr>
                                </thead>
                                <tbody id="leaderboard-table-body">
                                    <!-- Dữ liệu sẽ được tải bằng AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="leaderboard-empty" class="text-center py-4" style="display: none;">
                            <i class="fas fa-inbox text-muted mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted">Chưa có dữ liệu bảng xếp hạng cho ngày hôm nay</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Đóng                </button>
                        <button type="button" class="btn btn-primary" onclick="loadLeaderboard()">
                            <i class="fas fa-sync-alt me-1"></i>Làm mới                </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Suppliers Stats -->
        <div class="modal fade" id="suppliersStatsModal" tabindex="-1" aria-labelledby="suppliersStatsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="suppliersStatsModalLabel">
                            <i class="fas fa-truck text-warning me-2"></i>Thống kê nhà cung cấp <span id="suppliers-date"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="suppliers-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <div class="mt-2">Đang tải thống kê nhà cung cấp...</div>
                        </div>
                        
                        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Thống kê nhà cung cấp</strong><br/>
                                <small>Được sắp xếp theo doanh thu từ cao đến thấp</small>
                            </div>
                        </div>
                        
                        <div id="suppliers-content" class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-warning sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">Hạng</th>
                                        <th style="width: 320px;">Nhà cung cấp</th>
                                        <th class="text-end" style="width: 120px;">Doanh thu</th>
                                        <th class="text-end" style="width: 120px;">Chi phí</th>
                                        <th class="text-end" style="width: 120px;">Lợi nhuận</th>
                                        <th class="text-center" style="width: 100px;">Số đơn</th>
                                        <th class="text-center" style="width: 100px;">Tỷ lệ LN</th>
                                    </tr>
                                </thead>
                                <tbody id="suppliers-table-body">
                                    <!-- Dữ liệu sẽ được tải bằng AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="suppliers-empty" class="text-center py-4" style="display: none;">
                            <i class="fas fa-truck text-muted mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted">Chưa có đơn hàng nào từ nhà cung cấp</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Đóng                </button>
                        <button type="button" class="btn btn-warning" onclick="loadSuppliersStats()">
                            <i class="fas fa-sync-alt me-1"></i>Làm mới                </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Top Services -->
        <div class="modal fade" id="topServicesModal" tabindex="-1" aria-labelledby="topServicesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="topServicesModalLabel">
                            <i class="fas fa-chart-bar text-success me-2"></i>Top dịch vụ bán chạy nhất <span id="services-date"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="services-loading" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <div class="mt-2">Đang tải danh sách dịch vụ...</div>
                        </div>
                        
                            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Top 50 dịch vụ có doanh thu cao nhất</strong><br/>
                                <small>Được sắp xếp theo tổng doanh thu từ cao đến thấp</small>
                            </div>
                        </div>
                        
                        <div id="services-content" class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead class="table-success sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">Hạng</th>
                                        <th style="width: 320px;">ID - Tên dịch vụ</th>
                                        <th class="text-end" style="width: 120px;">Doanh thu</th>
                                        <th class="text-end" style="width: 120px;">Chi phí</th>
                                        <th class="text-end" style="width: 120px;">Lợi nhuận</th>
                                        <th class="text-center" style="width: 100px;">Số đơn</th>
                                        <th class="text-end" style="width: 120px;">Giá TB</th>
                                    </tr>
                                </thead>
                                <tbody id="services-table-body">
                                    <!-- Dữ liệu sẽ được tải bằng AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="services-empty" class="text-center py-4" style="display: none;">
                            <i class="fas fa-box-open text-muted mb-2" style="font-size: 3rem;"></i>
                            <p class="text-muted">Chưa có dịch vụ nào được bán</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Đóng                </button>
                        <button type="button" class="btn btn-success" onclick="loadTopServices()">
                            <i class="fas fa-sync-alt me-1"></i>Làm mới                </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- AI Chat Modal -->
        <div class="modal fade" id="aiChatModal" tabindex="-1" aria-labelledby="aiChatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header border-bottom-0 pb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-robot fs-4 me-2"></i>
                            <h5 class="modal-title fw-bold" id="aiChatModalLabel">AI Chat Assistant</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="ai-chat-box" class="p-4" style="height: 400px; overflow-y: auto; background-color: auto;">
                            <!-- Welcome Message -->
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-robot text-primary mb-0 fs-5 pb-0" style="padding-bottom: 0 !important; margin-bottom: 0 !important; line-height: 1;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="bg-white p-3 shadow-sm" style="border-radius: 0 15px 15px 15px; max-width: 80%;">
                                        <p class="mb-2 fw-bold"><i class="fas fa-robot me-1 text-primary"></i>Chào bạn! Tôi là AI Assistant. Tôi có thể giúp bạn:</p>
                                        <ul class="list-unstyled mb-2 small text-muted">
                                            <li class="mb-1"><i class="fas fa-chart-line me-2 text-success"></i>Phân tích dữ liệu và thống kê</li>
                                            <li class="mb-1"><i class="fas fa-coins me-2 text-warning"></i>Theo dõi doanh thu, lợi nhuận</li>
                                            <li class="mb-1"><i class="fas fa-users me-2 text-info"></i>Quản lý khách hàng</li>
                                            <li class="mb-1"><i class="fas fa-search me-2 text-primary"></i>Tìm kiếm và báo cáo</li>
                                        </ul>
                                        <p class="mb-0 text-muted small fst-italic">Hãy hỏi tôi bất cứ điều gì!</p>
                                        <div class="mt-2 text-end">
                                            <small class="text-muted" style="font-size: 10px;">{{ now()->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-3 bg-white border-top custom-chat-input-area" style="border-radius: 0 0 15px 15px;">
                            <form id="aiChatForm" onsubmit="handleAIChatSubmit(event)">
                                <div class="input-group">
                                    <input type="text" id="aiChatInput" class="form-control border-0 bg-light py-2 px-3" placeholder="Nhập tin nhắn..." style="border-radius: 20px 0 0 20px;" autocomplete="off">
                                    <button class="btn btn-dark px-4" type="submit" style="border-radius: 0 20px 20px 0;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>

@section('script')
<!-- Floating buttons -->
    <div class="position-fixed" style="bottom: 80px; right: 15px; z-index: 99999;">
        <!-- Button Suppliers Stats -->
        <div class="mb-3">
            <button type="button" class="btn btn-warning btn-icon rounded-circle shadow-lg" onclick="showSuppliersStats()"
                data-bs-toggle="tooltip" data-bs-placement="left"
                title="Thống kê nhà cung cấp" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-truck" style="font-size: 18px; color: white;"></i>
            </button>
        </div>

        <!-- Button Top Services -->
        <div class="mb-3">
            <button type="button" class="btn btn-success btn-icon rounded-circle shadow-lg" onclick="showTopServices()"
                data-bs-toggle="tooltip" data-bs-placement="left"
                title="Top 50 dịch vụ bán chạy nhất" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-bar" style="font-size: 18px; color: white;"></i>
            </button>
        </div>
        
        <!-- Button Leaderboard -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary btn-icon rounded-circle shadow-lg" onclick="showLeaderboard()"
                data-bs-toggle="tooltip" data-bs-placement="left"
                title="Top 50 khách hàng chi tiêu nhiều nhất" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-trophy" style="font-size: 18px; color: white;"></i>
            </button>
        </div>
        
        <!-- Button AI Chat -->
        <div>
            <button type="button" class="btn btn-primary btn-icon rounded-circle shadow-lg" onclick="showAIChatPopup()"
                data-bs-toggle="tooltip" data-bs-placement="left"
                title="Chat với AI Assistant" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-robot" style="font-size: 18px; color: white;"></i>
            </button>
        </div>
    </div>

    
<script>
// Initialize Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    
        function showSuppliersStats() {
            const modal = new bootstrap.Modal(document.getElementById('suppliersStatsModal'));
            modal.show();
            loadSuppliersStats();
        }
    
        function showTopServices() {
            const modal = new bootstrap.Modal(document.getElementById('topServicesModal'));
            modal.show();
            loadTopServices();
        }
    
        function showLeaderboard() {
            const modal = new bootstrap.Modal(document.getElementById('leaderboardModal'));
            modal.show();
            loadLeaderboard();
        }
    
        function showAIChatPopup() {
            const modal = new bootstrap.Modal(document.getElementById('aiChatModal'));
            modal.show();
            document.getElementById('aiChatModal').addEventListener('shown.bs.modal', function () {
                document.getElementById('aiChatInput').focus();
            });
        }
    
        function handleAIChatSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('aiChatInput');
            const message = input.value.trim();
            if (!message) return;
    
            appendMessage('user', message);
            input.value = '';
            const loadingId = appendLoading();
            
            $.ajax({
                url: '{{ route("admin.ai-chat") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: message
                },
                success: function(res) {
                     removeLoading(loadingId);
                     if(res.status === 'success') {
                         appendMessage('ai', res.message);
                     } else {
                         appendMessage('ai', "Có lỗi xảy ra: " + res.message);
                     }
                },
                error: function(xhr) {
                     removeLoading(loadingId);
                     appendMessage('ai', "Lỗi kết nối đến máy chủ.");
                     console.error(xhr);
                }
            });
        }
    
        function appendMessage(sender, text) {
            const chatBox = document.getElementById('ai-chat-box');
            const time = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            let html = '';
            if (sender === 'user') {
                html = `
                    <div class="d-flex mb-3 justify-content-end">
                        <div class="flex-grow-1 me-3 text-end">
                            <div class="bg-primary text-white p-3 shadow-lg d-inline-block text-start" style="border-radius: 20px 20px 0 20px; max-width: 80%;">
                                <p class="mb-0 small">${text}</p>
                                <div class="mt-1 text-end">
                                    <small class="text-white-50 fs-10">${time}</small>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                             <div class="avatar avatar-md rounded-circle bg-primary-transparent d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1);">
                                <i class="fas fa-user text-primary small"></i>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                             <div class="avatar avatar-md rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #1e293b; border: 1px solid rgba(255,255,255,0.1);">
                                <i class="fas fa-robot text-info fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="bg-dark-light p-3 shadow-lg" style="border-radius: 0 20px 20px 20px; max-width: 80%; background: #161c2d; border: 1px solid rgba(255,255,255,0.05);">
                                <p class="mb-0 small text-light">${text}</p>
                                <div class="mt-1 text-end">
                                    <small class="text-muted fs-10">${time}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            chatBox.insertAdjacentHTML('beforeend', html);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    
        function appendLoading() {
            const chatBox = document.getElementById('ai-chat-box');
            const id = 'loading-' + Date.now();
            const html = `
                <div id="${id}" class="d-flex mb-3">
                    <div class="flex-shrink-0">
                         <div class="avatar avatar-md rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-robot text-primary mb-0 fs-5 pb-0" style="padding-bottom: 0 !important; margin-bottom: 0 !important; line-height: 1;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="bg-white p-3 shadow-sm" style="border-radius: 0 15px 15px 15px; width: fit-content;">
                            <div class="typing-indicator">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            chatBox.insertAdjacentHTML('beforeend', html);
            chatBox.scrollTop = chatBox.scrollHeight;
            return id;
        }
    
        function removeLoading(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        // Functions to load data for modals
        function loadSuppliersStats() {
            $('#suppliers-loading').show();
            $('#suppliers-content').hide();
            $('#suppliers-empty').hide();
            $('#suppliers-date').text('Toàn thời gian');
    
            $.ajax({
                url: '{{ route("admin.provider-statistic") }}',
                type: 'GET',
                success: function(res) {
                    $('#suppliers-loading').hide();
                    if(res.status === 'success') {
                        if (res.data.length > 0) {
                            $('#suppliers-content').show();
                            let html = '';
                            res.data.forEach((item, index) => {
                                let revenue = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.revenue);
                                let cost = item.cost ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.cost) : '0 ₫';
                                let profit = item.profit ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.profit) : '0 ₫';
                                let profit_rate = item.profit_rate ? item.profit_rate + '%' : '0%';
    
                                html += `<tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.name}</td>
                                    <td class="text-end text-success fw-bold">${revenue}</td>
                                    <td class="text-end text-danger">${cost}</td>
                                    <td class="text-end text-primary fw-bold">${profit}</td>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill">${item.count}</span></td>
                                    <td class="text-center">${profit_rate}</td>
                                </tr>`;
                            });
                            $('#suppliers-table-body').html(html);
                        } else {
                            $('#suppliers-empty').show();
                        }
                    } else {
                        alert('Lỗi: ' + res.message);
                        $('#suppliers-empty').show();
                    }
                },
                error: function(xhr) {
                    $('#suppliers-loading').hide();
                    $('#suppliers-empty').show();
                    console.error(xhr);
                    // alert('Lỗi kết nối máy chủ (500/404). Vui lòng kiểm tra Console.');
                }
            });
        }
    
        function loadTopServices() {
            $('#services-loading').show();
            $('#services-content').hide();
            $('#services-empty').hide();
            $('#services-date').text('Toàn thời gian');
    
            $.ajax({
                url: '{{ route("admin.top-services") }}',
                type: 'GET',
                success: function(res) {
                    $('#services-loading').hide();
                    if(res.status === 'success') {
                        if (res.data.length > 0) {
                            $('#services-content').show();
                            let html = '';
                            res.data.forEach((item, index) => {
                                let revenue = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.revenue);
                                let cost = item.cost ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.cost) : '0 ₫';
                                let profit = item.profit ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.profit) : '0 ₫';
                                let avgPrice = item.avg_price ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.avg_price) : '0 ₫';
    
                                html += `<tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td><span class="badge bg-secondary-transparent text-muted me-1">${item.id}</span> ${item.name}</td>
                                    <td class="text-end text-success fw-bold">${revenue}</td>
                                    <td class="text-end text-danger">${cost}</td>
                                    <td class="text-end text-primary fw-bold">${profit}</td>
                                    <td class="text-center"><span class="badge bg-secondary rounded-pill">${item.count}</span></td>
                                    <td class="text-end">${avgPrice}</td>
                                </tr>`;
                            });
                            $('#services-table-body').html(html);
                        } else {
                            $('#services-empty').show();
                        }
                    } else {
                        alert('Lỗi: ' + res.message);
                        $('#services-empty').show();
                    }
                },
                error: function(xhr) {
                    $('#services-loading').hide();
                    $('#services-empty').show();
                    console.error(xhr);
                    // alert('Lỗi kết nối máy chủ (500/404). Vui lòng kiểm tra Console.');
                }
            });
        }
    
        function loadLeaderboard() {
            $('#leaderboard-loading').show();
            $('#leaderboard-content').hide();
            $('#leaderboard-empty').hide();
            $('#leaderboard-date').text('Toàn thời gian');
    
             $.ajax({
                url: '{{ route("admin.leaderboard") }}',
                type: 'GET',
                success: function(res) {
                    $('#leaderboard-loading').hide();
                    if(res.status === 'success') {
                        if (res.data.length > 0) {
                            $('#leaderboard-content').show();
                            let html = '';
                            res.data.forEach((item, index) => {
                                let totalSpend = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.total_spend);
                                
                                html += `<tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-primary text-white text-center rounded-circle" style="line-height:2rem; width:2rem; height:2rem;">
                                                ${item.username.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <div class="fw-bold">${item.username}</div>
                                                <div class="small text-muted">ID: ${item.id}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${item.email}</td>
                                    <td class="text-end text-success fw-bold">${totalSpend}</td>
                                    <td class="text-center"><span class="badge bg-info rounded-pill">${item.count}</span></td>
                                </tr>`;
                            });
                            $('#leaderboard-table-body').html(html);
                        } else {
                            $('#leaderboard-empty').show();
                        }
                    } else {
                        alert('Lỗi: ' + res.message);
                        $('#leaderboard-empty').show();
                    }
                },
                error: function(xhr) {
                    $('#leaderboard-loading').hide();
                    $('#leaderboard-empty').show();
                    console.error(xhr);
                    // alert('Lỗi kết nối máy chủ (500/404). Vui lòng kiểm tra Console.');
                }
            });
        }
    </script>
<style>
    .typing-indicator span {
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: #adb5bd;
        border-radius: 50%;
        animation: typing 1s infinite;
        margin: 0 2px;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    </style>
<script src="/admin-pack/assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Biểu đồ doanh thu
    var optionsRevenue = {
        series: @json($chartRevenueProfitLabels),
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        stroke: {
            width: [2, 2],
            curve: 'smooth',
            dashArray: [0, 5]
        },
        colors: ['#6366F1', '#10B981'],
        markers: {
            size: 4,
            colors: ['#fff', '#fff'],
            strokeColors: ['#6366F1', '#10B981'],
            strokeWidth: 2,
            hover: { size: 6 }
        },
        xaxis: {
            categories: @json($chartDays),
            labels: { style: { colors: '#9CA3AF' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#9CA3AF' },
                formatter: function (val) {
                    return val + "M";
                }
            }
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: true } }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            markers: { radius: 12 }
        }
    };

    var chartRevenue = new ApexCharts(document.querySelector("#chart_revenue"), optionsRevenue);
    chartRevenue.render();

    // Biểu đồ phương thức thanh toán
    var optionsPayment = {
        series: @json($paymentMethodsStats['series']),
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'inherit'
        },
        labels: @json($paymentMethodsStats['labels']),
        colors: ['#6366F1', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6'],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: false
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { width: 0 },
        legend: { show: false },
        tooltip: {
            enabled: true,
            theme: 'light',
            y: { formatter: function(val) { return val + "%" } }
        }
    };

    var chartPayment = new ApexCharts(document.querySelector("#chart_payment_methods"), optionsPayment);
    chartPayment.render();
</script>

<!-- Daterangepicker dependencies -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
$(function() {
    var start = moment().startOf('month');
    var end = moment().endOf('month');

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        // Lógica để tự động tải lại dữ liệu với AJAX sẽ được đặt ở đây nếu cần thiết trong tương lai.
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Hôm nay': [moment(), moment()],
           'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 ngày qua': [moment().subtract(6, 'days'), moment()],
           '30 ngày qua': [moment().subtract(29, 'days'), moment()],
           'Tháng này': [moment().startOf('month'), moment().endOf('month')],
           'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: "Áp dụng",
            cancelLabel: "Hủy",
            customRangeLabel: "Tùy chọn",
            daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            monthNames: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            firstDay: 1
        }
    }, cb);

    cb(start, end);
});

function exportToCSV() {
    var csv = [];
    var rows = document.querySelectorAll(".table-custom tr");
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) {
            // Clean inner text (remove commas/newlines) to prevent CSV breaking
            let text = cols[j].innerText.replace(/"/g, '""').trim();
            row.push('"' + text + '"');
        }
        
        csv.push(row.join(","));        
    }

    // Include top services data as well
    csv.push("\\n\\nTop Dịch vụ Doanh thu cao\\n");
    csv.push('"Hạng","Tên dịch vụ","Doanh thu","Tỷ lệ"');
    var services = document.querySelectorAll(".service-rank-item");
    services.forEach(function(item) {
        var rank = item.querySelector(".service-rank-number").innerText;
        var name = item.querySelector(".service-rank-name").innerText;
        var rev = item.querySelector(".service-rank-revenue").innerText;
        var pct = item.querySelector(".service-rank-percent").innerText;
        csv.push(`"${rank}","${name}","${rev}","${pct}"`);
    });

    // Download CSV file
    var csvFile = new Blob(["\\uFEFF"+csv.join("\\n")], {type: "text/csv;charset=utf-8;"});
    var downloadLink = document.createElement("a");
    downloadLink.download = "bao_cao_doanh_thu_" + moment().format('YYYY_MM_DD') + ".csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>
@endsection
@endsection
