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

  useEffect(() => {
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
          <Link href="/" className="flex items-center gap-3 relative z-10 w-[200px]">
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
          <div className="flex items-center justify-end gap-6 z-10 w-[200px]">
            <Link href="/login" className="hidden sm:block text-blue-500 font-bold hover:text-blue-600 transition-colors text-[14px]">Log in</Link>
            <Link href="/register">
              <button className="bg-[#e6f4ea] text-[#137333] hover:bg-[#d4edd9] font-bold py-2.5 px-5 rounded transition-all duration-300 text-[14px]">
                Get Started
              </button>
            </Link>
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
          
          <p className="text-lg md:text-[17px] text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            {config.title || 'Slike.vn'} là SMM Panel Việt Nam uy tín, cung cấp dịch vụ tăng like, follow, view, comment, share cho Facebook, TikTok, Instagram, YouTube và nhiều nền tảng mạng xã hội khác. Hệ thống xử lý nhanh, giá rẻ, dễ sử dụng, hỗ trợ 24/7.
          </p>
          
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
            <Link href="/register">
              <button className="w-full sm:w-auto bg-[#e6f4ea] text-[#137333] text-[15px] font-bold py-3.5 px-8 rounded transition-all hover:bg-[#d4edd9] flex items-center justify-center gap-2">
                Get started for free <ArrowRightOutlined className="text-xs" />
              </button>
            </Link>
            <Link href="#services">
              <button className="w-full sm:w-auto bg-white hover:bg-slate-50 border border-slate-200 text-slate-900 text-[15px] font-bold py-3.5 px-8 rounded transition-all">
                Explore Services
              </button>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-[11px] font-bold text-slate-500 mb-20 uppercase tracking-widest">
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#34D399]" /> No credit card required</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#34D399]" /> Instant activation</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-[#34D399]" /> Cancel anytime</div>
          </div>

          {/* BRANDS ROW - Using simple SVG-like Ant icons */}
          <div className="flex flex-wrap justify-center items-center gap-10 md:gap-16 text-slate-400 opacity-60 hover:opacity-100 transition duration-500 mb-20 text-3xl">
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors"><InstagramOutlined /></div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors" style={{ fill: 'currentColor' }}>
                <svg width="32" height="32" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12.525.02c1.31-.02 2.61-.01 3.91.01.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.95v7.4c-.01 1.96-.81 3.84-2.25 5.2-1.4 1.32-3.3 2.1-5.26 2.06-2.13-.02-4.14-.99-5.5-2.61-1.35-1.62-2-3.78-1.74-5.88.27-2.02 1.34-3.82 2.97-5.02 1.6-1.17 3.63-1.67 5.61-1.39l.13 4.12c-1.18-.3-2.48-.12-3.53.48-1.03.58-1.77 1.65-1.85 2.85-.09 1.25.4 2.5 1.32 3.32 1.05.9 2.55 1.15 3.88.8 1.37-.36 2.55-1.34 3.04-2.67.24-.65.34-1.36.32-2.06V.03l-1.22-.01z" fill="currentColor"/>
                </svg>
                <span className="text-xl font-bold tracking-tighter">TikTok</span>
             </div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors"><YoutubeOutlined /></div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors"><FacebookFilled /></div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors"><TwitterOutlined /></div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
             </div>
             <div className="flex items-center gap-2 hover:text-[#555] transition-colors">
                <span className="text-xl font-bold tracking-tighter">Spotify</span>
             </div>
          </div>
        </section>

        {/* SERVICES SECTION */}
        <section id="services" className="py-24 border-t border-slate-100 bg-white">
          <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div className="text-center mb-10">
              <h2 className="text-sm font-semibold text-slate-500 mb-6">We cover all major social media networks with platform-specific services. Pick a platform <br/> below to see what we offer.</h2>
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
                <li><Link href="/api" className="hover:text-[#3EDB7F] transition-colors">API Documentation</Link></li>
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
