'use client';
import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { 
  ArrowRightOutlined, PlayCircleFilled, 
  CheckCircleFilled, RocketOutlined, 
  SafetyCertificateOutlined, GlobalOutlined,
  AppstoreOutlined, DashboardOutlined
} from '@ant-design/icons';
import api from '@/lib/axios';

const brands = [
  'https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg',
  'https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg',
  'https://upload.wikimedia.org/wikipedia/commons/0/0e/Shopify_logo_2018.svg',
  'https://upload.wikimedia.org/wikipedia/commons/0/08/Pinterest-logo.png',
  'https://upload.wikimedia.org/wikipedia/commons/0/08/Canva_icon_2021.svg',
];

export default function LandingDefaultPage() {
  const [config, setConfig] = useState<any>({ title: 'SMM Panel' });

  useEffect(() => {
    api.get('/client/config').then(({ data }) => data?.status && setConfig(data.data)).catch(() => undefined);
  }, []);

  return (
    <div className="min-h-screen bg-white font-sans text-gray-800 overflow-x-hidden selection:bg-[#1962F2] selection:text-white">
      
      {/* APPTEK STYLE HEADER */}
      <header className="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md z-50 border-b border-gray-100 transition-all duration-300">
        <div className="container mx-auto px-6 py-4 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2 text-2xl font-black text-[#0E1552]">
            <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1962F2] to-[#24ABF5] flex items-center justify-center text-white">
              <AppstoreOutlined />
            </div>
            {config.title || 'Apptek SMM'}
          </Link>
          <nav className="hidden lg:flex items-center gap-8 text-sm font-bold text-[#0E1552]">
            <a href="#" className="hover:text-[#1962F2] transition-colors">Home</a>
            <a href="#" className="hover:text-[#1962F2] transition-colors">Services</a>
            <a href="#" className="hover:text-[#1962F2] transition-colors">Features</a>
            <a href="#" className="hover:text-[#1962F2] transition-colors">Pricing</a>
            <a href="#" className="hover:text-[#1962F2] transition-colors">Contact</a>
          </nav>
          <div className="flex items-center gap-4">
            <Link href="/login" className="hidden md:block text-sm font-bold text-[#0E1552] hover:text-[#1962F2] transition-colors">
              Login
            </Link>
            <Link href="/register" className="bg-[#0E1552] hover:bg-[#1962F2] text-white px-6 py-3 rounded-full text-sm font-bold shadow-lg shadow-blue-900/20 transition-all transform hover:-translate-y-0.5">
              Get Started Free
            </Link>
          </div>
        </div>
      </header>

      <main>
        {/* APPTEK STYLE HERO */}
        <section className="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
          {/* Animated Background Shapes */}
          <div className="absolute top-20 left-10 w-4 h-4 rounded-full bg-[#FF7549] animate-bounce" style={{ animationDuration: '3s' }} />
          <div className="absolute top-40 right-20 w-3 h-3 rounded-full bg-[#0E1552] animate-pulse" />
          <div className="absolute bottom-20 left-1/4 w-5 h-5 rounded-full bg-[#24ABF5] animate-bounce" style={{ animationDuration: '4s' }} />
          <div className="absolute -top-40 -right-40 w-96 h-96 bg-[#EBF8FF] rounded-full blur-3xl opacity-50 -z-10" />
          <div className="absolute bottom-0 -left-20 w-80 h-80 bg-[#FFF0EB] rounded-full blur-3xl opacity-50 -z-10" />

          <div className="container mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center z-10 relative">
            <div className="max-w-xl">
              <div className="inline-flex items-center gap-2 bg-blue-50 text-[#1962F2] px-4 py-2 rounded-full text-sm font-bold mb-6">
                <RocketOutlined /> Boost Your Social Media
              </div>
              <h1 className="text-5xl lg:text-[60px] font-extrabold text-[#0E1552] leading-[1.1] mb-6 tracking-tight">
                Versatile app for smart <br/>
                <span className="relative inline-block text-[#1962F2]">
                  solutions
                  <svg className="absolute -bottom-2 left-0 w-full" width="274" height="18" viewBox="0 0 274 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 13.5975C67 4.45571 118 -2.40247 269.5 13.5975" stroke="#FFD53F" stroke-width="8" stroke-linecap="round"/>
                  </svg>
                </span>
              </h1>
              <p className="text-lg text-gray-500 mb-10 leading-relaxed font-medium">
                The most advanced Social Media Marketing platform. Get authentic followers, likes, and engagement instantly. Grow your brand with our premium quality services.
              </p>
              <div className="flex flex-wrap items-center gap-6">
                <Link href="/register" className="bg-[#1962F2] hover:bg-[#0E1552] text-white px-8 py-4 rounded-full text-base font-bold shadow-xl shadow-blue-500/30 transition-all flex items-center gap-2 group">
                  Start Optimizing
                  <ArrowRightOutlined className="group-hover:translate-x-1 transition-transform" />
                </Link>
                <button className="flex items-center gap-3 text-[#0E1552] font-bold hover:text-[#1962F2] transition-colors group">
                  <span className="w-14 h-14 rounded-full bg-white shadow-lg flex items-center justify-center text-[#FF7549] text-2xl group-hover:scale-110 transition-transform">
                    <PlayCircleFilled />
                  </span>
                  Watch Video
                </button>
              </div>
            </div>

            <div className="relative lg:h-[600px] flex items-center justify-center">
              <div className="absolute inset-0 bg-gradient-to-br from-[#EBF8FF] to-[#FFF0EB] rounded-full blur-3xl opacity-60 -z-10" />
              <img 
                src="https://apptek.radiantthemes.com/wp-content/uploads/2022/10/banner-img.png" 
                alt="App Interface" 
                className="w-full max-w-lg object-contain drop-shadow-2xl animate-[float_6s_ease-in-out_infinite]"
                style={{ animation: 'float 6s ease-in-out infinite' }}
                onError={(e) => { e.currentTarget.src = 'https://images.unsplash.com/photo-1611162616475-46b635cb6868?q=80&w=800&auto=format&fit=crop'; e.currentTarget.className = 'w-full max-w-lg rounded-3xl shadow-2xl'; }}
              />
              
              {/* Floating elements */}
              <div className="absolute top-10 right-0 bg-white p-4 rounded-2xl shadow-xl animate-[float_5s_ease-in-out_infinite_0.5s]">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500 text-xl"><CheckCircleFilled /></div>
                  <div>
                    <div className="text-sm font-bold text-[#0E1552]">Completed</div>
                    <div className="text-xs text-gray-400">10k+ Orders</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* BRANDS SECTION */}
        <section className="py-12 border-y border-gray-100 bg-gray-50/50">
          <div className="container mx-auto px-6 text-center">
            <p className="text-sm font-bold text-gray-400 mb-8 uppercase tracking-widest">Trusted by 50,000+ Agencies Worldwide</p>
            <div className="flex flex-wrap justify-center items-center gap-12 md:gap-20 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
              {brands.map((src, i) => (
                <img key={i} src={src} className="h-8 md:h-10 object-contain" alt="Brand" />
              ))}
            </div>
          </div>
        </section>

        {/* FEATURES GRID (APPTEK STYLE) */}
        <section className="py-24 bg-white">
          <div className="container mx-auto px-6">
            <div className="text-center max-w-2xl mx-auto mb-16">
              <h2 className="text-4xl font-extrabold text-[#0E1552] mb-4">Amazing Features To <br/>Manage Your Growth</h2>
              <p className="text-gray-500 font-medium text-lg">We provide the best automated tools to help you scale your social media presence without any technical skills.</p>
            </div>

            <div className="grid md:grid-cols-3 gap-8">
              {[
                { icon: <DashboardOutlined />, title: 'Intuitive Dashboard', color: 'bg-blue-100 text-blue-600', shadow: 'hover:shadow-blue-100' },
                { icon: <RocketOutlined />, title: 'Lightning Fast', color: 'bg-orange-100 text-orange-500', shadow: 'hover:shadow-orange-100' },
                { icon: <GlobalOutlined />, title: 'Global Reach', color: 'bg-green-100 text-green-500', shadow: 'hover:shadow-green-100' },
                { icon: <SafetyCertificateOutlined />, title: 'Secure Gateway', color: 'bg-purple-100 text-purple-600', shadow: 'hover:shadow-purple-100' },
                { icon: <AppstoreOutlined />, title: 'Diverse Services', color: 'bg-pink-100 text-pink-500', shadow: 'hover:shadow-pink-100' },
                { icon: <CheckCircleFilled />, title: 'Guaranteed Results', color: 'bg-teal-100 text-teal-600', shadow: 'hover:shadow-teal-100' },
              ].map((item, i) => (
                <div key={i} className={`p-8 rounded-3xl border border-gray-100 bg-white transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl ${item.shadow}`}>
                  <div className={`w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-6 ${item.color}`}>
                    {item.icon}
                  </div>
                  <h3 className="text-xl font-bold text-[#0E1552] mb-3">{item.title}</h3>
                  <p className="text-gray-500 font-medium mb-6">Experience seamless automation with our state-of-the-art API integration and real-time tracking.</p>
                  <a href="#" className="font-bold text-[#1962F2] hover:text-[#0E1552] flex items-center gap-2">
                    Learn More <ArrowRightOutlined className="text-xs" />
                  </a>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* CTA SECTION */}
        <section className="py-24 bg-[#0E1552] relative overflow-hidden">
          <div className="absolute top-0 right-0 w-96 h-96 bg-[#1962F2] rounded-full blur-3xl opacity-30 -translate-y-1/2 translate-x-1/2" />
          <div className="absolute bottom-0 left-0 w-96 h-96 bg-[#FF7549] rounded-full blur-3xl opacity-20 translate-y-1/2 -translate-x-1/2" />
          
          <div className="container mx-auto px-6 relative z-10 text-center">
            <h2 className="text-4xl md:text-5xl font-extrabold text-white mb-6">Ready To Boost Your Profile?</h2>
            <p className="text-blue-200 text-lg mb-10 max-w-2xl mx-auto">Join over 50,000+ creators and agencies who trust our platform to scale their social media presence.</p>
            
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/register" className="bg-[#1962F2] hover:bg-white hover:text-[#0E1552] text-white px-8 py-4 rounded-full text-base font-bold shadow-xl transition-all">
                Create Free Account
              </Link>
              <Link href="/contact" className="bg-transparent border-2 border-white/30 hover:border-white text-white px-8 py-4 rounded-full text-base font-bold transition-all">
                Contact Sales
              </Link>
            </div>
          </div>
        </section>
      </main>
      
      {/* GLOBAL STYLES FOR ANIMATION */}
      <style dangerouslySetInnerHTML={{ __html: `
        @keyframes float {
          0% { transform: translateY(0px); }
          50% { transform: translateY(-20px); }
          100% { transform: translateY(0px); }
        }
      `}} />
    </div>
  );
}
