'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, DatePicker, Input, Select, Space, Statistic, Table, Tag, Typography } from 'antd';
import { ClockCircleOutlined, ReloadOutlined, SafetyCertificateOutlined, SearchOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import type { Dayjs } from 'dayjs';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from './auth-logs.module.css';

const { RangePicker } = DatePicker;
const { Text, Title } = Typography;

type AuthLog = {
  id: number;
  user_id?: number | null;
  username?: string | null;
  event: string;
  success: boolean;
  ip_address?: string | null;
  user_agent?: string | null;
  message?: string | null;
  metadata?: Record<string, unknown> | null;
  created_at: string;
};

type LogMeta = {
  last_24h: number;
  successful_24h: number;
  failed_24h: number;
  unique_ips_24h: number;
};

const eventMeta: Record<string, { label: string; color: string }> = {
  login_success: { label: 'Đăng nhập thành công', color: 'success' },
  login_failed: { label: 'Sai thông tin đăng nhập', color: 'error' },
  login_blocked: { label: 'Tài khoản bị khóa', color: 'error' },
  two_factor_required: { label: 'Yêu cầu 2FA', color: 'processing' },
  two_factor_failed: { label: 'Sai mã 2FA', color: 'warning' },
  logout: { label: 'Đăng xuất', color: 'default' },
  password_changed: { label: 'Đổi mật khẩu', color: 'purple' },
};

const describeAgent = (agent?: string | null) => {
  if (!agent) return 'Không xác định';
  const browser = /Edg\//.test(agent) ? 'Edge' : /Chrome\//.test(agent) ? 'Chrome' : /Firefox\//.test(agent) ? 'Firefox' : /Safari\//.test(agent) ? 'Safari' : 'Trình duyệt khác';
  const os = /Windows/.test(agent) ? 'Windows' : /Android/.test(agent) ? 'Android' : /iPhone|iPad/.test(agent) ? 'iOS' : /Mac OS/.test(agent) ? 'macOS' : /Linux/.test(agent) ? 'Linux' : 'Thiết bị khác';
  return `${browser} · ${os}`;
};

export default function AdminAuthLogsPage() {
  const [rows, setRows] = useState<AuthLog[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [event, setEvent] = useState<string>();
  const [success, setSuccess] = useState<boolean>();
  const [dates, setDates] = useState<[Dayjs | null, Dayjs | null] | null>(null);
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });
  const [meta, setMeta] = useState<LogMeta>({ last_24h: 0, successful_24h: 0, failed_24h: 0, unique_ips_24h: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/auth-logs', { params: {
        page: current,
        per_page: pageSize,
        search: search.trim() || undefined,
        event,
        success: success === undefined ? undefined : success ? 1 : 0,
        start_date: dates?.[0]?.format('YYYY-MM-DD'),
        end_date: dates?.[1]?.format('YYYY-MM-DD'),
      } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
      setMeta(response.data.meta || { last_24h: 0, successful_24h: 0, failed_24h: 0, unique_ips_24h: 0 });
    } catch {
      message.error('Không thể tải nhật ký đăng nhập.');
    } finally {
      setLoading(false);
    }
  }, [dates, event, search, success]);

  useEffect(() => {
    const timer = window.setTimeout(() => void load(), 0);
    return () => window.clearTimeout(timer);
  }, [load]);

  const reset = () => {
    setSearch('');
    setEvent(undefined);
    setSuccess(undefined);
    setDates(null);
  };

  const columns = [
    { title: 'Thời gian', dataIndex: 'created_at', width: 175, render: (value: string) => <span className={styles.time}><ClockCircleOutlined /> {dayjs(value).format('DD/MM/YYYY HH:mm:ss')}</span> },
    { title: 'Tài khoản', dataIndex: 'username', width: 155, render: (value?: string | null) => value ? <Tag color="blue">{value}</Tag> : <Text type="secondary">Không xác định</Text> },
    { title: 'Sự kiện', dataIndex: 'event', width: 205, render: (value: string) => { const info = eventMeta[value] || { label: value, color: 'default' }; return <Tag color={info.color}>{info.label}</Tag>; } },
    { title: 'Kết quả', dataIndex: 'success', width: 105, render: (value: boolean) => <Tag color={value ? 'success' : 'error'}>{value ? 'Thành công' : 'Thất bại'}</Tag> },
    { title: 'Địa chỉ IP', dataIndex: 'ip_address', width: 145, render: (value?: string | null) => <code className={styles.ip}>{value || 'N/A'}</code> },
    { title: 'Thiết bị', dataIndex: 'user_agent', width: 180, render: (value?: string | null) => <span title={value || ''}>{describeAgent(value)}</span> },
    { title: 'Chi tiết', dataIndex: 'message', width: 300, render: (value?: string | null) => value || '—' },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}>
      <div><Title level={2}>Nhật ký đăng nhập</Title><Text type="secondary">Theo dõi đăng nhập, 2FA, IP và thiết bị truy cập hệ thống</Text></div>
      <Button icon={<ReloadOutlined />} onClick={() => void load(page.current, page.pageSize)}>Làm mới</Button>
    </header>

    <section className={styles.stats}>
      <Card><Statistic title="Hoạt động 24 giờ" value={meta.last_24h} /></Card>
      <Card><Statistic title="Đăng nhập thành công" value={meta.successful_24h} valueStyle={{ color: '#16a34a' }} /></Card>
      <Card><Statistic title="Thất bại / sai 2FA" value={meta.failed_24h} valueStyle={{ color: '#dc2626' }} /></Card>
      <Card><Statistic title="IP khác nhau" value={meta.unique_ips_24h} /></Card>
    </section>

    <Card className={styles.card} title={<Space><SafetyCertificateOutlined />Lịch sử xác thực</Space>}>
      <div className={styles.filters}>
        <Input allowClear value={search} prefix={<SearchOutlined />} placeholder="Tài khoản, IP hoặc nội dung..." onChange={e => setSearch(e.target.value)} onPressEnter={() => void load(1, page.pageSize)} />
        <Select allowClear value={event} placeholder="Tất cả sự kiện" onChange={setEvent} options={Object.entries(eventMeta).map(([value, item]) => ({ value, label: item.label }))} />
        <Select allowClear value={success} placeholder="Tất cả kết quả" onChange={setSuccess} options={[{ value: true, label: 'Thành công' }, { value: false, label: 'Thất bại' }]} />
        <RangePicker value={dates} format="DD/MM/YYYY" onChange={setDates} placeholder={['Từ ngày', 'Đến ngày']} />
        <Button type="primary" icon={<SearchOutlined />} onClick={() => void load(1, page.pageSize)}>Tìm kiếm</Button>
        <Button onClick={reset}>Đặt lại</Button>
      </div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1265 }} pagination={{ ...page, showSizeChanger: true, showTotal: total => `${total} bản ghi` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
  </main>;
}
