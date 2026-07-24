'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { AreaChartOutlined, CheckCircleOutlined, DollarOutlined, HistoryOutlined, RiseOutlined, ShoppingCartOutlined, TeamOutlined, WalletOutlined } from '@ant-design/icons';
import { Card, Empty, Select, Spin, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './statistics.module.css';

const { Text, Title } = Typography;

type DailyPoint = { date: string; orders: number; spent: number };
type Statistics = {
  summary: { orders: number; completed: number; completion_rate: number; spent: number; quantity: number };
  financial_summary: { balance: number; deposited: number; spent: number; successful: number };
  daily: DailyPoint[];
  statuses: Record<string, number>;
  top_services: { id: number; name: string; orders_count: number; spent: number }[];
  currency: string;
};

const statusLabels: Record<string, string> = {
  Completed: 'Hoàn thành', Processing: 'Đang xử lý', 'In progress': 'Đang chạy',
  Pending: 'Chờ xử lý', Partial: 'Hoàn một phần', Canceled: 'Đã hủy',
};
const statusColors: Record<string, string> = {
  Completed: '#22c55e', Processing: '#8b5cf6', 'In progress': 'var(--client-primary)',
  Pending: '#f59e0b', Partial: '#06b6d4', Canceled: '#ef4444',
};

export default function StatisticsPage() {
  const [days, setDays] = useState(30);
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<Statistics | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const response = await api.get('/client/statistics', { params: { days } });
      if (response.data?.status) setData(response.data.data);
    } catch { message.error('Không thể tải dữ liệu thống kê.'); }
    finally { setLoading(false); }
  }, [days]);

  useEffect(() => { void Promise.resolve().then(load); }, [load]);

  const money = (value: number) => data?.currency === 'VND'
    ? `${Math.round(value).toLocaleString('vi-VN')} đ`
    : `${value.toLocaleString('en-US', { style: 'currency', currency: data?.currency || 'USD' })}`;

  const chart = useMemo(() => {
    const points = data?.daily || [];
    const width = 760, height = 230, padding = 18;
    const max = Math.max(1, ...points.map(point => point.orders));
    const coords = points.map((point, index) => ({
      x: padding + (index * (width - padding * 2)) / Math.max(1, points.length - 1),
      y: height - padding - (point.orders / max) * (height - padding * 2),
      ...point,
    }));
    return { width, height, coords, line: coords.map(point => `${point.x},${point.y}`).join(' '), max };
  }, [data]);

  const statusEntries = Object.entries(data?.statuses || {}).sort((a, b) => b[1] - a[1]);
  const totalStatuses = statusEntries.reduce((sum, [, value]) => sum + value, 0);
  let angle = 0;
  const donut = statusEntries.length ? `conic-gradient(${statusEntries.map(([status, value]) => {
    const start = angle;
    angle += value * 360 / Math.max(1, totalStatuses);
    return `${statusColors[status] || '#94a3b8'} ${start}deg ${angle}deg`;
  }).join(',')})` : '#eef2f7';

  return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}>
      <div><Title level={3}>Thống kê</Title><Text type="secondary">Tổng quan hoạt động và hiệu quả đơn hàng của bạn</Text></div>
      <Select value={days} onChange={setDays} options={[{ value: 7, label: '7 ngày gần đây' }, { value: 30, label: '30 ngày gần đây' }, { value: 90, label: '90 ngày gần đây' }]} />
    </div>

    {loading && !data ? <div className={styles.loading}><Spin size="large" /></div> : <>
      <div className={`${styles.metrics} ${styles.financial}`}>
        <Card><span className={`${styles.icon} ${styles.blue}`}><WalletOutlined /></span><div><Text type="secondary">Số dư khả dụng</Text><b>{money(data?.financial_summary.balance || 0)}</b><a href="/addfunds">Nhấn để nạp thêm →</a></div></Card>
        <Card><span className={`${styles.icon} ${styles.green}`}><RiseOutlined /></span><div><Text type="secondary">Tổng nạp</Text><b>{money(data?.financial_summary.deposited || 0)}</b><small>Giao dịch tiền vào</small></div></Card>
        <Card><span className={`${styles.icon} ${styles.orange}`}><DollarOutlined /></span><div><Text type="secondary">Tổng chi tiêu</Text><b>{money(data?.financial_summary.spent || 0)}</b><small>Giao dịch tiền ra</small></div></Card>
        <Card><span className={`${styles.icon} ${styles.purple}`}><HistoryOutlined /></span><div><Text type="secondary">Giao dịch thành công</Text><b>{(data?.financial_summary.successful || 0).toLocaleString('vi-VN')} phát sinh</b><small>Tổng giao dịch thành công</small></div></Card>
      </div>
      <div className={styles.metrics}>
        <Card><span className={`${styles.icon} ${styles.blue}`}><ShoppingCartOutlined /></span><div><Text type="secondary">Tổng đơn hàng</Text><b>{data?.summary.orders.toLocaleString('vi-VN') || 0}</b><small>Trong {days} ngày</small></div></Card>
        <Card><span className={`${styles.icon} ${styles.green}`}><CheckCircleOutlined /></span><div><Text type="secondary">Đã hoàn thành</Text><b>{data?.summary.completed.toLocaleString('vi-VN') || 0}</b><small>{data?.summary.completion_rate || 0}% tỷ lệ hoàn thành</small></div></Card>
        <Card><span className={`${styles.icon} ${styles.orange}`}><DollarOutlined /></span><div><Text type="secondary">Tổng chi tiêu</Text><b>{money(data?.summary.spent || 0)}</b><small>Chi phí đơn hàng</small></div></Card>
        <Card><span className={`${styles.icon} ${styles.purple}`}><TeamOutlined /></span><div><Text type="secondary">Tổng số tương tác đã đặt</Text><b>{data?.summary.quantity.toLocaleString('vi-VN') || 0}</b><small>Không bao gồm đơn đã hủy</small></div></Card>
      </div>

      <div className={styles.charts}>
        <Card className={styles.trendCard} title={<span><AreaChartOutlined /> Xu hướng đơn hàng</span>} extra={<Tag color="blue">{days} ngày</Tag>}>
          {chart.coords.length ? <div className={styles.chartWrap}>
            <svg viewBox={`0 0 ${chart.width} ${chart.height}`} role="img" aria-label="Biểu đồ số đơn hàng theo ngày">
              {[0, .25, .5, .75, 1].map(level => <line key={level} x1="18" x2="742" y1={18 + level * 194} y2={18 + level * 194} className={styles.gridLine} />)}
              <defs><linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stopColor="var(--client-primary)" stopOpacity=".3"/><stop offset="1" stopColor="var(--client-primary)" stopOpacity="0"/></linearGradient></defs>
              <polygon points={`18,212 ${chart.line} 742,212`} fill="url(#chartFill)" />
              <polyline points={chart.line} className={styles.chartLine} />
              {chart.coords.map((point, index) => <circle key={index} cx={point.x} cy={point.y} r="4" className={styles.dot}><title>{new Date(point.date).toLocaleDateString('vi-VN')}: {point.orders} đơn</title></circle>)}
            </svg>
            <div className={styles.axis}><span>{data?.daily[0] ? new Date(data.daily[0].date).toLocaleDateString('vi-VN') : ''}</span><span>Hôm nay</span></div>
          </div> : <Empty description="Chưa có dữ liệu" />}
        </Card>

        <Card className={styles.statusCard} title="Trạng thái đơn hàng">
          <div className={styles.donut} style={{ background: donut }}><div><b>{totalStatuses}</b><span>Tổng đơn</span></div></div>
          <div className={styles.legend}>{statusEntries.map(([status, value]) => <div key={status}><i style={{ background: statusColors[status] || '#94a3b8' }} /><span>{statusLabels[status] || status}</span><b>{value}</b></div>)}</div>
        </Card>
      </div>

      <Card className={styles.services} title="Dịch vụ sử dụng nhiều nhất">
        {data?.top_services.length ? data.top_services.map((service, index) => {
          const max = data.top_services[0]?.orders_count || 1;
          return <div className={styles.serviceRow} key={service.id}>
            <span className={styles.rank}>{index + 1}</span>
            <div className={styles.serviceInfo}><b>{service.name}</b><div><i style={{ width: `${service.orders_count * 100 / max}%` }} /></div></div>
            <span><b>{service.orders_count}</b><small> đơn hàng</small></span>
            <strong>{money(service.spent)}</strong>
          </div>;
        }) : <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có dữ liệu dịch vụ" />}
      </Card>
    </>}
  </div></ClientLayout>;
}
