@extends('client.layouts.master')
@section('title', 'Tạo Đơn Cron Job')
@section('content')

<div class="row">
    <!-- Purchase Form (Left Side) -->
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h6 class="card-title text-white mb-0 fw-bold"><i class="fas fa-plus"></i> THÊM LINK CRON</h6>
                <a href="#history" class="btn btn-sm btn-danger"><i class="fas fa-history"></i> LỊCH SỬ CRON</a>
            </div>
            <div class="card-body">
                <form action="{{ route('client.cron.store') }}" method="POST" id="buyCronForm">
                    @csrf
                    
                    <!-- Section: Cấu hình -->
                    <fieldset class="border rounded-3 p-3 mb-4 position-relative" style="border-color: #3b82f6 !important;">
                        <legend class="float-none w-auto px-2 fs-6 fw-bold text-primary" style="margin-left: 10px;">Cấu hình</legend>
                        
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label text-muted fw-bold">Liên kết cần CRON (*)</label>
                            <div class="col-md-9">
                                <input type="url" name="url" class="form-control bg-light" placeholder="https://" required>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label text-muted fw-bold">Vòng lặp (*)</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" name="loop_time" class="form-control bg-light text-center" value="60" min="30" required>
                                    <span class="input-group-text bg-light text-muted">giây</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label text-muted fw-bold">Phương thức (*)</label>
                            <div class="col-md-9">
                                <select name="method" class="form-select bg-light text-center">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Section: Server -->
                    <fieldset class="border rounded-3 p-3 mb-4 position-relative" style="border-color: #3b82f6 !important;">
                        <legend class="float-none w-auto px-2 fs-6 fw-bold text-primary" style="margin-left: 10px;">Server</legend>
                        
                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label text-muted fw-bold">Máy chủ (*)</label>
                            <div class="col-md-9">
                                <select name="server_id" class="form-select bg-light" onchange="calculateSimplePrice(this)">
                                    @foreach($servers as $server)
                                        <option value="{{ $server->id }}" data-price="{{ $server->price }}">
                                            {{ $server->name }} - {{ number_format($server->price) }}đ
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-md-3 col-form-label text-muted fw-bold">Thời hạn (*)</label>
                            <div class="col-md-9">
                                <select name="duration" class="form-select bg-light" onchange="calculateSimplePrice(this)">
                                    <option value="1">1 Tháng</option>
                                    <option value="3">3 Tháng</option>
                                    <option value="6">6 Tháng</option>
                                    <option value="12">12 Tháng</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Total & Submit -->
                    <div class="text-center mt-4">
                        <div class="mb-3 fs-5">
                            Số tiền cần thanh toán: <span class="text-danger fw-bold" id="simple_total_price">0đ</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fs-6 fw-bold text-uppercase rounded-3">
                            <i class="fas fa-shopping-cart me-2"></i> Thanh toán
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Notes (Right Side) -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="card-title text-white mb-0 fw-bold"><i class="fas fa-exclamation-triangle"></i> LƯU Ý</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-light border-0 p-0 text-muted small" style="line-height: 1.8;">
                    <p class="mb-2"><strong class="text-danger">Quy định về việc sử dụng hệ thống CRON:</strong></p>
                    <ul class="ps-3 mb-0">
                        <li class="mb-2"><strong>Không kích hoạt Firewall đối với các liên kết CRON:</strong> Việc bật Firewall có thể gây gián đoạn hệ thống và ngăn cản CRON thực hiện nhiệm vụ.</li>
                        <li class="mb-2"><strong>Sử dụng hệ thống CRON đúng mục đích:</strong> Chúng tôi có quyền tạm ngưng hoặc hủy vĩnh viễn các liên kết CRON vi phạm, đặc biệt trong các trường hợp lạm dụng gây ảnh hưởng đến hệ thống. Các trường hợp này sẽ không được hoàn tiền.</li>
                        <li class="mb-0"><strong>Cung cấp chính xác liên kết CRON:</strong> Đảm bảo thông tin đường dẫn liên kết, bao gồm tiền tố "https://..." là chính xác.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- History Header Anchor -->
    <div id="history"></div>

    <!-- Danh sách Cron (Main UI) -->
    <div class="col-md-12">
        <div class="card border-top-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title text-white mb-0"><i class="fas fa-list-ul"></i> LỊCH SỬ CRON</h5>
                <div>
                     
                </div>
            </div>
            <div class="card-body">
                <!-- Filters Form -->
                <form action="{{ route('client.cron.index') }}" method="GET" id="filterForm">
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <input type="text" name="keyword" class="form-control" placeholder="Link CRON" value="{{ request('keyword') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang chạy</option>
                                <option value="stopped" {{ request('status') == 'stopped' ? 'selected' : '' }}>Tạm dừng</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                            </select>
                        </div>
                         <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('client.cron.index') }}" class="btn btn-light border w-100"><i class="fas fa-trash"></i> Bỏ lọc</a>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold">SHOW:</span>
                            <select name="limit" class="form-select d-inline-block w-auto py-1" onchange="this.form.submit()">
                                <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        <div>
                             <span class="fw-bold">SORT BY DATE:</span>
                             <select name="sort_by" class="form-select d-inline-block w-auto py-1" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th width="150">Thao tác</th>
                                <th>Link CRON</th>
                                <th>Cấu hình</th>
                                <th>Trạng thái</th>
                                <th>Thực hiện lần cuối</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($crons as $cron)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input checkItem" type="checkbox" value="{{ $cron->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($cron->status == 'active')
                                            <button onclick="toggleStatus({{ $cron->id }}, 'active')" class="btn btn-sm btn-warning" title="Tạm dừng"><i class="fas fa-pause"></i></button>
                                        @else
                                            <button onclick="toggleStatus({{ $cron->id }}, 'stopped')" class="btn btn-sm btn-success" title="Kích hoạt"><i class="fas fa-play"></i></button>
                                        @endif
                                        <button onclick="editCron({{ $cron }})" class="btn btn-sm btn-primary" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                                        <button onclick="viewLogs({{ $cron->id }})" class="btn btn-sm btn-dark" title="Lịch sử"><i class="fas fa-history"></i></button>
                                        <a href="#" onclick="renewCron({{ $cron }}, {{ $cron->server->price }})" class="btn btn-sm btn-success" title="Gia hạn"><i class="fas fa-redo"></i></a>
                                        <button onclick="confirmDelete({{ $cron->id }})" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                                <td style="max-width: 300px;">
                                    <div class="text-break fw-bold text-primary">{{ $cron->url }}</div>
                                </td>
                                <td>
                                    <div>Vòng lặp: <b>{{ $cron->loop_time }} giây</b></div>
                                    <div>Method: <b>{{ $cron->method }}</b></div>
                                </td>
                                <td>
                                    @if($cron->status == 'active' && !$cron->expired_at->isPast())
                                        <span class="badge bg-success">Đang chạy</span>
                                    @elseif($cron->expired_at->isPast())
                                        <span class="badge bg-secondary">Hết hạn</span>
                                    @else
                                        <span class="badge bg-danger">{{ $cron->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cron->last_run_at)
                                        <div>{{ $cron->last_run_at->format('Y-m-d H:i:s') }}</div>
                                        <div>
                                            @if($cron->last_status_code >= 200 && $cron->last_status_code < 300)
                                                <span class="badge bg-success">Success {{ $cron->last_status_code }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Code {{ $cron->last_status_code }}</span>
                                            @endif
                                            <span class="text-muted small">({{ $cron->last_duration ?? 0 }}s)</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Chưa chạy</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">Ngày tạo: {{ $cron->created_at->format('Y-m-d H:i:s') }}</div>
                                    <div class="small text-danger">Hết hạn: {{ $cron->expired_at->format('Y-m-d H:i:s') }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Chưa có dữ liệu</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex mt-3 gap-2">
                    <button class="btn btn-warning btn-sm text-white" onclick="bulkAction('stopped')"><i class="fas fa-pause"></i> Tạm dừng</button>
                    <button class="btn btn-info btn-sm text-white" onclick="bulkAction('active')"><i class="fas fa-play"></i> Kích hoạt</button>
                    <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')"><i class="fas fa-trash"></i> Xóa</button>
                </div>
                <div class="mt-3">
                     {{ $crons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculateSimplePrice() {
        const form = document.querySelector('form[action="{{ route('client.cron.store') }}"]');
        const serverSelect = form.querySelector('select[name="server_id"]');
        const durationSelect = form.querySelector('select[name="duration"]');
        const priceDisplay = document.getElementById('simple_total_price');

        const selectedServer = serverSelect.options[serverSelect.selectedIndex];
        const price = parseInt(selectedServer.getAttribute('data-price') || 0);
        const months = parseInt(durationSelect.value);

        const total = price * months;
        priceDisplay.innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', () => {
        calculateSimplePrice();
        @if(session('success'))
            notify("{{ session('success') }}", "success");
        @endif
        @if(session('error'))
            notify("{{ session('error') }}", "error");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                notify("{{ $error }}", "error");
            @endforeach
        @endif
    });

    // Check All Logic
    document.getElementById('checkAll').addEventListener('change', function() {
        let isChecked = this.checked;
        document.querySelectorAll('.checkItem').forEach(item => {
            item.checked = isChecked;
        });
    });

    // Bulk Action Function
    function bulkAction(status) {
        let ids = [];
        document.querySelectorAll('.checkItem:checked').forEach(item => {
            ids.push(item.value);
        });

        if (ids.length === 0) {
            notify('Vui lòng chọn ít nhất một Cron Job.', 'error');
            return;
        }

        let actionText = '';
        let url = '';
        let confirmColor = '#3085d6';

        if (status === 'delete') {
            actionText = 'XÓA';
            url = "{{ route('client.cron.bulk_delete') }}";
            confirmColor = '#d33';
        } else {
            actionText = status === 'active' ? 'Kích hoạt' : 'Tạm dừng';
            url = "{{ route('client.cron.bulk_status') }}";
        }
        
        Swal.fire({
            title: 'Xác nhận',
            text: `Bạn có chắc chắn muốn ${actionText} ${ids.length} Cron Job đã chọn?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy',
            confirmButtonColor: confirmColor
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        ids: ids,
                        status: status, // status is ignored by bulkDelete but harmless
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        notify(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        notify('Lỗi: ' + (xhr.responseJSON.message || 'Có lỗi xảy ra'), 'error');
                    }
                });
            }
        });
    }

    // Toggle Status Function
    function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'stopped' : 'active';
        const actionText = newStatus === 'active' ? 'Kích hoạt' : 'Tạm dừng';
        
        Swal.fire({
            title: 'Xác nhận',
            text: `Bạn có chắc chắn muốn ${actionText} Cron Job này?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/cron/status/${id}/${newStatus}`;
            }
        });
    }

    // Confirm Delete
    function confirmDelete(id) {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: "Bạn có chắc chắn muốn xóa Cron Job này? Hành động này không thể hoàn tác.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/cron/delete/${id}`;
            }
        });
    }

    // Edit Cron Function
    function editCron(cron) {
        document.getElementById('edit_url').value = cron.url;
        document.getElementById('edit_loop_time').value = cron.loop_time;
        document.getElementById('edit_method').value = cron.method;
        document.getElementById('editCronForm').action = "/cron/update/" + cron.id;
        
        var editModal = new bootstrap.Modal(document.getElementById('editCronModal'));
        editModal.show();
    }

    // Renew Cron Function
    function renewCron(cron, pricePerMonth) {
        document.getElementById('renewCronForm').action = "/cron/renew/" + cron.id;
        document.getElementById('renew_price_per_month').value = pricePerMonth;
        calculateRenewPrice();
        
        var renewModal = new bootstrap.Modal(document.getElementById('renewCronModal'));
        renewModal.show();
    }

    function calculateRenewPrice() {
        const price = parseInt(document.getElementById('renew_price_per_month').value || 0);
        const months = parseInt(document.getElementById('renew_duration').value);
        const total = price * months;
        document.getElementById('renew_total_price').innerText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }

    // View Logs Function
    function viewLogs(cronId) {
        const tbody = document.getElementById('logTableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Đang tải...</td></tr>';
        
        var logModal = new bootstrap.Modal(document.getElementById('logModal'));
        logModal.show();

        fetch(`/cron/logs/${cronId}`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Chưa có lịch sử chạy</td></tr>';
                    return;
                }
                data.data.forEach(log => {
                    const statusBadge = log.status_code >= 200 && log.status_code < 300 
                        ? `<span class="badge bg-success">${log.status_code}</span>` 
                        : `<span class="badge bg-danger">${log.status_code}</span>`;
                        
                    const row = `
                        <tr>
                            <td>${log.created_at}</td>
                            <td>${statusBadge}</td>
                            <td>${log.duration}s</td>
                            <td><textarea class="form-control form-control-sm" rows="1" readonly>${log.response_body || ''}</textarea></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
            });
    }
</script>

<!-- Edit Modal -->
<div class="modal fade" id="editCronModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="editCronForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa Cấu hình Cron</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link CRON</label>
                        <input type="url" name="url" id="edit_url" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Vòng lặp (giây)</label>
                            <input type="number" name="loop_time" id="edit_loop_time" class="form-control" min="30" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Method</label>
                            <select name="method" id="edit_method" class="form-select">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Renew Modal -->
<div class="modal fade" id="renewCronModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" id="renewCronForm">
            @csrf
            <input type="hidden" id="renew_price_per_month">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gia hạn Cron Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thời gian gia hạn</label>
                        <select name="duration" id="renew_duration" class="form-select" onchange="calculateRenewPrice()">
                            <option value="1">1 Tháng</option>
                            <option value="3">3 Tháng</option>
                            <option value="6">6 Tháng</option>
                            <option value="12">12 Tháng</option>
                        </select>
                    </div>
                    <div class="fs-5">Tổng tiền: <span class="text-danger fw-bold" id="renew_total_price">0đ</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Thanh toán gia hạn</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logs Modal -->
<div class="modal fade" id="logModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lịch sử chạy (20 lần gần nhất)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody">
                            <!-- Logs injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
    .border-top-primary { border-top: 3px solid #0d6efd; }
    .border-top-danger { border-top: 3px solid #dc3545; }
    .table th { white-space: nowrap; }
</style>
@endsection
