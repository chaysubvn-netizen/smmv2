'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import {
  AppstoreOutlined,
  CodeOutlined,
  ClockCircleOutlined,
  CopyOutlined,
  GoogleOutlined,
  GlobalOutlined,
  LinkOutlined,
  SafetyCertificateOutlined,
  SaveOutlined,
  ShareAltOutlined,
  SettingOutlined,
  ShopOutlined,
  TrophyOutlined,
  UploadOutlined,
} from '@ant-design/icons';
import { Button, Form, Image, Input, InputNumber, Select, Space, Spin, Switch, Upload } from 'antd';import { message } from '@/lib/antd-message';
import type { InputNumberProps } from 'antd';
import type { UploadProps } from 'antd';
import api from '@/lib/axios';
import styles from './settings.module.css';

type Config = Record<string, string | number | boolean | null | undefined> & { id: number };
type SectionKey = 'general' | 'socialSeo' | 'oauth' | 'appearance' | 'rank' | 'payment' | 'affiliate' | 'security' | 'cron' | 'scripts';
type ApiResponse = { data: { data: Config[] }; meta: { editable: string[] } };

const sections: { key: SectionKey; label: string; hint: string; icon: React.ReactNode }[] = [
  { key: 'general', label: 'Cài đặt chung', hint: 'Tên website, SEO và múi giờ', icon: <SettingOutlined /> },
  { key: 'socialSeo', label: 'SEO Mạng xã hội', hint: 'Open Graph và Twitter Card', icon: <ShareAltOutlined /> },
  { key: 'oauth', label: 'Google OAuth', hint: 'Đăng nhập bằng tài khoản Google', icon: <GoogleOutlined /> },
  { key: 'appearance', label: 'Giao diện', hint: 'Logo, màu sắc và trang chủ', icon: <AppstoreOutlined /> },
  { key: 'rank', label: 'Cấp bậc thành viên', hint: 'Điều kiện nâng hạng tài khoản', icon: <TrophyOutlined /> },
  { key: 'payment', label: 'Nạp tiền', hint: 'Khuyến mãi và mã chuyển khoản', icon: <ShopOutlined /> },
  { key: 'affiliate', label: 'Tiếp thị liên kết', hint: 'Hoa hồng và giới hạn', icon: <LinkOutlined /> },
  { key: 'security', label: 'Bảo mật', hint: 'Anti-DDoS và chế độ bảo vệ', icon: <SafetyCertificateOutlined /> },
  { key: 'cron', label: 'Cronjob', hint: 'Link chạy tác vụ tự động', icon: <ClockCircleOutlined /> },
  { key: 'scripts', label: 'Mã tùy chỉnh', hint: 'Script tại header, body, footer', icon: <CodeOutlined /> },
];

const sectionFields: Record<SectionKey, string[]> = {
  general: ['title', 'description', 'keywords', 'timezone', 'currency', 'maintenance_mode', 'status_demo', 'auto_update', 'facebook_link', 'zalo_link', 'telegram_link', 'email_link', 'footer_text', 'system_version', 'developer_name', 'developer_url'],
  socialSeo: ['og_title', 'og_description', 'og_image', 'twitter_card_type', 'twitter_title', 'twitter_description', 'twitter_image'],
  oauth: ['google_login_status', 'google_client_id', 'google_client_secret', 'google_redirect_uri'],
  appearance: ['logo', 'favicon', 'og_image', 'landing_page', 'theme_client', 'theme_color_primary', 'theme_color_sidebar', 'theme_mode', 'theme_effect', 'mobile_bottom_nav_status'],
  rank: ['up_silver', 'up_gold', 'up_platinum', 'up_diamond', 'silver_rank', 'gold_rank', 'platinum_rank', 'diamond_rank'],
  payment: ['transfer_code', 'recharge_promotion', 'promotion_min', 'bank_status', 'usdt_status', 'binance_status', 'binance_id', 'binance_qr', 'binance_api_key', 'binance_secret_key', 'trc20_status', 'trc20_wallet', 'trc20_contract', 'trongrid_api_key'],
  affiliate: ['affiliate_status', 'affiliate_percent', 'affiliate_min', 'affiliate_max'],
  security: ['antiddos_status'],
  cron: [],
  scripts: ['script_header', 'script_body', 'script_footer'],
};

export default function AdminSettingsPage() {
  const [form] = Form.useForm();
  const [active, setActive] = useState<SectionKey>('general');
  const [config, setConfig] = useState<Config | null>(null);
  const [editable, setEditable] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [demoSaving, setDemoSaving] = useState(false);
  const [siteUrl, setSiteUrl] = useState('https://example.com');
  const [cronUrl, setCronUrl] = useState('');
  const [cronLoading, setCronLoading] = useState(false);
  const seoTitle = Form.useWatch('title', form);
  const seoDescription = Form.useWatch('description', form);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get<ApiResponse>('/admin/resources/settings', { params: { per_page: 10 } });
      const current = response.data.data.data?.[0];
      if (!current) throw new Error('Chưa có bản ghi cấu hình cho tên miền này.');
      setConfig(current);
      setEditable(response.data.meta.editable || []);
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } }; message?: string };
      message.error(detail.response?.data?.message || detail.message || 'Không thể tải cấu hình website.');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);
  useEffect(() => { setSiteUrl(window.location.origin); }, []);
  useEffect(() => {
    if (active !== 'cron' || cronUrl) return;
    setCronLoading(true);
    api.get('/admin/cron-link')
      .then(response => {
        const path = response.data?.data?.path;
        const laravelOrigin = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
        setCronUrl(path ? `${laravelOrigin}${path}` : (response.data?.data?.url || ''));
      })
      .catch(() => message.error('Không thể tải liên kết Cronjob.'))
      .finally(() => setCronLoading(false));
  }, [active, cronUrl]);
  useEffect(() => {
    if (config && !loading) {
      form.setFieldsValue({ ...config, status_demo: String(config.status_demo ?? 0), mobile_bottom_nav_status: Number(config.mobile_bottom_nav_status) === 1, auto_update: Number(config.auto_update) === 1 });
    }
  }, [config, form, loading]);

  const availableFields = useMemo(() => new Set(editable), [editable]);
  const show = (field: string) => availableFields.has(field);

  const toggleDemoMode = async (value: string) => {
    const enabled = value === '1';
    setDemoSaving(true);
    try {
      const response = await api.put('/admin/settings/demo-mode', { enabled });
      const statusDemo = String(response.data.data.status_demo);
      setConfig(current => current ? { ...current, status_demo: statusDemo } : current);
      form.setFieldValue('status_demo', statusDemo);
      message.success(response.data.message);
    } catch (error: unknown) {
      form.setFieldValue('status_demo', String(config?.status_demo ?? 0));
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể thay đổi chế độ Demo.');
    } finally {
      setDemoSaving(false);
    }
  };

  const save = async () => {
    if (!config) return;
    setSaving(true);
    try {
      const values = await form.validateFields();
      const data = Object.fromEntries(editable.filter(field => field in values).map(field => [field, values[field]]));
      if ('mobile_bottom_nav_status' in data) data.mobile_bottom_nav_status = data.mobile_bottom_nav_status ? 1 : 0;
      if ('auto_update' in data) data.auto_update = data.auto_update ? 1 : 0;
      const response = await api.put(`/admin/resources/settings/${config.id}`, { data });
      const updated = { ...config, ...(response.data.data || data) };
      setConfig(updated);
      form.setFieldsValue({ ...updated, status_demo: String(updated.status_demo ?? 0), mobile_bottom_nav_status: Number(updated.mobile_bottom_nav_status) === 1, auto_update: Number(updated.auto_update) === 1 });
      message.success('Đã lưu cấu hình website.');
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể lưu cấu hình. Vui lòng kiểm tra lại dữ liệu.');
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <div className={styles.loading}><Spin size="large" /><span>Đang tải cấu hình...</span></div>;

  return <main className={styles.page}>
    <header className={styles.heading}>
      <div>
        <span className={styles.eyebrow}><GlobalOutlined /> Quản trị hệ thống</span>
        <h1>Cấu hình website</h1>
        <p>Quản lý thông tin và thiết lập vận hành cho website của bạn.</p>
      </div>
      <span className={`${styles.systemStatus} ${config?.maintenance_mode === 'on' ? styles.warning : ''}`}>
        <i /> {config?.maintenance_mode === 'on' ? 'Đang bảo trì' : 'Hệ thống hoạt động'}
      </span>
    </header>

    <Form form={form} layout="vertical" className={styles.settingsCard} onFinish={save}>
      <aside className={styles.sidebar} aria-label="Nhóm cấu hình">
        <div className={styles.sidebarTitle}>CẤU HÌNH</div>
        {sections.map(section => <button key={section.key} type="button" className={active === section.key ? styles.activeNav : ''} onClick={() => setActive(section.key)}>
          <span className={styles.navIcon}>{section.icon}</span>
          <span><strong>{section.label}</strong><small>{section.hint}</small></span>
        </button>)}
      </aside>

      <section className={styles.formPanel}>
        <div className={styles.panelHeading}>
          <span className={styles.panelIcon}>{sections.find(item => item.key === active)?.icon}</span>
          <div><h2>{sections.find(item => item.key === active)?.label}</h2><p>{sections.find(item => item.key === active)?.hint}</p></div>
        </div>

        <div className={styles.formGrid}>
          {active === 'socialSeo' && <>
            <div className={styles.socialSeoHeading}><ShareAltOutlined /><h3>Open Graph (Facebook)</h3></div>
            {show('og_title') && <Form.Item name="og_title" label="OG Title"><Input placeholder="Tiêu đề khi chia sẻ lên Facebook" /></Form.Item>}
            {show('og_image') && <Form.Item name="og_image" label="OG Image"><SettingsImageUpload field="og_image" hint="Khuyến nghị 1200 × 630px" wide /></Form.Item>}
            {show('og_description') && <Form.Item className={styles.full} name="og_description" label="OG Description"><Input.TextArea rows={3} placeholder="Mô tả nội dung khi chia sẻ lên mạng xã hội" /></Form.Item>}
            <div className={styles.socialSeoDivider} />
            <div className={styles.socialSeoHeading}><ShareAltOutlined /><h3>Twitter Card</h3></div>
            {show('twitter_card_type') && <Form.Item name="twitter_card_type" label="Twitter Card Type"><Select options={[{ value: 'summary_large_image', label: 'Summary Large Image' }, { value: 'summary', label: 'Summary' }]} /></Form.Item>}
            {show('twitter_image') && <Form.Item name="twitter_image" label="Twitter Image"><SettingsImageUpload field="twitter_image" hint="Khuyến nghị 1200 × 630px" wide /></Form.Item>}
            {show('twitter_title') && <Form.Item name="twitter_title" label="Twitter Title"><Input placeholder="Tiêu đề Twitter Card" /></Form.Item>}
            {show('twitter_description') && <Form.Item name="twitter_description" label="Twitter Description"><Input.TextArea rows={3} placeholder="Mô tả Twitter Card" /></Form.Item>}
          </>}
          {active === 'general' && <>
            {show('title') && <Form.Item name="title" label="Tên website" rules={[{ required: true, message: 'Vui lòng nhập tên website' }]}><Input placeholder="Tên thương hiệu của bạn" /></Form.Item>}
            {show('timezone') && <Form.Item name="timezone" label="Múi giờ"><Select showSearch options={['Asia/Ho_Chi_Minh', 'Asia/Bangkok', 'UTC'].map(value => ({ value, label: value }))} /></Form.Item>}
            {show('description') && <Form.Item className={styles.full} name="description" label="Mô tả website"><Input.TextArea rows={3} placeholder="Mô tả ngắn hiển thị trên công cụ tìm kiếm" /></Form.Item>}
            {show('keywords') && <Form.Item className={styles.full} name="keywords" label="Từ khóa SEO"><Input placeholder="smm, social media, marketing..." /></Form.Item>}
            {show('currency') && <Form.Item name="currency" label="Tiền tệ"><Select options={['VND', 'USD', 'THB'].map(value => ({ value, label: value }))} /></Form.Item>}
            {show('maintenance_mode') && <Form.Item name="maintenance_mode" label="Chế độ vận hành"><Select options={[{ value: 'off', label: 'Hoạt động bình thường' }, { value: 'on', label: 'Bật chế độ bảo trì' }]} /></Form.Item>}
            {show('status_demo') && <Form.Item name="status_demo" label="Trạng thái Demo" extra="Khi bật, toàn hệ thống chỉ được xem dữ liệu; mọi thao tác thay đổi sẽ bị chặn."><Select loading={demoSaving} onChange={toggleDemoMode} options={[{ value: '1', label: 'Bật chế độ demo' }, { value: '0', label: 'Tắt chế độ demo' }]} /></Form.Item>}
            {show('auto_update') && <Form.Item name="auto_update" label="Tự động cập nhật hệ thống" valuePropName="checked"><Switch checkedChildren="Bật" unCheckedChildren="Tắt" /></Form.Item>}
            <div className={styles.sectionLabel}>Kênh hỗ trợ khách hàng</div>
            {show('facebook_link') && <Form.Item name="facebook_link" label="Facebook Support"><Input placeholder="https://facebook.com/..." /></Form.Item>}
            {show('zalo_link') && <Form.Item name="zalo_link" label="Zalo Support"><Input placeholder="https://zalo.me/..." /></Form.Item>}
            {show('telegram_link') && <Form.Item name="telegram_link" label="Telegram Support"><Input placeholder="https://t.me/..." /></Form.Item>}
            <section className={styles.otherInfo}>
              <h3>Thông tin khác</h3>
              <div className={styles.otherInfoGrid}>
                {show('email_link') && <Form.Item name="email_link" label="Email liên hệ"><Input type="email" placeholder="cskh@example.com" /></Form.Item>}
                {show('footer_text') && <Form.Item name="footer_text" label="Footer text"><Input placeholder={`© ${new Date().getFullYear()} ${String(seoTitle || 'Website')}. All rights reserved.`} /></Form.Item>}
                {show('system_version') && <Form.Item name="system_version" label="Phiên bản hệ thống"><Input placeholder="1.0.0" /></Form.Item>}
                {show('developer_name') && <Form.Item name="developer_name" label="Đơn vị phát triển (Tên hiển thị)"><Input placeholder="Tên đơn vị phát triển" /></Form.Item>}
                {show('developer_url') && <Form.Item name="developer_url" label="Link đơn vị phát triển" rules={[{ type: 'url', warningOnly: true }]}><Input placeholder="https://example.com" /></Form.Item>}
              </div>
            </section>
            <section className={styles.seoPreviewSection}>
              <h3>Xem trước SEO (Google)</h3>
              <div className={styles.googlePreview}>
                <div className={styles.googleTitle}>{String(seoTitle || 'Tên website của bạn')}</div>
                <div className={styles.googleUrl}>{siteUrl}</div>
                <p>{String(seoDescription || 'Mô tả website sẽ hiển thị tại đây khi khách hàng tìm thấy website trên Google.')}</p>
              </div>
            </section>
          </>}

          {active === 'oauth' && <>
            <div className={styles.notice}>Cho phép khách hàng đăng nhập nhanh bằng Google. Redirect URI phải trùng chính xác với URI đã khai báo trong Google Cloud Console.</div>
            {show('google_login_status') && <Form.Item name="google_login_status" label="Trạng thái Google Login"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            {show('google_client_id') && <Form.Item name="google_client_id" label="Google Client ID"><Input placeholder="Client ID từ Google Cloud Console" /></Form.Item>}
            {show('google_client_secret') && <Form.Item name="google_client_secret" label="Google Client Secret"><Input.Password autoComplete="new-password" placeholder="Client Secret" /></Form.Item>}
            {show('google_redirect_uri') && <Form.Item className={styles.full} name="google_redirect_uri" label="Redirect URI"><Input placeholder="https://domain.com/auth/google/callback" /></Form.Item>}
          </>}

          {active === 'appearance' && <>
            {show('logo') && <Form.Item name="logo" label="Logo"><SettingsImageUpload field="logo" hint="PNG, JPG, GIF, WebP hoặc SVG" /></Form.Item>}
            {show('favicon') && <Form.Item name="favicon" label="Favicon"><SettingsImageUpload field="favicon" hint="ICO, PNG hoặc SVG" compact /></Form.Item>}
            {show('og_image') && <Form.Item className={styles.full} name="og_image" label="Ảnh chia sẻ mạng xã hội"><SettingsImageUpload field="og_image" hint="Khuyến nghị 1200 × 630px" wide /></Form.Item>}
            {show('landing_page') && <Form.Item name="landing_page" label="Giao diện Landing Page"><Select options={[{ value: 'none', label: 'Không sử dụng' }, { value: 'default', label: 'Mặc định' }, { value: 'landing2', label: 'Landing 2' }]} /></Form.Item>}
            {show('theme_client') && <Form.Item name="theme_client" label="Giao diện khách hàng (Mã code)"><Input placeholder="default" /></Form.Item>}
            {show('client_nav_style') && <Form.Item name="client_nav_style" label="Kiểu Menu (Sidebar Khách Hàng)"><Select options={[{ value: 'nav1', label: 'Nav 1: Dạng danh sách (Mặc định)' }, { value: 'nav2', label: 'Nav 2: Dạng lưới mảng (Mới)' }]} /></Form.Item>}
            {show('theme_color_primary') && <Form.Item name="theme_color_primary" label="Màu chủ đạo"><Input type="color" className={styles.colorInput} /></Form.Item>}
            {show('theme_color_sidebar') && <Form.Item name="theme_color_sidebar" label="Màu nền Sidebar"><Input type="color" className={styles.colorInput} /></Form.Item>}
            {show('theme_mode') && <Form.Item name="theme_mode" label="Chế độ màu"><Select options={[{ value: 'light', label: 'Sáng' }, { value: 'dark', label: 'Tối' }, { value: 'auto', label: 'Theo thiết bị' }]} /></Form.Item>}
            {show('theme_effect') && <Form.Item name="theme_effect" label="Hiệu ứng giao diện"><Select options={[{ value: 'none', label: 'Tắt hiệu ứng' }, { value: 'snow', label: 'Tuyết rơi' }, { value: 'tet', label: 'Hoa đào Tết' }, { value: 'noel', label: 'Giáng sinh' }]} /></Form.Item>}
            {show('mobile_bottom_nav_status') && <Form.Item name="mobile_bottom_nav_status" label="Thanh điều hướng mobile" valuePropName="checked"><Switch checkedChildren="Bật" unCheckedChildren="Tắt" /></Form.Item>}
          </>}

          {active === 'rank' && ['silver', 'gold', 'platinum', 'diamond'].map(rank => <div className={styles.rankBox} key={rank}>
            <h3>{rank.charAt(0).toUpperCase() + rank.slice(1)}</h3>
            {show(`up_${rank}`) && <Form.Item name={`up_${rank}`} label="Tổng nạp để lên hạng"><NumberWithUnit unit="₫" /></Form.Item>}
            {show(`${rank}_rank`) && <Form.Item name={`${rank}_rank`} label="Ưu đãi / chiết khấu"><NumberWithUnit unit="%" /></Form.Item>}
          </div>)}

          {active === 'payment' && <>
            {show('transfer_code') && <Form.Item name="transfer_code" label="Tiền tố nội dung chuyển khoản"><Input placeholder="NAP" /></Form.Item>}
            {show('recharge_promotion') && <Form.Item name="recharge_promotion" label="Khuyến mãi nạp tiền"><NumberWithUnit unit="%" /></Form.Item>}
            {show('promotion_min') && <Form.Item name="promotion_min" label="Số tiền tối thiểu nhận khuyến mãi"><NumberWithUnit unit="₫" /></Form.Item>}
            {show('bank_status') && <Form.Item name="bank_status" label="Chuyển khoản ngân hàng"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            {show('usdt_status') && <Form.Item name="usdt_status" label="Thanh toán USDT"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            <div className={styles.sectionLabel}>Binance Pay</div>
            {show('binance_status') && <Form.Item name="binance_status" label="Trạng thái Binance"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            {show('binance_id') && <Form.Item name="binance_id" label="Binance ID nhận tiền"><Input placeholder="Binance ID" /></Form.Item>}
            {show('binance_qr') && <Form.Item name="binance_qr" label="Link QR Binance"><Input placeholder="https://.../qr.png" /></Form.Item>}
            {show('binance_api_key') && <Form.Item name="binance_api_key" label="Binance API Key"><Input.Password autoComplete="new-password" /></Form.Item>}
            {show('binance_secret_key') && <Form.Item name="binance_secret_key" label="Binance Secret Key"><Input.Password autoComplete="new-password" /></Form.Item>}
            <div className={styles.sectionLabel}>USDT TRC20 Auto</div>
            {show('trc20_status') && <Form.Item name="trc20_status" label="Trạng thái TRC20"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            {show('trc20_contract') && <Form.Item name="trc20_contract" label="Địa chỉ hợp đồng USDT (Contract)"><Input placeholder="TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj" /></Form.Item>}
            {show('trc20_wallet') && <Form.Item name="trc20_wallet" label="Địa chỉ ví nhận tiền (TRC20)"><Input placeholder="T..." /></Form.Item>}
            {show('trongrid_api_key') && <Form.Item name="trongrid_api_key" label="TronGrid API Key (không bắt buộc)"><Input.Password autoComplete="new-password" /></Form.Item>}
          </>}

          {active === 'affiliate' && <>
            {show('affiliate_status') && <Form.Item name="affiliate_status" label="Trạng thái chương trình"><Select options={[{ value: 'active', label: 'Đang bật' }, { value: 'inactive', label: 'Đang tắt' }]} /></Form.Item>}
            {show('affiliate_percent') && <Form.Item name="affiliate_percent" label="Phần trăm hoa hồng"><NumberWithUnit unit="%" max={100} /></Form.Item>}
            {show('affiliate_min') && <Form.Item name="affiliate_min" label="Hoa hồng tối thiểu"><NumberWithUnit unit="₫" /></Form.Item>}
            {show('affiliate_max') && <Form.Item name="affiliate_max" label="Hoa hồng tối đa"><NumberWithUnit unit="₫" /></Form.Item>}
          </>}

          {active === 'security' && <>
            <div className={styles.notice}>Khi bật Anti-DDoS, hệ thống kiểm tra danh sách IP bị chặn và áp dụng chế độ bảo vệ nghiêm ngặt hơn.</div>
            {show('antiddos_status') && <Form.Item name="antiddos_status" label="Trạng thái Anti-DDoS"><Select options={[{ value: 'active', label: 'Bật chế độ bảo vệ' }, { value: 'inactive', label: 'Tắt' }]} /></Form.Item>}
          </>}

          {active === 'cron' && <section className={styles.cronSection}>
            <div className={styles.cronInfo}>
              <ClockCircleOutlined />
              <div><h3>Chạy toàn bộ Cronjob</h3><p>Cấu hình dịch vụ cron gọi liên kết này định kỳ mỗi phút. Link đã gồm mã bảo mật, không chia sẻ công khai.</p></div>
            </div>
            <label>Link Cronjob</label>
            <Space.Compact className={styles.cronLink}>
              <Input value={cronUrl} readOnly placeholder={cronLoading ? 'Đang tạo liên kết...' : 'Không có liên kết'} />
              <Button type="primary" icon={<CopyOutlined />} loading={cronLoading} disabled={!cronUrl} onClick={() => {
                navigator.clipboard.writeText(cronUrl).then(() => message.success('Đã sao chép link Cronjob.')).catch(() => message.error('Không thể sao chép tự động.'));
              }}>Sao chép</Button>
            </Space.Compact>
            <div className={styles.cronCommand}><span>Lệnh mẫu</span><code>{cronUrl ? `curl -fsS "${cronUrl}"` : 'Đang tải...'}</code></div>
            <div className={styles.cronSchedule}><strong>Tần suất đề xuất</strong><code>* * * * *</code><span>Mỗi phút một lần</span></div>
          </section>}

          {active === 'scripts' && sectionFields.scripts.filter(show).map(field => <Form.Item className={styles.full} name={field} label={{ script_header: 'Script trong <head>', script_body: 'Script đầu <body>', script_footer: 'Script cuối <body>' }[field]} key={field}>
            <Input.TextArea className={styles.code} rows={6} placeholder="<!-- Mã HTML hoặc JavaScript tùy chỉnh -->" />
          </Form.Item>)}
        </div>

        <footer className={styles.actions}>
          <span>Mọi thay đổi sẽ áp dụng cho tên miền hiện tại.</span>
          <Button type="primary" htmlType="submit" icon={<SaveOutlined />} loading={saving}>Lưu thay đổi</Button>
        </footer>
      </section>
    </Form>
  </main>;
}

function NumberWithUnit({ unit, ...inputProps }: { unit: string } & InputNumberProps) {
  return <Space.Compact className={styles.numberCompact}>
    <InputNumber {...inputProps} min={0} className={styles.number} />
    <span className={styles.numberUnit}>{unit}</span>
  </Space.Compact>;
}

function SettingsImageUpload({
  field,
  value,
  onChange,
  hint,
  compact = false,
  wide = false,
}: {
  field: 'logo' | 'favicon' | 'og_image' | 'twitter_image';
  value?: string;
  onChange?: (value: string) => void;
  hint: string;
  compact?: boolean;
  wide?: boolean;
}) {
  const [uploading, setUploading] = useState(false);
  const apiRoot = String(api.defaults.baseURL || '').replace(/\/api\/?$/, '');
  const preview = value ? (/^https?:\/\//i.test(value) ? value : `${apiRoot}${value.startsWith('/') ? '' : '/'}${value}`) : '';

  const beforeUpload: UploadProps['beforeUpload'] = file => {
    const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'];
    if (!allowed.includes(file.type) && !file.name.toLowerCase().endsWith('.ico')) {
      message.error('Chỉ hỗ trợ JPG, PNG, GIF, WebP, SVG hoặc ICO.');
      return Upload.LIST_IGNORE;
    }
    if (file.size > 4 * 1024 * 1024) {
      message.error('Dung lượng ảnh tối đa là 4MB.');
      return Upload.LIST_IGNORE;
    }
    return true;
  };

  const upload: UploadProps['customRequest'] = async options => {
    const file = options.file as File;
    const data = new FormData();
    data.append('field', field);
    data.append('image', file, file.name);
    setUploading(true);
    try {
      const response = await api.post('/admin/settings/upload-image', data, {
        headers: { 'Content-Type': undefined },
      });
      const path = response.data.data.path as string;
      onChange?.(path);
      options.onSuccess?.(response.data);
      message.success('Đã tải ảnh lên và lưu cấu hình.');
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const validation = detail.response?.data?.errors?.image?.[0];
      const reason = validation || detail.response?.data?.message || 'Không thể tải ảnh lên.';
      options.onError?.(new Error(reason));
      message.error(reason);
    } finally {
      setUploading(false);
    }
  };

  return <div className={`${styles.imageUploader} ${wide ? styles.imageUploaderWide : ''}`}>
    <div className={`${styles.imagePreview} ${compact ? styles.compactPreview : ''}`}>
      {preview ? <Image preview={false} src={preview} alt={`Xem trước ${field}`} /> : <span>Chưa có ảnh</span>}
    </div>
    <div className={styles.uploadDetails}>
      <Upload accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/x-icon,.ico" showUploadList={false} beforeUpload={beforeUpload} customRequest={upload}>
        <Button icon={<UploadOutlined />} loading={uploading}>{value ? 'Thay ảnh' : 'Chọn ảnh tải lên'}</Button>
      </Upload>
      <small>{hint} · tối đa 4MB</small>
      {value ? <code>{value}</code> : null}
    </div>
  </div>;
}
