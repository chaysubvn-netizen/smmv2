<nav class="client-mobile-bottom-nav d-lg-none" aria-label="Mobile navigation">
    <a href="{{ route('statistics') }}" class="mobile-nav-item {{ request()->routeIs('statistics') ? 'active' : '' }}">
        <i class="ki-duotone ki-home fs-2"><span class="path1"></span><span class="path2"></span></i>
        <span>{{ __('Trang chủ') }}</span>
    </a>
    <a href="{{ route('addfuns') }}" class="mobile-nav-item {{ request()->routeIs('addfuns') ? 'active' : '' }}">
        <i class="ki-duotone ki-two-credit-cart fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        <span>{{ __('Nạp tiền') }}</span>
    </a>
    <a href="{{ route('new') }}" class="mobile-nav-item {{ request()->routeIs('new') ? 'active' : '' }}">
        <i class="ki-duotone ki-purchase fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        <span>{{ __('Đặt Hàng') }}</span>
    </a>
    <a href="{{ route('orders') }}" class="mobile-nav-item {{ request()->routeIs('orders') ? 'active' : '' }}">
        <i class="ki-duotone ki-cheque fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        <span>{{ __('Đơn hàng') }}</span>
    </a>
    <a href="{{ route('settings') }}" class="mobile-nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
        <i class="ki-duotone ki-profile-circle fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
        <span>{{ __('Tài khoản') }}</span>
    </a>
</nav>
