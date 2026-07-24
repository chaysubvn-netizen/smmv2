'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Form, Input, InputNumber, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, EditOutlined, PlusOutlined, ReloadOutlined, SearchOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './currencies.module.css';

const { Text, Title } = Typography;
type Currency = { id: number; name: string; code: string; symbol?: string; exchange_rate: number | string; thousand_separator: string; decimal_separator: string; decimal_places: number; position: 'left' | 'right'; status: 'active' | 'inactive'; created_at: string };
type CurrencyForm = Omit<Currency, 'id' | 'created_at'>;
const separatorLabel: Record<string, string> = { comma: ', (Comma)', dot: '. (Dot)', space: 'Khoảng trắng (Space)' };

const preview = (item: Partial<CurrencyForm>, value = 1234567.89) => {
  const places = Number(item.decimal_places ?? 2);
  const thousand = item.thousand_separator === 'dot' ? '.' : item.thousand_separator === 'space' ? ' ' : ',';
  const decimal = item.decimal_separator === 'comma' ? ',' : '.';
  const [whole, fraction] = value.toFixed(places).split('.');
  const formatted = whole.replace(/\B(?=(\d{3})+(?!\d))/g, thousand) + (places ? decimal + fraction : '');
  return item.position === 'left' ? `${item.symbol || ''}${formatted}` : `${formatted}${item.symbol || ''}`;
};

export default function AdminCurrenciesPage() {
  const [form] = Form.useForm<CurrencyForm>();
  const watched = Form.useWatch([], form) || {};
  const [rows, setRows] = useState<Currency[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [editing, setEditing] = useState<Currency | null | undefined>(undefined);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/currencies', { params: { page: current, limit: pageSize, search: search.trim() || undefined } });
      const data = response.data.data;
      setRows(data.data || []); setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách tiền tệ.'); }
    finally { setLoading(false); }
  }, [search]);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const create = () => { setEditing(null); form.resetFields(); form.setFieldsValue({ exchange_rate: 1, thousand_separator: 'comma', decimal_separator: 'dot', decimal_places: 2, position: 'right', status: 'active' }); };
  const edit = (item: Currency) => { setEditing(item); form.setFieldsValue({ ...item, exchange_rate: Number(item.exchange_rate) }); };
  const save = async () => {
    try {
      const values = await form.validateFields(); setSaving(true);
      const response = editing ? await api.post(`/admin/currencies/${editing.id}`, values) : await api.post('/admin/currencies', values);
      if (!response.data.status) throw new Error(response.data.message || 'Dữ liệu không hợp lệ.');
      message.success(response.data.message || 'Đã lưu tiền tệ.'); setEditing(undefined); await load(page.current, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { message?: string; response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || detail.message || 'Không thể lưu tiền tệ.');
    } finally { setSaving(false); }
  };
  const remove = (item: Currency) => Modal.confirm({ title: `Xóa tiền tệ ${item.code}?`, content: 'Người dùng sẽ không thể tiếp tục sử dụng đơn vị tiền tệ này.', okText: 'Xóa', cancelText: 'Hủy', okButtonProps: { danger: true }, onOk: async () => { const response = await api.delete(`/admin/currencies/${item.id}`); if (!response.data.status) throw new Error(response.data.message); message.success(response.data.message || 'Đã xóa tiền tệ.'); await load(page.current, page.pageSize); } });

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 65 },
    { title: 'Thao tác', width: 125, render: (_: unknown, item: Currency) => <Space size={4}><Button type="primary" size="small" icon={<EditOutlined />} onClick={() => edit(item)} /><Button danger type="text" size="small" icon={<DeleteOutlined />} onClick={() => remove(item)} /></Space> },
    { title: 'Tên', dataIndex: 'name', width: 190 },
    { title: 'Mã tiền tệ', dataIndex: 'code', width: 130, render: (value: string) => <Tag color="blue">{value}</Tag> },
    { title: 'Ký hiệu', dataIndex: 'symbol', width: 100, align: 'center' as const, render: (value?: string) => <strong>{value || '—'}</strong> },
    { title: 'Tỷ giá tiền tệ', width: 180, render: (_: unknown, item: Currency) => <span className={styles.rate}>{Number(item.exchange_rate).toLocaleString('vi-VN', { maximumFractionDigits: 4 })}</span> },
    { title: 'Định dạng mẫu', width: 170, render: (_: unknown, item: Currency) => <code className={styles.preview}>{preview(item)}</code> },
    { title: 'Tách đơn vị ngàn', dataIndex: 'thousand_separator', width: 145, render: (value: string) => separatorLabel[value] || value },
    { title: 'Phân số thập phân', dataIndex: 'decimal_separator', width: 165, render: (value: string) => separatorLabel[value] || value },
    { title: 'Số thập phân', dataIndex: 'decimal_places', width: 130, align: 'center' as const },
    { title: 'Trạng thái', dataIndex: 'status', width: 125, render: (value: string) => <Tag color={value === 'active' ? 'success' : 'error'}>{value === 'active' ? 'Kích hoạt' : 'Tạm tắt'}</Tag> },
    { title: 'Ngày thêm', dataIndex: 'created_at', width: 180, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý tiền tệ</Title><Text type="secondary">Thiết lập tỷ giá và định dạng hiển thị tiền trên hệ thống</Text></div></header>
    <Card className={styles.card} title="Danh sách tiền tệ" extra={<Button type="primary" icon={<PlusOutlined />} onClick={create}>Thêm tiền tệ mới</Button>}>
      <div className={styles.filters}><Input allowClear value={search} prefix={<SearchOutlined />} placeholder="Tìm tên hoặc mã tiền tệ..." onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize)} /><Button icon={<ReloadOutlined />} onClick={() => void load(page.current, page.pageSize)}>Làm mới</Button></div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1700 }} pagination={{ ...page, showSizeChanger: true, locale: { items_per_page: '/ trang' }, showTotal: total => `${total} tiền tệ` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
    <Modal width={820} open={editing !== undefined} title={editing ? `Chỉnh sửa tiền tệ ${editing.code}` : 'Thêm tiền tệ mới'} okText="Lưu" cancelText="Đóng" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(undefined)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <div className={styles.formGrid}>
          <Form.Item name="name" label="Tên quốc gia" rules={[{ required: true, message: 'Nhập tên quốc gia.' }]}><Input placeholder="Ví dụ: Việt Nam" /></Form.Item>
          <Form.Item name="code" label="Mã tiền tệ" rules={[{ required: true, message: 'Nhập mã tiền tệ.' }, { max: 10 }]}><Input className={styles.uppercase} placeholder="VND" onChange={event => form.setFieldValue('code', event.target.value.toUpperCase())} /></Form.Item>
          <Form.Item name="symbol" label="Ký hiệu tiền tệ"><Input placeholder="₫, $, €..." /></Form.Item>
          <Form.Item name="position" label="Vị trí hiển thị" rules={[{ required: true }]}><Select options={[{ value: 'right', label: 'Phía bên phải' }, { value: 'left', label: 'Phía bên trái' }]} /></Form.Item>
          <Form.Item name="thousand_separator" label="Dấu phân cách hàng nghìn" rules={[{ required: true }]}><Select options={[{ value: 'comma', label: ', (Comma)' }, { value: 'dot', label: '. (Dot)' }, { value: 'space', label: 'Khoảng trắng (Space)' }]} /></Form.Item>
          <Form.Item name="decimal_separator" label="Dấu phân cách thập phân" rules={[{ required: true }]}><Select options={[{ value: 'dot', label: '. (Dot)' }, { value: 'comma', label: ', (Comma)' }]} /></Form.Item>
          <Form.Item name="decimal_places" label="Số thập phân" rules={[{ required: true }]}><Select options={[0, 1, 2, 3, 4].map(value => ({ value, label: value === 0 ? '0' : `0.${'0'.repeat(value)}` }))} /></Form.Item>
          <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={[{ value: 'active', label: 'Kích hoạt' }, { value: 'inactive', label: 'Tạm tắt' }]} /></Form.Item>
          <Form.Item className={styles.full} name="exchange_rate" label="Tỷ giá quy đổi (bao nhiêu VND cho 1 đơn vị tiền tệ này)" rules={[{ required: true, message: 'Nhập tỷ giá.' }]}><InputNumber className={styles.number} min={0} precision={4} controls={false} placeholder="Nhập tỷ giá quy đổi" /></Form.Item>
        </div>
        <div className={styles.livePreview}><span>Xem trước định dạng</span><strong>{preview(watched)}</strong></div>
      </Form>
    </Modal>
  </main>;
}
