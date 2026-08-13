@extends('admin.layouts.app')
@section('title', 'Telegram Link & 2FA')
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="card custom-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-1">Cấu hình Telegram Link & 2FA</h4>
                    <p class="text-muted mb-0">Cấu hình bot dùng để liên kết tài khoản, gửi thông báo và mã OTP.</p>
                </div>
                <span class="badge {{ $config->telegram_status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $config->telegram_status === 'active' ? 'Đang bật' : 'Đang tắt' }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.config.telegram-link.update') }}" method="POST" data-ajax="true">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bot Username</label>
                            <div class="input-group"><span class="input-group-text">@</span><input class="form-control" name="tele_bot_username" value="{{ $config->tele_bot_username }}" placeholder="dhsolution_bot"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link Telegram Bot</label>
                            <input type="url" class="form-control" name="telegram_link" value="{{ $config->telegram_link }}" placeholder="https://t.me/dhsolution_bot">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Bot Token</label>
                            <input type="password" class="form-control" name="telegram_bot" value="{{ $config->telegram_bot }}" autocomplete="off" required>
                            <small class="text-muted">Lấy token từ @BotFather. Token không được hiển thị qua API Next.js.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="telegram_status">
                                <option value="active" @selected($config->telegram_status === 'active')>Bật</option>
                                <option value="inactive" @selected($config->telegram_status !== 'active')>Tắt</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Webhook URL</label>
                            <input type="url" class="form-control" name="telegram_webhook_url" value="{{ $config->telegram_webhook_url ?: $defaultWebhookUrl }}" required>
                            <small class="text-muted">Telegram yêu cầu URL HTTPS công khai. Endpoint nhận webhook: <code>/api/telegram/webhook</code>.</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Lưu cấu hình</button>
                </form>
                <hr class="my-4">
                <form action="{{ route('admin.config.telegram-link.webhook') }}" method="POST" data-ajax="true">
                    @csrf
                    <input type="hidden" name="telegram_webhook_url" value="{{ $config->telegram_webhook_url ?: $defaultWebhookUrl }}">
                    <button type="submit" class="btn btn-info"><i class="ti ti-webhook me-1"></i>Kiểm tra Bot & đăng ký Webhook</button>
                </form>
                <div class="alert alert-light border mt-4 mb-0">
                    <strong>Lệnh dành cho người dùng</strong>
                    <div class="mt-2"><code>/lienket API_KEY</code> — liên kết tài khoản.</div>
                    <div><code>/huylienket</code> — hủy liên kết tài khoản.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
