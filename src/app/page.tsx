'use client';

import { Form, Input } from 'antd';import { message } from '@/lib/antd-message';
import { ApiOutlined, ArrowRightOutlined, CheckCircleOutlined, CreditCardOutlined, CustomerServiceOutlined, DollarOutlined, FacebookFilled, GlobalOutlined, InstagramOutlined, LikeFilled, LockOutlined, MailOutlined, MenuOutlined, MessageFilled, PlayCircleFilled, RocketOutlined, SafetyCertificateOutlined, SendOutlined, StarFilled, ThunderboltOutlined, UserOutlined, VideoCameraFilled, YoutubeFilled } from '@ant-design/icons';
import Link from 'next/link';
import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import api from '@/lib/axios';
import Landing2Page from './landing2/page';
import Landing1Page from './landing1/page';
import styles from './landing.module.css';

type ModalType = 'login' | 'register' | null;
type AuthValues = Record<string, string>;
type AuthPayload = { user: Record<string, unknown>; message?: string };

const apiError = (error: unknown, fallback: string) => {
  const candidate = error as { response?: { data?: { message?: string; two_factor_auth?: boolean; two_factor_method?: string } } };
  return { message: candidate.response?.data?.message || fallback, data: candidate.response?.data };
};

export default function LandingPage() {
  const router = useRouter();
  const [modal, setModal] = useState<ModalType>(null);
  const [menuOpen, setMenuOpen] = useState(false);
  const [faq, setFaq] = useState(2);
  const [loading, setLoading] = useState(false);
  const [loggedIn, setLoggedIn] = useState(false);
  const [configReady, setConfigReady] = useState(false);
  const [requiresTwoFactor, setRequiresTwoFactor] = useState(false);
  const [twoFactorMethod, setTwoFactorMethod] = useState<'google' | 'telegram'>('google');
  const [config, setConfig] = useState<{ title?: string; logo?: string; landing_page?: string; description?: string; email_link?: string; footer_text?: string; system_version?: string; developer_name?: string; developer_url?: string }>({ title: 'SMM Panel', landing_page: 'default' });
  const logoUrl = config.logo?.startsWith('http') ? config.logo : config.logo ? `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${config.logo.startsWith('/') ? '' : '/'}${config.logo}` : null;

  useEffect(() => {
    api.get('/auth/api/me').then(() => setLoggedIn(true)).catch(() => setLoggedIn(false));
    api.get('/client/config', { params: { _: Date.now() } }).then((response) => { if (response.data?.status) { const nextConfig = response.data.data; setConfig(nextConfig); if (nextConfig.landing_page === 'none') router.replace('/login'); } }).finally(() => setConfigReady(true));
    if (new URLSearchParams(window.location.search).get('register') === '1') void Promise.resolve().then(() => setModal('register'));
  }, []);

  const finishAuth = (data: AuthPayload) => {
    message.success(data.message || 'Đăng nhập thành công!');
    router.push(data.user.role === 'admin' ? '/new' : '/admin');
  };
  const login = async (values: AuthValues) => {
    setLoading(true);
    try { const response = await api.post('/auth/api/login', values); if (response.data.status) finishAuth(response.data); else message.error(response.data.message); }
    catch (error: unknown) { const details = apiError(error, 'Không thể đăng nhập.'); if (details.data?.two_factor_auth) { setRequiresTwoFactor(true); setTwoFactorMethod(details.data.two_factor_method === 'telegram' ? 'telegram' : 'google'); } message.error(details.message); }
    finally { setLoading(false); }
  };
  const register = async (values: AuthValues) => {
    setLoading(true);
    try {
      const refUsername = localStorage.getItem('ref_username');
      const response = await api.post('/auth/api/register', { ...values, ref_username: refUsername || undefined });
      if (response.data.status) { localStorage.removeItem('ref_username'); finishAuth(response.data); } else message.error(response.data.message);
    }
    catch (error: unknown) { message.error(apiError(error, 'Không thể đăng ký.').message); }
    finally { setLoading(false); }
  };

  const features = [
    [<DollarOutlined key="price" />, 'Giá cực tốt', 'Dịch vụ SMM có mức giá cạnh tranh, phù hợp cả người dùng và đại lý.'],
    [<CreditCardOutlined key="pay" />, 'Nhiều cách thanh toán', 'Nạp tiền nhanh chóng qua các phương thức thanh toán phổ biến.'],
    [<ThunderboltOutlined key="fast" />, 'Tốc độ nhanh', 'Hệ thống tự động tiếp nhận và xử lý đơn hàng ngay sau khi đặt.'],
    [<ApiOutlined key="api" />, 'Hỗ trợ API', 'API đầy đủ giúp đại lý kết nối và bán lại dịch vụ dễ dàng.'],
    [<CustomerServiceOutlined key="support" />, 'Hỗ trợ 24/7', 'Đội ngũ kỹ thuật luôn sẵn sàng hỗ trợ khi bạn cần.'],
    [<SafetyCertificateOutlined key="quality" />, 'Chất lượng cao', 'Nguồn dịch vụ đa dạng, ổn định và được cập nhật liên tục.'],
  ];
  const faqs = [
    ['SMM Panel là gì?', 'SMM Panel là nền tảng cung cấp các dịch vụ hỗ trợ phát triển tài khoản mạng xã hội.'],
    ['Hệ thống cung cấp những dịch vụ nào?', 'Chúng tôi cung cấp lượt thích, theo dõi, bình luận, lượt xem và nhiều dịch vụ cho các nền tảng phổ biến.'],
    ['Mua dịch vụ tại đây có an toàn không?', 'Có. Hệ thống bảo mật tài khoản, hỗ trợ xác thực hai lớp và không yêu cầu mật khẩu mạng xã hội.'],
    ['Đặt đơn hàng loạt là gì?', 'Đây là tính năng giúp bạn gửi nhiều đơn hàng cùng lúc theo đúng định dạng quy định.'],
  ];

  if (!configReady || config.landing_page === 'none') return null;
  if (config.landing_page === 'landing2') return <Landing2Page />;
  if (config.landing_page === 'landing1') return <Landing1Page />;
  if (config.landing_page && config.landing_page !== 'default' && config.landing_page !== 'none') return <DistinctLanding theme={config.landing_page} title={config.title || 'SMM Panel'} logo={logoUrl} loggedIn={loggedIn} onStart={() => { if (loggedIn) router.push('/new'); else setModal('register'); }} />;
  return <div className={`${styles.page} ${config.landing_page && config.landing_page !== 'default' && config.landing_page !== 'none' ? styles[`landing_${config.landing_page}`] || '' : ''}`}>
    <header className={styles.header}>
      <Link href="/" className={styles.brand}>{logoUrl ? <img src={logoUrl} alt={config.title || 'Logo'} /> : <><span className={styles.brandMark}>S</span><strong>{config.title || 'SMM Panel'}</strong></>}</Link>
      <nav className={menuOpen ? styles.open : ''}><a href="/new" className={styles.activeNav}>Home</a><a href="#features">Service</a><Link href="/apidoc">API</Link></nav>
      <div className={styles.headerActions}><button className={styles.primaryButton} onClick={() => setModal('register')}>Register</button><button className={styles.menuButton} onClick={() => setMenuOpen(!menuOpen)} aria-label="Menu"><MenuOutlined /></button></div>
    </header>

    <main>
      <section className={styles.hero} id="home"><div className={styles.heroVisual}><div className={styles.orbit} /><img src="/landing/minsmm-layer.png" alt="SMM marketing" className={styles.heroPerson} /><span className={`${styles.socialTile} ${styles.youtube}`}><YoutubeFilled /></span><span className={`${styles.socialTile} ${styles.facebook}`}><FacebookFilled /></span><span className={`${styles.socialTile} ${styles.instagram}`}><InstagramOutlined /></span><span className={`${styles.socialTile} ${styles.telegram}`}><SendOutlined /></span><span className={`${styles.socialTile} ${styles.like}`}><LikeFilled /></span><span className={`${styles.socialTile} ${styles.message}`}><MessageFilled /></span><span className={`${styles.socialTile} ${styles.video}`}><VideoCameraFilled /></span><span className={`${styles.socialTile} ${styles.play}`}><PlayCircleFilled /></span><div className={styles.ratingBadge}><span><CheckCircleOutlined /><b>10000+</b><small>Đơn hàng</small></span><span><StarFilled /><b>9.8/10</b><small>Đánh giá</small></span></div></div><div className={styles.heroContent}>
        <span className={styles.heroKicker}>Dẫn đầu trong mọi thị trường</span>
        <h1><em>Supreme SMM</em> Service Reseller Panel</h1>
        <p>Tăng trưởng mạng xã hội thật dễ dàng! Nhận ngay lượt theo dõi, lượt thích, lượt xem, traffic website và nhiều hơn nữa chỉ với vài cú nhấp chuột.</p>
        <div className={styles.heroActions}><button className={styles.primaryButton} onClick={() => { if (loggedIn) router.push('/new'); else setModal('register'); }}>Bắt đầu ngay <ArrowRightOutlined /></button></div>
      </div></section>

      {!loggedIn && <section className={styles.loginPanel} id="login">
        <h3>Chào mừng đến với {config.title || 'Supreme SMM'}! Vui lòng đăng nhập để truy cập tài khoản.</h3>
        <Form className={styles.inlineLogin} onFinish={login}>
          <Form.Item name="username" rules={[{ required: true }]}><Input prefix={<UserOutlined />} placeholder="Tên người dùng" /></Form.Item>
          <Form.Item name="password" rules={[{ required: true }]}><Input.Password prefix={<LockOutlined />} placeholder="Mật khẩu" /></Form.Item>
          <button className={styles.primaryButton} disabled={loading}>{loading ? 'Đang xử lý...' : 'Đăng nhập'}</button>
        </Form>
        <div className={styles.loginMeta}><label><input type="checkbox" /> Ghi nhớ đăng nhập</label><button onClick={() => setModal('login')}>Quên mật khẩu?</button><span>Chưa có tài khoản? <button onClick={() => setModal('register')}>Đăng ký</button></span></div>
      </section>}

      <section className={`${styles.lightSection} ${styles.featuresSection}`} id="features"><SectionTitle label="" title={<>Vì sao nên đặt <em>dịch vụ SMM</em> tại đây?</>} subtitle="Giúp bạn xây dựng sự hiện diện trực tuyến nhanh chóng và hiệu quả." /><div className={styles.featureGrid}>{features.map(([icon, title, text], index) => <article key={String(title)}><span className={`${styles.featureIcon} ${styles[`icon${index}`]}`}>{icon}</span><div><h3>{title}</h3><p>{text}</p></div></article>)}</div></section>

      <section className={`${styles.lightSection} ${styles.guide}`} id="guide"><div className={styles.guideIntro}><h2>Hướng dẫn toàn diện để <em>bắt đầu</em></h2><p>Đưa hoạt động kinh doanh của bạn lên một tầm cao mới theo bốn bước đơn giản.</p><button className={styles.primaryButton} onClick={() => setModal('register')}>Đăng ký ngay <ArrowRightOutlined /></button></div><div className={styles.stepGrid}><Step number="Bước 1" icon={<UserOutlined />} title="Tạo tài khoản" text="Đăng ký và đăng nhập vào hệ thống." /><Step number="Bước 2" icon={<DollarOutlined />} title="Nạp số dư" text="Chọn phương thức phù hợp để nạp tiền." /><Step number="Bước 3" icon={<GlobalOutlined />} title="Chọn dịch vụ" text="Chọn dịch vụ SMM và đặt đơn hàng." /><Step number="Bước 4" icon={<RocketOutlined />} title="Nhận kết quả" text="Theo dõi tiến độ và tận hưởng tăng trưởng." /></div></section>

      <section className={styles.reviewSection} id="reviews"><SectionTitle label="" title={<>Khách hàng <em>nói gì về chúng tôi</em></>} subtitle="Những trải nghiệm thực tế từ người dùng của hệ thống." /><div className={styles.reviewGrid}>{[['Q','Dịch vụ xử lý rất nhanh, giao diện dễ hiểu và hỗ trợ nhiệt tình.','Quang Nguyễn'],['M','Mức giá tốt, nhiều dịch vụ và lịch sử đơn hàng rất rõ ràng.','Minh Anh'],['T','API ổn định giúp tôi vận hành hệ thống đại lý thuận tiện hơn.','Tuấn Trần']].map(([avatar,text,name]) => <article key={name}><div className={styles.reviewer}><b>{avatar}</b><span><strong>{name}</strong><small>{text}</small></span></div></article>)}</div></section>

      <section className={styles.payment}><div className={styles.paymentArt}><img src="/landing/payment-v2.png" alt="Cổng thanh toán tự động" /></div><div><h2>Chúng tôi hỗ trợ nhiều <em>cổng thanh toán tự động</em> phổ biến</h2><p>Nạp số dư thuận tiện qua những nhà cung cấp phổ biến. Giao dịch được ghi nhận rõ ràng và bảo vệ an toàn.</p></div></section>

      <section className={styles.faqSection} id="faq"><div className={styles.faqDecor}>? 💬 ?</div><SectionTitle label="" title={<><em>FAQ</em> - Câu hỏi thường gặp</>} subtitle="Giải đáp nhanh những điều bạn cần biết về hệ thống." /><div className={styles.faqGrid}>{faqs.map(([question, answer], index) => <article key={question}><button onClick={() => setFaq(faq === index ? -1 : index)}><span>{question}</span><b>{faq === index ? '−' : '+'}</b></button>{faq === index ? <p>{answer}</p> : null}</article>)}</div></section>
    </main>

    <footer><div><div className={styles.brand}><span className={styles.brandMark}>S</span><strong>{config.title || 'SMM Panel'}</strong></div><p>{config.description || 'Nền tảng Social Media Marketing nhanh chóng, tin cậy và dễ sử dụng.'}</p></div><div><a href="#home">Trang chủ</a><Link href="/services">Dịch vụ</Link><Link href="/apidoc">API</Link><button onClick={() => setModal('login')}>Đăng nhập</button></div><small>{config.footer_text || `© ${new Date().getFullYear()} ${config.title || 'SMM Panel'}. All rights reserved.`}{config.system_version ? ` · v${config.system_version}` : ''}{config.developer_name ? <> · Phát triển bởi {config.developer_url ? <a href={config.developer_url} target="_blank" rel="noopener noreferrer">{config.developer_name}</a> : config.developer_name}</> : null}</small></footer>
    {modal ? <AuthModal type={modal} loading={loading} requiresTwoFactor={requiresTwoFactor} twoFactorMethod={twoFactorMethod} onClose={() => { setModal(null); setRequiresTwoFactor(false); }} onSwitch={setModal} onLogin={login} onRegister={register} /> : null}
  </div>;
}

function DistinctLanding({ theme, title, logo, loggedIn, onStart }: { theme: string; title: string; logo: string | null; loggedIn: boolean; onStart: () => void }) {
  const labels: Record<string, [string, string, string]> = { landing2: ['SMM PANEL NO.1', 'Tăng trưởng thật cho thương hiệu của bạn', 'Dịch vụ social media nhanh, an toàn và tối ưu chi phí.'], landing1: ['LARAVEL TEMPLATE 01', 'Dịch vụ SMM nhanh và chuyên nghiệp', 'Mua like, follow, view và nhiều dịch vụ mạng xã hội với giá tốt.'], modern: ['Tăng trưởng thông minh', 'SMM hiện đại cho thương hiệu dẫn đầu', 'Tự động hóa mọi chiến dịch mạng xã hội.'], classic: ['Uy tín từ nền tảng', 'Dịch vụ SMM bền vững, đáng tin cậy', 'Giải pháp tăng trưởng ổn định cho doanh nghiệp.'], top: ['Nền tảng số 1', 'Tất cả dịch vụ mạng xã hội trong một nơi', 'Nhanh hơn, đơn giản hơn, hiệu quả hơn.'], pro: ['PRO SOLUTION', 'Scale your social presence', 'Công cụ chuyên nghiệp cho nhà bán hàng và đại lý.'], vip: ['VIP EXPERIENCE', 'Đặc quyền tăng trưởng dành riêng cho bạn', 'Dịch vụ cao cấp, hỗ trợ ưu tiên 24/7.'] };
  const [kicker, headline, desc] = labels[theme] || labels.modern;
  return <main className={`${styles.distinct} ${styles[`distinct_${theme}`] || styles.distinct_modern}`}><header><a href="/">{logo ? <img src={logo} alt={title} /> : <b>{title}</b>}</a><nav><a href="#services">Dịch vụ</a><a href="/apidoc">API</a><button onClick={onStart}>{loggedIn ? 'Dashboard' : 'Đăng ký'}</button></nav></header><section className={styles.distinctHero}><div><small>{kicker}</small><h1>{headline}</h1><p>{desc}</p><button className={styles.distinctCta} onClick={onStart}>{loggedIn ? 'Vào Dashboard' : 'Bắt đầu ngay'} →</button></div><div className={styles.distinctArt}><span>♥</span><span>↗</span><span>★</span><strong>+99%</strong></div></section><section id="services" className={styles.distinctCards}><article><b>⚡</b><h3>Tự động hoàn toàn</h3><p>Xử lý đơn hàng nhanh chóng 24/7.</p></article><article><b>◈</b><h3>Giá cạnh tranh</h3><p>Dịch vụ ổn định, chi phí tối ưu.</p></article><article><b>✓</b><h3>An toàn bảo mật</h3><p>Bảo vệ tài khoản và giao dịch của bạn.</p></article></section></main>;
}
function SectionTitle({ label, title, subtitle }: { label: string; title: React.ReactNode; subtitle: string }) { return <div className={styles.sectionTitle}><span>{label}</span><h2>{title}</h2><p>{subtitle}</p></div>; }
function Step({ number, icon, title, text }: { number: string; icon: React.ReactNode; title: string; text: string }) { return <article><i>{number}</i><span>{icon}</span><h3>{title}</h3><p>{text}</p></article>; }

type AuthModalProps = { type: Exclude<ModalType, null>; loading: boolean; requiresTwoFactor: boolean; twoFactorMethod: 'google' | 'telegram'; onClose: () => void; onSwitch: (type: Exclude<ModalType, null>) => void; onLogin: (values: AuthValues) => Promise<void>; onRegister: (values: AuthValues) => Promise<void> };
function AuthModal({ type, loading, requiresTwoFactor, twoFactorMethod, onClose, onSwitch, onLogin, onRegister }: AuthModalProps) {
  return <div className={styles.modalBackdrop} onMouseDown={(event) => { if (event.target === event.currentTarget) onClose(); }}><div className={styles.modal}><button className={styles.close} onClick={onClose}>×</button><h2>{type === 'register' ? 'Đăng ký' : 'Đăng nhập'}</h2><p>{type === 'register' ? 'Tạo tài khoản để bắt đầu sử dụng dịch vụ.' : 'Chào mừng bạn quay trở lại.'}</p>{type === 'register' ? <Form layout="vertical" size="large" onFinish={onRegister}><Form.Item label="Tên người dùng" name="username" rules={[{ required: true, min: 6 }]}><Input prefix={<UserOutlined />} placeholder="Nhập tên người dùng" /></Form.Item><Form.Item label="Email" name="email" rules={[{ required: true, type: 'email' }]}><Input prefix={<MailOutlined />} placeholder="Nhập email" /></Form.Item><Form.Item label="Mật khẩu" name="password" rules={[{ required: true, min: 6 }]}><Input.Password prefix={<LockOutlined />} placeholder="Nhập mật khẩu" /></Form.Item><Form.Item label="Xác nhận mật khẩu" name="password_confirmation" dependencies={['password']} rules={[{ required: true }, ({ getFieldValue }) => ({ validator(_, value) { return !value || getFieldValue('password') === value ? Promise.resolve() : Promise.reject(new Error('Mật khẩu xác nhận không khớp')); } })]}><Input.Password prefix={<LockOutlined />} placeholder="Nhập lại mật khẩu" /></Form.Item><button className={styles.modalSubmit} disabled={loading}>{loading ? 'Đang xử lý...' : 'Đăng ký'}</button></Form> : <Form layout="vertical" size="large" onFinish={onLogin}><Form.Item label="Tên người dùng" name="username" rules={[{ required: true }]}><Input prefix={<UserOutlined />} placeholder="Nhập tên người dùng" /></Form.Item><Form.Item label="Mật khẩu" name="password" rules={[{ required: true }]}><Input.Password prefix={<LockOutlined />} placeholder="Nhập mật khẩu" /></Form.Item>{requiresTwoFactor ? <Form.Item label="Mã xác thực" name="two_factor_code" rules={[{ required: true, len: 6 }]}><Input prefix={<SafetyCertificateOutlined />} maxLength={6} placeholder={twoFactorMethod === 'telegram' ? 'Mã đã gửi qua Telegram' : 'Mã Google Authenticator'} /></Form.Item> : null}<button className={styles.modalSubmit} disabled={loading}>{loading ? 'Đang xử lý...' : 'Đăng nhập'}</button></Form>}<div className={styles.modalSwitch}>{type === 'register' ? 'Đã có tài khoản?' : 'Chưa có tài khoản?'} <button onClick={() => onSwitch(type === 'register' ? 'login' : 'register')}>{type === 'register' ? 'Đăng nhập' : 'Đăng ký'}</button></div></div></div>;
}
