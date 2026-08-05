'use client';
import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { 
  RocketOutlined, 
  SafetyCertificateOutlined,
  CustomerServiceOutlined,
  GlobalOutlined,
  LineChartOutlined,
  ThunderboltFilled,
  CheckCircleFilled,
  ArrowRightOutlined,
  DashboardOutlined,
  HeartFilled,
  SettingOutlined,
  MoonOutlined,
  CodeOutlined,
  SyncOutlined,
  TeamOutlined,
  WalletOutlined,
  ShoppingCartOutlined,
  FundViewOutlined,
  InstagramOutlined,
  YoutubeOutlined,
  FacebookFilled,
  TwitterOutlined,
  PlaySquareOutlined,
  MessageOutlined
} from '@ant-design/icons';
import api from '@/lib/axios';

const services = [
  { title: 'Instant Processing', desc: 'Orders begin within seconds, not hours. Our automated system processes thousands simultaneously, 24/7. No manual queues, no waiting.', icon: <RocketOutlined /> },
  { title: '100% Secure', desc: 'We never ask for passwords. All services delivered via public URLs with bank-grade encrypted transactions.', icon: <SafetyCertificateOutlined /> },
  { title: 'Cheapest Prices', desc: "We're the direct provider — no middleman markup. You get wholesale pricing that resellers charge double for.", icon: <LineChartOutlined /> },
  { title: 'Auto-Refill', desc: 'Turn on auto-refill for any order. If numbers drop, we top them up automatically — at no extra cost. Set it and forget it.', icon: <SyncOutlined /> },
  { title: 'API for Resellers', desc: 'Full REST API v2 with webhooks. Build your own panel, automate bulk orders, or plug into your CRM. Full documentation included.', icon: <CodeOutlined /> },
  { title: '24/7 Support', desc: "Real humans, not chatbots. Average response time under 5 minutes, day or night. We've got your back.", icon: <CustomerServiceOutlined /> },
  { title: 'Live Tracking', desc: 'Watch every order from submission to completion in real-time. Detailed analytics and full history in your dashboard.', icon: <FundViewOutlined /> },
  { title: 'Mass & Drip Orders', desc: 'Upload thousands of orders via CSV or API batch. Drip-feed to simulate natural, organic-looking growth.', icon: <SettingOutlined /> }
];

const steps = [
  { num: '01', title: 'Create Account', desc: 'Sign up for free in 30 seconds. No credit card required. No commitments. No hidden fees.', icon: <TeamOutlined /> },
  { num: '02', title: 'Add Funds', desc: 'Deposit via bank transfer, crypto, PayPal, MoMo, ZaloPay, or e-wallet. Instant balance.', icon: <WalletOutlined /> },
  { num: '03', title: 'Place an Order', desc: "Pick a service, paste your public link, set the quantity, and hit submit. It's literally that easy.", icon: <ShoppingCartOutlined /> },
  { num: '04', title: 'Track & Grow', desc: 'Monitor your order in real-time. Watch your numbers climb. Rinse and repeat for consistent growth.', icon: <LineChartOutlined /> }
];

export default function LandingDefaultPage() {
  const [config, setConfig] = useState<any>({ title: 'SMM Panel' });
  const [activeTab, setActiveTab] = useState('Instagram');
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    api.get('/auth/api/me').then(({ data }) => {
      if (data?.status) {
        setUser(data.user);
      }
    }).catch(() => undefined);

    api.get('/client/config').then(({ data }) => {
      if (data?.status) {
        setConfig(data.data);
        document.title = data.data.title || 'SMM Panel';
        let metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) {
          metaDescription.setAttribute('content', data.data.description || 'Hệ thống SMM hàng đầu Việt Nam.');
        } else {
          metaDescription = document.createElement('meta');
          metaDescription.setAttribute('name', 'description');
          metaDescription.setAttribute('content', data.data.description || 'Hệ thống SMM hàng đầu Việt Nam.');
          document.head.appendChild(metaDescription);
        }
      }
    }).catch(() => undefined);
  }, []);

  const logoUrl = config.logo?.startsWith('http') ? config.logo : config.logo ? `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${config.logo.startsWith('/') ? '' : '/'}${config.logo}` : null;

  return (
    <div className="min-h-screen bg-[#FAFAFA] text-slate-800 font-sans selection:bg-[#34D399] selection:text-white relative overflow-x-hidden">
      {/* GLOBAL BACKGROUND GRID */}
      <div className="fixed inset-0 pointer-events-none z-0 opacity-[0.03]" style={{ backgroundImage: 'linear-gradient(#000 1px, transparent 1px), linear-gradient(90deg, #000 1px, transparent 1px)', backgroundSize: '60px 60px' }} />

      {/* HEADER */}
      <header className="fixed top-0 w-full z-50 bg-[#FAFAFA]/90 backdrop-blur-xl border-b border-slate-200 transition-all h-[72px] flex items-center">
        <div className="container mx-auto px-6 lg:px-12 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-3 relative z-10 w-fit shrink-0">
            {logoUrl ? (
              <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 w-auto object-contain" />
            ) : (
              <div className="flex items-center gap-2 text-2xl font-black text-slate-900 tracking-tight">
                <div className="w-8 h-8 rounded-lg bg-[#34D399] flex items-center justify-center text-white">
                  <DashboardOutlined />
                </div>
                {config.title || 'PRO SMM'}
              </div>
            )}
          </Link>
          <nav className="hidden md:flex items-center justify-center gap-10 font-semibold text-[14px] text-slate-500 z-10 flex-1">
            <a href="#services" className="hover:text-slate-900 transition-colors">Services</a>
            <a href="#features" className="hover:text-slate-900 transition-colors">Features</a>
            <a href="#workflow" className="hover:text-slate-900 transition-colors">How It Works</a>
            <a href="#faq" className="hover:text-slate-900 transition-colors">FAQ</a>
          </nav>
          <div className="flex items-center justify-end gap-6 z-10 w-fit shrink-0">
            {user ? (
              <Link href="/new" className="whitespace-nowrap">
                <button className="bg-[#ecfdf3] border border-[#d1f4e0] text-[#34D399] hover:bg-[#e0fcea] font-bold py-2.5 px-6 rounded-full transition-all duration-300 text-[15px]">
                  Bảng điều khiển
                </button>
              </Link>
            ) : (
              <>
                <Link 
                  href="/login" 
                  className="hidden sm:block text-slate-800 font-semibold hover:text-[#34D399] transition-colors text-[15px] whitespace-nowrap"
                >
                  Log in
                </Link>
                <Link href="/register" className="whitespace-nowrap">
                  <button className="bg-[#ecfdf3] border border-[#d1f4e0] text-[#34D399] hover:bg-[#e0fcea] font-bold py-2.5 px-6 rounded-full transition-all duration-300 text-[15px]">
                    Get Started
                  </button>
                </Link>
              </>
            )}
          </div>
        </div>
      </header>

      <main className="relative z-10 pt-32 pb-20 lg:pt-40 lg:pb-32">
        {/* HERO SECTION */}
        <section className="container mx-auto px-6 lg:px-12 text-center max-w-[900px]">
          <div className="inline-flex items-center gap-2 bg-slate-100/50 border border-slate-200 rounded-full px-4 py-1.5 mb-8 shadow-sm">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#34D399] opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-[#1FA660]"></span>
            </span>
            <span className="text-[11px] font-semibold text-slate-500 tracking-wide">Processing orders right now — every 0.2s</span>
          </div>
          
          <h1 className="text-6xl md:text-[80px] font-black text-[#0F172A] leading-[1.05] mb-8 tracking-tight">
            The #1 SMM panel for <br />
            <span className="text-[#3EDB7F]">serious growth</span>
          </h1>
          
          <p className="text-lg md:text-[17px] text-slate-500 mb-16 max-w-2xl mx-auto leading-relaxed">
            {config.title || 'Slike.vn'} là SMM Panel Việt Nam uy tín, cung cấp dịch vụ tăng like, follow, view, comment, share cho Facebook, TikTok, Instagram, YouTube và nhiều nền tảng mạng xã hội khác. Hệ thống xử lý nhanh, giá rẻ, dễ sử dụng, hỗ trợ 24/7.
          </p>
          
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12 mt-4">
            <Link href={user ? "/new" : "/register"}>
              <button className="w-full sm:w-auto bg-[#e6f4ea] text-[#137333] text-[15px] font-bold py-3.5 px-8 rounded transition-all hover:bg-[#d4edd9] flex items-center justify-center gap-2">
                {user ? 'Vào Dashboard' : 'Get started for free'} <ArrowRightOutlined className="text-xs" />
              </button>
            </Link>
            <Link href="#services">
              <button className="w-full sm:w-auto bg-white hover:bg-slate-50 border border-slate-200 text-slate-900 text-[15px] font-bold py-3.5 px-8 rounded transition-all">
                Explore Services
              </button>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-[12px] font-semibold text-slate-500 mb-20">
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#3EDB7F]" /> No credit card required</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#3EDB7F]" /> Instant activation</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#3EDB7F]" /> Cancel anytime</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#3EDB7F]" /> 24/7 live support</div>
          </div>

          {/* BRANDS ROW - Full color with fade edges */}
          <div 
            className="flex flex-wrap justify-center items-center gap-10 md:gap-16 transition duration-500 mb-20 text-4xl"
            style={{ maskImage: 'linear-gradient(to right, transparent, black 15%, black 85%, transparent)', WebkitMaskImage: 'linear-gradient(to right, transparent, black 15%, black 85%, transparent)' }}
          >
             <div className="flex items-center gap-3">
               <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" alt="Instagram" className="w-10 h-10 opacity-70" />
               <span className="text-lg font-bold tracking-tight text-slate-300">Instagram</span>
             </div>
             <div className="flex items-center gap-3">
               <svg className="w-9 h-9" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.525.02c1.31-.02 2.61-.01 3.91.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.95v7.4c-.01 1.96-.81 3.84-2.25 5.2-1.4 1.32-3.3 2.1-5.26 2.06-2.13-.02-4.14-.99-5.5-2.61-1.35-1.62-2-3.78-1.74-5.88.27-2.02 1.34-3.82 2.97-5.02 1.6-1.17 3.63-1.67 5.61-1.39l.13 4.12c-1.18-.3-2.48-.12-3.53.48-1.03.58-1.77 1.65-1.85 2.85-.09 1.25.4 2.5 1.32 3.32 1.05.9 2.55 1.15 3.88.8 1.37-.36 2.55-1.34 3.04-2.67.24-.65.34-1.36.32-2.06V.03l-1.22-.01z" fill="#000000"/><path d="M12.525.02v8.94c-1.44-.05-2.89-.35-4.2-.97v4.12c1.05.9 2.55 1.15 3.88.8v6.92c-1.35-1.62-2-3.78-1.74-5.88l-4.11-2.92c-1.4 1.32-3.3 2.1-5.26 2.06-2.13-.02-4.14-.99-5.5-2.61v4.06c.27-2.02 1.34-3.82 2.97-5.02 1.6-1.17 3.63-1.67 5.61-1.39z" fill="#FE2C55"/><path d="M12.525.02v8.94c.57-.26 1.1-.59 1.62-.95v6.52c.24-.65.34-1.36.32-2.06v-7.14c-1.18-.3-2.48-.12-3.53.48l1.59 4.17z" fill="#25F4EE"/></svg>
               <span className="text-lg font-bold tracking-tight text-slate-400">TikTok</span>
             </div>
             <div className="flex items-center gap-3 text-[#FF0000]">
               <YoutubeOutlined className="text-[40px]"/>
               <span className="text-lg font-bold tracking-tight text-slate-400">YouTube</span>
             </div>
             <div className="flex items-center gap-3 text-[#1877F2]">
               <FacebookFilled className="text-[36px]"/>
               <span className="text-lg font-bold tracking-tight text-slate-400">Facebook</span>
             </div>
             <div className="flex items-center gap-3 text-[#1DA1F2]">
               <TwitterOutlined className="text-[40px]"/>
               <span className="text-lg font-bold tracking-tight text-slate-400">Twitter / X</span>
             </div>
             <div className="flex items-center gap-3 text-[#0088CC]">
               <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" alt="Telegram" className="w-[38px] h-[38px]" />
               <span className="text-lg font-bold tracking-tight text-slate-400">Telegram</span>
             </div>
             <div className="flex items-center gap-3 text-[#1DB954]">
               <img src="https://upload.wikimedia.org/wikipedia/commons/2/26/Spotify_logo_with_text.svg" alt="Spotify" className="h-10 opacity-60" />
             </div>
          </div>

          {/* STATS ROW */}
          <div className="flex justify-center mb-16">
            <div className="inline-flex flex-wrap justify-center items-center gap-8 md:gap-14 bg-white/80 backdrop-blur-md rounded-[2.5rem] px-12 md:px-16 py-6 border border-slate-100 shadow-[0_2px_20px_rgba(0,0,0,0.02)]">
               <div className="text-center">
                 <div className="text-2xl md:text-3xl font-black text-[#34D399] mb-1 tracking-tight">50M+</div>
                 <div className="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Orders Delivered</div>
               </div>
               <div className="w-[1px] h-12 bg-slate-100 hidden md:block"></div>
               <div className="text-center">
                 <div className="text-2xl md:text-3xl font-black text-[#34D399] mb-1 tracking-tight">&lt; 30s</div>
                 <div className="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Avg. Delivery Time</div>
               </div>
               <div className="w-[1px] h-12 bg-slate-100 hidden md:block"></div>
               <div className="text-center">
                 <div className="text-2xl md:text-3xl font-black text-[#34D399] mb-1 tracking-tight">$0.001</div>
                 <div className="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Starting Price</div>
               </div>
            </div>
          </div>
        </section>

        {/* SERVICES SECTION */}
        <section id="services" className="pt-16 pb-24 border-t border-slate-100 bg-white">
          <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div className="text-center mb-12">
              <h4 className="text-[10.5px] font-bold text-slate-400 uppercase tracking-[0.15em] mb-8">SERVICES AVAILABLE FOR ALL MAJOR PLATFORMS</h4>
              
              {/* PLATFORMS MINI ROW */}
              <div className="flex flex-wrap justify-center items-center gap-x-8 gap-y-4 mb-16 max-w-4xl mx-auto">
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <InstagramOutlined className="text-[17px] text-pink-600"/>
                  <span className="text-[12.5px] font-bold text-slate-700">Instagram</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.525.02c1.31-.02 2.61-.01 3.91.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.95v7.4c-.01 1.96-.81 3.84-2.25 5.2-1.4 1.32-3.3 2.1-5.26 2.06-2.13-.02-4.14-.99-5.5-2.61-1.35-1.62-2-3.78-1.74-5.88.27-2.02 1.34-3.82 2.97-5.02 1.6-1.17 3.63-1.67 5.61-1.39l.13 4.12c-1.18-.3-2.48-.12-3.53.48-1.03.58-1.77 1.65-1.85 2.85-.09 1.25.4 2.5 1.32 3.32 1.05.9 2.55 1.15 3.88.8 1.37-.36 2.55-1.34 3.04-2.67.24-.65.34-1.36.32-2.06V.03l-1.22-.01z" fill="#000000"/><path d="M12.525.02v8.94c-1.44-.05-2.89-.35-4.2-.97v4.12c1.05.9 2.55 1.15 3.88.8v6.92c-1.35-1.62-2-3.78-1.74-5.88l-4.11-2.92c-1.4 1.32-3.3 2.1-5.26 2.06-2.13-.02-4.14-.99-5.5-2.61v4.06c.27-2.02 1.34-3.82 2.97-5.02 1.6-1.17 3.63-1.67 5.61-1.39z" fill="#FE2C55"/><path d="M12.525.02v8.94c.57-.26 1.1-.59 1.62-.95v6.52c.24-.65.34-1.36.32-2.06v-7.14c-1.18-.3-2.48-.12-3.53.48l1.59 4.17z" fill="#25F4EE"/></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">TikTok</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <YoutubeOutlined className="text-[17px] text-[#FF0000]"/>
                  <span className="text-[12.5px] font-bold text-slate-700">YouTube</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <FacebookFilled className="text-[17px] text-[#1877F2]"/>
                  <span className="text-[12.5px] font-bold text-slate-700">Facebook</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <TwitterOutlined className="text-[17px] text-[#1DA1F2]"/>
                  <span className="text-[12.5px] font-bold text-slate-700">Twitter / X</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" className="w-[17px] h-[17px]"/>
                  <span className="text-[12.5px] font-bold text-slate-700">Telegram</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg" className="w-[17px] h-[17px]"/>
                  <span className="text-[12.5px] font-bold text-slate-700">Spotify</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px] text-slate-800" viewBox="0 0 24 24" fill="currentColor"><path d="M15.429 7.625c-1.02-.02-2.247.382-3.085 1.077-.423-1.07-1.427-1.846-2.585-1.921-1.802-.119-3.418 1.144-3.791 2.923-.274 1.309.28 2.68 1.408 3.332.969.56 2.217.433 3.033-.429.624-.658.825-1.57.575-2.433-.141-.486-.475-.905-.92-1.121-.295-.143-.639-.181-.963-.092.302.348.513.782.573 1.25.086.666-.316 1.305-.956 1.442-.647.16-1.341-.165-1.605-.785-.297-.698-.016-1.536.632-1.928.718-.431 1.666-.279 2.196.386.43.539.522 1.258.283 1.884-.339.89-1.259 1.439-2.198 1.488-1.543.08-3.011-.983-3.238-2.502-.27-1.83 1.047-3.6 2.87-3.792 1.25-.132 2.455.539 3.02 1.665.485-1.157 1.637-1.956 2.898-1.968 2.012-.02 3.754 1.547 3.914 3.551.159 1.99-.958 3.86-2.825 4.391-1.404.4-2.915-.098-3.743-1.246l-1.332 3.125c-.244.571-.787.973-1.411 1.002-.68.031-1.314-.378-1.564-.997-.306-.757-.042-1.633.623-2.083l.89-2.09c-.588.583-1.455.772-2.228.471-.856-.334-1.45-1.171-1.498-2.088-.063-1.206.828-2.3 2.023-2.457 1.16-.153 2.257.625 2.52 1.772.062.274.076.56.03.834l.758-1.782c.44-.047.886.009 1.309.213-.678.113-1.218.665-1.348 1.345-.173.901.35 1.834 1.22 2.158 1.066.398 2.302-.125 2.768-1.144.5-1.085.313-2.383-.518-3.25-.794-.827-2.001-1.144-3.136-.788l1.3-3.048c.178-.035.358-.063.539-.08l.191-.015c1.196-.067 2.333.601 2.822 1.677.38.835.347 1.821-.122 2.628-.487.838-1.385 1.365-2.35 1.387-1.135.026-2.18-.707-2.585-1.761 1.037.669 2.459.67 3.491.02 1.353-.853 1.954-2.569 1.4-4.068-.456-1.233-1.758-2.022-3.078-2.022z" /></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">Threads</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px] text-[#5865F2]" fill="currentColor" viewBox="0 0 127.14 96.36"><path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a67.73,67.73,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.31,60,73.31,53s5-12.74,11.43-12.74S96.2,46,96.12,53,91.08,65.69,84.69,65.69Z"/></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">Discord</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px] text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">LinkedIn</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px] text-[#9146FF]" fill="currentColor" viewBox="0 0 24 24"><path d="M2.149 0l-1.612 4.119v16.836h5.731v3.045h3.224l3.045-3.045h4.657l6.269-6.269v-14.686h-21.314zm19.164 13.612l-3.582 3.582h-5.731l-3.045 3.045v-3.045h-4.836v-15.045h17.194v11.463zm-3.582-7.343v6.262h-2.149v-6.262h2.149zm-5.731 0v6.262h-2.149v-6.262h2.149z"/></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">Twitch</span>
                </div>
                <div className="flex items-center gap-1.5 opacity-90 transition-opacity hover:opacity-100">
                  <svg className="w-[17px] h-[17px] text-[#FF5500]" fill="currentColor" viewBox="0 0 100 100"><path d="M60 41c0-10.5-8.5-19-19-19-8.4 0-15.6 5.5-18.1 13.1-1.1-.1-2.1-.1-3.2-.1-9.9 0-18 8.1-18 18 0 9.8 7.9 17.8 17.7 18h46.6c8.5 0 15.4-6.9 15.4-15.4s-6.9-15.4-15.4-15.4c-.2 0-.4 0-.6 0.1v-0.1c0-0.1 0-0.1 0-0.1z"/></svg>
                  <span className="text-[12.5px] font-bold text-slate-700">SoundCloud</span>
                </div>
              </div>
            </div>
            
            <div className="flex flex-wrap justify-center gap-4 mb-12">
               {['Instagram', 'TikTok', 'YouTube', 'Telegram', 'Facebook'].map(tab => (
                 <button 
                  key={tab} 
                  onClick={() => setActiveTab(tab)}
                  className={`px-8 py-3 rounded text-sm font-bold transition-all border ${activeTab === tab ? 'bg-[#34C759] text-white border-[#34C759]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'}`}
                 >
                   {tab}
                 </button>
               ))}
               <button className="px-8 py-3 rounded text-sm font-bold bg-white text-slate-600 border border-slate-200">
                  More Platforms
               </button>
            </div>

            <div className="grid md:grid-cols-2 gap-16 items-start mt-12">
               <div>
                  <h3 className="text-3xl font-black text-slate-900 mb-4">{activeTab}</h3>
                  <p className="text-slate-500 text-[15px] leading-relaxed mb-6">
                    Boost your {activeTab} with real, high-retention followers, likes, and views. Perfect for personal brands, businesses, and influencers looking to grow fast. All services are safe and delivered via public URL — no password ever needed.
                  </p>
                  <div className="flex flex-wrap gap-2.5 mb-10">
                     {['Followers', 'Likes', 'Views', 'Reels Views', 'Story Views', 'Comments', 'Live Views', 'Saves', 'Shares'].map(tag => (
                        <span key={tag} className="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{tag}</span>
                     ))}
                  </div>
                  <Link href="/register">
                    <button className="bg-[#bcf0da] text-[#137333] font-bold py-3 px-8 rounded text-[15px] inline-flex items-center gap-2 hover:bg-[#a5e9cd] transition-colors shadow-sm">
                      Start ordering <ArrowRightOutlined className="text-sm" />
                    </button>
                  </Link>
               </div>
               <div className="grid grid-cols-2 gap-4">
                  {['Followers', 'Likes', 'Views', 'Comments', 'Reels', 'Story Views'].map((item, idx) => (
                    <div key={idx} className="bg-white border border-slate-200 rounded-xl p-8 text-center flex flex-col items-center justify-center shadow-sm hover:border-slate-300 transition-all">
                       <div className="w-12 h-12 border-2 border-slate-900 rounded-[10px] flex items-center justify-center mb-4">
                         <div className="w-4 h-4 border-2 border-slate-900 rounded-full"></div>
                       </div>
                       <span className="font-bold text-slate-900 text-sm">{item}</span>
                    </div>
                  ))}
               </div>
            </div>
          </div>
        </section>

        {/* WHY CHOOSE US */}
        <section id="features" className="py-24 bg-[#FAFAFA] border-t border-slate-200 relative">
          <div className="container mx-auto px-6 lg:px-12 max-w-6xl relative z-10">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-black text-[#0F172A] mb-4 tracking-tight">Built for people who take social media seriously</h2>
            </div>
            
            <div className="grid md:grid-cols-2 gap-6">
              {services.map((item, idx) => (
                <div key={idx} className="bg-white border border-slate-200 rounded-2xl p-8 flex gap-5">
                  <div className="w-10 h-10 shrink-0 flex items-center justify-center text-xl text-[#34C759]">
                    {item.icon}
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-slate-900 mb-2">{item.title}</h3>
                    <p className="text-slate-500 text-sm leading-relaxed">{item.desc}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* HOW IT WORKS */}
        <section id="workflow" className="py-24 bg-white border-t border-slate-200">
          <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div className="text-center mb-16 max-w-3xl mx-auto">
              <h2 className="text-3xl md:text-4xl font-black text-[#0F172A] mb-4 tracking-tight">4 steps. Under 5 minutes. Seriously.</h2>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              {steps.map((step, idx) => (
                <div key={idx} className="bg-white border border-slate-200 rounded-2xl p-8">
                  <div className="w-10 h-10 mb-8 mt-2 text-2xl text-[#34C759] flex items-center">
                    {step.icon}
                  </div>
                  <h4 className="text-slate-400 font-bold uppercase tracking-widest text-[10px] mb-2">STEP {step.num}</h4>
                  <h3 className="text-[17px] font-bold text-slate-900 mb-3">{step.title}</h3>
                  <p className="text-slate-500 text-[13px] leading-relaxed">{step.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>

      {/* FOOTER */}
      <footer className="bg-[#FAFAFA] border-t border-slate-200 pt-16 pb-12 relative z-10">
        <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
          <div className="grid md:grid-cols-4 gap-12 mb-16">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center gap-2 mb-6">
                {logoUrl ? (
                  <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 w-auto object-contain" />
                ) : (
                  <div className="text-2xl font-black text-slate-900 tracking-tight">
                    <DashboardOutlined className="text-[#3EDB7F] mr-2" /> {config.title || 'PRO SMM'}
                  </div>
                )}
              </div>
              <p className="text-slate-500 text-sm max-w-[280px] leading-relaxed">
                The #1 SMM panel for serious growth. We provide high-speed, premium quality social media marketing services at wholesale prices.
              </p>
            </div>
            
            <div>
              <h4 className="text-slate-900 font-bold mb-6 text-sm">Platform</h4>
              <ul className="space-y-4 text-slate-500 text-sm">
                <li><Link href="/services" className="hover:text-[#3EDB7F] transition-colors">Services</Link></li>
                <li><Link href="/apidoc" className="hover:text-[#3EDB7F] transition-colors">API Documentation</Link></li>
                <li><Link href="/register" className="hover:text-[#3EDB7F] transition-colors">Sign Up</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-slate-900 font-bold mb-6 text-sm">Legal & Support</h4>
              <ul className="space-y-4 text-slate-500 text-sm">
                <li><Link href="/login" className="hover:text-[#3EDB7F] transition-colors">Log in</Link></li>
                <li><a href="#" className="hover:text-[#3EDB7F] transition-colors">Terms of Service</a></li>
                <li><a href="#" className="hover:text-[#3EDB7F] transition-colors">Privacy Policy</a></li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
