'use client';

import { useCallback, useEffect, useRef, useState } from 'react';
import { Alert, Button, Card, Empty, Form, Input, Modal, Pagination, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { FacebookFilled, MailOutlined, MessageOutlined, PlusOutlined, SearchOutlined, SendOutlined } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './tickets.module.css';

const { Text, Title } = Typography;
const { TextArea } = Input;
type TicketMessage = { id: number; message: string; type: 'admin' | 'user'; created_at: string };
type Ticket = { id: number; title: string; type: string; order_id?: number; description: string; status: string; created_at: string; updated_at: string; messages?: TicketMessage[] };
type TicketForm = { title: string; type: string; order_id?: number; description: string };
type SupportConfig = { facebook_link?: string; zalo_link?: string; telegram_link?: string; email_link?: string };
const statusColor: Record<string, string> = { pending: 'gold', processing: 'blue', completed: 'green' };
const statusText: Record<string, string> = { pending: 'Chờ xử lý', processing: 'Đang xử lý', completed: 'Đã xử lý' };
const typeText: Record<string, string> = { orther: 'Khác', order: 'Đơn hàng', payment: 'Nạp tiền', refill: 'Bảo hành', cancel: 'Hủy đơn' };

export default function TicketsPage() {
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [createOpen, setCreateOpen] = useState(false);
  const [detailOpen, setDetailOpen] = useState(false);
  const [ticket, setTicket] = useState<Ticket | null>(null);
  const [searchText, setSearchText] = useState('');
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [support, setSupport] = useState<SupportConfig>({});
  const [form] = Form.useForm<TicketForm>();
  const [replyForm] = Form.useForm<{ message: string }>();
  const chatRef = useRef<HTMLDivElement>(null);

  const loadTickets = useCallback(async (page = 1, pageSize = 15) => {
    setLoading(true);
    try {
      const response = await api.get('/client/tickets', { params: { page, per_page: pageSize, search, status: status === 'all' ? undefined : status } });
      if (response.data?.status) {
        const data = response.data.data;
        const rows = Array.isArray(data) ? data : Array.isArray(data?.data) ? data.data : [];
        setTickets(rows);
        setPagination({ current: data?.current_page || 1, pageSize: data?.per_page || 15, total: data?.total ?? rows.length });
      }
    } catch { message.error('Không thể tải danh sách yêu cầu hỗ trợ.'); }
    finally { setLoading(false); }
  }, [search, status]);
  useEffect(() => { void Promise.resolve().then(() => loadTickets()); }, [loadTickets]);
  useEffect(() => {
    api.get('/client/config')
      .then((response) => { if (response.data?.status) setSupport(response.data.data || {}); })
      .catch(() => undefined);
  }, []);
  useEffect(() => { if (detailOpen) chatRef.current?.scrollTo({ top: chatRef.current.scrollHeight }); }, [detailOpen, ticket?.messages]);

  const submitSearch = () => { const value = searchText.trim(); setSearch(value); if (value === search) void loadTickets(1, pagination.pageSize); };
  const createTicket = async (values: TicketForm) => {
    setSubmitting(true);
    try { const response = await api.post('/client/tickets', values); if (response.data?.status) { message.success(response.data.message); setCreateOpen(false); form.resetFields(); await loadTickets(); } }
    catch (error: unknown) { const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể tạo yêu cầu hỗ trợ.'); }
    finally { setSubmitting(false); }
  };
  const openTicket = async (item: Ticket) => {
    setDetailOpen(true); setTicket({ ...item, messages: undefined });
    try { const response = await api.get(`/client/tickets/${item.id}`); if (response.data?.status) setTicket(response.data.data); }
    catch { message.error('Không thể tải nội dung trao đổi.'); }
  };
  const sendReply = async ({ message: content }: { message: string }) => {
    if (!ticket) return;
    setSubmitting(true);
    try { const response = await api.post(`/client/tickets/${ticket.id}/reply`, { message: content }); if (response.data?.status) { replyForm.resetFields(); setTicket({ ...ticket, messages: [...(ticket.messages || []), response.data.data] }); void loadTickets(pagination.current, pagination.pageSize); } }
    catch (error: unknown) { const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể gửi phản hồi.'); }
    finally { setSubmitting(false); }
  };
  const columns = [
    { title: 'ID', dataIndex: 'id', width: 80, render: (value: number, item: Ticket) => <Button type="link" onClick={() => openTicket(item)}>#{value}</Button> },
    { title: 'Mã đơn hàng', dataIndex: 'order_id', width: 140, render: (value?: number) => value ? `#${value}` : '-' },
    { title: 'Tiêu đề', dataIndex: 'title', render: (value: string, item: Ticket) => <button className={styles.titleButton} onClick={() => openTicket(item)}>{value}</button> },
    { title: 'Loại', dataIndex: 'type', width: 110, render: (value: string) => typeText[value] || value },
    { title: 'Trạng thái', dataIndex: 'status', width: 125, render: (value: string) => <Tag color={statusColor[value] || 'default'}>{statusText[value] || value}</Tag> },
    { title: 'Cập nhật', dataIndex: 'updated_at', width: 175, render: (value: string) => new Date(value).toLocaleString('vi-VN') },
  ];

  return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}><div><Title level={3}>Yêu cầu hỗ trợ</Title><Text type="secondary">Trao đổi trực tiếp với đội ngũ hỗ trợ</Text></div><Button type="primary" icon={<PlusOutlined />} onClick={() => setCreateOpen(true)}>Tạo yêu cầu mới</Button></div>
    <Alert className={styles.notice} type="info" showIcon title="Để được hỗ trợ nhanh nhất, hãy ghi rõ mã đơn hàng và mô tả chính xác vấn đề bạn gặp phải." />
    {Object.values(support).some(Boolean) ? <section className={styles.supportPanel} aria-label="Kênh hỗ trợ khách hàng">
      <div className={styles.supportGrid}>
        {support.facebook_link ? <a className={styles.supportItem} href={support.facebook_link} target="_blank" rel="noreferrer"><span className={`${styles.supportIcon} ${styles.facebook}`}><FacebookFilled /></span><span><small>Facebook</small><strong>{support.facebook_link}</strong></span></a> : null}
        {support.telegram_link ? <a className={styles.supportItem} href={support.telegram_link} target="_blank" rel="noreferrer"><span className={`${styles.supportIcon} ${styles.telegram}`}><SendOutlined /></span><span><small>Telegram</small><strong>{support.telegram_link}</strong></span></a> : null}
        {support.zalo_link ? <a className={styles.supportItem} href={support.zalo_link} target="_blank" rel="noreferrer"><span className={`${styles.supportIcon} ${styles.zalo}`}>Zalo</span><span><small>Zalo</small><strong>{support.zalo_link}</strong></span></a> : null}
        {support.email_link ? <a className={styles.supportItem} href={support.email_link.startsWith('mailto:') ? support.email_link : `mailto:${support.email_link}`}><span className={`${styles.supportIcon} ${styles.email}`}><MailOutlined /></span><span><small>Email liên hệ</small><strong>{support.email_link.replace(/^mailto:/, '')}</strong></span></a> : null}
      </div>
    </section> : null}
    <Card className={styles.card}>
      <div className={styles.filters}><Select value={status} onChange={setStatus} options={[{ value: 'all', label: 'Tất cả trạng thái' }, { value: 'pending', label: 'Chờ xử lý' }, { value: 'processing', label: 'Đang xử lý' }, { value: 'completed', label: 'Đã xử lý' }]} /><Space.Compact className={styles.search}><Input allowClear prefix={<SearchOutlined />} placeholder="Nhập ID, mã đơn hoặc tiêu đề..." value={searchText} onChange={(event) => setSearchText(event.target.value)} onPressEnter={submitSearch} /><Button type="primary" icon={<SearchOutlined />} onClick={submitSearch}>Tìm kiếm</Button></Space.Compact></div>
      <Table rowKey="id" columns={columns} dataSource={Array.isArray(tickets) ? tickets : []} loading={loading} pagination={false} scroll={{ x: 900 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có yêu cầu hỗ trợ nào" /> }} />
      <div className={styles.pagination}><Pagination {...pagination} showTotal={(total) => `${total} yêu cầu`} showSizeChanger pageSizeOptions={[10, 15, 20, 50]} locale={{ items_per_page: '/ trang' }} onChange={loadTickets} /></div>
    </Card>

    <Modal open={createOpen} onCancel={() => setCreateOpen(false)} footer={null} title="Tạo yêu cầu hỗ trợ" destroyOnHidden styles={{ mask: { background: 'rgba(15, 23, 42, 0.10)' } }}>
      <Form form={form} layout="vertical" initialValues={{ type: 'orther' }} onFinish={createTicket}>
        <Form.Item label="Tiêu đề" name="title" rules={[{ required: true, message: 'Nhập tiêu đề' }]}><Input placeholder="Nhập ngắn gọn vấn đề cần hỗ trợ" /></Form.Item>
        <Form.Item label="Loại hỗ trợ" name="type" rules={[{ required: true }]}><Select options={[{ value: 'orther', label: 'Khác' }, { value: 'order', label: 'Đơn hàng' }, { value: 'payment', label: 'Nạp tiền' }, { value: 'refill', label: 'Bảo hành' }, { value: 'cancel', label: 'Hủy đơn' }]} /></Form.Item>
        <Form.Item label="Mã đơn hàng" name="order_id"><Input inputMode="numeric" placeholder="Không bắt buộc" /></Form.Item>
        <Form.Item label="Nội dung" name="description" rules={[{ required: true, message: 'Nhập nội dung cần hỗ trợ' }]}><TextArea rows={5} maxLength={5000} showCount placeholder="Mô tả chi tiết vấn đề của bạn..." /></Form.Item>
        <Button type="primary" htmlType="submit" block size="large" loading={submitting}>Gửi yêu cầu</Button>
      </Form>
    </Modal>

    <Modal className={styles.chatModal} open={detailOpen} onCancel={() => { setDetailOpen(false); setTicket(null); replyForm.resetFields(); }} footer={null} width={720} title={ticket ? `Yêu cầu #${ticket.id} · ${ticket.title}` : 'Chi tiết yêu cầu'} styles={{ mask: { background: 'rgba(15, 23, 42, 0.10)' } }}>
      {ticket ? <><div className={styles.ticketMeta}><Tag color={statusColor[ticket.status] || 'default'}>{statusText[ticket.status] || ticket.status}</Tag>{ticket.order_id ? <Text>Mã đơn: <b>#{ticket.order_id}</b></Text> : null}<Text type="secondary">{typeText[ticket.type] || ticket.type}</Text></div>
      <div className={styles.chat} ref={chatRef}>{ticket.messages ? ticket.messages.map((item) => <div key={item.id} className={`${styles.message} ${item.type === 'admin' ? styles.admin : styles.user}`}><div className={styles.sender}><b>{item.type === 'admin' ? 'Hỗ trợ viên' : 'Bạn'}</b><small>{new Date(item.created_at).toLocaleString('vi-VN')}</small></div><div className={styles.bubble}>{item.message}</div></div>) : <div className={styles.chatLoading}>Đang tải nội dung...</div>}</div>
      {ticket.status !== 'completed' ? <Form form={replyForm} className={styles.reply} onFinish={sendReply}><Form.Item name="message" rules={[{ required: true, message: 'Nhập nội dung phản hồi' }]}><TextArea autoSize={{ minRows: 1, maxRows: 4 }} placeholder="Viết phản hồi..." /></Form.Item><Button type="primary" htmlType="submit" icon={<SendOutlined />} loading={submitting}>Gửi</Button></Form> : <Alert type="success" showIcon icon={<MessageOutlined />} title="Yêu cầu này đã được xử lý và đóng." />}</> : null}
    </Modal>
  </div></ClientLayout>;
}
