<!DOCTYPE html>
<html lang="vn" class="swal2-shown swal2-height-auto" data-bs-theme="dark">
<!--begin::Head-->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<head>
    <meta charset="utf-8" />
    <title>Đăng kí tài khoản</title>
    @include('components.seo-header')
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->



    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!--end::Global Stylesheets Bundle-->
    <script>
    // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
    if (window.top != window.self) {
        window.top.location.replace(window.self.location.href);
    }
    </script>
    <style>
        #fui-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .fui-toast {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 10px;
            padding: 12px 16px;
            min-width: 300px;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 12px;
            border-left: 5px solid #2ecc71;
            animation: slideInTop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.35), fadeOut 0.5s 2.5s forwards;
            position: relative;
            overflow: hidden;
            text-align: left;
        }

        .fui-toast.fui-toast-error { border-left-color: #e74c3c; }
        .fui-toast.fui-toast-warning { border-left-color: #f1c40f; }
        .fui-toast.fui-toast-info { border-left-color: #3498db; }

        .fui-toast-icon {
            font-size: 24px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fui-toast-success .fui-toast-icon { color: #2ecc71; }
        .fui-toast-error .fui-toast-icon { color: #e74c3c; }
        .fui-toast-warning .fui-toast-icon { color: #f1c40f; }
        .fui-toast-info .fui-toast-icon { color: #3498db; }

        .fui-toast-content { flex: 1; }
        .fui-toast-msg {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
        }

        @keyframes slideInTop {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }

        .fui-toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: #2ecc71;
            animation: progress 3s linear forwards;
        }

        .fui-toast-error .fui-toast-progress { background: #e74c3c; }
        .fui-toast-warning .fui-toast-progress { background: #f1c40f; }
        .fui-toast-info .fui-toast-progress { background: #3498db; }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<!--end::Head-->

<!--begin::Body-->

<body id="kt_body" class="auth-bg">
    <div id="fui-toast"></div>
    <!--begin::Theme mode setup on page load-->
    
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
                            action="{{ route('auth.register.post') }}">
                            @csrf
                            <!--begin::Heading-->
                            <div class="text-center mb-11">
                                <!--begin::Title-->
                                <h1 class="text-gray-900 fw-bolder mb-3">
                                    Đăng kí tài khoản
                                </h1>
                                <!--end::Title-->

                                <!--begin::Subtitle-->
                                <div class="text-gray-500 fw-semibold fs-6">
                                    Nhập đầy đủ dữ liệu để đăng kí
                                </div>
                                <!--end::Subtitle--->
                            </div>
                            <!--begin::Heading-->

                            <!--begin::Login options-->
                            @if(function_exists('s') && s('google_login_status') === 'active')
                            <div class="row g-3 mb-9">
                                <!--begin::Col-->
                                <div class="col-12">
                                    <!--begin::Google link--->
                                    <a href="{{ route('auth.google') }}"
                                        class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                                        <img alt="Logo" src="/assets/media/svg/brand-logos/google-icon.svg"
                                            class="h-15px me-3" />
                                        Đăng ký bằng Google
                                    </a>
                                    <!--end::Google link--->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Login options-->

                            <!--begin::Separator-->
                            <div class="separator separator-content my-14"><span
                                    class="w-125px text-gray-500 fw-semibold fs-7">Or with email</span></div>
                            <!--end::Separator-->
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
                                        Đăng kí tài khoản</span>
                                    <!--end::Indicator label-->

                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">
                                        Vui lòng đợi... <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->

                            <!--begin::Sign up-->
                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Đã có tài khoản?

                                <a href="{{ route('auth.login') }}" class="link-primary fw-semibold">
                                    Đăng nhập
                                </a>
                            </div>
                            <!--end::Sign up-->
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
                style="background-image: url(https://cdn.whoispanel.com/1/media/misc/auth-bg.png)">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <!--begin::Logo-->
                    <a href="{{ route('auth.login') }}" class="mb-0 mb-lg-12">
                        <img alt="Logo" src="{{ s('logo') }}" class="h-60px h-lg-75px" />
                    </a>
                    <!--end::Logo-->
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                        src="https://cdn.whoispanel.com/landing/11/imgs/q48garjh5m68huzt.png" alt="" />
                    <!--begin::Title-->
                    <h1 class="d-none d-lg-block text-white fs-2qx fw-bolder text-center mb-7">
                        {{ s('title') }}
                    </h1>
                    <!--end::Title-->

                    <!--begin::Text-->
                    <div class="d-none d-lg-block text-white fs-base text-center">
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
    var hostUrl = "";
    </script>

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
    @if (session('error'))
    <script>
    $(document).ready(function() {
        notify('{{ session("error") }}', 'error');
    });
    </script>
    @endif

    @if (session('success'))
    <script>
    $(document).ready(function() {
        notify('{{ session("success") }}', 'success');
    });
    </script>
    @endif

    <script>
    function fuiToast(message, type = "success") {
        const toastContainer = $("#fui-toast");
        if (!toastContainer.length) return;

        let icon = "fa-check-circle";
        if (type === "error") icon = "fa-times-circle";
        if (type === "warning") icon = "fa-exclamation-circle";
        if (type === "info") icon = "fa-info-circle";

        const toastId = "toast-" + Date.now();
        const html = `
            <div id="${toastId}" class="fui-toast fui-toast-${type}">
                <div class="fui-toast-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="fui-toast-content">
                    <p class="fui-toast-msg">${message}</p>
                </div>
                <div class="fui-toast-progress"></div>
            </div>
        `;

        toastContainer.prepend(html);

        // Auto remove
        setTimeout(() => {
            $(`#${toastId}`).remove();
        }, 3500);
    }

    function notify(message, type = "success") {
        fuiToast(message, type);
    }
    </script>

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