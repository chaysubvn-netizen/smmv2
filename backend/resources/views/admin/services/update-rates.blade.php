@extends('admin.layouts.app')
@section('title', 'Cập nhật giá dịch vụ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách cập nhật giá</h4>
                    <div class="card-tool">
                        <button type="button" class="btn btn-danger-light ms-2" id="btn-delete-all-rates" onclick="deleteAllRates()">
                            <i class="bx bx-trash-alt"></i> Xóa tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Thao tác</th>
                                    <th class="text-center">Tên dịch vụ</th>
                                    <th class="text-center">Giá cũ</th>
                                    <th class="text-center">Giá mới</th>
                                    <th class="text-center">Tăng/Giảm</th>
                                    <th class="text-center">Thời gian cập nhật</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'RateUpdate']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-danger-gradient" onclick="deleteData(['${row.id}'], 'RateUpdate')">
                                    <i class="bx bx-trash"></i>
                                    </button>
                            `;
                        }
                    },
                    {
                        data: 'service',
                        name: 'service',
                        render: function(data, type, row) {
                            return `<span class="text-primary">[${data.id}] - ${data.name}</span>`;
                        }
                    },
                    {
                        data: 'rate_old_format',
                        name: 'rate_old_format',
                        render: function(data, type, row) {
                            return (data);
                        }
                    },
                    {
                        data: 'rate_new_format',
                        name: 'rate_new_format',
                        render: function(data, type, row) {
                            return (data);
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            if (data == 'increase') {
                                return `<span class="badge bg-success">Tăng</span>`;
                            } else if (data == 'decrease') {
                                return `<span class="badge bg-danger">Giảm</span>`;
                            } else if (data == 'add') {
                                return `<span class="badge bg-secondary">Thêm</span>`;
                            } else if (data == 'delete') {
                                return `<span class="badge bg-warning">Xóa</span>`;
                            } else {
                                return `<span class="badge bg-info">Khác</span>`;
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
            });
        });

        function deleteAllRates() {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa tất cả lịch sử cập nhật giá trên trang web này và không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý, xóa tất cả!',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.loaddata.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            model: 'RateUpdate',
                            type: 'all'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                );
                                $('.table').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection
