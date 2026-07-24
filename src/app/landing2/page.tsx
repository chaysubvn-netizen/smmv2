'use client';

import Link from 'next/link';
import { useEffect, useState } from 'react';
import {
  ApiOutlined, ArrowRightOutlined, CheckCircleFilled, CustomerServiceOutlined,
  FacebookFilled, GlobalOutlined, InstagramOutlined, PlayCircleFilled,
  RocketOutlined, SafetyCertificateOutlined, SendOutlined, ShopOutlined,
  StarFilled, ThunderboltOutlined, UserOutlined, YoutubeFilled,
} from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './landing2.module.css';

type Config = { title?: string; logo?: string; description?: string; footer_text?: string };

const platforms = [
  ['Instagram', <InstagramOutlined key="ig" />],
  ['Facebook', <FacebookFilled key="fb" />],
  ['YouTube', <YoutubeFilled key="yt" />],
  ['TikTok', <PlayCircleFilled key="tt" />],
  ['Telegram', <SendOutlined key="tg" />],
  ['X (Twitter)', <b key="x">X</b>],
  ['LinkedIn', <b key="in">in</b>],
  ['Spotify', <b key="sp">●</b>],
  ['Discord', <b key="dc">◉</b>],
  ['Pinterest', <b key="pi">P</b>],
  ['Snapchat', <b key="sc">S</b>],
  ['Threads', <b key="th">@</b>],
  ['Reddit', <b key="rd">R</b>],
  ['Twitch', <b key="tw">T</b>],
  ['SoundCloud', <b key="so">☁</b>],
];

const benefits = [
  [<ThunderboltOutlined key="fast" />, 'Xử lý nhanh', 'Đơn hàng được tiếp nhận tự động và bắt đầu chỉ sau vài phút.'],
  [<SafetyCertificateOutlined key="safe" />, 'Không cần mật khẩu', 'Chỉ cần liên kết công khai, thông tin đăng nhập luôn được bảo mật.'],
  [<ApiOutlined key="api" />, 'API cho đại lý', 'Kết nối hệ thống bán hàng và xử lý số lượng lớn đơn hàng tự động.'],
  [<CustomerServiceOutlined key="support" />, 'Hỗ trợ 24/7', 'Theo dõi đơn rõ ràng và gửi ticket hỗ trợ ngay trên hệ thống.'],
];

export default function Landing2Page() {
  const [config, setConfig] = useState<Config>({ title: 'SMM Panel' });
  const [faq, setFaq] = useState(0);

  useEffect(() => {
    api.get('/client/config').then(response => {
      if (response.data?.status) setConfig(response.data.data);
    }).catch(() => undefined);
  }, []);

  const asset = (value?: string) => value
    ? (/^https?:/i.test(value) ? value : `${String(api.defaults.baseURL || '').replace(/\/api\/?$/, '')}${value.startsWith('/') ? '' : '/'}${value}`)
    : '';
  const title = config.title || 'SMM Panel';

  return <div className={styles.page}>
    <header className={styles.header}>
      <Link href="/" className={styles.brand}>
        {config.logo ? <img src={asset(config.logo)} alt={title} /> : <><span>S</span><b>{title}</b></>}
      </Link>
      <nav><a href="#services">Dịch vụ</a><a href="#why">Vì sao chọn chúng tôi</a><a href="#how">Cách hoạt động</a><a href="#faq">FAQ</a></nav>
      <div className={styles.headerActions}><Link href="/login">Đăng nhập</Link><Link href="/register" className={styles.primary}>Đăng ký miễn phí</Link></div>
    </header>

    <main>
      <section className={styles.hero}>
        <img className={styles.heroBackdrop} src="/landing/smmgen-hero-source.webp" alt="SMM Panel tăng trưởng mạng xã hội" />
        <div className={styles.socialDock} aria-label="Các nền tảng được hỗ trợ">
          <span className={styles.dockFacebook}><FacebookFilled /></span>
          <span className={styles.dockInstagram}><InstagramOutlined /></span>
          <span className={styles.dockLinkedin}>in</span>
          <span className={styles.dockSpotify}>●</span>
          <span className={styles.dockTelegram}><SendOutlined /></span>
          <span className={styles.dockTiktok}><PlayCircleFilled /></span>
        </div>
        <div className={styles.heroCopy}>
          <div className={styles.trustRow}>
            <div className={styles.googleReview}><strong>G</strong><span><b><StarFilled /><StarFilled /><StarFilled /><StarFilled /><StarFilled /></b><small>4.9 Reviews</small></span></div>
            <div className={styles.trustedAvatars}><i>Q</i><i>A</i><i>M</i><i>T</i><b>Được tin dùng bởi 90.534 người dùng</b></div>
          </div>
          <h1>SMM Panel giúp tăng trưởng mạng xã hội <em>nhanh và đáng tin cậy</em></h1>
          <p>{config.description || 'Nền tảng SMM toàn cầu dành cho nhà sáng tạo, doanh nghiệp và đại lý. Đặt lượt theo dõi, lượt thích, lượt xem và bình luận cho mọi nền tảng. Hệ thống xử lý tự động, bắt đầu giao trong vài phút và không bao giờ yêu cầu mật khẩu.'}</p>
          <div className={styles.heroButtons}><Link href="/login" className={styles.secondary}>Đăng nhập ngay</Link><Link href="/register" className={styles.primary}>Đăng ký miễn phí <ArrowRightOutlined /></Link></div>
        </div>
      </section>

      <section className={styles.stats}>
        <article><span><RocketOutlined /></span><div><b>95M+</b><small>Tương tác đã xử lý</small></div></article>
        <article><span><ThunderboltOutlined /></span><div><b>9.857</b><small>Dịch vụ hoạt động</small></div></article>
        <article><span><UserOutlined /></span><div><b>90K+</b><small>Người dùng tin tưởng</small></div></article>
        <article><span><GlobalOutlined /></span><div><b>#1</b><small>SMM Panel toàn cầu</small></div></article>
      </section>

      <section className={styles.intro}>
        <span className={styles.questionOrb}>?</span>
        <div className={styles.introArt}><img src="/landing/smmgen-about-source.webp" alt="Chuyên gia giới thiệu SMM Panel" /></div>
        <div className={styles.introCopy}><h2>SMM Panel <em>là gì?</em></h2><p>SMM Panel là nền tảng trực tuyến giúp người dùng mua các dịch vụ tiếp thị mạng xã hội với số lượng lớn, giá thấp và quản lý tất cả trong một bảng điều khiển tự động.</p><p>Thay vì mất nhiều tháng để phát triển tài khoản tự nhiên, người dùng có thể đặt lượt theo dõi, lượt thích, lượt xem, thời gian xem hoặc bình luận và nhận kết quả trong vài phút.</p><p>Khi bạn đặt đơn, hệ thống kết nối đến mạng lưới nhà cung cấp qua API, xử lý dịch vụ và cập nhật dashboard theo thời gian thực. Bạn có thể theo dõi mọi thứ ngay trên hệ thống.</p><p>Nhà sáng tạo xây dựng độ tin cậy, doanh nghiệp quản lý nhiều chiến dịch và đại lý mua dịch vụ với giá sỉ để bán lại cho khách hàng.</p><Link href="/register" className={styles.primary}>Tìm hiểu thêm về {title} <ArrowRightOutlined /></Link></div>
      </section>

      <section className={styles.why} id="why">
        <div className={styles.sectionTitle}><span>KHÁC BIỆT CỦA CHÚNG TÔI</span><h2>Tăng trưởng dễ dàng, vận hành chuyên nghiệp</h2><p>Mọi tính năng cần thiết để bắt đầu và mở rộng hoạt động kinh doanh SMM.</p></div>
        <div className={styles.benefitGrid}>{benefits.map(([icon, name, text]) => <article key={String(name)}><span>{icon}</span><h3>{name}</h3><p>{text}</p></article>)}</div>
      </section>

      <section className={styles.services} id="services">
        <div className={styles.platformOrb}>f</div>
        <div className={styles.sectionTitle}><h2>Mọi nền tảng. Mọi dịch vụ. <em>Một Dashboard</em></h2><p>Tất cả mạng xã hội phổ biến đều có trong một bảng điều khiển, với mô tả, mức giá và thời gian xử lý rõ ràng.</p></div>
        <div className={styles.platforms}>{platforms.map(([name, icon], index) => <Link href="/services" className={index === 1 ? styles.activePlatform : ''} key={String(name)}><span>{icon}</span><b>{name}</b></Link>)}</div>
      </section>

      <section className={styles.how} id="how">
        <div className={styles.sectionTitle}><span>BẮT ĐẦU TRONG VÀI PHÚT</span><h2>Chỉ 4 bước đơn giản</h2></div>
        <div className={styles.steps}>{[
          [<UserOutlined key="u" />, 'Tạo tài khoản', 'Đăng ký miễn phí bằng email của bạn.'],
          [<ShopOutlined key="s" />, 'Nạp số dư', 'Chọn phương thức thanh toán phù hợp.'],
          [<GlobalOutlined key="g" />, 'Chọn dịch vụ', 'Dán liên kết và nhập số lượng cần chạy.'],
          [<RocketOutlined key="r" />, 'Nhận kết quả', 'Hệ thống tự động xử lý và cập nhật tiến độ.'],
        ].map(([icon, name, text], index) => <article key={String(name)}><i>{String(index + 1).padStart(2, '0')}</i><span>{icon}</span><h3>{name}</h3><p>{text}</p></article>)}</div>
      </section>

      <section className={styles.faq} id="faq">
        <div><span>CÂU HỎI THƯỜNG GẶP</span><h2>Điều bạn cần biết trước khi bắt đầu</h2><p>Đội ngũ hỗ trợ luôn sẵn sàng trợ giúp bạn 24/7.</p></div>
        <div>{[
          ['Tôi có cần cung cấp mật khẩu mạng xã hội?', 'Không. Bạn chỉ cần cung cấp liên kết công khai của trang cá nhân hoặc bài đăng.'],
          ['Bao lâu thì đơn hàng bắt đầu?', 'Phần lớn dịch vụ bắt đầu trong vài phút. Thời gian cụ thể được ghi trong mô tả dịch vụ.'],
          ['Tôi có thể bán lại dịch vụ không?', 'Có. Bảng giá đại lý và API giúp bạn xây dựng hệ thống bán lại tự động.'],
        ].map(([q, a], index) => <article key={q}><button onClick={() => setFaq(faq === index ? -1 : index)}><b>{q}</b><span>{faq === index ? '−' : '+'}</span></button>{faq === index && <p>{a}</p>}</article>)}</div>
      </section>

      <section className={styles.cta}><div><span><StarFilled /> Bắt đầu ngay hôm nay</span><h2>Sẵn sàng tăng tốc thương hiệu của bạn?</h2><p>Tạo tài khoản miễn phí và đặt chiến dịch đầu tiên chỉ trong vài phút.</p></div><Link href="/register">Đăng ký miễn phí <ArrowRightOutlined /></Link></section>
    </main>

    <footer><div className={styles.brand}>{config.logo ? <img src={asset(config.logo)} alt={title} /> : <><span>S</span><b>{title}</b></>}</div><p>{config.footer_text || `© ${new Date().getFullYear()} ${title}. All rights reserved.`}</p><div><Link href="/services">Dịch vụ</Link><Link href="/apidoc">API</Link><Link href="/login">Đăng nhập</Link></div></footer>
  </div>;
}
