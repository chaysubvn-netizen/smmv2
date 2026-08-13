@extends('admin.layouts.app')
@section('title', 'Danh sách dịch vụ')
@section('content')
    @php
        $totalServices = \App\Models\Service::count();
        $activeServices = \App\Models\Service::where('status', 'active')->count();
        $inactiveServices = \App\Models\Service::where('status', 'inactive')->count();
        $apiServices = \App\Models\Service::where('mode', 'option')->count();
    @endphp

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold" style="letter-spacing: -0.5px;">Danh sách dịch vụ</h4>
            <p class="text-muted mb-0 fs-13">Quản lý, thiết lập giá và cấu hình toàn bộ dịch vụ của hệ thống</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.import.services.index') }}" class="btn btn-light border shadow-sm text-primary">
                <i class="bx bx-cloud-download me-1"></i> Nhập dịch vụ
            </a>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary shadow-sm">
                <i class="bx bx-plus me-1"></i> Thêm mới
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body d-flex align-items-center p-4 position-relative">
                    <div class="flex-shrink-0 bg-primary-transparent text-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bx bx-list-ul fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-12 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Tổng dịch vụ</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalServices) }}</h3>
                        <div class="mt-1">
                            <span class="badge bg-primary-transparent text-primary badge-pill-custom">Tất cả</span>
                        </div>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3">
                        <i class="bx bx-list-ul fs-1" style="font-size: 80px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body d-flex align-items-center p-4 position-relative">
                    <div class="flex-shrink-0 bg-success-transparent text-success rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bx bx-check-shield fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-12 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Đang hiển thị</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ number_format($activeServices) }}</h3>
                        <div class="mt-1">
                            <span class="badge bg-success-transparent text-success badge-pill-custom">Public</span>
                        </div>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3">
                        <i class="bx bx-check-shield fs-1" style="font-size: 80px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body d-flex align-items-center p-4 position-relative">
                    <div class="flex-shrink-0 bg-warning-transparent text-warning rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bx bx-hide fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-12 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Đang ẩn</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ number_format($inactiveServices) }}</h3>
                        <div class="mt-1">
                            <span class="badge bg-warning-transparent text-warning badge-pill-custom">Private</span>
                        </div>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3">
                        <i class="bx bx-hide fs-1" style="font-size: 80px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body d-flex align-items-center p-4 position-relative">
                    <div class="flex-shrink-0 bg-info-transparent text-info rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px;">
                        <i class="bx bx-plug fs-2"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1 fs-12 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Dịch vụ API</p>
                        <h3 class="mb-0 fw-bold text-dark">{{ number_format($apiServices) }}</h3>
                        <div class="mt-1">
                            <span class="badge bg-info-transparent text-info badge-pill-custom">Connected</span>
                        </div>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3">
                        <i class="bx bx-plug fs-1" style="font-size: 80px;"></i>
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
                    <h5 class="card-title fw-bold text-dark mb-0">Danh sách dịch vụ</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-warning btn-sm disabled" id="edit_attribute">
                            <i class="bx bx-edit me-1"></i> Sửa thuộc tính <span class="badge bg-warning ms-1">0</span>
                        </button>
                        <button class="btn btn-outline-success btn-sm disabled" id="edit_description">
                            <i class="bx bx-edit-alt me-1"></i> Sửa nội dung <span class="badge bg-success ms-1">0</span>
                        </button>
                        <button class="btn btn-outline-danger btn-sm disabled" id="delete_all">
                            <i class="bx bx-trash me-1"></i> Xoá <span class="badge bg-danger ms-1">0</span>
                        </button>
                        <button class="btn btn-light text-danger border btn-sm px-3" id="delete_all_records" onclick="deleteAllService()">
                            <i class="bx bx-trash-alt me-1"></i> Xoá tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <!-- Filter Section -->
                        <div class="filter-section mb-4 p-3 rounded-3 bg-light border-0 shadow-none">
                            <div class="row align-items-end g-3">
                                <div class="col-md-4">
                                    <label for="category_id" class="form-label text-dark fw-bold fs-13 mb-2">
                                        <i class="bx bx-filter-alt me-1 text-primary"></i> Lọc theo phân loại
                                    </label>
                                    <select name="category_id" id="category_id" class="form-select select2-custom"
                                        onchange="filterCategory()">
                                        <option value="">Tất cả phân loại</option>
                                        @foreach (\App\Models\Category::where('status', 'active')->orderBy('name', 'asc')->get() as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-auto ms-auto">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light border px-3" onclick="location.reload()">
                                            <i class="bx bx-refresh me-1"></i> Làm mới
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-hover align-middle w-100 text-nowrap" id="table-services">
                            <thead class="table-light text-muted fw-semibold">
                                <tr>
                                    <th width="30" class="text-center"></th>
                                    <th width="50" class="text-center">ID</th>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="check_all" class="form-check-input">
                                    </th>
                                    <th width="70" class="text-center">Ưu tiên</th>
                                    <th width="120">Thao tác</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Nguồn</th>
                                    <th>Phân loại</th>
                                    <th>Loại</th>
                                    <th>Giá (1k)</th>
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
@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
    }
    
    .card.border-0.shadow-sm.rounded-4 {
        transition: all 0.3s ease;
        border: 1px solid var(--glass-border) !important;
    }
    
    .card.border-0.shadow-sm.rounded-4:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    
    .bg-primary-transparent { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-transparent { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-transparent { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-info-transparent { background-color: rgba(13, 202, 240, 0.1) !important; }
    
    .badge-pill-custom {
        padding: 0.35em 0.8em;
        font-weight: 500;
        border-radius: 50rem;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.01) !important;
    }
    
    .rate-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .rate-item {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        margin-bottom: 2px;
    }
    
    .rate-label { color: #6c757d; min-width: 60px; }
    .rate-value { font-weight: 600; color: #212529; }
    
    /* Select2 Custom Styling */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        padding-left: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057;
        font-size: 14px;
        font-weight: 500;
    }
    
    .filter-section {
        border: 1px solid rgba(0,0,0,0.05) !important;
        background-color: rgba(0,0,0,0.01) !important;
    }
</style>
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

            $('#attributes').select2({
                placeholder: 'Chọn thuộc tính',
                allowClear: true
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
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" name="ids[]" class="form-check-input" value="${data}">`;
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
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="${row.edit_url}" class="btn btn-sm btn-light border text-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                    <i class="bx bx-pencil"></i>
                                </a>
                                <a href="${row.edit_rate_url}" class="btn btn-sm btn-light border text-success" data-bs-toggle="tooltip" title="Chỉnh giá">
                                    <i class="bx bx-dollar"></i>
                                </a>
                            </div>
                        `;
                        }
                    }, {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return `<a href="${row.edit_url}" class="text-dark fw-medium text-wrap" style="max-width: 250px; display: block;">${data}</a>`;
                        }
                    }, {
                        data: 'provider',
                        name: 'provider',
                        render: function(data, type, row) {
                            if (row?.mode == 'option') {
                                return `
                                    <ul class="px-2">
                                        <li><span class="text-primary">${data?.name}</span></li>
                                        <li><span class="text-danger">${row?.provider_id}</span></li>
                                        <li><span class="text-warning">Rate USD: ${convertAndFormatCurrency(row?.rate_original, "VND", "USD")}</span></li>
                                        <li><span class="text-info">Rate VND: ${convertAndFormatCurrency(row?.rate_original, "VND", "VND")}</span></li>
                                    </ul>
                                `
                            } else {
                                return `<span class="badge bg-light text-dark border px-2 py-1">Thủ công</span>`;
                            }
                        }
                    }, {
                        data: 'category',
                        name: 'category',
                        render: function(data, type, row) {
                            let iconHtml = '';
                            if (data?.icon) {
                                iconHtml = `<img src="${data.icon}" alt="${data?.name}" class="me-3 rounded-2 shadow-sm border" width="28" height="28" style="object-fit: cover;">`;
                            } else {
                                iconHtml = `<div class="bg-primary-transparent text-primary rounded-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                                                <i class="bx bx-folder fs-18"></i>
                                            </div>`;
                            }
                            return `<div class="d-flex align-items-center">
                                        ${iconHtml}
                                        <div>
                                            <span class="text-dark fw-bold mb-0 d-block" style="font-size: 13px;">${data?.name || 'Chưa phân loại'}</span>
                                            <span class="badge bg-primary-transparent text-primary badge-pill-custom" style="font-size: 9px; padding: 1px 6px;">Cấp 1</span>
                                        </div>
                                    </div>`;
                        }
                    }, {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            return `<span class="badge bg-light text-dark border px-2 py-1">${data}</span>`;
                        }
                    }, {
                        data: 'rate',
                        name: 'rate',
                        render: function(data, type, row) {
                            return `
                                <div class="rate-list">
                                    <div class="rate-item"><span class="rate-label">Member:</span><span class="rate-value">${row?.rate}</span></div>
                                    <div class="rate-item"><span class="rate-label">Silver:</span><span class="rate-value">${row?.rate_silver}</span></div>
                                    <div class="rate-item"><span class="rate-label">Gold:</span><span class="rate-value">${row?.rate_gold}</span></div>
                                    <div class="rate-item"><span class="rate-label">Plat:</span><span class="rate-value">${row?.rate_platinum}</span></div>
                                    <div class="rate-item"><span class="rate-label">Diam:</span><span class="rate-value text-danger">${row?.rate_diamond}</span></div>
                                </div>
                            `
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
                            return data == 'active' ?
                                '<span class="badge bg-success-transparent text-success px-3 py-1 rounded-pill border border-success-transparent">Hiển thị</span>' :
                                '<span class="badge bg-danger-transparent text-danger px-3 py-1 rounded-pill border border-danger-transparent">Ẩn</span>'
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
                    [3, 'asc']
                ],
                rowReorder: {
                    selector: 'td.reorder',
                    dataSrc: 'sort_order'
                }
            })

            table.on('row-reorder', function(e, diff, edit) {
                setTimeout(function() {
                    let ids = [];
                    $('#table-services tbody tr').each(function() {
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
                                model: 'Service',
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

            // checkbox all
            $('#check_all').on('click', function() {
                let checked = $(this).is(':checked');
                $('input[name="ids[]"]').prop('checked', checked);
                $('#delete_all .badge').text($('input[name="ids[]"]:checked').length);
                $('#delete_all').toggleClass('disabled', !checked);

                $("#edit_attribute .badge").text($('input[name="ids[]"]:checked').length);
                $("#edit_description .badge").text($('input[name="ids[]"]:checked').length);
                $('#edit_attribute').toggleClass('disabled', !checked);
                $('#edit_description').toggleClass('disabled', !checked);
            })
            // checkbox item
            $('#table-services').on('click', 'input[name="ids[]"]', function() {
                let checked = $(this).is(':checked');
                let count = $('input[name="ids[]"]:checked').length;
                $('#check_all').prop('checked', count == $('input[name="ids[]"]').length);
                $('#delete_all .badge').text(count);
                $('#delete_all').toggleClass('disabled', count == 0);

                $("#edit_attribute .badge").text(count);
                $("#edit_description .badge").text(count);
                $('#edit_attribute').toggleClass('disabled', count == 0);
                $('#edit_description').toggleClass('disabled', count == 0);
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

            // btn edit attribute
            $("#edit_attribute").on('click', function() {
                let ids = [];
                $('input[name="ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    window.location.href = "{{ route('admin.services.update-attributes') }}?ids=" + ids
                        .join(',');
                } else {
                    Swal.fire(
                        'Lỗi!',
                        'Vui lòng chọn ít nhất một mục để sửa thuộc tính.',
                        'error'
                    )
                }
            })

            // btn edit description
            $("#edit_description").on('click', function() {
                let ids = [];
                $('input[name="ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                if (ids.length > 0) {
                    window.location.href = "{{ route('admin.services.update-description') }}?ids=" + ids
                        .join(',');
                } else {
                    Swal.fire(
                        'Lỗi!',
                        'Vui lòng chọn ít nhất một mục để sửa nội dung.',
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
        function changePriority(id, value) {
            $.ajax({
                url: "{{ route('admin.loaddata.update-field') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    model: 'Service',
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

        function filterCategory() {
            let category_id = $('#category_id').val();
            $('#table-services').DataTable().ajax.url(
                "{{ route('admin.loaddata.index', ['model' => 'Service']) }}&category_id=" + category_id).load();

        }
    </script>
@endsection
