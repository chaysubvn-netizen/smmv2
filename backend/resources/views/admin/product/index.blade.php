@extends('admin.layouts.app')
@section('title', 'Danh sách sản phẩm')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Danh sách sản phẩm</h4>
                    <div class="">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus"></i>
                            <span>Thêm mới</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="table-product">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Tình trạng</th>
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
            let table = $('#table-product').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Product']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <a href="${row?.edit_url}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="${row?.stock_url}" class="btn btn-warning btn-sm">
                                    <i class="bx bx-box"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onClick="deleteData([${row?.id}], 'Product')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            `;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'price_format',
                        name: 'price_format',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary">${(data)}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data == 'active' ?
                                '<span class="badge bg-success">Hoạt động</span>' :
                                '<span class="badge bg-danger">Ngừng hoạt động</span>';
                        }
                    },
                    {
                        data: 'stock_count',
                        name: 'stock_count',
                        render: function(data, type, row) {
                            return data > 0 ?
                                `<span class="badge bg-success">${data} sản phẩm</span>` :
                                '<span class="badge bg-danger">Hết hàng</span>';
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
    </script>
@endsection
