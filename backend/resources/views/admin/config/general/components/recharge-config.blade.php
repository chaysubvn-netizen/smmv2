<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true">
    @csrf
    <div class="row g-4">
        {{-- Section 1: Thông tin cơ bản --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="background: rgba(255,255,255,0.03);">
                <div class="card-header bg-transparent border-bottom border-secondary-subtle py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="avatar avatar-sm bg-info-subtle text-info rounded-circle me-2">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        Thông tin nạp tiền
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="transfer_code" class="form-label text-secondary fw-medium small text-uppercase">Mã nội dung chuyển khoản</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary-subtle border-0"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control border-0" style="background: rgba(0,0,0,0.1);" id="transfer_code" name="transfer_code"
                                value="{{ s('transfer_code') }}" placeholder="Ví dụ: NAPTIEN">
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="recharge_promotion" class="form-label text-secondary fw-medium small text-uppercase">Khuyến mãi [%]</label>
                            <div class="input-group">
                                <input type="number" class="form-control border-0" style="background: rgba(0,0,0,0.1);" id="recharge_promotion" name="recharge_promotion"
                                    value="{{ s('recharge_promotion') }}" placeholder="0">
                                <span class="input-group-text bg-secondary-subtle border-0">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_min" class="form-label text-secondary fw-medium small text-uppercase">Nạp tối thiểu</label>
                            <div class="input-group">
                                <input type="number" class="form-control border-0" style="background: rgba(0,0,0,0.1);" id="promotion_min" name="promotion_min"
                                    value="{{ s('promotion_min') }}" placeholder="10000">
                                <span class="input-group-text bg-secondary-subtle border-0">đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Trạng thái & QR --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 shadow-sm border-0" style="background: rgba(255,255,255,0.03);">
                <div class="card-header bg-transparent border-bottom border-secondary-subtle py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <span class="avatar avatar-sm bg-success-subtle text-success rounded-circle me-2">
                            <i class="fas fa-toggle-on"></i>
                        </span>
                        Trạng thái thanh toán
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-secondary small text-uppercase fw-medium">Ngân hàng</label>
                            <select class="form-select border-0" style="background: rgba(0,0,0,0.1);" name="bank_status">
                                <option value="active" {{ s('bank_status') == 'active' ? 'selected' : '' }}>Bật</option>
                                <option value="inactive" {{ s('bank_status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small text-uppercase fw-medium">USDT</label>
                            <select class="form-select border-0" style="background: rgba(0,0,0,0.1);" name="usdt_status">
                                <option value="active" {{ s('usdt_status') == 'active' ? 'selected' : '' }}>Bật</option>
                                <option value="inactive" {{ s('usdt_status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary small text-uppercase fw-medium">Binance</label>
                            <select class="form-select border-0" style="background: rgba(0,0,0,0.1);" name="binance_status">
                                <option value="active" {{ s('binance_status') == 'active' ? 'selected' : '' }}>Bật</option>
                                <option value="inactive" {{ s('binance_status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.15);">
                        <div class="mb-3">
                            <label for="binance_id" class="form-label text-secondary small text-uppercase fw-medium">Binance ID nhận</label>
                            <input type="text" class="form-control border-0 bg-transparent p-0" id="binance_id" name="binance_id"
                                value="{{ s('binance_id') }}" placeholder="Nhập Binance ID">
                        </div>
                        <div class="mb-0">
                            <label for="binance_qr" class="form-label text-secondary small text-uppercase fw-medium">Link QR Binance</label>
                            <input type="text" class="form-control border-0 bg-transparent p-0" id="binance_qr" name="binance_qr"
                                value="{{ s('binance_qr') }}" placeholder="Dán link ảnh QR">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Cấu hình Auto --}}
        <div class="col-12">
            <div class="card shadow-none border-0 overflow-hidden" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-md bg-primary text-white rounded-3 shadow">
                                <i class="fas fa-robot"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0 text-primary fw-bold">Auto Binance Pay (API chính thức)</h5>
                            <p class="text-secondary mb-0 small opacity-75">Tự động kiểm tra giao dịch và cộng tiền ngay lập tức.</p>
                        </div>
                    </div>
                    
                    <div class="row align-items-end g-3">
                        <div class="col-lg-7">
                            <label for="binance_api_key" class="form-label text-secondary small text-uppercase fw-bold">Binance API Key</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-key text-primary"></i></span>
                                <input type="text" class="form-control border-0" id="binance_api_key" name="binance_api_key"
                                    value="{{ s('binance_api_key') }}" placeholder="Nhập Binance API Key">
                            </div>
                            <label for="binance_secret_key" class="form-label text-secondary small text-uppercase fw-bold mt-3">Binance Secret Key</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-0"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" class="form-control border-0" id="binance_secret_key" name="binance_secret_key"
                                    value="{{ s('binance_secret_key') }}" placeholder="Nhập Binance Secret Key" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="p-3 rounded bg-white-5 border border-primary-subtle" style="background: rgba(255,255,255,0.02);">
                                <div class="d-flex small text-secondary">
                                    <i class="fas fa-lightbulb text-warning me-2 mt-1"></i>
                                    <span>Vui lòng cài đặt Cron Job chạy mỗi phút một lần để xử lý đơn nạp tự động.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer: Save Button --}}
        <div class="col-12 mt-4">
            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: rgba(0,0,0,0.1);">
                <span class="text-secondary small"><i class="fas fa-info-circle me-1"></i> Hãy kiểm tra kỹ thông tin trước khi lưu.</span>
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill">
                    <i class="fas fa-save me-2"></i> Lưu cấu hình
                </button>
            </div>
        </div>
    </div>
</form>
