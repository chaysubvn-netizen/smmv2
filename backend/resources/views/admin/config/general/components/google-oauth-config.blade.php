<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true">
    @csrf
    <div class="row">
    
        <div class="col-12 col-lg-6 form-group mb-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Hướng dẫn:</strong> Để sử dụng Google OAuth Login, bạn cần tạo OAuth 2.0 Client ID từ 
                <a href="https://console.cloud.google.com/" target="_blank" class="alert-link">Google Cloud Console</a>.
            </div>
        </div>

        <div class="col-md-12 form-group mb-3">
            <label for="google_login_status" class="form-label">
                <i class="fab fa-google me-2"></i>Trạng thái Google Login
            </label>
            <select class="form-select" id="google_login_status" name="google_login_status">
                <option value="inactive" {{ s('google_login_status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
                <option value="active" {{ s('google_login_status') == 'active' ? 'selected' : '' }}>Bật</option>
            </select>
            <small class="text-muted">Bật/tắt chức năng đăng nhập bằng Google</small>
        </div>

        <div class="col-md-12 form-group mb-3">
            <label for="google_client_id" class="form-label">
                <i class="fas fa-key me-2"></i>Google Client ID
            </label>
            <input type="text" class="form-control" id="google_client_id" name="google_client_id" 
                value="{{ s('google_client_id') }}" placeholder="Nhập Google Client ID">
            <small class="text-muted">Client ID từ Google Cloud Console</small>
        </div>

        <div class="col-md-12 form-group mb-3">
            <label for="google_client_secret" class="form-label">
                <i class="fas fa-lock me-2"></i>Google Client Secret
            </label>
            <input type="password" class="form-control" id="google_client_secret" name="google_client_secret" 
                value="{{ s('google_client_secret') }}" placeholder="Nhập Google Client Secret">
            <small class="text-muted">Client Secret từ Google Cloud Console</small>
        </div>

        <div class="col-md-12 form-group mb-3">
            <label for="google_redirect_uri" class="form-label">
                <i class="fas fa-link me-2"></i>Redirect URI
            </label>
            <input type="text" class="form-control" id="google_redirect_uri" name="google_redirect_uri" 
                value="{{ s('google_redirect_uri') ?: url('/auth/google/callback') }}" 
                placeholder="{{ url('/auth/google/callback') }}">
            <small class="text-muted">
                URL callback sau khi đăng nhập Google. Mặc định: <code>{{ url('/auth/google/callback') }}</code>
            </small>
        </div>

        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Lưu ý:</strong> Sau khi lưu cấu hình, bạn cần thêm Redirect URI này vào 
                <strong>Authorized redirect URIs</strong> trong Google Cloud Console.
            </div>
        </div>

        <div class="col-12">
            <h5 class="mb-3"><i class="fas fa-question-circle me-2"></i>Hướng dẫn cấu hình</h5>
            <ol class="ps-3">
                <li class="mb-2">Truy cập <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                <li class="mb-2">Tạo project mới hoặc chọn project hiện có</li>
                <li class="mb-2">Vào <strong>APIs & Services</strong> → <strong>Credentials</strong></li>
                <li class="mb-2">Click <strong>Create Credentials</strong> → <strong>OAuth 2.0 Client ID</strong></li>
                <li class="mb-2">Chọn <strong>Web application</strong></li>
                <li class="mb-2">Thêm Redirect URI: <code>{{ url('/auth/google/callback') }}</code></li>
                <li class="mb-2">Copy <strong>Client ID</strong> và <strong>Client Secret</strong> vào form trên</li>
                <li class="mb-2">Lưu cấu hình và test đăng nhập</li>
            </ol>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="bx bx-save"></i> Lưu thay đổi
    </button>
</form>