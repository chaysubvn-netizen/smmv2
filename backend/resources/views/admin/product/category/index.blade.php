@extends('admin.layouts.app')
@section('title', 'Danh sách phân loại')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách danh mục</h4>
                    <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addCategory">
                            <i class="bx bx-plus"></i>
                            <span>Thêm mới</span>
                        </button>
                        <button type="button" class="btn btn-danger disabled" id="delete-selected"
                            onclick="deleteSelected()">
                            <i class="bx bx-trash"></i>
                            <span>Xóa</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table w-100" id="category-table">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" id="check-all">
                                    </th>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tên danh mục</th>
                                    <th>Hỉnh ảnh</th>
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

    <!-- Modal -->

    <div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="{{ route('admin.products.categories.create') }}" method="POST"
                data-ajax="true" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm mới nền tảng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Tên danh mục">
                    </div>
                    <div class="form-group mb-3">
                        <label for="sort_order" class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Thứ tự"
                            value="0">
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Ghi chú"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
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
        $(document).ready(function() {
            $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'ProductCategory']) }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="check-item" value="' + data +
                                '">';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: true,
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
                        `;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return '<a href="' + row.edit_url + '">' + data + '</a>';
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<img src="' + data +
                                '" alt="Image" style="width: 50px; height: 50px;">';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return data === 'active' ?
                                '<span class="badge bg-success">Hoạt động</span>' :
                                '<span class="badge bg-danger">Ngừng hoạt động</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss');
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
            });
            $('#check-all').on('click', function() {
                // disable delete button if no checkbox is checked
                if ($(this).is(':checked')) {
                    $('.check-item').prop('checked', true);
                    $('#delete-selected').removeClass('disabled');
                } else {
                    $('.check-item').prop('checked', false);
                    $('#delete-selected').addClass('disabled');
                }
            });
            $('#category-table').on('click', '.check-item', function() {
                if ($('.check-item:checked').length === $('.check-item').length) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }
                if ($('.check-item:checked').length > 0) {
                    $('#delete-selected').removeClass('disabled');
                } else {
                    $('#delete-selected').addClass('disabled');
                }
            });

        });
    </script>
    <script>
        function deleteSelected() {
            var selectedIds = [];
            $('.check-item:checked').each(function() {
                selectedIds.push($(this).val());
            });
            if (selectedIds.length > 0) {
                deleteData(selectedIds, 'ProductCategory')
            } else {
                Swal.fire(
                    'Thông báo',
                    'Vui lòng chọn ít nhất một mục để xóa.',
                    'warning'
                );
            }
        }
    </script>
@endsection
