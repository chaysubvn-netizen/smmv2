'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Empty, Input, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { CustomerServiceOutlined, FilterFilled, LinkOutlined } from '@ant-design/icons';
import Link from 'next/link';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './drip-feed.module.css';

const { Text } = Typography;
type Service = { id: number; name: string };
type DripFeed = { id: number; link?: string; quantity: number; total_quantity?: number; total_format?: string; start_count?: number; remains?: number; run?: number; loop_quantity?: number; loop_spacing?: number; dripfeed_status?: string; created_at: string; service?: Service };
const statusColor: Record<string, string> = { Processing: 'processing', Completed: 'green', Canceled: 'red' };
const statusLabel: Record<string, string> = { Processing: 'Đang xử lý', Completed: 'Hoàn thành', Canceled: 'Hủy' };

export default function DripFeedPage() {
  const [orders, setOrders] = useState<DripFeed[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });

  const load = useCallback(async (page = 1) => {
    setLoading(true);
    try {
      const response = await api.get('/client/drip-feeds', { params: { page, search, status } });
      if (response.data?.status) {
        const data = response.data.data;
        setOrders(data.data || []);
        setPagination({ current: data.current_page || 1, pageSize: data.per_page || 15, total: data.total || 0 });
      }
    } catch { message.error('Không thể tải danh sách Drip-feed.'); }
    finally { setLoading(false); }
  }, [search, status]);
  useEffect(() => { void Promise.resolve().then(() => load(1)); }, [load]);

  const columns = [
    { title: 'Thông tin', width: 190, render: (_: unknown, order: DripFeed) => <div className={styles.stack}><Text strong className={styles.id}>#{order.id}</Text><Text type="secondary">{new Date(order.created_at).toLocaleString('vi-VN')}</Text><Link href="/tickets"><Button size="small" type="primary" icon={<CustomerServiceOutlined />}>Hỗ trợ</Button></Link></div> },
    { title: 'Dịch vụ', width: 390, render: (_: unknown, order: DripFeed) => <div className={styles.stack}><Text strong><span className={styles.id}>#{order.service?.id}</span> - {order.service?.name || '-'}</Text>{order.link ? <a href={order.link} target="_blank" rel="noreferrer"><LinkOutlined /> {order.link}</a> : '-'}</div> },
    { title: 'Đơn hàng', width: 230, render: (_: unknown, order: DripFeed) => <div className={styles.stack}><span>Tổng tiền: <b className={styles.id}>{order.total_format || '0'}</b></span><span>Số lượng: <b>{Number(order.total_quantity || 0).toLocaleString()}</b></span><span>Bắt đầu: <b>{Number(order.start_count || 0).toLocaleString()}</b></span><span>Còn lại: <b>{Number(order.remains || 0).toLocaleString()}</b></span></div> },
    { title: 'Tiến độ', width: 210, render: (_: unknown, order: DripFeed) => <div className={styles.stack}><span>Trạng thái: <Tag color={statusColor[order.dripfeed_status || ''] || 'default'}>{statusLabel[order.dripfeed_status || ''] || order.dripfeed_status}</Tag></span><span>Khoảng cách: <b>{order.loop_spacing || 0} phút</b></span><span>Đã chạy: <b className={styles.id}>{order.run || 0}</b> / <b>{order.loop_quantity || 0}</b></span></div> },
  ];

  return <ClientLayout><div className={styles.page}><Card className={styles.card}>
    <div className={styles.filters}><Select value={status} onChange={setStatus} options={[{value:'all',label:'Tất cả trạng thái'},{value:'Processing',label:'Đang xử lý'},{value:'Completed',label:'Hoàn thành'},{value:'Canceled',label:'Hủy'}]} /><Space.Compact className={styles.search}><Input allowClear placeholder="Tìm kiếm theo mã đơn hàng" value={search} onChange={(event) => setSearch(event.target.value)} onPressEnter={() => load(1)} /><Button type="primary" icon={<FilterFilled />} onClick={() => load(1)}>Tìm kiếm</Button></Space.Compact></div>
    <Table rowKey="id" columns={columns} dataSource={orders} loading={loading} scroll={{ x: 1020 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Không có đơn hàng nào" /> }} pagination={{ ...pagination, showSizeChanger: false, onChange: load }} />
  </Card></div></ClientLayout>;
}
