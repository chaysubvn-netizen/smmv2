@extends('client.layouts.master')
@section('title', __('Cấu hình'))
@section('content')
    @include('client.layouts.breadcrumb', ['title' => __('Cấu hình')])
    <div class="row">
        <div class="col-md-12 col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Thông tin cơ bản') }}</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Email"
                                    value="{{ Auth::user()->email }}" readonly>
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" placeholder="username"
                                    value="{{ Auth::user()->username }}" readonly>
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="balance" class="form-label">{{ __('Số tiền') }}</label>
                                <input type="text" class="form-control" id="balance" placeholder="Số tiền"
                                    value="{{ convert_currency(Auth::user()->balance, Auth::user()->currency, false) }}"
                                    readonly>
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="created_at" class="form-label">{{ __('Đã đăng kí') }}</label>
                                <input type="text" class="form-control" id="created_at" placeholder="Đã đăng kí"
                                    value="{{ Auth::user()->created_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>
                        </div>
                        @if (Auth::user()->two_factor_secret)
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#secrest">{{ __('Tắt bảo mật 2 bước') }}</button>
                        @else
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#secrest">{{ __('Bảo mật 2 bước') }}</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Thay đổi mật khẩu') }}</h4>   
                </div>
                <div class="card-body">
                    <form action="{{ route('change-password') }}" method="POST" bvq-request>
                        <div class="form-group mb-3">
                            <label for="old_password" class="form-label">{{ __('Mật khẩu cũ') }}</label>
                            <input type="password" class="form-control" name="old_password" id="old_password">
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_password" class="form-label">{{ __('Mật khẩu mới') }}</label>
                            <input type="password" class="form-control" name="new_password" id="new_password"
                                placeholder="Mật khẩu mới">
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm_password" class="form-label">{{ __('Xác nhận mật khẩu') }}</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                                placeholder="Xác nhận mật khẩu">
                        </div>
                        <button type="submit" class="btn btn-primary col-12">{{ __('Cập nhật mật khẩu') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Thiết lập tài khoản') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('request-action') }}" method="POST" bvq-request>
                        <input type="hidden" name="action" value="update-settings">
                        <div class="form-group mb-3">
                            <label for="language" class="form-label">{{ __('Ngôn ngữ') }}</label>
                            <select class="form-select" name="language" id="language"
                                onchange="changeLanguage(this.value)">
                                <option value="vi" {{ Auth::user()->language == 'vi' ? 'selected' : '' }}>Tiếng Việt
                                </option>
                                <option value="en" {{ Auth::user()->language == 'en' ? 'selected' : '' }}>English
                                </option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="currency" class="form-label">{{ __('Tiền tệ') }}</label>
                            <select class="form-select" name="currency" id="currency"
                                onchange="changeCurrency(this.value)">
                                @foreach (\App\Models\Currency::where('status', 'active')->get() as $currency)
                                    <option value="{{ $currency->code }}"
                                        {{ Auth::user()->currency == $currency->code ? 'selected' : '' }}>
                                        {{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary col-12 mb-3">{{ __('Lưu thiết lập') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="secrest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('request-action') }}" method="POST" bvq-request class="modal-content" reload="true">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Thiết lập Google Authentication') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if (Auth::user()->two_factor_secret)
                    <div class="modal-body">
                        <input type="hidden" name="action" value="disable-2fa">
                        <p class="fw-bold text-gray-700 text-sm">
                            {{ __('Bạn có chắc chắn muốn tắt bảo mật 2 bước không?') }}</p>
                        <div class="form-group">
                            <label for="code" class="form-label">{{ __('Mã xác thực') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="code" id="code"
                                    placeholder="{{ __('Nhập mã xác thực') }}">
                                <button type="submit" class="btn btn-primary">{{ __('Xác thực') }}</button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="modal-body">
                        <input type="hidden" name="action" value="enable-2fa">
                        <input type="hidden" name="secret" value="{{ $secret }}">
                        <p class="fw-bold text-gray-700 text-sm">
                            {{ __('Quét mã QR. Nó sẽ tạo một mã số có 6 chữ số để bạn nhập vào bên dưới.') }}</p>
                        <div class="mb-3 d-flex justify-content-center">
                            {!! $qrCode !!}
                        </div>
                        <div class="p-3 rounded-2 border border-primary mb-3">
                            <p class="fw-bold text-gray-700 text-sm">
                                {{ __('Lưu ý: Nếu bạn không thể quét mã QR, hãy sao chép mã này và nhập vào ứng dụng Google Authenticator:') }}
                            </p>
                            <p class="fw-bold text-primary text-sm">{{ $secret }}</p>
                        </div>
                        <div class="form-group">
                            <label for="code" class="form-label">{{ __('Mã xác thực') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="code" id="code"
                                    placeholder="{{ __('Nhập mã xác thực') }}">
                                <button type="submit" class="btn btn-primary">{{ __('Xác thực') }}</button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
