'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Empty, Input, Pagination, Select, Space, Table, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DollarCircleOutlined, HistoryOutlined, RiseOutlined, SearchOutlined, SwapOutlined, WalletOutlined } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './cashflow.module.css';

const { Text, Title } = Typography;

type Transaction = {
  id: number;
  transaction_code?: string;
  type: 'add' | 'sub' | string;
  description?: string;
  amount: number | string;
  balance_after: number | string;
  amount_display?: number | string;
  balance_after_display?: number | string;
  created_at: string;
};
type Summary = { balance: number; deposited: number; spent: number; successful: number };

export default function CashflowPage() {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [type, setType] = useState('all');
  const [searchText, setSearchText] = useState('');
  const [search, setSearch] = useState('');
  const [currency, setCurrency] = useState('VND');
  const [summary, setSummary] = useState<Summary>({ balance: 0, deposited: 0, spent: 0, successful: 0 });
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });

  const loadTransactions = useCallback(async (page = 1, pageSize = 15) => {
    setLoading(true);
    try {
      const response = await api.get('/client/cashflow', {
        params: { page, per_page: pageSize, type: type === 'all' ? undefined : type, search },
      });
      if (response.data?.status) {
        const data = response.data.data;
        setTransactions(data.data || []);
        setCurrency(response.data.currency || 'VND');
        setSummary(response.data.summary || { balance: 0, deposited: 0, spent: 0, successful: 0 });
        setPagination({ current: data.current_page || 1, pageSize: data.per_page || 15, total: data.total || 0 });
      }
    } catch {
      message.error('Không thể tải lịch sử dòng tiền.');
    } finally {
      setLoading(false);
    }
  }, [search, type]);

  useEffect(() => { void Promise.resolve().then(() => loadTransactions(1)); }, [loadTransactions]);

  const submitSearch = () => {
    const value = searchText.trim();
    setSearch(value);
    if (value === search) void loadTransactions(1, pagination.pageSize);
  };
  const formatMoney = (value: number | string) => currency === 'VND'
    ? `${Math.round(Number(value || 0)).toLocaleString('vi-VN')} đ`
    : Number(value || 0).toLocaleString('en-US', { style: 'currency', currency });
  const columns = [
    { title: 'Thời gian', dataIndex: 'created_at', width: 175, render: (value: string) => new Date(value).toLocaleString('vi-VN') },
    { title: 'Nội dung', dataIndex: 'description', width: 340, className: styles.description, render: (value?: string) => value || '-' },
    { title: 'Mã giao dịch', dataIndex: 'transaction_code', width: 190, render: (value?: string) => value ? <Text copyable>{value}</Text> : '-' },
    { title: 'Số tiền', dataIndex: 'amount_display', width: 160, align: 'right' as const, render: (value: number | string, item: Transaction) => <Text strong type={item.type === 'sub' ? 'danger' : 'success'}>{item.type === 'sub' ? '-' : '+'} {formatMoney(value ?? item.amount)}</Text> },
    { title: 'Số dư', dataIndex: 'balance_after_display', width: 160, align: 'right' as const, render: (value: number | string, item: Transaction) => <Text strong>{formatMoney(value ?? item.balance_after)}</Text> },
  ];

  return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}><div><Title level={3}>Lịch sử dòng tiền</Title><Text type="secondary">Theo dõi toàn bộ biến động số dư trong tài khoản</Text></div><SwapOutlined /></div>
    <section className={styles.summary}>
      <Card><span className={`${styles.summaryIcon} ${styles.balance}`}><WalletOutlined /></span><div><Text type="secondary">Số dư khả dụng</Text><strong>{formatMoney(summary.balance)}</strong><a href="/addfunds">Nhấn để nạp thêm →</a></div></Card>
      <Card><span className={`${styles.summaryIcon} ${styles.deposit}`}><RiseOutlined /></span><div><Text type="secondary">Tổng nạp</Text><strong>{formatMoney(summary.deposited)}</strong></div></Card>
      <Card><span className={`${styles.summaryIcon} ${styles.spent}`}><DollarCircleOutlined /></span><div><Text type="secondary">Tổng chi tiêu</Text><strong>{formatMoney(summary.spent)}</strong></div></Card>
      <Card><span className={`${styles.summaryIcon} ${styles.success}`}><HistoryOutlined /></span><div><Text type="secondary">Giao dịch thành công</Text><strong>{summary.successful.toLocaleString('vi-VN')} phát sinh</strong></div></Card>
    </section>
    <Card className={styles.card}>
      <div className={styles.filters}>
        <Select value={type} onChange={setType} options={[{ value: 'all', label: 'Tất cả giao dịch' }, { value: 'add', label: 'Tiền vào' }, { value: 'sub', label: 'Tiền ra' }]} />
        <Space.Compact className={styles.search}><Input allowClear prefix={<SearchOutlined />} placeholder="Nhập mã giao dịch hoặc nội dung..." value={searchText} onChange={(event) => setSearchText(event.target.value)} onPressEnter={submitSearch} /><Button type="primary" icon={<SearchOutlined />} onClick={submitSearch}>Tìm kiếm</Button></Space.Compact>
      </div>
      <Table rowKey="id" columns={columns} dataSource={transactions} loading={loading} pagination={false} scroll={{ x: 950 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có giao dịch nào" /> }} />
      <div className={styles.pagination}><Pagination {...pagination} showTotal={(total) => `${total} giao dịch`} showSizeChanger pageSizeOptions={[10, 15, 20, 50]} locale={{ items_per_page: '/ trang' }} onChange={(page, pageSize) => loadTransactions(page, pageSize)} /></div>
    </Card>
  </div></ClientLayout>;
}
