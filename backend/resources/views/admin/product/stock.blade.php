@extends('admin.layouts.app')
@section('title', 'Quản lý kho hàng - ' . $product->name)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Danh sách kho hàng: <span class="text-success fw-bold"> Chưa bán</span> <span
                                class="badge bg-primary-gradient">{{ number_format($product->stocks()->where('status', 'available')->count()) }}</span>
                            <span class="text-danger fw-bold"></span> <span class="text-warning fw-bold">Đã bán</span> <span
                                class="badge bg-danger-gradient">{{ number_format($product->stocks()->where('status', 'sold')->count()) }}</span>
                        </h4>
                        <div class="">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#add-data">
                                <i class="bx bx-plus"></i>
                                <span>Thêm dữ liệu</span>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteItems()">
                                <i class="bx bx-trash"></i>
                                <span>Xóa</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table w-100" id="table-product-stock">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>ID</th>
                                        <th>Dữ liệu</th>
                                        <th>Trạng thái</th>
                                        <th>Người mua</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-data" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="{{ route('admin.products.stock.create') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm dữ liệu mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="data" class="form-label">Dữ liệu</label>
                        <textarea class="form-control" id="data" name="data" rows="3"
                            placeholder="username|password|2fa.... mỗi 1 dòng"></textarea>
                        <small class="text-muted">Hệ thống tự động lọc trùng dữ liệu</small>
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
        $(document).ready(function() {
            let table = $('#table-product-stock').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'ProductStock', 'product_id' => $product->id]) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <input type="checkbox" class="check-item" value="${data}">
                            `;
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
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
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let statusClass = data === 'available' ? 'bg-primary' :
                                'bg-danger';
                            let statusText = data === 'available' ? 'Chưa bán' : 'Đã bán';
                            return `<span class="badge ${statusClass}">${statusText}</span>`;
                        }
                    },
                    {
                        data: 'user',
                        name: 'user',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return data ?
                                `<span class="badge bg-success">${data.username}</span>` :
                                `<span class="badge bg-danger">Chưa có người mua</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss')
                        }
                    },
                ],
                order: [
                    [1, 'desc']
                ],
            });

            $('#check-all').on('click', function() {
                let checked = $(this).is(':checked');
                $('.check-item').prop('checked', checked);
            });

            $('#table-product-stock').on('click', '.check-item', function() {
                let allChecked = $('.check-item:checked').length === $('.check-item').length;
                $('#check-all').prop('checked', allChecked);
            });
        });
    </script>
    <script>
        function deleteItems() {
            let ids = [];
            $('.check-item:checked').each(function() {
                ids.push($(this).val());
            });
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Chưa chọn mục nào',
                    text: 'Vui lòng chọn ít nhất một mục để xóa.',
                });
                return;
            }
            deleteData(ids, "ProductStock")
        }
    </script>
@endsection
