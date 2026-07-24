'use client';

import { useEffect, useState } from 'react';
import { Button, Card, Form, Image, Input, InputNumber, Modal, Radio, Select, Space, Table, Tag, Typography, Upload } from 'antd';
import { AppstoreOutlined, DeleteOutlined, EditOutlined, FacebookFilled, GlobalOutlined, GoogleOutlined, InstagramOutlined, MenuOutlined, PlayCircleFilled, PlusOutlined, SearchOutlined, SendOutlined, ShopOutlined, TeamOutlined, UploadOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import type { UploadFile } from 'antd/es/upload/interface';
import { message } from '@/lib/antd-message';
import api from '@/lib/axios';
import styles from './platforms.module.css';

const { Text, Title } = Typography;
type Platform = { id: number; icon?: string; sort_order: number; name: string; status: string; created_at: string };
type Values = { name: string; sort_order: number; status: string; icon?: UploadFile[]; icon_preset?: string; icon_url?: string };

const asset = (path?: string) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';
const presetIcons: Record<string, React.ReactNode> = {
  global: <GlobalOutlined />, facebook: <FacebookFilled />, instagram: <InstagramOutlined />,
  tiktok: <PlayCircleFilled />, youtube: <PlayCircleFilled />, telegram: <SendOutlined />,
  discord: <TeamOutlined />, google: <GoogleOutlined />, shop: <ShopOutlined />, app: <AppstoreOutlined />,
};
const presetOptions = [['global', 'Website'], ['facebook', 'Facebook'], ['instagram', 'Instagram'], ['tiktok', 'TikTok'], ['youtube', 'YouTube'], ['telegram', 'Telegram'], ['discord', 'Discord'], ['google', 'Google'], ['shop', 'Cửa hàng'], ['app', 'Ứng dụng']].map(([value, label]) => ({ value, label: <Space>{presetIcons[value]}{label}</Space> }));
function PlatformIcon({ icon, name }: { icon?: string; name: string }) {
  if (icon?.startsWith('preset:')) return <span className={styles.fallback}>{presetIcons[icon.slice(7)] || <AppstoreOutlined />}</span>;
  if (icon) return <Image preview={false} src={asset(icon)} alt={name} width={34} height={34} />;
  return <span className={styles.fallback}>{name.slice(0, 1)}</span>;
}

export default function PlatformsPage() {
  const [rows, setRows] = useState<Platform[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [page, setPage] = useState({ current: 1, pageSize: 10, total: 0 });
  const [search, setSearch] = useState('');
  const [selected, setSelected] = useState<React.Key[]>([]);
  const [dragging, setDragging] = useState<number | null>(null);
  const [editing, setEditing] = useState<Platform | null | undefined>(undefined);
  const [iconMode, setIconMode] = useState<'none' | 'image' | 'url' | 'preset'>('none');
  const [form] = Form.useForm<Values>();

  const load = async (current = 1, pageSize = 10) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/resources/platforms', { params: { page: current, per_page: pageSize, search } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách nền tảng.'); }
    finally { setLoading(false); }
  };

  useEffect(() => { const timer = window.setTimeout(() => void load(), 0); return () => window.clearTimeout(timer); }, []);
  const create = () => { setEditing(null); setIconMode('none'); form.resetFields(); form.setFieldsValue({ sort_order: 0, status: 'active' }); };
  const edit = (row: Platform) => {
    const preset = row.icon?.startsWith('preset:') ? row.icon.slice(7) : undefined;
    const iconUrl = row.icon && /^https?:\/\//.test(row.icon) ? row.icon : undefined;
    setEditing(row); setIconMode(preset ? 'preset' : iconUrl ? 'url' : row.icon ? 'image' : 'none');
    form.setFieldsValue({ name: row.name, sort_order: row.sort_order, status: row.status, icon: [], icon_preset: preset, icon_url: iconUrl });
  };
  const save = async () => {
    try {
      const values = await form.validateFields(); setSaving(true);
      const body = new FormData(); body.append('name', values.name); body.append('sort_order', String(values.sort_order ?? 0)); body.append('status', values.status);
      if (iconMode === 'none') body.append('clear_icon', '1');
      else if (iconMode === 'url' && values.icon_url) body.append('icon_url', values.icon_url.trim());
      else if (iconMode === 'preset' && values.icon_preset) body.append('icon_preset', values.icon_preset);
      else { const file = values.icon?.[0]?.originFileObj; if (file) body.append('icon', file, file.name); }
      const config = { headers: { 'Content-Type': 'multipart/form-data' } };
      const response = editing ? await api.post(`/admin/platforms/${editing.id}`, body, config) : await api.post('/admin/platforms', body, config);
      message.success(response.data.message || 'Đã lưu nền tảng.'); setEditing(undefined); await load(page.current, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const requestError = error as { response?: { data?: { message?: string } } };
      message.error(requestError.response?.data?.message || 'Không thể lưu nền tảng. Kiểm tra icon và dữ liệu.');
    } finally { setSaving(false); }
  };
  const remove = () => {
    if (!selected.length) return void message.warning('Vui lòng chọn ít nhất một nền tảng.');
    Modal.confirm({ title: 'Xoá nền tảng đã chọn?', content: `Bạn đang chọn ${selected.length} nền tảng.`, okText: 'Xoá', okButtonProps: { danger: true }, cancelText: 'Huỷ', onOk: async () => { await Promise.all(selected.map(id => api.delete(`/admin/resources/platforms/${String(id)}`))); message.success('Đã xoá nền tảng.'); setSelected([]); await load(page.current, page.pageSize); } });
  };
  const order = async (row: Platform, value: number | null) => { await api.put(`/admin/resources/platforms/${row.id}`, { data: { sort_order: value ?? 0 } }); setRows(old => old.map(item => item.id === row.id ? { ...item, sort_order: value ?? 0 } : item)); message.success('Đã cập nhật thứ tự.'); };
  const drop = async (target: number) => {
    if (dragging === null || dragging === target) return;
    const oldIndex = rows.findIndex(item => item.id === dragging), newIndex = rows.findIndex(item => item.id === target); if (oldIndex < 0 || newIndex < 0) return;
    const next = [...rows], [moved] = next.splice(oldIndex, 1); next.splice(newIndex, 0, moved);
    const ordered = next.map((item, index) => ({ ...item, sort_order: (page.current - 1) * page.pageSize + index })); setRows(ordered); setDragging(null);
    try { await api.post('/admin/platforms/reorder', { ids: ordered.map(item => item.id) }); message.success('Đã cập nhật thứ tự nền tảng.'); }
    catch { message.error('Không thể lưu thứ tự.'); await load(page.current, page.pageSize); }
  };

  const columns = [
    { title: '', width: 46, render: () => <MenuOutlined className={styles.dragHandle} /> },
    { title: 'ID', dataIndex: 'id', width: 75, render: (id: number) => <b>#{id}</b> },
    { title: 'Ưu tiên', dataIndex: 'sort_order', width: 110, render: (value: number, row: Platform) => <InputNumber min={0} value={value} onChange={next => void order(row, next)} className={styles.order} /> },
    { title: 'Thao tác', width: 95, render: (_: unknown, row: Platform) => <Button type="primary" size="small" icon={<EditOutlined />} onClick={() => edit(row)} /> },
    { title: 'Tên nền tảng', dataIndex: 'name', render: (name: string, row: Platform) => <div className={styles.platform}><PlatformIcon icon={row.icon} name={name} /><strong>{name}</strong></div> },
    { title: 'Trạng thái', dataIndex: 'status', render: (status: string) => <Tag color={status === 'active' ? 'green' : 'red'}>{status === 'active' ? 'Hoạt động' : 'Ngừng hoạt động'}</Tag> },
    { title: 'Thời gian tạo', dataIndex: 'created_at', render: (value: string) => new Date(value).toLocaleString('vi-VN') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Danh sách nền tảng</Title><Text type="secondary">Kéo biểu tượng ☰ để thay đổi thứ tự hiển thị</Text></div></header>
    <Card className={styles.card} title="Danh sách nền tảng" extra={<Space wrap><Input value={search} onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize)} prefix={<SearchOutlined />} placeholder="Tìm tên nền tảng" allowClear /><Button type="primary" icon={<PlusOutlined />} onClick={create}>Thêm mới</Button><Button danger icon={<DeleteOutlined />} onClick={remove}>Xoá</Button></Space>}>
      <Table rowKey="id" loading={loading} dataSource={rows} columns={columns} rowSelection={{ selectedRowKeys: selected, onChange: setSelected }} onRow={row => ({ draggable: true, onDragStart: event => { event.dataTransfer.effectAllowed = 'move'; setDragging(row.id); }, onDragOver: event => { event.preventDefault(); event.dataTransfer.dropEffect = 'move'; }, onDrop: event => { event.preventDefault(); void drop(row.id); }, onDragEnd: () => setDragging(null), className: dragging === row.id ? styles.dragging : '' })} scroll={{ x: 900 }} pagination={{ ...page, showSizeChanger: true, showTotal: total => `${total} nền tảng` }} onChange={(pagination: TablePaginationConfig) => void load(pagination.current || 1, pagination.pageSize || 10)} />
    </Card>
    <Modal open={editing !== undefined} title={editing ? 'Chỉnh sửa nền tảng' : 'Thêm mới nền tảng'} okText="Lưu" cancelText="Đóng" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(undefined)}>
      <Form form={form} layout="vertical">
        <Form.Item label="Hình ảnh nền tảng"><Radio.Group value={iconMode} onChange={event => { setIconMode(event.target.value); form.setFieldsValue({ icon: [], icon_preset: undefined, icon_url: undefined }); }} optionType="button" buttonStyle="solid"><Radio.Button value="none">Không dùng</Radio.Button><Radio.Button value="image">Upload</Radio.Button><Radio.Button value="url">Nhập URL</Radio.Button><Radio.Button value="preset">Chọn icon</Radio.Button></Radio.Group></Form.Item>
        {iconMode === 'image' && <Form.Item name="icon" label="Ảnh icon" valuePropName="fileList" getValueFromEvent={event => event?.fileList} rules={editing ? [] : [{ required: true, message: 'Vui lòng chọn ảnh icon.' }]}><Upload beforeUpload={() => false} maxCount={1} accept="image/png,image/jpeg,image/gif,image/webp" listType="picture"><Button icon={<UploadOutlined />}>Chọn ảnh</Button></Upload></Form.Item>}
        {iconMode === 'url' && <Form.Item name="icon_url" label="URL hình ảnh" rules={[{ required: true, message: 'Vui lòng nhập URL hình ảnh.' }, { type: 'url', message: 'URL hình ảnh không hợp lệ.' }]}><Input placeholder="https://example.com/icon.png" /></Form.Item>}
        {iconMode === 'preset' && <Form.Item name="icon_preset" label="Icon có sẵn" rules={[{ required: true, message: 'Vui lòng chọn icon.' }]}><Select showSearch optionFilterProp="value" options={presetOptions} placeholder="Chọn icon" /></Form.Item>}
        <Form.Item name="sort_order" label="Thứ tự" rules={[{ required: true, message: 'Nhập thứ tự.' }]}><InputNumber min={0} style={{ width: '100%' }} /></Form.Item>
        <Form.Item name="name" label="Tên nền tảng" rules={[{ required: true, message: 'Nhập tên nền tảng.' }, { max: 255 }]}><Input placeholder="Nhập tên nền tảng" /></Form.Item>
        <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={[{ value: 'active', label: 'Hoạt động' }, { value: 'inactive', label: 'Ngừng hoạt động' }]} /></Form.Item>
      </Form>
    </Modal>
  </main>;
}
