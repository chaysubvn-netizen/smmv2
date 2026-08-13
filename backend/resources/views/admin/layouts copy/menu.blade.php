<!-- Admin Menu: Quản trị viên -->
<li class="side-nav-title">Quản trị viên</li>

<li class="side-nav-item">
    <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
        <i class="ti ti-dashboard"></i>
        <span> Bảng thống kê </span>
    </a>
</li>
@if (env('MAIN_SITE') == request()->getHost())
    <li class="side-nav-title">Services & Platform</li>
<li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarCharts" aria-expanded="false" aria-controls="sidebarCharts" class="side-nav-link collapsed">
                            <span class="menu-icon"><i class="ri-bar-chart-line"></i></span>
                            <span class="menu-text"> Dịch Vụ SMM </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarCharts" style="">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.platforms.index') }}" class="side-nav-link">
                                        <span class="menu-text">Danh Sách Nền Tảng</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.categories.index') }}" class="side-nav-link">
                                        <span class="menu-text">Danh Sách Phân Loại</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.services.index') }}" class="side-nav-link">
                                        <span class="menu-text">Danh Sách Dịch Vụ</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.discounts.index') }}" class="side-nav-link">
                                        <span class="menu-text">Mã Giảm Giá</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.providers.index') }}" class="side-nav-link">
                                        <span class="menu-text">Kết Nối Api</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.services.update-rates') }}" class="side-nav-link">
                                        <span class="menu-text">Cập Nhập Giá</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.data.orders') }}" class="side-nav-link">
                                        <span class="menu-text">Quản Lý Đơn Hàng</span>
                                    </a>
                                </li>
                                
                                @endif
                            </ul>
                        </div>
                    </li>

<!-- Admin Menu: Productions -->

<li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarInvoice" aria-expanded="false" aria-controls="sidebarInvoice" class="side-nav-link collapsed">
                            <span class="menu-icon"><i class="ri-shopping-bag-3-line"></i></span>
                            <span class="menu-text"> Sản Phẩm</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarInvoice" style="">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.products.index') }}" class="side-nav-link">
                                        <span class="menu-text">Quản lý sản phẩm</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.products.categories.index') }}" class="side-nav-link">
                                        <span class="menu-text">Quản lý danh mục</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.products.orders.index') }}" class="side-nav-link">
                                        <span class="menu-text"> Quản lý đơn hàng</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>


<li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarBank" aria-expanded="false" aria-controls="sidebarBank" class="side-nav-link collapsed">
                            <span class="menu-icon"><i class="ri-wallet-fill"></i></span>
                            <span class="menu-text"> Nạp Tiền</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarBank" style="">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.banks.accounts.index') }}" class="side-nav-link">
                                        <span class="menu-text">Tài khoản ngân hàng</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.data.deposits') }}" class="side-nav-link">
                                        <span class="menu-text">Lịch Sử Nạp Tiền</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.config.api-keys.index') }}" class="side-nav-link">
                                        <span class="menu-text"> Cấu Hình Api KEY</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>



<li class="side-nav-item">
    <a href="{{ route('admin.banks.bonus.index') }}" class="side-nav-link">
        <i class="ti ti-gift"></i>
        <span> Quản lý tiền thưởng </span>
    </a>
</li>



<li class="side-nav-item">
    <a href="{{ route('admin.posts.index') }}" class="side-nav-link">
        <i class="ti ti-news"></i>
        <span> Quản lý bài viết </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.affiliate.index') }}" class="side-nav-link">
        <i class="ti ti-users-plus"></i>
        <span> Quản lý giới thiệu </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.tickets.index') }}" class="side-nav-link">
        <i class="ti ti-ticket"></i>
        <span> Quản lý tickets </span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('admin.childpanels.index') }}" class="side-nav-link">
        <i class="ti ti-sitemap"></i>
        <span> Quản lý website </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.fb-tokens.index') }}" class="side-nav-link">
        <i class="ri-facebook-box-fill"></i>
        <span> Quản lý Token FB </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.users.index') }}" class="side-nav-link">
        <i class="ti ti-users"></i>
        <span> Danh sách thành viên </span>
    </a>
</li>




<li class="side-nav-item">
    <a href="{{ route('admin.data.transactions') }}" class="side-nav-link">
        <i class="ti ti-exchange"></i>
        <span> Lịch sử giao dịch </span>
    </a>
</li>
<!-- Admin Menu: WEBSITE CONFIG -->
<li class="side-nav-title">WEBSITE CONFIG</li>
<li class="side-nav-item">
    <a href="{{ route('admin.config.notifications') }}" class="side-nav-link">
        <i class="ti ti-bell-ringing"></i>
        <span> Cấu hình thông báo </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.config.general.index') }}" class="side-nav-link">
        <i class="ti ti-settings"></i>
        <span> Cấu hình Website </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.system.update') }}" class="side-nav-link">
        <i class="ti ti-history"></i>
        <span> Cập nhật hệ thống </span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ route('admin.currencies.index') }}" class="side-nav-link">
        <i class="ti ti-currency-dollar"></i>
        <span> Quản lý tiền tệ </span>
    </a>
</li>
