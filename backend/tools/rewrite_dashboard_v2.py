import re

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_content = """@extends('admin.layouts.app')
@section('title', 'Bảng thống kê')

@section('styles')
<style>
    /* Custom Dashboard Styles */
    .dash-widget {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .dash-widget:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px 0 rgba(34, 41, 47, 0.15);
    }
    .bg-grad-primary { background: linear-gradient(135deg, #7367F0 0%, #CE9FFC 100%); color: #fff; }
    .bg-grad-info { background: linear-gradient(135deg, #00CFE8 0%, #1CE2E6 100%); color: #fff; }
    .bg-grad-warning { background: linear-gradient(135deg, #FF9F43 0%, #FFC07A 100%); color: #fff; }
    .bg-grad-danger { background: linear-gradient(135deg, #EA5455 0%, #FF8A8A 100%); color: #fff; }
    .bg-grad-success { background: linear-gradient(135deg, #28C76F 0%, #81FBB8 100%); color: #fff; }
    .bg-grad-dark { background: linear-gradient(135deg, #1E1E1E 0%, #3B3B3B 100%); color: #fff; }
    
    .widget-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: rgba(255, 255, 255, 0.2);
    }
    .widget-icon-light {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .text-white-80 { color: rgba(255, 255, 255, 0.8) !important; }
    
    .stat-card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.03);
    }
    .stat-item {
        padding: 12px 0;
        border-bottom: 1px dashed #e9ecef;
    }
    .stat-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
    <div class="row">
        <!-- System Control Center -->
        @if (s('domain') == s('domain_main'))
            <div class="col-12 mb-4">
                <div class="card dash-widget bg-grad-dark">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="widget-icon me-3">
                                        <i class="fas fa-terminal"></i>
                                    </div>
                                    <h3 class="mb-0 text-white fw-bold">SMMPANEL_V2 <span class="badge bg-light text-dark rounded-pill ms-2 fs-6">{{ $currentVersion }}</span></h3>
                                </div>
                                <p class="text-white-80 mb-0">
                                    <i class="fas fa-magic me-2 text-warning"></i> Hệ thống đang chạy ở chế độ tối ưu hóa hiệu suất cao. Các cập nhật sẽ tự động hoàn tất ngầm.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3 mt-4">
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="d-block p-3 rounded text-decoration-none text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="small fw-bold mb-1 text-uppercase text-white-80"><i class="fab fa-telegram me-2 text-info"></i>Kênh cập nhật</div>
                                    <div class="fs-6 fw-bold">Telegram Official</div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="d-block p-3 rounded text-decoration-none text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="small fw-bold mb-1 text-uppercase text-white-80"><i class="fas fa-code me-2 text-warning"></i>Tìm kiếm API</div>
                                    <div class="fs-6 fw-bold">Hỗ trợ đối tác</div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="d-block p-3 rounded text-decoration-none text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="small fw-bold mb-1 text-uppercase text-white-80"><i class="fas fa-users me-2 text-success"></i>Zalo Group</div>
                                    <div class="fs-6 fw-bold">Thông báo hệ thống</div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="#" class="d-block p-3 rounded text-decoration-none text-white" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                                    <div class="small fw-bold mb-1 text-uppercase text-white-80"><i class="fas fa-comments me-2 text-primary"></i>Cộng đồng</div>
                                    <div class="fs-6 fw-bold">Trao đổi kinh nghiệm</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <script>
            // Auto Update Script
            document.addEventListener('DOMContentLoaded', function() {
                const currentVersion = '{{ $currentVersion }}';
                fetch(`/auto-update?version=${currentVersion}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.ok && data.message.includes('Update applied')) {
                            window.location.reload();
                        } else {
                            console.log('Update Check:', data.message);
                        }
                    })
                    .catch(err => console.error('Auto update error:', err));
            });
        </script>
 
        <!-- Subtle Cron Job Warning -->
        <div class="col-12 mb-4">
            <div class="alert alert-warning border-0 d-flex align-items-center shadow-sm" style="border-radius: 12px; background: #fff8e1;">
                <div class="flex-shrink-0 me-3">
                    <div class="widget-icon-light bg-warning text-white">
                        <i class="fas fa-hourglass-start"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <span class="text-warning fw-bold fs-5">System Attention:</span>
                    <span class="ms-1 text-dark">Thiết lập <b>CRON JOB</b> (1 phút/lần) để đảm bảo hệ thống vận hành tự động. </span>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#linkCronLoad" class="text-decoration-none fw-bold ms-2 text-warning text-decoration-underline">Cấu hình ngay →</a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>

        <!-- Top 4 Summary Cards -->
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card dash-widget bg-grad-primary h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-80 fw-bold text-uppercase fs-6">Đơn chờ xử lý</p>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($statics['orders']['pending'] ?? 0) }}</h2>
                        </div>
                        <div class="widget-icon shadow-sm">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card dash-widget bg-grad-info h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-80 fw-bold text-uppercase fs-6">Ticket chờ</p>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($statics['system']['tickets_pending'] ?? 0) }}</h2>
                        </div>
                        <div class="widget-icon shadow-sm">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card dash-widget bg-grad-warning h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-80 fw-bold text-uppercase fs-6">Web chờ duyệt</p>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($statics['system']['child_panels_pending'] ?? 0) }}</h2>
                        </div>
                        <div class="widget-icon shadow-sm">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="card dash-widget bg-grad-danger h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white-80 fw-bold text-uppercase fs-6">Rút tiền chờ</p>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($statics['system']['withdrawals_pending'] ?? 0) }}</h2>
                        </div>
                        <div class="widget-icon shadow-sm">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-end mb-3">
            <span class="badge bg-white text-dark border px-3 py-2 fw-bold text-uppercase shadow-sm fs-6">
                <i class="fas fa-circle text-success me-2 border-success border rounded-circle"></i> Live Data Center
            </span>
        </div>

        <!-- 4 Detailed Metric Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-top border-4 border-primary">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title text-primary fw-bold mb-0"><i class="fas fa-infinity me-2"></i>Toàn thời gian</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Thành viên</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['users']['total'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Đơn hàng</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['orders']['total'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Doanh thu</span>
                        <span class="fw-bold text-success fs-5">{{ convert_currency($statics['revenue_profit']['total_revenue'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Lợi nhuận</span>
                        <span class="fw-bold text-primary fs-5">{{ convert_currency($statics['revenue_profit']['total_profit'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-top border-4" style="border-top-color: #8b5cf6 !important;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold mb-0" style="color: #8b5cf6;"><i class="fas fa-calendar-alt me-2"></i>Tháng {{ now()->format('m') }}</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Thành viên</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['users']['month'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Đơn hàng</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['orders']['total_month'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Doanh thu</span>
                        <span class="fw-bold text-success fs-5">{{ convert_currency($statics['revenue_profit']['total_revenue_month'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Lợi nhuận</span>
                        <span class="fw-bold text-primary fs-5">{{ convert_currency($statics['revenue_profit']['total_profit_month'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-top border-4 border-success">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title text-success fw-bold mb-0"><i class="fas fa-calendar-week me-2"></i>Tuần này</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Thành viên</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['users']['week'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Đơn hàng</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['orders']['total_week'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Doanh thu</span>
                        <span class="fw-bold text-success fs-5">{{ convert_currency($statics['revenue_profit']['total_revenue_week'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Lợi nhuận</span>
                        <span class="fw-bold text-primary fs-5">{{ convert_currency($statics['revenue_profit']['total_profit_week'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 border-top border-4 border-warning">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title text-warning fw-bold mb-0"><i class="fas fa-bolt me-2"></i>Hôm nay</h5>
                </div>
                <div class="card-body pt-3">
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Thành viên</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['users']['today'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Đơn hàng</span>
                        <span class="fw-bold fs-5">{{ number_format($statics['orders']['total_today'] ?? 0) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Doanh thu</span>
                        <span class="fw-bold text-success fs-5">{{ convert_currency($statics['revenue_profit']['total_revenue_today'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold">Lợi nhuận</span>
                        <span class="fw-bold text-primary fs-5">{{ convert_currency($statics['revenue_profit']['total_profit_today'] ?? 0, Auth::user()->currency, false) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consolidated Finance Card -->
        <div class="col-12 mb-4">
            <div class="card stat-card border-0">
                <div class="card-header bg-white pt-4">
                    <h5 class="card-title fw-bold mb-0"><i class="fas fa-wallet me-2 text-info"></i>Tổng quan tài chính (Nạp tiền)</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="card bg-grad-info text-white mb-0 border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="mb-1 text-white-80 fw-bold text-uppercase fs-6">Tổng nạp toàn thời gian</p>
                                            <h2 class="mb-0 text-white fw-bold">{{ convert_currency($statics['users']['total_deposit'] ?? 0, Auth::user()->currency, false) }}</h2>
                                        </div>
                                        <div class="widget-icon">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                    </div>
                                    <hr class="my-3 border-white" style="opacity: 0.2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="mb-0 text-white-80 fw-bold">Số dư thành viên</p>
                                        <h4 class="mb-0 text-white fw-bold">{{ convert_currency($statics['users']['balance'] ?? 0, Auth::user()->currency, false) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card border border-info rounded-3 shadow-none mb-0 h-100" style="background: rgba(0, 207, 232, 0.05);">
                                        <div class="card-body p-4 d-flex align-items-center">
                                            <div class="widget-icon-light bg-info text-white me-3 shadow-sm">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted small text-uppercase fw-bold">Nạp hôm nay</p>
                                                <h4 class="mb-0 text-info fw-bold">{{ convert_currency($statics['users']['total_deposit_today'] ?? 0, Auth::user()->currency, false) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border border-success rounded-3 shadow-none mb-0 h-100" style="background: rgba(40, 199, 111, 0.05);">
                                        <div class="card-body p-4 d-flex align-items-center">
                                            <div class="widget-icon-light bg-success text-white me-3 shadow-sm">
                                                <i class="fas fa-calendar-week"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted small text-uppercase fw-bold">Nạp tuần này</p>
                                                <h4 class="mb-0 text-success fw-bold">{{ convert_currency($statics['users']['total_deposit_week'] ?? 0, Auth::user()->currency, false) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border border-primary rounded-3 shadow-none mb-0 h-100" style="background: rgba(115, 103, 240, 0.05);">
                                        <div class="card-body p-4 d-flex align-items-center">
                                            <div class="widget-icon-light bg-primary text-white me-3 shadow-sm">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted small text-uppercase fw-bold">Nạp tháng {{ now()->format('m') }}</p>
                                                <h4 class="mb-0 text-primary fw-bold">{{ convert_currency($statics['users']['total_deposit_month'] ?? 0, Auth::user()->currency, false) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="col-12 mb-4">
            <div class="card stat-card border-0">
                <div class="card-header bg-white pt-4">
                    <h5 class="card-title fw-bold mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>Tổng quan vận hành hệ thống</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-warning" style="background: rgba(255, 159, 67, 0.05);">
                                <div class="widget-icon-light bg-warning text-white me-3 shadow-sm"><i class="fas fa-clock fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">Chờ xử lý</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['pending'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-info" style="background: rgba(0, 207, 232, 0.05);">
                                <div class="widget-icon-light bg-info text-white me-3 shadow-sm"><i class="fas fa-sync fa-spin fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">Đang Xử lý</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['processing'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-primary" style="background: rgba(115, 103, 240, 0.05);">
                                <div class="widget-icon-light bg-primary text-white me-3 shadow-sm"><i class="fas fa-running fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">Đang chạy</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['inprogress'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-success" style="background: rgba(40, 199, 111, 0.05);">
                                <div class="widget-icon-light bg-success text-white me-3 shadow-sm"><i class="fas fa-check-double fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">Hoàn thành</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['completed'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-secondary" style="background: rgba(108, 117, 125, 0.05);">
                                <div class="widget-icon-light bg-secondary text-white me-3 shadow-sm"><i class="fas fa-hourglass-half fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">HT 1 phần</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['partial'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 col-6">
                            <div class="d-flex align-items-center p-3 rounded-3 border border-danger" style="background: rgba(234, 84, 85, 0.05);">
                                <div class="widget-icon-light bg-danger text-white me-3 shadow-sm"><i class="fas fa-times-circle fs-6"></i></div>
                                <div>
                                    <div class="small text-muted fw-bold">Đã hủy</div>
                                    <div class="h4 mb-0 fw-bold">{{ number_format($statics['orders']['canceled'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
"""

rest_of_file = content[content.find('        {{-- [NEW] Row 1: Charts --}}'):]
new_content += rest_of_file

with open('d:/smm_panel_v3/resources/views/admin/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Dashboard v2 rewritten successfully.")
