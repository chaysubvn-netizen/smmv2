import re

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_content = """@extends('admin.layouts.app')
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
            <div class="date-picker-wrap">
                01/05/2024 - 31/05/2024 <i class="far fa-calendar-alt text-muted ms-2"></i>
            </div>
            <button class="btn-export">
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
                
                <div class="service-rank-item">
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
                </div>

            </div>
        </div>
    </div>

@section('script')
<script src="/admin-pack/assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
    // Initialize Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Biểu đồ doanh thu
    var optionsRevenue = {
        series: [{
            name: 'Doanh thu',
            type: 'line',
            data: [45, 52, 53, 56, 62, 61, 66, 52, 45, 46, 57, 55, 61, 66, 53, 56, 64, 71, 66, 68, 57, 61, 67, 60, 58, 68, 79]
        }, {
            name: 'Lợi nhuận',
            type: 'line',
            data: [22, 28, 29, 34, 33, 36, 37, 27, 22, 23, 29, 29, 33, 36, 27, 28, 34, 39, 36, 36, 31, 33, 36, 32, 31, 36, 42]
        }],
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
            categories: ['01/05', '', '', '06/05', '', '', '', '11/05', '', '', '', '16/05', '', '', '', '21/05', '', '', '', '26/05', '', '', '', '31/05'],
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
        series: [65.2, 20.1, 10.3, 4.4],
        chart: {
            type: 'donut',
            height: 250,
            fontFamily: 'inherit'
        },
        labels: ['Số dư tài khoản', 'Ngân hàng', 'Ví điện tử', 'Thẻ cào'],
        colors: ['#6366F1', '#10B981', '#3B82F6', '#F59E0B'],
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
@endsection
@endsection
"""

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Dashboard v3 rewritten successfully matching the image.")
