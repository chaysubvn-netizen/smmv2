@extends('client.layouts.master')
@section('title', __('Đơn hàng sản phẩm'))
@section('content')

<!--begin::Header Modern-->
<div class="card mb-5 mb-xl-10 shadow-sm border-0">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3 font-inter">
            <!--begin::Icon-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-60px symbol-lg-100px symbol-fixed position-relative">
                    <div class="symbol-label fs-2hx fw-bold bg-light-primary text-primary">
                        <i class="ki-duotone ki-basket fs-3x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </div>
                </div>
            </div>
            <!--end::Icon-->

            <!--begin::Info-->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <h1 class="text-gray-900 fs-2 fw-bold me-1">{{ __('Đơn hàng của tôi') }}</h1>
                        </div>
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <span class="text-gray-500 me-5">{{ __('Tổng cộng') }} {{ $counts['all'] }} {{ __('đơn hàng') }}</span>
                        </div>
                    </div>

                    <!--begin::Search-->
                    <div class="d-flex align-items-center my-1 gap-2">
                        <div class="position-relative w-250px w-md-300px">
                             <input type="text" id="order-search" class="form-control form-control-solid bg-light border-0 text-gray-800" placeholder="{{ __('Tìm mã đơn hàng, tên sản phẩm...') }}" value="{{ request('search') }}" />
                        </div>
                        <button class="btn btn-primary" onclick="performSearch()">
                            {{ __('Tìm kiếm') }}
                        </button>
                    </div>
                    <!--end::Search-->
                </div>
            </div>
            <!--end::Info-->
        </div>

        <!--begin::Navs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold font-inter">
            @php
                $tabs = [
                    'all' => __('Tất cả'),
                    'pending' => __('Chờ xử lý'),
                    'processing' => __('Đang xử lý'),
                    'success' => __('Hoàn thành'),
                    'cancel' => __('Đã hủy'),
                ];
            @endphp
            @foreach($tabs as $key => $label)
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 {{ $current_status == $key ? 'active' : '' }}" href="{{ route('products_orders', ['status' => $key, 'search' => request('search')]) }}">
                    {{ $label }} <span class="badge badge-light-dark ms-2">{{ $counts[$key] }}</span>
                </a>
            </li>
            @endforeach
        </ul>
        <!--end::Navs-->
    </div>
</div>
<!--end::Header Modern-->

<!--begin::Orders Table-->
<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
             <!-- Selection Summary -->
             <div id="kt_orders_selection_header" class="d-none d-flex align-items-center">
                <div class="fw-bold me-5">
                    <span class="me-2" id="kt_orders_selection_count">0</span>{{ __('đã chọn') }}
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="copySelectedResults()">
                    <i class="fas fa-copy me-2"></i> {{ __('Sao chép kết quả đã chọn') }}
                </button>
             </div>
        </div>
    </div>
    <div class="card-body pt-0 font-inter">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_orders_table">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_orders_table .form-check-input" value="1" />
                            </div>
                        </th>
                        <th class="min-w-100px">{{ __('CHI TIẾT') }}</th>
                        <th class="min-w-250px">{{ __('SẢN PHẨM') }}</th>
                        <th class="min-w-100px text-end">{{ __('GIÁ') }}</th>
                        <th class="min-w-150px">{{ __('TRẠNG THÁI') }}</th>
                        <th class="min-w-150px">{{ __('MÃ ĐƠN HÀNG') }}</th>
                        <th class="min-w-150px">{{ __('THỜI GIAN') }}</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse ($order_products as $prd)
                    <tr>
                         <td>
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input order-checkbox" type="checkbox" value="{{ $prd->id }}" />
                            </div>
                         </td>
                         <td>
                            <button type="button" class="btn btn-sm btn-light-primary btn-active-primary px-4 py-2" onclick="showOrderDetail({{ $prd->id }})">
                                {{ __('Xem chi tiết') }}
                            </button>
                         </td>
                         <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold text-hover-primary fs-6">{{ $prd->product->name }}</span>
                                </div>
                                <span class="badge badge-light-dark fw-bold ms-3">x{{ $prd->quantity }}</span>
                            </div>
                         </td>
                         <td class="text-end">
                            <span class="text-gray-800 fw-bold fs-6">{{ convert_currency($prd->total, Auth::user()->currency, false) }}</span>
                         </td>
                         <td>
                            @if ($prd->status == 'success')
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-vertical h-20px bg-success me-3"></span>
                                    <span class="text-success fw-bold">{{ __('Hoàn thành') }}</span>
                                </div>
                            @elseif ($prd->status == 'pending')
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-vertical h-20px bg-warning me-3"></span>
                                    <span class="text-warning fw-bold">{{ __('Chờ xử lý') }}</span>
                                </div>
                            @elseif ($prd->status == 'processing')
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-vertical h-20px bg-primary me-3"></span>
                                    <span class="text-primary fw-bold">{{ __('Đang xử lý') }}</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-vertical h-20px bg-danger me-3"></span>
                                    <span class="text-danger fw-bold">{{ __('Đã hủy') }}</span>
                                </div>
                            @endif
                         </td>
                         <td>
                            <span class="badge badge-secondary fw-bold">PRD-{{ str_pad($prd->id, 7, '0', STR_PAD_LEFT) }}</span>
                         </td>
                         <td>
                             <div class="d-flex flex-column">
                                 <span class="text-gray-800 fw-bold fs-7">{{ $prd->created_at->format('H:i:s d/m/Y') }}</span>
                                 <span class="text-muted fs-8">{{ $prd->created_at->diffForHumans() }}</span>
                             </div>
                         </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-20">
                            <div class="d-flex flex-column flex-center">
                                <div class="symbol symbol-100px mb-5">
                                    <div class="symbol-label bg-light-dark border border-secondary border-dashed">
                                        <i class="ki-duotone ki-briefcase fs-3x text-gray-400"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <h3 class="fs-2x fw-bold text-gray-800 mb-3">{{ __('Chưa có đơn hàng nào') }}</h3>
                                <div class="fs-6 text-gray-500 mb-9">
                                    {{ __('Bạn chưa có đơn hàng nào. Hãy khám phá sản phẩm của chúng tôi!') }}
                                </div>
                                <a href="{{ route('products') }}" class="btn btn-primary px-6 py-3">
                                    <i class="ki-duotone ki-basket fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    {{ __('Mua sắm ngay') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-stack flex-wrap pt-10">
             <div class="fs-6 fw-bold text-gray-700">
                {{ __('Hiển thị') }} {{ $order_products->firstItem() }} {{ __('đến') }} {{ $order_products->lastItem() }} {{ __('của') }} {{ $order_products->total() }} {{ __('kết quả') }}
             </div>
             {{ $order_products->appends(request()->all())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
<!--end::Orders Table-->

<!-- Detail Modal -->
<div class="modal fade" id="modal_order_detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="fw-bold text-gray-900">{{ __('Thông tin chi tiết tài khoản') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                    <div id="order-detail-content">
                         <!-- Content injected by JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-center border-0 pt-0">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                <button type="button" class="btn btn-primary" onclick="copyResult()">
                    <i class="fas fa-copy me-2"></i> {{ __('Sao chép tất cả') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .nav-line-tabs .nav-item .nav-link.active, .nav-line-tabs .nav-item .nav-link:hover { border-bottom: 3px solid #009ef7 !important; color: #009ef7 !important; }
    .table-row-dashed tr { border-bottom-style: dashed !important; }
    .bullet-vertical { width: 4px; border-radius: 2px; }
    .form-control-solid.bg-light { background-color: auto !important; }
    .form-control-solid.bg-light:focus { background-color: auto !important; border: 1px solid #009ef7 !important; }
</style>

@endsection

@section('scripts')
<script>
    function performSearch() {
        let keyword = $('#order-search').val();
        let url = new URL(window.location.href);
        if (keyword) url.searchParams.set('search', keyword);
        else url.searchParams.delete('search');
        window.location.href = url.toString();
    }

    $('#order-search').on('keyup', function(e) {
        if (e.key === 'Enter') performSearch();
    });

    const orderData = {
        @foreach($order_products as $prd)
            {{ $prd->id }}: {!! $prd->data !!},
        @endforeach
    };

    // Selection handling
    $(document).on('change', '#kt_orders_table .form-check-input', function() {
        let count = $('#kt_orders_table .order-checkbox:checked').length;
        $('#kt_orders_selection_count').text(count);
        if (count > 0) {
            $('#kt_orders_selection_header').removeClass('d-none');
        } else {
            $('#kt_orders_selection_header').addClass('d-none');
        }
    });

    function copySelectedResults() {
        let results = [];
        $('#kt_orders_table .order-checkbox:checked').each(function() {
            let id = $(this).val();
            let data = orderData[id];
            if (Array.isArray(data)) {
                data.forEach(item => {
                    if (item.data) results.push(item.data.trim());
                });
            }
        });

        if (results.length > 0) {
            let text = results.join('\n');
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    text: '{{ __("Đã sao chép :count tài khoản vào bộ nhớ tạm", ["count" => "'+results.length+'"]) }}'.replace(':count', results.length),
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            });
        }
    }

    function showOrderDetail(id) {
        let data = orderData[id];
        let html = '';
        
        if (Array.isArray(data)) {
            data.forEach((item, index) => {
                html += `
                    <div class="p-4 bg-light rounded mb-3 position-relative border">
                        <div class="fw-bold mb-1 text-primary"></div>
                        <div class="text-gray-800 break-all" id="item-${id}-${index}">${item.data}</div>
                        <button class="btn btn-icon btn-sm btn-clean position-absolute top-0 end-0 mt-2 me-2" onclick="copyText('item-${id}-${index}')">
                            <i class="fas fa-copy fs-7 text-gray-400"></i>
                        </button>
                    </div>
                `;
            });
        } else {
            html = `<div class="alert alert-warning">${__('Không có dữ liệu')}</div>`;
        }

        $('#order-detail-content').html(html);
        $('#modal_order_detail').modal('show');
    }

    function copyText(elementId) {
        let text = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            toastr.success('{{ __("Đã sao chép vào bộ nhớ tạm") }}');
        });
    }

    function copyResult() {
        let results = [];
        $('#order-detail-content .text-gray-800').each(function() {
            let content = $(this).text().trim();
            if (content) results.push(content);
        });
        
        if (results.length > 0) {
            let text = results.join('\n');
            navigator.clipboard.writeText(text).then(() => {
                toastr.success('{{ __("Đã sao chép :count tài khoản") }}'.replace(':count', results.length));
            });
        }
    }
</script>
@endsection


