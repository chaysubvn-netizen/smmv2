@extends('admin.layouts.app')
@section('title', 'Import Services')
@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
    }
    
    .card.border-0.shadow-sm.rounded-4 {
        transition: all 0.3s ease;
        border: 1px solid var(--glass-border) !important;
    }
    
    .bg-primary-transparent { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-transparent { background-color: rgba(25, 135, 84, 0.1) !important; }
    
    .badge-pill-custom {
        padding: 0.35em 0.8em;
        font-weight: 500;
        border-radius: 50rem;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
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
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.01) !important;
    }

    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        border-color: var(--bs-primary);
        background: rgba(13, 110, 253, 0.05);
    }
</style>
@endsection
@section('content')
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold" style="letter-spacing: -0.5px;">Import Services</h4>
            <p class="text-muted mb-0 fs-13">Nhập và quản lý danh sách dịch vụ từ API Provider</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-light border shadow-sm text-dark px-3">
                <i class="bx bx-help-circle me-1"></i> Hướng dẫn import
            </button>
            <a href="#" class="btn btn-light border shadow-sm text-primary px-3">
                <i class="bx bx-download me-1"></i> Tải file mẫu
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0">Thông tin nhập</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="GET">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label text-dark fw-bold fs-13 mb-2">Dịch vụ</label>
                                <select class="form-control select2-custom" id="category_id" name="category_id">
                                    <option value="">Chọn dịch vụ cần import...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == request()->get('category_id') ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="provider_id" class="form-label text-dark fw-bold fs-13 mb-2">Provider</label>
                                <select class="form-control select2-custom" id="provider_id" name="provider_id">
                                    <option value="">Chọn provider...</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}"
                                            {{ $provider->id == request()->get('provider_id') ? 'selected' : '' }}>
                                            {{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="upload-area mb-4">
                                    <div class="mb-3">
                                        <i class="bx bx-cloud-upload text-muted" style="font-size: 48px;"></i>
                                    </div>
                                    <h6 class="text-dark fw-bold mb-1">Kéo thả file vào đây hoặc <span class="text-primary" style="cursor: pointer;">chọn file</span> để upload</h6>
                                    <p class="text-muted fs-12 mb-0">Hỗ trợ định dạng: .csv, .xlsx, .xls | Dung lượng tối đa: 10MB</p>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="bx bx-search-alt me-1"></i> Lấy danh sách dịch vụ để import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold text-dark mb-0">Thông tin dịch vụ</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm px-3">
                            <i class="bx bx-history me-1"></i> Import history
                        </button>
                        <button class="btn btn-primary btn-sm px-3 shadow-sm disabled" id="btn-import" onclick="importServices()">
                            <i class="bx bx-import me-1"></i> Chọn dịch vụ để import <span id="countImport" class="badge bg-white text-primary ms-1">0</span>
                        </button>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100 text-nowrap" id="table-services">
                            <thead class="table-light text-muted fw-semibold">
                                <tr>
                                    <th width="40" class="text-center">
                                        <input type="checkbox" id="check-all" class="form-check-input">
                                    </th>
                                    <th width="100">Service ID</th>
                                    <th>Service Name</th>
                                    <th>Category</th>
                                    <th>Service Type</th>
                                    <th>Rate per/1k</th>
                                    <th>Rate Format</th>
                                    <th width="100">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="check-item form-check-input" value="{{ $service['id'] }}">
                                        </td>
                                        <td><span class="fw-bold text-dark">#{{ $service['id'] }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-transparent text-primary rounded-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px;">
                                                    <i class="bx bx-cube fs-14"></i>
                                                </div>
                                                <span class="text-dark fw-medium">{{ $service['name'] }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary-transparent text-primary badge-pill-custom">{{ $service['category'] }}</span></td>
                                        <td><span class="badge bg-info-transparent text-info badge-pill-custom">{{ $service['type'] }}</span></td>
                                        <td><span class="text-success fw-bold">{{ $service['rate_format_vnd'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">VND</span></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-light border text-primary" title="Sửa"><i class="bx bx-pencil"></i></button>
                                                <button class="btn btn-sm btn-light border text-danger" title="Xóa"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Khởi tạo DataTable với các tùy chọn cần thiết
            const table = $('#table-services').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                order: [
                    [1, 'asc']
                ],
                // Thêm stateSave để lưu trạng thái checkbox khi chuyển trang
                stateSave: true,
                // Xử lý sự kiện draw callback
                drawCallback: function(settings) {
                    // Cập nhật trạng thái check-all khi vẽ lại bảng
                    var allChecked = $('.check-item:visible').length === $(
                        '.check-item:visible:checked').length;
                    $('#check-all').prop('checked', allChecked);
                    updateImportButton();
                }
            });

            $('#category_id, #provider_id').select2({
                placeholder: 'Chọn...',
                allowClear: true
            });

            // Sử dụng event delegation cho các checkbox động
            $('#table-services tbody').on('click', '.check-item', function() {
                var allChecked = $('.check-item:visible').length === $('.check-item:visible:checked')
                    .length;
                $('#check-all').prop('checked', allChecked);
                updateImportButton();
            });

            $('#check-all').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('.check-item').prop('checked', isChecked);
                updateImportButton();
            });

        });
    </script>
    <script>
        function updateImportButton() {
            let count = $('.check-item:checked').length;
            $('#countImport').text(count);
            if (count > 0) {
                $('#btn-import').removeClass('disabled');
            } else {
                $('#btn-import').addClass('disabled');
            }
        }

        function importServices() {
            let ids = [];
            $('.check-item:checked').each(function() {
                ids.push($(this).val());
            });
            if (ids.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Vui lòng chọn dịch vụ để nhập!',
                });
                return;
            }
            console.log(ids);
            Swal.fire({
                title: 'Bạn có chắc chắn muốn nhập dịch vụ này không?',
                text: "Bạn sẽ không thể hoàn tác lại!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có, nhập dịch vụ!',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.import.services.add') }}",
                        type: "POST",
                        data: {
                            ids: ids,
                            provider_id: $('#provider_id').val(),
                            category_id: $('#category_id').val(),
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                onBeforeOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success == true) {
                                Swal.fire(
                                    'Thành công!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    response.redirect ? window.location.href = response
                                        .redirect : location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra, vui lòng thử lại sau.',
                                'error'
                            );
                        }
                    });
                }
            })
        }
    </script>
@endsection
