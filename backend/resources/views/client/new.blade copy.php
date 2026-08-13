<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') | {{ s('title') }}</title>
    @include('components.seo-header', ['title' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link rel="stylesheet" href="/subx/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="/subx/css/style-preset.css">
    <link rel="stylesheet" href="/subx/css/custom.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
        window.$ = window.jQuery;
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Fredoka:wght@300..700&family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <link rel="stylesheet" href="/subx/css/client-layout.css">
    <link rel="stylesheet" href="/subx/css/manhdev-toast.css">
    <link rel="stylesheet" href="/subx/css/notification-modal.css">
    
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



    <link rel="stylesheet" href="/subx/css/inter.css">
    <link rel="stylesheet" href="/subx/css/custom-manhdev.css">
    @yield('css')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="tab" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="{{ s('theme_mode') ?? 'light' }}">


    @include('client.layouts.menu')
    @include('client.layouts.header')

    <div class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </div>

    @include('client.layouts.mobile-bottom-nav')

    <!-- Support Button -->
    <button type="button" class="support-floating-btn" id="supportFloatingBtn" title="Cần hỗ trợ?">
        <i class="ti ti-headset"></i>
    </button>

    <!-- Support Popup -->
    <div class="support-popup-overlay" id="supportPopupOverlay">
        <div class="support-popup-container">
            <div class="support-popup">
                <button type="button" class="support-popup-close" id="supportPopupClose">
                    <i class="ti ti-x"></i>
                </button>
                <div class="support-popup-header">
                    <div class="support-popup-icon"><i class="ti ti-headset"></i></div>
                    <h4 class="support-popup-title">{{ __('Cần hỗ trợ?') }}</h4>
                    <p class="support-popup-subtitle">{{ __('Liên hệ với chúng tôi qua các kênh sau') }}</p>
                </div>
                <div class="support-popup-body">
                    <div class="support-popup-buttons">
                        @if(s('facebook'))
                        <a href="{{ s('facebook') }}" target="_blank" class="support-popup-btn support-popup-btn-facebook">
                            <div class="support-popup-btn-icon"><i class="ti ti-brand-facebook"></i></div>
                            <div class="support-popup-btn-content">
                                <span class="support-popup-btn-label">Facebook</span>
                                <span class="support-popup-btn-desc">{{ __('Nhắn tin ngay') }}</span>
                            </div>
                        </a>
                        @endif
                        @if(s('zalo'))
                        <a href="https://zalo.me/{{ s('zalo') }}" target="_blank" class="support-popup-btn support-popup-btn-zalo">
                            <div class="support-popup-btn-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 5.58 2 10c0 2.5 1.26 4.74 3.25 6.25L4 22l6-1.25c.5.08 1 .12 1.5.12 5.52 0 10-3.58 10-8s-4.48-8-10-8zm0 14c-.83 0-1.64-.12-2.41-.35l-.71-.18-2.59.54.56-2.53-.18-.71C6.12 13.64 6 12.83 6 12c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6z" /></svg>
                            </div>
                            <div class="support-popup-btn-content">
                                <span class="support-popup-btn-label">Zalo</span>
                                <span class="support-popup-btn-desc">{{ __('Chat trực tiếp') }}</span>
                            </div>
                        </a>
                        @endif
                        @if(s('telegram'))
                        <a href="https://t.me/{{ s('telegram') }}" target="_blank" class="support-popup-btn support-popup-btn-telegram">
                            <div class="support-popup-btn-icon"><i class="ti ti-brand-telegram"></i></div>
                            <div class="support-popup-btn-content">
                                <span class="support-popup-btn-label">Telegram</span>
                                <span class="support-popup-btn-desc">{{ __('Gửi tin nhắn') }}</span>
                            </div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="global-notification-modal" id="notificationModal" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="global-notification-overlay" id="notificationModalOverlay" aria-hidden="true"></div>
        <div class="global-notification-wrapper">
            <div class="global-notification-container">
                <div class="global-notification-header">
                    <div class="global-notification-header-inner">
                        <span class="global-notification-header-icon" aria-hidden="true">
                            <img src="https://subx.vn/frontend/package/Notification.gif" alt="" class="global-notification-header-icon-img">
                        </span>
                        <h2 class="global-notification-title" id="notificationModalLabel">{{ __('Thông báo') }}</h2>
                    </div>
                    <button type="button" class="global-notification-close" id="notificationModalCloseBtn" aria-label="Đóng">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="global-notification-body">
                    <div class="global-notification-prose">
                        {!! s('notification_modal') !!}
                    </div>
                </div>
                <div class="global-notification-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="globalCloseForOneHourBtn">
                        <i class="ti ti-clock me-1"></i>{{ __('Đóng trong 1h') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-4" id="notificationModalUnderstoodBtn">
                        <i class="ti ti-check me-1"></i>{{ __('Đã hiểu') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Selector Modal -->
    <div class="modal fade" id="currencySelectorModal" tabindex="-1" aria-labelledby="currencySelectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="currencySelectorModalLabel">
                        <i class="ti ti-currency-dollar me-2"></i>{{ __('Chọn tiền tệ') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="currency-list">
                        <!-- Currencies will be rendered here by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/subx/js/loader.js"></script>
    <script src="/subx/js/pc-preset.js"></script>
    <script src="/subx/js/pc-theme.js"></script>
    <script src="/subx/js/support-popup.js"></script>
    <script src="/subx/js/notification-modal.js"></script>
    <script src="/subx/js/skeleton-menus.js"></script>
    <script src="/subx/js/currency-selector.js"></script>
    <script src="/subx/js/page-translator.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="/subx/js/logout.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.7/dist/notiflix-aio-3.2.7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="/assets/js/cmsbvq.js?v=1.0.1"></script>
    <script src="/assets/js/bvq.js?v=1.0.1"></script>
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

    <script>
        (function () {
            if (typeof window.ManhDevToast === 'undefined') return;
            var typeMap = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
            window.Toast = {
                fire: function (opts) {
                    var o = opts && typeof opts === 'object' ? opts : {};
                    var type = typeMap[o.icon] || 'info';
                    var title = o.title || '';
                    var msg = o.message !== undefined ? o.message : (typeof o === 'string' ? o : '');
                    window.ManhDevToast.show(type, title, msg);
                }
            };
        })();

        $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
            if (jqxhr.status === 419) {
                window.location.reload();
            }
        });
    </script>

    @yield('scripts')
    {!! s("script_footer") !!}
</body>

</html>
