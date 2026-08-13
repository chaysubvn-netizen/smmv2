@extends('client.layouts.master')

@section('title', 'Bảng giá và Cấp bậc')

@section('content')
<div class="container-fluid py-5" style="min-height: 100vh;">
    
    <style>
        /* Modern Design Tokens using Metronic Variables */
        :root {
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            --card-radius: 16px;
        }

        /* Membership Cards */
        .rank-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(260px, 1fr));
            gap: 22px;
            margin-bottom: 40px;
        }
        .rank-card {
            position: relative;
            background: #f7f8fb;
            border-radius: 18px;
            padding: 22px 18px;
            border: 1px solid #e9ebf1;
            box-shadow: 0 10px 22px rgba(20, 25, 38, 0.06);
        }
        .rank-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .rank-avatar {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 28px;
            background: linear-gradient(135deg, #5c6bd6, #7e57c2);
        }
        .rank-ring {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            border: 3px solid #2f70ff;
            position: relative;
        }
        .rank-ring::after {
            content: "";
            position: absolute;
            inset: 16px;
            border: 2px solid currentColor;
            border-radius: 50%;
            opacity: .55;
        }
        .ring-blue { color: #2f70ff; border-color: #2f70ff; }
        .ring-yellow { color: #f2b100; border-color: #f2b100; }
        .ring-green { color: #15c39a; border-color: #15c39a; }
        .ring-red { color: #ef334e; border-color: #ef334e; }
        .rank-current {
            position: absolute;
            top: -10px;
            right: 16px;
            background: #111522;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            border-radius: 999px;
            padding: 6px 12px;
            text-transform: uppercase;
        }
        .rank-name { font-size: 36px; font-weight: 800; color: var(--bs-gray-900); margin-bottom: 4px; text-align: center; }
        .rank-subtitle { font-size: 24px; font-weight: 700; color: #83889a; margin-bottom: 14px; text-align: center; }
        .rank-feature {
            background: #eef0f4;
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }
        .rank-feature i {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            margin-top: 2px;
        }
        .feature-cyan { background: #16c7cc; }
        .feature-amber { background: #f6a400; }
        .feature-orange { background: #ff6215; }
        .rank-feature b {
            display: block;
            font-size: 17px;
            color: #303645;
            margin-bottom: 2px;
        }
        .rank-feature span {
            display: block;
            font-size: 14px;
            color: #8c93a5;
        }
        .rank-missing {
            background: #fff5e5;
            border-radius: 8px;
            padding: 10px 12px;
            color: #f2a400;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .rank-missing::after { display: none; }

        /* Filter Section */
        .filter-section {
            background: var(--bs-app-blank-bg, var(--bs-card-bg));
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            border: 1px solid var(--bs-card-border-color);
        }
        .search-wrapper { position: relative; }
        .search-wrapper i { position: absolute; left: 16px; top: 18px; color: var(--bs-gray-500); }
        .search-input { 
            padding-left: 45px !important; 
            height: 52px; 
            border-radius: 12px; 
            border: 1px solid var(--bs-gray-300);
            background: var(--bs-gray-100);
            color: var(--bs-gray-900);
        }
        .search-input:focus {
            background: var(--bs-app-blank-bg, var(--bs-card-bg));
            border-color: var(--bs-primary);
        }
        .filter-select { 
            height: 52px !important; 
            border-radius: 12px !important; 
            border: 1px solid var(--bs-gray-300) !important; 
            background: var(--bs-gray-100) !important; 
            color: var(--bs-gray-900) !important;
        }
        .btn-search-custom { height: 52px; border-radius: 12px; padding: 0 30px; font-weight: 700; background: var(--bs-primary); color: #fff; border: none; }
        .btn-reset-custom { height: 52px; border-radius: 12px; padding: 0 20px; font-weight: 700; background: var(--bs-gray-200); color: var(--bs-gray-700); border: none; }

        .table-container { background: var(--bs-app-blank-bg, var(--bs-card-bg)); border-radius: var(--card-radius); box-shadow: var(--card-shadow); overflow: hidden; border: 1px solid var(--bs-card-border-color); }
        .table-premium { width: 100%; border-collapse: collapse; }
        .table-premium th { background: var(--bs-gray-100); padding: 20px 24px; font-size: 11px; font-weight: 800; color: var(--bs-gray-600); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--bs-card-border-color); }
        .table-premium td { padding: 20px 24px; vertical-align: middle; border-bottom: 1px solid var(--bs-gray-100); }
        .category-header { background: var(--bs-gray-100); padding: 14px 24px; }
        .category-title { font-size: 14px; font-weight: 800; color: var(--bs-gray-900); display: flex; align-items: center; }
        .category-title i { color: var(--bs-primary) !important; }
        .service-info { display: flex; flex-direction: column; }
        .service-top { display: flex; align-items: center; margin-bottom: 6px; }
        .service-id-badge { background: var(--bs-primary-light); color: var(--bs-primary); font-weight: 800; font-size: 11px; padding: 4px 10px; border-radius: 6px; margin-right: 12px; }
        .service-name { font-weight: 700; color: var(--bs-gray-900); font-size: 14px; }
        .service-meta { display: flex; flex-wrap: wrap; gap: 6px; font-size: 10px; color: var(--bs-gray-500); }
        .price-col { font-weight: 700; font-size: 14px; color: var(--bs-gray-800); }
        .price-active { color: var(--bs-primary); }
        .action-btns { display: flex; gap: 8px; }
        .btn-action { padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s; border: none; }
        .btn-desc { background: var(--bs-danger-light); color: var(--bs-danger); }
        .btn-desc:hover { background: var(--bs-danger); color: #fff; }
        .btn-order { background: var(--bs-primary-light); color: var(--bs-primary); }
        .btn-order:hover { background: var(--bs-primary); color: #fff; }

        @media (max-width: 1200px) { .rank-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .rank-grid { grid-template-columns: 1fr; } .filter-section .row > div { margin-bottom: 15px; } }

        [data-bs-theme="dark"] .rank-card, [data-bs-theme="dark"] .filter-section, [data-bs-theme="dark"] .table-container { background: var(--bs-body-bg); border-color: var(--bs-gray-200); }
        [data-bs-theme="dark"] .search-input, [data-bs-theme="dark"] .filter-select { background: var(--bs-gray-100) !important; border-color: var(--bs-gray-200) !important; }
        [data-bs-theme="dark"] .table-premium th, [data-bs-theme="dark"] .category-header { background: var(--bs-gray-100); }
        [data-bs-theme="dark"] .rank-feature { background: #262c3d; }
        [data-bs-theme="dark"] .rank-feature b { color: #d7dced; }
        [data-bs-theme="dark"] .rank-feature span { color: #8f97ad; }
        [data-bs-theme="dark"] .table-premium td { border-bottom-color: var(--bs-gray-200); }
    </style>

    <!-- Page Header -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="fw-bolder text-gray-900 mb-0">{{ __('Bảng giá và Cấp bậc') }}</h4>
    </div>

    @php
        $rawUserLevel = strtolower((string) (Auth::user()->level ?? 'member'));
        $levelMap = [
            'member' => 'member',
            'silver' => 'silver',
            'collaborator' => 'silver',
            'gold' => 'gold',
            'agency' => 'gold',
            'platinum' => 'platinum',
            'distributor' => 'platinum',
            'diamond' => 'platinum',
        ];
        $userLevel = $levelMap[$rawUserLevel] ?? 'member';
        $userDeposit = (float) (Auth::user()->total_deposit ?? 0);
        $silverDeposit = (float) (s('silver_deposit') ?? 500000);
        $goldDeposit = (float) (s('gold_deposit') ?? 10000000);
        $platinumDeposit = (float) (s('platinum_deposit') ?? 50000000);
    @endphp

    <div class="rank-grid">
        <div class="rank-card">
            @if($userLevel === 'member')
                <span class="rank-current"><i class="fas fa-check me-1"></i> {{ __('Hiện tại') }}</span>
            @endif
            <div class="rank-head">
                <div class="rank-avatar"><i class="far fa-user"></i></div>
                <div class="rank-ring ring-blue"></div>
            </div>
            <div class="rank-name">{{ __('Khách lẻ') }}</div>
            <div class="rank-subtitle">{{ __('Cấp bậc mặc định cho tất cả khách hàng') }}</div>
            <div class="rank-feature">
                <i class="feature-cyan far ri-price-tag-3-line text-white"></i>
                <div>
                    <b>{{ __('Giá bán lẻ') }}</b>
                    <span>{{ __('Áp dụng giá tiêu chuẩn') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-amber far fa-folder"></i>
                <div>
                    <b>{{ __('Không yêu cầu nạp tối thiểu') }}</b>
                    <span>{{ __('Dễ dàng bắt đầu') }}</span>
                </div>
            </div>
        </div>

        <div class="rank-card">
            @if($userLevel === 'silver')
                <span class="rank-current"><i class="fas fa-check me-1"></i> {{ __('Hiện tại') }}</span>
            @endif
            <div class="rank-head">
                <div class="rank-avatar"><i class="ri-medal-line" style="font-size: 32px;"></i></div>
                <div class="rank-ring ring-yellow"></div>
            </div>
            <div class="rank-name">{{ __('Cộng tác viên') }}</div>
            <div class="rank-subtitle">{{ __('Cấp bậc đặc biệt với ưu đãi hấp dẫn') }}</div>
            <div class="rank-feature">
                <i class="feature-amber far ri-price-tag-3-line text-white"></i>
                <div>
                    <b>{{ __('Giá Cộng tác viên') }}</b>
                    <span>{{ __('Ưu đãi đặc biệt') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-orange fas fa-check"></i>
                <div>
                    <b>{{ __('Hỗ trợ tạo website con') }}</b>
                    <span>{{ __('Chi phí gia hạn 100.000đ 1 tháng') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-amber far fa-folder"></i>
                <div>
                    <b>{{ __('Nạp tối thiểu') }}</b>
                    <span>{{ convert_currency($silverDeposit, Auth::user()->currency ?? 'VND', false) }}</span>
                </div>
            </div>
            <div class="rank-missing">
                <span><i class="far fa-clock me-1"></i> {{ __('Còn thiếu') }}</span>
                <span>{{ convert_currency(max(0, $silverDeposit - $userDeposit), Auth::user()->currency ?? 'VND', false) }}</span>
            </div>
        </div>

        <div class="rank-card">
            @if($userLevel === 'gold')
                <span class="rank-current"><i class="fas fa-check me-1"></i> {{ __('Hiện tại') }}</span>
            @endif
            <div class="rank-head">
                <div class="rank-avatar"><i class="ri-trophy-line" style="font-size: 32px;"></i></div>
                <div class="rank-ring ring-green"></div>
            </div>
            <div class="rank-name">{{ __('Đại lý') }}</div>
            <div class="rank-subtitle">{{ __('Cấp bậc đặc biệt với ưu đãi hấp dẫn') }}</div>
            <div class="rank-feature">
                <i class="feature-cyan ri-price-tag-3-line text-white"></i>
                <div>
                    <b>{{ __('Giá Đại lý') }}</b>
                    <span>{{ __('Ưu đãi đặc biệt') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-orange fas fa-check"></i>
                <div>
                    <b>{{ __('Hỗ trợ tạo website con') }}</b>
                    <span>{{ __('Chi phí gia hạn 100.000đ 1 tháng') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-amber far fa-folder"></i>
                <div>
                    <b>{{ __('Nạp tối thiểu') }}</b>
                    <span>{{ convert_currency($goldDeposit, Auth::user()->currency ?? 'VND', false) }}</span>
                </div>
            </div>
            <div class="rank-missing">
                <span><i class="far fa-clock me-1"></i> {{ __('Còn thiếu') }}</span>
                <span>{{ convert_currency(max(0, $goldDeposit - $userDeposit), Auth::user()->currency ?? 'VND', false) }}</span>
            </div>
        </div>

        <div class="rank-card">
            @if($userLevel === 'platinum')
                <span class="rank-current"><i class="fas fa-check me-1"></i> {{ __('Hiện tại') }}</span>
            @endif
            <div class="rank-head">
                <div class="rank-avatar"><i class="far fa-star"></i></div>
                <div class="rank-ring ring-red"></div>
            </div>
            <div class="rank-name">{{ __('Nhà phân phối') }}</div>
            <div class="rank-subtitle">{{ __('Cấp bậc đặc biệt với ưu đãi hấp dẫn') }}</div>
            <div class="rank-feature">
                <i class="feature-orange ri-price-tag-3-line text-white"></i>
                <div>
                    <b>{{ __('Giá Nhà phân phối') }}</b>
                    <span>{{ __('Ưu đãi đặc biệt') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-orange fas fa-check"></i>
                <div>
                    <b>{{ __('Hỗ trợ tạo website con') }}</b>
                    <span>{{ __('Chi phí gia hạn 100.000đ 1 tháng') }}</span>
                </div>
            </div>
            <div class="rank-feature">
                <i class="feature-amber far fa-folder"></i>
                <div>
                    <b>{{ __('Nạp tối thiểu') }}</b>
                    <span>{{ convert_currency($platinumDeposit, Auth::user()->currency ?? 'VND', false) }}</span>
                </div>
            </div>
            <div class="rank-missing">
                <span><i class="far fa-clock me-1"></i> {{ __('Còn thiếu') }}</span>
                <span>{{ convert_currency(max(0, $platinumDeposit - $userDeposit), Auth::user()->currency ?? 'VND', false) }}</span>
            </div>
        </div>
    </div>

    <h4 class="fw-bolder text-gray-900 mb-4">{{ __('Bảng giá dịch vụ') }}</h4>

    <!-- Filter Bar -->
    <div class="filter-section">
        <form method="GET" action="{{ route('services') }}">
            <div class="row g-3">
                <div class="col-lg-5 col-md-12">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control search-input" placeholder="{{ __('Tìm kiếm tên dịch vụ...') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="platforms" id="platformSelect" class="form-select filter-select">
                        <option value="">{{ __('Tất cả các nền tảng') }}</option>
                        @foreach($allPlatforms as $platform)
                            @if(is_object($platform))
                                <option value="{{ $platform->id }}" data-image="{{ $platform->icon }}" {{ $platforms == $platform->id ? 'selected' : '' }}>{{ __($platform->name) }}</option>
                            @else
                                <option value="{{ $platform }}" {{ $platforms == $platform ? 'selected' : '' }}>{{ __($platform) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <select name="category" class="form-select filter-select">
                        <option value="">{{ __('Tất cả danh mục') }}</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>{{ __($cat->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-search-custom flex-grow-1"><i class="fas fa-search me-2"></i> {{ __('Tìm kiếm') }}</button>
                    <a href="{{ route('services') }}" class="btn btn-reset-custom"><i class="fas fa-trash-alt"></i></a>
                </div>
            </div>
        </form>
    </div>

    <!-- Latest Services Notification (Optional) -->
    <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-5">
        <span class="p-4 bg-primary rounded-circle me-4 d-flex align-items-center justify-content-center">
            <span class="badge badge-primary">NEW</span>
        </span>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h5 class="mb-1 text-primary fw-bolder">LATEST SERVICES</h5>
            <span>Cập nhật dịch vụ mới nhất từ hệ thống. Đảm bảo chất lượng và tốc độ tốt nhất.</span>
        </div>
    </div>

    <!-- Services Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th width="35%">{{ __('DỊCH VỤ') }}</th>
                        <th width="12%" class="text-center">{{ __('Thành viên') }}</th>
                        <th width="12%" class="text-center">{{ __('CTV') }}</th>
                        <th width="12%" class="text-center">{{ __('Đại lý') }}</th>
                        <th width="12%" class="text-center">{{ __('NPP') }}</th>
                        <th width="17%" class="text-center">{{ __('THAO TÁC') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr class="category-header">
                            <td colspan="6">
                                <div class="category-title">
                                    @if($cat->platform && $cat->platform->icon)
                                        <img src="{{ $cat->platform->icon }}" class="me-2 rounded-circle" width="20" height="20" alt="{{ $cat->platform->name }}">
                                    @else
                                        <i class="fas fa-bookmark text-primary me-2"></i>
                                    @endif
                                    {{ __($cat->name) }}
                                </div>
                            </td>
                        </tr>
                        @foreach ($cat->services as $service)
                            <tr>
                                <td>
                                    <div class="service-info">
                                        <div class="service-top">
                                            <span class="service-id-badge">{{ $service->id }}</span>
                                            <span class="service-name">{{ __($service->name) }}</span>
                                        </div>
                                        <div class="service-meta">
                                            <span>MIN: {{ number_format($service->min) }}</span>
                                            <span class="mx-1">•</span>
                                            <span>MAX: {{ number_format($service->max) }}</span>
                                            @if($service->refill)
                                                <span class="mx-1">•</span>
                                                <span class="text-success"><i class="fas fa-shield-alt me-1"></i>{{ __('Bảo hành') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="price-col {{ (Auth::user()->level ?? 'member') == 'member' ? 'price-active' : '' }}">
                                        {{ convert_currency(load_rate($service, 'member'), Auth::user()->currency ?? 'VND', false) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="price-col {{ (Auth::user()->level ?? '') == 'silver' ? 'price-active' : '' }}">
                                        {{ convert_currency(load_rate($service, 'silver'), Auth::user()->currency ?? 'VND', false) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="price-col {{ (Auth::user()->level ?? '') == 'gold' ? 'price-active' : '' }}">
                                        {{ convert_currency(load_rate($service, 'gold'), Auth::user()->currency ?? 'VND', false) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="price-col {{ (Auth::user()->level ?? '') == 'platinum' ? 'price-active' : '' }}">
                                        {{ convert_currency(load_rate($service, 'platinum'), Auth::user()->currency ?? 'VND', false) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <button class="btn-action btn-desc" onclick="openDescModal(this)" 
                                            data-id="{{ $service->id }}"
                                            data-name="{{ __($service->name) }}" 
                                            data-desc="{{ $service->description ? $service->description : __('Dịch vụ hiện chưa có mô tả chi tiết.') }}">
                                            <i class="fas fa-info-circle me-1"></i> {{ __('Mô tả') }}
                                        </button>
                                        <a href="{{ route('new', ['service' => $service->id]) }}" class="btn-action btn-order">
                                            <i class="fas fa-shopping-cart me-1"></i> {{ __('Đặt hàng') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Shared Description Modal -->
    <div class="modal fade" id="modal-desc-shared" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bolder" id="modal-desc-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start pt-4">
                    <div class="bg-light p-4 rounded-3 text-muted" id="modal-desc-content" style="white-space: pre-line; font-size: 13px; line-height: 1.6;">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const $platform = $('#platformSelect');
    const $category = $('select[name="category"]');
    const allCategories = @json($allCategories->map(function($cat) {
        return [
            'id' => $cat->id,
            'name' => __($cat->name),
            'platform_id' => $cat->platform_id
        ];
    }));

    function formatPlatform(option) {
        if (!option.id) return option.text;
        const img = $(option.element).data('image');
        if (img) {
            return $(`<span><img src="${img}" class="me-2" width="20" height="20" style="border-radius:50%"> ${option.text}</span>`);
        }
        return option.text;
    }

    $platform.select2({ width: '100%', templateResult: formatPlatform, templateSelection: formatPlatform });
    $category.select2({ width: '100%' });

    $platform.on('change', function() {
        const selected = $(this).val();
        $category.empty();
        $category.append('<option value="">{{ __('Tất cả danh mục') }}</option>');

        const filtered = !selected ? allCategories : allCategories.filter(cat => cat.platform_id == selected);
        filtered.forEach(cat => {
            $category.append(`<option value="${cat.id}">${cat.name}</option>`);
        });
        $category.val('').trigger('change');
    });
    
    // Make function global so onclick works
    window.openDescModal = function(btn) {
        const id = $(btn).data('id');
        const name = $(btn).data('name');
        const desc = $(btn).data('desc');
        
        $('#modal-desc-title').text('{{ __("Chi tiết dịch vụ") }} #' + id);
        
        // Use html() to preserve any formatting if present, relying on initial sanitization if any.
        // The container has white-space: pre-line so newlines will typically be respected by text() too, 
        // but html gives more flexibility if the description has tags.
        $('#modal-desc-content').html(desc);

        const modal = new bootstrap.Modal(document.getElementById('modal-desc-shared'));
        modal.show();
    };
});
</script>
@endsection


