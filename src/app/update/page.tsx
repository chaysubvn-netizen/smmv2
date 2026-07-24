'use client';

import { useEffect, useState } from 'react';
import { AppstoreOutlined, ArrowDownOutlined, ArrowUpOutlined, FacebookFilled, GlobalOutlined, GoogleOutlined, HistoryOutlined, InstagramOutlined, PlayCircleFilled, PlusOutlined, SendOutlined, ShopOutlined, StopOutlined, TeamOutlined } from '@ant-design/icons';
import { Button, Empty, Spin, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './update.module.css';

const { Text, Title } = Typography;

type ServiceUpdate = {
  id: number;
  type: 'increase' | 'decrease' | 'add' | 'delete';
  rate_old_display: string;
  rate_new_display: string;
  created_at: string;
  service: { id: number; name: string; icon?: string };
};

type PageData = { data: ServiceUpdate[]; current_page: number; last_page: number };

const types = {
  increase: { label: 'Tăng giá', icon: <ArrowUpOutlined />, color: 'red', className: styles.increase },
  decrease: { label: 'Giảm giá', icon: <ArrowDownOutlined />, color: 'green', className: styles.decrease },
  add: { label: 'Dịch vụ mới', icon: <PlusOutlined />, color: 'blue', className: styles.add },
  delete: { label: 'Tắt dịch vụ', icon: <StopOutlined />, color: 'default', className: styles.deleted },
};

const imageUrl = (path?: string) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  return `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000').replace('/api', '')}${path}`;
};

const presetIcons: Record<string, React.ReactNode> = {
  global: <GlobalOutlined />, facebook: <FacebookFilled />, instagram: <InstagramOutlined />,
  tiktok: <PlayCircleFilled />, youtube: <PlayCircleFilled />, telegram: <SendOutlined />,
  discord: <TeamOutlined />, google: <GoogleOutlined />, shop: <ShopOutlined />, app: <AppstoreOutlined />,
};

function ServiceIcon({ icon }: { icon?: string }) {
  const [failed, setFailed] = useState(false);
  if (icon?.startsWith('preset:')) {
    return <span className={styles.fallbackIcon}>{presetIcons[icon.slice(7)] || <AppstoreOutlined />}</span>;
  }
  if (icon && !failed) {
    return <img src={imageUrl(icon)} alt="" onError={() => setFailed(true)} />;
  }
  return <span className={styles.fallbackIcon}><AppstoreOutlined /></span>;
}

export default function UpdatesPage() {
  const [items, setItems] = useState<ServiceUpdate[]>([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);

  const load = async (nextPage = 1) => {
    setLoading(true);
    try {
      const response = await api.get('/client/updates', { params: { page: nextPage, per_page: 20 } });
      const result: PageData = response.data.data;
      setItems(current => nextPage === 1 ? result.data : [...current, ...result.data]);
      setPage(result.current_page);
      setLastPage(result.last_page);
    } catch {
      message.error('Không thể tải lịch sử cập nhật dịch vụ.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { void load(); }, []);

  return (
    <ClientLayout>
      <main className={styles.page}>
        <header className={styles.header}>
          <div>
            <Title level={3}>Cập nhật dịch vụ</Title>
            <Text type="secondary">Theo dõi dịch vụ mới và những thay đổi giá gần đây</Text>
          </div>
          <span className={styles.headerIcon}><HistoryOutlined /></span>
        </header>

        <section className={styles.card}>
          {loading && items.length === 0 ? <div className={styles.loading}><Spin size="large" /></div> : null}
          {!loading && items.length === 0 ? <Empty description="Chưa có cập nhật dịch vụ nào" /> : null}
          <div className={styles.feed}>
            {items.map(item => {
              const meta = types[item.type] || types.add;
              return (
                <article className={`${styles.item} ${meta.className}`} key={item.id}>
                  <div className={styles.itemTop}>
                    <Tag color={meta.color} icon={meta.icon}>{meta.label}</Tag>
                    <time>{new Date(item.created_at).toLocaleString('vi-VN')}</time>
                  </div>
                  <div className={styles.content}>
                    <div className={styles.serviceLine}>
                      <ServiceIcon icon={item.service.icon} />
                      <strong><span>#{item.service.id}</span> {item.service.name}</strong>
                    </div>
                    {(item.type === 'increase' || item.type === 'decrease') && (
                      <div className={styles.priceLine}>
                        <div><small>Giá trước</small><span>{item.rate_old_display}</span></div>
                        <span className={styles.priceArrow}>{meta.icon}</span>
                        <div><small>Giá mới</small><b>{item.rate_new_display}</b></div>
                      </div>
                    )}
                    {item.type === 'add' && <p className={styles.note}>Dịch vụ vừa được bổ sung và hiện đã có thể đặt hàng.</p>}
                    {item.type === 'delete' && <p className={styles.note}>Dịch vụ này hiện đã ngừng nhận đơn mới.</p>}
                  </div>
                </article>
              );
            })}
          </div>
          {page < lastPage && <div className={styles.more}><Button loading={loading} onClick={() => void load(page + 1)}>Xem thêm cập nhật</Button></div>}
        </section>
      </main>
    </ClientLayout>
  );
}
