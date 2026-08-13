```blade
@extends('client.layouts.master')
@section('title', 'Cập nhật')
@section('content')
@push('styles')

<!--end::Actions-->

<div class="content flex-column-fluid" id="kt_content">
    <div class="card">
        <div class="card-body">
            <div class="timeline timeline-border-dashed">

                @forelse ($updates as $ut)
                <div class="timeline-item">
                    <!--begin::Timeline line-->
                    <div class="timeline-line"></div>
                    <!--end::Timeline line-->

                    <!--begin::Timeline icon-->
                    <div class="timeline-icon me-4">
                        @if ($ut->type == 'increase')
                        <i class="fa fa-arrow-up fs-1 text-danger"></i>
                        @elseif ($ut->type == 'decrease')
                        <i class="fa fa-arrow-down fs-1 text-success"></i>
                        @elseif ($ut->type == 'add')
                        <i class="fa fa-plus fs-1 text-info"></i>
                        @elseif ($ut->type == 'delete')
                        <i class="fa fa-times fs-1 text-secondary"></i>
                        @endif
                    </div>
                    <!--end::Timeline icon-->

                    <!--begin::Timeline content-->
                    <div class="timeline-content mb-10 mt-n2">
                        <div class="overflow-auto pe-3">
                            <!--begin::Title-->
                            <div class="fs-6 mb-2"> <img src="{{ asset($ut->service->category->icon) }}" alt="image"
                                    class="rounded-circle" width="20" height="20">

                                [{{ $ut->service->id }}] {{ $ut->service->name }}

                                @if ($ut->type == 'increase' || $ut->type == 'decrease')
                                <p class="mb-0 fs-7 fw-semibold">
                                    <span class="{{ $ut->type == 'increase' ? 'text-danger' : 'text-success' }}">
                                        {{ $ut->type == 'increase' ? __('Tăng giá') : __('Giảm giá') }}
                                    </span>:
                                    {{ convert_currency($ut->rate_old, Auth::user()->currency, false) }}
                                    <i
                                        class="fa {{ $ut->type == 'increase' ? 'fa-arrow-up text-danger' : 'fa-arrow-down text-success' }} mx-2"></i>
                                    {{ convert_currency($ut->rate_new, Auth::user()->currency, false) }}
                                </p>
                                @elseif ($ut->type == 'add')
                                <p class="mb-0 fs-7 fw-semibold text-info">{{ __('Dịch vụ mới') }}</p>
                                @elseif ($ut->type == 'delete')
                                <p class="mb-0 fs-7 fw-semibold text-purple">{{ __('Tắt dịch vụ') }}</p>
                                @endif
                            </div>
                            <!--end::Title-->

                            <!--begin::Description-->
                            <div class="d-flex align-items-center mt-1 fs-6">
                                <div class="text-muted me-2 fs-8">
                                    {{ $ut->created_at }}
                                </div>
                            </div>
                            <!--end::Description-->
                        </div>
                    </div>
                    <!--end::Timeline content-->
                </div>
                @empty
                <div class="alert alert-info">
                    {{ __('Chưa có cập nhật dịch vụ nào.') }}
                </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection