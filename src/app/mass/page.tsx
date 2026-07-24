'use client';

import { useMemo, useState } from 'react';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import { BulbOutlined, CheckCircleOutlined, CloseCircleOutlined, RocketOutlined } from '@ant-design/icons';
import { Button, Input, Progress } from 'antd';import { message } from '@/lib/antd-message';
import styles from './mass.module.css';

const { TextArea } = Input;

type ParsedOrder = { line: number; service_id: number; link: string; quantity: number; comments?: string };
type OrderResult = ParsedOrder & { success: boolean; message: string; order_id?: number; payment?: number };

function parseOrders(value: string) {
  const orders: ParsedOrder[] = [];
  const errors: string[] = [];
  value.split(/\r?\n/).forEach((raw, index) => {
    if (!raw.trim()) return;
    const parts = raw.split('|').map((part) => part.trim());
    const serviceId = Number(parts[0]);
    const link = parts[1] || '';
    const quantity = parts[2] ? Number(parts[2]) : 1;
    if (!Number.isInteger(serviceId) || serviceId <= 0 || !link || !Number.isInteger(quantity) || quantity <= 0) {
      errors.push(`Dòng ${index + 1}: sai định dạng service_id | link | quantity`);
      return;
    }
    orders.push({ line: index + 1, service_id: serviceId, link, quantity, comments: parts.slice(3).join('|') || undefined });
  });
  return { orders, errors };
}

export default function MassOrderPage() {
  const [rawOrders, setRawOrders] = useState('');
  const [note, setNote] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [results, setResults] = useState<OrderResult[]>([]);
  const [totalToProcess, setTotalToProcess] = useState(0);
  const parsed = useMemo(() => parseOrders(rawOrders), [rawOrders]);
  const successful = results.filter((item) => item.success).length;
  const failed = results.filter((item) => !item.success).length;
  const progress = totalToProcess ? Math.round((results.length / totalToProcess) * 100) : 0;

  const submit = async () => {
    if (!parsed.orders.length || parsed.errors.length) {
      message.error(parsed.errors[0] || 'Vui lòng nhập ít nhất một đơn hàng.');
      return;
    }
    if (parsed.orders.length > 100) {
      message.error('Mỗi lần chỉ được xử lý tối đa 100 đơn hàng.');
      return;
    }
    setSubmitting(true);
    setResults([]);
    setTotalToProcess(parsed.orders.length);
    try {
      const completed: OrderResult[] = [];
      for (const order of parsed.orders) {
        try {
          const response = await api.post('/client/orders', {
            service_id: order.service_id,
            link: order.link,
            quantity: order.quantity,
            comments: order.comments ? order.comments.replaceAll('/', '\n') : undefined,
            note: note || undefined,
          });
          const body = response.data;
          completed.push({ ...order, success: Boolean(body.success), message: body.message || 'Đã xử lý đơn hàng.', order_id: body.data?.order_id, payment: body.data?.payment });
        } catch (error: any) {
          completed.push({ ...order, success: false, message: error.response?.data?.message || error.message || 'Không thể xử lý đơn hàng.' });
        }
        setResults([...completed]);
      }
      const count = completed.filter((item) => item.success).length;
      if (count) message.success(`Đã đặt thành công ${count}/${completed.length} đơn hàng.`);
      else message.error('Không có đơn hàng nào được đặt thành công.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <ClientLayout>
      <div className={styles.layout}>
        <section className={styles.mainCard}>
          <h1><RocketOutlined /> Đặt đơn hàng loạt</h1>
          <label>Danh sách đơn hàng:</label>
          <TextArea value={rawOrders} onChange={(event) => setRawOrders(event.target.value)} rows={13} className={styles.orderInput} placeholder={'123 | https://www.facebook.com/post1 | 100\n124 | https://www.facebook.com/page-package\n125 | https://www.facebook.com/post2 | 500 | Hay quá/Like nhé/Đỉnh'} />
          <div className={styles.inputMeta}><span>Số dòng: {parsed.orders.length}</span><button onClick={() => { setRawOrders(''); setResults([]); }}>Xóa hết</button></div>
          {parsed.errors.length ? <div className={styles.parseErrors}>{parsed.errors.map((error) => <div key={error}>{error}</div>)}</div> : null}
          <label>Ghi chú chung (tùy chọn):</label>
          <Input value={note} onChange={(event) => setNote(event.target.value)} placeholder="Ghi chú gắn vào tất cả đơn" />
          <Button type="primary" size="large" block icon={<RocketOutlined />} loading={submitting} onClick={submit} className={styles.submit}>Bắt đầu đặt</Button>

          <div className={styles.progressSection}>
            <h2>〽 Tiến trình</h2>
            <Progress percent={progress} showInfo={false} status={failed ? 'exception' : successful ? 'success' : 'active'} />
            <div className={styles.counters}><span>Đã xử lý: {results.length}</span><span className={styles.success}>Thành công: {successful}</span><span className={styles.failed}>Thất bại: {failed}</span></div>
            <div className={styles.logs}>
              {!results.length ? 'Log sẽ hiển thị tại đây sau khi bắt đầu đặt đơn...' : results.map((result) => (
                <div key={`${result.line}-${result.service_id}`} className={result.success ? styles.logSuccess : styles.logFailed}>
                  {result.success ? <CheckCircleOutlined /> : <CloseCircleOutlined />}
                  <span>Dòng {result.line} · SV {result.service_id}: {result.success ? `ORDER ID ${result.order_id}` : result.message}</span>
                </div>
              ))}
            </div>
          </div>
        </section>

        <aside className={styles.sidebar}>
          <section className={styles.sideCard}>
            <div className={styles.sideTitle}><span className={styles.bulbIcon}><BulbOutlined /></span><h2>Hướng dẫn chuẩn SMM</h2></div>
            <pre>{'service_id | link | quantity (Default)\nservice_id | link (Package)\nservice_id | link | quantity | comment1/comment2/comment3'}</pre>
            <ul><li>Tách các cột bằng dấu <strong>|</strong>, bỏ qua khoảng trắng.</li><li>Comments phân cách bằng dấu <strong>/</strong>.</li><li>Backend kiểm tra giá, số dư, min/max và loại dịch vụ.</li><li>Tối đa 100 đơn trong mỗi lần xử lý.</li></ul>
          </section>
        </aside>
      </div>
    </ClientLayout>
  );
}
