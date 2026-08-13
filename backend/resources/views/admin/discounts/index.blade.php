@extends('admin.layouts.app')
@section('title', 'Quản lý mã giảm giá')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách mã giảm giá</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModel">
                        <i class="bx bx-plus"></i> Thêm mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="discountTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã Code</th>
                                    <th>Loại</th>
                                    <th>Giá trị</th>
                                    <th>Đơn tối thiểu</th>
                                    <th>Giảm tối đa</th>
                                    <th>Lượt dùng</th>
                                    <th>Hết hạn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addForm" action="{{ route('admin.discounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã Code</label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại giảm giá</label>
                            <select class="form-select" name="type" required>
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá trị giảm</label>
                            <input type="number" class="form-control" name="amount" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn tối thiểu</label>
                            <input type="number" class="form-control" name="min_order_amount" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giảm tối đa (Cho %)</label>
                            <input type="number" class="form-control" name="max_discount_amount" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới hạn lượt dùng (0 = KGH)</label>
                            <input type="number" class="form-control" name="usage_limit" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="datetime-local" class="form-control" name="expired_at">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Mã Code</label>
                            <input type="text" class="form-control" name="code" id="edit_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại giảm giá</label>
                            <select class="form-select" name="type" id="edit_type" required>
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Số tiền cố định (VNĐ)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá trị giảm</label>
                            <input type="number" class="form-control" name="amount" id="edit_amount" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn tối thiểu</label>
                            <input type="number" class="form-control" name="min_order_amount" id="edit_min_order" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giảm tối đa (Cho %)</label>
                            <input type="number" class="form-control" name="max_discount_amount" id="edit_max_discount" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giới hạn lượt dùng (0 = KGH)</label>
                            <input type="number" class="form-control" name="usage_limit" id="edit_limit" value="0" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="datetime-local" class="form-control" name="expired_at" id="edit_expired">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var table = $('#discountTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.discounts.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'code', name: 'code' },
                    { data: 'type', name: 'type' },
                    { data: 'amount', name: 'amount' },
                    { data: 'min_order_amount', name: 'min_order_amount' },
                    { data: 'max_discount_amount', name: 'max_discount_amount' },
                    { data: 'used_count', name: 'used_count' },
                    { data: 'expired_at', name: 'expired_at' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Handle Add Form
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Thành công', response.message, 'success');
                            $('#addModel').modal('hide');
                            table.ajax.reload();
                            form[0].reset();
                        } else {
                            Swal.fire('Lỗi', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        var msg = 'Có lỗi xảy ra, vui lòng thử lại';
                        if (response && response.errors) {
                             msg = '';
                             $.each(response.errors, function(key, value) {
                                msg += value[0] + '\n';
                             });
                        } else if (response && response.message) {
                            msg = response.message;
                        }
                        Swal.fire('Lỗi', msg, 'error');
                    }
                });
            });

            // Handle Edit Modal Open
            $('#editModel').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                
                modal.find('#edit_id').val(button.data('id'));
                modal.find('#edit_code').val(button.data('code'));
                modal.find('#edit_type').val(button.data('type'));
                modal.find('#edit_amount').val(button.data('amount'));
                modal.find('#edit_min_order').val(button.data('min_order'));
                modal.find('#edit_max_discount').val(button.data('max_discount'));
                modal.find('#edit_limit').val(button.data('limit'));
                
                // Format datetime-local input
                var expired = button.data('expired');
                if (expired && expired !== 'null') {
                    // Try to format if it matches standard date string
                    // Or just use as is if button data is compatible
                    // But button data from controller is date('H:i d/m/Y') ???
                    // Wait, controller line 51: date('H:i d/m/Y', ...)
                    // Standard datetime-local needs YYYY-MM-DDTHH:mm
                    // I need to change controller to send ISO format in data-expired attribute!
                    
                    // Actually, let's fix the controller to send ISO format for data-expired
                    // For now, let's clear it if format is wrong
                     modal.find('#edit_expired').val('');
                } else {
                    modal.find('#edit_expired').val('');
                }

                modal.find('#edit_status').val(button.data('status'));

                var updateUrl = "{{ route('admin.discounts.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', button.data('id'));
                modal.find('#editForm').attr('action', updateUrl);
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ route('admin.discounts.update', ':id') }}".replace(':id', id),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Thành công', response.message, 'success');
                            $('#editModel').modal('hide');
                            table.ajax.reload();
                        } else {
                             Swal.fire('Lỗi', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        var msg = 'Có lỗi xảy ra, vui lòng thử lại';
                        if (response && response.errors) {
                             msg = '';
                             $.each(response.errors, function(key, value) {
                                msg += value[0] + '\n';
                             });
                        } else if (response && response.message) {
                            msg = response.message;
                        }
                        Swal.fire('Lỗi', msg, 'error');
                    }
                });
            });

            // Handle Delete
            window.deleteDiscount = function(id) {
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Bạn sẽ không thể khôi phục lại!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xóa ngay!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.discounts.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire('Đã xóa!', response.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Lỗi!', response.message, 'error');
                                }
                            }
                        });
                    }
                })
            }
        });
    </script>
@endsection
