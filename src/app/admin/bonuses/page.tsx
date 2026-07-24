'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Form, InputNumber, Modal, Select, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './bonuses.module.css';

const { Text, Title } = Typography;
type Bonus = { id: number; type: 'banking' | 'usdt' | 'binance'; min_amount: number; bonus: number; status: 'active' | 'inactive'; created_at: string };
type BonusForm = Omit<Bonus, 'id' | 'created_at'>;
const typeMeta = { banking: { label: 'Nạp ngân hàng', color: 'blue' }, usdt: { label: 'Nạp USDT', color: 'success' }, binance: { label: 'Binance Pay', color: 'warning' } } as const;

export default function AdminBonusesPage() {
  const [form] = Form.useForm<BonusForm>();
  const [rows, setRows] = useState<Bonus[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [open, setOpen] = useState(false);
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/resources/bonuses', { params: { page: current, per_page: pageSize } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách tiền thưởng.'); }
    finally { setLoading(false); }
  }, []);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const create = () => { form.resetFields(); form.setFieldsValue({ type: 'banking', min_amount: 1, bonus: 0, status: 'active' }); setOpen(true); };
  const save = async () => {
    try {
      const values = await form.validateFields(); setSaving(true);
      const response = await api.post('/admin/resources/bonuses', { data: values });
      message.success(response.data.message || 'Đã thêm tiền thưởng.'); setOpen(false); await load(1, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể thêm tiền thưởng.');
    } finally { setSaving(false); }
  };
  const remove = (item: Bonus) => Modal.confirm({ title: 'Xóa mức thưởng này?', content: `${typeMeta[item.type].label}: từ $${item.min_amount} được thưởng ${item.bonus}%`, okText: 'Xóa', okButtonProps: { danger: true }, cancelText: 'Hủy', onOk: async () => { await api.delete(`/admin/resources/bonuses/${item.id}`); message.success('Đã xóa mức thưởng.'); await load(page.current, page.pageSize); } });

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 90 },
    { title: 'Thao tác', width: 140, render: (_: unknown, item: Bonus) => <Button type="text" danger icon={<DeleteOutlined />} onClick={() => remove(item)} /> },
    { title: 'Loại nạp', dataIndex: 'type', width: 250, render: (value: Bonus['type']) => <Tag color={typeMeta[value].color}>{typeMeta[value].label}</Tag> },
    { title: 'Số tiền', dataIndex: 'min_amount', width: 220, render: (value: number) => <b>${Number(value).toLocaleString('en-US')}</b> },
    { title: 'Thưởng thêm', dataIndex: 'bonus', width: 260, render: (value: number) => <span className={styles.bonus}>+{Number(value).toLocaleString('vi-VN')}%</span> },
    { title: 'Trạng thái', dataIndex: 'status', width: 180, render: (value: string) => <Tag color={value === 'active' ? 'success' : 'error'}>{value === 'active' ? 'Kích hoạt' : 'Không kích hoạt'}</Tag> },
    { title: 'Thời gian tạo', dataIndex: 'created_at', render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Tiền thưởng nạp</Title><Text type="secondary">Thiết lập phần trăm thưởng theo phương thức và mức nạp tối thiểu</Text></div></header>
    <Card className={styles.card} title="Danh sách tiền thưởng" extra={<Button type="primary" icon={<PlusOutlined />} onClick={create}>Thêm mới</Button>}>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1300 }} pagination={{ ...page, showSizeChanger: true, showTotal: total => `${total} mức thưởng`, locale: { items_per_page: '/ trang' } }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
    <Modal width={500} open={open} title="Thêm tiền thưởng" okText="Thêm tiền thưởng" cancelText="Đóng" confirmLoading={saving} onOk={() => void save()} onCancel={() => setOpen(false)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <Form.Item name="type" label="Thưởng cho" rules={[{ required: true }]}><Select options={[{ value: 'banking', label: 'Nạp ngân hàng' }, { value: 'usdt', label: 'USDT' }, { value: 'binance', label: 'Binance Pay' }]} /></Form.Item>
        <Form.Item name="min_amount" label="Số tiền nạp tối thiểu USD" extra="Đơn vị mặc định là USD" rules={[{ required: true, message: 'Nhập số tiền tối thiểu.' }]}><InputNumber min={0} precision={0} style={{ width: '100%' }} placeholder="Nhập số tiền nạp tối thiểu" /></Form.Item>
        <Form.Item name="bonus" label="Thưởng thêm (%)" rules={[{ required: true, message: 'Nhập phần trăm thưởng.' }]}><InputNumber min={0} max={100} precision={0} style={{ width: '100%' }} placeholder="Nhập số % thưởng thêm" /></Form.Item>
        <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={[{ value: 'active', label: 'Kích hoạt' }, { value: 'inactive', label: 'Không kích hoạt' }]} /></Form.Item>
      </Form>
    </Modal>
  </main>;
}
