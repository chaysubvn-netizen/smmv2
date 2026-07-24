'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Empty, Input, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { FilterFilled } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './subscriptions.module.css';

const { Text } = Typography;

type Service = { id: number; name: string };
type Subscription = { id: number; status: string; created_at: string; remains?: number; input_data?: Record<string, string | number>; service?: Service };
const statusColor: Record<string, string> = { Pending: 'gold', Processing: 'blue', 'In progress': 'processing', Completed: 'green', Partial: 'orange', Canceled: 'red' };
const statusLabel: Record<string, string> = { Pending: 'Chờ xử lý', Processing: 'Đang xử lý', 'In progress': 'Đang thực hiện', Completed: 'Hoàn thành', Partial: 'Hoàn tiền một phần', Canceled: 'Hủy' };

export default function SubscriptionsPage() {
  const [orders, setOrders] = useState<Subscription[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });

  const fetchSubscriptions = useCallback(async (page = 1) => {
    setLoading(true);
    try {
      const response = await api.get('/client/subscriptions', { params: { page, search, status } });
      if (response.data?.status) {
        const data = response.data.data;
        setOrders(data.data || []);
        setPagination({ current: data.current_page || 1, pageSize: data.per_page || 15, total: data.total || 0 });
      }
    } catch { message.error('Không thể tải danh sách subscriptions.'); }
    finally { setLoading(false); }
  }, [search, status]);

  useEffect(() => { void Promise.resolve().then(() => fetchSubscriptions(1)); }, [fetchSubscriptions]);

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 75, render: (id: number) => <Text strong>#{id}</Text> },
    { title: 'Username', render: (_: unknown, item: Subscription) => <Text copyable>{String(item.input_data?.username || '-')}</Text> },
    { title: 'Quantity', render: (_: unknown, item: Subscription) => `${Number(item.input_data?.min || 0).toLocaleString()} - ${Number(item.input_data?.max || 0).toLocaleString()}` },
    { title: 'Post', render: (_: unknown, item: Subscription) => `${item.remains || 0} / ${item.input_data?.posts || 0}` },
    { title: 'Old Post', render: (_: unknown, item: Subscription) => `0 / ${item.input_data?.old_posts || 0}` },
    { title: 'Service', render: (_: unknown, item: Subscription) => <span><b>#{item.service?.id}</b> - {item.service?.name || '-'}</span> },
    { title: 'Status', dataIndex: 'status', render: (value: string) => <Tag color={statusColor[value] || 'default'}>{statusLabel[value] || value}</Tag> },
    { title: 'Created', dataIndex: 'created_at', render: (value: string) => new Date(value).toLocaleString('vi-VN') },
  ];

  return <ClientLayout><div className={styles.page}><Card className={styles.historyCard}>
    <div className={styles.filters}>
      <Select value={status} onChange={setStatus} options={[{value:'all',label:'Tất cả trạng thái'},{value:'Pending',label:'Chờ xử lý'},{value:'Processing',label:'Đang xử lý'},{value:'In progress',label:'Đang thực hiện'},{value:'Completed',label:'Hoàn thành'},{value:'Partial',label:'Hoàn tiền một phần'},{value:'Canceled',label:'Hủy'}]} />
      <Space.Compact className={styles.search}><Input allowClear placeholder="Tìm kiếm theo mã đơn hàng" value={search} onChange={(event) => setSearch(event.target.value)} onPressEnter={() => fetchSubscriptions(1)} /><Button type="primary" icon={<FilterFilled />} onClick={() => fetchSubscriptions(1)}>Tìm kiếm</Button></Space.Compact>
    </div>
    <Table rowKey="id" columns={columns} dataSource={orders} loading={loading} scroll={{ x: 1050 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Không có đơn hàng nào" /> }} pagination={{ ...pagination, showSizeChanger: false, onChange: (page) => fetchSubscriptions(page) }} />
  </Card></div></ClientLayout>;
}
