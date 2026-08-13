<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ s('theme_mode') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield(__('title')) | {{ s('title') }}</title>
    @include('components.seo-header', ['title' => false])


    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/assets1/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/assets1/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Vendor Stylesheets-->
    <link href="/assets1/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!--end::Vendor Stylesheets-->

    <!-- Icons - Defer for performance -->
    <script defer src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <script lang="text/javascript">
        (function(_0x4b2c, _0x1a2d) {
            var _0x3e1f = atob;
            var _0x5f9a = JSON.parse;
            var _0x2d8e = function(_0x5c42ab) {
                return _0x5f9a(_0x3e1f(_0x5c42ab));
            };

            // Define global variables as non-enumerable to hide them from F12 console property listings
            Object.defineProperty(window, 'bvq_config', {
                value: _0x2d8e(_0x4b2c),
                enumerable: false,
                writable: false,
                configurable: false
            });

            Object.defineProperty(window, 'translations', {
                value: _0x2d8e(_0x1a2d),
                enumerable: false,
                writable: false,
                configurable: false
            });
        })(
            '{{ base64_encode(json_encode([
                'locale' => app()->getLocale(),
                'timezone' => config('app.timezone'),
                'currency' => json_currency(),
                'currencies' => collect($currencies)->mapWithKeys(fn($curr) => [strtoupper($curr->code) => json_currency($curr->code)])->toArray(),
                'api_key' => Auth::check() ? Auth::user()->api_key : '',
                'csrf_token' => csrf_token(),
                'base_url' => url('/'),
                'api_url' => url('/api'),
                'current_url' => url()->current(),
            ])) }}',
            '{{ base64_encode(json_encode([
                "Có bảo hành" => __('Có bảo hành'),
                "Không bảo hành" => __('Không bảo hành'),
                "Có huỷ" => __('Có huỷ'),
                "Không huỷ" => __('Không huỷ'),
                "Không có dữ liệu" => __('Không có dữ liệu'),
                "Bảo hành" => __('Bảo hành'),
                "ngày" => __('ngày'),
            ])) }}'
        );
    </script>


<script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Professionalservice",
            "@id": "https://minsmm.net",
            "url": "https://minsmm.net",
            "priceRange": "1$-5000$",
            "email": "mailto:quyetcoder2k3@gmail.com",
            "founder": "Quyetoder2k3",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Hà Nội",
                "addressCountry": "Việt Nam",
                "addressRegion": "Hà Nội",
                "postalCode": "10000",
                "streetAddress": "Tuy Lai Xã Phúc Sơn, Huyện Mỹ Đức, Hà Nội, Vietnam"
            },
            "description": "MINSMM.NET | #1 SMM Panel | Cheapest & Fastest | Social Media Marketing",
            "name": "minsmm",
            "telephone": "0395046244",
            "openingHoursSpecification": [{
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": [
                        "Monday",
                        "Tuesday",
                        "Wednesday",
                        "Thursday",
                        "Friday"
                    ],
                    "opens": "08:00",
                    "closes": "18:00"
                },
                {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": [
                        "Saturday"
                    ],
                    "opens": "8:00",
                    "closes": "11:00"
                }
            ],
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "10.7714201",
                "longitude": "106.6529533"
            },
            "potentialAction": {
                "@type": "ReserveAction",
                "target": {
                    "@type": "EntryPoint",
                    "urlTemplate": "https://minsmm.net",
                    "inLanguage": "en",
                    "actionPlatform": [
                        "http://schema.org/DesktopWebPlatform",
                        "http://schema.org/IOSPlatform",
                        "http://schema.org/AndroidPlatform"
                    ]
                },
                "result": {
                    "@type": "Reservation",
                    "name": "minsmm"
                }
            },

            "sameAs": [
                "https://www.facebook.com/quyetcoder2k3",
            ]
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Person",
            "name": "Bùi Văn Quyết",
            "jobTitle": "CEO",
            "image": "",
            "worksFor": "minsmm",
            "url": "https://minsmm.net",
            "sameAs": [
                "https://www.facebook.com.com/quyetcoder2k3/",
            ],
            "AlumniOf": [
                "Aprotrain Aptech",
            ],
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Ha Noi",
                "addressRegion": "vietnam"
            }
        }
    </script>

    @yield('css')
    <style>
.card-service {
  background: auto;
  border-radius: 12px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
  overflow: hidden;
  border: 1px solid #e5e9f2;
  padding: 18px;
}

.card-header {
  border-left: 4px solid #17a2b8;
  padding-left: 12px;
  margin-bottom: 16px;
}

.section-title {
  color: auto;
  font-weight: 600;
  margin-bottom: 4px;
}

.service-name {
  color: auto;
  font-size: 15px;
}

.card-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-row {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.info-box {
  display: flex;
  align-items: center;
  gap: 12px;
  background: auto;
  padding: 10px 12px;
  border-radius: 10px;
  flex: 1;
  min-width: 180px;
  border: 1px solid #eaeaea;
  transition: 0.2s;
}

.info-box:hover {
  background: auto;
  transform: translateY(-2px);
}

.icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  color: #f0f2f5;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 16px;
}

.bg-primary { background-color: #0d6efd !important; }
.bg-danger  { background-color: #dc3545 !important; }
.bg-success { background-color: #28a745 !important; }
.text-cyan  { color: #17a2b8; }

.label {
  font-size: 13px;
  color: #6c757d;
}

.value {
  font-weight: 600;
  color: auto;
}

.desc-box {
  background: auto;
  border-left: 4px solid #00bcd4;
  padding: 12px 16px;
  border-radius: 8px;
}

@media (max-width: 768px) {
  .info-row {
    flex-direction: column;
  }
}

 </style>


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
        }

        .fui-toast.fui-toast-error {
            border-left-color: #e74c3c;
        }

        .fui-toast.fui-toast-warning {
            border-left-color: #f1c40f;
        }

        .fui-toast.fui-toast-info {
            border-left-color: #3498db;
        }

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

        .fui-toast-content {
            flex: 1;
        }

        .fui-toast-msg {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
        }

        @keyframes slideInTop {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        /* Progress Bar */
        .fui-toast::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: rgba(0,0,0,0.05);
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


    <style>
        .radius-12 { border-radius: 12px !important; }
        .hover-bg-light:hover { background-color: var(--bs-light) !important; }
        .scroll-sm::-webkit-scrollbar { width: 4px; }
        .scroll-sm::-webkit-scrollbar-track { background: transparent; }
        .scroll-sm::-webkit-scrollbar-thumb { background: #e4e6ef; border-radius: 10px; }

        /* Theme-adaptive Currency Button */
        .btn-currency-pill {
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        [data-bs-theme="light"] .btn-currency-pill {
            background-color: #f1f1f4;
            color: #3f4254;
            border-color: #e4e6ef;
        }
        /* Sidebar / Aside Refinements */
        .aside-dark .menu-item .menu-link {
            padding: 0.65rem 1rem;
            transition: color 0.2s ease;
        }
        .aside-dark .menu-item .menu-link .menu-title {
            font-size: 0.95rem;
            font-weight: 500;
        }
        .aside-dark .menu-item .menu-link.active {
            background-color: #2b2b40 !important;
            border-radius: 0.475rem;
        }
        .aside-dark .menu-item .menu-link.active .menu-title {
            color: #ffffff !important;
            font-weight: 600;
        }
        .aside-dark .menu-item .menu-link.active .menu-icon i,
        .aside-dark .menu-item .menu-link:hover .menu-icon i {
            color: var(--bs-primary) !important;
        }
        .aside-dark .menu-section {
            color: #494974 !important;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
            font-weight: 700;
            font-size: 0.7rem !important;
            padding-top: 1.5rem !important;
        }
        .aside-logo {
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .aside-toggle {
            transition: transform 0.3s ease;
        }
        body.aside-minimize .aside-toggle i {
            transform: rotate(180deg);
        }
        .aside-toggle i {
            color: #494974 !important;
            font-size: 1.5rem !important;
        }
        .aside-toggle:hover i {
            color: var(--bs-primary) !important;
        }
        [data-bs-theme="dark"] .btn-currency-pill {
            background-color: #1e1e2d;
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .btn-currency-pill:hover {
            background-color: var(--bs-primary-light) !important;
            color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }

        /* Sidebar Footer UI Refresh */
        .aside-user-card {
            background-color: #1e1e2e;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        [data-bs-theme="light"] .aside-user-card {
            background-color: #f5f8fa;
            border-color: #eff2f5;
        }
        .aside-user-card:hover {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .balance-pill {
            background-color: rgba(0, 158, 247, 0.05);
            transition: all 0.3s ease;
        }
        .balance-pill:hover {
            background-color: rgba(0, 158, 247, 0.1) !important;
            border-color: var(--bs-primary) !important;
            transform: translateY(-2px);
        }

        /* API Documentation Styles (Metronic Refinement) */
        .api-panel {
            background: #ffffff;
            border-radius: 0.625rem;
            border: 1px solid #eff2f5;
        }
        [data-bs-theme="dark"] .api-panel {
            background: #1e1e2d;
            border-color: #323248;
        }
        .code-panel {
            background: #151521;
            border-radius: 0.625rem;
            color: #cdcdde;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            border: 1px solid #1e1e2d;
        }
        .text-code-muted { color: #565674; }
        .text-code-keyword { color: #b572f3; }
        .text-code-string { color: #50cd89; }
        .text-code-variable { color: #f1416c; }
        
        
 
.loader-wrapper {
    position: fixed;
    inset: 0;
    background: auto; /* nền giống ảnh */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.dot-spinner {
    position: relative;
    width: 28px;     /* giảm từ 50px */
    height: 28px;
    animation: spin 1s linear infinite;
}

.dot {
    position: absolute;
    width: 5px;      /* giảm từ 8px */
    height: 5px;
    background: #7c4dff;
    border-radius: 50%;
}

/* 4 chấm gần nhau hơn */
.dot:nth-child(1) { top: 0; left: 50%; transform: translateX(-50%); }
.dot:nth-child(2) { right: 0; top: 50%; transform: translateY(-50%); }
.dot:nth-child(3) { bottom: 0; left: 50%; transform: translateX(-50%); }
.dot:nth-child(4) { left: 0; top: 50%; transform: translateY(-50%); }

@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}

.client-mobile-bottom-nav {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
    box-shadow: 0 -6px 24px rgba(0, 0, 0, 0.12);
    padding: 6px 8px calc(6px + env(safe-area-inset-bottom));
}

.mobile-nav-item {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: var(--bs-gray-600);
    text-decoration: none;
    border-radius: 12px;
    padding: 8px 4px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
}

.mobile-nav-item i {
    color: inherit;
}

.mobile-nav-item.active {
    background: var(--bs-secondary-bg) !important;
    color: var(--bs-emphasis-color) !important;
    border: 1px solid var(--bs-border-color);
}

@media (min-width: 992px) {
    .client-mobile-bottom-nav {
        display: none !important;
    }
}

@media (max-width: 991.98px) {
    body.mobile-nav-enabled #kt_content {
        padding-bottom: 90px;
    }
}
    </style>

    <style>
        /* Bold & Beautiful Icon Styles */
        .aside-menu .menu-item .menu-link .menu-icon {
            background-color: #212130;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            transition: all 0.2s ease;
        }
        
        .aside-menu .menu-item .menu-link .menu-icon i {
            color: #ffffff !important;
            font-size: 1.25rem !important;
            font-weight: 800 !important;
        }

        .aside-menu .menu-item .menu-link.active .menu-icon {
            background-color: #009ef7;
            box-shadow: 0px 4px 10px rgba(0, 158, 247, 0.4);
        }
        
        .aside-menu .menu-item .menu-link.active .menu-title {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        .aside-menu .menu-item .menu-link:hover .menu-icon {
            background-color: #2b2b40;
        }
        
        .aside-menu .menu-item .menu-link:hover .menu-title {
            color: #ffffff !important;
        }

        .menu-heading {
            color: #565674 !important;
            letter-spacing: 0.05rem;
        }
        
        /* Global Notification Modal */
        .global-notification-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .global-notification-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .global-notification-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .global-notification-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
            transform: translateY(-20px);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .global-notification-modal.show .global-notification-wrapper {
            transform: translateY(0);
        }

        .global-notification-container {
            background: var(--bs-body-bg);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
        }

        .global-notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .global-notification-header-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .global-notification-header-icon-img {
            width: 32px;
            height: 32px;
        }

        .global-notification-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--bs-text-dark);
        }

        .global-notification-close {
            background: transparent;
            border: none;
            color: var(--bs-text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: color 0.2s;
        }

        .global-notification-close:hover {
            color: var(--bs-danger);
        }

        .global-notification-body {
            padding: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .global-notification-prose {
            font-size: 0.95rem;
            color: var(--bs-text-gray-700);
            line-height: 1.6;
        }

        .global-notification-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 15px 20px;
            background-color: var(--bs-light);
            border-top: 1px solid var(--bs-border-color);
        }
    </style>
    {!! s('script_header') !!}
</head>

@php
    $mobileBottomNavEnabled = s('mobile_bottom_nav_status');
    $mobileBottomNavEnabled = is_null($mobileBottomNavEnabled) ? 1 : (int) $mobileBottomNavEnabled;
@endphp
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed {{ $mobileBottomNavEnabled === 1 ? 'mobile-nav-enabled' : '' }}">
    <div id="fui-toast"></div>
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "{{ s('theme_mode') ?? 'dark' }}";
        if (localStorage.getItem("data-bs-theme") !== defaultThemeMode) {
            localStorage.setItem("data-bs-theme", defaultThemeMode);
        }
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

    {!! s("script_body") !!}
    
    <!--begin::Main-->
    <div class="loader-wrapper" role="status" aria-label="Đang tải dữ liệu...">
        <div class="dot-spinner">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Aside-->
            @include('client.layouts.menu')
            <!--end::Aside-->

            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                @include('client.layouts.header')
                <!--end::Header-->

                <!--begin::Content-->
               <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="container-fluid mt-4" id="kt_content_container">
                        @yield('content')
                    </div>
                </div>
                <!--end::Content-->

                <!--begin::Footer-->
                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex flex-column flex-md-row flex-stack">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-semibold me-1">2025&copy;</span>
                            <a href="https://{{ request()->getHost() }}" target="_blank" class="text-gray-800 text-hover-primary">{{ request()->getHost() }}</a>
                        </div>
                        <!--end::Copyright-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    @if ($mobileBottomNavEnabled === 1)
        @include('client.layouts.mobile-bottom-nav')
    @endif

    <!-- Global Notification Modal -->
    <div class="global-notification-modal" id="notificationModal" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="global-notification-overlay" id="notificationModalOverlay" aria-hidden="true"></div>
        <div class="global-notification-wrapper">
            <div class="global-notification-container">
                <div class="global-notification-header">
                    <div class="global-notification-header-inner">
                        <span class="global-notification-header-icon" aria-hidden="true">
                            <img src="https://subx.vn/frontend/package/Notification.gif" alt="" class="global-notification-header-icon-img">
                        </span>
                        <h2 class="global-notification-title" id="notificationModalLabel">{{ __('Thông báo hệ thống') }}</h2>
                    </div>
                    <button type="button" class="global-notification-close" id="notificationModalCloseBtn" aria-label="Đóng">
                        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                </div>
                <div class="global-notification-body">
                    <div class="global-notification-prose">
                        {!! s('sys_notification') ?: '<p>Nền Tảng Tăng Tương Tác Mạng Xã Hội Nhanh Chóng - Chất Lượng.<br>Hỗ trợ tăng <strong>Follow, Like, View, Comment</strong> cho các nền tảng như Facebook, Instagram, TikTok...<br>Giá rẻ – Tốc độ nhanh – Hệ thống tự động 24/7.</p>' !!}
                    </div>
                </div>
                <div class="global-notification-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="globalCloseForOneHourBtn">
                        <i class="ki-duotone ki-time fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>{{ __('Đóng trong 1h') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-4" id="notificationModalUnderstoodBtn">
                        <i class="ki-duotone ki-check fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>{{ __('Đã hiểu') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById('notificationModal');
            if(!modal) return;
            
            var closeBtn = document.getElementById('notificationModalCloseBtn');
            var understoodBtn = document.getElementById('notificationModalUnderstoodBtn');
            var closeOneHourBtn = document.getElementById('globalCloseForOneHourBtn');
            var overlay = document.getElementById('notificationModalOverlay');
            
            // Check if user has closed it within the last hour or "understood" (24h)
            var hideUntil = localStorage.getItem('hideNotificationUntil');
            var now = new Date().getTime();
            
            if(!hideUntil || now > parseInt(hideUntil)) {
                // Show modal after a small delay
                setTimeout(function() {
                    modal.classList.add('show');
                }, 500);
            }
            
            function closeModal() {
                modal.classList.remove('show');
            }
            
            function closeForDuration(hours) {
                var expireTime = new Date().getTime() + (hours * 60 * 60 * 1000);
                localStorage.setItem('hideNotificationUntil', expireTime);
                closeModal();
            }
            
            if(closeBtn) closeBtn.addEventListener('click', closeModal);
            if(overlay) overlay.addEventListener('click', closeModal);
            
            if(closeOneHourBtn) {
                closeOneHourBtn.addEventListener('click', function() {
                    closeForDuration(1); // 1 hour
                });
            }
            
            if(understoodBtn) {
                understoodBtn.addEventListener('click', function() {
                    closeForDuration(24); // 24 hours
                });
            }
        });
    </script>

    <!--begin::Scrolltop-->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true" aria-label="Cuộn lên đầu trang">
        <i class="ki-duotone ki-arrow-up" aria-hidden="true"><span class="path1"></span><span class="path2"></span></i>
    </div>
    <!--end::Scrolltop-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "{{ url('/') }}";
    </script>

    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="/assets1/plugins/global/plugins.bundle.js"></script>
    <script src="/assets1/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="/assets1/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="/assets1/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.7/dist/notiflix-aio-3.2.7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="/assets/js/cmsbvq.js?v=20260521"></script>
    <script src="/assets/js/bvq.js?v=20260521"></script>
    <script src="/assets/js/coupon.js?v=1.0.1"></script>


 


    @yield('scripts')
    <!-- Theme Effect -->
    <script>
        window.themeEffect = "{{ s('theme_effect') ?? 'none' }}";
    </script>
    
    <script src="/assets/js/effects.js?v=1.0.1"></script>
    {!! s("script_footer") !!}

    <script>
        KTUtil.onDOMContentLoaded(function() {
            var url = "{{ request()->url() }}";
            KTMenu.updateByLinkAttribute(url);
            
            // Hide loader as soon as DOM is ready for much faster LCP/FCP
            var loader = document.querySelector(".loader-wrapper");
            if(loader) {
                loader.style.transition = "opacity 0.3s ease";
                loader.style.opacity = "0";
                setTimeout(function() { loader.style.display = "none"; }, 300);
            }
        });
    </script>
</body>
<!--end::Body-->

</html>
