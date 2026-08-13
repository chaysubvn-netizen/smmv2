@extends('admin.layouts.app')
@section('title', 'Quản lý đơn hàng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">
                        Danh sách đơn hàng
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-orders">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tài khoản</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Thanh toán</th>
                                    <th>Giảm giá</th>
                                    <th>Dữ liệu thêm</th>
                                    <th>Dữ liệu</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
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
            let table = $('#table-orders').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'OrderProduct']) }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'user',
                        name: 'user',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${data.username}</span>`;
                        }
                    },
                    {
                        data: 'product',
                        name: 'product',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${data.name}</span>`;
                        }
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="text-primary">${formatNumber(data)}</span>`;
                        }
                    },
                    {
                        data: 'price_format',
                        name: 'price_format',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="text-danger">${(data)}</span>`;
                        }
                    },
                    {
                        data: 'total_format',
                        name: 'total_format',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="text-danger">${(data)}</span>`;
                        }
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'data_input',
                        name: 'data_input',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<textarea class="form-control" rows="3" readonly>${data}</textarea>`;
                        }
                    },
                    {
                        data: 'data',
                        name: 'data',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<textarea class="form-control" rows="3" readonly>${data}</textarea>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == 'success') {
                                return '<span class="badge bg-success">Đã xử lý</span>';
                            } else {
                                return '<span class="badge bg-danger">Chưa xử lý</span>';
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
                    [0, "desc"]
                ],
            });
        })
    </script>
@endsection
