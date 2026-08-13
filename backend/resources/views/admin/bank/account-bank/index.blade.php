@extends('admin.layouts.app')
@section('title', 'Thêm Ngân Hàng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách ngân hàng</h4>
                    <div class="">
                        <button type="button" class="btn btn-primary btn-primary-gradient btn-wave" data-bs-toggle="modal"
                            data-bs-target="#addPlatformModal">
                            <i class="bx bx-plus"></i>
                            <span>Thêm mới</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100 table-bordedred" id="table-banks">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Icon</th>
                                    <th>Tên ngân hàng</th>
                                    <th>Chủ tài khoản</th>
                                    <th>Số tài khoản</th>
                                    <th>Chi nhánh</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPlatformModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.banks.accounts.create') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm thông tin mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="icon" class="form-label">Ảnh/Icon</label>
                        <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                    </div>
                    <div class="form-group mb-3">
                        <label for="bank_name" class="form-label">Tên ngân hàng</label>
                        <select name="bank_name" id="bank_name" class="form-control">
                            <option value="mbbank">Mbbank</option>
                            <option value="vietcombank">Vietcombank</option>
                            <option value="acb">ACB</option>
                            <option value="viettinbank">Viettinbank</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bank_code" class="form-label">Mã ngân hàng (VietQR)</label>
                        <input type="text" class="form-control" id="bank_code" name="bank_code" placeholder="Ví dụ: MB, VCB, ICB, ACB...">
                        <small class="text-muted">Xem mã tại vietqr.io/danh-sach-bin-ngan-hang</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="account_number" class="form-label">Số tài khoản</label>
                        <input type="text" class="form-control" id="account_number" name="account_number"
                            placeholder="Nhập số tài khoản">
                    </div>
                    <div class="form-group mb-3">
                        <label for="account_name" class="form-label">Tên tài khoản</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Nhập tên tài khoản">
                    </div>
                    <div class="form-group mb-3">
                        <label for="branch" class="form-label">Chi nhánh</label>
                        <input type="text" class="form-control" id="branch" name="branch" placeholder="Nhập chi nhánh">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="form_status" class="form-control">
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngừng hoạt động</option>
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
@endsection
@section('script')
    <script>
        function deleteBankAccount(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                text: "Dữ liệu này sẽ không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, xóa nó!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.loaddata.delete') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: [id],
                            model: 'AccountBank'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                );
                                $('#table-banks').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Lỗi!',
                                'Đã có lỗi xảy ra trong quá trình xóa.',
                                'error'
                            );
                        }
                    });
                }
            })
        }

        $(document).ready(function() {
            let table = $("#table-banks").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'AccountBank']) }}",
                    type: "GET",
                },
                columns: [
                    {
                        data: "id",
                        name: "id",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                        render: function(data,type,row){
                            return `
                                <a href="${row?.edit_url}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger-gradient btn-sm" onclick="deleteBankAccount(${row.id})">
                                    <i class="bx bx-trash"></i>
                                </button>
                            `
                        }
                    },
                    {
                        data: "icon",
                        name: "icon",
                        render: function(data, type, row) {
                            return `<img src="${data}" alt="icon" style="width: 50px; height: 50px;">`;
                        },
                    },
                    {
                        data: "bank_name",
                        name: "bank_name",
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${data}</span>`;
                        },
                    },
                    {
                        data: "account_name",
                        name: "account_name",
                    },
                    {
                        data: "account_number",
                        name: "account_number",
                    },
                    {
                        data: "branch",
                        name: "branch",
                    },
                    {
                        data: "status",
                        name: "status",
                        render: function(data, type, row) {
                            return data == 'active' ? '<span class="badge bg-success">Kích hoạt</span>' : '<span class="badge bg-danger">Không kích hoạt</span>';
                        },
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format("DD/MM/YYYY");
                        },
                    },
                ],
            });
        });
    </script>
@endsection
