'use client';

import { useEffect, useState } from 'react';
import { Button, Card, DatePicker, Input, Modal, Progress, Spin, Table, Tag, Tooltip, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { BarChartOutlined, CalendarOutlined, CarOutlined, CloseOutlined, CreditCardOutlined, DollarOutlined, DownloadOutlined, FieldTimeOutlined, PieChartOutlined, ReloadOutlined, RiseOutlined, RobotOutlined, SendOutlined, ShoppingCartOutlined, TeamOutlined, TrophyOutlined, UserAddOutlined, WalletOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './admin.module.css';

const { Text, Title } = Typography;
type Period = { users: number; orders: number; revenue: number; profit: number; deposits?: number; pending_orders?: number };
type Daily = { date: string; revenue: number; profit: number; orders: number; new_users: number };
type Order = { id: number; quantity: number; total: number; status: string; user?: { username: string }; service?: { name: string } };
type Deposit = { id: number; real_amount: number; method: string; status: string; created_at: string; user?: { username: string } };
type Dashboard = { summary: { deposits: number; balance: number; deposits_today: number; deposits_month: number; order_payments: number; orders_pending: number; tickets_pending: number; websites_pending: number }; periods: Record<string, Period>; daily: Daily[]; revenue_table: Daily[]; payment_methods: Array<{ method?: string; amount: number }>; top_services: Array<{ service_id: number; name: string; revenue: number; orders_count: number; percent: number }>; order_statuses: Record<string, number>; recent_orders: Order[]; recent_deposits: Deposit[]; leaderboard: Array<{ user_id: number; username: string; email: string; spending: number; orders_count: number }>; providers: Array<{ provider_id: number; name: string; revenue: number; cost: number; profit: number; orders_count: number; margin: number }> };
type AnalysisPeriod = 'today' | 'yesterday' | 'week' | 'month' | 'year' | 'custom' | 'all';
const money = (value: number) => `${Math.round(Number(value || 0)).toLocaleString('vi-VN')}₫`;
const palette = ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ec4899'];

function RevenueChart({ rows }: { rows: Daily[] }) {
  const max = Math.max(1, ...rows.flatMap(row => [row.revenue, row.profit]));
  const left = 72; const right = 750; const top = 18; const bottom = 220;
  const x = (index: number) => left + (index / Math.max(1, rows.length - 1)) * (right - left);
  const y = (value: number) => bottom - (value / max) * (bottom - top);
  const points = (key: 'revenue' | 'profit') => rows.map((row, index) => `${x(index)},${y(row[key])}`).join(' ');
  const compactMoney = (value: number) => value >= 1_000_000 ? `${(value / 1_000_000).toFixed(1)}tr` : value >= 1_000 ? `${Math.round(value / 1_000)}k` : `${Math.round(value)}`;
  const ticks = [0, .25, .5, .75, 1];
  const labelIndexes = rows.length ? Array.from(new Set([0, Math.floor((rows.length - 1) / 4), Math.floor((rows.length - 1) / 2), Math.floor((rows.length - 1) * .75), rows.length - 1])) : [];
  return <div className={styles.chart}><svg viewBox="0 0 780 260" role="img" aria-label="Biểu đồ doanh thu và lợi nhuận"><defs><linearGradient id="revenueFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stopColor="#6366f1" stopOpacity=".25"/><stop offset="1" stopColor="#6366f1" stopOpacity="0"/></linearGradient></defs>{ticks.map(tick => { const tickY = y(max * tick); return <g key={tick}><line x1={left} x2={right} y1={tickY} y2={tickY} stroke="#e7ebf1"/><text x={left - 10} y={tickY + 4} textAnchor="end" className={styles.axisText}>{compactMoney(max * tick)}</text></g>; })}<polygon points={`${left},${bottom} ${points('revenue')} ${right},${bottom}`} fill="url(#revenueFill)"/><polyline points={points('revenue')} fill="none" stroke="#6366f1" strokeWidth="3.5" strokeLinejoin="round"/><polyline points={points('profit')} fill="none" stroke="#10b981" strokeWidth="3" strokeLinejoin="round"/>{rows.map((row, index) => <g key={`${row.date}-${index}`}><circle cx={x(index)} cy={y(row.revenue)} r="4" fill="#6366f1"><title>{`${row.date} — Doanh thu: ${money(row.revenue)}`}</title></circle><circle cx={x(index)} cy={y(row.profit)} r="3.5" fill="#10b981"><title>{`${row.date} — Lợi nhuận: ${money(row.profit)}`}</title></circle></g>)}{labelIndexes.map(index => <text key={index} x={x(index)} y="248" textAnchor="middle" className={styles.axisText}>{rows[index]?.date}</text>)}</svg><div className={styles.legend}><span><i className={styles.legendRevenue}/>Doanh thu</span><span><i className={styles.legendProfit}/>Lợi nhuận</span></div></div>;
}

export default function AdminDashboard() {
  const [data, setData] = useState<Dashboard | null>(null); const [loading, setLoading] = useState(true);
  const [analysisPeriod, setAnalysisPeriod] = useState<AnalysisPeriod>('month');
  const [customRange, setCustomRange] = useState<[string, string] | null>(null);
  const [refreshing, setRefreshing] = useState(false);
  const [popup, setPopup] = useState<'providers' | 'services' | 'leaderboard' | 'ai' | null>(null);
  const [chatInput, setChatInput] = useState(''); const [chatLoading, setChatLoading] = useState(false);
  const [chat, setChat] = useState<Array<{ role: 'user' | 'assistant'; message: string }>>([{ role: 'assistant', message: 'Xin chào! Bạn có thể hỏi tôi về thành viên, đơn hàng hoặc doanh thu.' }]);
  useEffect(() => {
    const params = { period: analysisPeriod, ...(customRange ? { start_date: customRange[0], end_date: customRange[1] } : {}) };
    api.get('/admin/dashboard', { params }).then(response => setData(response.data.data)).catch(() => message.error('Không thể tải dashboard quản trị.')).finally(() => setLoading(false));
  }, [analysisPeriod, customRange]);
  const refreshDashboard = async () => {
    setRefreshing(true);
    try {
      const params = { period: analysisPeriod, ...(customRange ? { start_date: customRange[0], end_date: customRange[1] } : {}) };
      const response = await api.get('/admin/dashboard', { params });
      setData(response.data.data);
      message.success('Đã cập nhật dữ liệu.');
    } catch {
      message.error('Không thể cập nhật dữ liệu.');
    } finally {
      setRefreshing(false);
    }
  };
  const payments = data?.payment_methods || []; const paymentTotal = payments.reduce((sum, item) => sum + Number(item.amount), 0);
  const donutParts = payments.map((item, index) => {
    const startAmount = payments.slice(0, index).reduce((sum, entry) => sum + Number(entry.amount), 0);
    const start = paymentTotal ? startAmount / paymentTotal * 100 : 0;
    const end = paymentTotal ? (startAmount + Number(item.amount)) / paymentTotal * 100 : 0;
    return `${palette[index % palette.length]} ${start}% ${end}%`;
  });
  const donut = `conic-gradient(${donutParts.length ? donutParts.join(',') : '#eef1f5 0 100%'})`;
  if (loading) return <div className={styles.loading}><Spin size="large" /></div>;
  const monthLabel = `Tháng ${String(new Date().getMonth() + 1).padStart(2, '0')}`;
  const all = data?.periods?.all || { users: 0, orders: 0, revenue: 0, profit: 0 };
  const month = data?.periods?.month || { users: 0, orders: 0, revenue: 0, profit: 0 };
  const week = data?.periods?.week || { users: 0, orders: 0, revenue: 0, profit: 0 };
  const today = data?.periods?.today || { users: 0, orders: 0, revenue: 0, profit: 0 };
  const summary = data?.summary || { deposits: 0, balance: 0, deposits_today: 0, deposits_month: 0, order_payments: 0, orders_pending: 0, tickets_pending: 0, websites_pending: 0 };
  const selectedAnalysis = data?.periods?.[analysisPeriod] || { users: 0, orders: 0, revenue: 0, profit: 0 };
  const selectedAnalysisLabel: Record<AnalysisPeriod, string> = { today: 'Hôm nay', yesterday: 'Hôm qua', week: 'Tuần này', month: 'Tháng này', year: 'Năm nay', custom: customRange ? `${customRange[0]} - ${customRange[1]}` : 'Tùy chọn', all: 'Toàn thời gian' };
  const activeLabel = selectedAnalysisLabel[analysisPeriod];
  const statGroups = [[
    { label: 'Thành viên đăng ký', value: selectedAnalysis.users, tag: activeLabel, icon: <TeamOutlined /> },
    { label: `Đăng ký ${activeLabel.toLowerCase()}`, value: selectedAnalysis.users, tag: activeLabel, icon: <UserAddOutlined /> },
    { label: 'Tổng số dư thành viên', value: money(summary.balance), tag: 'Hiện tại', icon: <WalletOutlined /> },
    { label: 'Tổng tiền nạp', value: money(selectedAnalysis.deposits || 0), tag: activeLabel, icon: <DollarOutlined /> },
    { label: `Nạp ${activeLabel.toLowerCase()}`, value: money(selectedAnalysis.deposits || 0), tag: activeLabel, icon: <DollarOutlined /> },
    { label: `Tiền nạp ${activeLabel.toLowerCase()}`, value: money(selectedAnalysis.deposits || 0), tag: activeLabel, icon: <CalendarOutlined /> },
    { label: 'Tổng đơn hàng', value: selectedAnalysis.orders, tag: activeLabel, icon: <ShoppingCartOutlined /> },
    { label: 'Tổng thanh toán đơn', value: money(selectedAnalysis.revenue), tag: activeLabel, icon: <CreditCardOutlined /> },
  ], [
    { label: `Đơn hàng ${activeLabel.toLowerCase()}`, value: selectedAnalysis.orders, tag: activeLabel, icon: <RiseOutlined /> },
    { label: `Đơn trong ${activeLabel.toLowerCase()}`, value: selectedAnalysis.orders, tag: activeLabel, icon: <CalendarOutlined /> },
    { label: `Đơn phát sinh ${activeLabel.toLowerCase()}`, value: selectedAnalysis.orders, tag: activeLabel, icon: <ShoppingCartOutlined /> },
    { label: 'Đơn đang chờ xử lý', value: selectedAnalysis.pending_orders || 0, tag: activeLabel, icon: <FieldTimeOutlined /> },
    { label: 'Tổng doanh thu', value: money(selectedAnalysis.revenue), tag: activeLabel, icon: <DollarOutlined /> },
    { label: 'Tổng lợi nhuận', value: money(selectedAnalysis.profit), tag: activeLabel, icon: <WalletOutlined /> },
    { label: `Doanh thu ${activeLabel.toLowerCase()}`, value: money(selectedAnalysis.revenue), tag: activeLabel, icon: <PieChartOutlined /> },
    { label: `Lợi nhuận ${activeLabel.toLowerCase()}`, value: money(selectedAnalysis.profit), tag: activeLabel, icon: <DollarOutlined /> },
  ]];
  const exportReport = () => { const rows = [['Ngày','Doanh thu','Lợi nhuận','Đơn hàng','Khách hàng mới'], ...(data?.daily || []).map(row => [row.date,row.revenue,row.profit,row.orders,row.new_users])]; const url = URL.createObjectURL(new Blob(['\uFEFF' + rows.map(row => row.join(',')).join('\n')], { type: 'text/csv' })); const a = document.createElement('a'); a.href = url; a.download = 'bao-cao-doanh-thu.csv'; a.click(); URL.revokeObjectURL(url); };
  const sendChat = async () => { const value = chatInput.trim(); if (!value || chatLoading) return; setChat(current => [...current, { role: 'user', message: value }]); setChatInput(''); setChatLoading(true); try { const response = await api.post('/admin/ai-chat', { message: value }); setChat(current => [...current, { role: 'assistant', message: response.data.data.message }]); } catch { message.error('Không thể gửi câu hỏi.'); } finally { setChatLoading(false); } };
  const reportModalFooter = <div className={styles.reportModalFooter}><Button icon={<CloseOutlined />} onClick={() => setPopup(null)}>Đóng</Button><Button type="primary" icon={<ReloadOutlined />} loading={refreshing} onClick={() => void refreshDashboard()}>Làm mới</Button></div>;
  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý doanh thu</Title><Text type="secondary">Trang chủ / Quản lý doanh thu</Text></div><Button icon={<DownloadOutlined />} onClick={exportReport}>Xuất báo cáo</Button></header>
    <section className={styles.analysisFilter}>
      <div className={styles.analysisTitle}><BarChartOutlined /> PHÂN TÍCH TÀI CHÍNH SMM</div>
      <div className={styles.periodTabs}>
        {([
          ['today', 'Hôm nay'],
          ['yesterday', 'Hôm qua'],
          ['week', 'Tuần'],
          ['month', 'Tháng'],
          ['year', 'Năm'],
          ['custom', 'Tùy chọn'],
          ['all', 'Toàn thời gian'],
        ] as Array<[AnalysisPeriod, string]>).map(([value, label]) => <button type="button" key={value} className={analysisPeriod === value ? styles.periodActive : ''} onClick={() => setAnalysisPeriod(value)}>{label}</button>)}
      </div>
      {analysisPeriod === 'custom' && <div className={styles.customRange}><DatePicker.RangePicker format="DD/MM/YYYY" placeholder={['Từ ngày', 'Đến ngày']} onChange={dates => {
        if (dates?.[0] && dates?.[1]) setCustomRange([dates[0].format('YYYY-MM-DD'), dates[1].format('YYYY-MM-DD')]);
        else setCustomRange(null);
      }} /></div>}
    </section>
    {statGroups.map((group, groupIndex) => <section className={styles.summaryGrid} key={groupIndex}>{group.map((item, index) => { const tone = index % 4; return <Card className={`${styles.summaryCard} ${styles[`tone${tone}`]}`} key={item.label}><span className={styles.summaryIcon}>{item.icon}</span><div className={styles.summaryContent}><span>{item.label}</span><strong>{typeof item.value === 'number' ? item.value.toLocaleString('vi-VN') : item.value}</strong><small>{item.tag}</small></div></Card>; })}</section>)}
    <section className={styles.chartGrid}><Card className={styles.panel} title="Biểu đồ doanh thu"><RevenueChart rows={data?.daily || []} /></Card><Card className={styles.panel} title="Doanh thu theo phương thức thanh toán"><div className={styles.paymentBody}><div className={styles.donut} style={{ background: donut }}><div><b>{money(paymentTotal)}</b><small>Tổng</small></div></div><div className={styles.paymentList}>{payments.map((item, index) => <div key={`${item.method}-${index}`}><span><i style={{ background: palette[index % palette.length] }}/>{item.method || 'Khác'}</span><span><b>{paymentTotal ? (Number(item.amount) / paymentTotal * 100).toFixed(1) : 0}%</b><small>{money(item.amount)}</small></span></div>)}</div></div></Card></section>
    <section className={styles.chartGrid}><Card className={styles.panel} title="Bảng thống kê doanh thu"><Table rowKey="date" size="small" pagination={false} dataSource={data?.revenue_table || []} columns={[{ title: 'Ngày', dataIndex: 'date' },{ title: 'Doanh thu', dataIndex: 'revenue', render: (value: number) => <b>{money(value)}</b> },{ title: 'Lợi nhuận', dataIndex: 'profit', render: (value: number) => <b>{money(value)}</b> },{ title: 'Đơn hàng', dataIndex: 'orders', align: 'center' as const },{ title: 'Khách hàng mới', dataIndex: 'new_users', align: 'center' as const }]} /></Card><Card className={styles.panel} title="Top dịch vụ doanh thu cao"><div className={styles.ranking}>{(data?.top_services || []).map((item, index) => <div className={styles.rank} key={item.service_id}><span className={styles.rankNo}>{index + 1}</span><div><div className={styles.rankTop}><strong>{item.name}</strong><span>{money(item.revenue)} <small>{item.percent}%</small></span></div><Progress percent={item.percent} showInfo={false} strokeColor={palette[index % palette.length]} /></div></div>)}</div></Card></section>
    <section className={styles.bottomGrid}><Card className={styles.panel} title="Đơn hàng mới nhất"><Table rowKey="id" size="small" pagination={false} dataSource={data?.recent_orders || []} columns={[{ title: 'ID', dataIndex: 'id', render: (id: number) => <b>#{id}</b> },{ title: 'Khách hàng', render: (_: unknown, row: Order) => row.user?.username || '-' },{ title: 'Dịch vụ', render: (_: unknown, row: Order) => row.service?.name || '-' },{ title: 'Tổng tiền', dataIndex: 'total', render: money },{ title: 'Trạng thái', dataIndex: 'status', render: (status: string) => <Tag color={status === 'Completed' ? 'green' : status === 'Canceled' ? 'red' : 'blue'}>{status}</Tag> }]} scroll={{ x: 650 }} /></Card><Card className={styles.panel} title="Nạp tiền gần đây"><Table rowKey="id" size="small" pagination={false} dataSource={data?.recent_deposits || []} columns={[{ title: 'ID', dataIndex: 'id', render: (id: number) => `#${id}` },{ title: 'Khách hàng', render: (_: unknown, row: Deposit) => row.user?.username || '-' },{ title: 'Phương thức', dataIndex: 'method' },{ title: 'Số tiền', dataIndex: 'real_amount', render: money },{ title: 'Trạng thái', dataIndex: 'status', render: (status: string) => <Tag>{status}</Tag> }]} scroll={{ x: 580 }} /></Card></section>
    <section className={styles.bottomGrid}><Card className={styles.panel} title="Top khách hàng chi tiêu hôm nay"><Table rowKey="user_id" size="small" pagination={false} dataSource={(data?.leaderboard || []).slice(0, 10)} columns={[{ title: 'Hạng', render: (_: unknown, __: unknown, index: number) => <b>#{index + 1}</b> },{ title: 'Khách hàng', dataIndex: 'username' },{ title: 'Email', dataIndex: 'email' },{ title: 'Chi tiêu', dataIndex: 'spending', render: money },{ title: 'Số đơn', dataIndex: 'orders_count' }]} scroll={{ x: 620 }} /></Card><Card className={styles.panel} title="Thống kê nhà cung cấp"><Table rowKey={row => `${row.provider_id}-${row.name}`} size="small" pagination={false} dataSource={(data?.providers || []).slice(0, 10)} columns={[{ title: 'Nhà cung cấp', dataIndex: 'name' },{ title: 'Doanh thu', dataIndex: 'revenue', render: money },{ title: 'Chi phí', dataIndex: 'cost', render: money },{ title: 'Lợi nhuận', dataIndex: 'profit', render: money },{ title: 'Tỷ lệ', dataIndex: 'margin', render: (value: number) => <Tag color={value >= 0 ? 'green' : 'red'}>{value}%</Tag> }]} scroll={{ x: 650 }} /></Card></section>
    <div className={styles.floatingTools}><Tooltip title="Thống kê nhà cung cấp" placement="left"><Button className={styles.toolYellow} shape="circle" icon={<CarOutlined />} onClick={() => setPopup('providers')} /></Tooltip><Tooltip title="Top dịch vụ bán chạy" placement="left"><Button className={styles.toolGreen} shape="circle" icon={<BarChartOutlined />} onClick={() => setPopup('services')} /></Tooltip><Tooltip title="Bảng xếp hạng khách hàng" placement="left"><Button className={styles.toolBlue} shape="circle" icon={<TrophyOutlined />} onClick={() => setPopup('leaderboard')} /></Tooltip><Tooltip title="Trợ lý AI" placement="left"><Button className={styles.toolBlue} shape="circle" icon={<RobotOutlined />} onClick={() => setPopup('ai')} /></Tooltip></div>
    <Modal width={980} open={popup === 'providers'} title="Thống kê nhà cung cấp" footer={reportModalFooter} onCancel={() => setPopup(null)}><Table rowKey={row => `${row.provider_id}-${row.name}`} dataSource={data?.providers || []} pagination={{ pageSize: 10 }} columns={[{ title: 'Hạng', render: (_: unknown, __: unknown, index: number) => index + 1 },{ title: 'Nhà cung cấp', dataIndex: 'name' },{ title: 'Doanh thu', dataIndex: 'revenue', render: money },{ title: 'Chi phí', dataIndex: 'cost', render: money },{ title: 'Lợi nhuận', dataIndex: 'profit', render: money },{ title: 'Số đơn', dataIndex: 'orders_count' },{ title: 'Tỷ lệ LN', dataIndex: 'margin', render: (value: number) => `${value}%` }]} scroll={{ x: 760 }} /></Modal>
    <Modal width={900} open={popup === 'services'} title="Top dịch vụ bán chạy nhất" footer={reportModalFooter} onCancel={() => setPopup(null)}><Table rowKey="service_id" dataSource={data?.top_services || []} pagination={false} columns={[{ title: 'Hạng', render: (_: unknown, __: unknown, index: number) => index + 1 },{ title: 'Dịch vụ', dataIndex: 'name' },{ title: 'Doanh thu', dataIndex: 'revenue', render: money },{ title: 'Số đơn', dataIndex: 'orders_count' },{ title: 'Tỷ trọng', dataIndex: 'percent', render: (value: number) => `${value}%` }]} /></Modal>
    <Modal width={900} open={popup === 'leaderboard'} title="Bảng xếp hạng khách hàng hôm nay" footer={reportModalFooter} onCancel={() => setPopup(null)}><Table rowKey="user_id" dataSource={data?.leaderboard || []} pagination={{ pageSize: 10 }} columns={[{ title: 'Hạng', render: (_: unknown, __: unknown, index: number) => index + 1 },{ title: 'ID', dataIndex: 'user_id' },{ title: 'Tên đăng nhập', dataIndex: 'username' },{ title: 'Email', dataIndex: 'email' },{ title: 'Tổng chi tiêu', dataIndex: 'spending', render: money },{ title: 'Số đơn', dataIndex: 'orders_count' }]} scroll={{ x: 700 }} /></Modal>
    <Modal open={popup === 'ai'} title="AI Assistant" footer={null} onCancel={() => setPopup(null)}><div className={styles.chatBox}>{chat.map((item, index) => item.role === 'user' ? <div className={`${styles.chatMessage} ${styles.chatUser}`} key={index}>{item.message}</div> : <div className={`${styles.chatMessage} ${styles.chatAssistant}`} key={index} dangerouslySetInnerHTML={{ __html: item.message }} />)}</div><div className={styles.chatInput}><Input value={chatInput} placeholder="Hỏi về doanh thu, đơn hàng..." onChange={event => setChatInput(event.target.value)} onPressEnter={() => void sendChat()} /><Button type="primary" loading={chatLoading} icon={<SendOutlined />} onClick={() => void sendChat()} /></div></Modal>
  </main>;
}
