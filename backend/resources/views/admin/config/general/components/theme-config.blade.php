<style>
    .theme-selector-item {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
    }

    .theme-selector-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .theme-selector-input:checked + .theme-selector-item {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }

    .theme-selector-input:checked + .theme-selector-item::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #0d6efd;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .theme-preview-img {
        width: 100%;
        height: 150px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        overflow: hidden;
    }

    .theme-preview-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .theme-info {
        padding: 15px;
        text-align: center;
    }

    .theme-name {
        font-weight: 600;
        margin-bottom: 5px;
    }
</style>
<form action="{{ route('admin.config.general.update') }}" method="POST" data-ajax="true" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-4">
        <h4 class="card-title mb-4">Chọn giao diện trang chủ (Landing Page)</h4>
        <div class="row g-3">
            <!-- Option: Không sử dụng -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_none" value="none" {{ s('landing_page') == 'none' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_none">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center text-muted">
                        <span class="fs-1"></span>
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Không sử dụng</div>
                        <small class="text-muted">Chuyển hướng đăng nhập</small>
                    </div>
                </label>
            </div>

            <!-- Option: Mặc định -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_default" value="default" {{ s('landing_page') == 'default' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_default">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                     <img src="/assets/images/smm/smm1.png" class="img-fluid rounded theme-img" alt="Homepage 1">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Mặc định</div>
                        <small class="text-muted">Giao diện cơ bản</small>
                    </div>
                </label>
            </div>

            <!-- Option: Hiện đại -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_modern" value="modern" {{ s('landing_page') == 'modern' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_modern">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                         <img src="/assets/images/smm/smm2.png" class="img-fluid rounded theme-img" alt="Modern">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Hiện đại (Modern)</div>
                        <small class="text-muted">Phong cách mới mẻ</small>
                    </div>
                </label>
            </div>

            <!-- Option: Cổ điển -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_classic" value="classic" {{ s('landing_page') == 'classic' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_classic">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                         <img src="/assets/images/smm/smm3.png" class="img-fluid rounded theme-img" alt="Classic">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Cổ điển (Classic)</div>
                        <small class="text-muted">Phong cách truyền thống</small>
                    </div>
                </label>
            </div>

            <!-- Option: Chuyên Nghiệp -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_top" value="top" {{ s('landing_page') == 'top' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_top">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                         <img src="/assets/images/smm/smm4.png" class="img-fluid rounded theme-img" alt="Top Pro">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Chuyên Nghiệp (Top)</div>
                        <small class="text-muted">Giao diện cao cấp</small>
                    </div>
                </label>
            </div>

            <!-- Option: Pro -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_pro" value="pro" {{ s('landing_page') == 'pro' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_pro">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                         <img src="/assets/images/smm/smm5.png" class="img-fluid rounded theme-img" alt="Pro Max">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Pro</div>
                        <small class="text-muted">Đầy đủ tính năng</small>
                    </div>
                </label>
            </div>
              <!-- Option: Pro -->
            <div class="col-6 col-md-4 col-lg-3">
                <input type="radio" class="btn-check theme-selector-input" name="landing_page" id="theme_vip" value="vip" {{ s('landing_page') == 'vip' ? 'checked' : '' }}>
                <label class="card h-100 theme-selector-item" for="theme_vip">
                    <div class="theme-preview-img d-flex align-items-center justify-content-center bg-light">
                         <img src="/assets/images/smm/smm5.png" class="img-fluid rounded theme-img" alt="Pro Max">
                    </div>
                    <div class="theme-info">
                        <div class="theme-name">Vip</div>
                        <small class="text-muted">Đầy đủ tính năng</small>
                    </div>
                </label>
            </div>
        </div>
    </div>


    <hr class="my-4">

    <h4 class="card-title mb-4">Hiệu ứng trang web (Admin & Client)</h4>
    <div class="row g-3">
        @php
            $effects = [
                'none' => ['name' => 'Tắt hiệu ứng', 'class' => 'text-muted', 'icon' => 'ri-prohibited-line'],
                'snow' => ['name' => 'Tuyết rơi (Mùa đông)', 'class' => 'text-primary', 'icon' => 'ri-snowy-line'],
                'tet' => ['name' => 'Hoa đào (Tết)', 'class' => 'text-danger', 'icon' => 'ri-sun-line'],
                'noel' => ['name' => 'Giáng sinh (Noel)', 'class' => 'text-success', 'icon' => 'ri-gift-line'],
            ];
            $currentEffect = $config->theme_effect ?? 'none';
        @endphp

        @foreach($effects as $key => $effect)
        <div class="col-6 col-md-3">
            <input type="radio" class="btn-check theme-selector-input" name="theme_effect" id="effect_{{ $key }}" value="{{ $key }}" {{ $currentEffect == $key ? 'checked' : '' }}>
            <label class="card h-100 theme-selector-item text-center p-3" for="effect_{{ $key }}">
                <div class="mb-2">
                    <i class="{{ $effect['icon'] }} fs-1 {{ $effect['class'] }}"></i>
                </div>
                <div class="theme-name">{{ $effect['name'] }}</div>
            </label>
        </div>
        @endforeach
    </div>



    <hr class="my-4">

    <h4 class="card-title mb-4">Chế độ giao diện (Mode)</h4>
    <div class="row g-3">
        @php
            $currMode = $config->theme_mode ?? 'dark';
        @endphp
        <div class="col-6 col-md-4">
            <input type="radio" class="btn-check theme-selector-input" name="theme_mode" id="mode_light" value="light" {{ $currMode == 'light' ? 'checked' : '' }}>
            <label class="card h-100 theme-selector-item text-center p-3" for="mode_light">
                <div class="mb-2">
                    <i class="ri-sun-line fs-1 text-warning"></i>
                </div>
                <div class="theme-name">Sáng (Light)</div>
            </label>
        </div>
        <div class="col-6 col-md-4">
            <input type="radio" class="btn-check theme-selector-input" name="theme_mode" id="mode_dark" value="dark" {{ $currMode == 'dark' ? 'checked' : '' }}>
            <label class="card h-100 theme-selector-item text-center p-3 bg-dark text-white" for="mode_dark">
                <div class="mb-2">
                    <i class="ri-moon-line fs-1 text-light"></i>
                </div>
                <div class="theme-name">Tối (Dark)</div>
            </label>
        </div>
    </div>

    <hr class="my-4">

    <h4 class="card-title mb-3">Menu di dong</h4>
    <div class="card border shadow-sm">
        <div class="card-body">
            <div class="form-check form-switch">
                <input type="hidden" name="mobile_bottom_nav_status" value="0">
                <input class="form-check-input" type="checkbox" id="mobile_bottom_nav_status" name="mobile_bottom_nav_status" value="1" {{ (int) (s('mobile_bottom_nav_status') ?? 1) === 1 ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="mobile_bottom_nav_status">
                    Bat/tat thanh menu nhanh o cuoi man hinh dien thoai
                </label>
            </div>
            <small class="text-muted d-block mt-2">Tat se an thanh menu duoi cung tren giao dien mobile.</small>
        </div>
    </div>

    <!-- <hr class="my-4">

    <h4 class="card-title mb-4">Tuỳ chỉnh màu sắc hệ thống</h4>
    
    <div class="row g-4">
    
        <div class="col-12 col-lg-6">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="card-title fs-16 mb-0 text-primary"><i class="ri-brush-line me-2"></i>Màu chủ đạo (Primary)</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Màu nền chính (Primary Color)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_primary" value="{{ $config->theme_color_primary ?? '#0d6efd' }}" title="Chọn màu">
                            <input type="text" class="form-control" value="{{ $config->theme_color_primary ?? '#0d6efd' }}" onchange="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mã RGB (Cho hiệu ứng trong suốt)</label>
                        <input type="text" class="form-control" name="theme_color_primary_rgb" value="{{ $config->theme_color_primary_rgb ?? '13, 110, 253' }}" placeholder="VD: 13, 110, 253">
                        <small class="text-muted">Dùng để tạo các màu nền mờ (VD: rgba(13, 110, 253, 0.1))</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Màu chữ trên nền chính</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_primary_text" value="{{ $config->theme_color_primary_text ?? '#ffffff' }}">
                            <input type="text" class="form-control" value="{{ $config->theme_color_primary_text ?? '#ffffff' }}" onchange="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
        <div class="col-12 col-lg-6">
            <div class="card h-100 border shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="card-title fs-16 mb-0 text-info"><i class="ri-links-line me-2"></i>Liên kết (Links)</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Màu đường dẫn (Link Color)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_link" value="{{ $config->theme_color_link ?? '#0d6efd' }}">
                            <input type="text" class="form-control" value="{{ $config->theme_color_link ?? '#0d6efd' }}" onchange="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Màu khi di chuột (Link Hover)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_link_hover" value="{{ $config->theme_color_link_hover ?? '#0a58ca' }}">
                            <input type="text" class="form-control" value="{{ $config->theme_color_link_hover ?? '#0a58ca' }}" onchange="this.previousElementSibling.value = this.value">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-12">
            <div class="card border shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 pb-0">
                    <h5 class="card-title fs-16 mb-0 text-success"><i class="ri-layout-left-line me-2"></i>Menu bên trái (Sidebar)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label">Màu nền Sidebar</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_sidebar" value="{{ $config->theme_color_sidebar ?? '#ffffff' }}">
                                <input type="text" class="form-control" value="{{ $config->theme_color_sidebar ?? '#ffffff' }}" onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>
                       
                        <div class="col-md-4">
                            <label class="form-label">Màu chữ mục menu</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" style="max-width: 50px;" name="theme_color_menu_item" value="{{ $config->theme_color_menu_item ?? '#212529' }}">
                                <input type="text" class="form-control" value="{{ $config->theme_color_menu_item ?? '#212529' }}" onchange="this.previousElementSibling.value = this.value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="mt-5 mb-4">
        <button type="submit" class="btn btn-primary w-100 py-2 fs-16"><i class="ri-save-line me-2"></i>Lưu cài đặt giao diện</button>
    </div>
</form>
