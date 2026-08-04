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
  DashboardOutlined
} from '@ant-design/icons';
import { Button, Card, Statistic, Steps, Typography, Rate, Tag, Divider } from 'antd';
import api from '@/lib/axios';

const { Title, Text, Paragraph } = Typography;

const services = [
  { title: 'Tăng Follow, Like, View', desc: 'Thúc đẩy tương tác mạnh mẽ cho Instagram, Facebook, Tiktok v.v...', icon: <RocketOutlined />, color: '#1677ff' },
  { title: 'Tốc độ xử lý siêu tốc', desc: 'Hàng ngàn đơn hàng được xử lý tự động trong tích tắc bằng API.', icon: <ThunderboltFilled />, color: '#faad14' },
  { title: 'Bảo mật an toàn 100%', desc: 'Không yêu cầu mật khẩu mạng xã hội, an toàn tuyệt đối cho tài khoản.', icon: <SafetyCertificateOutlined />, color: '#52c41a' },
  { title: 'Thống kê minh bạch', desc: 'Theo dõi chi tiết tiến độ đơn hàng và lịch sử chi tiêu theo thời gian thực.', icon: <LineChartOutlined />, color: '#722ed1' },
  { title: 'Đa quốc gia', desc: 'Hỗ trợ dịch vụ từ nhiều Server, đa dạng quốc gia cho nhiều mục đích.', icon: <GlobalOutlined />, color: '#eb2f96' },
  { title: 'Hỗ trợ 24/7', desc: 'Đội ngũ CSKH chuyên nghiệp luôn sẵn sàng hỗ trợ bạn bất kỳ lúc nào.', icon: <CustomerServiceOutlined />, color: '#13c2c2' }
];

export default function LandingDefaultPage() {
  const [config, setConfig] = useState<any>({ title: 'SMM Panel' });

  useEffect(() => {
    api.get('/client/config').then(({ data }) => {
      if (data?.status) {
        setConfig(data.data);
        
        // Dynamically update SEO
        document.title = data.data.title || 'SMM Panel';
        let metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription) {
          metaDescription.setAttribute('content', data.data.description || 'Hệ thống SMM tự động, kéo tương tác mạnh mẽ cho mạng xã hội.');
        } else {
          metaDescription = document.createElement('meta');
          metaDescription.setAttribute('name', 'description');
          metaDescription.setAttribute('content', data.data.description || 'Hệ thống SMM tự động, kéo tương tác mạnh mẽ cho mạng xã hội.');
          document.head.appendChild(metaDescription);
        }
      }
    }).catch(() => undefined);
  }, []);

  const logoUrl = config.logo?.startsWith('http') ? config.logo : config.logo ? `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${config.logo.startsWith('/') ? '' : '/'}${config.logo}` : null;

  return (
    <div className="min-h-screen bg-[#f8fafc] font-sans text-slate-800 overflow-x-hidden selection:bg-[#1677ff] selection:text-white">
      
      {/* NAVBAR */}
      <header className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100 shadow-sm transition-all h-20 flex items-center">
        <div className="container mx-auto px-6 lg:px-12 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2 text-2xl font-black text-slate-900 tracking-tight">
            {logoUrl ? (
              <img src={logoUrl} alt={config.title || 'Logo'} className="h-10 object-contain" />
            ) : (
              <>
                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1677ff] to-[#36cfc9] flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                  <DashboardOutlined />
                </div>
                {config.title || 'PRO SMM'}
              </>
            )}
          </Link>
          <nav className="hidden md:flex items-center gap-8 font-semibold text-slate-600">
            <a href="#features" className="hover:text-[#1677ff] transition-colors">Tính năng</a>
            <a href="#services" className="hover:text-[#1677ff] transition-colors">Dịch vụ</a>
            <a href="#workflow" className="hover:text-[#1677ff] transition-colors">Cách hoạt động</a>
          </nav>
          <div className="flex items-center gap-3">
            <Link href="/login">
              <Button type="text" size="large" className="font-bold text-slate-600 hover:text-[#1677ff] hidden sm:block">Đăng nhập</Button>
            </Link>
            <Link href="/register">
              <Button type="primary" size="large" className="font-bold shadow-md shadow-blue-500/30 rounded-full px-6 flex items-center gap-2">
                Bắt đầu ngay <ArrowRightOutlined />
              </Button>
            </Link>
          </div>
        </div>
      </header>

      <main>
        {/* HERO SECTION */}
        <section className="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-white">
          <div className="absolute top-0 right-0 w-[800px] h-[800px] bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-full blur-3xl opacity-60 -translate-y-1/3 translate-x-1/3" />
          
          <div className="container mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <div className="max-w-2xl">
              <Tag color="blue" className="px-3 py-1 rounded-full text-sm font-bold border-blue-200 mb-6 flex items-center w-fit gap-2">
                <ThunderboltFilled className="text-yellow-500" /> Hệ thống SMM Số 1 Việt Nam
              </Tag>
              <Title level={1} className="!text-5xl lg:!text-[64px] !font-black !leading-[1.1] !mb-6 text-slate-900 tracking-tight">
                Phát triển mạng <br /> xã hội của bạn <br />
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#1677ff] to-[#722ed1]">Siêu Tốc Độ</span>
              </Title>
              <Paragraph className="!text-lg !text-slate-500 !mb-8 !leading-relaxed font-medium">
                Nền tảng tự động hóa các dịch vụ Marketing Mạng xã hội tốt nhất. Tăng trưởng lượng người theo dõi, tương tác và khách hàng tiềm năng cho doanh nghiệp của bạn chỉ với vài cú click.
              </Paragraph>
              <div className="flex flex-wrap items-center gap-4">
                <Link href="/register">
                  <Button type="primary" size="large" style={{ height: '54px', padding: '0 32px' }} className="rounded-full text-lg font-bold shadow-xl shadow-blue-500/30 flex items-center gap-2">
                    Tạo tài khoản miễn phí <ArrowRightOutlined />
                  </Button>
                </Link>
                <Link href="#workflow">
                  <Button size="large" type="default" style={{ height: '54px', padding: '0 32px' }} className="rounded-full text-lg font-bold">
                    Tìm hiểu thêm
                  </Button>
                </Link>
              </div>
              <div className="mt-10 flex items-center gap-6">
                <div className="flex -space-x-4">
                  {[1,2,3,4].map(i => (
                    <img key={i} className="w-12 h-12 rounded-full border-2 border-white object-cover shadow-sm" src={`https://i.pravatar.cc/100?img=${i+10}`} alt="User" />
                  ))}
                  <div className="w-12 h-12 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-500 shadow-sm">50k+</div>
                </div>
                <div className="flex flex-col">
                  <div className="flex text-yellow-400 text-lg"><StarFilled /><StarFilled /><StarFilled /><StarFilled /><StarFilled /></div>
                  <Text className="text-sm font-semibold text-slate-500">Được tin dùng bởi 50,000+ Đại lý</Text>
                </div>
              </div>
            </div>

            {/* HERO IMAGE/ABSTRACT */}
            <div className="relative flex justify-center lg:justify-end">
              <div className="relative w-full max-w-lg aspect-square">
                <div className="absolute inset-0 bg-gradient-to-tr from-[#1677ff] to-[#722ed1] rounded-[3rem] rotate-3 opacity-10 animate-pulse" />
                <div className="absolute inset-0 bg-white rounded-[3rem] shadow-2xl -rotate-3 transition-transform hover:rotate-0 duration-500 border border-slate-100 overflow-hidden flex flex-col">
                  <div className="h-12 bg-slate-50 border-b border-slate-100 flex items-center gap-2 px-6">
                    <div className="w-3 h-3 rounded-full bg-red-400" />
                    <div className="w-3 h-3 rounded-full bg-yellow-400" />
                    <div className="w-3 h-3 rounded-full bg-green-400" />
                  </div>
                  <div className="flex-1 p-8 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-slate-50/50 flex flex-col gap-6">
                    <Card size="small" className="w-3/4 rounded-2xl shadow-sm border-0 border-l-4 border-l-[#1677ff]">
                      <Statistic title="Đơn hàng đã xử lý" value={1254302} prefix={<CheckCircleFilled className="text-[#1677ff]" />} />
                    </Card>
                    <Card size="small" className="w-3/4 self-end rounded-2xl shadow-sm border-0 border-l-4 border-l-[#52c41a]">
                      <Statistic title="Tài khoản hoạt động" value={54200} prefix={<LineChartOutlined className="text-[#52c41a]" />} />
                    </Card>
                    <Card size="small" className="w-full rounded-2xl shadow-sm border-0 mt-auto bg-gradient-to-r from-[#1677ff] to-[#36cfc9]">
                      <Statistic title={<span className="text-white/80">Số dư hiện tại</span>} value={1500000} suffix="VNĐ" valueStyle={{ color: '#fff', fontWeight: 'bold' }} />
                    </Card>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* LOGO BAR */}
        <section className="py-10 bg-white border-y border-slate-100">
          <div className="container mx-auto px-6 lg:px-12 flex flex-wrap justify-center items-center gap-10 md:gap-20 opacity-40 grayscale">
             <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" className="h-8 object-contain" alt="Ig" />
             <img src="https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg" className="h-8 object-contain" alt="Tiktok" />
             <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Twitter_new_X_logo.png" className="h-6 object-contain" alt="X" />
             <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" className="h-8 object-contain" alt="Fb" />
             <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg" className="h-8 object-contain" alt="Yt" />
          </div>
        </section>

        {/* SERVICES / FEATURES */}
        <section id="features" className="py-24 bg-slate-50">
          <div className="container mx-auto px-6 lg:px-12">
            <div className="text-center max-w-3xl mx-auto mb-16">
              <Title level={2} className="!text-3xl lg:!text-4xl !font-bold text-slate-900 !mb-4">Dịch vụ chất lượng cao, đa nền tảng</Title>
              <Paragraph className="!text-lg text-slate-500 font-medium">Hệ thống cung cấp đầy đủ các tiện ích để quản lý chiến dịch Social Media hiệu quả với chi phí tối ưu nhất.</Paragraph>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {services.map((item, idx) => (
                <Card 
                  key={idx} 
                  hoverable 
                  className="rounded-3xl border-slate-200 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1"
                  bodyStyle={{ padding: '32px' }}
                >
                  <div 
                    className="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm"
                    style={{ backgroundColor: `${item.color}15`, color: item.color }}
                  >
                    {item.icon}
                  </div>
                  <Title level={4} className="!font-bold !mb-3 text-slate-800">{item.title}</Title>
                  <Paragraph className="!text-slate-500 font-medium !mb-0">{item.desc}</Paragraph>
                </Card>
              ))}
            </div>
          </div>
        </section>

        {/* HOW IT WORKS */}
        <section id="workflow" className="py-24 bg-white">
          <div className="container mx-auto px-6 lg:px-12">
            <div className="grid lg:grid-cols-2 gap-16 items-center">
              <div>
                <Tag color="cyan" className="px-3 py-1 rounded-full text-sm font-bold border-cyan-200 mb-6">Quy trình vận hành</Tag>
                <Title level={2} className="!text-3xl lg:!text-4xl !font-bold text-slate-900 !mb-6">Bắt đầu dễ dàng chỉ với 3 bước</Title>
                <Paragraph className="!text-lg text-slate-500 font-medium !mb-10">
                  Giao diện thân thiện và dễ sử dụng. Bạn không cần bất kỳ kiến thức kỹ thuật nào để bắt đầu chạy chiến dịch.
                </Paragraph>
                
                <Steps
                  direction="vertical"
                  current={0}
                  className="custom-steps"
                  items={[
                    {
                      title: <span className="text-xl font-bold text-slate-800">1. Tạo tài khoản & Nạp tiền</span>,
                      description: <span className="text-slate-500 font-medium text-base">Đăng ký hoàn toàn miễn phí, nạp tiền tự động 24/7 qua hệ thống Ngân hàng, Momo tiện lợi.</span>,
                    },
                    {
                      title: <span className="text-xl font-bold text-slate-800">2. Mua dịch vụ SMM</span>,
                      description: <span className="text-slate-500 font-medium text-base">Lựa chọn dịch vụ phù hợp, dán đường dẫn (Link) và nhập số lượng cần tăng.</span>,
                    },
                    {
                      title: <span className="text-xl font-bold text-slate-800">3. Theo dõi & Tận hưởng</span>,
                      description: <span className="text-slate-500 font-medium text-base">Hệ thống của chúng tôi tự động xử lý đơn hàng của bạn. Bạn chỉ cần xem kết quả gửi về!</span>,
                    },
                  ]}
                />
              </div>
              <div className="relative">
                 <div className="absolute inset-0 bg-blue-100 rounded-full blur-3xl opacity-50 translate-x-10 translate-y-10" />
                 <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=2426&auto=format&fit=crop" className="relative z-10 rounded-[2rem] shadow-2xl object-cover aspect-[4/3] border-8 border-white" alt="Workflow" />
                 
                 {/* Floating Badges */}
                 <Card size="small" className="absolute -bottom-6 left-6 z-20 rounded-2xl shadow-xl border-0 bg-white/90 backdrop-blur-md">
                    <Statistic title="Đánh giá tích cực" value={99.8} suffix="%" valueStyle={{ color: '#52c41a', fontWeight: 'bold' }} />
                 </Card>
              </div>
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="py-24 bg-slate-900 border-t border-slate-800 relative overflow-hidden">
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-r from-blue-600 to-purple-600 rounded-full blur-[100px] opacity-20" />
          
          <div className="container mx-auto px-6 relative z-10 text-center max-w-4xl">
            <Title level={1} className="!text-4xl md:!text-5xl !font-black !text-white !mb-6">Sẵn sàng để đưa thương hiệu của bạn vươn xa?</Title>
            <Paragraph className="!text-xl text-slate-300 font-medium !mb-10">Tham gia cùng hàng ngàn khách hàng đã và đang thành công rực rỡ với các công cụ SMM Tự động của chúng tôi.</Paragraph>
            
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/register">
                <Button type="primary" size="large" style={{ height: '60px', padding: '0 40px' }} className="rounded-full text-lg font-bold shadow-xl shadow-blue-600/30">
                  Tạo tài khoản & Trải nghiệm
                </Button>
              </Link>
            </div>
          </div>
        </section>
      </main>

      {/* FOOTER */}
      <footer className="bg-white border-t border-slate-100 py-12">
        <div className="container mx-auto px-6 lg:px-12 flex flex-col md:flex-row items-center justify-between gap-6">
          <div className="flex items-center gap-2 text-xl font-black text-slate-900">
            {logoUrl ? (
              <img src={logoUrl} alt={config.title || 'Logo'} className="h-8 object-contain" />
            ) : (
              <>
                <DashboardOutlined className="text-[#1677ff]" /> {config.title || 'PRO SMM PANEL'}
              </>
            )}
          </div>
          <Text className="text-slate-500 font-medium text-center">
            © {new Date().getFullYear()} {config.title || 'SMM Panel'}. Kéo tương tác đỉnh cao. All rights reserved.
          </Text>
        </div>
      </footer>
      
      {/* OVERRIDE ANTD STYLES FOR LANDING */}
      <style dangerouslySetInnerHTML={{ __html: `
        .custom-steps .ant-steps-item-title {
          font-family: inherit !important;
        }
        .custom-steps .ant-steps-item-description {
          padding-bottom: 24px !important;
        }
      `}} />
    </div>
  );
}
