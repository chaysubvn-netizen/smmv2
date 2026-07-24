'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Form, Input, Modal, Radio, Select, Space, Statistic, Table, Tag, Tooltip, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { CopyOutlined, DeleteOutlined, EyeInvisibleOutlined, EyeOutlined, PlusOutlined, ReloadOutlined, SearchOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './facebook-tokens.module.css';

const { Text, Title } = Typography;
const { TextArea } = Input;
type FbToken = { id: number; type: string; data: string; uid?: string; name?: string; note?: string; status: string; updated_at: string };
type Stats = { all: number; live: number; processing: number; block: number; die: number };
type AddForm = { list: string; type: string; note?: string };
const statusMeta: Record<string, { label: string; color: string }> = { live: { label: 'LIVE', color: 'success' }, processing: { label: 'PROCESSING', color: 'processing' }, block: { label: 'BLOCK', color: 'warning' }, die: { label: 'DIE', color: 'error' } };
const typeLabel: Record<string, string> = { cookie_token: 'COOKIE | TOKEN', cookie: 'COOKIE', token: 'ACCESS TOKEN' };

export default function AdminFacebookTokensPage() {
  const [form] = Form.useForm<AddForm>();
  const [rows, setRows] = useState<FbToken[]>([]);
  const [stats, setStats] = useState<Stats>({ all: 0, live: 0, processing: 0, block: 0, die: 0 });
  const [page, setPage] = useState({ current: 1, pageSize: 50, total: 0 });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [refreshing, setRefreshing] = useState<number>();
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [visible, setVisible] = useState<Set<number>>(new Set());

  const load = useCallback(async (current = 1, pageSize = 50) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/facebook-tokens', { params: { page: current, per_page: pageSize, search: search.trim() || undefined, status } });
      const data = response.data.data;
      setRows(data.data || []); setStats(response.data.stats || {});
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách Facebook Token.'); }
    finally { setLoading(false); }
  }, [search, status]);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const add = async () => {
    try {
      const values = await form.validateFields(); setSaving(true);
      const response = await api.post('/admin/facebook-tokens', values);
      message.success(response.data.message); form.resetFields(); form.setFieldValue('type', 'cookie_token'); await load(1, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể thêm tài nguyên.');
    } finally { setSaving(false); }
  };
  const copy = async (value: string) => { await navigator.clipboard.writeText(value); message.success('Đã sao chép tài nguyên.'); };
  const refresh = async (item: FbToken) => { setRefreshing(item.id); try { const response = await api.post(`/admin/facebook-tokens/${item.id}/refresh`); if (response.data.is_live) message.success(response.data.message); else message.error(response.data.message); await load(page.current, page.pageSize); } finally { setRefreshing(undefined); } };
  const updateStatus = async (id: number, value: string) => { await api.put(`/admin/facebook-tokens/${id}/status`, { status: value }); message.success('Đã cập nhật trạng thái.'); await load(page.current, page.pageSize); };
  const remove = (item: FbToken) => Modal.confirm({ title: `Xóa tài nguyên #${item.id}?`, okText: 'Xóa', cancelText: 'Hủy', okButtonProps: { danger: true }, onOk: async () => { await api.delete(`/admin/facebook-tokens/${item.id}`); message.success('Đã xóa tài nguyên.'); await load(page.current, page.pageSize); } });
  const bulkRemove = (kind: 'die' | 'all') => Modal.confirm({ title: kind === 'all' ? 'Xóa tất cả tài nguyên?' : 'Xóa toàn bộ tài nguyên Die?', content: 'Thao tác này không thể hoàn tác.', okText: 'Xóa', cancelText: 'Hủy', okButtonProps: { danger: true }, onOk: async () => { const response = await api.delete(`/admin/facebook-tokens/${kind}`); message.success(response.data.message); await load(1, page.pageSize); } });
  const toggle = (id: number) => setVisible(old => { const next = new Set(old); if (next.has(id)) next.delete(id); else next.add(id); return next; });
  const masked = (value: string) => value.length <= 18 ? '••••••••••••' : `${value.slice(0, 8)}••••••••••${value.slice(-6)}`;

  const columns = [
    { title: 'Tài nguyên', width: 390, render: (_: unknown, item: FbToken) => <div><div className={styles.resource}><Tag color="blue">{typeLabel[item.type] || item.type.toUpperCase()}</Tag><code title={visible.has(item.id) ? item.data : undefined}>{visible.has(item.id) ? item.data : masked(item.data)}</code><Tooltip title={visible.has(item.id) ? 'Ẩn' : 'Hiện'}><Button type="text" size="small" icon={visible.has(item.id) ? <EyeInvisibleOutlined /> : <EyeOutlined />} onClick={() => toggle(item.id)} /></Tooltip><Tooltip title="Sao chép"><Button type="text" size="small" icon={<CopyOutlined />} onClick={() => void copy(item.data)} /></Tooltip></div><small>ID: #{item.id}</small></div> },
    { title: 'Ghi chú', dataIndex: 'note', width: 200, render: (value?: string) => value || '---' },
    { title: 'Trạng thái', dataIndex: 'status', width: 160, render: (value: string, item: FbToken) => <Select className={styles.statusSelect} variant="borderless" value={value} onChange={next => void updateStatus(item.id, next)} options={Object.entries(statusMeta).map(([key, meta]) => ({ value: key, label: <Tag color={meta.color}>{meta.label}</Tag> }))} /> },
    { title: 'UID FB', dataIndex: 'uid', width: 170, render: (value?: string) => <code>{value || '---'}</code> },
    { title: 'Tên FB', dataIndex: 'name', width: 200, render: (value?: string) => value || '---' },
    { title: 'Cập nhật', dataIndex: 'updated_at', width: 170, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
    { title: 'Thao tác', width: 110, fixed: 'right' as const, render: (_: unknown, item: FbToken) => <Space size={4}><Tooltip title="Kiểm tra lại"><Button size="small" type="primary" ghost icon={<ReloadOutlined spin={refreshing === item.id} />} disabled={refreshing !== undefined} onClick={() => void refresh(item)} /></Tooltip><Tooltip title="Xóa"><Button size="small" danger type="text" icon={<DeleteOutlined />} onClick={() => remove(item)} /></Tooltip></Space> },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý Facebook Token</Title><Text type="secondary">Quản lý Cookie và Access Token phục vụ hệ thống</Text></div></header>
    <Card className={styles.addCard} title="Thêm tài nguyên"><Text type="secondary">Mỗi dòng một Cookie, Access Token hoặc Cookie|AccessToken</Text><Form form={form} layout="vertical" initialValues={{ type: 'cookie_token' }} requiredMark={false} className={styles.form}><Form.Item name="list" rules={[{ required: true, message: 'Nhập ít nhất một tài nguyên.' }]}><TextArea rows={7} placeholder="Mỗi dòng một Cookie hoặc Token (hoặc Cookie|AccessToken)" /></Form.Item><div className={styles.formBottom}><Form.Item name="type" label="Loại dữ liệu"><Radio.Group options={[{ value: 'cookie_token', label: 'Cookie|AccessToken' }, { value: 'cookie', label: 'Cookie' }, { value: 'token', label: 'AccessToken' }]} /></Form.Item><Form.Item name="note" label="Ghi chú (Tùy chọn)"><Input placeholder="VD: Nick clone, Nick 2FA..." /></Form.Item></div><Button className={styles.addButton} type="primary" icon={<PlusOutlined />} loading={saving} onClick={() => void add()}>Thêm tài nguyên</Button></Form></Card>
    <section className={styles.stats}>{Object.entries(statusMeta).map(([key, meta]) => <button key={key} className={`${styles.stat} ${status === key ? styles.activeStat : ''}`} onClick={() => setStatus(key)}><Statistic title={meta.label} value={stats[key as keyof Stats]} styles={{ content: { color: key === 'live' ? '#00b884' : key === 'die' ? '#f04438' : key === 'block' ? '#f59e0b' : '#1688f8' } }} /></button>)}</section>
    <Card className={styles.listCard} title={`Danh sách tài nguyên (${stats.all})`} extra={<Space><Button danger ghost onClick={() => bulkRemove('die')}>Xóa hết Die</Button><Button danger onClick={() => bulkRemove('all')}>Xóa tất cả</Button></Space>}>
      <div className={styles.filters}><div className={styles.statusTabs}>{(['all', 'live', 'processing', 'block', 'die'] as const).map(key => <Button key={key} type={status === key ? 'primary' : 'default'} danger={key === 'die' && status === key} onClick={() => setStatus(key)}>{key === 'all' ? 'Tất cả' : statusMeta[key].label} <span>{stats[key]}</span></Button>)}</div><Input allowClear value={search} prefix={<SearchOutlined />} placeholder="Tìm UID, tên, ghi chú..." onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize)} /></div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1400 }} pagination={{ ...page, showSizeChanger: true, locale: { items_per_page: '/ trang' }, showTotal: total => `${total} tài nguyên` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 50)} />
    </Card>
  </main>;
}
