@extends('client.layouts.master')
@section('title', 'Affiliate Program')
@section('content')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="alert alert-primary mb-0 text-primary py-2 text-sm fw-bold">
                🤑
                {{ __('Bạn muốn nhận được tiền? Sử dụng Chương trình liên kết của chúng tôi để nhận được nhiều hơn! Giới thiệu bạn bè của bạn và hãy kiếm tiền!') }}
                👍
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Liên kết chia sẻ") }}</span>
                            <div class="text-sm text-gray-500">
                                <span href="{{ route('auth.register') }}"
                                    class="text-primary fw-bold">{{ route('ref', ['username' => Auth::user()->username]) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Phần trăm hoa hồng") }}</span>
                            <div class="text-sm">
                                <span class="text-primary fw-bold">{{ s('affiliate_percent') }}%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Hạn mức trả thưởng") }}</span>
                            <div class="text-sm">
                                <span class="text-primary fw-bold">{{ convert_currency(s('affiliate_min'), Auth::user()->currency, false) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-8 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Tổng thu nhập") }}</span>
                            <div class="text-sm text-gray-500">
                                <span>{{ number_format(Auth::user()->total_commission) }}đ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Chưa trả") }}</span>
                            <div class="text-sm">
                                <span
                                    class="text-primary fw-bold">{{ convert_currency(Auth::user()->commission, Auth::user()->level, false) }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-sm fw-bold text-gray-700">{{ __("Đã trả thưởng") }}</span>
                            <div class="text-sm">
                                <span
                                    class="text-primary fw-bold">{{ convert_currency(Auth::user()->total_commission, Auth::user()->level, false) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="table-repsonsive">
                        <table class="table w-100">
                            <thead>
                                <tr>
                                    <th>{{ __("Giới thiệu") }}</th>
                                    <th>{{ __("Thu nhập") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usersRef as $user)
                                    <tr>
                                        <td>
                                            <span class="text-primary fw-bold">
                                                {{ substr($user->user->username, 0, 4) }}...
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-bold">
                                                {{ convert_currency($user->commission, Auth::user()->level, false) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
