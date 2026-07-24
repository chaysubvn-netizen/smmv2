'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, DatePicker, Input, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { ReloadOutlined, SearchOutlined, SwapOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import type { Dayjs } from 'dayjs';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './transactions.module.css';

const { Text, Title } = Typography;
const { RangePicker } = DatePicker;

type Transaction = {
  id: number; transaction_code?: string; type?: string; amount?: number | string;
  amount_format?: string; balance_before_format?: string; balance_after_format?: string;
  description?: string; status?: string; created_at: string;
  user?: { id: number; username: string };
};

const statusMeta: Record<string, { label: string; color: string }> = {
  success: { label: 'Hoàn thành', color: 'success' }, completed: { label: 'Hoàn thành', color: 'success' },
  pending: { label: 'Đang xử lý', color: 'warning' }, processing: { label: 'Đang xử lý', color: 'processing' },
  failed: { label: 'Thất bại', color: 'error' }, cancelled: { label: 'Đã hủy', color: 'default' },
};

export default function AdminTransactionsPage() {
  const [rows, setRows] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [type, setType] = useState<string>();
  const [status, setStatus] = useState<string>();
  const [dates, setDates] = useState<[Dayjs | null, Dayjs | null] | null>(null);
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/transactions', { params: {
        page: current, per_page: pageSize, search: search.trim() || undefined, type, status,
        start_date: dates?.[0]?.format('YYYY-MM-DD'), end_date: dates?.[1]?.format('YYYY-MM-DD'),
      } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải lịch sử giao dịch.'); }
    finally { setLoading(false); }
  }, [dates, search, status, type]);

  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const reset = () => { setSearch(''); setType(undefined); setStatus(undefined); setDates(null); };
  const columns = [
    { title: 'ID', dataIndex: 'id', width: 65 },
    { title: 'Tài khoản', width: 150, render: (_: unknown, item: Transaction) => item.user ? <Tag color="blue">{item.user.username}</Tag> : <Text type="danger">Đã xóa</Text> },
    { title: 'Mã giao dịch', dataIndex: 'transaction_code', width: 210, render: (value?: string) => <span className={styles.code}>{value || 'N/A'}</span> },
    { title: 'Nhận', width: 145, render: (_: unknown, item: Transaction) => <strong className={item.type === 'add' ? styles.add : styles.subtract}>{item.type === 'add' ? '+' : '-'}{item.amount_format ?? item.amount ?? 0}</strong> },
    { title: 'Trước khi', dataIndex: 'balance_before_format', width: 150, render: (value?: string) => <span className={styles.balance}>{value ?? 0}</span> },
    { title: 'Sau khi', dataIndex: 'balance_after_format', width: 150, render: (value?: string) => <span className={styles.balance}>{value ?? 0}</span> },
    { title: 'Ghi chú', dataIndex: 'description', width: 360, render: (value?: string) => <div className={styles.note}>{value || '—'}</div> },
    { title: 'Trạng thái', dataIndex: 'status', width: 135, render: (value?: string) => { const meta = statusMeta[(value || '').toLowerCase()] || { label: value || 'Đang xử lý', color: 'warning' }; return <Tag color={meta.color}>{meta.label}</Tag>; } },
    { title: 'Thời gian', dataIndex: 'created_at', width: 180, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Lịch sử giao dịch</Title><Text type="secondary">Theo dõi toàn bộ biến động số dư của thành viên</Text></div></header>
    <Card className={styles.card} title={<Space><SwapOutlined />Lịch sử giao dịch</Space>}>
      <div className={styles.filters}>
        <Input allowClear value={search} prefix={<SearchOutlined />} placeholder="Username, mã giao dịch, ghi chú..." onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize)} />
        <Select allowClear value={type} placeholder="Tất cả loại" onChange={setType} options={[{ value: 'add', label: 'Cộng tiền' }, { value: 'subtract', label: 'Trừ tiền' }]} />
        <Select allowClear value={status} placeholder="Tất cả trạng thái" onChange={setStatus} options={[{ value: 'success', label: 'Hoàn thành' }, { value: 'pending', label: 'Đang xử lý' }, { value: 'failed', label: 'Thất bại' }]} />
        <RangePicker value={dates} format="DD/MM/YYYY" placeholder={['Từ ngày', 'Đến ngày']} onChange={value => setDates(value)} />
        <Button type="primary" icon={<SearchOutlined />} onClick={() => void load(1, page.pageSize)}>Tìm kiếm</Button>
        <Button icon={<ReloadOutlined />} onClick={reset}>Đặt lại</Button>
      </div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1545 }} pagination={{ ...page, showSizeChanger: true, locale: { items_per_page: '/ trang' }, showTotal: total => `${total} giao dịch` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
  </main>;
}
