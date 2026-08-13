<!DOCTYPE html>
<html dir="ltr" lang="vi">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ s('title') }}</title>
    @include('components.seo-header')

    <link rel="canonical" href="{{ url()->current() }}">
    <script type="text/javascript" src="/landing/ydsosyal/js/bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js"></script>
    <link rel="stylesheet" href="https://cdn.hassbase.com/plugins/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css'>
    <link rel="stylesheet" type="text/css" href="/landing/ydsosyal/css/cnutco3j7djwle3o.css">
    <link rel="stylesheet" type="text/css" href="/landing/ydsosyal/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/landing/ydsosyal/css/bootstrap-datetimepicker.css">
    <script type="text/javascript" src="/landing/ydsosyal/js/jquery.min.js"></script>
    <style>
        .integration-fixed {
            position: fixed;
            z-index: 10000000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 24px;
            row-gap: 12px;
        }

        .integration-fixed__top-left {
            top: 0;
            left: 0;
        }

        .integration-fixed__top-right {
            top: 0;
            right: 0;
        }

        .integration-fixed__bottom-left {
            bottom: 0;
            left: 0;
        }

        .integration-fixed__bottom-right {
            bottom: 0;
            right: 0;
        }
    </style>
    <script>
        window.modules = {};

        var htmlcontent = document.querySelector("HTML");
        var default_theme = "dark";

        function colorApp() {
            let getmode = localStorage.getItem("lightMode") || default_theme;
            if (getmode === "auto") {
                let isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches;
                htmlcontent.setAttribute("class", isDarkMode ? "dark" : "light");
            } else {
                htmlcontent.setAttribute("class", getmode);
            }
        }

        colorApp();
    </script>
</head>

<body class="">
    <div class="main-header">
        <div class="container">
            <div class="grid-container">
                <div class="col-span-12">
                    <div class="main-header-content">
                        <div class="main-header-logo">
                            <a href="/">
                                <img src="{{ asset(s('logo')) }}" alt="" title="" class="logo">
                            </a>
                        </div>
                        <div class="main-header-right">
                            <div class="main-header-link">
                                <a href="/new" class="main-header-link-item">
                                    <i class="navbar-icon far fa-home"></i> Home </a>
                                <a href="{{ route('auth.login') }}" class="main-header-link-item">
                                    <i class="navbar-icon fas fa-sign-in"></i> Sign In </a>
                                <a href="{{ route('services') }}" class="main-header-link-item">
                                    <i class="navbar-icon far fa-list-alt"></i> Services </a>
                                <a href="{{ route('api.v2') }}" class="main-header-link-item">
                                    <i class="navbar-icon far fa-key"></i> API </a>
                            </div>
                            <div class="main-header-btn">
                                <a href="{{ route('auth.register') }}" class="main-header-btn-item active">Sign Up</a>
                            </div>
                        </div>
                        <div class="main-header-btn-mobile-group">
                            <div class="main-header-mobile-btn" onclick="openMainHeader()">
                                <i class="far fa-bars"></i>
                            </div>
                        </div>
                    </div>
                    <div class="main-header-mobile">
                        <div class="main-header-mobile-top">
                            <div class="main-header-logo">
                                <a href="index.html">
                                    <img src="../drake.io.vn/storage/resources/images/f66a39eb-1757-43e2-a6bd-e251cded8438.json"
                                        alt="" title="" class="logo">
                                </a>
                            </div>
                            <div class="main-header-btn-mobile-group">
                                <div class="main-header-mobile-btn" onclick="closeMainHeader()">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="main-header-mobile-link">
                            <a href="/new" class="main-header-mobile-link-item">
                                <i class="navbar-icon far fa-home"></i> Home </a>
                            <a href="{{ route('auth.login') }}" class="main-header-mobile-link-item">
                                <i class="navbar-icon fas fa-sign-in"></i> Sign In </a>
                            <a href="{{ route('services') }}" class="main-header-mobile-link-item">
                                <i class="navbar-icon far fa-list-alt"></i> Services </a>
                            <a href="{{ route('apidoc') }}" class="main-header-mobile-link-item">
                                <i class="navbar-icon far fa-key"></i> API </a>
                        </div>
                        <div class="main-header-mobile-bottom">
                            <a href="{{ route('auth.register') }}" class="main-header-btn-item active">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="home-section">
        <div class="particle-animation">
            <canvas data-particle-animation></canvas>
        </div>
        <div class="container">
            <div class="container">
                <div class="grid-container">
                    <div class="col-span-12">
                        <div class="home-section-content">
                            <div class="home-left-list">
                                <span class="badge"><i class="fa fa-bolt"></i> World's #1 SMM Panel & Provider</span>
                                <h1 class="home-title">All In One <span class="text-primary">SMM Panel</span> Platform🔥
                                </h1>
                                <p class="small">{{ strtoupper(request()->getHost()) }} is right now the leading SMM provider with high quality
                                    services. We strive to deliver NON-DROP results for all of our services with
                                    Lifetime Guarantee.</p>
                                <div class="home-btn">
                                    <a href="{{ route('auth.register') }}" class="btn btn-main text-center site-gradient">Sign Up for
                                        Free <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="social-slider">
            <div class="social-item">
                <span>Vimeo</span>
                <i class="fab fa-vimeo-v"></i>
                <i class="fab fa-vimeo-v absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Twitch</span>
                <i class="fab fa-twitch"></i>
                <i class="fab fa-twitch absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Reddit</span>
                <i class="fab fa-reddit"></i>
                <i class="fab fa-reddit absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Telegram</span>
                <i class="fab fa-telegram"></i>
                <i class="fab fa-telegram absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>TikTok</span>
                <i class="fab fa-tiktok"></i>
                <i class="fab fa-tiktok absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Instagram</span>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-instagram absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Twitter</span>
                <i class="fab fa-twitter"></i>
                <i class="fab fa-twitter absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Spotify</span>
                <i class="fab fa-spotify"></i>
                <i class="fab fa-spotify absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Discord</span>
                <i class="fab fa-discord"></i>
                <i class="fab fa-discord absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Dailymotion</span>
                <i class="fab fa-dailymotion"></i>
                <i class="fab fa-dailymotion absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Tumblr</span>
                <i class="fab fa-tumblr"></i>
                <i class="fab fa-tumblr absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>YouTube</span>
                <i class="fab fa-youtube"></i>
                <i class="fab fa-youtube absolute-icon"></i>
            </div>
            <div class="social-item">
                <span>Pinterest</span>
                <i class="fab fa-pinterest"></i>
                <i class="fab fa-pinterest absolute-icon"></i>
            </div>
        </div>
    </section>
    <section class="feature-list">
        <div class="container">
            <div class="grid-container">
                <div class="main-page-title col-span-12">
                    <i class="fa fa-bolt"></i>Why choose us?
                </div>
                <div class="feature-box col-span-4">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h2 class="feature-title">Fast and Secure Order Processing</h2>
                    <p class="feature-text">Your orders are processed as soon as possible and completed safely.</p>
                </div>
                <div class="feature-box col-span-4">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="feature-title">High Quality Followers</h2>
                    <p class="feature-text">The followers offered are provided by real and interactive accounts.</p>
                </div>
                <div class="feature-box col-span-4">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h2 class="feature-title">Affordable Prices</h2>
                    <p class="feature-text">It aims to protect its customers' budgets by providing high quality
                        services at affordable prices.
                    <p>
                </div>
                <div class="feature-box col-span-6">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h2 class="feature-title">Quick Support</h2>
                    <p class="feature-text">It has a fast customer service team to support customers at all times.</p>
                </div>
                <div class="feature-box col-span-6">
                    <div class="feature-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h2 class="feature-title">Wide Range of Services</h2>
                    <p class="feature-text">It offers many different services for Instagram, Twitter, Facebook, YouTube
                        and other social media platforms.
                    <p>
                </div>
            </div>
        </div>
    </section>
    <section class="why-work">
        <div class="container">
            <div class="grid-container">
                <div class="main-page-title col-span-12">
                    <i class="fas fa-cog"></i>
                    <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">How Does It Work?</font>
                    </font>
                </div>
                <div class="step-box col-span-3">
                    <div class="step-icon">
                        <i class="fa fa-user-plus" aria-hidden="true"></i>
                    </div>
                    <div class="step-text">
                        <h3>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Create Account</font>
                            </font>
                        </h3>
                        <p>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Quickly create a new user profile using your
                                    email and password. You can start shopping immediately with an easy registration
                                    process.</font>
                            </font>
                        </p>
                    </div>
                    <div class="step-number">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">1</font>
                        </font>
                    </div>
                </div>
                <div class="step-box col-span-3">
                    <div class="step-icon">
                        <i class="fa fa-wallet" aria-hidden="true"></i>
                    </div>
                    <div class="step-text">
                        <h3>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Load Balance</font>
                            </font>
                        </h3>
                        <p>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Top up your balance with secure payment methods
                                    to complete your transactions. Add balance quickly and easily with our credit card
                                    or bank transfer options.</font>
                            </font>
                        </p>
                    </div>
                    <div class="step-number">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">2</font>
                        </font>
                    </div>
                </div>
                <div class="step-box col-span-3">
                    <div class="step-icon">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    </div>
                    <div class="step-text">
                        <h3>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Order Now</font>
                            </font>
                        </h3>
                        <p>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Discover the services that best suit your needs
                                    and create your order by choosing according to your budget. Make your choice among
                                    various options and complete your transaction.</font>
                            </font>
                        </p>
                    </div>
                    <div class="step-number">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">3</font>
                        </font>
                    </div>
                </div>
                <div class="step-box col-span-3">
                    <div class="step-icon">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                    </div>
                    <div class="step-text">
                        <h3>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Order Completed</font>
                            </font>
                        </h3>
                        <p>
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">Once your order is confirmed, all that remains
                                    is to wait for the results. Enjoy our services and save time with our solutions
                                    tailored to your needs.</font>
                            </font>
                        </p>
                    </div>
                    <div class="step-number">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">4</font>
                        </font>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="faq-box">
        <div class="container">
            <div class="grid-container">
                <div class="main-page-title col-span-12">
                    <i class="fas fa-question"></i>
                    <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">FAQ</font>
                    </font>
                </div>
                <div class="col-span-12">
                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-header">
                                <span class="faq-icon">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">+</font>
                                    </font>
                                </span>
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">What is SMM Panel? </font>
                                </font>
                            </div>
                            <div class="faq-content" style="display: none;">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">SMM Panel (Social Media Marketing Panel) is
                                        a tool used to manage your social media accounts, increase the number of
                                        followers and increase traffic to your site. SMM panels offer various services
                                        to increase popularity on social media platforms, these services include buying
                                        followers, likes, comments, shares, views and more.</font>
                                </font>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-header">
                                <span class="faq-icon">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">+</font>
                                    </font>
                                </span>
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">How Do SMM Panels Help? </font>
                                </font>
                            </div>
                            <div class="faq-content" style="display: none;">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">SMM panels are especially useful for
                                        businesses, marketers and influencers. In social media marketing strategies, the
                                        number of followers and interaction rates are very important. SMM panels offer
                                        an easy and fast solution to achieve these goals.</font>
                                </font>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-header">
                                <span class="faq-icon">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">+</font>
                                    </font>
                                </span>
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">How to Use SMM Panel? </font>
                                </font>
                            </div>
                            <div class="faq-content" style="display: none;">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">SMM panels are usually web-based and require
                                        membership. After the membership process, it is possible to purchase different
                                        services through the panels. For example, if a user wants to buy followers for
                                        their Instagram account, they can go to the relevant section in the SMM panel
                                        and select and purchase a follower package. This process is usually completed in
                                        a few minutes and results in the purchased followers being added to the account.
                                    </font>
                                </font>
                            </div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-header">
                                <span class="faq-icon">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">×</font>
                                    </font>
                                </span>
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Which Social Media Platforms Does It
                                        Support? </font>
                                </font>
                            </div>
                            <div class="faq-content" style="display: none;">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">SMM panels offer services for many different
                                        social media platforms such as Instagram, Facebook, Twitter, TikTok, and
                                        YouTube.</font>
                                </font>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="grid-container">
                <div class="col-span-12">
                    <div class="footer-list">
                        <a href="/new">
                            <div class="footer-logo" onclick="javascript:location.href='/new'">
                                <img src="{{ asset(s('logo')) }}" alt="ydsocial" height="50px" width="150px" title="">
                            </div>
                        </a>
                        <div class="footer-btn">
                            <a class="btn btn-main border-10" href="sign-up.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Register </font>
                                </font>
                            </a>
                        </div>
                    </div>
                    <div class="footer-link">
                        <div class="footer-link-item">
                            <i class="navbar-icon fas fa-sign-in"></i>
                            <a href="sign-in.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Member login</font>
                                </font>
                            </a>
                        </div>
                        <div class="footer-link-item">
                            <i class="navbar-icon far fa-list-alt"></i>
                            <a href="services.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Services</font>
                                </font>
                            </a>
                        </div>
                        <div class="footer-link-item">
                            <i class="navbar-icon far fa-key"></i>
                            <a href="api.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">API</font>
                                </font>
                            </a>
                        </div>
                        <div class="footer-link-item">
                            <i class="navbar-icon far fa-address-card"></i>
                            <a href="sign-up.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Fast Membership</font>
                                </font>
                            </a>
                        </div>
                        <div class="footer-link-item">
                            <i class="navbar-icon fas fa-address-card"></i>
                            <a href="sign-in.html">
                                <font style="vertical-align: inherit;">
                                    <font style="vertical-align: inherit;">Our Contact Information</font>
                                </font>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script type="text/javascript" src="/landing/ydsosyal/js/full.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/lottie-player.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/particle-animation.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/qhp098ip83oky66u.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/anb3tbczm6u23a9j.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/xfop43hidcl6hnnw.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/sntjo6a58nah6mwu.js"></script>
    <script type="text/javascript" src="/landing/ydsosyal/js/pco8i4n9s0y0addm.js"></script>
</body>

</html>
