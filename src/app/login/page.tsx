'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Button, Form, Input } from 'antd';
import { ArrowLeftOutlined, GoogleOutlined, LockOutlined, UserOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from '../auth.module.css';

type LoginConfig = { title?: string; logo?: string; google_login_status?: string };

export default function LoginPage() {
  const router = useRouter();
  const [config, setConfig] = useState<LoginConfig>({});
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (localStorage.getItem('token') && localStorage.getItem('user')) {
      router.replace('/new');
      return;
    }
    const googleError = new URLSearchParams(window.location.search).get('google_error');
    if (googleError) message.error(googleError);
    api.get('/client/config').then(response => {
      if (response.data?.status) setConfig(response.data.data);
    }).catch(() => undefined);
  }, [router]);

  const submit = async (values: Record<string, string>) => {
    setLoading(true);
    try {
      const response = await api.post('/auth/api/login', values);
      if (!response.data.status) return message.error(response.data.message);
      localStorage.setItem('token', response.data.access_token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      message.success(response.data.message || 'Đăng nhập thành công');
      router.push('/new');
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể đăng nhập');
    } finally {
      setLoading(false);
    }
  };

  const apiRoot = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
  const logo = config.logo ? (config.logo.startsWith('http') ? config.logo : `${apiRoot}${config.logo.startsWith('/') ? '' : '/'}${config.logo}`) : '';

  return <main className={styles.page}>
    <section className={styles.visual}>
      <Link href="/" className={styles.back}><ArrowLeftOutlined /> Trang chủ</Link>
      <div className={styles.visualInner}>
        <div className={styles.logoMark}>{logo ? <img src={logo} alt="Logo" /> : 'S'}</div>
        <h1>{config.title || 'SMM Panel'}</h1>
        <p>Nền tảng quản lý dịch vụ mạng xã hội nhanh chóng và chuyên nghiệp.</p>
        <ul className={styles.benefits}><li>Giá sỉ cực rẻ, cạnh tranh nhất thị trường</li><li>Xử lý đơn hàng hoàn toàn tự động 24/7</li><li>Hỗ trợ đa nền tảng: Facebook, TikTok, Instagram...</li><li>Bảo mật thông tin và an toàn cho tài khoản</li></ul>
        <div className={styles.dots}>● ● ●</div>
      </div>
    </section>
    <section className={styles.formSide}>
      <div className={styles.formWrap}>
        <span className={styles.eyebrow}>CHÀO MỪNG TRỞ LẠI</span>
        <h2>Đăng nhập tài khoản</h2>
        <p className={styles.muted}>Nhập thông tin để tiếp tục sử dụng hệ thống.</p>
        {config.google_login_status === 'active' && <>
          <Button className={styles.googleButton} size="large" block icon={<GoogleOutlined />} onClick={() => { window.location.href = `${apiRoot}/auth/google`; }}>Đăng nhập bằng Google</Button>
          <div className={styles.divider}><span>hoặc</span></div>
        </>}
        <Form layout="vertical" size="large" onFinish={submit}>
          <Form.Item label="Tên người dùng" name="username" rules={[{ required: true, message: 'Vui lòng nhập tên người dùng' }]}><Input prefix={<UserOutlined />} placeholder="Nhập tên người dùng" /></Form.Item>
          <Form.Item label="Mật khẩu" name="password" rules={[{ required: true, message: 'Vui lòng nhập mật khẩu' }]}><Input.Password prefix={<LockOutlined />} placeholder="Nhập mật khẩu" /></Form.Item>
          <Button type="primary" htmlType="submit" block loading={loading}>Đăng nhập</Button>
        </Form>
        <p className={styles.switch}>Chưa có tài khoản? <Link href="/register">Đăng ký ngay</Link></p>
      </div>
    </section>
  </main>;
}
