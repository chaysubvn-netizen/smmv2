<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ s('title') }} - {{ s('tagline') }}</title>
    <meta name="description" content="{{ s('meta_description') }}">
    <meta name="keywords" content="{{ s('meta_keywords') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ s('title') }}">
    <meta property="og:description" content="{{ s('meta_description') }}">
    <meta property="og:image" content="{{ s('logo') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ s('title') }}">
    <meta property="twitter:description" content="{{ s('meta_description') }}">
    <meta property="twitter:image" content="{{ s('logo') }}">
    <link rel="shortcut icon" href="{{ s('favicon') }}">
    
    <!-- Fonts - Preload for performance -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"></noscript>
    
    <!-- Icons - Defer for performance -->
    <script defer src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --secondary: #4f46e5;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --white: #ffffff;
            --shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #cbd5e1; /* Increased contrast for accessibility */
            --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
            transition: background-color 0.3s ease;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1.5rem 0;
            transition: var(--transition);
        }

        header.scrolled {
            background: rgba(var(--bg-body-rgb), 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
        }

        .logo iconify-icon {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
            opacity: 0.8;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
            opacity: 1;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.5rem;
        }

        /* Mobile Menu Overlay */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 300px;
            height: 100vh;
            background: var(--bg-card);
            z-index: 2000;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1999;
            display: none;
            backdrop-filter: blur(4px);
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        .mobile-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-links a {
            text-decoration: none;
            color: var(--text-main);
            font-size: 1.1rem;
            font-weight: 600;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login {
            color: var(--text-main);
            background: transparent;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn-signup {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-card);
            border: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--text-main);
            cursor: pointer;
            transition: var(--transition);
        }

        /* Hero Section */
        .hero {
            padding: 10rem 0 6rem;
            position: relative;
            background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.05) 0%, transparent 40%);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-tag {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero h1 span {
            color: var(--primary);
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            max-width: 500px;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .hero-badges {
            display: flex;
            gap: 2rem;
        }

        .hero-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .badge-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--bg-card);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .badge-text div:first-child {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .badge-text div:last-child {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Mockup */
        .mockup-container {
            position: relative;
        }

        .dashboard-mockup {
            background: var(--bg-card);
            border-radius: 24px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            transform: perspective(1000px) rotateY(-5deg) rotateX(2deg);
            transition: var(--transition);
        }

        .dashboard-mockup:hover {
            transform: perspective(1000px) rotateY(0) rotateX(0);
        }

        .mockup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .mockup-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 1.5rem;
        }

        .mockup-sidebar {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-item {
            padding: 0.75rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
        }

        .sidebar-item.active {
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
        }

        .mockup-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg-body);
            padding: 1rem;
            border-radius: 15px;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            text-align: center;
        }

        .section-header {
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: var(--text-muted);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--bg-card);
            padding: 2rem 1.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: left;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Stats Bar */
        .stats-bar {
            background: var(--primary);
            padding: 3rem 0;
            margin-top: 4rem;
            border-radius: 30px;
            color: white;
        }

        .stats-grid {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        .stat-info h4 {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .stat-info p {
            font-size: 0.8rem;
            opacity: 0.8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero h1 { font-size: 3rem; }
            .hero p { margin: 0 auto 2.5rem; }
            .hero-btns { justify-content: center; }
            .hero-badges { justify-content: center; flex-wrap: wrap; }
            .hero h1 span { display: block; }
        }

        @media (max-width: 768px) {
            header { padding: 1rem 0; }
            .logo { font-size: 1.25rem; }
            .nav-actions .btn { display: none; }
            .nav-links { display: none; }
            .menu-toggle { display: flex; width: 36px; height: 36px; font-size: 1.2rem; }
            .hero { padding: 8rem 0 4rem; }
            .hero-tag { font-size: 0.7rem; padding: 0.4rem 0.8rem; }
            .hero h1 { font-size: 2.2rem; margin-bottom: 1rem; }
            .hero p { font-size: 0.95rem; margin-bottom: 2rem; }
            .hero-btns { flex-direction: column; gap: 0.75rem; }
            .hero-badges { grid-template-columns: 1fr; gap: 1rem; text-align: left; }
            .hero-badge { background: var(--bg-card); padding: 1rem; border-radius: 15px; box-shadow: var(--shadow); }
            .mockup-grid { grid-template-columns: 1fr; }
            .mockup-sidebar { display: none; }
            .stat-info h4 { font-size: 1.4rem; }
            .btn { width: 100%; justify-content: center; padding: 0.85rem 1.5rem; }
            .features { padding: 4rem 0; }
            .section-header h2 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <header id="header">
        <div class="container">
            <nav>
                <a href="/" class="logo" aria-label="Go to SMMPanel Homepage">
                    <iconify-icon icon="solar:rocket-bold-duotone" aria-hidden="true"></iconify-icon>
                    SMMPanel
                </a>
                <div class="nav-links">
                    <a href="#" class="active">Home</a>
                    <a href="#">Services</a>
                    <a href="#">Features</a>
                    <a href="#">How it works</a>
                    <a href="#">API</a>
                    <a href="#">Blog</a>
                    <a href="#">Contact</a>
                </div>
                <div class="nav-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark mode">
                        <iconify-icon icon="solar:sun-bold-duotone" id="theme-icon"></iconify-icon>
                    </button>
                    <a href="{{ route('auth.login') }}" class="btn btn-login">Log in</a>
                    <a href="{{ route('auth.register') }}" class="btn btn-signup">Sign up <iconify-icon icon="solar:user-plus-bold-duotone"></iconify-icon></a>
                    <button class="menu-toggle" id="menu-toggle" aria-label="Open mobile menu">
                        <iconify-icon icon="solar:hamburger-menu-bold-duotone"></iconify-icon>
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <div class="mobile-menu-overlay" id="menu-overlay"></div>
    <div class="mobile-menu" id="mobile-menu">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div class="logo">SMMPanel</div>
            <button class="theme-toggle" id="close-menu" aria-label="Close mobile menu">
                <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
            </button>
        </div>
        <div class="mobile-links">
            <a href="#" class="active">Home</a>
            <a href="#">Services</a>
            <a href="#">Features</a>
            <a href="#">How it works</a>
            <a href="#">API</a>
            <a href="#">Blog</a>
            <a href="#">Contact</a>
        </div>
        <div style="margin-top: auto; display: flex; flex-direction: column; gap: 1rem;">
            <a href="{{ route('auth.login') }}" class="btn btn-login" style="width: 100%; justify-content: center;">Log in</a>
            <a href="{{ route('auth.register') }}" class="btn btn-signup" style="width: 100%; justify-content: center;">Sign up</a>
        </div>
    </div>

    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-tag">
                        <iconify-icon icon="solar:bolt-circle-bold-duotone" aria-hidden="true"></iconify-icon>
                        #1 SMM PANEL FOR RESELLERS
                    </div>
                    <h1>The Best SMM Panel for <span>Your Business</span></h1>
                    <p>Cheapest SMM panel with fast service delivery, real-time updates and 24/7 customer support. Grow your business with us.</p>
                    
                    <div class="hero-btns">
                        <a href="{{ route('auth.register') }}" class="btn btn-signup" style="padding: 1rem 2rem; font-size: 1.1rem;">
                            Get Started Now <iconify-icon icon="solar:round-alt-arrow-right-bold-duotone" aria-hidden="true"></iconify-icon>
                        </a>
                        <a href="#" class="btn btn-login" style="padding: 1rem 2rem; font-size: 1.1rem; background: var(--bg-card);">
                            View Services <iconify-icon icon="solar:list-bold-duotone"></iconify-icon>
                        </a>
                    </div>

                    <div class="hero-badges">
                        <div class="hero-badge">
                            <div class="badge-icon"><iconify-icon icon="solar:tag-price-bold-duotone"></iconify-icon></div>
                            <div class="badge-text">
                                <div>Cheapest Prices</div>
                                <div>Best rates in the market</div>
                            </div>
                        </div>
                        <div class="hero-badge">
                            <div class="badge-icon"><iconify-icon icon="solar:bolt-bold-duotone"></iconify-icon></div>
                            <div class="badge-text">
                                <div>Fast Delivery</div>
                                <div>Instant and reliable</div>
                            </div>
                        </div>
                        <div class="hero-badge">
                            <div class="badge-icon"><iconify-icon icon="solar:shield-check-bold-duotone"></iconify-icon></div>
                            <div class="badge-text">
                                <div>99.9% Uptime</div>
                                <div>Always online for you</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mockup-container">
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="logo" style="font-size: 1rem;">
                                <iconify-icon icon="solar:rocket-bold-duotone"></iconify-icon>
                                SMMPanel
                            </div>
                            <iconify-icon icon="solar:menu-dots-bold" style="color: var(--text-muted);"></iconify-icon>
                        </div>
                        <div class="mockup-grid">
                            <div class="mockup-sidebar">
                                <div class="sidebar-item active"><iconify-icon icon="solar:widget-bold-duotone"></iconify-icon> Dashboard</div>
                                <div class="sidebar-item"><iconify-icon icon="solar:cart-large-4-bold-duotone"></iconify-icon> New Order</div>
                                <div class="sidebar-item"><iconify-icon icon="solar:history-bold-duotone"></iconify-icon> Orders</div>
                                <div class="sidebar-item"><iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon> Services</div>
                                <div class="sidebar-item"><iconify-icon icon="solar:wallet-bold-duotone"></iconify-icon> Add Funds</div>
                                <div class="sidebar-item"><iconify-icon icon="solar:letter-bold-duotone"></iconify-icon> Tickets</div>
                            </div>
                            <div class="mockup-main">
                                <div style="font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem;">Dashboard</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 1.5rem;">Welcome back, Reseller!</div>
                                
                                <div class="mockup-stats">
                                    <div class="stat-card">
                                        <div class="stat-value">1243</div>
                                        <div class="stat-label">Total Orders</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-value">1187</div>
                                        <div class="stat-label">Completed</div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="stat-value">56</div>
                                        <div class="stat-label">In Progress</div>
                                    </div>
                                </div>

                                <div style="background: var(--bg-body); border-radius: 15px; padding: 1rem;">
                                    <div style="font-size: 0.75rem; font-weight: 700; margin-bottom: 1rem;">Recent Orders</div>
                                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.65rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <iconify-icon icon="logos:instagram-icon"></iconify-icon> Instagram Followers
                                            </div>
                                            <div style="color: #10b981; font-weight: 700;">Completed</div>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.65rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <iconify-icon icon="logos:youtube-icon"></iconify-icon> YouTube Views
                                            </div>
                                            <div style="color: #f59e0b; font-weight: 700;">Processing</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Powerful Features to Boost Your Business</h2>
                <p>Everything you need in one place</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:widget-3-bold-duotone"></iconify-icon></div>
                    <h3>5000+ Services</h3>
                    <p>Wide range of services for all social media platforms</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:bolt-bold-duotone"></iconify-icon></div>
                    <h3>Instant Delivery</h3>
                    <h3>Instant Delivery</h3>
                    <p>Lightning fast delivery within seconds</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:tag-price-bold-duotone"></iconify-icon></div>
                    <h3>Cheap Prices</h3>
                    <p>Most competitive prices in the market</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:headphones-round-bold-duotone"></iconify-icon></div>
                    <h3>24/7 Support</h3>
                    <p>Round the clock customer support</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:code-bold-duotone"></iconify-icon></div>
                    <h3>API Access</h3>
                    <p>Powerful API for seamless integration</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><iconify-icon icon="solar:shield-keyhole-bold-duotone"></iconify-icon></div>
                    <h3>Secure & Safe</h3>
                    <p>100% safe and secure transactions</p>
                </div>
            </div>

            <div class="stats-bar">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon"><iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon></div>
                        <div class="stat-info">
                            <h4>10K+</h4>
                            <p>Happy Customers</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><iconify-icon icon="solar:cart-large-bold-duotone"></iconify-icon></div>
                        <div class="stat-info">
                            <h4>1M+</h4>
                            <p>Orders Completed</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><iconify-icon icon="solar:box-bold-duotone"></iconify-icon></div>
                        <div class="stat-info">
                            <h4>5000+</h4>
                            <p>Services Available</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><iconify-icon icon="solar:graph-bold-duotone"></iconify-icon></div>
                        <div class="stat-info">
                            <h4>99.9%</h4>
                            <p>Uptime</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon"><iconify-icon icon="solar:headphones-round-bold-duotone"></iconify-icon></div>
                        <div class="stat-info">
                            <h4>24/7</h4>
                            <p>Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        themeToggle.addEventListener('click', () => {
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                themeIcon.setAttribute('icon', 'solar:sun-bold-duotone');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.setAttribute('icon', 'solar:moon-bold-duotone');
            }
        });

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menu-toggle');
        const closeMenu = document.getElementById('close-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuOverlay = document.getElementById('menu-overlay');

        const toggleMenu = () => {
            mobileMenu.classList.toggle('active');
            menuOverlay.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        };

        menuToggle.addEventListener('click', toggleMenu);
        closeMenu.addEventListener('click', toggleMenu);
        menuOverlay.addEventListener('click', toggleMenu);
    </script>
</body>
</html>
