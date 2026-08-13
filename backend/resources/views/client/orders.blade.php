@extends('client.layouts.master')
@section('title', __('Lịch sử đơn hàng'))
@section('content')
<style>
.empty-state i {
    font-size: 64px;              /* TO icon */
    margin-bottom: 12px;
    color: #64748b;               /* xám nhạt cho dark mode */

    width: 80px;
    height: 80px;
    border-radius: 18px;
    border: 2px dashed #334155;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    background: rgba(100,116,139,0.08);
}
    .table thead th {
    background-color: auto;
    color: #3f4254;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border-bottom: 1px solid #ebedf3;
    vertical-align: middle;
}

.table thead th.text-end {
    text-align: right;
}
/* Custom Styles for Order History */
.stats-card {
    transition: all 0.3s ease;
    border: 1px dashed #e4e6ef;
}
.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
}
.filter-btn {
    white-space: nowrap;
    border-radius: 50px;
    padding: 0.5rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.filter-btn.active {
    background-color: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
}
.filter-btn:not(.active) {
    background-color: var(--bs-light);
    color: var(--bs-gray-600);
}
.filter-btn:not(.active):hover {
    background-color: var(--bs-gray-200);
    color: var(--bs-primary);
}
.order-sort-select {
    min-width: 150px;
}
.empty-state-icon {
    width: 80px;
    height: 80px;
    background: #f1f1f4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #a1a5b7;
}

/* Dark mode adjustments */
[data-bs-theme="dark"] .stats-card {
    border-color: #323248;
    background: #1e1e2d;
}
[data-bs-theme="dark"] .filter-btn:not(.active) {
    background-color: #2b2b40;
    color: #92929f;
}
[data-bs-theme="dark"] .filter-btn:not(.active):hover {
    background-color: #323248;
    color: #fff;
}
[data-bs-theme="dark"] .empty-state-icon {
    background: #2b2b40;
    color: #565674;
}
.border-dashed {
    border-style: dashed !important;
}
</style>

<div class="row g-5">
    {{-- STATS CARDS --}}
    <div class="col-12">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 bg-body shadow-sm" style="border: 2px dashed #10b981;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-success rounded-3">
                                        <i class="ki-outline ki-check-circle fs-2x text-success"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-gray-600 fw-semibold mb-1">{{ __('Completed orders') }}</p>
                                <h5 class="mb-0 fs-2 fw-bold text-gray-800">{{ number_format($orders->where('status', 'Completed')->count()) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 bg-body shadow-sm" style="border: 2px dashed #f59e0b;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-warning rounded-3">
                                        <i class="ki-outline ki-time fs-2x text-warning"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-gray-600 fw-semibold mb-1">{{ __('Pending orders') }}</p>
                                <h5 class="mb-0 fs-2 fw-bold text-gray-800">{{ number_format($orders->where('status', 'Pending')->count()) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 bg-body shadow-sm" style="border: 2px dashed #ef4444;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-danger rounded-3">
                                        <i class="ki-outline ki-cross-circle fs-2x text-danger"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-gray-600 fw-semibold mb-1">{{ __('Canceled orders') }}</p>
                                <h5 class="mb-0 fs-2 fw-bold text-gray-800">{{ number_format($orders->where('status', 'Canceled')->count()) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                 <div class="card h-100 bg-body shadow-sm" style="border: 2px dashed #4680ff;">
                     <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="symbol symbol-50px">
                                    <span class="symbol-label bg-light-primary rounded-3">
                                        <i class="ki-outline ki-arrows-loop fs-2x text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-gray-600 fw-semibold mb-1">{{ __('Orders in progress') }}</p>
                                <h5 class="mb-0 fs-2 fw-bold text-gray-800">{{ number_format($orders->where('status', 'In progress')->count()) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">| {{ __('Lịch sử đơn hàng') }}</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Quản lý mọi đơn hàng của bạn tại đây') }}</span>
                </h3>
            </div>
            <div class="card-body py-3">
                
                {{-- FILTERS --}}
                <form method="GET" id="filterForm">
                    <div class="d-flex flex-wrap gap-2 mb-8 overflow-auto pb-2">
                        @php
                            $currentStatus = request('status', 'all');
                            $statuses = [
                                'all' => ['label' => 'Tất cả', 'icon' => 'ki-category', 'color' => 'dark'],
                                'Processing' => ['label' => 'Đang xử lý', 'icon' => 'ki-gear', 'color' => 'info'],
                                'Pending' => ['label' => 'Đang chờ', 'icon' => 'ki-hourglass', 'color' => 'warning'],
                                'In progress' => ['label' => 'Đang chạy', 'icon' => 'ki-rocket', 'color' => 'primary'],
                                'Completed' => ['label' => 'Hoàn thành', 'icon' => 'ki-check', 'color' => 'success'],
                                'Partial' => ['label' => 'Hoàn 1 phần', 'icon' => 'ki-chart-pie-3', 'color' => 'info'],
                                'Canceled' => ['label' => 'Đã huỷ', 'icon' => 'ki-cross', 'color' => 'danger'],
                            ];
                        @endphp
                        
                        @foreach ($statuses as $key => $status)
                            <button type="button" class="filter-btn d-flex align-items-center gap-2 {{ $currentStatus == ($key == 'all' ? 'all' : $key) ? 'active' : '' }}" 
                                onclick="applyFilter('status', '{{ $key == 'all' ? '' : $key }}')">
                                <i class="ki-outline {{ $status['icon'] }} fs-5"></i>
                                {{ __($status['label']) }}
                                <span class="badge badge-circle badge-sm badge-{{ $status['color'] }} ms-1">
                                    {{ $key == 'all' ? $orders->total() : $orders->where('status', $key)->count() }}
                                </span>
                            </button>
                        @endforeach
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    </div>

                    {{-- CONTROLS AND SEARCH --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-5">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-gray-600 fw-semibold text-nowrap">{{ __('Hiển thị') }}:</span>
                            <select name="per_page" class="form-select form-select-sm form-select-solid w-70px" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                               
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-gray-600 fw-semibold text-nowrap">{{ __('Sắp xếp theo') }}:</span>
                            <select name="sort_by" class="form-select form-select-sm form-select-solid order-sort-select" onchange="this.form.submit()">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>{{ __('Thời gian đặt hàng') }}</option>
                                <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>{{ __('Cập nhật gần nhất') }}</option>
                                <option value="total_payment" {{ request('sort_by') == 'total_payment' ? 'selected' : '' }}>{{ __('Tổng tiền') }}</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                             <span class="text-gray-600 fw-semibold text-nowrap">{{ __('Thứ tự') }}:</span>
                            <select name="sort_order" class="form-select form-select-sm form-select-solid w-100px" onchange="this.form.submit()">
                                <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>{{ __('Mới nhất') }}</option>
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>{{ __('Cũ nhất') }}</option>
                            </select>
                        </div>

                        <div class="ms-md-auto col-12 col-md-auto">
                            <a href="{{ route('orders') }}" class="btn btn-sm btn-dark w-100">
                                <i class="ki-outline ki-arrows-circle fs-4 me-1"></i> {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                    
                    {{-- SEARCH BAR --}}
                    <div class="d-flex mb-5 gap-2">
                        <div class="position-relative flex-grow-1">
                            <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-solid ps-12" placeholder="{{ __('Tìm kiếm theo mã đơn hàng, link hoặc tên dịch vụ...') }}" />
                        </div>
                        <button type="submit" class="btn btn-primary text-nowrap">
                            <i class="ki-outline ki-magnifier fs-2 me-1"></i> {{ __('Tìm Kiếm') }}
                        </button>
                    </div>
                </form>

                {{-- DATA LIST --}}
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_orders_table">
                         <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_orders_table .form-check-input" value="1" />
                                    </div>
                                </th>
                                <th class="min-w-100px">{{ __('MÃ Đơn') }}</th>
                                <th class="min-w-100px">{{ __('Thời Gian') }}</th>
                                <th class="min-w-150px">{{ __('Link') }}</th>
                                <th class="min-w-100px">{{ __('Tổng Tiền') }}</th>
                                <th class="min-w-100px">{{ __('Bắt Đầu') }}</th>
                                <th class="min-w-100px">{{ __('Số Lượng') }}</th>
                                <th class="min-w-100px">{{ __('Còn Lại') }}</th>
                                <th class="min-w-150px">{{ __('Dịch Vụ') }}</th>
                                <th class="min-w-100px">{{ __('Trạng thái') }}</th>
                                <th class="text-end min-w-70px">{{ __('Thao Tác') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @if ($orders->isEmpty())
                                <tr>
                                    <td colspan="10">
                                      <div class="text-center">
                                        <svg width="184" height="152" viewBox="0 0 184 152" xmlns="http://www.w3.org/2000/svg">
                                            <g fill="none" fill-rule="evenodd">
                                                <g transform="translate(24 31.67)">
                                                    <ellipse fill-opacity=".8" fill="#F5F5F7" cx="67.797" cy="106.89" rx="67.797" ry="12.668"></ellipse>
                                                    <path d="M122.034 69.674L98.109 40.229c-1.148-1.386-2.826-2.225-4.593-2.225h-51.44c-1.766 0-3.444.839-4.592 2.225L13.56 69.674v15.383h108.475V69.674z" fill="#AEB8C2"></path>
                                                    <path d="M101.537 86.214L80.63 61.102c-1.001-1.207-2.507-1.867-4.048-1.867H31.724c-1.54 0-3.047.66-4.048 1.867L6.769 86.214v13.792h94.768V86.214z" fill="url(#linearGradient-1)" transform="translate(13.56)">
                                                    </path>
                                                    <path d="M33.83 0h67.933a4 4 0 0 1 4 4v93.344a4 4 0 0 1-4 4H33.83a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4z" fill="#F5F5F7"></path>
                                                    <path d="M42.678 9.953h50.237a2 2 0 0 1 2 2V36.91a2 2 0 0 1-2 2H42.678a2 2 0 0 1-2-2V11.953a2 2 0 0 1 2-2zM42.94 49.767h49.713a2.262 2.262 0 1 1 0 4.524H42.94a2.262 2.262 0 0 1 0-4.524zM42.94 61.53h49.713a2.262 2.262 0 1 1 0 4.525H42.94a2.262 2.262 0 0 1 0-4.525zM121.813 105.032c-.775 3.071-3.497 5.36-6.735 5.36H20.515c-3.238 0-5.96-2.29-6.734-5.36a7.309 7.309 0 0 1-.222-1.79V69.675h26.318c2.907 0 5.25 2.448 5.25 5.42v.04c0 2.971 2.37 5.37 5.277 5.37h34.785c2.907 0 5.277-2.421 5.277-5.393V75.1c0-2.972 2.343-5.426 5.25-5.426h26.318v33.569c0 .617-.077 1.216-.221 1.789z" fill="#DCE0E6"></path>
                                                </g>
                                                <path d="M149.121 33.292l-6.83 2.65a1 1 0 0 1-1.317-1.23l1.937-6.207c-2.589-2.944-4.109-6.534-4.109-10.408C138.802 8.102 148.92 0 161.402 0 173.881 0 184 8.102 184 18.097c0 9.995-10.118 18.097-22.599 18.097-4.528 0-8.744-1.066-12.28-2.902z" fill="#DCE0E6"></path>
                                                <g transform="translate(149.65 15.383)" fill="#FFF">
                                                    <ellipse cx="20.654" cy="3.167" rx="2.849" ry="2.815">
                                                    </ellipse>
                                                    <path d="M5.698 5.63H0L2.898.704zM9.259.704h4.985V5.63H9.259z">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
                                        <p class="mb-0"> {{ __('Không có đơn hàng nào') }}</p>
                                           </div>
                                    </td>
                                </tr>
                            @else
                                @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="{{ $order->id }}" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="javascript:;" class="text-gray-800 text-hover-primary fw-bold">#{{ $order->id }}</a>
                                            <i class="ki-outline ki-copy fs-5 text-gray-400 cursor-pointer copy-text mt-1" data-clipboard-text="{{ $order->id }}"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fs-7">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                                         <li>
                                            <a href="{{ route('tickets') }}">
                                                <button class="btn btn-sm btn-primary py-0">
                                                    <span class="ml-1 text-xs">{{ __('Hỗ trợ') }}</span>
                                                </button>
                                            </a>
                                        </li>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0" style="max-width: 200px;">
                                            <li class="mb-1">
                                                <a href="{{ $order->link }}" target="_blank" class="text-blue-500 text-hover-primary fs-7 text-truncate d-block" title="{{ $order->link }}">
                                                     <i class="ki-outline ki-link fs-8 me-1"></i>{{ $order->link }}
                                                </a>
                                            </li>
                                            <li>
                                                @if ($order->input_data['comments'] ?? false)
                                                <span class="text-muted fs-8 cursor-pointer text-hover-primary"
                                                    onclick="showInputData('#show-data-{{ $order->id }}')">
                                                    <i class="ki-outline ki-message-text-2 fs-8 me-1"></i>{{ __('Thông tin thêm') }}
                                                </span>
                                                <div style="display: none;" id="show-data-{{ $order->id }}">
                                                    {{ $order->input_data['comments'] }}</div>
                                                @endif
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-gray-800">{{ convert_currency($order->payment / ($order->loop_quantity ?? 1), Auth::user()->currency, false) }}</span>
                                    </td>
                                     <td>
                                        <span class="text-gray-600">{{ number_format($order->start_count ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-600">{{ number_format($order->quantity) }}</span>
                                    </td>
                                     <td>
                                        <span class="text-gray-600">{{ number_format($order->remains ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-800 fw-bold fs-7" title="{{ $order->service->id }} - {{ $order->service->name }}"><span class="fw-bold text-sm">[{{ $order->service->id }}]</span> - {{ $order->service->name }}</span>
                                    </td>
                                    <td>
                                   {!!
                                            status_order($order->status, true) !!}
                                    </td>

                                    <td class="text-end">
                                        @if ($order->status != 'Canceled' && $order->status != 'Completed')
                                            @if ($order->cancel == true)
                                            <button class="btn btn-sm btn-warning py-1 px-2"
                                                onclick="actionOrder([{{ $order->id }}], 'cancel')">
                                                <span class="ms-1 fs-7">{{ __('Yêu cầu huỷ') }}</span>
                                            </button>
                                            @endif
                                        @else
                                            @if ($order->refill == true)
                                            <button class="btn btn-sm btn-success py-1 px-2"
                                                onclick="actionOrder([{{ $order->id }}], 'refill')">
                                                <span class="ms-1 fs-7">{{ __('Yêu cầu bảo hành') }}</span>
                                            </button>
                                            @endif
                                        @endif
                                    </td>
                                    <td width="1">
            <div class="d-flex align-items-end flex-column text-nowrap">
                <div class="mb-auto d-flex flex-column">
                    
                    
                    
                    
          
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                        @if ($orders->isEmpty())
                            <div class="text-muted fs-7">
                                0 of 0 row(s) selected
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted fs-7">Page 1 of 0</span>
                                <ul class="pagination">
                                   <li class="page-item disabled"><a class="page-link" href="#"><i class="previous"></i></a></li>
                                   <li class="page-item disabled"><a class="page-link" href="#"><i class="next"></i></a></li>
                                </ul>
                            </div>
                        @else
                            <div class="text-muted fs-7">
                                {{ __('Hiển thị') }} {{ $orders->firstItem() }} - {{ $orders->lastItem() }} {{ __('trong tổng số') }} {{ $orders->total() }} {{ __('đơn hàng') }}
                            </div>
                            {{ $orders->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL COMMENTS --}}
<div class="modal fade" id="modalComments" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Thông tin thêm') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                 <div id="comments-input" class="bg-light p-3 rounded text-break"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showInputData(elm) {
    let text = $(elm).text();
    $("#modalComments").modal('show');
    text = text.replace(/(\r\n|\n|\r)/g, "<br>");
    $("#comments-input").html(text);
}

function applyFilter(name, value) {
    let form = document.getElementById('filterForm');
    let input = form.querySelector(`input[name="${name}"]`);
    if (input) {
        input.value = value;
        form.submit();
    }
}
</script>
@endsection
