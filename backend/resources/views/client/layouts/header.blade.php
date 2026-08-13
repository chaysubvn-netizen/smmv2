<div id="kt_header" class="header ">
    <!--begin::Container-->
    <div class="container-fluid d-flex flex-stack">
        <!--begin::Brand-->
        <div class="d-flex align-items-center me-5">
            <!--begin::Aside toggle-->
            <div class="d-lg-none btn btn-icon btn-active-color-white w-30px h-30px ms-n2 me-3" id="kt_aside_toggle" aria-label="Toggle navigation menu">
                <i class="ki-duotone ki-abstract-14 fs-2" aria-hidden="true"><span class="path1"></span><span class="path2"></span></i>
            </div>
            <!--end::Aside  toggle-->

            <!--begin::Logo-->
            <a href="{{ route('new') }}">
                <img alt="Logo" src="{{ s('logo') }}" class="h-25px h-lg-30px" />
            </a>
            <!--end::Logo-->

            <!--begin::Nav-->

            <!--end::Nav-->
        </div>
        <!--end::Brand-->


        <!--begin::Topbar-->
        <div class="d-flex align-items-center flex-shrink-0">

            @if (Auth::check())
                <!--begin::Currency-->
                <div class="dropdown d-inline-block ms-1">
                    <button
                        class="btn btn-sm btn-currency-pill rounded-pill d-flex align-items-center gap-2 py-2 px-4"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Change currency">
                        <span class="fw-bold fs-7">{{ Auth::user()->currency }}</span>
                        <i class="ki-duotone ki-down fs-7" aria-hidden="true"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end w-250px mt-2 p-0 shadow-sm border-0 radius-12 overflow-hidden">
                        <div class="py-3 px-4 bg-light-primary border-bottom">
                            <h6 class="text-primary fw-bold mb-0 fs-7">
                                {{ __('Chọn tiền tệ') }}
                            </h6>
                        </div>

                        <div class="max-h-300px overflow-y-auto scroll-sm p-2">
                            @foreach ($currencies as $curr)
                                <div class="menu-item px-2 py-1">
                                    <label class="form-check form-check-custom form-check-solid d-flex align-items-center justify-content-between p-3 rounded-3 cursor-pointer hover-bg-light"
                                        onclick="changeCurrency('{{ $curr->code }}')">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="symbol symbol-30px">
                                                <div class="symbol-label fs-5 fw-bold bg-light-info text-info rounded-circle">
                                                    {{ $curr->symbol }}
                                                </div>
                                            </div>
                                            <span class="text-gray-800 fw-bold fs-7">{{ $curr->code }}</span>
                                        </div>
                                        <input class="form-check-input w-15px h-15px" type="radio" name="currency_radio"
                                            {{ Auth::user()->currency == $curr->code ? 'checked' : '' }}>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!--end::Currency-->

                <!--begin::Quick links-->
                <div class="d-flex align-items-center ms-1">
                    <!--begin::Menu wrapper-->
                    <div class="btn btn-icon btn-color-white d-flex justify-content-center align-items-center bg-hover-white bg-hover-opacity-10 w-30px h-30px h-40px w-40px"
                        data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" aria-label="Change language">
                        <img class="w-20px h-20px rounded-1"
                            src="{{ Auth::user()->language == 'vi' ? '/assets/images/flags/vietnam.png' : '/assets/images/flags/flag1.png' }}"
                            alt="Language flag" />
                    </div>

                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                        data-kt-menu="true">
                        <div class="menu-item px-3 my-0">
                            <a href="javascript:void(0)" class="menu-link px-3 py-2" data-kt-element="mode"
                                data-kt-value="{{ Auth::user()->language == 'en' ? 'vi' : 'en' }}"
                                onclick="changeLanguage('{{ Auth::user()->language == 'en' ? 'vi' : 'en' }}')">
                                <span class="menu-icon" data-kt-element="icon">
                                    <img class="w-20px h-20px rounded-1"
                                        src="{{ Auth::user()->language == 'en' ? '/assets/images/flags/vietnam.png' : '/assets/images/flags/flag1.png' }}"
                                        alt="" />
                                </span>
                                <span class="menu-title">
                                    {{ Auth::user()->language == 'en' ? __('Tiếng Việt') : __('English') }}
                                </span>
                            </a>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Quick links-->
            @endif

            <!--begin::Theme mode-->
            <div class="d-flex align-items-center ms-1">

                
                <!--begin::Menu toggle-->

                <!--end::Menu-->

            </div>
            <!--end::Theme mode-->

            <!--begin::User-->
            @if (Auth::check())
            <div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle">
                <!--begin::User info-->
                <div class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3"
                    data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">

                    <!--begin::Name-->
                    <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4">
                        <span class="text-muted fs-8 fw-semibold lh-1 mb-1">{{ Auth::user()->level }}</span>
                        <span class="text-white fs-8 fw-bold lh-1">{{ Auth::user()->username }}</span>
                    </div>
                    <!--end::Name-->

                    <!--begin::Symbol-->
                    <div class="symbol symbol-30px symbol-md-40px">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?: Auth::user()->username) }}&background=4680ff&color=fff&bold=true" alt="image" />
                    </div>
                    <!--end::Symbol-->
                </div>
                <!--end::User info-->


                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img alt="Logo" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?: Auth::user()->username) }}&background=4680ff&color=fff&bold=true&size=128" />
                            </div>
                            <!--end::Avatar-->

                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    {{ Auth::user()->username }}
                                </div>

                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                                    {{ Auth::user()->email }} </a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->

                    @if (Auth::check() && Auth::user()->role === 'admin' && Auth::id() == s('user_id'))
                        <div class="menu-item px-5">
                            <a href="{{ route('admin.dashboard') }}" class="menu-link px-5">
                                {{ __('Quản trị viên') }}
                            </a>
                        </div>
                    @endif

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ route('settings') }}" class="menu-link px-5">
                            {{ __('Cấu hình') }}
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu separator-->
                    <!--end::Menu separator-->


                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title position-relative">
                                {{ __('Ngôn ngữ') }}

                                <span
                                    class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                                    {{ strtoupper(Auth::user()->language == 'vi' ? 'VN' : Auth::user()->language) }} <img class="w-15px h-15px rounded-1 ms-2"
                                        src="{{ Auth::user()->language == 'vi' ? '/assets/images/flags/vietnam.png' : '/assets/images/flags/flag1.png' }}"
                                        alt="" />
                                </span>
                            </span>
                        </a>

                        <!--begin::Menu sub-->
                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="javascript:void(0)"
                                    onclick="changeLanguage('{{ Auth::user()->language == 'vi' ? 'en' : 'vi' }}')"
                                    class="menu-link d-flex px-5 active">
                                    <span class="symbol symbol-20px me-4">
                                        <img class="rounded-1"
                                            src="{{ Auth::user()->language == 'vi' ? '/assets/images/flags/flag1.png' : '/assets/images/flags/vietnam.png' }}"
                                            alt="" />
                                    </span>
                                    {{ Auth::user()->language == 'vi' ? __('English') : __('Tiếng Việt') }}
                                </a>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu sub-->
                    </div>
                    <!--end::Menu item-->

                    <div class="separator my-2"></div>

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ route('auth.logout') }}" class="menu-link px-5">
                            Đăng xuất
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
            </div>
            @else
            <div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle">
                <!--begin::User info-->
                <div class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3"
                    data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">

                    <!--begin::Name-->
                    <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4">
                        <span class="text-white fs-8 fw-bold lh-1">Chưa đăng nhập</span>
                    </div>
                    <!--end::Name-->

                    <!--begin::Symbol-->
                    <div class="symbol symbol-30px symbol-md-40px">
                        <img src="https://ui-avatars.com/api/?name=Guest&background=6c757d&color=fff" alt="image" />
                    </div>
                    <!--end::Symbol-->
                </div>
                <!--end::User info-->


                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true">
                    <!--end::Menu item-->

                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->


                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="{{ route('auth.login') }}" class="menu-link px-5">
                            Đăng nhập
                        </a>
                    </div>
                    <div class="menu-item px-5">
                        <a href="{{ route('auth.register') }}" class="menu-link px-5">
                            Đăng ký
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
            </div>
            @endif
            <!--end::User -->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
