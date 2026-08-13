<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Cài đặt Website</title>
    @include('components.seo-header')
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" /> <!--end::Fonts-->



    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
</head>
<!--end::Head-->

<!--begin::Body-->

<body id="kt_body" class="auth-bg">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <!--end::Theme mode setup on page load-->

    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">

        <!--begin::Authentication - Sign-up -->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">

                        <!--begin::Form-->
                        <form class="form w-100" novalidate="novalidate" id="form-register" novalidate method="POST"
                            action="{{ route('auth.install.post') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">
                                    Cài đặt Website
                                </h1>
                                <!--end::Title-->

                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">
                                    Nhập đầy đủ dữ liệu để đăng kí
                                </div>
                                <!--end::Subtitle--->
                            </div>
                            <!--begin::Heading-->

                            <!--begin::Separator-->
                            <div class="separator separator-content my-14"><span
                                    class="w-125px text-gray-500 fw-semibold fs-7">Or with email</span></div>
                            <!--end::Separator-->

                            @if (env('MAIN_SITE') != request()->getHost())
                                <div class="fv-row mb-8">
                                    <!--begin::Email-->
                                    <input type="text" placeholder="Nhập API KEY" name="api_key" autocomplete="off"
                                        value="{{ old('api_key') }}" class="form-control bg-transparent" />
                                    <!--end::Email-->
                                </div>
                            @endif

                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="Nhập tài khoản" name="username" autocomplete="off"
                                    value="{{ old('username') }}" class="form-control bg-transparent" />
                                <!--end::Email-->
                            </div>
                            <!--begin::Input group--->
                            <div class="fv-row mb-8">
                                <!--begin::Email-->
                                <input type="text" placeholder="Nhập địa chỉ email" name="email" autocomplete="off"
                                    value="{{ old('email') }}" class="form-control bg-transparent" />
                                <!--end::Email-->
                            </div>

                            <!--begin::Input group-->
                            <div class="fv-row mb-8" data-kt-password-meter="true">
                                <!--begin::Wrapper-->
                                <div class="mb-1">
                                    <!--begin::Input wrapper-->
                                    <div class="position-relative mb-3">
                                        <input class="form-control bg-transparent" type="password"
                                            placeholder="Nhập mật khẩu" name="password" autocomplete="off" />

                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            data-kt-password-meter-control="visibility">
                                            <i class="ki-duotone ki-eye-slash fs-2"></i> <i
                                                class="ki-duotone ki-eye fs-2 d-none"></i> </span>
                                    </div>
                                    <!--end::Input wrapper-->

                                    <!--begin::Meter-->
                                    <div class="d-flex align-items-center mb-3"
                                        data-kt-password-meter-control="highlight">
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                        </div>
                                        <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                    </div>
                                    <!--end::Meter-->
                                </div>
                                <!--end::Wrapper-->

                                <!--begin::Hint-->
                                <div class="text-muted">
                                    Use 8 or more characters with a mix of letters, numbers & symbols.
                                </div>
                                <!--end::Hint-->
                            </div>
                            <!--end::Input group--->

                            <!--end::Input group--->
                            <div class="fv-row mb-8">
                                <!--begin::Repeat Password-->
                                <input type="text" placeholder="Xác nhận mật khẩu" name="password_confirmation"
                                    type="password" autocomplete="off" class="form-control bg-transparent" />
                                <!--end::Repeat Password-->
                            </div>
                            <!--end::Input group--->

                            <!--begin::Accept-->
                            <div class="fv-row mb-8">
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="condition" value="1" />
                                    <span class="form-check-label fw-semibold text-gray-700 fs-base ms-1">
                                        I Accept the <a href="#" class="ms-1 link-primary">Terms</a>
                                    </span>
                                </label>
                            </div>
                            <!--end::Accept-->

                            <!--begin::Submit button-->
                            <div class="d-grid mb-10">
                                <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">

                                    <!--begin::Indicator label-->
                                    <span class="indicator-label">
                                        Cài đặt Website</span>
                                    <!--end::Indicator label-->

                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">
                                        Vui lòng đợi... <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    <!--end::Indicator progress--> </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                        <!--end::Form-->

                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->
            </div>
            <!--end::Body-->

            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background-image: url(/assets/media/misc/auth-bg.png)">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <a href="{{ route('auth.register') }}" class="mb-0 mb-lg-12">
                        <img alt="Logo" src="{{ s('logo') }}" class="h-60px h-lg-75px" />
                    </a>
                    <!--end::Logo-->

                    <!--begin::Image-->
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                        src="/assets/media/misc/auth-screens.png" alt="" />
                    <!--end::Image-->

                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        {{ s('title') }}
                    </h1>
                    <!--end::Title-->

                    <!--begin::Text-->
                    <a class="d-none d-lg-block text-white fs-base text-center">
                        {{ s('description') }}
                </div>
                <!--end::Text-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Aside-->
    </div>
    <!--end::Authentication - Sign-up-->



    </div>
    <!--end::Root-->
    <!--end::Main-->


    <!--begin::Javascript-->
    <script>
        var hostUrl = "/assets/index.html";
    </script>

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
    @if (session('error'))
        <script>
            Swal.fire(
                'Lỗi',
                '{{ session('error') }}',
                'error'
            )
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire(
                'Thành công',
                '{{ session('success') }}',
                'success'
            )
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('#form-register').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Đang xử lý...`).addClass('disabled');
            });
        });
    </script>
</body>
<!--end::Body-->

</html>
