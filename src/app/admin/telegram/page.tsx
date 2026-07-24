'use client';

import { useCallback, useEffect, useState } from 'react';
import { Alert, Button, Card, Form, Input, Select, Space, Switch, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { ApiOutlined, CheckCircleOutlined, LinkOutlined, LockOutlined, RobotOutlined, SaveOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './telegram.module.css';

const { Text, Title } = Typography;
type TelegramConfig = { tele_bot_username?: string; telegram_link?: string; telegram_bot?: string; telegram_status: 'active' | 'inactive'; telegram_webhook_url?: string; bot_token_configured?: boolean; default_webhook_url?: string };

export default function AdminTelegramPage() {
  const [form] = Form.useForm<TelegramConfig>();
  const status = Form.useWatch('telegram_status', form);
  const webhookUrl = Form.useWatch('telegram_webhook_url', form);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [registering, setRegistering] = useState(false);
  const [configured, setConfigured] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get('/admin/telegram'); const data = response.data.data as TelegramConfig;
      setConfigured(Boolean(data.bot_token_configured));
      form.setFieldsValue({ tele_bot_username: data.tele_bot_username || '', telegram_link: data.telegram_link || '', telegram_bot: '', telegram_status: data.telegram_status || 'inactive', telegram_webhook_url: data.telegram_webhook_url || data.default_webhook_url });
    } catch { message.error('Không thể tải cấu hình Telegram.'); }
    finally { setLoading(false); }
  }, [form]);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const save = async () => {
    try {
      const values = await form.validateFields(); setSaving(true);
      const payload = { ...values, telegram_bot: values.telegram_bot?.trim() || undefined };
      const response = await api.put('/admin/telegram', payload);
      message.success(response.data.message || 'Đã lưu cấu hình Telegram.'); setConfigured(Boolean(response.data.data?.bot_token_configured)); form.setFieldValue('telegram_bot', '');
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const first = detail.response?.data?.errors ? Object.values(detail.response.data.errors)[0]?.[0] : undefined;
      message.error(first || detail.response?.data?.message || 'Không thể lưu cấu hình Telegram.');
    } finally { setSaving(false); }
  };
  const registerWebhook = async () => {
    try {
      const url = await form.validateFields(['telegram_webhook_url']); setRegistering(true);
      const response = await api.post('/admin/telegram/webhook', { telegram_webhook_url: url.telegram_webhook_url });
      message.success(response.data.message); form.setFieldsValue({ tele_bot_username: response.data.data?.username, telegram_link: response.data.data?.username ? `https://t.me/${response.data.data.username}` : form.getFieldValue('telegram_link'), telegram_status: 'active' });
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể đăng ký webhook Telegram.');
    } finally { setRegistering(false); }
  };

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Telegram Link &amp; 2FA</Title><Text type="secondary">Liên kết tài khoản, gửi thông báo và mã xác thực OTP qua Telegram</Text></div><Tag icon={status === 'active' ? <CheckCircleOutlined /> : undefined} color={status === 'active' ? 'success' : 'default'}>{status === 'active' ? 'Đang bật' : 'Đang tắt'}</Tag></header>
    <div className={styles.layout}>
      <Card loading={loading} className={styles.card} title={<Space><RobotOutlined />Cấu hình Telegram Bot</Space>}>
        <Form form={form} layout="vertical" requiredMark={false} initialValues={{ telegram_status: 'inactive' }}>
          <div className={styles.grid}>
            <Form.Item name="tele_bot_username" label="Bot Username"><Space.Compact block><Button className={styles.prefix} disabled>@</Button><Input placeholder="dhsolution_bot" /></Space.Compact></Form.Item>
            <Form.Item name="telegram_link" label="Link Telegram Bot" rules={[{ type: 'url', message: 'Link Telegram không hợp lệ.' }]}><Input prefix={<LinkOutlined />} placeholder="https://t.me/dhsolution_bot" /></Form.Item>
            <Form.Item className={styles.tokenField} name="telegram_bot" label={<Space>Bot Token {configured && <Tag color="success">Đã cấu hình</Tag>}</Space>} rules={configured ? [] : [{ required: true, message: 'Nhập Bot Token.' }]} extra={configured ? 'Để trống nếu không muốn thay Bot Token hiện tại.' : 'Lấy token từ @BotFather.'}><Input.Password prefix={<LockOutlined />} autoComplete="new-password" placeholder={configured ? 'Nhập token mới để thay đổi' : '123456789:AA...'} /></Form.Item>
            <Form.Item name="telegram_status" label="Trạng thái" valuePropName="value"><Select options={[{ value: 'active', label: 'Bật' }, { value: 'inactive', label: 'Tắt' }]} /></Form.Item>
            <Form.Item className={styles.webhookField} name="telegram_webhook_url" label="Webhook URL" rules={[{ required: true, message: 'Nhập Webhook URL.' }, { type: 'url', message: 'Webhook URL không hợp lệ.' }]} extra="Telegram yêu cầu HTTPS công khai. Endpoint nhận webhook: /api/telegram/webhook"><Input prefix={<ApiOutlined />} placeholder="https://domain.com/api/telegram/webhook" /></Form.Item>
          </div>
          {webhookUrl && !webhookUrl.startsWith('https://') && <Alert className={styles.warning} type="warning" showIcon title="Webhook Telegram cần một địa chỉ HTTPS công khai; địa chỉ localhost hoặc HTTP sẽ không đăng ký được." />}
          <Space wrap><Button type="primary" icon={<SaveOutlined />} loading={saving} onClick={() => void save()}>Lưu cấu hình</Button><Button className={styles.webhookButton} icon={<ApiOutlined />} loading={registering} disabled={!configured} onClick={() => void registerWebhook()}>Kiểm tra Bot &amp; đăng ký Webhook</Button></Space>
        </Form>
      </Card>
      <aside className={styles.side}>
        <Card className={styles.guide} title="Trạng thái kết nối"><div className={styles.connection}><span className={configured ? styles.connected : styles.disconnected}><RobotOutlined /></span><div><strong>{configured ? 'Bot Token đã sẵn sàng' : 'Chưa có Bot Token'}</strong><p>{configured ? 'Bạn có thể kiểm tra bot và đăng ký webhook.' : 'Nhập Bot Token rồi lưu cấu hình trước.'}</p></div></div><div className={styles.switchRow}><span>Kích hoạt Telegram</span><Switch checked={status === 'active'} onChange={checked => form.setFieldValue('telegram_status', checked ? 'active' : 'inactive')} /></div></Card>
        <Card className={styles.guide} title="Lệnh dành cho người dùng"><div className={styles.command}><code>/lienket API_KEY</code><span>Liên kết tài khoản với Telegram</span></div><div className={styles.command}><code>/huylienket</code><span>Hủy liên kết tài khoản</span></div></Card>
        <Alert type="info" showIcon title="Bảo mật Bot Token" description="Bot Token không được gửi ngược về trình duyệt. Giao diện chỉ hiển thị trạng thái đã cấu hình." />
      </aside>
    </div>
  </main>;
}
