@extends('admin.layouts.app')
@section('title', 'Danh sách API Key')
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | MBBANK | SPAY5S.COM</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="mb_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="mb_account_number" name="mb_account_number"
                                    value="{{ s('mb_account_number') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="mb_api_key" class="form-label">API KEY SPAY5S.COM</label>
                                <input type="text" class="form-control" id="mb_api_key" name="mb_api_key"
                                    value="{{ s('mb_api_key') }}" placeholder="Nhập API KEY">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | Vietcombank | SPAY5S.COM</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="vcb_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="vcb_account_number" name="vcb_account_number"
                                    value="{{ s('vcb_account_number') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="vcb_api_key" class="form-label">API KEY SPAY5S.COM</label>
                                <input type="text" class="form-control" id="vcb_api_key" name="vcb_api_key"
                                    value="{{ s('vcb_api_key') }}" placeholder="Nhập API KEY">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | ACB | SPAY5S.COM</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="acb_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="acb_account_number" name="acb_account_number"
                                    value="{{ s('acb_account_number') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="acb_api_key" class="form-label">API KEY SPAY5S.COM</label>
                                <input type="text" class="form-control" id="acb_api_key" name="acb_api_key"
                                    value="{{ s('acb_api_key') }}" placeholder="Nhập API KEY">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | Viettinbank | SPAY5S.COM</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="vtb_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="vtb_account_number"
                                    name="vtb_account_number" value="{{ s('vtb_account_number') }}"
                                    placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-6 form-group mb-3">
                                <label for="vtb_api_key" class="form-label">API KEY SPAY5S.COM</label>
                                <input type="text" class="form-control" id="vtb_api_key" name="vtb_api_key"
                                    value="{{ s('vtb_api_key') }}" placeholder="Nhập API KEY">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | FPAYMENT | USDT</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="usdt_wallet" class="form-label">Address Wallet</label>
                                <input type="text" class="form-control" id="usdt_wallet" name="usdt_wallet"
                                    value="{{ s('usdt_wallet') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="usdt_wallet_token" class="form-label">Token Wallet</label>
                                <input type="text" class="form-control" id="usdt_wallet_token"
                                    name="usdt_wallet_token" value="{{ s('usdt_wallet_token') }}"
                                    placeholder="Nhập API KEY">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="usdt_exchange_rate" class="form-label">Exchange Rate</label>
                                <input type="text" class="form-control" id="usdt_exchange_rate"
                                    name="usdt_exchange_rate" value="{{ s('usdt_exchange_rate') }}"
                                    placeholder="Nhập link cron">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
      
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">AutoBank | OCB | OMNI</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="ocb_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="ocb_account_number" name="ocb_account_number"
                                    value="{{ s('ocb_account_number') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="ocb_username" class="form-label">Tài khoản (Username)</label>
                                <input type="text" class="form-control" id="ocb_username" name="ocb_username"
                                    value="{{ s('ocb_username') }}" placeholder="Tên đăng nhập">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="ocb_password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="ocb_password" name="ocb_password"
                                    value="{{ s('ocb_password') }}" placeholder="Mật khẩu">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
      
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Notification</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="telegram_bot" class="form-label">Bot Token</label>
                                <input type="text" class="form-control" id="telegram_bot" name="telegram_bot"
                                    value="{{ s('telegram_bot') }}" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="telegram_chat_id" class="form-label">ChatID (Nhận)</label>
                                <input type="text" class="form-control" id="telegram_chat_id" name="telegram_chat_id"
                                    value="{{ s('telegram_chat_id') }}" placeholder="Nhập API KEY">
                            </div>
                            <div class="col-md-12 col-lg-4 form-group mb-3">
                                <label for="telegram_status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="telegram_status" name="telegram_status">
                                    <option value="active" {{ s('telegram_status') == 'active' ? 'selected' : '' }}>Bật
                                    </option>
                                    <option value="inactive" {{ s('telegram_status') == 'inactive' ? 'selected' : '' }}>
                                        Tắt</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-6">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Notification | Email | SMTP</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                    value="{{ s('smtp_host') }}" placeholder="Nhập SMTP Host">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                <input type="text" class="form-control" id="smtp_port" name="smtp_port"
                                    value="{{ s('smtp_port') }}" placeholder="Nhập SMTP Port">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="smtp_username" class="form-label">SMTP Username</label>
                                <input type="text" class="form-control" id="smtp_username" name="smtp_username"
                                    value="{{ s('smtp_username') }}" placeholder="Nhập SMTP User">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                <input type="text" class="form-control" id="smtp_password" name="smtp_password"
                                    value="{{ s('smtp_password') }}" placeholder="Nhập SMTP Pass">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
