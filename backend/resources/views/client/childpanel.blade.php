@extends('client.layouts.master')
@section('title', __('Tạo Child Panel'))

@section('css')
<style>
    /* Custom Variables mapped to Metronic Theme Variables */
    :root {
        --cp-primary: #e74c3c;
        --cp-secondary: #3498db;
    }

    /* Page Header Accent */
    .page-title-accent {
        border-left: 3px solid var(--cp-primary);
        padding-left: 15px;
        margin-bottom: 25px;
    }
    .page-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--bs-gray-900);
        margin: 0;
    }

    /* Custom Card Style - Extending Metronic .card */
    .child-panel-card {
        background: var(--bs-card-bg);
        box-shadow: var(--bs-card-box-shadow);
        border: 1px solid var(--bs-card-border-color);
        border-radius: 0.625rem;
        margin-bottom: 1.5rem;
        padding: 2rem;
    }

    /* Stepper Styling */
    .cp-stepper-container {
        position: relative;
        display: flex;
        justify-content: space-between;
        margin-bottom: 3rem;
        padding: 0 1rem;
    }

    .cp-progress-track {
        position: absolute;
        top: 20px;
        left: 0;
        width: 100%;
        height: 4px;
        background-color: var(--bs-gray-200);
        z-index: 0;
        transform: translateY(-50%);
    }

    .cp-progress-bar {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: var(--cp-secondary);
        transition: width 0.4s ease;
        z-index: 0;
    }

    .cp-step-item {
        position: relative;
        z-index: 1;
        text-align: center;
        width: 40px;
    }

    .cp-step-circle {
        width: 40px;
        height: 40px;
        background-color: var(--bs-card-bg);
        border: 2px solid var(--bs-gray-300);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--bs-gray-500);
        margin: 0 auto;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .cp-step-label {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--bs-gray-500);
        white-space: nowrap;
        opacity: 0.8;
    }

    /* Active State */
    .cp-step-item.active .cp-step-circle {
        border-color: var(--cp-secondary);
        background-color: var(--cp-secondary);
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
    }
    .cp-step-item.active .cp-step-label {
        color: var(--bs-gray-900);
        opacity: 1;
    }

    /* Completed State */
    .cp-step-item.completed .cp-step-circle {
        border-color: var(--bs-success);
        background-color: var(--bs-success);
        color: #ffffff;
    }
    .cp-step-item.completed .cp-step-label {
        color: var(--bs-success);
    }

    /* Wizard Content */
    .wizard-content {
        display: none;
        animation: fadeUp 0.4s ease;
    }
    .wizard-content.active {
        display: block;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Helper Styles */
    .req-list li {
        margin-bottom: 0.5rem;
        color: var(--bs-gray-600);
        font-size: 0.9rem;
    }
    .req-list li i {
        color: var(--bs-success);
        margin-right: 0.5rem;
    }

    .badge-provider {
        background-color: var(--bs-gray-100);
        color: var(--bs-gray-600);
        border: 1px solid var(--bs-gray-200);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .badge-provider {
        background-color: var(--bs-gray-800);
        border-color: var(--bs-gray-700);
        color: var(--bs-gray-400);
    }
</style>
@endsection

@section('content')

<!-- Toolbar / Page Title -->
<div class="row mb-5">
    <div class="col-12 d-flex justify-content-between align-items-center page-title-accent">
        <h1 class="page-title">{{ __('Child Panel của bạn') }}</h1>
        <div id="topAction" class="{{ request('action') == 'create' ? 'd-none' : '' }}">
            <button class="btn btn-danger btn-sm" onclick="toggleView('wizard')">
                <i class="fas fa-plus me-1"></i> {{ __('Tạo Child Panel mới') }}
            </button>
        </div>
    </div>
</div>

<!-- List View -->
<div id="listView" class="card card-flush {{ request('action') == 'create' ? 'd-none' : '' }}">
    <div class="card-body">
        @if($childs->isEmpty())
            <div class="d-flex flex-column flex-center py-10">
                <div class="symbol symbol-100px symbol-circle mb-5 bg-light-primary text-primary">
                    <i class="fas fa-box-open fs-1"></i>
                </div>
                <h5 class="text-gray-800 fs-2 fw-bold mb-2">{{ __('Chưa có Child Panel nào') }}</h5>
                <p class="text-muted fs-6 mb-5">{{ __('Hãy tạo Child Panel đầu tiên của bạn để bắt đầu kinh doanh.') }}</p>
                <button class="btn btn-primary" onclick="toggleView('wizard')">
                    <i class="fas fa-plus me-2"></i> {{ __('Tạo ngay') }}
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-300 align-middle gs-0 gy-3">
                    <thead>
                        <tr class="fw-bolder text-muted">
                            <th class="min-w-50px">ID</th>
                            <th class="min-w-150px">{{ __('Tên miền') }}</th>
                            <th class="min-w-100px">{{ __('Trạng thái') }}</th>
                            <th class="min-w-100px">{{ __('Key') }}</th>
                            <th class="min-w-100px">{{ __('DNS') }}</th>
                            <th class="min-w-120px">{{ __('Hạn sử dụng') }}</th>
                            <th class="min-w-120px text-end">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($childs as $child)
                            <tr>
                                <td><span class="text-gray-800 fw-bold">#{{ $child->id }}</span></td>
                                <td>
                                    <a href="http://{{ $child->name }}" target="_blank" class="text-gray-800 text-hover-primary fw-bolder">{{ $child->name }}</a>
                                </td>
                                <td>
                                    @if ($child->status == 'active')
                                        <span class="badge badge-light-success">{{ __('Hoạt động') }}</span>
                                    @elseif ($child->status == 'inactive')
                                        <span class="badge badge-light-danger">{{ __('Ngừng') }}</span>
                                    @elseif ($child->status == 'pending')
                                        <span class="badge badge-light-warning">{{ __('Chờ') }}</span>
                                    @else
                                        <span class="badge badge-light-secondary">{{ $child->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-light-primary" onclick="showKeyWebsite('{{ $child->name }}')" title="{{ __('Xem Key') }}">
                                        <i class="fas fa-key fs-6"></i>
                                    </button>
                                </td>
                                <td>
                                    @if (isset($child->data['nameserver1']))
                                        <span class="badge badge-light-info">{{ __('Đã trỏ') }}</span>
                                    @else
                                        <span class="badge badge-light-danger">{{ __('Chưa') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($child->expired_at)
                                        @if($child->expired_at->isPast())
                                            <span class="text-danger fw-bold fs-7">{{ $child->expired_at->format('d/m/Y') }} ({{ __('Hết hạn') }})</span>
                                        @else
                                            <span class="text-gray-600 fw-bold fs-7">{{ $child->expired_at->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted fs-7">--/--/----</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light-success fw-bold" onclick="renewWebsite('{{ $child->id }}', '{{ $child->name }}')">
                                        <i class="fas fa-sync-alt me-1"></i> {{ __('Gia hạn') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Wizard View -->
<div id="createWizard" class="{{ request('action') == 'create' ? '' : 'd-none' }}">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            
            <!-- Header Info Card -->
            <div class="card child-panel-card mb-10">
                <div class="d-flex flex-stack flex-wrap gap-4">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <span class="symbol-label bg-light-primary">
                                <i class="fas fa-crown text-primary fs-2"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <h2 class="text-gray-900 fs-4 fw-bolder mb-1">{{ __('Tạo Child Panel SMM') }}</h2>
                            <div class="text-muted fw-bold fs-7">{{ __('Sở hữu website riêng với thương hiệu của bạn') }}</div>
                        </div>
                    </div>
                    <div class="d-flex flex-column text-end">
                        <span class="text-muted fw-bold fs-7 text-uppercase">{{ __('Phí duy trì') }}</span>
                        <span class="text-primary fw-bolder fs-3">
                             {{ convert_currency(s('fee_childpanel') ?? 0, Auth::user()->currency, false) }} 
                             <span class="fs-6 text-gray-500">{{ Auth::user()->currency }}/{{ __('Tháng') }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stepper Navigation -->
            <div class="cp-stepper-container">
                <div class="cp-progress-track">
                    <div class="cp-progress-bar" id="stepperProgressBar" style="width: 0%"></div>
                </div>
                
                <div class="cp-step-item active" data-step="1" onclick="jumpToStep(1)">
                    <div class="cp-step-circle">1</div>
                    <div class="cp-step-label">{{ __('Chuẩn bị Domain') }}</div>
                </div>
                <div class="cp-step-item" data-step="2" onclick="jumpToStep(2)">
                    <div class="cp-step-circle">2</div>
                    <div class="cp-step-label">{{ __('Cấu hình DNS') }}</div>
                </div>
                <div class="cp-step-item" data-step="3" onclick="jumpToStep(3)">
                    <div class="cp-step-circle">3</div>
                    <div class="cp-step-label">{{ __('Thông tin') }}</div>
                </div>
                <div class="cp-step-item" data-step="4">
                    <div class="cp-step-circle">4</div>
                    <div class="cp-step-label">{{ __('Hoàn thành') }}</div>
                </div>
            </div>

            <!-- Wizard Form -->
            <form id="childPanelForm" action="{{ route('request-action') }}" method="POST" bvq-request>
                @csrf
                <input type="hidden" name="action" value="create-childpanel">

                <!-- Step 1 Content -->
                <div class="wizard-content active" data-step="1">
                    <div class="card child-panel-card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">{{ __('Bước 1: Chuẩn bị Tên miền') }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label fw-bolder text-dark fs-6">{{ __('Yêu cầu tên miền:') }}</label>
                                    <ul class="req-list list-unstyled mt-2">
                                        <li><i class="fas fa-check-circle"></i> {{ __('Đã được đăng ký và kích hoạt') }}</li>
                                        <li><i class="fas fa-check-circle"></i> {{ __('Có quyền quản lý DNS') }}</li>
                                        <li><i class="fas fa-check-circle"></i> {{ __('Không dùng tên miền miễn phí (.tk, .ml...)') }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label fw-bolder text-dark fs-6">{{ __('Gợi ý nhà cung cấp:') }}</label>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <span class="badge-provider">Namecheap</span>
                                        <span class="badge-provider">GoDaddy</span>
                                        <span class="badge-provider">Mat Bao</span>
                                        <span class="badge-provider">Tenten</span>
                                    </div>
                                    <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-3 mt-4">
                                        <i class="fas fa-exclamation-triangle text-warning fs-4 me-3 mt-1"></i>
                                        <div class="d-flex flex-column pe-0 pe-sm-10">
                                            <span class="fw-bold text-gray-700 fs-7">{{ __('Hãy đảm bảo bạn đã sở hữu tên miền trước khi tiếp tục.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="separator separator-dashed my-5"></div>

                            <div class="fv-row">
                                <label class="form-label required fs-6 fw-bolder text-dark">{{ __('Kiểm tra tên miền của bạn') }}</label>
                                <div class="input-group input-group-solid">
                                    <input type="text" class="form-control form-control-solid" name="website" id="domainInput" placeholder="example.com">
                                    <button type="button" class="btn btn-primary" onclick="validateDomainStep()">{{ __('Kiểm tra') }}</button>
                                </div>
                                <div id="domainResult" class="form-text mt-2"></div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end py-6">
                            <button type="button" class="btn btn-light me-3" onclick="toggleView('list')">{{ __('Hủy') }}</button>
                            <button type="button" class="btn btn-primary" id="nextBtn1" onclick="goToStep(2)" disabled>{{ __('Tiếp tục') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Content -->
                <div class="wizard-content" data-step="2">
                    <div class="card child-panel-card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">{{ __('Bước 2: Cấu hình DNS') }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-8">
                                <i class="fas fa-info-circle fs-2tx text-primary me-4"></i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-bold">
                                        <h4 class="text-gray-900 fw-bolder">{{ __('Hướng dẫn') }}</h4>
                                        <div class="fs-6 text-gray-700">{{ __('Vui lòng truy cập trang quản lý tên miền của bạn và thay đổi Nameserver (NS) về 2 địa chỉ sau:') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label fw-bolder text-gray-700">{{ __('Nameserver 1') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" value="burt.ns.cloudflare.com" readonly>
                                        <button type="button" class="btn btn-light-primary" onclick="copyText('burt.ns.cloudflare.com')"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label fw-bolder text-gray-700">{{ __('Nameserver 2') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" value="leia.ns.cloudflare.com" readonly>
                                        <button type="button" class="btn btn-light-primary" onclick="copyText('leia.ns.cloudflare.com')"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mt-5">
                                <label class="form-check form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" id="nsConfirm" onchange="toggleStep2Next()"/>
                                    <span class="form-check-label fw-bold text-gray-700">{{ __('Tôi xác nhận đã thay đổi Nameserver cho tên miền này') }}</span>
                                </label>
                            </div>
                        </div>
                         <div class="card-footer d-flex justify-content-end py-6">
                            <button type="button" class="btn btn-light me-3" onclick="goToStep(1)">{{ __('Quay lại') }}</button>
                            <button type="button" class="btn btn-primary" id="nextBtn2" onclick="goToStep(3)" disabled>{{ __('Tiếp tục') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3 Content -->
                <div class="wizard-content" data-step="3">
                    <div class="card child-panel-card">
                         <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 text-dark">{{ __('Bước 3: Thông tin Quản trị') }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-6 mb-5">
                                    <label class="form-label required fw-bolder text-dark fs-6">{{ __('Tên Website') }}</label>
                                    <input type="text" class="form-control form-control-solid" name="panel_name" placeholder="{{ __('Ví dụ: SMM Panel Top 1') }}" required>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label required fw-bolder text-dark fs-6">{{ __('Tài khoản Admin') }}</label>
                                    <input type="text" class="form-control form-control-solid" name="admin_username" value="admin" required>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label required fw-bolder text-dark fs-6">{{ __('Mật khẩu Admin') }}</label>
                                    <input type="password" class="form-control form-control-solid" name="admin_password" id="admin_password" placeholder="••••••••" required>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label required fw-bolder text-dark fs-6">{{ __('Nhập lại mật khẩu') }}</label>
                                    <input type="password" class="form-control form-control-solid" id="confirm_password" placeholder="••••••••" required>
                                </div>
                            </div>
                            
                            <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-4">
                                <i class="fas fa-lock text-danger fs-3 me-3 mt-1"></i>
                                <div class="d-flex flex-column">
                                    <h5 class="text-danger fw-bolder mb-1">{{ __('Lưu ý quan trọng') }}</h5>
                                    <span class="text-gray-700 fs-7">{{ __('Thông tin này dùng để đăng nhập trang quản trị (Admin Panel). Bạn không thể thay đổi Tên tài khoản sau khi tạo.') }}</span>
                                </div>
                            </div>
                        </div>
                         <div class="card-footer d-flex justify-content-end py-6">
                            <button type="button" class="btn btn-light me-3" onclick="goToStep(2)">{{ __('Quay lại') }}</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                {{ __('Xác nhận & Tạo ngay') }} <i class="fas fa-rocket ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4 Content -->
                <div class="wizard-content" data-step="4">
                    <div class="card child-panel-card">
                        <div class="card-body text-center py-20">
                            <div class="mb-10">
                                <i class="fas fa-check-circle text-success fs-5x"></i>
                            </div>
                            <h1 class="fw-bolder text-dark mb-5">{{ __('Yêu cầu đã được gửi!') }}</h1>
                            <div class="fs-6 text-gray-600 mb-10">
                                {{ __('Hệ thống đang tiến hành khởi tạo website cho bạn.') }}
                                <br>{{ __('Quá trình này có thể mất vài phút. Vui lòng kiểm tra danh sách Child Panel.') }}
                            </div>
                            <button type="button" class="btn btn-primary" onclick="toggleView('list')">{{ __('Quay lại danh sách') }}</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentStep = 1;

    $(document).ready(function() {
        updateUI();
    });

    function toggleView(view) {
        if (view === 'wizard') {
            $('#listView').addClass('d-none');
            $('#topAction').addClass('d-none');
            $('#createWizard').removeClass('d-none');
            goToStep(1);
        } else {
            $('#createWizard').addClass('d-none');
            $('#listView').removeClass('d-none');
            $('#topAction').removeClass('d-none');
        }
    }

    function goToStep(step) {
        if (step === 3 && !$('#nsConfirm').is(':checked')) {
            toastr.error('{{ __("Vui lòng xác nhận đã trỏ Nameserver") }}');
            return;
        }

        currentStep = step;
        updateUI();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function jumpToStep(step) {
        if (step < currentStep || (step === 2 && !$('#nextBtn1').is(':disabled'))) {
            goToStep(step);
        }
    }

    function updateUI() {
        // Update Content
        $('.wizard-content').removeClass('active');
        $(`.wizard-content[data-step="${currentStep}"]`).addClass('active');

        // Update Stepper
        $('.cp-step-item').removeClass('active completed');
        $('.cp-step-item').each(function() {
            const step = $(this).data('step');
            if (step < currentStep) {
                $(this).addClass('completed');
            } else if (step === currentStep) {
                $(this).addClass('active');
            }
        });

        // Update Progress Bar
        const progress = ((currentStep - 1) / 3) * 100;
        $('#stepperProgressBar').css('width', progress + '%');
    }

    function validateDomainStep() {
        const domain = $('#domainInput').val().trim();
        const resultDiv = $('#domainResult');
        const nextBtn = $('#nextBtn1');
        
        if (!domain) {
            toastr.error('{{ __("Vui lòng nhập tên miền") }}');
            return;
        }

        const domainRegex = /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/;
        if (!domainRegex.test(domain)) {
            resultDiv.html('<span class="text-danger fw-bold">{{ __("Tên miền không hợp lệ.") }}</span>');
            nextBtn.prop('disabled', true);
            return;
        }

        resultDiv.html('<span class="text-success fw-bold"><i class="fas fa-check me-1"></i> {{ __("Tên miền hợp lệ. Bạn có thể tiếp tục.") }}</span>');
        nextBtn.prop('disabled', false);
    }

    function toggleStep2Next() {
        const isChecked = $('#nsConfirm').is(':checked');
        $('#nextBtn2').prop('disabled', !isChecked);
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            toastr.success('{{ __("Đã sao chép: ") }}' + text);
        });
    }

    function showKeyWebsite(domain) {
        Swal.fire({
            title: "{{ __('Xác thực') }}",
            text: "{{ __('Nhập mật khẩu để xem Key cho ') }}" + domain,
            input: 'password',
            showCancelButton: true,
            confirmButtonText: '{{ __("Xem") }}',
            preConfirm: (password) => {
                return $.ajax({
                    url: '{{ route('request-action') }}',
                    type: 'POST',
                    data: {
                        action: 'key-childpanel',
                        password: password,
                        domain: domain,
                        _token: '{{ csrf_token() }}'
                    }
                }).catch(error => {
                    Swal.showValidationMessage(`Lỗi: ${error.responseJSON.message}`);
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value.success) {
                Swal.fire({
                    title: 'Activation Key',
                    html: `<div class="p-4 bg-light rounded mt-3 fs-2 fw-bolder text-dark"><code>${result.value.data.key}</code></div>`,
                    icon: 'success'
                });
            }
        });
    }

    function renewWebsite(id, domain) {
        Swal.fire({
            title: "{{ __('Xác nhận gia hạn') }}",
            text: "{{ __('Bạn có chắc chắn muốn gia hạn Child Panel ') }}" + domain + " {{ __(' thêm 1 tháng với phí là ') }} " + '{{ convert_currency(s('fee_childpanel') ?? 0, Auth::user()->currency, true) }}' + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("Gia hạn ngay") }}',
            cancelButtonText: '{{ __("Hủy") }}',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '{{ route('request-action') }}',
                    type: 'POST',
                    data: {
                        action: 'renew-childpanel',
                        id: id,
                        _token: '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message);
                    }
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(error.message || 'Có lỗi xảy ra');
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '{{ __("Thành công") }}',
                    text: result.value.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            }
        });
    }

    $(document).on('bvq-request:success', '#childPanelForm', function(e, response) {
        if (response.success) {
            goToStep(4);
        }
    });

    $(document).on('bvq-request:error', '#childPanelForm', function(e, response) {
         toastr.error(response.message);
    });
</script>
@endsection
