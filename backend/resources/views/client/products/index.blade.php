@extends('client.layouts.master')
@section('title', __('Sản phẩm'))
@section('content')


<div class="page-header-modern page-header-compact">
    <div class="container">
        <h1 class="page-title-modern">
                            <i class="fa-solid fa-shopping-bag"></i>
                        Tất cả sản phẩm        </h1>
        <p class="page-subtitle-modern">
            Khám phá bộ sưu tập sản phẩm chất lượng cao được chọn lọc dành riêng cho bạn        </p>
    </div>
</div>
<!-- Search & Filter Section -->
<div class="card mb-10">
    <div class="card-body">
        <!-- Main Search -->
        <div class="d-flex align-items-center mb-5">
            <div class="position-relative w-100">
                <i class="ki-duotone ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-4"><span class="path1"></span><span class="path2"></span></i>
                <input type="text" id="product-search" class="form-control form-control-solid ps-12" placeholder="{{ __('Tìm kiếm sản phẩm...') }}" />
            </div>
            <a href="{{ route('products_orders') }}" class="btn btn-icon btn-light-primary ms-3 w-40px h-40px" data-bs-toggle="tooltip" title="{{ __('Đơn hàng đã mua') }}">
                <i class="ki-duotone ki-handcart fs-2"><span class="path1"></span><span class="path2"></span></i>
            </a>
        </div>

        <!-- Filters Row -->
        <div class="d-flex flex-wrap gap-3">
             <!-- Category -->
            <div class="w-100 w-md-200px">
                <select class="form-select form-select-solid" id="category-filter" data-control="select2" data-placeholder="{{ __('Tất cả danh mục') }}">
                    <option value="all">{{ __('Tất cả danh mục') }}</option>
                     @foreach ($allCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Price Range -->
            <div class="d-flex align-items-center gap-2">
                <input type="number" id="price-min" class="form-control form-control-solid w-150px" placeholder="{{ __('Mức giá từ') }}" value="{{ request('min_price') }}">
                <span class="text-gray-400">-</span>
                <input type="number" id="price-max" class="form-control form-control-solid w-150px" placeholder="{{ __('Mức giá đến') }}" value="{{ request('max_price') }}">
            </div>
            
             <!-- Sort -->
            <div class="w-100 w-md-150px">
                <select class="form-select form-select-solid" id="product-sort" data-control="select2" data-hide-search="true" data-placeholder="{{ __('Sắp xếp') }}">
                    <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>{{ __('Mặc định') }}</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>{{ __('Giá tăng dần') }}</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>{{ __('Giá giảm dần') }}</option>
                </select>
            </div>

            <button class="btn btn-primary ms-auto" onclick="filterProducts()">
                <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i> {{ __('Lọc') }}
            </button>
             <button class="btn btn-light btn-icon" onclick="window.location.reload()">
                <i class="ki-duotone ki-arrows-circle fs-2"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    </div>
</div>

<!-- Product List -->
<div id="products-container">
    @foreach ($categories as $category)
        @if($category->products->where('status', 'active')->count() > 0)
        <div class="mb-10 category-section" data-cat-id="{{ $category->id }}">
            <h3 class="fw-bold text-gray-900 fs-4 mb-5 border-start border-4 border-primary ps-3">{{ $category->name }} ({{ $category->products->where('status', 'active')->count() }})</h3>
            
            <div class="row g-5 g-xl-10">
                @foreach ($category->products->where('status', 'active') as $product)
                    <div class="col-md-6 col-lg-4 col-xl-3 product-item-wrapper" data-name="{{ strtolower($product->name) }}">
                         <a href="{{ route('product.detail', $product->slug) }}" class="d-block text-decoration-none h-100">
                            <div class="card h-100 border-0 shadow-sm border-hover-primary transition-300 card-product-hover">
                                <div class="card-body p-4">
                                    <!-- Badges -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge badge-light-primary fw-bold px-3 py-2 rounded-pill fs-8 border border-primary border-dashed">
                                            <i class="ki-duotone ki-tag me-1 fs-8"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> {{ __('SẢN PHẨM') }}
                                        </span>
                                        <i class="ki-duotone ki-heart fs-4 text-gray-400 heart-btn"><span class="path1"></span><span class="path2"></span></i>
                                    </div>

                                    <!-- Image Grid/Container -->
                                    <div class="position-relative mb-4">
                                        <div class="d-flex flex-center bg-light-primary rounded-3 overflow-hidden" style="height: 160px;">
                                             <img src="{{ $product->image ? $product->image : 'https://via.placeholder.com/300x169.png?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="mw-100 mh-100" style="object-fit: contain;">
                                        </div>
                                        @if(isset($product->old_price) && $product->old_price > $product->price)
                                            @php
                                                $discount = round((($product->old_price - $product->price) / $product->old_price) * 100);
                                            @endphp
                                            <div class="position-absolute bottom-0 start-0 mb-2 ms-2">
                                                 <span class="badge badge-danger fw-bold fs-8 shadow-sm">
                                                    {{ __('GIẢM ĐẾN') }} {{ $discount }}%
                                                 </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Title -->
                                    <div class="mb-2" style="min-height: 44px;">
                                        <div class="text-gray-900 fs-6 fw-bold text-hover-primary lh-base mn-h-40px text-line-2">
                                            {{ $product->name }}
                                        </div>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="d-flex align-items-end mb-3">
                                         <span class="text-danger fw-bolder fs-4 me-2">{{ convert_currency($product->price, Auth::user()->currency, false) }}</span>
                                         @if(isset($product->old_price) && $product->old_price > $product->price)
                                            <span class="text-gray-400 fw-semibold text-decoration-line-through fs-7">{{ convert_currency($product->old_price, Auth::user()->currency, false) }}</span>
                                         @endif
                                    </div>

                                    <div class="separator separator-dashed mb-3"></div>

                                    <!-- Footer (Status & Sold) -->
                                    <div class="d-flex align-items-center justify-content-between fs-8">
                                        <div class="d-flex align-items-center mb-2">
                                        <i class="ki-duotone ki-star fs-6 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                                        <span class="fw-bold text-gray-600 fs-7">{{ number_format($product->reviews_avg_rating ?? 5, 1) }}</span>
                                    </div>
                                         <!-- Fake sold data if not exists, or use random for visual matching reference -->
                                        <div class="text-gray-500 fw-semibold">{{ __('Đã bán') }} {{ number_format($product->order_products_sum_quantity ?? 0) }}</div>

                                        <div class="d-flex align-items-center text-success fw-bold">
                                             <i class="ki-duotone ki-flash-circle fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>
                                             {{ __('Giao ngay') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
</div>

<style>
    .card-product-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
    .text-line-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Page Header Modern */
    .page-header-modern {
        background: linear-gradient(135deg, auto 0%, auto 100%);
        padding: 40px 0;
        margin-bottom: 30px;
        color: auto;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .page-header-modern.page-header-compact {
        padding: 30px 0;
    }
    .breadcrumb-modern {
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        color: auto;
        margin-bottom: 15px;
    }
    .breadcrumb-modern a {
        color: auto;
        text-decoration: none;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .breadcrumb-modern a:hover {
        color: auto;
    }
    .breadcrumb-modern .separator {
        margin: 0 10px;
        color: auto;
    }
    .breadcrumb-modern .current {
        color: auto;
    }
    .page-title-modern {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        color: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .page-title-modern i {
        color: #009ef7; /* Primary Color */
    }
    .page-subtitle-modern {
        font-size: 1.1rem;
        color: auto;
        margin: 0;
        max-width: 600px;
    }
</style>

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 safely
        $('.form-select[data-control="select2"]').each(function() {
             if (!$(this).data('select2')) {
                 $(this).select2({ minimumResultsForSearch: -1 });
             }
        });

        // Search features
        $('#product-search').on('keyup', function(e) {
            if (e.key === 'Enter') {
                filterProducts();
            }
        });
        
        // Populate filters from URL
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('category')) {
            $('#category-filter').val(urlParams.get('category')).trigger('change');
        }
        if(urlParams.has('keyword')) {
            $('#product-search').val(urlParams.get('keyword'));
        }

        window.filterProducts = function() {
            let catId = $('#category-filter').val();
            let keyword = $('#product-search').val();
            let minPrice = $('#price-min').val();
            let maxPrice = $('#price-max').val();
            let sort = $('#product-sort').val();
            
            let url = new URL(window.location.href);
            
            if (catId && catId !== 'all') url.searchParams.set('category', catId);
            else url.searchParams.delete('category');
            
            if (keyword) url.searchParams.set('keyword', keyword);
            else url.searchParams.delete('keyword');

            if (minPrice) url.searchParams.set('min_price', minPrice);
            else url.searchParams.delete('min_price');

            if (maxPrice) url.searchParams.set('max_price', maxPrice);
            else url.searchParams.delete('max_price');

            if (sort && sort !== 'default') url.searchParams.set('sort', sort);
            else url.searchParams.delete('sort');
            
            window.location.href = url.toString();
        }
    });
</script>
@endsection

@endsection
