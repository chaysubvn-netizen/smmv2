@extends('client.layouts.master')
@section('title', __('Đơn lặp lại'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                <form method="GET" class="row mb-3">
                        <div class="col-md-12 col-lg-4 form-group mb-3">
                            <select name="status" class="form-control">
                                <option value="">{{ __('Tất cả trạng thái') }}</option>
                                <option value="Processing" {{ request('status') == 'Processing' ? 'selected' : '' }}>
                                    {{ __('Đang xử lý') }}
                                </option>
                                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                    {{ __('Hoàn thành') }}
                                </option>
                                <option value="Canceled" {{ request('status') == 'Canceled' ? 'selected' : '' }}>
                                    {{ __('Huỷ') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-12 col-lg-8 form-group mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    placeholder="{{ __('Tìm kiếm theo mã đơn hàng') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary d-flex align-items-center gap-1" type="submit">
                                        <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                                        <span class="ml-1 text-sm">{{ __('Tìm kiếm') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table w-100">
                            <thead>
                                <tr class="fw-semibold text-gray-700 fs-6">
                                    <th colspan="3">{{ __('Thông tin') }}</th>
                                    <th>{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->isEmpty())
                                       <td colspan="8">
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
                                @endif
                                @foreach ($orders as $order)
                                    <tr class="">
                                        <td>
                                            <ul>
                                                <li class="">ID: <span
                                                        class="text-primary fw-bold">{{ $order->id }}</span></li>
                                                <li class="text-sm">
                                                    {{ $order->created_at->format('d/m/Y H:i') }}</li>
                                                </li>
                                                <li>
                                            <a href="{{ route('tickets') }}">
                                                <button class="btn btn-sm btn-primary py-0">
                                                    <span class="ml-1 text-xs">{{ __('Hỗ trợ') }}</span>
                                                </button>
                                            </a>
                                        </li>
                                    </ul>
                                </td>
                                        <td>
                                            <ul>
                                                <li><span class="fw-bold text-sm">[{{ $order->service->id }}]</span> -
                                                    <span class="text-sm fw-bold">{{ $order->service->name }}</span>
                                                </li>
                                                <li>
                                                    <a href="{{ $order->link }}" target="_blank"
                                                        class="text-sm text-primary">{{ $order->link }}</a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <ul>
                                                <li class="text-sm mb-0">{{ __('Tổng tiền') }}: <span
                                                        class="text-primary fw-bold">{{ convert_currency(($order->rate * ($order->quantity * $order->loop_quantity)) / 1000, Auth::user()->currency, false) }}</span>
                                                </li>
                                                <li class="text-sm mb-0">
                                                    {{ __('Số lượng') }}: <span
                                                        class="text-gray-700 fw-bold">{{ number_format($order->quantity * $order->loop_quantity) }}</span>
                                                </li>
                                                <li class="text-sm mb-0">
                                                    {{ __('Bắt đầu') }}: <span
                                                        class="text-gray-700 fw-bold">{{ number_format($order->start_count ?? 0) }}</span>
                                                </li>
                                                <li class="text-sm mb-0">
                                                    {{ __('Còn lại') }}: <span
                                                        class="text-gray-700 fw-bold">{{ number_format($order->remains ?? 0) }}</span>
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <ul>
                                                <li class="text-sm mb-0">{{ __('Trạng thái') }}: {!! status_order($order->dripfeed_status, true) !!}
                                                </li>
                                                <li class="mb-0 text-sm">
                                                    {{ __('Khoảng cách') }}: <span
                                                        class="text-gray-700 fw-bold">{{ $order->loop_spacing }}</span>
                                                </li>
                                                <li class="mb-0 text-sm">
                                                    {{ __('Chạy') }}: <span
                                                        class="text-primary fw-bold">{{ $order->run }}</span> / <span
                                                        class="text-gray-800 fw-bold">{{ $order->loop_quantity }}</span>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $orders->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
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
</script>
@endsection