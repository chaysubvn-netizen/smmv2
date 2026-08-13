@extends('client.layouts.master')
@section('title', $product->name)
@section('content')
<style>
    .order-stock-info {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.25rem;
        background-color: #059669 !important;
        border-radius: 50rem !important;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .order-stock-info:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .order-stock-info i {
        font-size: 1.1rem;
        color: white !important;
        margin-right: 0.75rem;
    }

    .order-stock-info span {
        font-size: 0.9rem;
        font-weight: 500;
        color: white !important;
    }

    .order-stock-info strong {
        font-size: 1rem;
        color: #fff !important;
        margin: 0 0.2rem;
    }
</style>
<div id="kt_content_container" class="container-xxl">
    <!--begin::Hero Card-->
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin: Pic-->
                <div class="me-7 mb-4">
                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" />
                        <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                    </div>
                </div>
                <!--end::Pic-->

                <!--begin::Info-->
                <div class="flex-grow-1">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::User-->
                        <div class="d-flex flex-column">
                            <!--begin::Name-->
                            <div class="d-flex align-items-center mb-2">
                                <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $product->name }}</a>
                                <a href="#" class="btn btn-sm btn-light-success fw-bold ms-2 fs-8 py-1 px-3">{{ $product->category->name }}</a>
                            </div>
                            <!--end::Name-->

                            <!--begin::Info-->
                            <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <i class="ki-duotone ki-geolocation fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                    {{ __('Kho:') }} {{ $product->stocks->where('status', 'available')->count() }}
                                </a>
                                <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <i class="ki-duotone ki-chart-simple fs-4 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    {{ __('Đã bán:') }} {{ number_format($product->order_products_sum_quantity ?? 0) }}
                                </a>
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::User-->

                        <!--begin::Actions-->
                      
                        <!--end::Actions-->
                    </div>
                    <!--end::Title-->

                    <!--begin::Stats-->
                    <div class="d-flex flex-wrap flex-stack">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-column flex-grow-1 pe-8">
                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap">
                                <!--begin::Stat-->
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <div class="fs-2 fw-bold counted" data-kt-countup="true" data-kt-countup-value="{{ convert_currency($product->price, Auth::user()->currency, false) }}" data-kt-countup-prefix="$">{{ convert_currency($product->price, Auth::user()->currency, false) }}</div>
                                    </div>
                                    <!--end::Number-->
                                    <!--begin::Label-->
                                    <div class="fw-semibold fs-6 text-gray-400">{{ __('Giá cơ bản') }}</div>
                                    <!--end::Label-->
                                </div>
                                <!--end::Stat-->
                            </div>
                            <!--end::Stats-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Details-->

            <!--begin::Navs-->
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#tab_overview">{{ __('Tổng quan') }}</a>
                </li>
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#tab_reviews">{{ __('Đánh giá') }} ({{ $rating_summary['count'] }})</a>
                </li>
            </ul>
            <!--begin::Navs-->
        </div>
    </div>
    <!--end::Hero Card-->

    <div class="row g-5 g-xl-8">
        <!--begin::Left Content-->
        <div class="col-xl-8">
            <!--begin::Plans Widget-->
            <div class="card mb-5 mb-xl-8" id="tab_overview">
                <div class="card-body py-6">
                    <!-- Package Details Widget -->
                    <div class="card shadow-sm border border-primary border-dashed mb-8 bg-dark bg-opacity-10">
                        <div class="card-body p-6">
                            <div class="d-flex align-items-center mb-5 cursor-pointer" onclick="$('#packageDesc').slideToggle()">
                                <div class="symbol symbol-50px me-5">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-abstract-41 fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <div class="text-primary fw-bold fs-8 text-uppercase mb-1 ls-2">{{ __('CHI TIẾT GÓI') }}</div>
                                    <div class="text-white fw-boldest fs-3">{{ $product->name }}</div>
                                </div>
                                <div class="ms-2">
                                    <div class="btn btn-sm btn-icon btn-active-light-primary">
                                        <i class="ki-duotone ki-down fs-2 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div id="packageDesc" class="mt-5 fs-6 fw-normal rounded-3 p-5 border border-gray-800 border-dashed" style="display: block; color: #ffffff !important; background: rgba(0, 158, 247, 0.05); line-height: 1.6;">
                                {!! $product->short_description ?? __('Gói này chưa có mô tả') !!}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-stack mb-5">
                         <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-white ls-1">{{ __('Gói dịch vụ khác') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7" style="color: #6c757d !important;">{{ __('Chọn gói phù hợp với nhu cầu của bạn') }}</span>
                        </h3>
                    </div>

                    <div class="row g-4">
                        <!-- Current Product -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border border-dashed hover-elevate-up">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-40px me-3">
                                            <img src="{{ $product->image }}" alt="" class="">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6">{{ $product->name }}</span>
                                            <span class="text-muted fs-8">{{ __('Hiện tại') }}</span>
                                        </div>
                                        <span class="badge badge-light-success ms-auto"><i class="fas fa-check text-success"></i></span>
                                    </div>
                                    <div class="text-primary fw-bold fs-3 mb-3">{{ convert_currency($product->price, Auth::user()->currency, false) }}</div>
                                    <button class="btn btn-sm btn-light-primary w-100 disabled">{{ __('Đang chọn') }}</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Relates -->
                         @php 
                            $relates = \App\Models\Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->where('status', 'active')
                                ->limit(5)->get();
                        @endphp
                        @foreach($relates as $item)
                        <div class="col-md-6 col-lg-4">
                             <div class="card h-100 border border-transparent bg-light hover-elevate-up cursor-pointer" onclick="window.location.href='{{ route('product.detail', $item->slug) }}'">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-40px me-3">
                                            <img src="{{ $item->image }}" alt="" class="">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold fs-6 line-clamp-1">{{ $item->name }}</span>
                                            <span class="text-muted fs-8">{{ __('Gói khác') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-gray-800 fw-bold fs-3 mb-3">{{ convert_currency($item->price, Auth::user()->currency, false) }}</div>
                                    <button class="btn btn-sm btn-light w-100">{{ __('Xem ngay') }}</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!--end::Plans Widget-->

            <!--begin::Description Widget-->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ __('Chi tiết sản phẩm') }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="fs-6 fw-semibold text-gray-600">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
            <!--end::Description Widget-->
            
            <!--begin::Reviews Widget-->
            <div class="card mb-5 mb-xl-8" id="tab_reviews">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ __('Đánh giá khách hàng') }}</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Tổng cộng') }} {{ $rating_summary['count'] }} {{ __('đánh giá') }}</span>
                    </h3>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#modal_review">
                            <i class="fas fa-pencil me-2"></i> {{ __('Viết đánh giá') }}
                        </button>
                    </div>
                </div>
                <div class="card-body py-3">
                    <!-- Rating Summary -->
                    <div class="row mb-8">
                        <div class="col-sm-4 d-flex flex-center flex-column mb-5 mb-sm-0">
                            <div class="fs-2hx fw-bold text-white mb-1">{{ number_format($rating_summary['average'], 1) }}</div>
                            <div class="rating justify-content-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="rating-label {{ $i <= round($rating_summary['average']) ? 'checked' : '' }}">
                                        <i class="fas fa-star fs-1"></i>
                                    </div>
                                @endfor
                            </div>
                            <div class="text-gray-400 fs-6">{{ __('Dựa trên') }} {{ $rating_summary['count'] }} {{ __('đánh giá') }}</div>
                        </div>
                        <div class="col-sm-8">
                            <div class="d-flex flex-column gap-3">
                                @for($i = 5; $i >= 1; $i--)
                                    @php
                                        $count = $rating_summary['stars'][$i] ?? 0;
                                        $percent = $rating_summary['count'] > 0 ? ($count / $rating_summary['count']) * 100 : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="text-gray-400 fw-bold fs-6 me-3 min-w-40px">{{ $i }} <i class="fas fa-star text-warning fs-8"></i></div>
                                        <div class="progress h-6px w-100 bg-light-darken">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-gray-400 fw-bold fs-6 ms-3 min-w-40px text-end">{{ $count }}</div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                     <!-- Reviews List -->
                    @foreach($reviews as $review)
                    <div class="mb-9">
                        <div class="d-flex flex-stack position-relative mb-2">
                            <div class="d-flex align-items-center me-6">
                                <div class="symbol symbol-45px symbol-circle me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold">{{ substr($review->user->username ?? 'U', 0, 1) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-1">{{ $review->user->username ?? 'User' }}</a>
                                    <div class="rating mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="rating-label {{ $i <= $review->rating ? 'checked' : '' }}">
                                                <i class="fas fa-star fs-8"></i>
                                            </div>
                                        @endfor
                                    </div>
                                    @if($review->order_product_id)
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge badge-light-success fs-9 px-2 py-1 me-2">
                                                <i class="fas fa-check-circle text-success me-1"></i> {{ __('Đã mua hàng') }}
                                            </span>
                                            <span class="text-gray-500 fs-9">#{{ $review->order_product_id }}</span>
                                        </div>
                                    @endif
                                    <div class="fs-7 fw-semibold text-gray-600">{{ $review->comment }}</div>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <div class="text-gray-400 fs-7 mb-2">{{ $review->created_at->diffForHumans() }}</div>
                                @if(Auth::check() && Auth::user()->role === 'admin')
                                <button type="button" class="btn btn-sm btn-link text-primary py-0" onclick="openReplyModal({{ $review->id }}, '{{ $review->user->username }}')">
                                    <i class="fas fa-reply me-1"></i> {{ __('Phản hồi') }}
                                </button>
                                @endif
                            </div>
                        </div>

                        <!-- Replies -->
                        @if($review->replies->count() > 0)
                        <div class="ms-10 ps-5 border-start border-gray-300">
                            @foreach($review->replies as $reply)
                            <div class="d-flex flex-stack position-relative mt-5">
                                <div class="d-flex align-items-center me-6">
                                    <div class="symbol symbol-35px symbol-circle me-3">
                                        <span class="symbol-label @if($reply->user->role === 'admin') bg-light-success text-success @else bg-light-info text-info @endif fw-bold">{{ substr($reply->user->username ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="fs-6 fw-bold text-gray-900 text-hover-primary me-2">{{ $reply->user->username ?? 'User' }}</a>
                                            @if($reply->user->role === 'admin')
                                                <span class="badge badge-light-success fs-9 px-2 py-1">{{ __('Phản hồi của shop') }}</span>
                                            @else
                                                <span class="badge badge-light-primary fs-9 px-2 py-1">{{ __('Phản hồi') }}</span>
                                            @endif
                                        </div>
                                        <div class="fs-7 fw-semibold text-gray-600 mt-1">{{ $reply->comment }}</div>
                                    </div>
                                </div>
                                <div class="text-gray-400 fs-8">{{ $reply->created_at->diffForHumans() }}</div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="separator separator-dashed my-6"></div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!--end::Reviews Widget-->
        </div>
        <!--end::Left Content-->

        <!--begin::Sidebar-->
        <div class="col-xl-4">
            <div class="card card-flush sticky-top" style="top: 100px; z-index: 90;">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">{{ __('Thông tin thanh toán') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <!-- Qty -->
                    <div class="mb-5">
                        <label class="form-label fw-semibold">{{ __('Số lượng') }}</label>
                        <div class="position-relative">
                             <div class="input-group input-group-solid">
                                <button class="btn btn-icon btn-outline btn-active-color-primary" type="button" onclick="updateQty(-1)">
                                    <i class="fas fa-minus fs-7"></i>
                                </button>
                                <input type="number" class="form-control form-control-solid text-center" id="quantity" value="1" min="1" onchange="calculateTotal()"/>
                                <button class="btn btn-icon btn-outline btn-active-color-primary" type="button" onclick="updateQty(1)">
                                    <i class="fas fa-plus fs-7"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Input Data -->
                    <div class="mb-5">
                        <label class="form-label fw-semibold">{{ __('Dữ liệu đơn hàng') }}</label>
                         @if ($product->data_input)
                            <textarea class="form-control form-control-solid" id="data_input" rows="3" placeholder="{{ $product->data_input }}"></textarea>
                        @else
                            <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed p-4">
                                <span class="text-gray-700 fs-7">{{ __('Sản phẩm này không yêu cầu nhập dữ liệu thêm.') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Coupon -->
                    <div class="mb-5">
                         <div class="d-flex align-items-center mb-3 cursor-pointer text-primary" onclick="$('#couponArea').slideToggle()">
                            <i class="fas fa-ticket-alt me-2"></i> {{ __('Mã giảm giá') }}
                         </div>
                         <div id="couponArea" style="display: none;">
                            <div class="input-group input-group-solid">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Nhập mã...">
                                <button class="btn btn-primary" type="button" onclick="checkCoupon()">{{ __('Áp dụng') }}</button>
                            </div>
                            <div id="coupon-message" class="mt-2"></div>
                         </div>
                    </div>

                    <div class="separator separator-dashed my-5"></div>

                    <!-- Totals -->
                    <div class="d-flex flex-stack mb-2">
                         <span class="text-muted fw-semibold fs-6">{{ __('Đơn giá') }}</span>
                         <span class="text-gray-800 fw-bold fs-6">{{ convert_currency($product->price, Auth::user()->currency, false) }}</span>
                    </div>
                    <div class="d-flex flex-stack mb-5">
                         <span class="text-gray-900 fw-bold fs-4">{{ __('Tổng thanh toán') }}</span>
                         <span class="text-danger fw-bold fs-3" id="total-display"></span>
                   
                    </div>
                    <div class="order-stock-info" id="stockInfo">
                        <i class="ki-duotone ki-delivery-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <span>
                            {{ __('Kho hàng:') }} <strong id="stockCount">{{ $product->stocks->where('status', 'available')->count() }}</strong> {{ __('sản phẩm') }}
                        </span>
                    </div>
                    <!-- Actions -->
                    <div class="mb-0">
                         <button class="btn btn-primary w-100" onclick="handleBuy()">
                            <i class="fas fa-shopping-cart me-2"></i> {{ __('Thanh toán ngay') }}
                         </button>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Sidebar-->
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="modal_review" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Viết đánh giá') }}</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="modal-body">
                <form id="form-review">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-7">
                        <label class="fw-semibold mb-2 {{ Auth::user()->role !== 'admin' ? 'required' : '' }}">{{ __('Chọn đơn hàng đã mua') }}</label>
                        <select class="form-select form-select-solid" name="order_product_id" {{ Auth::user()->role !== 'admin' ? 'required' : '' }}>
                            <option value="">-- {{ __('Chọn đơn hàng đã mua') }} --</option>
                            @foreach($purchasedOrders as $order)
                                <option value="{{ $order->id }}">#{{ $order->id }} - {{ $product->name }} (x{{ $order->quantity }}) - {{ $order->created_at->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                        @if(Auth::user()->role === 'admin')
                            <div class="text-muted fs-9 mt-1">{{ __('Admin có thể để trống nếu muốn đánh giá chung.') }}</div>
                        @elseif(count($purchasedOrders) == 0)
                            <div class="text-danger fs-9 mt-1">{{ __('Bạn cần mua sản phẩm này để có thể đánh giá.') }}</div>
                        @endif
                    </div>
                    <div class="d-flex flex-column align-items-center mb-5">
                        <label class="fw-semibold fs-5 mb-3">{{ __('Bạn đánh giá sản phẩm này thế nào?') }}</label>
                        <div class="rating-stars d-flex gap-2">
                            <input type="hidden" name="rating" id="review-rating" value="5">
                             @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star fs-2x text-warning cursor-pointer" data-value="{{ $i }}"></i>
                            @endfor
                        </div>
                        <span class="text-muted mt-2" id="rating-text">{{ __('Tuyệt vời') }}</span>
                    </div>
                    <div class="d-flex flex-column">
                        <label class="fw-semibold mb-2">{{ __('Nhận xét') }}</label>
                        <textarea class="form-control form-control-solid" name="comment" rows="4" placeholder="{{ __('Sản phẩm rất tốt...') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                <button type="button" class="btn btn-primary" onclick="submitReview()">{{ __('Gửi đánh giá') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="modal_reply" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">{{ __('Phản hồi đánh giá') }}</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="modal-body">
                <form id="form-reply">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="parent_id" id="reply-parent-id">
                    <div class="mb-5">
                        <label class="fw-semibold mb-2">{{ __('Phản hồi cho:') }} <span id="reply-target-user" class="text-primary"></span></label>
                        <textarea class="form-control form-control-solid" name="comment" rows="4" placeholder="{{ __('Nhập nội dung phản hồi...') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                <button type="button" class="btn btn-primary" onclick="submitReply()">{{ __('Gửi phản hồi') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Currency Configuration from Server
    const currencyConfig = @json(json_currency());
    const vndRate = {{ \App\Models\Currency::where('code', 'VND')->first()->exchange_rate ?? 1 }};
    const unitPriceVND = {{ $product->price }};
    let currentCoupon = null;

    function formatPrice(amountVND) {
        let convertedAmount = amountVND;
        
        // Convert if target is not VND
        if (currencyConfig.code !== 'VND') {
            convertedAmount = amountVND / vndRate;
        }

        // Format according to currency config
        const decimalPlaces = (currencyConfig.code === 'USD') ? 5 : currencyConfig.decimal_places;
        const thousandSeparator = currencyConfig.thousand_separator === 'dot' ? '.' : ',';
        const decimalSeparator = currencyConfig.decimal_separator === 'dot' ? '.' : ',';

        // Custom formatting logic to match PHP number_format
        let parts = convertedAmount.toFixed(decimalPlaces).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
        let formattedNumber = parts.join(decimalSeparator);

        if (currencyConfig.position === 'left') {
            return currencyConfig.symbol + formattedNumber;
        } else {
            return formattedNumber + ' ' + currencyConfig.symbol;
        }
    }

    function updateQty(change) {
        let qtyInput = document.getElementById('quantity');
        let newQty = parseInt(qtyInput.value) + change;
        if (newQty < 1) newQty = 1;
        qtyInput.value = newQty;
        calculateTotal();
    }

    function calculateTotal() {
        let qty = parseInt(document.getElementById('quantity').value) || 1;
        if(qty < 1) qty = 1;
        
        let totalVND = qty * unitPriceVND;
        let formatted = formatPrice(totalVND);
        
        // Re-check coupon if exists
        if (currentCoupon) {
            checkCoupon(true); 
        } else {
             const display = document.getElementById('total-display');
             if (display) display.innerText = formatted;
        }
    }

    function checkCoupon(silent = false) {
        let code = $('#coupon_code').val();
        let qty = parseInt(document.getElementById('quantity').value) || 1;
        let amountVND = qty * unitPriceVND;

        if (!code) {
            if(!silent) Swal.fire({
                text: '{{ __("Vui lòng nhập mã giảm giá") }}',
                icon: "info",
                buttonsStyling: false,
                confirmButtonText: "{{ __('OK') }}",
                customClass: { confirmButton: "btn btn-primary" }
            });
            return;
        }

        $.post('{{ route('check-discount') }}', {
            code: code,
            amount: amountVND,
            _token: '{{ csrf_token() }}'
        }, function(res) {
            if (res.success) {
                currentCoupon = code;
                 let discountVND = res.discount;
                 let finalTotalVND = amountVND - discountVND;
                 if (finalTotalVND < 0) finalTotalVND = 0;

                 let formattedFinal = formatPrice(finalTotalVND);
                 let formattedDiscount = formatPrice(discountVND);

                 $('#total-display').html(`${formattedFinal} <br><small class='text-success fs-7'>(-${formattedDiscount})</small>`);
                 $('#coupon-message').html(`<span class="text-success">${res.message}</span>`);
            } else {
                if(!silent) {
                    currentCoupon = null;
                    let formatted = formatPrice(amountVND);
                    $('#total-display').html(formatted);
                    $('#coupon-message').html(`<span class="text-danger">${res.message}</span>`);
                }
            }
        });
    }

    calculateTotal();

    const ratingTexts = {
        1: '{{ __("Tệ") }}',
        2: '{{ __("Không hài lòng") }}',
        3: '{{ __("Bình thường") }}',
        4: '{{ __("Hài lòng") }}',
        5: '{{ __("Tuyệt vời") }}'
    };

    $('.rating-stars i').hover(function() {
        let val = $(this).data('value');
        updateStarUI(val);
    }, function() {
        let val = $('#review-rating').val();
         updateStarUI(val);
    });

    $('.rating-stars i').click(function() {
        let val = $(this).data('value');
        $('#review-rating').val(val);
        updateStarUI(val);
    });

    function updateStarUI(val) {
        $('.rating-stars i').each(function() {
            let starVal = $(this).data('value');
            if (starVal <= val) {
                $(this).removeClass('far text-gray-300').addClass('fas text-warning');
            } else {
                $(this).removeClass('fas text-warning').addClass('far text-gray-300');
            }
        });
        $('#rating-text').text(ratingTexts[val]);
    }

    function submitReview() {
        let data = $('#form-review').serialize();
        let submitBtn = $('#modal_review button.btn-primary');
        submitBtn.attr('disabled', true).text('{{ __("Đang xử lý...") }}');

        $.post("{{ route('review.store') }}", data).done(function(res) {
            submitBtn.attr('disabled', false).text('{{ __("Gửi đánh giá") }}');
            if(res.success) {
                $('#modal_review').modal('hide');
                Swal.fire({
                    text: res.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "{{ __('OK') }}",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    text: res.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "{{ __('OK') }}",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            }
        }).fail(function(xhr) {
             submitBtn.attr('disabled', false).text('{{ __("Gửi đánh giá") }}');
             let msg = xhr.responseJSON?.message || "{{ __('Có lỗi xảy ra') }}";
             Swal.fire({
                text: msg,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "{{ __('OK') }}",
                customClass: { confirmButton: "btn btn-primary" }
            });
        });
    }

    function openReplyModal(id, username) {
        $('#reply-parent-id').val(id);
        $('#reply-target-user').text('@' + username);
        $('#modal_reply').modal('show');
    }

    function submitReply() {
        let data = $('#form-reply').serialize();
        let submitBtn = $('#modal_reply button.btn-primary');
        submitBtn.attr('disabled', true).text('{{ __("Đang xử lý...") }}');

        $.post("{{ route('review.store') }}", data).done(function(res) {
            submitBtn.attr('disabled', false).text('{{ __("Gửi phản hồi") }}');
            if(res.success) {
                $('#modal_reply').modal('hide');
                Swal.fire({
                    text: res.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "{{ __('OK') }}",
                    customClass: { confirmButton: "btn btn-primary" }
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    text: res.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "{{ __('OK') }}",
                    customClass: { confirmButton: "btn btn-primary" }
                });
            }
        }).fail(function(xhr) {
             submitBtn.attr('disabled', false).text('{{ __("Gửi phản hồi") }}');
             let msg = xhr.responseJSON?.message || "{{ __('Có lỗi xảy ra') }}";
             Swal.fire({
                text: msg,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "{{ __('OK') }}",
                customClass: { confirmButton: "btn btn-primary" }
            });
        });
    }

    function handleBuy() {
        let quantity = parseInt($('#quantity').val()) || 1;
        let dataInput = $('#data_input').val();
        let btn = $('button[onclick="handleBuy()"]');
        btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> {{ __("Đang xử lý...") }}');

        $.ajax({
            url: '{{ route('request-product') }}',
            type: 'POST',
            data: {
                id: {{ $product->id }},
                quantity: quantity,
                data_input: dataInput,
                coupon: currentCoupon,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    notify(response.message, "success");
                    setTimeout(() => {
                        window.location.href = '{{ route("products_orders") }}';
                    }, 2000);
                } else {
                    notify(response.message, "error");
                    btn.attr('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i> {{ __("Thanh toán ngay") }}');
                }
            },
            error: function(xhr) {
                 let msg = xhr.responseJSON?.message || '{{ __("Có lỗi xảy ra") }}';
                 notify(msg, "error");
                 btn.attr('disabled', false).html('<i class="fas fa-shopping-cart me-2"></i> {{ __("Thanh toán ngay") }}');
            }
        });
    }
</script>
@endsection