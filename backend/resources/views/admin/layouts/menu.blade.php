<li class="pc-item pc-caption">
    <label>Quản trị viên</label>
    <i class="ph-duotone ph-gauge"></i>
</li>

<li class="pc-item">
    <a href="{{ route('admin.dashboard') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-dashboard"></i>
        </span>
        <span class="pc-mtext">Bảng thống kê</span>
    </a>
</li>

@if (env('MAIN_SITE') == request()->getHost())
<li class="pc-item pc-caption">
    <label>Services & Platform</label>
    <i class="ph-duotone ph-chart-pie"></i>
</li>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ri-bar-chart-line"></i></span>
        <span class="pc-mtext">Dịch Vụ SMM</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a href="{{ route('admin.platforms.index') }}" class="pc-link">Danh Sách Nền Tảng</a></li>
        <li class="pc-item"><a href="{{ route('admin.categories.index') }}" class="pc-link">Danh Sách Phân Loại</a></li>
        <li class="pc-item"><a href="{{ route('admin.services.index') }}" class="pc-link">Danh Sách Dịch Vụ</a></li>
        <li class="pc-item"><a href="{{ route('admin.discounts.index') }}" class="pc-link">Mã Giảm Giá</a></li>
        <li class="pc-item"><a href="{{ route('admin.providers.index') }}" class="pc-link">Kết Nối Api</a></li>
        <li class="pc-item"><a href="{{ route('admin.services.update-rates') }}" class="pc-link">Cập Nhập Giá</a></li>
        <li class="pc-item"><a href="{{ route('admin.data.orders') }}" class="pc-link">Quản Lý Đơn Hàng</a></li>
    </ul>
</li>
@endif

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ri-shopping-bag-3-line"></i></span>
        <span class="pc-mtext">Sản Phẩm</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a href="{{ route('admin.products.index') }}" class="pc-link">Quản lý sản phẩm</a></li>
        <li class="pc-item"><a href="{{ route('admin.products.categories.index') }}" class="pc-link">Quản lý danh mục</a></li>
        <li class="pc-item"><a href="{{ route('admin.products.orders.index') }}" class="pc-link">Quản lý đơn hàng</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ri-wallet-fill"></i></span>
        <span class="pc-mtext">Nạp Tiền</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a href="{{ route('admin.banks.accounts.index') }}" class="pc-link">Tài khoản ngân hàng</a></li>
        <li class="pc-item"><a href="{{ route('admin.data.deposits') }}" class="pc-link">Lịch Sử Nạp Tiền</a></li>
        <li class="pc-item"><a href="{{ route('admin.config.api-keys.index') }}" class="pc-link">Cấu Hình Api KEY</a></li>
    </ul>
</li>

<li class="pc-item">
    <a href="{{ route('admin.banks.bonus.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-gift"></i></span>
        <span class="pc-mtext">Quản lý tiền thưởng</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.posts.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-news"></i></span>
        <span class="pc-mtext">Quản lý bài viết</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.affiliate.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-users-plus"></i></span>
        <span class="pc-mtext">Quản lý giới thiệu</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.tickets.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-ticket"></i></span>
        <span class="pc-mtext">Quản lý tickets</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.childpanels.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-sitemap"></i></span>
        <span class="pc-mtext">Quản lý website</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.fb-tokens.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ri-facebook-box-fill"></i></span>
        <span class="pc-mtext">Quản lý Token FB</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.users.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-users"></i></span>
        <span class="pc-mtext">Danh sách thành viên</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.data.transactions') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-exchange"></i></span>
        <span class="pc-mtext">Lịch sử giao dịch</span>
    </a>
</li>

<li class="pc-item pc-caption">
    <label>WEBSITE CONFIG</label>
    <i class="ph-duotone ph-devices"></i>
</li>

<li class="pc-item">
    <a href="{{ route('admin.config.notifications') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-bell-ringing"></i></span>
        <span class="pc-mtext">Cấu hình thông báo</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.config.general.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-settings"></i></span>
        <span class="pc-mtext">Cấu hình Website</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.config.telegram-link.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-brand-telegram"></i></span>
        <span class="pc-mtext">Telegram Link & 2FA</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.system.update') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-history"></i></span>
        <span class="pc-mtext">Cập nhật hệ thống</span>
    </a>
</li>

<li class="pc-item">
    <a href="{{ route('admin.currencies.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-currency-dollar"></i></span>
        <span class="pc-mtext">Quản lý tiền tệ</span>
    </a>
</li>
