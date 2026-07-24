'use client';

import { useCallback, useEffect, useState } from 'react';
import { Alert, Button, Card, Empty, Input, Segmented, Space, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { BellOutlined, CodeOutlined, CreditCardOutlined, EyeOutlined, NotificationOutlined, SaveOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './notifications.module.css';

const { Text, Title } = Typography;
const { TextArea } = Input;
type NoticeKey = 'notice_modal' | 'notice_service' | 'notice_recharge';
type Notices = Record<NoticeKey, string>;
type EditorMode = Record<NoticeKey, 'edit' | 'preview'>;

const definitions: Array<{ key: NoticeKey; title: string; label: string; description: string; icon: React.ReactNode; color: string }> = [
  { key: 'notice_modal', title: 'Thông báo Modal Dịch vụ', label: 'Nội dung thông báo Modal', description: 'Hiển thị trong cửa sổ thông báo khi thành viên truy cập khu vực dịch vụ.', icon: <NotificationOutlined />, color: 'purple' },
  { key: 'notice_service', title: 'Thông báo Dịch vụ', label: 'Nội dung thông báo', description: 'Hiển thị trực tiếp trên trang đặt dịch vụ của thành viên.', icon: <BellOutlined />, color: 'blue' },
  { key: 'notice_recharge', title: 'Thông báo Nạp tiền', label: 'Nội dung thông báo trên trang nạp tiền', description: 'Hướng dẫn hoặc lưu ý quan trọng trước khi thành viên thực hiện nạp tiền.', icon: <CreditCardOutlined />, color: 'green' },
];
const previewDocument = (content: string) => `<!doctype html><html><head><meta charset="utf-8"><style>body{margin:0;padding:18px;color:#26344d;font:14px/1.7 Arial,sans-serif;overflow-wrap:anywhere}img{max-width:100%;height:auto}a{color:#1677ff}table{width:100%;border-collapse:collapse}td,th{padding:7px;border:1px solid #d8dee8}</style></head><body>${content}</body></html>`;

export default function AdminNotificationsPage() {
  const [data, setData] = useState<Notices>({ notice_modal: '', notice_service: '', notice_recharge: '' });
  const [saved, setSaved] = useState<Notices>({ notice_modal: '', notice_service: '', notice_recharge: '' });
  const [mode, setMode] = useState<EditorMode>({ notice_modal: 'edit', notice_service: 'edit', notice_recharge: 'edit' });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState<NoticeKey>();

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get('/admin/notifications');
      const notices = { notice_modal: response.data.data.notice_modal || '', notice_service: response.data.data.notice_service || '', notice_recharge: response.data.data.notice_recharge || '' };
      setData(notices); setSaved(notices);
    } catch { message.error('Không thể tải cấu hình thông báo.'); }
    finally { setLoading(false); }
  }, []);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const save = async (key: NoticeKey) => {
    setSaving(key);
    try {
      const response = await api.put('/admin/notifications', { [key]: data[key] });
      message.success(response.data.message || 'Đã cập nhật thông báo.');
      setSaved(old => ({ ...old, [key]: response.data.data?.[key] ?? data[key] }));
      setData(old => ({ ...old, [key]: response.data.data?.[key] ?? old[key] }));
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể lưu thông báo.');
    } finally { setSaving(undefined); }
  };
  const insert = (key: NoticeKey, before: string, after = '') => {
    const element = document.getElementById(key) as HTMLTextAreaElement | null;
    const start = element?.selectionStart ?? data[key].length; const end = element?.selectionEnd ?? start;
    const next = `${data[key].slice(0, start)}${before}${data[key].slice(start, end)}${after}${data[key].slice(end)}`;
    setData(old => ({ ...old, [key]: next }));
    requestAnimationFrame(() => { element?.focus(); element?.setSelectionRange(start + before.length, end + before.length); });
  };

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Cấu hình thông báo</Title><Text type="secondary">Quản lý nội dung thông báo hiển thị tại các khu vực dành cho thành viên</Text></div></header>
    <Alert className={styles.help} type="info" showIcon title="Nội dung hỗ trợ HTML" description="Bạn có thể dùng thanh định dạng nhanh hoặc nhập HTML trực tiếp. Hãy xem trước nội dung trước khi lưu." />
    <section className={styles.list}>{definitions.map(item => { const dirty = data[item.key] !== saved[item.key]; return <Card key={item.key} loading={loading} className={styles.card} title={<Space><span className={styles.icon}>{item.icon}</span>{item.title}{dirty && <Tag color="warning">Chưa lưu</Tag>}</Space>} extra={<Segmented size="small" value={mode[item.key]} onChange={value => setMode(old => ({ ...old, [item.key]: value as 'edit' | 'preview' }))} options={[{ value: 'edit', label: <Space size={4}><CodeOutlined />Soạn thảo</Space> }, { value: 'preview', label: <Space size={4}><EyeOutlined />Xem trước</Space> }]} />}>
      <Text className={styles.description} type="secondary">{item.description}</Text>
      {mode[item.key] === 'edit' ? <div className={styles.editorWrap}><label htmlFor={item.key}>{item.label}</label><div className={styles.toolbar}><Button size="small" onClick={() => insert(item.key, '<strong>', '</strong>')}><strong>B</strong></Button><Button size="small" onClick={() => insert(item.key, '<em>', '</em>')}><em>I</em></Button><Button size="small" onClick={() => insert(item.key, '<u>', '</u>')}><u>U</u></Button><Button size="small" onClick={() => insert(item.key, '<p>', '</p>')}>Đoạn</Button><Button size="small" onClick={() => insert(item.key, '<ul>\n<li>', '</li>\n</ul>')}>Danh sách</Button><Button size="small" onClick={() => insert(item.key, '<a href="https://">', '</a>')}>Liên kết</Button></div><TextArea id={item.key} value={data[item.key]} rows={8} placeholder="Nhập nội dung thông báo..." onChange={event => setData(old => ({ ...old, [item.key]: event.target.value }))} /></div> : <div className={styles.previewBox}>{data[item.key].trim() ? <iframe title={`Xem trước ${item.title}`} sandbox="" srcDoc={previewDocument(data[item.key])} /> : <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có nội dung thông báo" />}</div>}
      <div className={styles.actions}><span>{data[item.key].length.toLocaleString('vi-VN')} ký tự</span><Button type="primary" icon={<SaveOutlined />} loading={saving === item.key} disabled={!dirty || Boolean(saving)} onClick={() => void save(item.key)}>Lưu thông báo</Button></div>
    </Card>; })}</section>
  </main>;
}
