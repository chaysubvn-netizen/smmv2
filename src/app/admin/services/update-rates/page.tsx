'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Modal, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, ReloadOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './update-rates.module.css';

const { Text, Title } = Typography;

type RateUpdate = {
  id: number;
  service: { id: number; name: string } | null;
  rate_old_format: string;
  rate_new_format: string;
  type: 'increase' | 'decrease' | 'add' | 'delete' | string;
  created_at: string;
};

export default function AdminUpdateRatesPage() {
  const [updates, setUpdates] = useState<RateUpdate[]>([]);
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (page = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/resources/rate-updates', { params: { page, per_page: pageSize } });
      const result = response.data.data;
      setUpdates(result.data || []);
      setPagination({ current: result.current_page || 1, pageSize: result.per_page || 20, total: result.total || 0 });
    } catch {
      message.error('Không thể tải lịch sử cập nhật giá.');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const removeSingle = (id: number) => {
    Modal.confirm({
      title: 'Xóa bản ghi này?',
      content: 'Lịch sử cập nhật giá này sẽ bị xóa vĩnh viễn.',
      okText: 'Xóa', okType: 'danger', cancelText: 'Hủy',
      onOk: async () => {
        try {
          await api.delete(`/admin/resources/rate-updates/${id}`);
          message.success('Đã xóa bản ghi.');
          await load(pagination.current, pagination.pageSize);
        } catch {
          message.error('Có lỗi xảy ra khi xóa.');
        }
      },
    });
  };

  const removeAll = () => {
    Modal.confirm({
      title: 'Bạn có chắc chắn?',
      content: 'Hành động này sẽ xóa tất cả lịch sử cập nhật giá trên trang web này và không thể hoàn tác!',
      okText: 'Đồng ý, xóa tất cả!', okType: 'danger', cancelText: 'Hủy',
      onOk: async () => {
        try {
          await api.delete('/admin/resources/rate-updates');
          message.success('Đã xóa tất cả lịch sử cập nhật giá.');
          await load(1, pagination.pageSize);
        } catch {
          message.error('Có lỗi xảy ra khi xóa.');
        }
      },
    });
  };

  const columns = [
    { title: '#', dataIndex: 'id', width: 58, align: 'center' as const },
    { title: 'Thao tác', width: 92, align: 'center' as const, render: (_: unknown, item: RateUpdate) => <Button className={styles.deleteRow} type="text" danger icon={<DeleteOutlined />} aria-label={`Xóa lịch sử #${item.id}`} onClick={() => removeSingle(item.id)} /> },
    { title: 'Tên dịch vụ', render: (_: unknown, item: RateUpdate) => <Text className={styles.service}>[{item.service?.id || '—'}] - {item.service?.name || 'Dịch vụ đã bị xóa'}</Text> },
    { title: 'Giá cũ', dataIndex: 'rate_old_format', width: 115, align: 'center' as const },
    { title: 'Giá mới', dataIndex: 'rate_new_format', width: 115, align: 'center' as const },
    { title: 'Tăng/Giảm', dataIndex: 'type', width: 125, align: 'center' as const, render: (value: string) => {
      if (value === 'increase') return <Tag color="success">Tăng</Tag>;
      if (value === 'decrease') return <Tag color="error">Giảm</Tag>;
      if (value === 'add') return <Tag>Thêm</Tag>;
      if (value === 'delete') return <Tag color="warning">Xóa</Tag>;
      return <Tag color="processing">Khác</Tag>;
    } },
    { title: 'Thời gian cập nhật', dataIndex: 'created_at', width: 190, align: 'center' as const, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  return <main className={styles.page}>
    <Card className={styles.card}>
      <header className={styles.cardHeader}>
        <div><Title level={4}>Danh sách cập nhật giá</Title><Text type="secondary">Lịch sử thay đổi giá dịch vụ</Text></div>
        <div className={styles.actions}><Button icon={<ReloadOutlined />} onClick={() => void load(pagination.current, pagination.pageSize)}>Làm mới</Button><Button className={styles.deleteAll} danger icon={<DeleteOutlined />} onClick={removeAll} disabled={!pagination.total}>Xóa tất cả</Button></div>
      </header>
      <Table
        rowKey="id"
        columns={columns}
        dataSource={updates}
        loading={loading}
        scroll={{ x: 980 }}
        pagination={{ ...pagination, showSizeChanger: true, showTotal: total => `${total} bản ghi`, locale: { items_per_page: '/ trang' } }}
        onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)}
      />
    </Card>
  </main>;
}
