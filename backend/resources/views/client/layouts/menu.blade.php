<div id="kt_aside" class="aside card" data-kt-drawer="true" data-kt-drawer-name="aside"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_toggle">
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid px-4">
        <!--begin::Aside Menu-->

        <div class="hover-scroll-overlay-y mh-100 my-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="{default: '#kt_aside_footer', lg: '#kt_header, #kt_aside_footer'}"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu" data-kt-scroll-offset="{default: '5px', lg: '75px'}">
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_aside_menu"
                data-kt-menu="true">
                @foreach ($ListMenuItems = getListMenuItems(); as $key => $menu)
                    @if ($menu['breadcrumb'] == true)
                        <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7 mb-3">{{ $menu['name'] }}</span></div>
                    @else
                        @php
                            $isActive = request()->url() == $menu['route'] || (isset($menu['active_routes']) && in_array(request()->route()->getName(), $menu['active_routes']));
                        @endphp
                        <div class="menu-item"><!--begin:Menu link-->
                            <a class="menu-link {{ $isActive ? 'active' : '' }}"
                                href="{{ $menu['route'] }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone {{ $menu['icon'] }} fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                    </i>
                                </span>
                                <span class="menu-title">
                                    {{ $menu['name'] }}
                                </span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <!--end::Aside menu-->

    <!--begin::Footer-->
    <div class="aside-footer flex-column-auto pt-4 pb-5 px-4" id="kt_aside_footer">
        @if (Auth::check())
            <div class="d-flex flex-column gap-2">
                <!-- Balance Box -->
                <a href="{{ route('addfuns') }}"
                    class="d-flex align-items-center justify-content-center py-2 px-3 rounded-3 border border-dashed border-gray-300 bg-hover-light-primary text-decoration-none balance-pill"
                    aria-label="Your balance: {{ convert_currency(Auth::user()->balance, Auth::user()->currency, false) }}. Click to add funds.">
                    <div class="d-flex align-items-center gap-3">
                        <i class="ki-duotone ki-wallet fs-2 text-gray-500" aria-hidden="true">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                class="path4"></span>
                        </i>
                        <span class="text-primary fw-bold fs-6">
                            {{ convert_currency(Auth::user()->balance, Auth::user()->currency, false) }}
                        </span>
                    </div>
                </a>

                <!-- User Info Box -->
                <div class="aside-user-card d-flex align-items-center justify-content-between p-2 px-3 rounded-3 shadow-sm">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <!-- Avatar -->
                        <div class="symbol symbol-30px">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?: Auth::user()->username) }}&background=4680ff&color=fff&bold=true" alt="User Avatar" class="rounded-2" />
                        </div>
                        <!-- Info -->
                        <div class="d-flex flex-column overflow-hidden">
                            <span class="text-gray-900 fw-bold fs-8 text-truncate">  {{ Auth::user()->email }}</span>
                            <span class="text-muted fs-9" style="font-size: 0.7rem">@ {{ Auth::user()->username }}</span>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="ms-1">
                        <button class="btn btn-icon btn-sm btn-active-light-primary w-25px h-25px"
                            data-kt-menu-trigger="click" data-kt-menu-placement="top-end" aria-label="Account options">
                            <i class="ki-duotone ki-dots-horizontal fs-3" aria-hidden="true">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                        </button>
                        <!-- User Menu -->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-3 fs-7 w-150px"
                            data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="{{ route('settings') }}" class="menu-link px-4">
                                    {{ __('Cấu hình') }}
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="{{ route('auth.logout') }}" class="menu-link px-4 text-danger">
                                    {{ __('Đăng xuất') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('auth.login') }}" class="btn btn-primary btn-sm w-100 rounded-3">{{ __('Đăng nhập') }}</a>
                <a href="{{ route('auth.register') }}"
                    class="btn btn-light-primary btn-sm w-100 rounded-3">{{ __('Đăng ký') }}</a>
            </div>
        @endif
    </div>
    <!--end::Footer-->
</div>
