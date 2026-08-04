'use client';
import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { 
  PlayCircleOutlined, CheckCircleFilled, RocketOutlined, 
  TeamOutlined, DollarCircleOutlined, MobileOutlined, 
  ToolOutlined, LineChartOutlined, HeartFilled, SafetyCertificateOutlined 
} from '@ant-design/icons';
import api from '@/lib/axios';

const brands = [
  'https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg', // Instagram
  'https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg', // TikTok
  'https://upload.wikimedia.org/wikipedia/commons/0/0e/Shopify_logo_2018.svg', // Shopify
  'https://upload.wikimedia.org/wikipedia/commons/0/08/Pinterest-logo.png', // Pinterest
  'https://upload.wikimedia.org/wikipedia/commons/0/08/Canva_icon_2021.svg', // Canva
];

const avatars = [
  'https://randomuser.me/api/portraits/women/44.jpg',
  'https://randomuser.me/api/portraits/women/68.jpg',
  'https://randomuser.me/api/portraits/men/32.jpg',
  'https://randomuser.me/api/portraits/women/24.jpg',
  'https://randomuser.me/api/portraits/men/46.jpg'
];

export default function Landing1Page() {
  const [config, setConfig] = useState<any>({ title: 'SMM Panel' });

  useEffect(() => {
    api.get('/client/config').then(({ data }) => data?.status && setConfig(data.data)).catch(() => undefined);
  }, []);

  return (
    <div className="min-h-screen bg-white font-sans overflow-x-hidden selection:bg-[#3fb0f4] selection:text-white">
      
      {/* BACKGROUND GRADIENTS */}
      <div className="absolute top-0 left-0 right-0 h-[800px] bg-gradient-to-br from-[#eaf6ff] via-[#f5faff] to-white -z-10" />

      {/* HEADER */}
      <header className="container mx-auto px-6 py-5 flex items-center justify-between">
        <div className="flex items-center gap-2 text-xl font-bold text-gray-800">
          <div className="w-8 h-8 rounded-full border-[3px] border-[#3fb0f4] flex items-center justify-center">
            <div className="w-3 h-3 rounded-full bg-orange-300" />
          </div>
          {config.title || 'SMM STUDIO'}
        </div>
        <nav className="hidden md:flex items-center gap-8 text-sm font-semibold text-gray-600">
          <a href="#" className="text-gray-900">Home</a>
          <a href="#" className="hover:text-gray-900 transition-colors">Course</a>
          <a href="#" className="hover:text-gray-900 transition-colors">What's Inside</a>
          <a href="#" className="hover:text-gray-900 transition-colors">About</a>
          <a href="#" className="hover:text-gray-900 transition-colors">Testimonials</a>
          <a href="#" className="hover:text-gray-900 transition-colors">FAQ</a>
        </nav>
        <Link href="/register" className="hidden md:inline-flex bg-[#3fb0f4] hover:bg-[#209ee6] transition-colors text-white rounded-full px-6 py-2.5 font-bold shadow-lg shadow-blue-200">
          Enroll Now – $297
        </Link>
      </header>

      {/* HERO SECTION */}
      <main className="container mx-auto px-6 pt-16 pb-20 grid lg:grid-cols-2 gap-12 items-center">
        {/* Left Content */}
        <div className="max-w-xl relative">
          <div className="absolute -top-10 -left-6 w-8 h-8 rounded-full bg-orange-300 opacity-80" style={{ borderRadius: '40% 60% 70% 30% / 40% 50% 60% 50%' }} />
          
          <h2 className="text-[#3fb0f4] font-bold tracking-wide uppercase text-sm mb-4">
            The ultimate beginner-friendly course
          </h2>
          <h1 className="text-5xl lg:text-[64px] font-extrabold text-[#111827] leading-[1.1] mb-6 tracking-tight">
            The Social Media Manager <br/> Insider <span className="inline-block w-12 h-12 bg-orange-200 rounded-full align-middle ml-2 relative">
              <span className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-2xl">😊</span>
            </span>
          </h1>
          <p className="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg">
            Learn how to become a freelance social media manager, land clients, and build the freedom lifestyle you've been dreaming of.
          </p>
          
          <div className="flex flex-wrap items-center gap-5 mb-10">
            <Link href="/register" className="bg-[#3fb0f4] hover:bg-[#209ee6] transition-colors text-white rounded-full px-8 py-4 font-bold text-lg shadow-xl shadow-blue-200 flex-shrink-0">
              Enroll Now – $297
            </Link>
            <button className="flex items-center gap-2 text-gray-600 font-semibold hover:text-[#3fb0f4] transition-colors">
              <span className="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center bg-white shadow-sm text-[#3fb0f4] text-xl">
                <PlayCircleOutlined />
              </span>
              Watch Promo
            </button>
          </div>

          <div className="flex items-center gap-4">
            <div className="flex -space-x-3">
              {avatars.map((url, i) => (
                <img key={i} src={url} alt="Student" className="w-10 h-10 rounded-full border-2 border-white shadow-sm" />
              ))}
            </div>
            <div>
              <div className="flex text-yellow-400 text-sm mb-0.5">
                ★★★★★
              </div>
              <div className="text-xs text-gray-500 max-w-[200px] font-medium leading-tight">
                Join 2,000+ students building their social media business
              </div>
            </div>
          </div>
        </div>

        {/* Right Illustration */}
        <div className="relative lg:h-[600px] flex justify-center lg:justify-end">
          {/* Main Image */}
          <div className="relative z-10">
            <img 
              src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=800&auto=format&fit=crop" 
              alt="Instructor" 
              className="w-[450px] object-cover object-top rounded-t-full rounded-b-[40px] drop-shadow-2xl opacity-95"
              style={{ maskImage: 'linear-gradient(to bottom, black 80%, transparent 100%)', WebkitMaskImage: 'linear-gradient(to bottom, black 80%, transparent 100%)' }}
            />
          </div>

          {/* Floating Card 1 */}
          <div className="absolute top-10 -right-4 lg:-right-10 bg-white rounded-2xl p-4 shadow-xl z-20 w-48 animate-bounce" style={{ animationDuration: '4s' }}>
            <div className="flex gap-3">
              <div className="text-[#3fb0f4] mt-1"><HeartFilled /></div>
              <p className="text-xs font-semibold text-gray-700">Build a business that gives you freedom</p>
            </div>
          </div>

          {/* Floating Card 2 */}
          <div className="absolute top-1/3 -right-6 lg:-right-16 bg-white rounded-2xl p-4 shadow-xl z-20 w-48">
            <div className="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-1">Monthly Income</div>
            <div className="text-2xl font-bold text-gray-900 mb-1">$8,750</div>
            <div className="text-[10px] text-green-500 font-semibold mb-2">+28% this month</div>
            <svg viewBox="0 0 100 30" className="w-full h-8 stroke-[#3fb0f4] stroke-2 fill-none stroke-linecap-round stroke-linejoin-round">
              <path d="M0 25 L20 15 L40 20 L60 5 L80 10 L100 2" />
            </svg>
          </div>

          {/* Floating Card 3 */}
          <div className="absolute bottom-1/4 lg:bottom-1/3 -left-4 lg:-left-12 bg-white rounded-2xl p-4 shadow-xl z-20 w-64">
            <div className="flex gap-3 mb-2">
              <img src={avatars[1]} alt="Reviewer" className="w-8 h-8 rounded-full" />
              <div>
                <p className="text-xs font-medium text-gray-700 leading-tight">This course changed my life. I quit my 9-5 and now work from anywhere!</p>
                <div className="text-[10px] text-gray-500 font-semibold mt-1">- Jenna B.</div>
              </div>
            </div>
            <div className="flex text-yellow-400 text-[10px] justify-end">★★★★★</div>
          </div>

          {/* Blue circles & shapes behind */}
          <div className="absolute top-0 right-20 w-12 h-12 bg-[#3fb0f4] rounded-full z-0 opacity-80" />
          <div className="absolute bottom-20 left-10 w-6 h-6 bg-[#3fb0f4] rounded-full z-0 opacity-80" />
          <div className="absolute bottom-10 right-32 w-10 h-10 bg-orange-200 rounded-full z-0" style={{ borderRadius: '40% 60% 70% 30%' }} />
        </div>
      </main>

      {/* BRANDS SECTION */}
      <section className="border-y border-gray-100 bg-white/50 py-12">
        <div className="container mx-auto px-6 text-center">
          <p className="text-gray-500 text-sm font-semibold mb-8">Trusted by aspiring social media managers worldwide</p>
          <div className="flex flex-wrap justify-center items-center gap-10 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
            {brands.map((src, i) => (
              <img key={i} src={src} className="h-8 md:h-10 object-contain" alt="Brand" />
            ))}
          </div>
        </div>
      </section>

      {/* WHAT YOU'LL LEARN SECTION */}
      <section className="container mx-auto px-6 py-24 text-center">
        <h3 className="text-[#3fb0f4] font-bold uppercase text-xs tracking-wider mb-3">What You'll Learn</h3>
        <h2 className="text-3xl md:text-4xl font-extrabold text-[#111827] mb-16">
          Everything you need to build <br/> a successful SMM business
        </h2>

        <div className="grid md:grid-cols-3 lg:grid-cols-6 gap-6">
          {[
            { icon: <RocketOutlined />, title: 'Start Your SMM Business', desc: 'Learn the exact steps to get started with no experience.', color: 'text-blue-500', bg: 'bg-blue-50' },
            { icon: <TeamOutlined />, title: 'Find & Land Clients', desc: 'Discover how to find clients and pitch your services.', color: 'text-teal-500', bg: 'bg-teal-50' },
            { icon: <DollarCircleOutlined />, title: 'Price Your Services', desc: 'Learn how to price with confidence and get paid what you\'re worth.', color: 'text-orange-500', bg: 'bg-orange-50' },
            { icon: <MobileOutlined />, title: 'Content & Strategy Mastery', desc: 'Create scroll-stopping content and winning strategies for any niche.', color: 'text-[#3fb0f4]', bg: 'bg-sky-50' },
            { icon: <ToolOutlined />, title: 'Workflows & Systems', desc: 'Build systems that save time and scale your business.', color: 'text-blue-500', bg: 'bg-blue-50' },
            { icon: <LineChartOutlined />, title: 'Scale Into an Agency', desc: 'Go from solo to scaling with a team and multiple clients.', color: 'text-[#3fb0f4]', bg: 'bg-sky-50' },
          ].map((item, i) => (
            <div key={i} className="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
              <div className={`w-14 h-14 ${item.bg} ${item.color} rounded-2xl flex items-center justify-center text-2xl mx-auto mb-5`} style={{ borderRadius: '35% 65% 55% 45%' }}>
                {item.icon}
              </div>
              <h4 className="font-bold text-gray-900 mb-3 text-[15px] leading-tight">{item.title}</h4>
              <p className="text-xs text-gray-500 leading-relaxed">{item.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* INSTRUCTOR SECTION */}
      <section className="container mx-auto px-6 py-10">
        <div className="bg-[#3fb0f4] rounded-[40px] p-8 md:p-12 flex flex-col lg:flex-row items-center gap-10 relative overflow-hidden">
          {/* Left image */}
          <div className="lg:w-1/3 relative z-10 shrink-0">
             <div className="bg-white/20 p-2 rounded-[30px] w-full max-w-[300px] mx-auto">
               <img src="https://images.unsplash.com/photo-1544168190-79c154273140?q=80&w=600&auto=format&fit=crop" alt="Instructor" className="w-full rounded-[20px] object-cover h-[300px]" />
             </div>
          </div>
          
          {/* Middle Content */}
          <div className="lg:w-1/3 z-10 text-center lg:text-left">
            <h3 className="text-blue-100 font-semibold uppercase text-xs tracking-wider mb-2">Meet Your Instructor</h3>
            <h2 className="text-4xl font-extrabold text-white mb-4">
              Hi, I'm Sara! <span className="text-2xl align-top bg-orange-300 text-white rounded-full inline-flex w-8 h-8 items-center justify-center">😊</span>
            </h2>
            <p className="text-blue-50 text-sm leading-relaxed mb-8 opacity-90">
              I went from a broke freelancer to a 6-figure social media agency owner. Now I teach aspiring social media managers how to build profitable, freedom-based businesses of their own.
            </p>
            <button className="bg-white text-[#3fb0f4] font-bold rounded-full px-6 py-3 text-sm hover:bg-gray-50 transition-colors shadow-lg">
              Learn More About Sara
            </button>
          </div>

          {/* Right Checklist */}
          <div className="lg:w-1/3 z-10">
            <div className="space-y-4 text-white text-sm font-medium">
              {[
                { label: '6-Figure Agency Owner', icon: '💰' },
                { label: 'Thousands of Students', icon: '👥' },
                { label: 'Proven Systems & Strategies', icon: '⚙️' },
                { label: 'Real-World Experience', icon: '📈' },
              ].map((item, i) => (
                <div key={i} className="flex items-center gap-4 bg-white/10 rounded-2xl p-4">
                  <span className="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center bg-white/5 text-lg">
                    {item.icon}
                  </span>
                  {item.label}
                </div>
              ))}
            </div>
          </div>

          {/* Decorative Background */}
          <div className="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-y-1/2 translate-x-1/4" />
          <div className="absolute bottom-0 left-0 w-80 h-80 bg-black opacity-5 rounded-full translate-y-1/3 -translate-x-1/4" />
        </div>
      </section>

      {/* FOOTER CTA SECTION */}
      <section className="container mx-auto px-6 py-20 pb-32">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <h3 className="text-[#3fb0f4] font-bold uppercase text-xs tracking-wider mb-3">Join Today</h3>
            <h2 className="text-4xl font-extrabold text-[#111827] mb-6">Invest in your freedom</h2>
            <p className="text-gray-500 mb-8 max-w-sm">
              A one-time investment that can change your life forever.
            </p>
            <div className="space-y-4 mb-10">
              {[
                'Lifetime access to the course',
                'Templates, resources & tools',
                'Private student community',
                'New updates included'
              ].map((item, i) => (
                <div key={i} className="flex items-center gap-3 font-medium text-sm text-gray-700">
                  <span className="text-[#3fb0f4] text-lg"><CheckCircleFilled /></span>
                  {item}
                </div>
              ))}
            </div>
            
            <div className="relative inline-block">
              <div className="bg-white border-2 border-gray-100 rounded-2xl p-5 shadow-sm absolute -right-20 -top-8 rotate-3 z-10 hidden md:block">
                <div className="flex items-center gap-2">
                  <span className="font-bold text-gray-800">Your future self<br/>will thank you.</span>
                  <span className="text-[#3fb0f4] text-xl"><HeartFilled /></span>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-[#f5faff] rounded-[40px] p-8 md:p-12 text-center border-2 border-[#eaf6ff] relative lg:ml-10">
            <h4 className="text-5xl font-extrabold text-[#111827] mb-2">$297</h4>
            <p className="text-sm font-semibold text-gray-600 mb-4">One-time payment</p>
            <p className="text-xs text-gray-400 mb-8 font-medium">or 2 payments of <strong className="text-gray-800">$148.50</strong></p>
            
            <button className="w-full bg-[#3fb0f4] hover:bg-[#209ee6] transition-colors text-white rounded-full px-8 py-4 font-bold text-lg shadow-xl shadow-blue-200 mb-6">
              Enroll Now – $297
            </button>
            <p className="text-xs text-gray-500 font-semibold flex items-center justify-center gap-2">
              <SafetyCertificateOutlined className="text-lg" /> 14-Day Money-Back Guarantee
            </p>
          </div>
        </div>
      </section>

    </div>
  );
}
