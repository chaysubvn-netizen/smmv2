@extends('admin.layouts.app')
@section('title', 'Import Categories')
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
    
    .card.border-0.shadow-sm.rounded-4:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    
    .bg-primary-transparent { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-info-transparent { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-transparent { background-color: rgba(255, 193, 7, 0.1) !important; }
    
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
</style>
@endsection
@section('content')
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold" style="letter-spacing: -0.5px;">Import Categories</h4>
            <p class="text-muted mb-0 fs-13">Đồng bộ và nhập hàng loạt danh mục từ API Provider</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if (count($ListServices) > 0)
                <button class="btn btn-primary shadow-sm disabled" id="btnImportService" onclick="importService()">
                    <i class="bx bx-import me-1"></i> Nhập danh mục đã chọn <span id="countService" class="badge bg-white text-primary ms-1">0</span>
                </button>
            @endif
            <a href="{{ request()->fullUrlWithQuery(['refresh' => 1]) }}" class="btn btn-light border shadow-sm px-3" title="Tải lại dữ liệu mới nhất từ Provider">
                <i class="bx bx-refresh me-1"></i> Làm mới dữ liệu
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="alert bg-warning-transparent border-warning text-warning d-flex align-items-center rounded-3 p-3">
                <i class="bx bx-error fs-4 me-2"></i>
                <div class="fw-medium">Lưu ý: Quá trình tải dữ liệu trực tiếp từ nguồn có thể mất vài giây. Hệ thống sẽ lưu tạm (cache) dữ liệu trong 5 phút để tăng tốc độ.</div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0">Cấu hình nguồn tải</h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="platform_id" class="form-label text-dark fw-bold fs-13 mb-2">Nền tảng</label>
                                <select class="form-select select2-custom" id="platform_id" name="platform_id">
                                    <option value="">Chọn nền tảng</option>
                                    @foreach ($platforms as $platform)
                                        <option value="{{ $platform->id }}"
                                            {{ request()->get('platform_id') == $platform->id ? 'selected' : '' }}>
                                            {{ $platform->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="keyword" class="form-label text-dark fw-bold fs-13 mb-2">Từ khoá lọc</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bx bx-search fs-18 text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="keyword" name="keyword"
                                        value="{{ request()->get('keyword') }}" placeholder="Nhập từ khoá lọc...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="provider_id" class="form-label text-dark fw-bold fs-13 mb-2">API Providers</label>
                                <select class="form-select select2-custom" id="provider_id" name="provider_id">
                                    <option value="">Chọn API PROVIDERS</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}"
                                            {{ request()->get('provider_id') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="bx bx-cloud-download me-1"></i> Tải danh mục từ nguồn
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <h5 class="card-title fw-bold text-dark mb-0">Danh sách danh mục từ nguồn</h5>
                </div>
                <div class="card-body pt-2">
                    @php
                        $currentPlatform = \App\Models\Platform::find(request()->get('platform_id'));
                        $platformIcon = $currentPlatform ? $currentPlatform->icon : null;
                    @endphp
                    @if ($errMsg != '')
                        <div class="alert bg-danger-transparent text-danger border-0 rounded-3">
                            <i class="bx bx-info-circle me-1"></i> {{ $errMsg }}
                        </div>
                    @endif

                    @if (count($ListServices) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle w-100 text-nowrap">
                                <thead class="table-light text-muted fw-semibold">
                                    <tr>
                                        <th width="50" class="text-center">STT</th>
                                        <th width="40" class="text-center">
                                            <input type="checkbox" id="checkAllService" class="form-check-input">
                                        </th>
                                        <th>Tên danh mục (Gốc)</th>
                                        <th>Loại (Category)</th>
                                        <th>Nền tảng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ListServices as $key => $service)
                                        <tr>
                                            <td class="text-center text-muted fs-13">{{ $key + 1 }}</td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input" name="service_ids[]"
                                                    value="{{ $service['category_name'] }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($platformIcon)
                                                        <img src="{{ $platformIcon }}" alt="" class="me-2 rounded shadow-sm border" width="28" height="28" style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary-transparent text-primary rounded-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                                                            <i class="bx bx-folder fs-18"></i>
                                                        </div>
                                                    @endif
                                                    <span class="text-dark fw-bold fs-13">{{ $service['category_name'] }}</span>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-primary-transparent text-primary badge-pill-custom">{{ $service['category'] }}</span></td>
                                            <td><span class="badge bg-info-transparent text-info badge-pill-custom">{{ $service['platform'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-20 mb-3" alt="No data">
                            <p class="text-muted">Chưa có dữ liệu. Vui lòng cấu hình nguồn và nhấn "Tải danh mục".</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#platform_id').select2({
                placeholder: 'Chọn nền tảng',
                allowClear: true,
                width: '100%'
            });
            $('#provider_id').select2({
                placeholder: 'Chọn API PROVIDERS',
                allowClear: true,
                width: '100%'
            });

            $('#checkAllService').change(function() {
                var isChecked = $(this).is(':checked');
                $('input[name="service_ids[]"]').prop('checked', isChecked);
                updateCount();
            });

            $(document).on('change', 'input[name="service_ids[]"]', function() {
                updateCount();
            });

            function updateCount() {
                var count = $('input[name="service_ids[]"]:checked').length;
                $('#countService').text(count);
                if (count > 0) {
                    $('#btnImportService').removeClass('disabled');
                } else {
                    $('#btnImportService').addClass('disabled');
                }
                
                var allChecked = $('input[name="service_ids[]"]').length === count && count > 0;
                $('#checkAllService').prop('checked', allChecked);
            }
        });

        function importService() {
            var selectedServices = [];
            $('input[name="service_ids[]"]:checked').each(function() {
                selectedServices.push($(this).val());
            });

            if (selectedServices.length > 0) {
                Swal.fire({
                    title: 'Xác nhận nhập',
                    text: "Bạn có chắc chắn muốn nhập " + selectedServices.length + " danh mục đã chọn?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Có, nhập ngay!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.import.categories.add') }}",
                            type: "POST",
                            data: {
                                service_ids: selectedServices,
                                platform_id: $('#platform_id').val(),
                                provider_id: $('#provider_id').val(),
                                type: 'categories',
                                _token: "{{ csrf_token() }}"
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Đang nhập dữ liệu...',
                                    text: 'Hệ thống đang đồng bộ danh mục, vui lòng không đóng trình duyệt.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                });
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = "{{ route('admin.import.categories.index') }}";
                                    });
                                } else {
                                    Swal.fire('Lỗi!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Lỗi!', 'Đã xảy ra lỗi không xác định trong quá trình nhập.', 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Lỗi!', 'Vui lòng chọn ít nhất một danh mục để nhập.', 'error');
            }
        }
    </script>
@endsection
