<div class="row">
    <div class="col-md-12">
        <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true">
            @csrf
            <div class="mb-3">
                <label class="form-label">Trạng thái Anti-DDoS</label>
                <select name="antiddos_status" class="form-control">
                    <option value="inactive" {{ $config->antiddos_status == 'inactive' ? 'selected' : '' }}>Tắt</option>
                    <option value="active" {{ $config->antiddos_status == 'active' ? 'selected' : '' }}>Bật (Chế độ bảo vệ)</option>
                </select>
                <small class="text-muted">Khi bật, hệ thống sẽ kiểm tra danh sách IP bị chặn và có thể áp dụng các biện pháp bảo vệ nghiêm ngặt hơn.</small>
            </div>
            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
            </div>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh sách IP bị chặn</h5>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addIpModal">
                <i class="fas fa-plus me-1"></i>Thêm IP mới
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="ip-block-table">
                <thead>
                    <tr>
                        <th>IP</th>
                        <th>Lý do</th>
                        <th>Thời gian</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Dữ liệu sẽ được load qua Ajax hoặc render trực tiếp --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal thêm IP -->
<div class="modal fade" id="addIpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add-ip-form">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm IP vào danh sách chặn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ IP</label>
                        <input type="text" name="ip" class="form-control" placeholder="123.123.123.123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lý do chặn</label>
                        <input type="text" name="reason" class="form-control" placeholder="Spam, DDoS attack...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm chặn</button>
                </div>
            </form>
        </div>
    </div>
</div>
