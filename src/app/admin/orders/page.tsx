'use client';

import { useCallback, useEffect, useState } from 'react';
import type { ReactNode } from 'react';
import { Button, Card, DatePicker, Dropdown, Form, Input, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { AppstoreOutlined, CheckCircleOutlined, ClearOutlined, ClockCircleOutlined, CloseCircleOutlined, CopyOutlined, DatabaseOutlined, DeleteOutlined, DownloadOutlined, EditOutlined, EyeOutlined, GiftOutlined, MoreOutlined, ReloadOutlined, SearchOutlined, SyncOutlined, UnorderedListOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import api from '@/lib/axios';
import styles from './orders.module.css';

const { Text, Title } = Typography;
type Service = { id: number; name: string };
type NamedOption = { id: number; name: string };
type DatePreset = 'today' | 'yesterday' | 'week' | 'month' | 'year' | 'all';
type Order = { id: number; link: string; quantity: number; rate: number; rate_usd?: string; total: number; payment?: number; profit: number; start_count: number; remains: number; status: string; created_at: string; updated_at?: string; start_time?: string; success_time?: string; root_id?: number; response_data?: { order?: number | string }; input_data?: { comments?: string | string[] }; user?: { id: number; username: string; email?: string }; service?: Service };
type Filters = { user_id?: string; email?: string; order_ids?: string; link?: string; api_order_id?: string; provider_id?: number; service_id?: number; platform_id?: number; service_status?: string; source?: string; status?: string; dates?: [dayjs.Dayjs, dayjs.Dayjs] };
import dayjs from 'dayjs';

const statuses = ['Pending', 'Processing', 'In Progress', 'In progress', 'Active', 'Completed', 'Partial', 'Canceled', 'Refunded', 'Partially Refunded', 'Error'];
const statusLabels: Record<string, string> = { Pending: 'Đang chờ', Processing: 'Đang xử lý', 'In Progress': 'Đang thực hiện', 'In progress': 'Đang thực hiện', Active: 'Đang hoạt động', Completed: 'Hoàn thành', Partial: 'Hoàn thành một phần', Canceled: 'Đã hủy', Refunded: 'Đã hoàn tiền', 'Partially Refunded': 'Hoàn tiền một phần', Error: 'Lỗi' };
const statusLabel = (status?: string) => statusLabels[status || ''] || status || 'Không xác định';
const colors: Record<string, string> = { Pending: 'orange', Processing: 'blue', 'In Progress': 'cyan', 'In progress': 'cyan', Active: 'geekblue', Completed: 'green', Partial: 'purple', Canceled: 'red', Refunded: 'volcano', 'Partially Refunded': 'magenta', Error: 'red' };
const money = (value: number) => `${Math.round(Number(value || 0)).toLocaleString('vi-VN')}₫`;
const rateMoney = (value: number) => `${Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 3 })}đ`;

export default function AdminOrdersPage() {
  const [filterForm] = Form.useForm<Filters>(); const [editForm] = Form.useForm();
  const [orders, setOrders] = useState<Order[]>([]); const [services, setServices] = useState<Service[]>([]);
  const [providers, setProviders] = useState<NamedOption[]>([]); const [platforms, setPlatforms] = useState<NamedOption[]>([]);
  const [counts, setCounts] = useState<Record<string, number>>({}); const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 20, total: 0 }); const [selected, setSelected] = useState<React.Key[]>([]);
  const [datePreset, setDatePreset] = useState<DatePreset>('today');
  const [editing, setEditing] = useState<Order | null>(null); const [viewing, setViewing] = useState<Order | null>(null); const [saving, setSaving] = useState(false);

  const load = useCallback(async (page = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const values = filterForm.getFieldsValue();
      const response = await api.get('/admin/orders', { params: { page, per_page: pageSize, user_id: values.user_id, email: values.email, order_ids: values.order_ids, link: values.link, api_order_id: values.api_order_id, provider_id: values.provider_id, service_id: values.service_id, platform_id: values.platform_id, service_status: values.service_status, source: values.source, status: values.status, date_from: values.dates?.[0]?.format('YYYY-MM-DD'), date_to: values.dates?.[1]?.format('YYYY-MM-DD') } });
      const result = response.data.data; setOrders(result.data || []); setCounts(response.data.meta.counts || {}); setServices(response.data.meta.services || []); setProviders(response.data.meta.providers || []); setPlatforms(response.data.meta.platforms || []); setPagination({ current: result.current_page, pageSize: result.per_page, total: result.total });
    } catch { message.error('Không thể tải danh sách đơn hàng.'); } finally { setLoading(false); }
  }, [filterForm]);
  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const openEdit = (order: Order) => { setEditing(order); editForm.setFieldsValue({ status: order.status, start_count: order.start_count, remains: order.remains }); };
  const copyLink = async (link: string) => { await navigator.clipboard.writeText(link); message.success('Đã sao chép link.'); };
  const save = async () => { if (!editing) return; setSaving(true); try { await api.put(`/admin/orders/${editing.id}`, await editForm.validateFields()); message.success('Đã cập nhật đơn hàng.'); setEditing(null); await load(pagination.current, pagination.pageSize); } finally { setSaving(false); } };
  const removeSelected = () => Modal.confirm({ title: `Xóa ${selected.length} đơn hàng?`, okText: 'Xóa', okType: 'danger', cancelText: 'Hủy', onOk: async () => { await Promise.all(selected.map(id => api.delete(`/admin/resources/orders/${String(id)}`))); setSelected([]); message.success('Đã xóa đơn hàng đã chọn.'); await load(); } });
  const removeAll = () => Modal.confirm({ title: 'Xóa tất cả đơn hàng?', content: 'Hành động này không thể hoàn tác.', okText: 'Đồng ý, xóa tất cả', okType: 'danger', cancelText: 'Hủy', onOk: async () => { await api.delete('/admin/orders/all'); setSelected([]); message.success('Đã xóa tất cả đơn hàng.'); await load(); } });
  const refreshStatuses = async () => { setLoading(true); try { const response = await api.post('/admin/orders/refresh-statuses'); message.success(response.data?.message || 'Đã cập nhật trạng thái đơn hàng.'); await load(pagination.current, pagination.pageSize); } catch { message.error('Không thể cập nhật trạng thái đơn hàng.'); setLoading(false); } };
  const cleanOrders = () => { let days = 30; Modal.confirm({ title: 'Dọn dẹp đơn hàng cũ', content: <div><p>Nhập số ngày cần giữ lại. Đơn cũ hơn sẽ bị xóa vĩnh viễn.</p><Input type="number" min={1} defaultValue={30} onChange={event => { days = Math.max(1, Number(event.target.value || 30)); }} /></div>, okText: 'Dọn dẹp', okType: 'danger', cancelText: 'Hủy', onOk: async () => { const response = await api.post('/admin/orders/clean', { days }); message.success(response.data?.message || 'Đã dọn dẹp đơn hàng cũ.'); await load(); } }); };
  const exportFile = (type: 'csv' | 'txt') => {
    const header = ['ID', 'Username', 'Dịch vụ', 'Link', 'Số lượng', 'Tổng tiền', 'Lợi nhuận', 'Trạng thái', 'Ngày tạo'];
    const lines = orders.map(item => [item.id, item.user?.username || '', item.service?.name || '', item.link, item.quantity, item.total, item.profit, statusLabel(item.status), item.created_at]);
    const content = type === 'csv' ? [header, ...lines].map(row => row.map(value => `"${String(value).replaceAll('"', '""')}"`).join(',')).join('\n') : lines.map(row => row.join(' | ')).join('\n');
    const url = URL.createObjectURL(new Blob([type === 'csv' ? '\uFEFF' + content : content], { type: 'text/plain;charset=utf-8' })); const anchor = document.createElement('a'); anchor.href = url; anchor.download = `orders-${dayjs().format('YYYY-MM-DD-HHmm')}.${type}`; anchor.click(); URL.revokeObjectURL(url);
  };
  const statCards: Array<[string, string, ReactNode, boolean]> = [
    ['total', 'Tổng đơn hàng', <UnorderedListOutlined key="all" />, false],
    ['pending', 'Chờ xử lý', <ClockCircleOutlined key="pending" />, false],
    ['completed', 'Hoàn thành', <CheckCircleOutlined key="done" />, false],
    ['partial', 'Hoàn 1 phần', <SyncOutlined key="partial" />, false],
    ['canceled', 'Đã hủy', <CloseCircleOutlined key="cancel" />, false],
    ['revenue', 'Doanh thu', <DatabaseOutlined key="revenue" />, true],
    ['cost', 'Giá vốn', <AppstoreOutlined key="cost" />, true],
    ['profit', 'Lợi nhuận', <GiftOutlined key="profit" />, true],
  ];
  const applyDatePreset = (preset: DatePreset) => {
    const now = dayjs();
    const ranges = {
      today: [now, now],
      yesterday: [now.subtract(1, 'day'), now.subtract(1, 'day')],
      week: [now.startOf('week'), now],
      month: [now.startOf('month'), now],
      year: [now.startOf('year'), now],
    } as const;
    setDatePreset(preset);
    filterForm.setFieldValue('dates', preset === 'all' ? undefined : ranges[preset]);
    window.setTimeout(() => void load(1, pagination.pageSize), 0);
  };
  const columns = [
    { title: 'ID', dataIndex: 'id', width: 65, render: (id: number) => <Text>{id}</Text> },
    { title: 'Thao tác', width: 95, render: (_: unknown, item: Order) => <Dropdown trigger={['click']} menu={{ items: [{ key: 'view', icon: <EyeOutlined />, label: 'Xem', onClick: () => setViewing(item) }, { key: 'copy', icon: <CopyOutlined />, label: 'Copy Link', onClick: () => void copyLink(item.link) }, { type: 'divider' }, { key: 'edit', icon: <EditOutlined />, label: 'Cập nhật', onClick: () => openEdit(item) }] }}><Button type="primary" size="small" icon={<MoreOutlined />} aria-label={`Thao tác đơn hàng #${item.id}`} /></Dropdown> },
    { title: 'Đơn hàng', width: 235, render: (_: unknown, item: Order) => <ul className={styles.infoList}><li><b>Dịch vụ:</b> <Tag color="blue">[{item.service?.id || 'NULL'}]</Tag></li><li><b>Tài khoản:</b> <Tag color="success">{item.user?.username || '—'} [{item.user?.id || 'NULL'}]</Tag></li><li><b>Tạo lúc:</b> <span className={styles.created}>{dayjs(item.created_at).format('DD/MM/YYYY HH:mm')}</span></li><li><b>Cập nhật:</b> <span>{item.updated_at ? dayjs(item.updated_at).format('DD/MM/YYYY HH:mm') : 'N/A'}</span></li></ul> },
    { title: 'Thông tin', width: 435, render: (_: unknown, item: Order) => <ul className={styles.infoList}><li><b>Link:</b> <a className={styles.orderLink} onClick={() => void copyLink(item.link)}>{item.link}</a></li><li><b>Quantity:</b> <Tag color="cyan">{item.quantity?.toLocaleString('vi-VN')}</Tag></li><li>Rate: <span className={styles.rate}>{rateMoney(item.rate)} / {item.rate_usd || '0 USD'}</span></li><li>Charge: <span className={styles.money}>({money(item.total)})</span> <span>- ({money(item.profit)})</span></li><li><b>Start:</b> <Tag>{item.start_count ?? 0}</Tag> | <b>Remains:</b> {item.remains ?? 0}</li><li><b>Time Start:</b> <span>{item.start_time ? dayjs(item.start_time).format('DD/MM/YYYY HH:mm') : 'N/A'}</span></li><li><b>Success Time:</b> <span className={styles.successTime}>{item.success_time ? dayjs(item.success_time).format('DD/MM/YYYY HH:mm') : 'N/A'}</span></li></ul> },
    { title: 'Trạng thái', width: 450, render: (_: unknown, item: Order) => <ul className={styles.infoList}><li><b>Trạng thái:</b> <Tag color={colors[item.status] || 'default'}>{statusLabel(item.status)}</Tag></li><li><b>Dịch vụ:</b> <span className={styles.serviceName}>{item.service?.name || 'Không có'}</span></li><li><b>Đơn nguồn:</b> <Tag color="default">{item.response_data?.order || item.root_id || 'N/A'}</Tag></li></ul> },
  ];
  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý Đơn hàng SMM</Title><Text type="secondary">Theo dõi, cập nhật và xử lý tất cả các đơn hàng của người dùng</Text></div><Button className={styles.reloadButton} icon={<ReloadOutlined />} loading={loading} onClick={() => void load(pagination.current, pagination.pageSize)}>Làm mới dữ liệu</Button></header>
    <section className={styles.stats}>{statCards.map(([key, label, icon, monetary]) => <Card className={`${styles.stat} ${styles[`stat_${key}`] || ''}`} key={key}><span className={styles.statIcon}>{icon}</span><span className={styles.statText}><small>{label}</small><strong>{monetary ? money(counts[key] || 0) : (counts[key] || 0).toLocaleString('vi-VN')}</strong></span></Card>)}</section>
    <Card className={styles.filterCard}>
      <Form form={filterForm} layout="vertical" initialValues={{ dates: [dayjs(), dayjs()] }}>
        <div className={styles.filters}>
          <Form.Item name="user_id" label="ID User"><Input placeholder="ID User" /></Form.Item>
          <Form.Item name="email" label="Email"><Input placeholder="Email" /></Form.Item>
          <Form.Item name="order_ids" label="Mã đơn hàng (nhiều dòng)"><Input.TextArea autoSize={{ minRows: 1, maxRows: 6 }} placeholder="Mã đơn hàng..." /></Form.Item>
          <Form.Item name="link" label="Liên kết"><Input placeholder="Liên kết..." /></Form.Item>
          <Form.Item name="api_order_id" label="Mã đơn hàng API"><Input placeholder="Mã đơn hàng API..." /></Form.Item>
          <Form.Item name="provider_id" label="API Supplier"><Select allowClear placeholder="-- API Supplier --" options={providers.map(item => ({ value: item.id, label: item.name }))} /></Form.Item>
          <Form.Item name="status" label="Trạng thái"><Select allowClear placeholder="Tất cả trạng thái" options={statuses.map(value => ({ value, label: statusLabel(value) }))} /></Form.Item>
          <Form.Item label="Chọn thời gian" className={styles.dateFilter}>
            <div className={styles.datePresets}>
              <Button type={datePreset === 'today' ? 'primary' : 'default'} onClick={() => applyDatePreset('today')}>Hôm nay</Button><Button type={datePreset === 'yesterday' ? 'primary' : 'default'} onClick={() => applyDatePreset('yesterday')}>Hôm qua</Button><Button type={datePreset === 'week' ? 'primary' : 'default'} onClick={() => applyDatePreset('week')}>Tuần này</Button><Button type={datePreset === 'month' ? 'primary' : 'default'} onClick={() => applyDatePreset('month')}>Tháng này</Button><Button type={datePreset === 'year' ? 'primary' : 'default'} onClick={() => applyDatePreset('year')}>Năm này</Button><Button type={datePreset === 'all' ? 'primary' : 'default'} onClick={() => applyDatePreset('all')}>Toàn thời gian</Button>
            </div>
            <Form.Item name="dates" noStyle><DatePicker.RangePicker style={{ width: '100%' }} format="YYYY-MM-DD" /></Form.Item>
          </Form.Item>
          <Form.Item label="Hiển thị"><Select value={pagination.pageSize} onChange={value => void load(1, value)} options={[10, 20, 50, 100].map(value => ({ value, label: `${value} đơn / trang` }))} /></Form.Item>
          <Form.Item name="platform_id" label="Nền tảng dịch vụ"><Select allowClear placeholder="-- Tất cả nền tảng --" options={platforms.map(item => ({ value: item.id, label: item.name }))} /></Form.Item>
          <Form.Item name="service_status" label="Trạng thái dịch vụ"><Select allowClear placeholder="Tất cả" options={[{ value: 'active', label: 'Hoạt động' }, { value: 'inactive', label: 'Tạm dừng' }]} /></Form.Item>
          <Form.Item name="source" label="Nguồn đơn"><Select allowClear placeholder="Tất cả nguồn" options={[{ value: 'api', label: 'Đơn API' }, { value: 'manual', label: 'Đơn thủ công' }]} /></Form.Item>
          <Form.Item name="service_id" label="Dịch vụ"><Select allowClear showSearch optionFilterProp="label" placeholder="-- Gõ hoặc cuộn để tìm dịch vụ --" options={services.map(item => ({ value: item.id, label: `#${item.id} ${item.name}` }))} /></Form.Item>
        </div>
        <div className={styles.filterActions}><Button type="primary" icon={<SearchOutlined />} onClick={() => void load(1, pagination.pageSize)}>Tìm kiếm</Button><Button danger icon={<DeleteOutlined />} aria-label="Xóa bộ lọc" onClick={() => { filterForm.resetFields(); setDatePreset('today'); window.setTimeout(() => void load(1, pagination.pageSize), 0); }} /></div>
      </Form>
    </Card>
    <Card className={styles.tableCard}><div className={styles.listHeader}><div className={styles.toolbarGroup}><Title level={4}>| Danh Sách Đơn Hàng</Title><Space.Compact><Button onClick={() => setSelected(orders.map(item => item.id))}>✓ Chọn tất cả</Button><Button onClick={() => setSelected([])}>⊗ Bỏ chọn</Button></Space.Compact><Dropdown menu={{ items: [{ key: 'csv', label: 'Xuất CSV (.csv)', onClick: () => exportFile('csv') }, { key: 'txt', label: 'Xuất TXT (.txt)', onClick: () => exportFile('txt') }] }}><Button className={styles.exportButton} icon={<DownloadOutlined />}>Xuất file</Button></Dropdown></div><div className={styles.toolbarGroup}><Button className={styles.refreshAll} icon={<SyncOutlined />} onClick={() => void refreshStatuses()}>Cập nhật tất cả</Button><Button className={styles.cleanButton} icon={<ClearOutlined />} onClick={cleanOrders}>Dọn dẹp</Button><Button danger disabled={!selected.length} icon={<DeleteOutlined />} onClick={removeSelected}>Xóa {selected.length}</Button><Button type="primary" danger icon={<DeleteOutlined />} onClick={removeAll}>Xóa tất cả</Button></div></div><Table rowKey="id" rowSelection={{ selectedRowKeys: selected, onChange: setSelected }} columns={columns} dataSource={orders} loading={loading} scroll={{ x: 1340 }} pagination={{ ...pagination, showSizeChanger: true, showTotal: total => `${total} đơn hàng` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} /></Card>
    <Modal className={styles.detailModal} width={760} open={Boolean(viewing)} title={<span>Thông tin đơn hàng <Tag color="blue">#{viewing?.id}</Tag></span>} footer={<Button onClick={() => setViewing(null)}>Đóng</Button>} onCancel={() => setViewing(null)}>
      {viewing && <div className={styles.detailGrid}>
        <label className={styles.fullField}><span>Dịch vụ</span><Input value={viewing.service?.name || 'Không tìm thấy'} readOnly /></label>
        <label className={styles.fullField}><span>Link</span><Input value={viewing.link} readOnly suffix={<Button type="text" size="small" icon={<CopyOutlined />} onClick={() => void copyLink(viewing.link)}>Copy</Button>} /></label>
        <label><span>Quantity</span><Input value={viewing.quantity?.toLocaleString('vi-VN')} readOnly /></label>
        <label><span>Rate</span><Input value={`${rateMoney(viewing.rate)} / ${viewing.rate_usd || '0 USD'}`} readOnly /></label>
        <label className={styles.fullField}><span>Comments</span><Input.TextArea rows={4} value={Array.isArray(viewing.input_data?.comments) ? viewing.input_data.comments.join('\n') : viewing.input_data?.comments || ''} placeholder="Không có comments" readOnly /></label>
        <label><span>Start count</span><Input value={viewing.start_count ?? 0} readOnly /></label>
        <label><span>Remains</span><Input value={viewing.remains ?? 0} readOnly /></label>
        <label><span>Trạng thái</span><div className={styles.readonlyBox}><Tag color={colors[viewing.status] || 'default'}>{statusLabel(viewing.status)}</Tag></div></label>
        <label><span>Thanh toán</span><Input value={money(viewing.total)} readOnly /></label>
      </div>}
    </Modal>
    <Modal open={Boolean(editing)} title={`Cập nhật đơn hàng #${editing?.id}`} okText="Lưu thay đổi" cancelText="Hủy" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(null)}><Form form={editForm} layout="vertical"><Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={statuses.map(value => ({ value, label: statusLabel(value) }))} /></Form.Item><Space style={{ width: '100%' }} align="start"><Form.Item name="start_count" label="Số bắt đầu"><Input type="number" /></Form.Item><Form.Item name="remains" label="Số còn lại"><Input type="number" /></Form.Item></Space></Form></Modal>
  </main>;
}
