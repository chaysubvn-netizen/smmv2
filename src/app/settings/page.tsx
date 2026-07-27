'use client';

import { useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import {
  CheckCircleFilled,
  ClockCircleOutlined,
  CopyOutlined,
  DollarOutlined,
  EnvironmentOutlined,
  KeyOutlined,
  LockOutlined,
  MailOutlined,
  PhoneOutlined,
  SafetyCertificateOutlined,
  ReloadOutlined,
  SendOutlined,
  UserOutlined,
  WalletOutlined,
} from '@ant-design/icons';
import { App, Button, Form, Input, Modal } from 'antd';import { message } from '@/lib/antd-message';
import styles from './settings.module.css';

type UserData = {
  name?: string;
  username?: string;
  email?: string;
  phone?: string;
  balance?: number | string;
  total_deposit?: number | string;
  commission?: number | string;
  api_key?: string;
  role?: string;
  level?: string;
  last_login_ip?: string;
  ip?: string;
  current_ip?: string;
  monthly_deposit?: number | string;
  currency?: string;
  balance_display?: number | string;
  monthly_deposit_display?: number | string;
  total_deposit_display?: number | string;
  commission_display?: number | string;
  two_factor_enabled?: boolean;
  two_factor_method?: 'google' | 'telegram' | null;
  telegram_linked?: boolean;
};

type TelegramLinkData = {
  linked: boolean;
  bot_username: string | null;
  bot_url: string | null;
  link_command: string;
  unlink_command: string;
  two_factor_method: string | null;
  instructions: string[];
};

export default function SettingsPage() {
  const { modal } = App.useApp();
  const [user, setUser] = useState<UserData>({});
  const [activeTab, setActiveTab] = useState<'account' | 'security'>('account');
  const [loading, setLoading] = useState(false);
  const [twoFactorLoading, setTwoFactorLoading] = useState(false);
  const [twoFactorSetup, setTwoFactorSetup] = useState<{ secret: string; qr_code: string } | null>(null);
  const [twoFactorCode, setTwoFactorCode] = useState('');
  const [telegramLink, setTelegramLink] = useState<TelegramLinkData | null>(null);
  const [selectedTwoFactor, setSelectedTwoFactor] = useState<'google' | 'telegram' | null>(null);

  useEffect(() => {
    const fetchTelegramLink = async () => {
      try {
        const response = await api.get('/client/telegram-link');
        if (response.data?.status) setTelegramLink(response.data.data);
      } catch (error: any) {
        message.error(error.response?.data?.message || 'Không thể tải thông tin liên kết Telegram.');
      }
    };
    fetchTelegramLink();
  }, []);

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const response = await api.get('/client/profile');
        if (!response.data?.status) return;
        const current = response.data.data as UserData;
        setUser(current);
      } catch (error: any) {
        message.error(error.response?.data?.message || 'Không thể tải dữ liệu tài khoản từ máy chủ.');
      }
    };
    fetchProfile();
  }, []);

  const initials = useMemo(() => {
    const name = user.name || user.username || 'User';
    return name.split(/\s+/).slice(-2).map((word) => word[0]).join('').toUpperCase();
  }, [user.name, user.username]);

  const saveProfile = async (values: Pick<UserData, 'name' | 'phone' | 'email'>) => {
    setLoading(true);
    try {
      const response = await api.put('/client/profile', values);
      if (response.data.status === false) throw new Error(response.data.message);
      const updated = { ...user, ...(response.data.data || values) };
      setUser(updated);
      message.success(response.data.message || 'Đã cập nhật thông tin tài khoản.');
    } catch (error: any) {
      message.error(error.response?.data?.message || error.message || 'Không thể cập nhật thông tin.');
    } finally {
      setLoading(false);
    }
  };

  const changePassword = async (values: { old_password: string; new_password: string; confirm_password: string }) => {
    if (values.new_password !== values.confirm_password) {
      message.error('Mật khẩu xác nhận không khớp.');
      return;
    }
    setLoading(true);
    try {
      const response = await api.post('/client/change-password', values);
      if (response.data.status === false) throw new Error(response.data.message);
      message.success(response.data.message || 'Đổi mật khẩu thành công.');
    } catch (error: any) {
      message.error(error.response?.data?.message || error.message || 'Có lỗi xảy ra.');
    } finally {
      setLoading(false);
    }
  };

  const copy = async (text: string, label = 'Nội dung') => {
    await navigator.clipboard.writeText(text);
    message.success(`Đã sao chép ${label}.`);
  };

  const regenerateApiKey = () => {
    modal.confirm({
      title: 'Đổi API Key?',
      content: 'API Key hiện tại sẽ hết hiệu lực ngay. Các ứng dụng đang sử dụng key cũ cần được cập nhật lại.',
      okText: 'Đổi API Key',
      cancelText: 'Hủy',
      okButtonProps: { danger: true },
      onOk: async () => {
        try {
          const response = await api.post('/client/api-key/regenerate');
          const apiKey = response.data?.data?.api_key as string;
          setUser((current) => {
            const updated = { ...current, api_key: apiKey };
            return updated;
          });
          setTelegramLink((current) => current ? { ...current, link_command: `/lienket ${apiKey}` } : current);
          message.success(response.data.message || 'Đã đổi API Key.');
        } catch (error: any) {
          message.error(error.response?.data?.message || 'Không thể đổi API Key.');
          throw error;
        }
      },
    });
  };

  const prepareGoogleAuthenticator = async () => {
    setTwoFactorLoading(true);
    try {
      const response = await api.get('/client/two-factor/setup');
      if (response.data.enabled) setUser((current) => ({ ...current, two_factor_enabled: true }));
      else {
        setSelectedTwoFactor('google');
        setTwoFactorSetup({ secret: response.data.secret, qr_code: response.data.qr_code });
      }
    } catch (error: any) {
      message.error(error.response?.data?.message || 'Không thể tạo mã Google Authenticator.');
    } finally {
      setTwoFactorLoading(false);
    }
  };

  const sendTelegramCode = async () => {
    if (!user.telegram_linked) {
      message.warning('Vui lòng liên kết Telegram bằng lệnh ở tab Tài khoản trước.');
      return;
    }
    setTwoFactorLoading(true);
    try {
      const response = await api.post('/client/two-factor/telegram/send');
      setSelectedTwoFactor('telegram');
      message.success(response.data.message);
    } catch (error: any) {
      message.error(error.response?.data?.message || 'Không thể gửi mã Telegram.');
    } finally {
      setTwoFactorLoading(false);
    }
  };

  const toggleTwoFactor = async () => {
    if (!/^\d{6}$/.test(twoFactorCode)) {
      message.error('Vui lòng nhập mã xác thực gồm 6 chữ số.');
      return;
    }
    setTwoFactorLoading(true);
    try {
      const endpoint = user.two_factor_enabled
        ? '/client/two-factor/disable'
        : selectedTwoFactor === 'telegram'
          ? '/client/two-factor/telegram/enable'
          : '/client/two-factor/enable';
      const payload = user.two_factor_enabled || selectedTwoFactor === 'telegram'
        ? { code: twoFactorCode }
        : { code: twoFactorCode, secret: twoFactorSetup?.secret };
      const response = await api.post(endpoint, payload);
      const enabled = !user.two_factor_enabled;
      setUser((current) => ({ ...current, two_factor_enabled: enabled, two_factor_method: enabled ? selectedTwoFactor : null }));
      setTwoFactorSetup(null);
      setSelectedTwoFactor(null);
      setTwoFactorCode('');
      message.success(response.data.message);
    } catch (error: any) {
      message.error(error.response?.data?.message || 'Không thể thay đổi xác thực hai lớp.');
    } finally {
      setTwoFactorLoading(false);
    }
  };

  const displayName = user.name || user.username || 'Khách hàng';
  const money = (value?: number | string) => user.currency === 'VND' || !user.currency
    ? `${Math.round(Number(value || 0)).toLocaleString('vi-VN')} đ`
    : Number(value || 0).toLocaleString('en-US', { style: 'currency', currency: user.currency });
  return (
    <ClientLayout>
      <main className={styles.page}>
        <section className={styles.hero}>
          <div className={styles.cover} />
          <div className={styles.heroBody}>
            <div className={styles.avatar}>{initials}</div>
            <div className={styles.heroText}>
              <h1>{displayName}</h1>
              <p>Khách hàng</p>
            </div>
            <Link href="/addfunds" className={styles.depositButton}><WalletOutlined /> Nạp tiền</Link>
          </div>
        </section>

        <nav className={styles.tabs} aria-label="Cài đặt tài khoản">
          <button className={activeTab === 'account' ? styles.activeTab : ''} onClick={() => setActiveTab('account')}>
            <UserOutlined /> Tài khoản
          </button>
          <button className={activeTab === 'security' ? styles.activeTab : ''} onClick={() => setActiveTab('security')}>
            <SafetyCertificateOutlined /> Bảo mật
          </button>
        </nav>

        {activeTab === 'account' ? (
          <div className={styles.accountGrid}>
            <aside className={styles.sidebarCards}>
              <section className={`${styles.card} ${styles.profileCard}`}>
                <span className={styles.badge}>{user.level || 'Thành viên'}</span>
                <div className={styles.smallAvatar}>{initials}</div>
                <h2>{displayName}</h2>
                <p>Khách hàng</p>
                <div className={styles.dashedLine} />
                <ul className={styles.contactList}>
                  <li><MailOutlined /><span>{user.email || 'Chưa cập nhật email'}</span></li>
                  <li><PhoneOutlined /><span>{user.phone || 'Chưa cập nhật số điện thoại'}</span></li>
                  <li><EnvironmentOutlined /><span>{user.current_ip || user.last_login_ip || user.ip || 'Chưa có dữ liệu IP'}</span></li>
                  <li><ClockCircleOutlined /><span>Đang hoạt động</span></li>
                </ul>
              </section>

              <section className={styles.card}>
                <header className={styles.cardHeader}><h2>Số dư</h2></header>
                <div className={styles.balanceList}>
                  <Balance icon={<WalletOutlined />} value={money(user.balance_display ?? user.balance)} label="Số dư ví" />
                  <Balance icon={<DollarOutlined />} value={money(user.monthly_deposit_display ?? user.monthly_deposit)} label="Tổng nạp tháng" />
                  <Balance icon={<DollarOutlined />} value={money(user.total_deposit_display ?? user.total_deposit)} label="Tổng nạp" />
                  <Balance icon={<WalletOutlined />} value={money(user.commission_display ?? user.commission)} label="Số dư hoa hồng" />
                </div>
              </section>
            </aside>

            <div className={styles.mainCards}>
              <section className={styles.card}>
                <header className={`${styles.cardHeader} ${styles.telegramHeader}`}><h2>Liên kết Telegram</h2></header>
                {telegramLink ? (
                  <ol className={styles.instructions}>
                    <li>Truy cập vào {telegramLink.bot_url ? <a href={telegramLink.bot_url} target="_blank" rel="noreferrer">@{telegramLink.bot_username}</a> : <strong>bot Telegram của hệ thống</strong>}</li>
                    <li>Nhập lệnh <button onClick={() => copy(telegramLink.link_command, 'lệnh liên kết')}>{telegramLink.link_command} <CopyOutlined /></button></li>
                    <li>{telegramLink.instructions[2]}</li>
                    <li>Muốn hủy liên kết, vui lòng nhập lệnh <strong>{telegramLink.unlink_command}</strong>.</li>
                    <li><span className={telegramLink.linked ? styles.linkedStatus : styles.unlinkedStatus}>{telegramLink.linked ? 'Đã liên kết Telegram' : 'Chưa liên kết Telegram'}</span></li>
                  </ol>
                ) : <div className={styles.telegramLoading}>Đang tải thông tin Telegram...</div>}
              </section>

              <section className={styles.card}>
                <header className={styles.cardHeader}><h2>Thông tin tài khoản</h2></header>
                <Form key={`${user.email || ''}-${user.phone || ''}-${user.name || ''}`} initialValues={user} layout="vertical" onFinish={saveProfile} className={styles.formBody}>
                  <div className={styles.formGrid}>
                    <Form.Item label="Họ và tên" name="name"><Input placeholder="Nhập họ và tên" /></Form.Item>
                    <Form.Item label="Tên người dùng"><Input value={user.username || ''} disabled /></Form.Item>
                    <Form.Item label="Số điện thoại" name="phone" rules={[{ required: true, message: 'Vui lòng nhập số điện thoại' }]}><Input placeholder="Nhập số điện thoại" /></Form.Item>
                    <Form.Item label="Email" name="email" rules={[{ required: true, type: 'email', message: 'Email chưa hợp lệ' }]}><Input placeholder="Nhập email của bạn" /></Form.Item>
                  </div>
                  <div className={styles.formActions}><Button type="primary" htmlType="submit" loading={loading}><CheckCircleFilled /> Cập nhật</Button></div>
                </Form>
              </section>

              <section className={styles.card}>
                <header className={styles.cardHeader}><h2>Thông tin API</h2></header>
                <div className={styles.apiRow}>
                  <Input value={user.api_key || 'Chưa có API Key'} readOnly prefix={<KeyOutlined />} />
                  <Button onClick={() => copy(user.api_key || '', 'API Key')} disabled={!user.api_key}><CopyOutlined /> Sao chép</Button>
                  <Button danger onClick={regenerateApiKey}><ReloadOutlined /> Đổi API Key</Button>
                </div>
              </section>
            </div>
          </div>
        ) : (
          <div className={styles.securityGrid}>
            <section className={styles.card}>
              <header className={styles.cardHeader}><h2>Đổi mật khẩu</h2></header>
              <Form layout="vertical" onFinish={changePassword} className={styles.formBody}>
                <Form.Item label="Mật khẩu hiện tại" name="old_password" rules={[{ required: true, message: 'Vui lòng nhập mật khẩu hiện tại' }]}><Input.Password prefix={<LockOutlined />} /></Form.Item>
                <Form.Item label="Mật khẩu mới" name="new_password" rules={[{ required: true, min: 8, message: 'Mật khẩu cần ít nhất 8 ký tự' }]}><Input.Password prefix={<KeyOutlined />} /></Form.Item>
                <Form.Item label="Xác nhận mật khẩu mới" name="confirm_password" rules={[{ required: true, message: 'Vui lòng xác nhận mật khẩu' }]}><Input.Password prefix={<KeyOutlined />} /></Form.Item>
                <div className={styles.formActions}><Button type="primary" htmlType="submit" loading={loading}><LockOutlined /> Đổi mật khẩu</Button></div>
              </Form>
            </section>

            <section className={styles.card}>
              <header className={styles.cardHeader}><h2>Xác thực hai lớp</h2></header>
              <div className={styles.twoFactorBody}>
                <div className={styles.shield}><SafetyCertificateOutlined /></div>
                <h3>Bảo vệ tài khoản của bạn</h3>
                <p>Sử dụng mã xác thực từ Google Authenticator hoặc Telegram để tăng cường bảo mật.</p>
                {!user.two_factor_enabled && !twoFactorSetup ? (
                  <div className={styles.authChoices}>
                    <button onClick={prepareGoogleAuthenticator} disabled={twoFactorLoading}><span className={styles.googleMark}>G</span> Google Authenticator</button>
                    <button onClick={sendTelegramCode} disabled={twoFactorLoading}><SendOutlined /> Telegram Authenticator</button>
                  </div>
                ) : null}
                {twoFactorSetup ? (
                  <div className={styles.setupBox}>
                    <img src={twoFactorSetup.qr_code} alt="QR Google Authenticator" />
                    <p>Quét mã QR bằng Google Authenticator, hoặc nhập khóa:</p>
                    <button className={styles.secret} onClick={() => copy(twoFactorSetup.secret, 'khóa bí mật')}>{twoFactorSetup.secret} <CopyOutlined /></button>
                  </div>
                ) : null}
                {selectedTwoFactor === 'telegram' && !user.two_factor_enabled ? <p className={styles.telegramHint}>Mã OTP đã được gửi đến tài khoản Telegram liên kết.</p> : null}
                {user.two_factor_enabled ? <p className={styles.enabledMethod}>Đang sử dụng: <strong>{user.two_factor_method === 'telegram' ? 'Telegram Authenticator' : 'Google Authenticator'}</strong></p> : null}
                {user.two_factor_enabled && user.two_factor_method === 'telegram' ? <Button className={styles.sendAgain} onClick={sendTelegramCode} loading={twoFactorLoading}><SendOutlined /> Gửi mã tắt 2FA</Button> : null}
                {(user.two_factor_enabled || twoFactorSetup || selectedTwoFactor === 'telegram') ? (
                  <div className={styles.otpForm}>
                    <Input value={twoFactorCode} onChange={(event) => setTwoFactorCode(event.target.value.replace(/\D/g, '').slice(0, 6))} placeholder="Nhập mã xác thực 6 số" maxLength={6} />
                    <Button type={user.two_factor_enabled ? 'default' : 'primary'} danger={user.two_factor_enabled} loading={twoFactorLoading} onClick={toggleTwoFactor}>
                      <SafetyCertificateOutlined /> {user.two_factor_enabled ? 'Tắt xác thực' : 'Bật xác thực'}
                    </Button>
                  </div>
                ) : null}
                <small>{user.two_factor_enabled ? 'Xác thực hai lớp đang được bật.' : 'Chọn một phương thức xác thực để bắt đầu.'}</small>
              </div>
            </section>
          </div>
        )}
      </main>
    </ClientLayout>
  );
}

function Balance({ icon, value, label }: { icon: React.ReactNode; value: string; label: string }) {
  return <div className={styles.balanceItem}><span className={styles.balanceIcon}>{icon}</span><div><strong>{value}</strong><small>{label}</small></div></div>;
}
