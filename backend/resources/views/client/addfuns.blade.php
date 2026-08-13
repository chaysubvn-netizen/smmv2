@extends('client.layouts.master')
@section('title', __('Nạp tiền'))

@section('content')
@include('client.layouts.breadcrumb', ['title' => 'Nạp tiền'])
    <style>
        /* Metronic Variables & Theme Adjustments */
        [data-bs-theme="dark"] .bank-card-premium {
            background: #1e1e2d;
            border-color: #323248;
        }
        
        [data-bs-theme="dark"] .copy-item {
            background: #151521;
        }

        .bank-card-premium {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid var(--bs-gray-200);
            cursor: default;
        }

        .bank-card-premium:hover {
            border-color: var(--bs-primary);
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.05);
        }

        .bank-logo-container {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--bs-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .bank-logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .copy-item {
            background: var(--bs-gray-100);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .copy-item:hover {
            background: var(--bs-gray-200);
        }

        .copy-item .info {
            display: flex;
            flex-direction: column;
        }

        .copy-item .label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--bs-gray-500);
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }

        .copy-item .value {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--bs-gray-800);
            font-family: 'Inter', sans-serif;
        }

        .btn-copy {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: var(--bs-body-bg);
            color: var(--bs-primary);
            border: 1px solid var(--bs-gray-300);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-copy:hover {
            background: var(--bs-primary);
            color: #ffffff;
            border-color: var(--bs-primary);
            transform: scale(1.05);
        }

        .qr-toggle-btn {
            background: rgba(var(--bs-primary-rgb), 0.08);
            color: var(--bs-primary);
            font-weight: 700;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .qr-toggle-btn:hover {
            background: rgba(var(--bs-primary-rgb), 0.15);
            transform: translateY(-1px);
        }

        .qr-content {
            display: none;
            padding-top: 1.25rem;
            text-align: center;
        }

        .qr-content.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .method-content {
            display: none;
        }

        .method-content.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        /* Custom Table Styling */
        .table-recharge thead th {
            background: transparent;
            border-bottom: 2px solid var(--bs-gray-200);
            color: var(--bs-gray-500);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 1rem 1.25rem;
        }

        .table-recharge tbody td {
            padding: 1.25rem;
            vertical-align: middle;
            color: var(--bs-gray-800);
            font-weight: 600;
            border-bottom: 1px dashed var(--bs-gray-200);
        }

        .table-recharge tbody tr:last-child td {
            border-bottom: none;
        }

        .promotion-card {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-info-rgb), 0.05) 100%);
            border: 1px dashed var(--bs-primary);
            border-radius: 16px;
            padding: 1.5rem;
        }

        /* Binance Pay Styles */
        .binance-stepper {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
            position: relative;
        }
        .binance-stepper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 25%;
            right: 25%;
            height: 2px;
            background: var(--bs-gray-200);
            z-index: 1;
        }
        .stepper-item {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .stepper-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bs-gray-200);
            color: var(--bs-gray-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin: 0 auto 0.75rem;
            border: 3px solid var(--bs-body-bg);
            transition: all 0.3s;
        }
        .stepper-item.active .stepper-number {
            background: var(--bs-primary);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.1);
        }
        .stepper-item.completed .stepper-number {
            background: var(--bs-success);
            color: #fff;
        }
        .stepper-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--bs-gray-500);
            transition: all 0.3s;
        }
        .stepper-item.active .stepper-label {
            color: var(--bs-primary);
        }

        .binance-qr-box {
            background: #fff;
            padding: 1.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        [data-bs-theme="dark"] .binance-qr-box {
            background: #1e1e2d;
        }

        .binance-verify-img {
            max-width: 100%;
            border-radius: 0.75rem;
            border: 1px solid var(--bs-gray-200);
        }

        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>


</style>

<div class="container-xxl py-5">
    <!-- Page Header & Notification -->
    @if (s('notice_recharge'))
        <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-10">
            <div class="d-flex flex-column pe-0 pe-sm-10 w-100">
                <div class="fs-6 text-gray-700">
                    {!! nl2br(e(s(''))) !!}
                </div>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @else
        <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-10">
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h4 class="mb-2 text-danger fw-bolder">{{ __('LƯU Ý QUAN TRỌNG!') }}</h4>
                <div class="fs-6 text-gray-700">
                    <p class="mb-1">🔥 <strong>{{ date('d/m/Y') }}:</strong> {{ __('Tỉ giá 1 USD = :rate VND', ['rate' => number_format(s('rate_usdt') ?? 26000)]) }}</p>
                    <p class="mb-1">• {{ __('Khách hàng sử dụng Binance vui lòng chọn hình thức nạp tiền Auto USDT - Binance. Nhanh và không mất phí.') }}</p>
                    <p class="mb-0 text-danger fw-bold">• {{ __('SẼ HẠN CHẾ SỬ DỤNG BANK VIỆT NAM. VUI LÒNG DÙNG USDT ĐỂ ĐƯỢC ƯU ĐÃI.') }}</p>
                </div>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @endif

    <div class="row g-10 mb-10">
        <!-- Left Column: Payment Form -->
        <div class="col-xl-7">
            <div class="card card-flush shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-900 fs-3">{{ __('Phương thức nạp tiền') }}</span>
                        <span class="text-gray-500 mt-1 fw-bold fs-7">{{ __('Chọn kênh thanh toán phù hợp') }}</span>
                    </h3>
                </div>
                
                <div class="card-body pt-5">
                    <div class="mb-8">
                        <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{ __('Chọn phương thức') }}</label>
                        <select class="form-select form-select-solid fw-bolder" id="paymentMethodSelect" data-control="select2" data-hide-search="true">
                            <option value="" selected disabled>{{ __('Vui lòng chọn...') }}</option>
                            @if (s('bank_status') == 'active')
                            <option value="banking" data-icon="https://i.imgur.com/G4xKIDf.png">{{ __('Auto - Ngân Hàng (Việt Nam)') }}</option>
                            @endif
                            @if (s('usdt_status') == 'active')
                            <option value="usdt" data-icon="https://i.imgur.com/iBEGgng.png">{{ __('Auto - Crypto (USDT BEP20/TRC20)') }}</option>
                            @endif
                            @if (s('binance_status') == 'active')
                            <option value="binance" data-icon="https://i.imgur.com/G4xKIDf.png">{{ __('Auto - Binance Pay (USDT)') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="separator separator-dashed my-8"></div>

                    <!-- Banking Content -->
                    <div id="banking-info" class="method-content">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-header bg-primary py-6 text-center d-block">
                                <h3 class="card-title justify-content-center">
                                    <span class="text-white fw-bolder fs-3"><i class="ri-bank-card-line me-2"></i>{{ __('Tạo hoá đơn nạp tiền') }}</span>
                                </h3>
                            </div>
                            <div class="card-body p-8">
                                <form action="{{ route('deposit.create') }}" method="POST" bvq-request>
                                    @csrf
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-bolder text-gray-700 mb-4">{{ __('Số tiền nạp') }}</label>
                                        <div class="position-relative">
                                            <input type="number" class="form-control form-control-solid form-control-lg fw-bolder fs-3 py-5 ps-6" name="amount" id="recharge_amount" placeholder="50,000" value="50000">
                                            <span class="position-absolute top-50 end-0 translate-middle-y me-6 badge badge-light-primary fw-bolder fs-6">VND</span>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-10">
                                        @foreach([50000, 100000, 200000, 500000, 1000000, 2000000] as $amount)
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline btn-outline-dashed btn-active-light-primary w-100 fw-bold py-4 px-0 fs-7 border-2" onclick="setAmount({{ $amount }})">
                                                {{ number_format($amount) }}đ
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-bolder text-gray-700 mb-4">{{ __('Chọn ngân hàng thanh toán') }}</label>
                                        <div class="row g-4">
                                            @foreach ($bankings as $index => $banking)
                                            <div class="col-12">
                                                <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex align-items-center p-5 cursor-pointer w-100 border-2 {{ $index == 0 ? 'active' : '' }}">
                                                    <input class="form-check-input d-none" type="radio" name="bank_id" value="{{ $banking->id }}" {{ $index == 0 ? 'checked' : '' }} />
                                                    <div class="symbol symbol-50px me-5">
                                                        <img src="{{ asset($banking->icon) }}" class="rounded-3" alt="{{ $banking->bank_name }}" />
                                                    </div>
                                                    <div class="d-flex flex-column text-start">
                                                        <span class="fw-bolder text-gray-800 text-uppercase fs-6">{{ $banking->bank_name }}</span>
                                                        <span class="fw-bold text-gray-500 fs-8">{{ $banking->account_number }} - {{ strtoupper($banking->account_name) }}</span>
                                                    </div>
                                                    <div class="ms-auto checked-icon">
                                                        <i class="ri-checkbox-circle-fill text-primary fs-1"></i>
                                                    </div>
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 fw-bolder py-5 fs-4 shadow-sm">
                                        {{ __('TIẾP TỤC THANH TOÁN') }} <i class="ri-arrow-right-line ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- USDT Content -->
                    <div id="usdt-info" class="method-content">
                        <form action="" method="POST" bvq-request>
                            <div class="mb-8">
                                <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{ __('Số tiền (USDT)') }}</label>
                                <div class="position-relative">
                                    <input type="number" class="form-control form-control-solid fw-bolder fs-4 py-4" name="amount" placeholder="Ví dụ: 10" value="10">
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-5 text-gray-500 fw-bold fs-5">USDT</span>
                                </div>
                            </div>

                            <div class="d-flex flex-stack bg-light-success rounded-3 p-4 mb-8 border border-success border-dashed">
                                <span class="text-gray-700 fw-bold fs-6">{{ __('Chiết khấu ưu đãi') }}:</span>
                                <span class="badge badge-success fs-5 fw-bolder">{{ s('discount_usdt') }}%</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bolder py-4 fs-5">
                                <i class="fas fa-rocket me-2"></i> {{ __('Thanh toán ngay') }}
                            </button>
                        </form>
                    </div>

                    <!-- Binance Pay Content -->
                    <div id="binance-info" class="method-content">
                        <div class="binance-stepper">
                            <div class="stepper-item active" id="step-tab-1">
                                <div class="stepper-number">1</div>
                                <div class="stepper-label">{{ __('Make payment') }}</div>
                            </div>
                            <div class="stepper-item" id="step-tab-2">
                                <div class="stepper-number">2</div>
                                <div class="stepper-label">{{ __('Verify payment') }}</div>
                            </div>
                        </div>

                        <!-- Step 1: Info -->
                        <div class="step-content active" id="binance-step-1">
                            <div class="mb-8">
                                <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{ __('Số tiền (USDT)') }}</label>
                                <div class="position-relative">
                                    <input type="number" class="form-control form-control-solid fw-bolder fs-4 py-4" id="binance_amount_input" placeholder="Ví dụ: 10" value="10">
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-5 text-gray-500 fw-bold fs-5">USDT</span>
                                </div>
                            </div>

                            <div class="text-center mb-8">
                                <h1 class="fw-bolder text-gray-800 mb-2"><span id="display_binance_amount">10</span> USDT</h1>
                                <p class="text-gray-500 fw-bold">{{ __('Send to Binance ID') }}</p>
                            </div>

                            <div class="copy-item shadow-sm border border-success border-dashed bg-light-success mb-5">
                                <div class="info">
                                    <span class="label">{{ __('Nội dung nạp tiền') }}</span>
                                    <span class="value text-success">{{ s('transfer_code') }}{{ Auth::id() }}</span>
                                </div>
                                <button class="btn-copy text-success border-success-subtle" onclick="copyToClipboard('{{ s('transfer_code') }}{{ Auth::id() }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>

                            <div class="copy-item shadow-sm border border-primary border-dashed bg-light-primary mb-8">
                                <div class="info">
                                    <span class="label">{{ __('Binance ID') }}</span>
                                    <span class="value text-primary">{{ s('binance_id') ?? '422336960' }}</span>
                                </div>
                                <button class="btn-copy text-primary border-primary-subtle" onclick="copyToClipboard('{{ s('binance_id') ?? '422336960' }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>

                            <div class="binance-qr-box text-center">
                                <div class="fw-bolder text-gray-800 mb-4">{{ __('Scan with Binance App to pay') }}</div>
                                <img src="{{ s('binance_qr') ?? 'https://i.imgur.com/G4xKIDf.png' }}" class="rounded shadow-sm mb-4" style="max-width: 200px;" alt="Binance QR">
                                <div class="text-gray-500 fs-7 px-5">{{ __('Please ensure the transfer amount is exactly as requested.') }}</div>
                            </div>

                            <button type="button" class="btn btn-primary w-100 fw-bolder py-4 fs-5" onclick="nextBinanceStep(2)">
                                {{ __('Confirm payment') }} <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <!-- Step 2: Verification -->
                        <div class="step-content" id="binance-step-2">
                            <form action="{{ route('addfuns.post') }}" method="POST" bvq-request>
                                @csrf
                                <input type="hidden" name="method" value="binance">
                                <input type="hidden" name="amount" id="binance_hidden_amount" value="10">
                                <div class="bg-light-warning rounded-3 p-5 mb-8 border border-warning border-dashed">
                                    <div class="d-flex flex-stack mb-3">
                                        <div class="text-gray-600 fs-8 fw-bold">{{ __('Amount') }}</div>
                                        <div class="text-gray-800 fs-4 fw-bolder"><span id="summary_binance_amount">10</span> USDT</div>
                                    </div>
                                    <div class="separator separator-dashed my-3"></div>
                                    <div class="d-flex flex-stack mb-3">
                                        <div class="text-gray-600 fs-8 fw-bold">{{ __('Nội dung nạp tiền') }}</div>
                                        <div class="text-gray-800 fs-6 fw-bolder">{{ s('transfer_code') }}{{ Auth::id() }} <i class="fas fa-copy text-success ms-1 cursor-pointer" onclick="copyToClipboard('{{ s('transfer_code') }}{{ Auth::id() }}')"></i></div>
                                    </div>
                                    <div class="d-flex flex-stack">
                                        <div class="text-gray-600 fs-8 fw-bold">{{ __('Send to Binance ID') }}</div>
                                        <div class="text-gray-800 fs-6 fw-bolder">{{ s('binance_id') ?? '422336960' }} <i class="fas fa-check-circle text-primary ms-1"></i></div>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{ __('Enter you Binance Order ID') }}</label>
                                    <input type="text" class="form-control form-control-solid fw-bolder py-4" name="binance_order_id" placeholder="Ex: 1234567890">
                                </div>

                                <div class="mb-8">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="" id="agreementBinance" required/>
                                        <label class="form-check-label fw-bold text-gray-700 fs-7" for="agreementBinance">
                                            {{ __('I understand after the funds added I will not ask fraudulent dispute or charge-back.') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="text-center mb-8">
                                    <img src="data:image/png;base64,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" class="binance-verify-img mb-5" alt="Where to find Order ID">
                                    <div class="text-start fs-7 text-gray-600">
                                        <p class="mb-2">1. {{ __('Copy the Order ID from the successful payment details in your Binance account.') }}</p>
                                        <p class="mb-0">2. {{ __('Paste it into the field above and tap "Verify payment".') }}</p>
                                    </div>
                                </div>

                                <div class="d-flex gap-4">
                                    <button type="button" class="btn btn-light-primary fw-bolder py-4 px-6" onclick="nextBinanceStep(1)">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-root fw-bolder py-4 fs-5">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Notes -->
        <div class="col-xl-5">
            <div class="card card-flush shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-primary fs-3" style="color: #4680ff !important;">{{ __('Ghi chú') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <p class="text-gray-700 mb-6 fs-6">{{ __('Vui lòng đọc kỹ mô tả của từng phương thức thanh toán trước khi nạp tiền!') }}</p>
                    
                    <div class="mb-5 text-gray-800 fw-bolder fs-5" style="font-family: 'Inter', sans-serif;">
                        <div class="mb-2">{{ __('Khuyến mãi 5% cho đơn nạp từ 100$') }}</div>
                        <div>{{ __('Khuyến mãi 10% cho đơn nạp từ 2.000$') }}</div>
                    </div>
                    
                    <p class="text-gray-600 fs-7 mb-6 fst-italic">
                        {{ __('Người dùng API có thể liên hệ với chúng tôi để được hỗ trợ thêm về ưu đãi này.') }}
                    </p>

                    <p class="text-gray-700 fs-6">
                        {{ __('Khi nạp tiền và sử dụng dịch vụ, mặc định bạn đã đọc kỹ và đồng ý với mọi điều khoản/ghi chú của hệ thống. Nếu cần hỗ trợ thêm, vui lòng') }} <a href="{{ route('tickets') ?? '#' }}" class="fw-bolder text-primary">{{ __('liên hệ chúng tôi') }}</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Row -->
    <div class="row g-10">
        <div class="col-xl-12">
            <div class="card card-flush shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder text-gray-900 fs-3">{{ __('Lịch sử giao dịch') }}</span>
                        <span class="text-gray-500 mt-1 fw-bold fs-7">{{ __('Quản lý các lần nạp tiền của bạn') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button class="btn btn-sm btn-light-primary fw-bolder" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                            <i class="fas fa-filter me-2"></i> {{ __('Lọc dữ liệu') }}
                        </button>
                    </div>
                </div>

                <div class="card-body pt-5">
                    <!-- Filter Panel -->
                    <div class="collapse mb-8" id="filterPanel">
                        <div class="bg-light rounded-4 p-6 border border-dashed border-gray-300">
                            <form method="GET" action="{{ route('addfuns') }}">
                                <div class="row g-5">
                                    <div class="col-md-5">
                                        <label class="form-label fs-7 fw-bolder text-gray-700">{{ __('Từ ngày') }}</label>
                                        <input type="date" name="from_date" class="form-control form-control-solid" value="{{ request('from_date', date('Y-m-01')) }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fs-7 fw-bolder text-gray-700">{{ __('Đến ngày') }}</label>
                                        <input type="date" name="to_date" class="form-control form-control-solid" value="{{ request('to_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100 fw-bolder">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-recharge">
                            <thead>
                                <tr>
                                    <th class="ps-0 min-w-50px">ID</th>
                                    <th class="min-w-100px">{{ __('Nạp từ') }}</th>
                                    <th class="min-w-80px">{{ __('Loại') }}</th>
                                    <th class="min-w-120px">{{ __('Số tiền') }}</th>
                                    <th class="min-w-100px text-end">{{ __('Trạng thái') }}</th>
                                    <th class="min-w-150px text-end pe-0">{{ __('Thời gian') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recharges as $transaction)
                                <tr>
                                    <td class="ps-0">
                                        @if ($transaction->method == 'usdt' && $transaction->status == 'waiting')
                                        <a href="https://app.fpayment.net/payment/{{ $transaction->transaction_id }}" target="_blank" class="fw-bolder text-primary text-hover-primary">
                                            #{{ $transaction->id }}
                                        </a>
                                        @else
                                        <span class="text-gray-600">#{{ $transaction->id }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="badge badge-light-primary fw-bolder text-uppercase fs-8">{{ ucfirst($transaction->method) }}</div>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fs-7">{{ $transaction->type }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-900 fw-bolder fs-6">{{ convert_currency($transaction->amount, Auth::user()->currency, false) }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if (in_array($transaction->status, ['pending', 'waiting']))
                                            <span class="badge badge-light-warning fw-bolder px-4 py-3">{{ $transaction->method == 'banking' ? __('Chờ thanh toán') : __('Chờ xử lý') }}</span>
                                            @if($transaction->method == 'banking')
                                                <a href="{{ route('deposit.pay', $transaction->id) }}" class="btn btn-sm btn-icon btn-light-info ms-2" title="{{ __('Thanh toán') }}">
                                                    <i class="fas fa-external-link-alt fs-9"></i>
                                                </a>
                                            @endif
                                        @elseif ($transaction->status == 'completed')
                                            <span class="badge badge-light-success fw-bolder px-4 py-3">{{ __('Thành công') }}</span>
                                        @elseif ($transaction->status == 'expired')
                                            <span class="badge badge-light-secondary fw-bolder px-4 py-3">{{ __('Hết hạn') }}</span>
                                        @else
                                            <span class="badge badge-light-danger fw-bolder px-4 py-3">{{ __('Thất bại') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-0">
                                        <span class="text-gray-500 fs-7 fw-bold">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="text-center py-20">
                                        <svg width="184" height="152" viewBox="0 0 184 152"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g fill="none" fill-rule="evenodd">
                                                <g transform="translate(24 31.67)">
                                                    <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89"
                                                        rx="67.797" ry="12.668"></ellipse>
                                                    <path
                                                        d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z"
                                                        fill="#AEB8C2"></path>
                                                    <path
                                                        d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z"
                                                        fill="url(#linearGradient-1)" transform="translate(13.56)">
                                                    </path>
                                                    <path
                                                        d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z"
                                                        fill="#F5F5F7"></path>
                                                    <path
                                                        d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z"
                                                        fill="#DCE0E6"></path>
                                                </g>
                                                <path
                                                    d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z"
                                                    fill="#DCE0E6"></path>
                                                <g transform="translate(149.65 15.383)" fill="#FFF">
                                                    <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815">
                                                    </ellipse>
                                                    <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
                                            <div class="text-gray-400 fw-bold fs-5">{{ __('Chưa có giao dịch nào phát sinh') }}</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex flex-stack flex-wrap pt-10">
                        <div class="fs-6 fw-bold text-gray-700"></div>
                        <div class="pagination-custom">
                            {{ $recharges->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select elements
        const $methodSelect = $('#paymentMethodSelect');
        const bankInfo = document.getElementById('banking-info');
        const usdtInfo = document.getElementById('usdt-info');
        const binanceInfo = document.getElementById('binance-info');

        // Initialize Select2 with Custom Templates for Icons
        if ($.fn.select2) {
            const formatMethod = (item) => {
                if (!item.id) return item.text;
                const icon = $(item.element).data('icon');
                if (!icon) return item.text;
                
                return $(`<span><img src="${icon}" class="rounded-circle h-20px me-3" alt="icon"/>${item.text}</span>`);
            };

            $methodSelect.select2({
                minimumResultsForSearch: Infinity,
                placeholder: $methodSelect.data('placeholder'),
                templateSelection: formatMethod,
                templateResult: formatMethod
            });
        }

        // Handle Change Event
        $methodSelect.on('change', function() {
            const value = $(this).val();
            
            // Hide all first
            bankInfo.classList.remove('active');
            usdtInfo.classList.remove('active');
            if (binanceInfo) binanceInfo.classList.remove('active');

            // Show selected
            if (value === 'banking') {
                bankInfo.classList.add('active');
            } else if (value === 'usdt') {
                usdtInfo.classList.add('active');
            } else if (value === 'binance') {
                binanceInfo.classList.add('active');
            }
        });
    });

    function nextBinanceStep(step) {
        if (step === 2) {
            const amount = document.getElementById('binance_amount_input').value;
            if (!amount || amount <= 0) {
                showNotification('{{ __("Vui lòng nhập số tiền hợp lệ") }}', 'error');
                return;
            }
            document.getElementById('summary_binance_amount').innerText = amount;
            document.getElementById('display_binance_amount').innerText = amount;
            document.getElementById('binance_hidden_amount').value = amount;
        }

        // Toggle Step Tabs
        const tabs = document.querySelectorAll('.binance-stepper .stepper-item');
        tabs.forEach((tab, index) => {
            if (index + 1 === step) {
                tab.classList.add('active');
                tab.classList.remove('completed');
            } else if (index + 1 < step) {
                tab.classList.add('completed');
                tab.classList.remove('active');
            } else {
                tab.classList.remove('active', 'completed');
            }
        });

        // Toggle Step Content
        const contents = document.querySelectorAll('#binance-info .step-content');
        contents.forEach((content, index) => {
            if (index + 1 === step) {
                content.classList.add('active');
            } else {
                content.classList.remove('active');
            }
        });
    }

    function toggleQR(id) {
        const qrSection = document.getElementById(id);
        const allQR = document.querySelectorAll('.qr-content');
        
        // Hide others in the same group if needed, or just toggle this one
        qrSection.classList.toggle('show');
    }

    function copyToClipboard(text) {
        if (!navigator.clipboard) {
            // Fallback for older browsers
            let textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
            showNotification('{{ __("Đã sao chép: ") }}' + text);
            return;
        }

        navigator.clipboard.writeText(text).then(function() {
            showNotification('{{ __("Đã sao chép: ") }}' + text);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    function setAmount(amount) {
        document.getElementById('recharge_amount').value = amount;
    }

    function showNotification(message, type = 'success') {
        // Use Metronic's Swal (SweetAlert2) if available, otherwise fallback to alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                buttonsStyling: false,
                confirmButtonText: "{{ __('Đã hiểu') }}",
                customClass: {
                    confirmButton: "btn btn-primary"
                },
                timer: 2000
            });
        } else if (typeof toastr !== 'undefined') {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        } else {
            // Minimalist native notification if no library found
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.background = '#333';
            toast.style.color = '#fff';
            toast.style.padding = '10px 20px';
            toast.style.borderRadius = '5px';
            toast.style.zIndex = '9999';
            toast.innerText = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    }
    </script>
    @endsection