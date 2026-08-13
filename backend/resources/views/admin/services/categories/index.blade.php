@extends('admin.layouts.app')
@section('title', 'Danh sách phân loại')
@section('content')
    @php
        $totalCats = \App\Models\Category::count();
        $activeCats = \App\Models\Category::where('status', 'active')->count();
        $inactiveCats = \App\Models\Category::where('status', 'inactive')->count();
        $totalPlatforms = \App\Models\Platform::count();
    @endphp

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold">Danh sách phân loại</h4>
            <p class="text-muted mb-0 fs-13">Quản lý và tổ chức các danh mục dịch vụ của bạn</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.import.categories.index') }}" class="btn btn-light border shadow-sm text-primary">
                <i class="bx bx-cloud-download me-1"></i> Nhập danh mục
            </a>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPlatformModal">
                <i class="bx bx-plus me-1"></i> Thêm danh mục mới
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="flex-shrink-0 bg-primary-transparent text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bx bx-folder fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-13 fw-medium">Tổng danh mục</p>
                        <h4 class="mb-0 fw-bold">{{ $totalCats }}</h4>
                        <p class="text-muted mb-0 fs-11 mt-1">Tất cả danh mục</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="flex-shrink-0 bg-success-transparent text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bx bx-check-shield fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-13 fw-medium">Đang hiển thị</p>
                        <h4 class="mb-0 fw-bold">{{ $activeCats }}</h4>
                        <p class="text-muted mb-0 fs-11 mt-1">Hoạt động trên web</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="flex-shrink-0 bg-warning-transparent text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bx bx-hide fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-13 fw-medium">Đang ẩn</p>
                        <h4 class="mb-0 fw-bold">{{ $inactiveCats }}</h4>
                        <p class="text-muted mb-0 fs-11 mt-1">Không hiển thị</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="flex-shrink-0 bg-info-transparent text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bx bx-layer fs-3"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-13 fw-medium">Nền tảng</p>
                        <h4 class="mb-0 fw-bold">{{ $totalPlatforms }}</h4>
                        <p class="text-muted mb-0 fs-11 mt-1">Số lượng nền tảng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="card-title fw-bold text-dark mb-0">Danh sách danh mục</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btn-delete">
                            <i class="bx bx-trash me-1"></i> Xóa đã chọn
                        </button>
                        <button type="button" class="btn btn-light text-danger border btn-sm px-3" id="btn-delete-all-cat" onclick="deleteAllCategory()">
                            <i class="bx bx-trash-alt me-1"></i> Xóa tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100 text-nowrap" id="table-categories">
                            <thead class="table-light text-muted fw-semibold">
                                <tr>
                                    <th width="30" class="text-center"></th>
                                    <th width="50" class="text-center">ID</th>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="checkAll" name="ids[]" class="form-check-input">
                                    </th>
                                    <th width="70" class="text-center">Thứ tự</th>
                                    <th width="100">Thao tác</th>
                                    <th>Nền tảng</th>
                                    <th>Danh mục</th>
                                    <th>Giảm giá</th>
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

    <div class="modal fade" id="addPlatformModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.categories.create') }}" method="POST" data-ajax="true"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thêm mới phân loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="platform_id" class="form-label">Nên tảng</label>
                        <select name="platform_id" id="platform_id" class="form-control">
                            <option value="">Chọn nền tảng</option>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="icon" class="form-label required">Icon</label>
                        <input type="file" class="form-control" id="icon" name="icon" placeholder="Nhập icon">
                    </div>
                    <div class="form-group mb-3">
                        <label for="sort_order" class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0"
                            placeholder="Nhập thứ tự">
                    </div>
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Tên phân loại</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Nhập tên phân loại">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 form-group">
                            <label for="is_discount" class="form-label">Giảm giá</label>
                            <select name="is_discount" id="is_discount" class="form-control">
                                <option value="0">Tắt</option>
                                <option value="1">Bật</option>
                            </select>
                        </div>
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
            let table = $('#table-categories').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.loaddata.index', ['model' => 'Category']) }}",
                    type: "GET",
                },
                createdRow: function(row, data, dataIndex) {
                    $(row).attr('data-id', data.id);
                },
                columns: [{
                        data: null,
                        orderable: false,
                        className: 'reorder',
                        render: function(data, type, row) {
                            return '<i class="bx bx-move-vertical fs-18" style="cursor: move;"></i>';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="form-check-input" name="ids[]" value="${row.id}">`
                        }
                    },
                    {
                        data: 'sort_order',
                        name: 'sort_order',
                        render: function(data, type, row) {
                            return `<input type="number" class="form-control form-control-sm text-center" 
                                        value="${data}" 
                                        onchange="changePriority(${row.id}, this.value)" 
                                        style="width: 70px; margin: 0 auto;">`
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<div class="d-flex gap-1 justify-content-center">
                                        <a href="${row.edit_url}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-light border text-danger" onclick="deleteData([${row.id}], 'Category', $('#table-categories').DataTable())" data-bs-toggle="tooltip" title="Xóa">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>`
                        }
                    },
                    {
                        data: 'platform.name',
                        name: 'platform.name',
                        render: function(data, type, row) {
                            return `<span class="badge bg-light text-dark border px-2 py-1">${data ? data : 'Không có'}</span>`
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'details-control',
                        render: function(data, type, row) {
                            let iconHtml = '';
                            if (row.icon) {
                                iconHtml = `<img src="${row.icon}" alt="${data}" class="me-2 rounded" width="24" height="24" style="object-fit: cover;">`;
                            } else {
                                iconHtml = `<i class="bx bx-folder text-primary me-2 fs-5"></i>`;
                            }
                            return `<div class="d-flex align-items-center fw-medium" style="cursor: pointer;" title="Nhấn để xem danh sách dịch vụ con">
                                        <i class="bx bx-chevron-right fs-5 text-muted toggle-icon me-1 transition-all"></i>
                                        ${iconHtml}
                                        <span class="text-dark me-2">${data}</span>
                                        <span class="badge bg-primary-transparent text-primary" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">Cấp 1</span>
                                    </div>`
                        }
                    },
                    {
                        data: 'is_discount',
                        name: 'is_discount',
                        render: function(data, type, row) {
                            return data == 1 ? '<span class="badge bg-success">Bật</span>' :
                                '<span class="badge bg-danger">Tắt</span>'
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            return data == 'active' ?
                                '<span class="badge bg-success-transparent text-success px-3 py-1 rounded-pill border border-success-transparent">Hiển thị</span>' :
                                '<span class="badge bg-danger-transparent text-danger px-3 py-1 rounded-pill border border-danger-transparent">Ẩn</span>'
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return moment(data).format('DD/MM/YYYY HH:mm:ss')
                        }
                    }
                ],
                'order': [
                    [3, 'asc']
                ],
                rowReorder: {
                    selector: 'td.reorder',
                    dataSrc: 'sort_order'
                }
            });

            // Add event listener for opening and closing details
            $('#table-categories tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var icon = $(this).find('i.toggle-icon');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('bx-chevron-down').addClass('bx-chevron-right');
                } else {
                    row.child(formatChildRow(row.data())).show();
                    tr.addClass('shown');
                    icon.removeClass('bx-chevron-right').addClass('bx-chevron-down');
                }
            });

            function formatChildRow(d) {
                if (!d.services || d.services.length === 0) {
                    return '<div class="ps-5 text-muted py-2"><i class="bx bx-info-circle me-1"></i>Không có dịch vụ con nào trong danh mục này.</div>';
                }
                
                let html = '<div class="ps-5 pe-3 py-2 bg-light rounded"><table class="table table-sm table-borderless mb-0 w-100" style="background: transparent;">';
                d.services.forEach(function(service) {
                    let sStatus = service.status == 'active' ? 
                        '<span class="badge bg-success-transparent text-success px-2 py-0 rounded-pill" style="font-size:0.65rem;">Hiển thị</span>' : 
                        '<span class="badge bg-danger-transparent text-danger px-2 py-0 rounded-pill" style="font-size:0.65rem;">Ẩn</span>';
                    
                    html += `
                        <tr>
                            <td style="padding-left: 20px;">
                                <div class="d-flex align-items-center">
                                    <div style="width: 15px; height: 1px; background-color: #ccc; margin-right: 10px;"></div>
                                    <i class="bx bx-folder text-secondary me-2 fs-6"></i>
                                    <span class="text-dark me-2 fs-13 fw-medium">${service.name}</span>
                                    <span class="badge bg-info-transparent text-info" style="font-size: 0.6rem; padding: 0.15rem 0.3rem;">Cấp 2</span>
                                </div>
                            </td>
                            <td width="150">
                                <a href="${service.edit_url}" class="btn btn-sm btn-light border text-primary py-0 px-2 fs-12" title="Chỉnh sửa dịch vụ"><i class="bx bx-pencil me-1"></i> Sửa dịch vụ</a>
                            </td>
                            <td width="100">${sStatus}</td>
                        </tr>
                    `;
                });
                html += '</table></div>';
                return html;
            }

            table.on('row-reorder', function(e, diff, edit) {
                // Wait for RowReorder to finish updating the table
                setTimeout(function() {
                    let ids = [];
                    // Get IDs of ALL rows on the current page in their NEW displayed order
                    $('#table-categories tbody tr').each(function() {
                        // The checkbox contains the row ID
                        let id = $(this).find('input[name="ids[]"]').val();
                        if (id) {
                            ids.push(id);
                        }
                    });

                    if (ids.length > 0) {
                        $.ajax({
                            url: "{{ route('admin.loaddata.sort') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                model: 'Category',
                                data: ids
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        text: response.message,
                                        timer: 800,
                                        showConfirmButton: false
                                    });
                                    // reload with false to keep the same page
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Lỗi!', response.message, 'error');
                                    table.ajax.reload(null, false);
                                }
                            },
                            error: function() {
                                Swal.fire('Lỗi!', 'Không thể kết nối đến máy chủ', 'error');
                                table.ajax.reload(null, false);
                            }
                        });
                    }
                }, 200);
            });

            $('#checkAll').on('click', function() {
                $('input[name="ids[]"]').prop('checked', this.checked);
            });

            $('#btn-delete').on('click', function() {
                let ids = [];
                $('input[name="ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    return deleteData(ids, 'Category', table);
                } else {
                    Swal.fire(
                        'Lỗi!',
                        'Vui lòng chọn ít nhất một mục để xoá.',
                        'error'
                    )
                }
            });
        });

        function changePriority(id, value) {
            $.ajax({
                url: "{{ route('admin.loaddata.update-field') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    model: 'Category',
                    field: 'sort_order',
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function(error) {
                    Swal.fire('Lỗi!', 'Đã xảy ra lỗi, vui lòng thử lại sau.', 'error');
                }
            });
        }

        function deleteAllCategory() {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa tất cả phân loại trên trang web này và không thể hoàn tác!",
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
                            model: 'Category',
                            type: 'all'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Đã xóa!',
                                    response.message,
                                    'success'
                                );
                                $('#table-categories').DataTable().ajax.reload();
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
