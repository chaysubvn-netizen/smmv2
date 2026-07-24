'use client';

import { useCallback, useEffect, useState } from 'react';
import { Card, Input, Table, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DollarOutlined, GiftOutlined, SearchOutlined, TeamOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './affiliates.module.css';

const { Text, Title } = Typography;
type Affiliate = { id: number; referral_code?: string; commission: number | string; created_at: string; user?: { id: number; username: string; total_deposit: number | string }; ref_user?: { id: number; username: string } };
type Meta = { total: number; total_deposit: number; total_commission: number };
const money = (value: number | string | undefined) => `${Math.round(Number(value || 0)).toLocaleString('vi-VN')}đ`;

export default function AdminAffiliatesPage() {
  const [rows, setRows] = useState<Affiliate[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [meta, setMeta] = useState<Meta>({ total: 0, total_deposit: 0, total_commission: 0 });
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20, keyword = '') => {
    setLoading(true);
    try {
      const response = await api.get('/admin/affiliates', { params: { page: current, per_page: pageSize, search: keyword.trim() || undefined } });
      const data = response.data.data;
      setRows(data.data || []); setMeta(response.data.meta || {});
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách tiếp thị liên kết.'); }
    finally { setLoading(false); }
  }, []);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 85 },
    { title: 'Tài khoản', width: 220, render: (_: unknown, item: Affiliate) => <div className={styles.user}><span>{(item.user?.username || '?').slice(0, 1).toUpperCase()}</span><div><b>{item.user?.username || 'Tài khoản đã xóa'}</b><small>ID: {item.user?.id || '—'}</small></div></div> },
    { title: 'Người giới thiệu', width: 220, render: (_: unknown, item: Affiliate) => <div className={styles.referrer}><b>{item.ref_user?.username || 'Không có'}</b><small>{item.referral_code ? `Mã: ${item.referral_code}` : 'Không có mã giới thiệu'}</small></div> },
    { title: 'Tổng tiền nạp', width: 200, render: (_: unknown, item: Affiliate) => <span className={styles.deposit}>{money(item.user?.total_deposit)}</span> },
    { title: 'Tổng hoa hồng nhận', dataIndex: 'commission', width: 220, render: (value: number | string) => <span className={styles.commission}>{money(value)}</span> },
    { title: 'Thời gian', dataIndex: 'created_at', render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  const stats = [
    { label: 'Tổng đã mời', value: meta.total.toLocaleString('vi-VN'), icon: <TeamOutlined />, className: styles.blue },
    { label: 'Tổng đã nạp', value: money(meta.total_deposit), icon: <DollarOutlined />, className: styles.green },
    { label: 'Tổng hoa hồng nhận', value: money(meta.total_commission), icon: <GiftOutlined />, className: styles.purple },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý Affiliate</Title><Text type="secondary">Theo dõi thành viên được giới thiệu và hoa hồng phát sinh</Text></div></header>
    <section className={styles.stats}>{stats.map(stat => <Card key={stat.label} className={`${styles.stat} ${stat.className}`}><span className={styles.statIcon}>{stat.icon}</span><div><small>{stat.label}</small><strong>{stat.value}</strong></div></Card>)}</section>
    <Card className={styles.card} title={<span>Danh Sách Người Được Giới Thiệu — Hiện có <b>{meta.total.toLocaleString('vi-VN')}</b> người</span>} extra={<Input className={styles.search} value={search} allowClear prefix={<SearchOutlined />} placeholder="Tìm tài khoản hoặc mã giới thiệu" onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize, search)} />}>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1150 }} pagination={{ ...page, showSizeChanger: true, showTotal: total => `${total} người`, locale: { items_per_page: '/ trang' } }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20, search)} />
    </Card>
  </main>;
}
