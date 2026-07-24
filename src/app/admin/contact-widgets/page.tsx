'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Form, Image, Input, Modal, Space, Switch, Table, Typography, Upload } from 'antd';
import { DeleteOutlined, EditOutlined, PlusOutlined, UploadOutlined } from '@ant-design/icons';
import type { UploadFile } from 'antd/es/upload/interface';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from './contact-widgets.module.css';

const { Text, Title } = Typography;
const asset = (path?: string) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';
type Widget = { id: number; name: string; url: string; image?: string; is_active: boolean };
type WidgetForm = { name: string; url: string; image?: UploadFile[] };

export default function ContactWidgetsPage() {
  const [form] = Form.useForm<WidgetForm>();
  const [rows, setRows] = useState<Widget[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [editing, setEditing] = useState<Widget | null | undefined>(undefined);

  const load = useCallback(async () => {
    setLoading(true);
    try { const response = await api.get('/admin/contact-widgets'); setRows(response.data?.data || []); }
    catch { message.error('Không thể tải danh sách kênh liên hệ.'); }
    finally { setLoading(false); }
  }, []);
  useEffect(() => { void load(); }, [load]);

  const openCreate = () => { form.resetFields(); setEditing(null); };
  const openEdit = (row: Widget) => { form.setFieldsValue({ name: row.name, url: row.url, image: [] }); setEditing(row); };
  const save = async () => {
    try {
      const values = await form.validateFields();
      setSaving(true);
      const body = new FormData();
      body.append('name', values.name.trim());
      body.append('url', values.url.trim());
      body.append('is_active', editing ? (editing.is_active ? '1' : '0') : '1');
      const file = values.image?.[0]?.originFileObj;
      if (file) body.append('image', file, file.name);
      const endpoint = editing ? `/admin/contact-widgets/${editing.id}` : '/admin/contact-widgets';
      const response = await api.post(endpoint, body, { headers: { 'Content-Type': 'multipart/form-data' } });
      message.success(response.data?.message || 'Đã lưu kênh liên hệ.');
      setEditing(undefined);
      await load();
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const first = detail.response?.data?.errors ? Object.values(detail.response.data.errors)[0]?.[0] : undefined;
      message.error(first || detail.response?.data?.message || 'Không thể lưu kênh liên hệ.');
    } finally { setSaving(false); }
  };
  const toggle = async (row: Widget, checked: boolean) => {
    setRows(current => current.map(item => item.id === row.id ? { ...item, is_active: checked } : item));
    try { await api.put(`/admin/contact-widgets/${row.id}/status`, { is_active: checked }); }
    catch { setRows(current => current.map(item => item.id === row.id ? { ...item, is_active: !checked } : item)); message.error('Không thể cập nhật trạng thái.'); }
  };
  const remove = (row: Widget) => Modal.confirm({
    title: `Xóa kênh “${row.name}”?`, content: 'Kênh này sẽ không còn hiển thị trên website.', okText: 'Xóa', cancelText: 'Hủy', okButtonProps: { danger: true },
    onOk: async () => { await api.delete(`/admin/contact-widgets/${row.id}`); message.success('Đã xóa kênh liên hệ.'); await load(); },
  });

  const columns = [
    { title: 'Ảnh', width: 80, render: (_: unknown, row: Widget) => row.image ? <Image className={styles.icon} preview={false} src={asset(row.image)} alt={row.name} /> : '-' },
    { title: 'Tên hiển thị', dataIndex: 'name', width: 330, render: (value: string) => <Text strong>{value}</Text> },
    { title: 'Đường dẫn', dataIndex: 'url', render: (value: string) => <a href={/^https?:\/\//i.test(value) ? value : `https://${value}`} target="_blank" rel="noreferrer">{value}</a> },
    { title: 'Hiển thị', width: 100, align: 'center' as const, render: (_: unknown, row: Widget) => <Switch checked={row.is_active} onChange={checked => void toggle(row, checked)} /> },
    { title: '', width: 105, align: 'right' as const, render: (_: unknown, row: Widget) => <Space><Button type="text" icon={<EditOutlined />} onClick={() => openEdit(row)} aria-label={`Sửa ${row.name}`} /><Button type="text" danger icon={<DeleteOutlined />} onClick={() => remove(row)} aria-label={`Xóa ${row.name}`} /></Space> },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Widget Liên hệ</Title><Text type="secondary">Quản lý các kênh liên hệ hiển thị trên website</Text></div><Button type="primary" size="large" icon={<PlusOutlined />} onClick={openCreate}>Thêm liên hệ</Button></header>
    <Table className={styles.table} rowKey="id" columns={columns} dataSource={rows} loading={loading} pagination={false} scroll={{ x: 760 }} locale={{ emptyText: 'Chưa có kênh liên hệ' }} />
    <Modal open={editing !== undefined} title={editing ? 'Chỉnh sửa liên hệ' : 'Thêm liên hệ mới'} okText="Lưu" cancelText="Hủy" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(undefined)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <Form.Item name="name" label="Tên hiển thị" rules={[{ required: true, message: 'Nhập tên hiển thị.' }]}><Input placeholder="VD: Zalo, Facebook, Hotline..." /></Form.Item>
        <Form.Item name="url" label="Link liên hệ" rules={[{ required: true, message: 'Nhập link liên hệ.' }]}><Input placeholder="VD: https://zalo.me/0123456789" /></Form.Item>
        <Form.Item name="image" label="Hình ảnh" valuePropName="fileList" getValueFromEvent={event => event?.fileList} rules={editing ? [] : [{ required: true, message: 'Chọn hình ảnh.' }]} extra="PNG, JPG, GIF, SVG, WebP • Tối đa 10MB"><Upload beforeUpload={() => false} maxCount={1} accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml" listType="picture"><Button icon={<UploadOutlined />}>Tải ảnh lên</Button></Upload></Form.Item>
        {editing?.image ? <div className={styles.current}><span>Ảnh hiện tại</span><Image src={asset(editing.image)} alt={editing.name} width={48} height={48} /></div> : null}
      </Form>
    </Modal>
  </main>;
}
