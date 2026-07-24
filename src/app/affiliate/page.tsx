'use client';

import { useCallback, useEffect, useState } from 'react';
import { Alert, Button, Card, Empty, Input, Pagination, Statistic, Table, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { CopyOutlined, DollarOutlined, GiftOutlined, LinkOutlined, TeamOutlined, WalletOutlined } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './affiliate.module.css';

const { Text, Title } = Typography;
type Referral = { id: number; username: string; total_deposit: number; commission: number; created_at: string };
type AffiliateData = {
  enabled: boolean; percent: number; minimum_payout: number; maximum_payout: number; currency: string;
  referral_url: string; pending_commission: number; paid_commission: number; total_commission: number;
  referrals: { data: Referral[]; current_page: number; per_page: number; total: number };
};

export default function AffiliatePage() {
  const [data, setData] = useState<AffiliateData | null>(null);
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });

  const loadAffiliate = useCallback(async (page = 1, pageSize = 15) => {
    setLoading(true);
    try {
      const response = await api.get('/client/affiliate', { params: { page, per_page: pageSize } });
      if (response.data?.status) {
        const result = response.data.data as AffiliateData;
        if (result.referral_url) result.referral_url = `${window.location.origin}${new URL(result.referral_url).pathname}`;
        setData(result);
        setPagination({ current: result.referrals.current_page || 1, pageSize: result.referrals.per_page || 15, total: result.referrals.total || 0 });
      }
    } catch { message.error('Không thể tải thông tin tiếp thị liên kết.'); }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { void Promise.resolve().then(() => loadAffiliate()); }, [loadAffiliate]);
  const money = (value = 0) => `${Number(value).toLocaleString('vi-VN')} ${data?.currency === 'VND' ? 'đ' : data?.currency || 'VND'}`;
  const copyLink = async () => {
    if (!data?.referral_url) return;
    await navigator.clipboard.writeText(data.referral_url);
    message.success('Đã sao chép liên kết giới thiệu.');
  };
  const columns = [
    { title: 'Người được giới thiệu', dataIndex: 'username', render: (value: string) => <Text strong>{value}</Text> },
    { title: 'Tổng nạp', dataIndex: 'total_deposit', align: 'right' as const, render: (value: number) => money(value) },
    { title: 'Hoa hồng', dataIndex: 'commission', align: 'right' as const, render: (value: number) => <Text strong type="success">+ {money(value)}</Text> },
    { title: 'Ngày tham gia', dataIndex: 'created_at', width: 180, render: (value: string) => new Date(value).toLocaleString('vi-VN') },
  ];

  return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}><div><Title level={3}>Tiếp thị liên kết</Title><Text type="secondary">Chia sẻ liên kết và nhận hoa hồng từ bạn bè</Text></div><TeamOutlined /></div>
    {!loading && data && !data.enabled ? <Alert type="warning" showIcon title="Chương trình tiếp thị liên kết đang tạm ngừng." /> : null}
    <Alert className={styles.intro} type="info" showIcon icon={<GiftOutlined />} title="Giới thiệu bạn bè và nhận hoa hồng cho mỗi lần nạp tiền thành công." />
    <Card className={styles.linkCard}>
      <div className={styles.linkInfo}><div><LinkOutlined /><span><Text strong>Liên kết chia sẻ</Text><Text type="secondary">Gửi liên kết này cho bạn bè của bạn</Text></span></div><div><Text strong>Hoa hồng</Text><b>{data?.percent || 0}%</b></div><div><Text strong>Hạn mức trả thưởng</Text><b>{money(data?.minimum_payout)}</b></div></div>
      <Input size="large" readOnly value={data?.referral_url || ''} suffix={<Button type="primary" icon={<CopyOutlined />} onClick={copyLink}>Sao chép</Button>} />
    </Card>
    <div className={styles.stats}>
      <Card><Statistic title="Tổng thu nhập" value={data?.total_commission || 0} formatter={(value) => money(Number(value))} prefix={<DollarOutlined />} /></Card>
      <Card><Statistic title="Hoa hồng chưa trả" value={data?.pending_commission || 0} formatter={(value) => money(Number(value))} prefix={<WalletOutlined />} /></Card>
      <Card><Statistic title="Đã trả thưởng" value={data?.paid_commission || 0} formatter={(value) => money(Number(value))} prefix={<GiftOutlined />} /></Card>
      <Card><Statistic title="Người đã giới thiệu" value={pagination.total} prefix={<TeamOutlined />} /></Card>
    </div>
    <Card className={styles.tableCard} title="Danh sách người được giới thiệu">
      <Table rowKey="id" columns={columns} dataSource={data?.referrals.data || []} loading={loading} pagination={false} scroll={{ x: 760 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Chưa có người được giới thiệu" /> }} />
      <div className={styles.pagination}><Pagination {...pagination} showTotal={(total) => `${total} người`} showSizeChanger pageSizeOptions={[10, 15, 20, 50]} locale={{ items_per_page: '/ trang' }} onChange={loadAffiliate} /></div>
    </Card>
  </div></ClientLayout>;
}
