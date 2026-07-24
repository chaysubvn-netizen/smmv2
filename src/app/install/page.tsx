'use client';

import {
  ApiOutlined,
  CheckCircleFilled,
  EyeInvisibleOutlined,
  EyeTwoTone,
  LockOutlined,
  MailOutlined,
  SafetyCertificateOutlined,
  SettingOutlined,
  UserOutlined,
} from '@ant-design/icons';
import { Alert, Button, Checkbox, Form, Input, Spin } from 'antd';
import { useRouter } from 'next/navigation';
import { useEffect, useState } from 'react';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from './install.module.css';

type InstallStatus = {
  installed: boolean;
  is_main_site: boolean;
  host: string;
};

type InstallValues = {
  api_key?: string;
  username: string;
  email: string;
  password: string;
  password_confirmation: string;
  condition: boolean;
};

const siteHeaders = () => ({
  'X-Site-Host': typeof window === 'undefined' ? '' : window.location.hostname,
});

export default function InstallPage() {
  const router = useRouter();
  const [status, setStatus] = useState<InstallStatus | null>(null);
  const [checking, setChecking] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    api.get('/install/status', { headers: siteHeaders() })
      .then(({ data }) => {
        const nextStatus = data.data as InstallStatus;
        setStatus(nextStatus);
        if (nextStatus.installed) router.replace('/login');
      })
      .catch((requestError) => {
        setError(requestError.response?.data?.message || 'Không thể kiểm tra trạng thái cài đặt.');
      })
      .finally(() => setChecking(false));
  }, [router]);

  const submit = async (values: InstallValues) => {
    setSubmitting(true);
    setError('');
    try {
      const response = await api.post('/install', values, { headers: siteHeaders() });
      message.success(response.data.message || 'Cài đặt website thành công.');
      router.replace(response.data.redirect || '/login');
    } catch (requestError: any) {
      const detail = requestError.response?.data?.message || 'Cài đặt thất bại. Vui lòng thử lại.';
      setError(detail);
      message.error(detail);
    } finally {
      setSubmitting(false);
    }
  };

  if (checking || status?.installed) {
    return <main className={styles.loading}><Spin size="large" /><p>Đang kiểm tra hệ thống...</p></main>;
  }

  return (
    <main className={styles.page}>
      <section className={styles.formPanel}>
        <div className={styles.formWrap}>
          <div className={styles.mobileBrand}><SettingOutlined /> SMM Panel</div>
          <div className={styles.heading}>
            <span className={styles.kicker}>THIẾT LẬP BAN ĐẦU</span>
            <h1>Cài đặt Website</h1>
            <p>Nhập đầy đủ thông tin để khởi tạo tài khoản quản trị.</p>
          </div>

          {error ? <Alert className={styles.alert} type="error" showIcon title={error} closable onClose={() => setError('')} /> : null}

          <Form layout="vertical" size="large" requiredMark={false} onFinish={submit}>
            {!status?.is_main_site ? (
              <Form.Item name="api_key" rules={[{ required: true, message: 'Vui lòng nhập API KEY.' }]}>
                <Input prefix={<ApiOutlined />} placeholder="Nhập API KEY" autoComplete="off" />
              </Form.Item>
            ) : null}

            <Form.Item label="Tên tài khoản quản trị" name="username" rules={[{ required: true, min: 6, message: 'Tên tài khoản phải có ít nhất 6 ký tự.' }]}>
              <Input prefix={<UserOutlined />} placeholder="Nhập tên tài khoản" autoComplete="username" />
            </Form.Item>

            <Form.Item label="Địa chỉ email" name="email" rules={[{ required: true, type: 'email', message: 'Vui lòng nhập email hợp lệ.' }]}>
              <Input prefix={<MailOutlined />} placeholder="admin@example.com" autoComplete="email" />
            </Form.Item>

            <Form.Item label="Mật khẩu" name="password" rules={[{ required: true, min: 6, message: 'Mật khẩu phải có ít nhất 6 ký tự.' }]}>
              <Input.Password prefix={<LockOutlined />} placeholder="Nhập mật khẩu" autoComplete="new-password" iconRender={(visible) => visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />} />
            </Form.Item>

            <Form.Item label="Xác nhận mật khẩu" name="password_confirmation" dependencies={['password']} rules={[
              { required: true, message: 'Vui lòng xác nhận mật khẩu.' },
              ({ getFieldValue }) => ({ validator(_, value) { return !value || getFieldValue('password') === value ? Promise.resolve() : Promise.reject(new Error('Mật khẩu xác nhận không khớp.')); } }),
            ]}>
              <Input.Password prefix={<SafetyCertificateOutlined />} placeholder="Nhập lại mật khẩu" autoComplete="new-password" />
            </Form.Item>

            <Form.Item name="condition" valuePropName="checked" rules={[{ validator: (_, checked) => checked ? Promise.resolve() : Promise.reject(new Error('Bạn cần đồng ý với điều khoản sử dụng.')) }]}>
              <Checkbox>Tôi đồng ý với <a href="#">điều khoản sử dụng</a></Checkbox>
            </Form.Item>

            <Button className={styles.submit} type="primary" htmlType="submit" block loading={submitting}>
              {submitting ? 'Đang cài đặt...' : 'Cài đặt Website'}
            </Button>
          </Form>

          <p className={styles.host}>Tên miền đang cài đặt: <strong>{status?.host}</strong></p>
        </div>
      </section>

      <aside className={styles.showcase}>
        <div className={styles.glowOne} /><div className={styles.glowTwo} />
        <div className={styles.brand}><span><SettingOutlined /></span><b>SMM Panel</b></div>
        <div className={styles.showcaseContent}>
          <span className={styles.badge}><CheckCircleFilled /> Trình cài đặt an toàn</span>
          <h2>Khởi tạo hệ thống<br />chỉ trong vài phút.</h2>
          <p>Tạo tài khoản quản trị và cấu hình website để bắt đầu vận hành nền tảng SMM của bạn.</p>
          <div className={styles.preview}>
            <div className={styles.previewTop}><i /><i /><i /></div>
            <div className={styles.previewBody}><div className={styles.previewSide} /><div className={styles.previewMain}><span /><div><b /><b /><b /></div><em /><em /></div></div>
          </div>
          <ul><li><CheckCircleFilled /> Bảo mật thông tin quản trị</li><li><CheckCircleFilled /> Tự động cấu hình website</li><li><CheckCircleFilled /> Sẵn sàng sử dụng ngay sau cài đặt</li></ul>
        </div>
      </aside>
    </main>
  );
}
