<div class="col-md-6 form-group mb-3">
    <label for="google_login_status" class="form-label">Trạng thái Google Login</label>
    <select class="form-select" id="google_login_status" name="google_login_status">
        <option value="inactive" {{ s('google_login_status') == 'inactive' ? 'selected' : '' }}>Tắt</option>
        <option value="active" {{ s('google_login_status') == 'active' ? 'selected' : '' }}>Bật</option>
    </select>
</div>

<div class="col-md-6 form-group mb-3">
    <label for="google_client_id" class="form-label">Google Client ID</label>
    <input type="text" class="form-control" id="google_client_id" name="google_client_id" 
        value="{{ s('google_client_id') }}" placeholder="Nhập Google Client ID">
</div>

<div class="col-md-6 form-group mb-3">
    <label for="google_client_secret" class="form-label">Google Client Secret</label>
    <input type="password" class="form-control" id="google_client_secret" name="google_client_secret" 
        value="{{ s('google_client_secret') }}" placeholder="Nhập Google Client Secret">
</div>

<div class="col-md-6 form-group mb-3">
    <label for="google_redirect_uri" class="form-label">Redirect URI</label>
    <input type="text" class="form-control" id="google_redirect_uri" name="google_redirect_uri" 
        value="{{ s('google_redirect_uri') ?: url('/auth/google/callback') }}" 
        placeholder="{{ url('/auth/google/callback') }}">
    <small class="text-muted">Mặc định: <code>{{ url('/auth/google/callback') }}</code></small>
</div>
