'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Input, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { SearchOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './deposits.module.css';

const { Text, Title } = Typography;
type Deposit = { id: number; method: string; type?: string; transaction_id?: string; amount: number; bonus: number; real_amount: number; amount_format?: string; bonus_format?: string; real_amount_format?: string; description?: string; status: string; created_at: string; user?: { id: number; username: string } };

const methodLabel = (item: Deposit) => {
  if (item.method === 'Cộng tay') return 'Cộng tay';
  if (item.type === 'bank' || item.method === 'banking') return 'Chuyển khoản';
  if (item.method === 'binance' || item.type === 'binance') return 'Nạp Binance';
  return 'Nạp USDT';
};
const fallbackMoney = (value: number) => `${Math.round(Number(value || 0)).toLocaleString('vi-VN')}đ`;

export default function AdminDepositsPage() {
  const [rows, setRows] = useState<Deposit[]>([]);
  const [loading, setLoading] = useState(true);
  const [type, setType] = useState<string>();
  const [search, setSearch] = useState('');
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20, selectedType?: string, keyword = '') => {
    setLoading(true);
    try {
      const response = await api.get('/admin/deposits', { params: { page: current, per_page: pageSize, type: selectedType, search: keyword.trim() || undefined } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải lịch sử nạp tiền.'); }
    finally { setLoading(false); }
  }, []);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const approve = (item: Deposit) => Modal.confirm({ title: 'Xác nhận duyệt nạp tiền?', content: `Số tiền ${item.amount_format || fallbackMoney(item.amount)} sẽ được xử lý và cộng vào tài khoản ${item.user?.username || ''}.`, okText: 'Đồng ý', cancelText: 'Hủy', onOk: async () => { try { const response = await api.post(`/admin/deposits/${item.id}/approve`); if (response.data?.success === false) throw new Error(response.data.message); message.success(response.data.message || 'Duyệt nạp tiền thành công.'); await load(page.current, page.pageSize, type, search); } catch (error: unknown) { const detail = error as { message?: string; response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || detail.message || 'Không thể duyệt giao dịch.'); } } });
  const cancel = (item: Deposit) => Modal.confirm({ title: 'Xác nhận hủy nạp tiền?', content: 'Giao dịch này sẽ được đánh dấu là thất bại.', okText: 'Đồng ý', okButtonProps: { danger: true }, cancelText: 'Đóng', onOk: async () => { try { const response = await api.post(`/admin/deposits/${item.id}/cancel`); if (response.data?.success === false) throw new Error(response.data.message); message.success(response.data.message || 'Đã hủy giao dịch.'); await load(page.current, page.pageSize, type, search); } catch (error: unknown) { const detail = error as { message?: string; response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || detail.message || 'Không thể hủy giao dịch.'); } } });

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 55 },
    { title: 'Tài khoản', width: 130, render: (_: unknown, item: Deposit) => item.user?.username || '—' },
    { title: 'Phương thức', width: 145, render: (_: unknown, item: Deposit) => methodLabel(item) },
    { title: 'Mã giao dịch', dataIndex: 'transaction_id', width: 160, render: (value?: string) => value || '—' },
    { title: 'Số tiền', width: 110, render: (_: unknown, item: Deposit) => <span className={styles.money}>{item.amount_format || fallbackMoney(item.amount)}</span> },
    { title: 'Khuyến mãi', width: 110, render: (_: unknown, item: Deposit) => <span className={styles.money}>{item.bonus_format || fallbackMoney(item.bonus)}</span> },
    { title: 'Thực nhận', width: 120, render: (_: unknown, item: Deposit) => <span className={styles.money}>{item.real_amount_format || fallbackMoney(item.real_amount)}</span> },
    { title: 'Nội dung', dataIndex: 'description', width: 250, render: (value?: string) => <div className={styles.description}>{value || '—'}</div> },
    { title: 'Trạng thái', dataIndex: 'status', width: 120, render: (value: string) => value === 'completed' ? <Tag color="success">Thành công</Tag> : value === 'failed' ? <Tag color="error">Thất bại</Tag> : <Tag color="warning">Chờ duyệt</Tag> },
    { title: 'Thời gian', dataIndex: 'created_at', width: 180, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
    { title: 'Thao tác', width: 145, fixed: 'right' as const, render: (_: unknown, item: Deposit) => item.status !== 'completed' && item.status !== 'failed' ? <Space size={5}><Button type="primary" size="small" onClick={() => approve(item)}>Duyệt</Button><Button danger type="primary" size="small" onClick={() => cancel(item)}>Hủy</Button></Space> : null },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Lịch sử nạp tiền</Title><Text type="secondary">Theo dõi và duyệt giao dịch nạp tiền của thành viên</Text></div></header>
    <Card className={styles.card} title="Danh sách đã nạp tiền">
      <div className={styles.filter}><label className={styles.methodFilter}><span>Phương thức nạp</span><Select allowClear value={type} placeholder="Chọn phương thức nạp" options={[{ value: 'bank', label: 'Chuyển khoản' }, { value: 'usdt', label: 'Nạp USDT' }, { value: 'binance', label: 'Nạp Binance' }, { value: 'manual', label: 'Cộng tay' }]} onChange={value => { setType(value); void load(1, page.pageSize, value, search); }} /></label><label className={styles.searchFilter}><span>Tìm kiếm</span><Input value={search} prefix={<SearchOutlined />} allowClear placeholder="Username, mã giao dịch, nội dung..." onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize, type, search)} /></label><Button type="primary" icon={<SearchOutlined />} onClick={() => void load(1, page.pageSize, type, search)}>Tìm kiếm</Button></div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1545 }} pagination={{ ...page, showSizeChanger: true, showLessItems: true, showTotal: total => `${total} giao dịch`, locale: { items_per_page: '/ trang' } }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20, type, search)} />
    </Card>
  </main>;
}
