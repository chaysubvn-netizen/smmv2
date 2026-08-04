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
  StarFilled,
  ArrowRightOutlined,
  DashboardOutlined,
  HeartFilled,
  SettingOutlined,
  UsergroupAddOutlined,
  TrophyOutlined
} from '@ant-design/icons';
import api from '@/lib/axios';

const services = [
  { title: 'Processing 24/7', desc: 'Our automated system never sleeps. Orders start processing instantly.', icon: <SettingOutlined /> },
  { title: '100% Secure', desc: 'No passwords required. Your account safety is our top priority.', icon: <SafetyCertificateOutlined /> },
  { title: 'Cheapest Prices', desc: 'Direct provider sourcing allows us to offer the most competitive market rates.', icon: <LineChartOutlined /> },
  { title: 'API for Resellers', desc: 'Seamlessly connect your own panel to our services with our robust API.', icon: <ThunderboltFilled /> },
  { title: 'Global Reach', desc: 'High quality services for users worldwide across all major platforms.', icon: <GlobalOutlined /> },
  { title: 'Live Support', desc: 'Dedicated support team ready to assist you anytime via ticket or chat.', icon: <CustomerServiceOutlined /> }
];

const steps = [
  { num: '01', title: 'Create Account', desc: 'Sign up for free in less than 30 seconds. No credit card required.' },
  { num: '02', title: 'Add Funds', desc: 'Load your balance safely using Cards, Crypto, or Bank Transfer.' },
  { num: '03', title: 'Choose Service', desc: 'Paste your link, select category and submit your order.' },
  { num: '04', title: 'Track & Grow', desc: 'Watch your metrics skyrocket instantly in real-time.' }
];

export default function LandingDefaultPage() {
  const [config, setConfig] = useState<any>({ title: 'SMM Panel' });

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
    <div className="min-h-screen bg-[#0B0F19] text-gray-300 font-sans selection:bg-green-500 selection:text-white relative overflow-x-hidden">
      {/* GLOBAL BACKGROUND GRID & GLOWS */}
      <div className="fixed inset-0 pointer-events-none z-0 opacity-20" style={{ backgroundImage: 'linear-gradient(#222 1px, transparent 1px), linear-gradient(90deg, #222 1px, transparent 1px)', backgroundSize: '40px 40px' }} />
      <div className="fixed top-[-20%] left-[-10%] w-[600px] h-[600px] bg-green-500/20 blur-[120px] rounded-full pointer-events-none z-0" />
      <div className="fixed bottom-1/4 right-[-10%] w-[500px] h-[500px] bg-emerald-500/10 blur-[100px] rounded-full pointer-events-none z-0" />

      {/* HEADER */}
      <header className="fixed top-0 w-full z-50 bg-[#0B0F19]/80 backdrop-blur-xl border-b border-gray-800 transition-all h-20 flex items-center">
        <div className="container mx-auto px-6 lg:px-12 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-3 relative z-10">
            {logoUrl ? (
              <img src={logoUrl} alt={config.title || 'Logo'} className="h-9 w-auto object-contain brightness-0 invert" />
            ) : (
              <div className="flex items-center gap-2 text-2xl font-black text-white tracking-tight">
                <div className="w-9 h-9 rounded-lg bg-green-500 flex items-center justify-center text-black">
                  <DashboardOutlined />
                </div>
                {config.title || 'PRO SMM'}
              </div>
            )}
          </Link>
          <nav className="hidden md:flex items-center gap-8 font-semibold text-sm text-gray-400 z-10">
            <a href="#services" className="hover:text-white transition-colors">Services</a>
            <a href="#features" className="hover:text-white transition-colors">Features</a>
            <a href="#workflow" className="hover:text-white transition-colors">How it works</a>
            <a href="#faq" className="hover:text-white transition-colors">FAQ</a>
          </nav>
          <div className="flex items-center gap-6 z-10">
            <Link href="/login" className="hidden sm:block text-gray-400 font-bold hover:text-white transition-colors text-sm">Log in</Link>
            <Link href="/register">
              <button className="bg-transparent border border-green-500 text-green-400 hover:bg-green-500 hover:text-black font-bold py-2 px-6 rounded-full transition-all duration-300 text-sm shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-[0_0_25px_rgba(34,197,94,0.6)]">
                Get Started
              </button>
            </Link>
          </div>
        </div>
      </header>

      <main className="relative z-10 pt-32 pb-20 lg:pt-48 lg:pb-32">
        {/* HERO SECTION */}
        <section className="container mx-auto px-6 lg:px-12 text-center max-w-5xl">
          <div className="inline-flex items-center gap-2 bg-[#1a202c] border border-gray-700/50 rounded-full px-4 py-1.5 mb-8 shadow-sm">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <span className="text-xs font-semibold text-gray-300 uppercase tracking-widest">Processing orders right now — every 0.2s</span>
          </div>
          
          <h1 className="text-5xl md:text-7xl font-black text-white leading-[1.1] mb-8 tracking-tight">
            The #1 SMM panel for <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-300">serious growth.</span>
          </h1>
          
          <p className="text-lg md:text-xl text-gray-400 mb-12 max-w-2xl mx-auto leading-relaxed">
            Built for people who take social media seriously. Increase your engagement, build authority, and scale your brand with the world's most advanced automated platform.
          </p>
          
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
            <Link href="/register">
              <button className="w-full sm:w-auto bg-green-500 text-black text-lg font-bold py-4 px-10 rounded-full shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:shadow-[0_0_30px_rgba(34,197,94,0.6)] transition-all hover:scale-105 flex items-center justify-center gap-2">
                Get started for free <ArrowRightOutlined />
              </button>
            </Link>
            <Link href="#services">
              <button className="w-full sm:w-auto bg-[#1a202c] hover:bg-[#2d3748] border border-gray-700 text-white text-lg font-bold py-4 px-10 rounded-full transition-all">
                Explore Services
              </button>
            </Link>
          </div>

          <div className="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-sm font-semibold text-gray-400 mb-20">
            <div className="flex items-center gap-2"><CheckCircleFilled className="text-green-500" /> No credit card required</div>
            <div className="flex items-center gap-2"><CheckCircleFilled className="text-green-500" /> Instant activation</div>
            <div className="flex items-center gap-2"><CheckCircleFilled className="text-green-500" /> Cancel anytime</div>
          </div>

          {/* STATS ROW */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
             <div className="bg-[#111827] border border-gray-800 rounded-2xl p-6 text-center hover:border-green-500/50 transition-colors">
               <div className="text-3xl font-black text-white mb-1">50M+</div>
               <div className="text-xs text-gray-500 uppercase font-bold tracking-wider">Orders Delivered</div>
             </div>
             <div className="bg-[#111827] border border-gray-800 rounded-2xl p-6 text-center hover:border-green-500/50 transition-colors">
               <div className="text-3xl font-black text-white mb-1">&lt; 30s</div>
               <div className="text-xs text-gray-500 uppercase font-bold tracking-wider">Avg Delivery Time</div>
             </div>
             <div className="bg-[#111827] border border-gray-800 rounded-2xl p-6 text-center hover:border-green-500/50 transition-colors">
               <div className="text-3xl font-black text-white mb-1">99.9%</div>
               <div className="text-xs text-gray-500 uppercase font-bold tracking-wider">Uptime Server</div>
             </div>
             <div className="bg-[#111827] border border-gray-800 rounded-2xl p-6 text-center hover:border-green-500/50 transition-colors">
               <div className="text-3xl font-black text-white mb-1">4.9/5</div>
               <div className="text-xs text-gray-500 uppercase font-bold tracking-wider">Customer Rating</div>
             </div>
          </div>
        </section>

        {/* LOGOS RUNNER */}
        <section className="py-20 border-y border-gray-800/50 bg-[#0B0F19]/50 overflow-hidden relative">
          <div className="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-[#0B0F19] to-transparent z-10" />
          <div className="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-[#0B0F19] to-transparent z-10" />
          <div className="container mx-auto px-6">
            <div className="flex justify-center items-center gap-12 md:gap-20 opacity-40 grayscale hover:grayscale-0 transition duration-500 flex-wrap">
               <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" className="h-8 object-contain" alt="Instagram" />
               <img src="https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg" className="h-8 object-contain" alt="Tiktok" />
               <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Twitter_new_X_logo.png" className="h-6 object-contain filter invert" alt="X" />
               <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" className="h-8 object-contain" alt="Facebook" />
               <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" className="h-8 object-contain" alt="Telegram" />
               <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg" className="h-8 object-contain" alt="YouTube" />
            </div>
          </div>
        </section>

        {/* WHY CHOOSE US */}
        <section id="features" className="py-32">
          <div className="container mx-auto px-6 lg:px-12">
            <div className="text-center mb-16">
              <h4 className="text-green-500 font-bold uppercase tracking-widest text-sm mb-4">WHY CHOOSE US</h4>
              <h2 className="text-4xl md:text-5xl font-black text-white">Built for anyone serious about <br/>social media growth</h2>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {services.map((item, idx) => (
                <div key={idx} className="bg-[#111827] border border-gray-800 rounded-3xl p-8 hover:bg-[#1a2333] transition-colors group">
                  <div className="w-14 h-14 rounded-full bg-gray-900 border border-gray-700 flex items-center justify-center text-xl text-green-400 mb-6 group-hover:scale-110 group-hover:border-green-500 transition-all">
                    {item.icon}
                  </div>
                  <h3 className="text-xl font-bold text-white mb-3">{item.title}</h3>
                  <p className="text-gray-400 font-medium leading-relaxed">{item.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* HOW IT WORKS */}
        <section id="workflow" className="py-32 bg-[#0d1321] border-y border-gray-800">
          <div className="container mx-auto px-6 lg:px-12">
            <div className="text-center mb-20 max-w-3xl mx-auto">
              <h4 className="text-green-500 font-bold uppercase tracking-widest text-sm mb-4">HOW IT WORKS</h4>
              <h2 className="text-4xl md:text-5xl font-black text-white mb-6">4 steps. Under 3 minutes. Seriously.</h2>
              <p className="text-xl text-gray-400">Our platform is designed to be as frictionless as possible.</p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
              {steps.map((step, idx) => (
                <div key={idx} className="relative p-6 group">
                  <div className="absolute top-0 right-4 text-7xl font-black text-gray-800/40 select-none group-hover:text-green-500/10 transition-colors">{step.num}</div>
                  <h3 className="relative text-xl font-bold text-white mb-4 mt-8 z-10">{step.title}</h3>
                  <p className="relative text-gray-400 z-10">{step.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* TWO COLS: WHAT IS / WHY USE */}
        <section className="py-32">
          <div className="container mx-auto px-6 lg:px-12">
            <div className="grid lg:grid-cols-2 gap-12">
              <div className="bg-[#111827] border border-gray-800 rounded-3xl p-10 md:p-14">
                <h3 className="text-3xl font-black text-white mb-6">What is an SMM Panel?</h3>
                <p className="text-gray-400 text-lg leading-relaxed mb-6">
                  An SMM (Social Media Marketing) panel is an online store that sells comprehensive social media marketing services at highly competitive wholesale prices.
                </p>
                <p className="text-gray-400 text-lg leading-relaxed">
                  People use SMM panels to quickly boost engagement, grow followings, and build social proof—a critical element in today's digital landscape.
                </p>
              </div>
              <div className="bg-[#111827] border border-gray-800 rounded-3xl p-10 md:p-14">
                <h3 className="text-3xl font-black text-white mb-6">Why do people use us?</h3>
                <ul className="space-y-4">
                  {[
                    'Extremely cheap pricing for high quantity',
                    'Fully automated API for instant delivery',
                    'Multiple secure payment gateways available',
                    'Drip-feed functionality for organic looking growth'
                  ].map((text, i) => (
                    <li key={i} className="flex items-start gap-4">
                      <div className="mt-1 min-w-[24px] h-6 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center text-xs border border-green-500/30">
                        <CheckCircleFilled />
                      </div>
                      <span className="text-gray-300 text-lg">{text}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </section>

        {/* CTA CARD */}
        <section className="py-20">
          <div className="container mx-auto px-6 lg:px-12 text-center">
            <div className="bg-gradient-to-br from-[#131d2e] to-[#0a0f18] border border-gray-700 rounded-[3rem] p-12 md:p-24 relative overflow-hidden shadow-2xl">
              <div className="absolute top-0 right-0 w-96 h-96 bg-green-500 rounded-full blur-[150px] opacity-20 pointer-events-none" />
              <div className="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500 rounded-full blur-[150px] opacity-10 pointer-events-none" />
              
              <div className="relative z-10 max-w-3xl mx-auto">
                <h2 className="text-4xl md:text-6xl font-black text-white mb-6">Ready to dominate?</h2>
                <p className="text-xl text-gray-400 mb-10 max-w-2xl mx-auto">Create your account now and get access to thousands of premium social media services.</p>
                <Link href="/register">
                  <button className="bg-green-500 hover:bg-green-400 text-black text-xl font-bold py-5 px-12 rounded-full shadow-[0_0_30px_rgba(34,197,94,0.3)] transition-all hover:scale-105">
                    Create free account
                  </button>
                </Link>
                <div className="mt-8 flex items-center justify-center gap-4 text-gray-500 text-sm font-semibold opacity-60">
                  <HeartFilled /> 100% Satisfaction Guarantee
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      {/* FOOTER */}
      <footer className="bg-[#070A11] border-t border-gray-900 pt-16 pb-12 relative z-10">
        <div className="container mx-auto px-6 lg:px-12">
          <div className="grid md:grid-cols-4 gap-12 mb-16">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center gap-2 mb-6">
                {logoUrl ? (
                  <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 w-auto object-contain brightness-0 invert" />
                ) : (
                  <div className="text-2xl font-black text-white">
                    <DashboardOutlined className="text-green-500 mr-2" /> {config.title || 'PRO SMM'}
                  </div>
                )}
              </div>
              <p className="text-gray-500 text-sm max-w-xs leading-relaxed">
                The ultimate SMM Panel providing high speed, premium quality social media marketing services at wholesale prices.
              </p>
            </div>
            
            <div>
              <h4 className="text-white font-bold mb-6 uppercase tracking-wider text-xs">Platform</h4>
              <ul className="space-y-4 text-gray-500 text-sm">
                <li><Link href="/services" className="hover:text-green-400 transition-colors">Services</Link></li>
                <li><Link href="/api" className="hover:text-green-400 transition-colors">API Documentation</Link></li>
                <li><Link href="/register" className="hover:text-green-400 transition-colors">Sign Up</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-white font-bold mb-6 uppercase tracking-wider text-xs">Support</h4>
              <ul className="space-y-4 text-gray-500 text-sm">
                <li><a href="#" className="hover:text-green-400 transition-colors">FAQ</a></li>
                <li><a href="#" className="hover:text-green-400 transition-colors">Terms of Service</a></li>
                <li><a href="#" className="hover:text-green-400 transition-colors">Privacy Policy</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-gray-900 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-600 text-sm">
            <div>© {new Date().getFullYear()} {config.title || 'SMM Platform'}. All rights reserved.</div>
            <div className="flex gap-6">
               <a href="#" className="hover:text-white transition-colors">Terms</a>
               <a href="#" className="hover:text-white transition-colors">Privacy</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
