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
  FundViewOutlined
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
    <div className="min-h-screen bg-[#FAFAFA] text-slate-800 font-sans selection:bg-green-500 selection:text-white relative overflow-x-hidden">
      {/* GLOBAL BACKGROUND GRID */}
      <div className="fixed inset-0 pointer-events-none z-0 opacity-[0.03]" style={{ backgroundImage: 'linear-gradient(#000 1px, transparent 1px), linear-gradient(90deg, #000 1px, transparent 1px)', backgroundSize: '60px 60px' }} />

      {/* HEADER */}
      <header className="fixed top-0 w-full z-50 bg-[#FAFAFA]/90 backdrop-blur-xl border-b border-slate-200 transition-all h-20 flex items-center">
        <div className="container mx-auto px-6 lg:px-12 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-3 relative z-10 w-[200px]">
            {logoUrl ? (
              <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 w-auto object-contain" />
            ) : (
              <div className="flex items-center gap-2 text-2xl font-black text-slate-900 tracking-tight">
                <div className="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white">
                  <DashboardOutlined />
                </div>
                {config.title || 'PRO SMM'}
              </div>
            )}
          </Link>
          <nav className="hidden md:flex items-center justify-center gap-8 font-semibold text-[13px] text-slate-500 z-10 flex-1">
            <a href="#services" className="hover:text-slate-900 transition-colors">Services</a>
            <a href="#features" className="hover:text-slate-900 transition-colors">Features</a>
            <a href="#workflow" className="hover:text-slate-900 transition-colors">How It Works</a>
            <a href="#faq" className="hover:text-slate-900 transition-colors">FAQ</a>
          </nav>
          <div className="flex items-center justify-end gap-6 z-10 w-[200px]">
            <button className="hidden sm:block text-slate-500 hover:text-slate-900 transition-colors text-lg">
              <MoonOutlined />
            </button>
            <Link href="/login" className="hidden sm:block text-slate-900 font-bold hover:text-green-600 transition-colors text-[13px]">Log in</Link>
            <Link href="/register">
              <button className="bg-green-100/50 text-green-600 border border-green-200/50 hover:bg-green-100 hover:border-green-300 font-bold py-2 px-5 rounded-md transition-all duration-300 text-[13px]">
                Get Started
              </button>
            </Link>
          </div>
        </div>
      </header>

      <main className="relative z-10 pt-32 pb-20 lg:pt-40 lg:pb-32">
        {/* HERO SECTION */}
        <section className="container mx-auto px-6 lg:px-12 text-center max-w-[900px]">
          <div className="inline-flex items-center gap-2 bg-slate-100 border border-slate-200 rounded-full px-4 py-1.5 mb-8 shadow-sm">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <span className="text-[11px] font-semibold text-slate-500 tracking-wide">Processing orders right now — every 0.2s</span>
          </div>
          
          <h1 className="text-6xl md:text-[80px] font-black text-[#0F172A] leading-[1.05] mb-8 tracking-tight">
            The #1 SMM panel for <br />
            <span className="text-green-500">serious growth</span>
          </h1>
          
          <p className="text-lg md:text-[17px] text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            {config.title || 'Slike.vn'} là SMM Panel Việt Nam uy tín, cung cấp dịch vụ tăng like, follow, view, comment, share cho Facebook, TikTok, Instagram, YouTube và nhiều nền tảng mạng xã hội khác. Hệ thống xử lý nhanh, giá rẻ, dễ sử dụng, hỗ trợ 24/7, phù hợp cho cá nhân, doanh nghiệp, marketer và đại lý cần tăng tương tác mạng xã hội hiệu quả.
          </p>
          
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
            <Link href="/register">
              <button className="w-full sm:w-auto bg-green-50 border border-green-200 text-green-600 text-[15px] font-bold py-3.5 px-8 rounded-full transition-all hover:bg-green-100 flex items-center justify-center gap-2">
                Get started for free <ArrowRightOutlined className="text-xs" />
              </button>
            </Link>
            <Link href="#services">
              <button className="w-full sm:w-auto bg-white hover:bg-slate-50 border border-slate-200 text-slate-900 text-[15px] font-bold py-3.5 px-8 rounded-full transition-all">
                Explore Services
              </button>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-[11px] font-bold text-slate-500 mb-20 uppercase tracking-widest">
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-green-500" /> No credit card required</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-green-500" /> Instant activation</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-green-500" /> Cancel anytime</div>
            <div className="flex items-center gap-1.5"><CheckCircleFilled className="text-green-500" /> 24/7 live support</div>
          </div>

          {/* BRANDS ROW */}
          <div className="flex flex-wrap justify-center items-center gap-8 md:gap-14 opacity-50 grayscale hover:grayscale-0 transition duration-500 mb-20">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" className="h-6 object-contain" alt="Instagram" />
            <img src="https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg" className="h-6 object-contain" alt="Tiktok" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg" className="h-6 object-contain" alt="YouTube" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" className="h-6 object-contain" alt="Facebook" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Twitter_new_X_logo.png" className="h-5 object-contain" alt="X" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" className="h-6 object-contain" alt="Telegram" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/f/ff/Spotify_Logo_2019.svg" className="h-6 object-contain" alt="Spotify" />
          </div>

          {/* STATS ROW */}
          <div className="flex flex-wrap justify-center items-center gap-16 border-t border-slate-200 pt-12 max-w-2xl mx-auto">
             <div className="text-center">
               <div className="text-2xl font-black text-green-500 mb-1">50M+</div>
               <div className="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Orders Delivered</div>
             </div>
             <div className="text-center">
               <div className="text-2xl font-black text-green-500 mb-1">&lt; 30s</div>
               <div className="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Avg Delivery Time</div>
             </div>
             <div className="text-center">
               <div className="text-2xl font-black text-green-500 mb-1">$0.001</div>
               <div className="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Starting Price</div>
             </div>
          </div>
        </section>

        {/* SERVICES SECTION */}
        <section id="services" className="py-24 border-t border-slate-200 bg-white mt-10">
          <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
            <div className="text-center mb-10">
              <h4 className="text-green-500 font-bold uppercase tracking-widest text-[11px] mb-4">OUR SERVICES</h4>
              <h2 className="text-4xl md:text-[44px] font-black text-[#0F172A] mb-4 tracking-tight">Growth services for every major platform</h2>
              <p className="text-[17px] text-slate-500">We cover all major social media networks with platform-specific services. Pick a platform <br/> below to see what we offer.</p>
            </div>
            
            <div className="flex flex-wrap justify-center gap-3 mb-12">
               {['Instagram', 'TikTok', 'YouTube', 'Telegram', 'Facebook'].map(tab => (
                 <button 
                  key={tab} 
                  onClick={() => setActiveTab(tab)}
                  className={`px-5 py-2 rounded-full font-bold text-sm transition-all border ${activeTab === tab ? 'bg-green-500 text-white border-green-500' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'}`}
                 >
                   {tab}
                 </button>
               ))}
               <button className="px-5 py-2 rounded-full font-bold text-sm bg-white text-slate-600 border border-slate-200">
                  More Platforms
               </button>
            </div>

            <div className="grid md:grid-cols-2 gap-12 items-start mt-12">
               <div>
                  <h3 className="text-2xl font-black text-slate-900 mb-4">{activeTab}</h3>
                  <p className="text-slate-500 text-lg leading-relaxed mb-6">
                    Boost your {activeTab} with real, high-retention followers, likes, and views. Perfect for personal brands, businesses, and influencers looking to grow fast. All services are safe and delivered via public URL — no password ever needed.
                  </p>
                  <div className="flex flex-wrap gap-2 mb-8">
                     {['Followers', 'Likes', 'Views', 'Reels Views', 'Story Views', 'Comments', 'Live Views', 'Saves', 'Shares'].map(tag => (
                        <span key={tag} className="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{tag}</span>
                     ))}
                  </div>
                  <Link href="/register">
                    <button className="bg-green-100 border border-green-200 text-green-600 font-bold py-2.5 px-6 rounded-full text-sm inline-flex items-center gap-2 hover:bg-green-50 transition-colors">
                      Start ordering <ArrowRightOutlined className="text-xs" />
                    </button>
                  </Link>
               </div>
               <div className="grid grid-cols-2 gap-4">
                  {['Followers', 'Likes', 'Views', 'Comments', 'Reels', 'Story Views'].map((item, idx) => (
                    <div key={idx} className="bg-white border border-slate-200 rounded-2xl p-6 text-center flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow">
                       <div className="w-10 h-10 border-2 border-slate-900 rounded-lg flex items-center justify-center mb-3">
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
              <h4 className="text-green-500 font-bold uppercase tracking-widest text-[11px] mb-4">WHY CHOOSE US</h4>
              <h2 className="text-4xl md:text-[44px] font-black text-[#0F172A] mb-4 tracking-tight">Built for people who take social media seriously</h2>
              <p className="text-[17px] text-slate-500">We're not just another panel — we're the direct provider behind many panels. That means <br/> lower prices, better quality, and faster delivery.</p>
            </div>
            
            <div className="grid md:grid-cols-2 gap-6">
              {services.map((item, idx) => (
                <div key={idx} className="bg-white border border-slate-100 rounded-2xl p-8 flex gap-6 hover:shadow-lg transition-shadow shadow-sm">
                  <div className="w-12 h-12 shrink-0 rounded-full bg-green-50 flex items-center justify-center text-xl text-green-500 border border-green-100">
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
              <h2 className="text-4xl md:text-[44px] font-black text-[#0F172A] mb-4 tracking-tight">4 steps. Under 5 minutes. Seriously.</h2>
              <p className="text-[17px] text-slate-500">No complicated setup, no technical knowledge needed. Just sign up, fund your account, <br/> and start growing your social media presence.</p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
              {steps.map((step, idx) => (
                <div key={idx} className="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                  <div className="absolute top-6 right-6 text-7xl font-black text-slate-50/50 group-hover:text-green-50/50 transition-colors z-0 select-none tracking-tighter">
                    {step.num}
                  </div>
                  <div className="relative z-10 w-10 h-10 mb-8 mt-2 text-2xl text-slate-800 flex items-center">
                    {step.icon}
                  </div>
                  <h4 className="relative z-10 text-green-500 font-black uppercase tracking-widest text-[9px] mb-2">STEP {step.num}</h4>
                  <h3 className="relative z-10 text-xl font-bold text-slate-900 mb-3">{step.title}</h3>
                  <p className="relative z-10 text-slate-500 text-[13px] leading-relaxed">{step.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* TWO COLS: WHAT IS / WHY USE */}
        <section className="py-24 bg-[#FAFAFA] border-t border-slate-200">
          <div className="container mx-auto px-6 lg:px-12 max-w-5xl">
            <div className="bg-white border border-slate-200 rounded-[2rem] p-8 md:p-14 shadow-sm flex flex-col md:flex-row gap-12">
              <div className="flex-1">
                <h4 className="text-green-500 font-bold uppercase tracking-widest text-[11px] mb-3">LEARN MORE</h4>
                <h3 className="text-[34px] font-black text-slate-900 mb-6 tracking-tight">What is an SMM Panel?</h3>
                <div className="space-y-4 text-slate-600 text-sm leading-relaxed">
                  <p>An SMM Panel (Social Media Marketing Panel) is an online platform where businesses, marketers, influencers, and resellers can purchase social media services — followers, likes, views, comments — all in one place.</p>
                  <p>Think of it as a wholesale marketplace for social media growth. Instead of running expensive ad campaigns or waiting months for organic growth, you can boost your presence instantly at a fraction of the cost.</p>
                  <p>Our panel connects you directly to the service provider — no middlemen, no extra markup. That's why our prices are the lowest you'll find anywhere.</p>
                </div>
              </div>
              <div className="flex-1 pt-6">
                <h4 className="font-bold text-slate-900 mb-6">Why do people use SMM Panels?</h4>
                <ul className="space-y-4">
                  {[
                    "Kickstart new accounts and get past the 'zero follower' problem",
                    'Boost posts and videos to trigger algorithm promotion',
                    'Build social proof for brands, businesses, and personal profiles',
                    'Resell services to clients at higher margins — build an SMM business',
                    'Save time — compress days of manual growth into minutes',
                    'A/B test engagement strategies without waiting weeks for results'
                  ].map((text, i) => (
                    <li key={i} className="flex items-start gap-3">
                      <div className="min-w-[16px] mt-0.5 text-green-500">
                        <CheckCircleFilled className="text-sm" />
                      </div>
                      <span className="text-slate-600 text-[13px]">{text}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </section>

        {/* CTA CARD */}
        <section className="py-24 bg-white">
          <div className="container mx-auto px-6 lg:px-12 text-center">
             <div className="max-w-2xl mx-auto">
                <h2 className="text-4xl md:text-[44px] font-black text-[#0F172A] mb-8 tracking-tight">Ready to grow your social media?</h2>
                <Link href="/register">
                  <button className="bg-green-500 hover:bg-green-400 text-white text-[15px] font-bold py-4 px-10 rounded-full transition-all shadow-lg shadow-green-500/20">
                    Create free account
                  </button>
                </Link>
             </div>
          </div>
        </section>
      </main>

      {/* FOOTER */}
      <footer className="bg-white border-t border-slate-200 pt-16 pb-12 relative z-10">
        <div className="container mx-auto px-6 lg:px-12 max-w-6xl">
          <div className="grid md:grid-cols-4 gap-12 mb-16">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center gap-2 mb-6">
                {logoUrl ? (
                  <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 w-auto object-contain" />
                ) : (
                  <div className="text-2xl font-black text-slate-900 tracking-tight">
                    <DashboardOutlined className="text-green-500 mr-2" /> {config.title || 'PRO SMM'}
                  </div>
                )}
              </div>
              <p className="text-slate-500 text-sm max-w-[250px] leading-relaxed">
                The #1 SMM panel for serious growth. We provide high-speed, premium quality social media marketing services at wholesale prices.
              </p>
            </div>
            
            <div>
              <h4 className="text-slate-900 font-bold mb-6 text-sm">Platform</h4>
              <ul className="space-y-4 text-slate-500 text-[13px]">
                <li><Link href="/services" className="hover:text-green-500 transition-colors">Services</Link></li>
                <li><Link href="/api" className="hover:text-green-500 transition-colors">API Documentation</Link></li>
                <li><Link href="/register" className="hover:text-green-500 transition-colors">Sign Up</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-slate-900 font-bold mb-6 text-sm">Legal & Support</h4>
              <ul className="space-y-4 text-slate-500 text-[13px]">
                <li><a href="#" className="hover:text-green-500 transition-colors">FAQ</a></li>
                <li><a href="#" className="hover:text-green-500 transition-colors">Terms of Service</a></li>
                <li><a href="#" className="hover:text-green-500 transition-colors">Privacy Policy</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-xs font-medium">
            <div>© {new Date().getFullYear()} {config.title || 'Slike.vn'}. All rights reserved.</div>
            <div className="flex gap-6">
               <a href="#" className="hover:text-slate-600 transition-colors">Terms</a>
               <a href="#" className="hover:text-slate-600 transition-colors">Privacy</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
