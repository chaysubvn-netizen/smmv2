'use client';
/* eslint-disable @next/next/no-img-element */

import Link from 'next/link';
import { useEffect, useState } from 'react';
import {
  ArrowRightOutlined, BookOutlined, CheckCircleFilled,
  ClockCircleOutlined, CodeOutlined, EyeOutlined, GiftOutlined,
  LoadingOutlined, LoginOutlined, MenuOutlined, RocketOutlined,
  SafetyCertificateOutlined, StarFilled, ThunderboltFilled, WalletOutlined,
} from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './landing2.module.css';

type Config = { title?: string; logo?: string; description?: string; footer_text?: string };

const platforms = [
  ['instagram', 'E4405F', 'Instagram'], ['facebook', '1877F2', 'Facebook'],
  ['tiktok', '00f2ea', 'TikTok'], ['youtube', 'FF0000', 'YouTube'],
  ['telegram', '26A5E4', 'Telegram'], ['x', 'ffffff', 'X / Twitter'],
  ['discord', '5865F2', 'Discord'], ['spotify', '1DB954', 'Spotify'],
  ['linkedin', '0A66C2', 'LinkedIn'], ['pinterest', 'BD081C', 'Pinterest'],
  ['reddit', 'FF4500', 'Reddit'], ['twitch', '9146FF', 'Twitch'],
  ['snapchat', 'FFFC00', 'Snapchat'], ['whatsapp', '25D366', 'WhatsApp'],
];

const services = [
  ['instagram', 'E4405F', 'Instagram Services', 'Followers, Likes, Views, Comments, Story Views, Reels Views, IGTV Views...'],
  ['tiktok', '00f2ea', 'TikTok Services', 'Followers, Likes, Views, Shares, Saves, Live Stream Views, Favorites...'],
  ['youtube', 'FF0000', 'YouTube Services', 'Subscribers, Views, Watch Hours, Likes, Comments, Shorts Views...'],
  ['facebook', '1877F2', 'Facebook Services', 'Page Likes, Followers, Post Likes, Views, Group Members, Event Joined...'],
  ['telegram', '26A5E4', 'Telegram Services', 'Members, Post Views, Reactions, Channel Subscribers, Group Members...'],
  ['spotify', '1DB954', 'Spotify Services', 'Followers, Plays, Monthly Listeners, Playlist Followers, Saves...'],
];

const reviews = [
  ['Minh Agency', 'Reseller • 2 năm', '55', 'Đã dùng hơn 10 trang web, đây là hệ thống tốt nhất. Giá rẻ, tốc độ nhanh, drop cực ít. API ổn định và rất mượt.'],
  ['Hoàng Thị Thanh Tâm', 'Streamer • 8 tháng', '112', 'Mình mới tập livestream và rất ít mắt xem. Nhờ hệ thống đã giúp mình có nhiều người xem hơn. Quá ngon!'],
  ['Thuận Thiên', 'Reseller • 1 năm', '82', 'Hỗ trợ cực nhanh. Các hình thức thanh toán tự động rất tiện lợi và dễ sử dụng!'],
];

const simpleIcon = (name: string, color: string) => `https://cdn.simpleicons.org/${name}/${color}`;

export default function Landing2Page() {
  const [config, setConfig] = useState<Config>({ title: 'SMM Panel' });
  const [menu, setMenu] = useState(false);

  useEffect(() => {
    api.get('/client/config').then(({ data }) => data?.status && setConfig(data.data)).catch(() => undefined);
  }, []);

  const title = config.title || 'SMM Panel';
  const asset = (value?: string) => value
    ? (/^https?:/i.test(value) ? value : `${String(api.defaults.baseURL || '').replace(/\/api\/?$/, '')}/${value.replace(/^\//, '')}`)
    : '';

  return <div className={styles.page}>
    <div className={styles.ambience} />
    <header className={styles.header}>
      <Link href="/" className={styles.brand}>
        {config.logo
          ? <img src={asset(config.logo)} alt={title} />
          : <><span className={styles.brandMark}><ThunderboltFilled /></span><b>{title}</b></>}
      </Link>
      <nav><a href="#services">Dịch vụ</a><a href="#pricing">Bảng giá</a><Link href="/apidoc">API</Link></nav>
      <div className={styles.headerActions}>
        <Link href="/login" className={styles.login}><LoginOutlined /> Đăng nhập</Link>
        <Link href="/register" className={styles.gradientButton}><RocketOutlined /> Đăng ký</Link>
      </div>
      <button className={styles.menuButton} onClick={() => setMenu(!menu)} aria-label="Mở menu"><MenuOutlined /></button>
      {menu && <div className={styles.mobileMenu}><a href="#services">Dịch vụ</a><a href="#pricing">Bảng giá</a><Link href="/apidoc">API</Link><Link href="/login">Truy cập ngay</Link></div>}
    </header>

    <main>
      <section className={styles.hero}>
        <div className={styles.heroCopy}>
          <div className={styles.live}><i /><span>Hơn 200K+ đơn hàng/ngày</span></div>
          <h1><span>{title}</span></h1>
          <p>{config.description || 'Hệ thống chuyên cung cấp các dịch vụ tăng Like, Follow, Share, Comment, View Video và hơn thế nữa cho Facebook, Instagram, TikTok, YouTube...'}</p>
          <div className={styles.actions}>
            <Link href="/login" className={styles.gradientButton}><ArrowRightOutlined /> Truy cập ngay</Link>
            <Link href="/services" className={styles.glassButton}><EyeOutlined /> Xem bảng giá</Link>
          </div>
          <div className={styles.heroStats}>
            <div><b>500K+</b><small>Khách hàng</small></div><i />
            <div><b>2.500+</b><small>Dịch vụ</small></div><i />
            <div><b>99%</b><small>Uptime</small></div>
          </div>
        </div>
        <div className={styles.dashboard}>
          <div className={styles.windowBar}><i /><i /><i /><span>{title.toLowerCase()}/home</span></div>
          <div className={styles.balance}><WalletOutlined /><small>Số dư tài khoản</small><strong>897.247<span>.50₫</span></strong><em>↗ +53.5% so với tháng trước</em></div>
          {[['instagram','E4405F','Instagram Followers','Hoàn thành'],['tiktok','00f2ea','TikTok Views','Đang xử lý...'],['youtube','FF0000','YouTube Subscribers','Chờ']].map((item, i) =>
            <div className={styles.order} key={item[2]}><img src={simpleIcon(item[0],item[1])} alt="" /><b>{item[2]}</b><span>{i === 0 ? <CheckCircleFilled /> : i === 1 ? <LoadingOutlined /> : <ClockCircleOutlined />} {item[3]}</span></div>
          )}
          <div className={styles.orderToast}><CheckCircleFilled /><span><b>Đơn hàng #28471</b><small>10.000 followers • Hoàn thành</small></span></div>
        </div>
      </section>

      <section className={styles.platformSection}>
        <div className={styles.sectionHeading}><label>HỖ TRỢ TOÀN DIỆN</label><h2>Mọi nền tảng <span>trong tầm tay</span></h2></div>
        <div className={styles.marquee}><div>{[...platforms,...platforms].map((p,i) => <span key={`${p[0]}-${i}`}><img src={simpleIcon(p[0],p[1])} alt="" />{p[2]}</span>)}</div></div>
      </section>

      <section className={styles.section} id="services">
        <div className={styles.sectionHeading}><label>DỊCH VỤ</label><h2>Mọi thứ bạn cần để <span>phát triển</span></h2><p>Giải pháp tăng trưởng toàn diện cho doanh nghiệp, nhà sáng tạo nội dung và đại lý.</p></div>
        <div className={styles.serviceGrid}>{services.map(s => <Link href="/services" className={styles.card} key={s[0]}><i><img src={simpleIcon(s[0],s[1])} alt="" /></i><h3>{s[2]}</h3><p>{s[3]}</p><b>Từ 100đ <ArrowRightOutlined /></b></Link>)}</div>
      </section>

      <section className={styles.numbers} id="pricing">
        {[['50M+','Đơn hàng hoàn thành'],['2.500+','Dịch vụ hoạt động'],['28+','Nền tảng hỗ trợ'],['500K+','Khách hàng tin dùng']].map(n => <div key={n[1]}><b>{n[0]}</b><small>{n[1]}</small></div>)}
      </section>

      <section className={styles.section}>
        <div className={styles.sectionHeading}><label>QUY TRÌNH</label><h2>Chỉ <span>3 bước</span> đơn giản</h2></div>
        <div className={styles.steps}>{[
          ['1','Đăng ký & Nạp tiền','Tạo tài khoản miễn phí trong 30 giây. Nạp tiền qua ngân hàng và ví điện tử.'],
          ['2','Chọn dịch vụ','Chọn nền tảng, dịch vụ cần mua. Nhập liên kết và số lượng mong muốn.'],
          ['3','Nhận kết quả','Hệ thống tự động xử lý. Theo dõi tiến độ theo thời gian thực trên dashboard.'],
        ].map(x => <article className={styles.card} key={x[0]}><i>{x[0]}</i><h3>{x[1]}</h3><p>{x[2]}</p></article>)}</div>
      </section>

      <section className={`${styles.section} ${styles.features}`}>
        <div><label>TÍNH NĂNG</label><h2>Tại sao chọn <span>{title}</span>?</h2><p>Không chỉ là panel — chúng tôi xây dựng hệ sinh thái tăng trưởng hoàn chỉnh.</p>
          <div className={styles.featureList}>
            <article><ThunderboltFilled /><div><b>Tốc độ siêu tốc</b><p>Đơn hàng bắt đầu xử lý ngay lập tức, phần lớn hoàn thành chỉ trong vài phút.</p></div></article>
            <article><SafetyCertificateOutlined /><div><b>An toàn và bảo mật</b><p>Không yêu cầu mật khẩu mạng xã hội. Dữ liệu khách hàng luôn được bảo vệ.</p></div></article>
            <article><CodeOutlined /><div><b>API mạnh mẽ</b><p>REST API đầy đủ, tài liệu rõ ràng và tích hợp dễ dàng vào mọi hệ thống.</p></div></article>
            <article><WalletOutlined /><div><b>Thanh toán đa dạng</b><p>Nạp tiền tự động 24/7 qua nhiều phương thức thanh toán phổ biến.</p></div></article>
          </div>
        </div>
        <div className={styles.apiPanel}><header><b>API Dashboard</b><span>Active</span></header><pre><em>POST</em> /v2/order{'\n\n'}{'{'}{'\n'}  &quot;service&quot;: <i>4521</i>,{'\n'}  &quot;link&quot;: <q>https://facebook.com/page</q>,{'\n'}  &quot;quantity&quot;: <strong>10000</strong>{'\n'}{'}'}{'\n\n'}{'// Response'}{'\n'}{'{'} &quot;order&quot;: <strong>2847193</strong>, &quot;status&quot;: <q>done</q> {'}'}</pre><footer><i /><span>85% API Uptime</span></footer></div>
      </section>

      <section className={styles.apiCta} id="api">
        <div><h2>API cho <span>Developer</span></h2><p>REST API mạnh mẽ với đầy đủ endpoint, tài liệu rõ ràng và sẵn sàng tích hợp.</p><Link href="/apidoc" className={styles.gradientButton}><BookOutlined /> Xem tài liệu</Link></div>
        <div className={styles.apiStats}>{[['6','Endpoints'],['<0.3s','Response time'],['99.9%','Uptime SLA'],['3','SDK Languages']].map(x => <article key={x[1]}><b>{x[0]}</b><small>{x[1]}</small></article>)}</div>
      </section>

      <section className={styles.section}>
        <div className={styles.sectionHeading}><label>ĐÁNH GIÁ</label><h2>Khách hàng <span>nói gì</span></h2></div>
        <div className={styles.reviews}>{reviews.map(r => <article className={styles.card} key={r[0]}><div className={styles.stars}>{[1,2,3,4,5].map(x => <StarFilled key={x} />)}</div><p>“{r[3]}”</p><footer><img src={`https://picsum.photos/id/${r[2]}/96/96`} alt={r[0]} /><span><b>{r[0]}</b><small>{r[1]}</small></span></footer></article>)}</div>
      </section>

      <section className={styles.finalCta}>
        <span><GiftOutlined /> Ưu đãi dành cho khách hàng nạp lớn</span>
        <h2>Sẵn sàng <em>bùng nổ</em><br />tương tác?</h2>
        <Link href="/register" className={styles.gradientButton}>Tham gia ngay <ArrowRightOutlined /></Link>
        <p>
          <span><CheckCircleFilled /> Hỗ trợ 24/7</span>
          <span><CheckCircleFilled /> Quy trình chỉ vài giây</span>
          <span><CheckCircleFilled /> API cho reseller</span>
        </p>
      </section>
    </main>

    <footer className={styles.footer}><div className={styles.brand}>{config.logo
      ? <img src={asset(config.logo)} alt={title} />
      : <><span className={styles.brandMark}><ThunderboltFilled /></span><b>{title}</b></>}</div><p>{config.footer_text || `© ${new Date().getFullYear()} ${title}. All rights reserved.`}</p></footer>
  </div>;
}
