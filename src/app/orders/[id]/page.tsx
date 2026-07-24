'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useParams, useRouter } from 'next/navigation';
import { ArrowLeftOutlined, CheckCircleFilled, CustomerServiceOutlined, LinkOutlined, SendOutlined } from '@ant-design/icons';
import { Button, Card, Form, Input, Modal, Result, Spin, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './order-detail.module.css';

const { Text, Title } = Typography;
const { TextArea } = Input;

type Order = {
  id: number;
  link: string;
  quantity: number;
  start_count: number;
  remains: number;
  total: number | string;
  status: string;
  created_at: string;
  service?: {
    name?: string;
    min?: number | string;
    max?: number | string;
    warranty?: number | string;
    average_time?: string;
    description?: string;
    note?: string;
  };
};

type TicketForm = { title: string; description: string };

const statusText: Record<string, string> = {
  Completed: 'Hoàn thành', Processing: 'Đang xử lý', 'In progress': 'Đang chạy',
  Pending: 'Chờ xử lý', Canceled: 'Đã hủy', Partial: 'Hoàn một phần',
};
const statusColor: Record<string, string> = {
  Completed: 'success', Processing: 'purple', 'In progress': 'processing',
  Pending: 'warning', Canceled: 'error', Partial: 'cyan',
};

export default function OrderDetailPage() {
  const params = useParams<{ id: string }>();
  const router = useRouter();
  const [order, setOrder] = useState<Order | null>(null);
  const [currency, setCurrency] = useState('VND');
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);
  const [ticketOpen, setTicketOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [form] = Form.useForm<TicketForm>();

  useEffect(() => {
    const loadOrder = async () => {
      try {
        const response = await api.get(`/client/orders/${params.id}`);
        if (response.data?.status) {
          setOrder(response.data.data);
          setCurrency(response.data.currency || 'VND');
        }
        else setNotFound(true);
      } catch { setNotFound(true); }
      finally { setLoading(false); }
    };
    void loadOrder();
  }, [params.id]);

  const createTicket = async (values: TicketForm) => {
    if (!order) return;
    setSubmitting(true);
    try {
      const response = await api.post('/client/tickets', { ...values, type: 'order', order_id: order.id });
      if (response.data?.status) {
        message.success('Đã tạo ticket hỗ trợ thành công.');
        setTicketOpen(false);
        form.resetFields();
        router.push('/tickets');
      } else message.error(response.data?.message || 'Không thể tạo ticket hỗ trợ.');
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể tạo ticket hỗ trợ.');
    } finally { setSubmitting(false); }
  };

  if (loading) return <ClientLayout><div className={styles.loading}><Spin size="large" /></div></ClientLayout>;
  if (notFound || !order) return <ClientLayout><Result status="404" title="Không tìm thấy đơn hàng" extra={<Button type="primary" onClick={() => router.push('/orders')}>Quay lại danh sách</Button>} /></ClientLayout>;

  const progress = order.quantity > 0 ? Math.max(0, Math.min(100, ((order.quantity - order.remains) / order.quantity) * 100)) : 0;
  const formatMoney = (value: number | string) => currency === 'VND'
    ? `${Math.round(Number(value || 0)).toLocaleString('vi-VN')} đ`
    : Number(value || 0).toLocaleString('en-US', { style: 'currency', currency });

  return <ClientLayout><div className={styles.page}>
    <Link href="/orders" className={styles.back}><ArrowLeftOutlined /> Quay lại danh sách</Link>
    <Title level={2}>Chi tiết đơn hàng #{order.id}</Title>
    <div className={styles.layout}>
      <Card className={styles.mainCard} title={<span className={styles.service}>{order.service?.name || 'Dịch vụ'}</span>} extra={<Tag color={statusColor[order.status]}>{statusText[order.status] || order.status}</Tag>}>
        <div className={styles.infoGrid}>
          <div><Text type="secondary">MÃ ĐƠN HÀNG:</Text><strong>#{order.id}</strong></div>
          <div><Text type="secondary">NGÀY ĐẶT:</Text><strong>{new Date(order.created_at).toLocaleString('vi-VN')}</strong></div>
          <div><Text type="secondary">LOẠI ĐƠN HÀNG:</Text><Tag>Mặc định</Tag></div>
          <div><Text type="secondary">CHI PHÍ:</Text><strong className={styles.cost}>{formatMoney(order.total)}</strong></div>
          <div className={styles.full}><Text type="secondary">LIÊN KẾT:</Text><a href={order.link} target="_blank" rel="noreferrer">{order.link} <LinkOutlined /></a></div>
        </div>
        <div className={styles.progressSection}>
          <h3>Tiến độ đơn hàng</h3>
          <div className={styles.stats}>
            <div><b>{order.quantity?.toLocaleString('vi-VN')}</b><span>Tổng số lượng</span></div>
            <div><b>{order.start_count?.toLocaleString('vi-VN')}</b><span>Số ban đầu</span></div>
            <div><b className={styles.remaining}>{order.remains?.toLocaleString('vi-VN')}</b><span>Số lượng còn lại</span></div>
          </div>
          <div className={styles.progress}><i style={{ width: `${progress}%` }} /><CheckCircleFilled /></div>
        </div>
      </Card>
      <Card className={styles.supportCard} title="Ghi chú & Hỗ trợ">
        <div className={styles.notes}>
          <p>✅ Link ví dụ: Link đúng định dạng của dịch vụ</p>
          <p>🕘 Thời gian bắt đầu: {order.service?.average_time || 'Ngay lập tức'}</p>
          <p>⚖️ Tối thiểu/Tối đa: {order.service?.min || '-'} / {order.service?.max || '-'}</p>
          {order.service?.warranty ? <p>♻️ Bảo hành: {order.service.warranty} ngày</p> : null}
          {order.service?.description ? <p>{order.service.description}</p> : null}
          {order.service?.note ? <p>{order.service.note}</p> : null}
        </div>
        <div className={styles.supportBox}>
          <b>ⓘ Bạn cần hỗ trợ?</b>
          <p>Nếu đơn hàng có vấn đề hoặc sai số liệu, bạn có thể tạo ticket hỗ trợ nội bộ.</p>
          <Button type="primary" block icon={<CustomerServiceOutlined />} onClick={() => setTicketOpen(true)}>Chat hỗ trợ (Ticket)</Button>
        </div>
      </Card>
    </div>
    <Modal
      open={ticketOpen}
      onCancel={() => setTicketOpen(false)}
      footer={null}
      title="Tạo yêu cầu hỗ trợ mới"
      destroyOnHidden
      mask={false}
    >
      <Form form={form} layout="vertical" onFinish={createTicket}>
        <Form.Item label="Đơn hàng gặp vấn đề" required><Input value={`#${order.id} — ${order.service?.name || 'Dịch vụ'}`} readOnly /></Form.Item>
        <Form.Item label="Tiêu đề yêu cầu" name="title" rules={[{ required: true, whitespace: true, message: 'Vui lòng nhập tiêu đề.' }]}><Input maxLength={255} placeholder="VD: Đơn hàng chạy chậm, cần hủy đơn..." /></Form.Item>
        <Form.Item label="Mô tả chi tiết" name="description" rules={[{ required: true, whitespace: true, message: 'Vui lòng mô tả vấn đề.' }]}><TextArea rows={6} maxLength={5000} showCount placeholder="Vui lòng cung cấp chi tiết vấn đề..." /></Form.Item>
        <div className={styles.formActions}><Button onClick={() => setTicketOpen(false)}>Hủy</Button><Button type="primary" htmlType="submit" loading={submitting} icon={<SendOutlined />}>Gửi yêu cầu</Button></div>
      </Form>
    </Modal>
  </div></ClientLayout>;
}
