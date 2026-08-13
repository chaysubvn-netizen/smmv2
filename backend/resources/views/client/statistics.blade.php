@extends('client.layouts.master')
@section('title', __('Thống kê'))

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar mb-5 mb-lg-7" id="kt_toolbar">
        <div class="page-title d-flex flex-column me-3">
            <h1 class="d-flex text-dark fw-bolder my-1 fs-3">{{ __('Trang thống kê') }}</h1>
            <ul class="breadcrumb breadcrumb-dot fw-bold text-gray-600 fs-7 my-1">
                <li class="breadcrumb-item text-gray-600">
                    <a href="{{ route('new') }}" class="text-gray-600 text-hover-primary">{{ __('Trang chủ') }}</a>
                </li>
                <li class="breadcrumb-item text-gray-500">{{ __('Thống kê') }}</li>
            </ul>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="row g-5 g-xl-8">
        <!-- Left Column: Stats & Chart -->
        <div class="col-xl-8 col-lg-7">
            <!-- Stats Cards Row -->
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                 <!-- Balance -->
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                     <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-wallet fs-2 text-warning">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                        </i>
                                     </span>
                                </div>
                                 <div class="d-flex flex-column">
                                     <h2 class="mb-0 fw-bolder text-dark fs-4">{{ convert_currency(Auth::user()?->balance ?? 0, Auth::user()?->currency ?? 'VND', false) }}</h2>
                                     <div class="text-gray-400 fw-bold fs-7">{{ __('Số dư') }}</div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Month Deposit -->
                <div class="col-sm-6 col-md-3">
                     <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                     <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-chart-simple fs-2 text-success">
                                             <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                        </i>
                                     </span>
                                </div>
                                 <div class="d-flex flex-column">
                                     <h2 class="mb-0 fw-bolder text-dark fs-4">{{ convert_currency(Auth::user()?->total_deposit ?? 0, Auth::user()?->currency ?? 'VND', false) }}</h2>
                                     <div class="text-gray-400 fw-bold fs-7">{{ __('Nạp tháng') }}</div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Total Orders -->
                 <div class="col-sm-6 col-md-3">
                     <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                 <div class="symbol symbol-40px me-3">
                                     <span class="symbol-label bg-light-danger">
                                        <i class="ki-duotone ki-basket fs-2 text-danger">
                                             <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                        </i>
                                     </span>
                                </div>
                                 <div class="d-flex flex-column">
                                     <h2 class="mb-0 fw-bolder text-dark fs-4">{{ number_format($totalOrders) }}</h2>
                                     <div class="text-gray-400 fw-bold fs-7">{{ __('Đơn hàng') }}</div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Products Used -->
                 <div class="col-sm-6 col-md-3">
                     <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                 <div class="symbol symbol-40px me-3">
                                     <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-briefcase fs-2 text-primary">
                                             <span class="path1"></span><span class="path2"></span>
                                        </i>
                                     </span>
                                </div>
                                 <div class="d-flex flex-column">
                                     <h2 class="mb-0 fw-bolder text-dark fs-4">{{ number_format($productsUsed) }}</h2>
                                     <div class="text-gray-400 fw-bold fs-7">{{ __('SP đã mua') }}</div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!--begin::Charts Widget-->
             <div class="card card-xl-stretch mb-xl-8 h-xl-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">{{ __('Biểu đồ thống kê') }}</span>
                    </h3>
                     <!-- Legend -->
                     <div class="card-toolbar" data-kt-buttons="true">
                        <span class="fs-6 fw-bold text-gray-400 me-3">{{ __('Chú thích:') }}</span>
                        <div class="d-flex align-items-center">
                             <div class="d-flex align-items-center me-3">
                                <span class="bullet bullet-dot bg-success me-2 h-10px w-10px"></span>
                                <span class="text-gray-600 fw-bold fs-7">{{ __('Tiền nạp') }}</span>
                             </div>
                             <div class="d-flex align-items-center">
                                <span class="bullet bullet-dot bg-warning me-2 h-10px w-10px"></span>
                                <span class="text-gray-600 fw-bold fs-7">{{ __('Đơn hàng') }}</span>
                             </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div id="kt_charts_widget_statistical" class="min-h-300px min-h-md-350px card-rounded-bottom"></div>
                </div>
             </div>
             <!--end::Charts Widget-->
        </div>

         <!-- Right Column: CTA & Recent Orders -->
        <div class="col-xl-4 col-lg-5">
            <!-- CTA -->
             <div class="card mb-5 mb-xl-8" style="background: linear-gradient(135deg, rgba(70, 128, 255, 0.1) 0%, rgba(34, 197, 94, 0.1) 100%); border: 1px solid rgba(70, 128, 255, 0.2);">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold text-dark">{{ __('Tăng trưởng vượt bậc với SMM') }}</h5>
                    <p class="text-muted mb-3" style="font-size: 14px; line-height: 1.6;">
                        {{ __('Mua dịch vụ SMM để quản lý mạng xã hội hiệu quả, tăng tương tác & tiếp cận nhanh!') }}
                    </p>
                    <a href="{{ route('new') }}" class="btn btn-primary w-100 fw-semibold">
                        <i class="ki-duotone ki-basket me-2 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> {{ __('Mua ngay') }}
                    </a>
                </div>
             </div>

             <!-- Recent Orders -->
            <div class="card mb-5 mb-xl-8">
                <div class="card-header d-flex align-items-center justify-content-between border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-5 mb-1">{{ __('Đơn hàng gần đây') }}</span>
                    </h3>
                    <a href="{{ route('orders') }}" class="text-primary small fw-semibold">{{ __('Xem tất cả') }}</a>
                </div>
                <div class="card-body py-3">
                     <div class="table-responsive">
                         <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bolder text-muted">
                                    <th class="min-w-100px">{{ __('Dịch vụ') }}</th>
                                    <th class="min-w-100px text-end">{{ __('Thanh toán') }}</th>
                                    <th class="min-w-100px text-end">{{ __('Trạng thái') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder text-hover-primary fs-6">{{Str::limit($order->service_name, 25)}}</span>
                                                <span class="text-muted fw-bold text-muted d-block fs-7">{{ number_format($order->quantity) }} sl</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-dark fw-bolder d-block fs-6">{{ convert_currency($order->payment / ($order->loop_quantity ?? 1), Auth::user()->currency, false) }}</span>
                                        </td>
                                        <td class="text-end">
                                            {!! status_order($order->status, true) !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ki-duotone ki-information-5 fs-1 d-block mb-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> 
                                                {{ __('Chưa có đơn hàng nào.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                         </table>
                    </div>
                </div>
            </div>
        </div>    <!--end::Recent Orders Widget-->
         </div>

@endsection

@section('scripts')
<script>
    "use strict";

    var KTChartsWidgetStatistical = function () {
        var chart = {
            self: null,
            rendered: false
        };

        var initChart = function(chart) {
            var element = document.getElementById("kt_charts_widget_statistical");

            if (!element) {
                return;
            }

            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
            var baseColor = KTUtil.getCssVariableValue('--bs-success');
            var secondaryColor = KTUtil.getCssVariableValue('--bs-warning');

            var options = {
                series: [{
                    name: '{{ __("Tiền nạp") }}',
                    data: @json($depositData)
                }, {
                    name: '{{ __("Đơn hàng") }}',
                    data: @json($ordersData)
                }],
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {

                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                    colors: [baseColor, secondaryColor]
                },
                xaxis: {
                    categories: @json($categories),
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    tickAmount: 7,
                    labels: {
                        rotate: 0,
                        rotateAlways: false,
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: baseColor,
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        },
                         formatter: function (val) {
                            return val;
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    },
                },
                colors: [baseColor, secondaryColor],
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    strokeColor: [baseColor, secondaryColor],
                    strokeWidth: 3
                }
            };

            chart.self = new ApexCharts(element, options);
            chart.self.render();
            chart.rendered = true;
        }

        return {
            init: function () {
                initChart(chart);

                // Update chart on theme mode change
                KTThemeMode.on("kt.thememode.change", function() {
                    if (chart.rendered) {
                        chart.self.destroy();
                    }
                    initChart(chart);
                });
            }
        }
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTChartsWidgetStatistical.init();
    });
</script>
@endsection
