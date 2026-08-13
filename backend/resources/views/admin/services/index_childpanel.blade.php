@extends('admin.layouts.app')
@section('title', 'Danh sách dịch vụ')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        Danh sách dịch vụ
                    </h4>
                    <div class="card-tool">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configService">
                            <i class="bx bx-cog"></i>
                            Cấu hình dịch vụ
                        </button>
                        <button class="btn btn-outline-danger btn-danger-gradient disabled" id="delete_all">
                            <i class="bx bx-trash"></i>
                            Xoá <span class="badge bg-danger">0</span>
                        </button>
                        <button class="btn btn-danger-light ms-2" id="delete_all_records" onclick="deleteAllService()">
                            <i class="bx bx-trash-alt"></i>
                            Xoá tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form action="">
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="category_id" class="form-label">Phân loại:</label>
                                    <select name="category_id" id="category_id" class="form-select"
                                        onchange="filterCategory()">
                                        <option value="">-- Tìm kiếm phân loại --</option>
                                        @foreach (\App\Models\Category::where('status', 'active')->get() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                        <table class="table w-100 table-bordered" id="table-services">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th width="1%">
                                        <input type="checkbox" id="check_all">
                                    </th>
                                    <th>Thao tác</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Phân loại</th>
                                    <th>Type</th>
                                    <th>Giá cho 1k</th>
                                    <th>Min/Max</th>
                                    <th>Ghi chú</th>
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

    <div class="modal fade" id="configService" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cấu hình dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="auto_update_service" class="form-label">Tự động cập nhật dịch vụ</label>
                        <select name="auto_update_service" id="auto_update_service" class="form-select">
                            <option value="active" {{ s('auto_update_service') == 'active' ? 'selected' : '' }}>Bật</option>
                            <option value="inactive" {{ s('auto_update_service') == 'inactive' ? 'selected' : '' }}>Tắt
                            </option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="rate_update_percent" class="form-label">Tăng giá (%)</label>
                        <input type="number" class="form-control" id="rate_update_percent" name="rate_update_percent"
                            value="{{ s('rate_update_percent') }}" placeholder="Tăng giá (%)">
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
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: 'Chọn phân loại',
                allowClear: true,
                width: '100%',
            });
        })
    </script>

    <script>
        $(document).ready(function() {
            const table = $('#table-services').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Service']) }}",
                    type: "GET",
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" name="ids[]" value="${data}">`;
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <a href="${row.edit_url}" class="btn btn-primary btn-sm btn-primary-gradient">
                                <i class="bx bxs-edit"></i>
                                Sửa
                            </a>
                            <a href="${row.edit_rate_url}" class="btn btn-success btn-sm btn-success-gradient">
                                <i class="bx bxs-edit"></i>
                                Chỉnh giá
                            </a>
                        `;
                        }
                    }, {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<a href="${row.edit_url}" class="text-primary">${data}</a>`;
                        }
                    }, {
                        data: 'category',
                        name: 'category',
                        render: function(data, type, row) {
                            return `<span class="text-success fw-bolds">${data?.name}</span>`;
                        }
                    }, {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            return `<span class="badge bg-primary-gradient">${data}</span>`;
                        }
                    }, {
                        data: 'rate',
                        name: 'rate',
                        render: function(data, type, row) {
                            return `
                                <ul class="px-2">
                                    <li><span class="text-primary">Member: ${(row?.rate)}</span></li>
                                    <li><span class="text-success">Silver: ${(row?.rate_silver)}</span></li>
                                    <li><span class="text-warning">Gold: ${(row?.rate_gold)}</span></li>
                                    <li><span class="text-infor">Platium: ${(row?.rate_platinum)}</span></li>
                                    <li><span class="text-danger">Diamond: ${(row?.rate_diamond)}</span></li>
                                </ul>
                            `;
                        }
                    }, {
                        data: 'min_max',
                        name: 'min_max',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `${formatNumber(row?.min)}/${formatNumber(row?.max)}`;
                        }
                    }, {
                        data: 'description',
                        name: 'description',
                    }, {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return `<span class="badge bg-${data == 'active' ? 'success' : 'danger'}">${data == 'active' ? 'Hoạt động' : 'Ngừng hoạt động'}</span>`;
                        }
                    }, {
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
            })

            // checkbox all
            $('#check_all').on('click', function() {
                let checked = $(this).is(':checked');
                $('input[name="ids[]"]').prop('checked', checked);
                $('#delete_all .badge').text($('input[name="ids[]"]:checked').length);
                $('#delete_all').toggleClass('disabled', !checked);
            })
            // checkbox item
            $('#table-services').on('click', 'input[name="ids[]"]', function() {
                let checked = $(this).is(':checked');
                let count = $('input[name="ids[]"]:checked').length;
                $('#check_all').prop('checked', count == $('input[name="ids[]"]').length);
                $('#delete_all .badge').text(count);
                $('#delete_all').toggleClass('disabled', count == 0);
            })

            // btn delete all
            $("#delete_all").on('click', function() {
                let ids = [];
                $('input[name="ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    return deleteData(ids, 'Service', table);
                } else {
                    Swal.fire(
                        'Lỗi!',
                        'Vui lòng chọn ít nhất một mục để xoá.',
                        'error'
                    )
                }
            })
        })

        function deleteAllService() {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa tất cả dịch vụ trên trang web này và không thể hoàn tác!",
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
                            model: 'Service',
                            type: 'all'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                );
                                $('#table-services').DataTable().ajax.reload();
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
    <script>
        function filterCategory() {
            let category_id = $('#category_id').val();
            $('#table-services').DataTable().ajax.url(
                "{{ route('admin.loaddata.index', ['model' => 'Service']) }}&category_id=" + category_id).load();

        }
    </script>
@endsection
