@extends('admin.layouts.app') 

@section('title', 'Quản Lý Child Panel - Admin')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1">Quản lý Child Panel</h4>
                    <p class="text-muted mb-0">Quản lý các website đại lý, cấu hình hệ thống và theo dõi doanh thu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s btn-light-primary">
                                <i class="ti ti-server f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Tổng Child Panel</p>
                            <h5 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h5>
                            <small class="text-muted">
                                <span class="text-success">{{ number_format($stats['active'] ?? 0) }} hoạt động</span>
                                · <span class="text-danger">{{ number_format($stats['expired'] ?? 0) }} hết hạn</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s btn-light-success">
                                <i class="ti ti-building-store f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Web Con <span class="badge bg-light-success text-success ms-1">Cấp 1</span></p>
                            <h5 class="mb-0 text-success">{{ number_format($stats['direct_child'] ?? 0) }}</h5>
                            <small class="text-muted">Tạo từ <strong>{{ $site->domain }}</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s btn-light-warning">
                                <i class="ti ti-building f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Web Cháu/Chắt <span class="badge bg-light-warning text-warning ms-1">Cấp 2+</span></p>
                            <h5 class="mb-0 text-warning">{{ number_format($stats['grandchild'] ?? 0) }}</h5>
                            <small class="text-muted">Tạo từ các Web Con</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s btn-light-info">
                                <i class="ti ti-coin f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Tổng doanh thu thuê</p>
                            <h5 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0) }}₫</h5>
                            <small class="text-muted">Tổng cộng tất cả cấp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="childPanelTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" role="tab" aria-controls="list" aria-selected="true">
                                <i class="ti ti-list me-1"></i>Danh sách đại lý
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" role="tab" aria-controls="settings" aria-selected="false">
                                <i class="ti ti-settings me-1"></i>Cấu hình hệ thống
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab 1: List -->
                        <div class="tab-pane fade show active" id="list" role="tabpanel">
                            <!-- Filters -->
                            <form method="GET" action="{{ route('admin.childpanels.index') }}" class="mb-4">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold mb-1">Tìm kiếm</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text"><i class="ti ti-search"></i></span>
                                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Domain, email, username..." value="{{ $search }}">
                                        </div>
                                    </div>
                                    @if($isMainSite)
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Cấp bậc</label>
                                        <select name="level" class="form-select form-select-sm">
                                            <option value="all"       {{ $level == 'all'       ? 'selected' : '' }}>Tất cả cấp</option>
                                            <option value="child"     {{ $level == 'child'     ? 'selected' : '' }}>🏪 Web Con (Cấp 1)</option>
                                            <option value="grandchild"{{ $level == 'grandchild'? 'selected' : '' }}>🏢 Web Cháu (Cấp 2)</option>
                                            <option value="deeper"    {{ $level == 'deeper'    ? 'selected' : '' }}>🏗️ Web Chắt/Chít (Cấp 3+)</option>
                                        </select>
                                    </div>
                                    @endif
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Trạng thái</label>
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="all"     {{ $status == 'all'     ? 'selected' : '' }}>Tất cả trạng thái</option>
                                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                            <option value="active"  {{ $status == 'active'  ? 'selected' : '' }}>Đang hoạt động</option>
                                            <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold mb-1">Hiển thị</label>
                                        <select name="per_page" class="form-select form-select-sm">
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 bản ghi</option>
                                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 bản ghi</option>
                                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 bản ghi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="ti ti-filter me-1"></i>Lọc
                                        </button>
                                    </div>
                                    <div class="col-md-1">
                                        <a href="{{ route('admin.childpanels.index') }}" class="btn btn-light-secondary btn-sm w-100" title="Reset">
                                            <i class="ti ti-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Domain & Nguồn gốc</th>
                                            <th class="text-center">Cấp bậc</th>
                                            <th class="text-center">Trạng thái</th>
                                            <th class="text-center">Thống kê nội bộ</th>
                                            <th class="text-center">Hết hạn</th>
                                            <th class="text-end">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($panels) > 0)
                                            @foreach($panels as $panel)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avtar avtar-s btn-light-{{ $panel->hierarchy_color }} flex-shrink-0">
                                                                <i class="ti {{ $panel->hierarchy_icon }} f-18"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1 fw-bold">{{ $panel->domain }}</h6>
                                                                {{-- Hierarchy breadcrumb --}}
                                                                <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                                                    <span class="badge bg-light text-secondary border" style="font-size:.7rem;">
                                                                        <i class="ti ti-home me-1"></i>{{ $panel->parent_domain }}
                                                                    </span>
                                                                    <i class="ti ti-chevron-right text-muted" style="font-size:.7rem;"></i>
                                                                    <span class="badge bg-light-{{ $panel->hierarchy_color }} text-{{ $panel->hierarchy_color }}" style="font-size:.7rem;">
                                                                        {{ $panel->domain }}
                                                                    </span>
                                                                </div>
                                                                <small class="text-muted">
                                                                    <i class="ti ti-user me-1"></i>{{ $panel->user->username ?? 'Unknown' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light-{{ $panel->hierarchy_color }} text-{{ $panel->hierarchy_color }} border border-{{ $panel->hierarchy_color }} d-block mb-1">
                                                            {{ $panel->hierarchy_label }}
                                                        </span>
                                                        <small class="text-muted" style="font-size:.7rem;">Cấp {{ $panel->hierarchy_level }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light-{{ $panel->status_badge_color }} text-{{ $panel->status_badge_color }}">
                                                            {{ $panel->status_display_name }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <div class="px-2 py-1 bg-light rounded text-center" style="min-width: 60px;" title="Tổng đơn hàng">
                                                                <span class="d-block fw-bold small">{{ number_format($panel->total_orders ?? 0) }}</span>
                                                                <small class="text-muted" style="font-size: 0.65rem;">Đơn</small>
                                                            </div>
                                                            <div class="px-2 py-1 bg-light-success rounded text-center" style="min-width: 70px;" title="Tổng nạp">
                                                                <span class="d-block fw-bold small">{{ number_format($panel->total_deposits ?? 0) }}₫</span>
                                                                <small class="text-muted" style="font-size: 0.65rem;">Tổng nạp</small>
                                                            </div>
                                                            <div class="px-2 py-1 bg-light-primary rounded text-center" style="min-width: 70px;" title="Tổng chi">
                                                                <span class="d-block fw-bold small">{{ number_format($panel->total_spent ?? 0) }}₫</span>
                                                                <small class="text-muted" style="font-size: 0.65rem;">Tổng chi</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($panel->expires_at)
                                                            <span class="d-block small fw-bold">{{ $panel->expires_at->format('d/m/Y') }}</span>
                                                            <span class="badge {{ $panel->days_remaining > 7 ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} small">
                                                                Còn {{ $panel->days_remaining }} ngày
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-light-secondary dropdown-toggle hide-arrow" type="button" data-bs-toggle="dropdown">
                                                                <i class="ti ti-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('admin.childpanels.login', $panel->id) }}" target="_blank">
                                                                        <i class="ti ti-login me-2 text-primary"></i>Truy cập Admin
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" data-action="show-statistics" data-id="{{ $panel->id }}">
                                                                        <i class="ti ti-chart-bar me-2 text-info"></i>Thống kê chi tiết
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                @if($panel->status !== 'active')
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" data-action="update-status" data-id="{{ $panel->id }}" data-status="active">
                                                                            <i class="ti ti-check me-2 text-success"></i>Kích hoạt
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                @if($panel->status !== 'expired')
                                                                    <li>
                                                                        <a class="dropdown-item" href="#" data-action="update-status" data-id="{{ $panel->id }}" data-status="expired">
                                                                            <i class="ti ti-clock-off me-2 text-warning"></i>Hết hạn
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#" data-action="delete-panel" data-id="{{ $panel->id }}" data-domain="{{ $panel->domain }}">
                                                                        <i class="ti ti-trash me-2"></i>Xóa sạch (CF + cPanel + DB)
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">Không có dữ liệu Child Panel nào.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">Đang xem {{ $panels->firstItem() }} đến {{ $panels->lastItem() }} của {{ $panels->total() }}</small>
                                {{ $panels->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                        <!-- Tab 2: Settings -->
                        <div class="tab-pane fade" id="settings" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center py-3 mb-4">
                                        <div class="avtar avtar-s btn-light-primary me-3 flex-shrink-0">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading fw-bold mb-1 text-primary">Hướng dẫn cấu hình dành cho Admin</h6>
                                            <ul class="mb-0 small text-muted list-unstyled">
                                                <li><i class="ti ti-circle-check text-success me-1"></i><strong>Bật hệ thống:</strong> Cho phép hoặc tạm dừng dịch vụ thuê Child Panel trên toàn hệ thống.</li>
                                                <li><i class="ti ti-circle-check text-success me-1"></i><strong>Giá thuê:</strong> @if($isMainSite) Bạn đang ở <strong>Site Mẹ</strong>, có thể điều chỉnh giá thuê hàng tháng. @else Bạn đang ở <strong>Child Panel</strong>, giá thuê được quy định bởi Site Mẹ và không thể thay đổi. @endif</li>
                                                <li><i class="ti ti-circle-check text-success me-1"></i><strong>Tự động hóa:</strong> Khi bật, hệ thống sẽ tự động trừ tiền duy trì từ số dư người dùng.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form id="settingsForm" method="POST" action="{{ route('admin.childpanels.settings') }}">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0 shadow-none mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0 fw-bold">Trạng thái hệ thống</h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="child_panel_enabled" name="child_panel_enabled" {{ ($site && $site->child_panel_enabled) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                <label class="form-label fw-bold">Giá thuê mỗi tháng (VNĐ)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control fw-bold text-primary {{ !$isMainSite ? 'bg-light' : '' }}" 
                                                        name="child_panel_monthly_price" 
                                                        value="{{ $mainSite->child_panel_monthly_price ?? 100000 }}"
                                                        {{ !$isMainSite ? 'readonly' : '' }}>
                                                    <span class="input-group-text {{ !$isMainSite ? 'bg-light' : '' }}">₫ / Tháng</span>
                                                </div>
                                                @if(!$isMainSite)
                                                    <div class="mt-2 small text-danger">
                                                        <i class="ti ti-alert-triangle me-1"></i>Chỉ <strong>Site Mẹ</strong> mới có quyền thay đổi giá thuê.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info border-0">
                                            <h6 class="alert-heading fw-bold mb-1"><i class="ti ti-info-circle me-1"></i>Hướng dẫn vận hành</h6>
                                            <p class="mb-0 small">Hệ thống sẽ tự động trừ tiền người dùng vào ngày hết hạn. Nếu số dư không đủ, trang web sẽ tự động chuyển sang trạng thái hết hạn.</p>
                                        </div>
                                    </div>
                                </div>

                                @if($isMainSite)
                                    <h5 class="mb-3 border-bottom pb-2 mt-4 text-primary">Cấu hình Tự động hóa (Cloudflare & cPanel)</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 fw-bold"><i class="ti ti-brand-cloudflare me-1"></i>Cloudflare Configuration</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">CF Account Email</label>
                                                        <input type="email" class="form-control" name="cloudflare_email" value="{{ $site->cloudflare_email }}" placeholder="admin@cloudflare.com">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Global API Key (để quản lý Zones)</label>
                                                        <input type="text" class="form-control" name="cloudflare_global_key" value="{{ $site->cloudflare_global_key }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">API Token (để xác thực chính)</label>
                                                        <input type="text" class="form-control" name="cloudflare_token" value="{{ $site->cloudflare_token }}">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 mb-3">
                                                            <label class="form-label">Account ID</label>
                                                            <input type="text" class="form-control" name="cloudflare_account_id" value="{{ $site->cloudflare_account_id }}">
                                                        </div>
                                                        <div class="col-6 mb-3">
                                                            <label class="form-label">IP Server (A Record)</label>
                                                            <input type="text" class="form-control" name="cloudflare_ip_host" value="{{ $site->cloudflare_ip_host }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold" id="hostingPanelTitle"><i class="ti ti-server me-1"></i>Hosting Configuration</h6>
                                                    <select name="hosting_panel_type" id="hostingPanelType" class="form-select form-select-sm w-auto">
                                                        <option value="cpanel" {{ ($site->hosting_panel_type ?? 'cpanel') == 'cpanel' ? 'selected' : '' }}>cPanel</option>
                                                        <option value="aapanel" {{ ($site->hosting_panel_type ?? 'cpanel') == 'aapanel' ? 'selected' : '' }}>aaPanel</option>
                                                    </select>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label" id="labelHostUrl">Host URL (kèm port)</label>
                                                        <input type="text" class="form-control" name="cpanel_host" id="inputHostUrl" value="{{ $site->cpanel_host }}" placeholder="https://....:2083">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label" id="labelHostUser">Username</label>
                                                        <input type="text" class="form-control" name="cpanel_user" value="{{ $site->cpanel_user }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label" id="labelHostPass">Password</label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="cpanel_pass" name="cpanel_pass" value="{{ $site->cpanel_pass }}">
                                                            <button class="btn btn-outline-secondary" type="button" data-action="toggle-password" data-target="cpanel_pass">
                                                                <i class="ti ti-eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const panelTypeSelect = document.getElementById('hostingPanelType');
                                                    const title = document.getElementById('hostingPanelTitle');
                                                    const labelUrl = document.getElementById('labelHostUrl');
                                                    const labelUser = document.getElementById('labelHostUser');
                                                    const labelPass = document.getElementById('labelHostPass');
                                                    const inputUrl = document.getElementById('inputHostUrl');
                                                    
                                                    function updatePanelUI() {
                                                        const type = panelTypeSelect.value;
                                                        if (type === 'cpanel') {
                                                            title.innerHTML = '<i class="ti ti-server me-1"></i>cPanel Hosting Configuration';
                                                            labelUrl.textContent = 'Host URL (kèm port)';
                                                            labelUser.textContent = 'cPanel Username';
                                                            labelPass.textContent = 'cPanel Password';
                                                            inputUrl.placeholder = 'https://....:2083';
                                                        } else {
                                                            title.innerHTML = '<i class="ti ti-server me-1"></i>aaPanel Hosting Configuration';
                                                            labelUrl.textContent = 'aaPanel URL (kèm port)';
                                                            labelUser.textContent = 'API Secret Key';
                                                            labelPass.textContent = 'Website ID';
                                                            inputUrl.placeholder = 'http://your-server-ip:7800';
                                                        }
                                                    }
                                                    
                                                    panelTypeSelect.addEventListener('change', updatePanelUI);
                                                    updatePanelUI();
                                                });
                                            </script>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                                        <i class="ti ti-device-floppy me-1"></i>Lưu cấu hình
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="child-panel-config" class="d-none"
        data-base-url="{{ url('/admin/childpanels') }}"
        data-settings-url="{{ route('admin.childpanels.settings') }}"></div>

    <!-- Statistics Modal -->
    <div class="modal fade" id="statisticsModal" tabindex="-1" aria-labelledby="statisticsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-s btn-light-info">
                            <i class="ti ti-chart-bar f-20"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="statisticsModalLabel">
                                Thống kê chi tiết
                            </h5>
                            <small class="text-muted" id="statsDomain"></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" id="statisticsBody">
                    <!-- Loading -->
                    <div id="statsLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2">Đang tải thống kê...</p>
                    </div>

                    <!-- Content -->
                    <div id="statsContent" style="display: none;">

                        <!-- Top 4 Summary Cards -->
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light-primary text-center h-100">
                                    <div class="card-body py-3 px-2">
                                        <div class="avtar avtar-s btn-light-primary mx-auto mb-2">
                                            <i class="ti ti-shopping-cart f-20"></i>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="statsOrdersTotal">0</h4>
                                        <small class="text-muted">Tổng đơn hàng</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light-success text-center h-100">
                                    <div class="card-body py-3 px-2">
                                        <div class="avtar avtar-s btn-light-success mx-auto mb-2">
                                            <i class="ti ti-coin f-20"></i>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-success" id="statsDeposits">0₫</h4>
                                        <small class="text-muted">Tổng nạp</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light-warning text-center h-100">
                                    <div class="card-body py-3 px-2">
                                        <div class="avtar avtar-s btn-light-warning mx-auto mb-2">
                                            <i class="ti ti-receipt f-20"></i>
                                        </div>
                                        <h4 class="mb-0 fw-bold text-warning" id="statsSpent">0₫</h4>
                                        <small class="text-muted">Tổng chi</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light-info text-center h-100">
                                    <div class="card-body py-3 px-2">
                                        <div class="avtar avtar-s btn-light-info mx-auto mb-2">
                                            <i class="ti ti-users f-20"></i>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="statsUsersTotal">0</h4>
                                        <small class="text-muted">Người dùng</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info + Time row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="card border h-100">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold"><i class="ti ti-info-circle me-1 text-primary"></i>Thông tin cơ bản</h6>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <table class="table table-sm table-borderless mb-0" style="table-layout:fixed;">
                                            <tr>
                                                <td class="text-muted small py-1" style="width:38%">Trạng thái</td>
                                                <td class="py-1"><span id="statsStatus" class="badge"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Chủ sở hữu</td>
                                                <td class="fw-semibold small py-1 text-truncate" id="statsOwner"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Admin</td>
                                                <td class="fw-semibold small py-1 text-truncate" id="statsAdmin"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Email</td>
                                                <td class="small py-1 text-truncate" id="statsEmail"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Phí thuê</td>
                                                <td class="fw-bold small py-1 text-primary" id="statsPrice"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border h-100">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold"><i class="ti ti-clock me-1 text-warning"></i>Thời gian & DNS</h6>
                                    </div>
                                    <div class="card-body py-2 px-3">
                                        <table class="table table-sm table-borderless mb-0" style="table-layout:fixed;">
                                            <tr>
                                                <td class="text-muted small py-1" style="width:38%">Ngày tạo</td>
                                                <td class="small py-1" id="statsCreated"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Hết hạn</td>
                                                <td class="small py-1" id="statsExpires"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Còn lại</td>
                                                <td class="py-1"><span id="statsDays" class="badge"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Cloudflare</td>
                                                <td class="py-1"><span id="statsCF" class="badge"></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small py-1">Nameserver</td>
                                                <td class="py-1" style="word-break:break-all;overflow-wrap:break-word;"><small id="statsNS" class="text-muted"></small></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Breakdown -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold"><i class="ti ti-shopping-cart me-1 text-primary"></i>Chi tiết đơn hàng</h6>
                                    </div>
                                    <div class="card-body py-0 px-3">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Hoàn thành</span>
                                                <span class="badge bg-success rounded-pill" id="statsOrdersCompleted">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Đang xử lý</span>
                                                <span class="badge bg-warning rounded-pill" id="statsOrdersPending">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Hủy / Hoàn</span>
                                                <span class="badge bg-danger rounded-pill" id="statsOrdersCancelled">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">7 ngày qua</span>
                                                <span class="badge bg-info rounded-pill" id="statsOrdersRecent">0</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold"><i class="ti ti-wallet me-1 text-success"></i>Chi tiết tài chính</h6>
                                    </div>
                                    <div class="card-body py-0 px-3">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Nạp chờ duyệt</span>
                                                <span class="fw-bold small text-warning" id="statsPendingDeposits">0₫</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Số dư users</span>
                                                <span class="fw-bold small" id="statsTotalBalance">0₫</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Doanh thu 7 ngày</span>
                                                <span class="fw-bold small text-success" id="statsRecentRevenue">0₫</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Lợi nhuận ước tính</span>
                                                <span class="fw-bold small" id="statsProfit">0₫</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold"><i class="ti ti-users me-1 text-info"></i>Chi tiết người dùng</h6>
                                    </div>
                                    <div class="card-body py-0 px-3">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Admin</span>
                                                <span class="badge bg-primary rounded-pill" id="statsUsersAdmin">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="small text-muted">Client</span>
                                                <span class="badge bg-info rounded-pill" id="statsUsersClient">0</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection




@push('scripts')
<script src="{{ asset('js/admin-child-panel.js') }}"></script>
@endpush
