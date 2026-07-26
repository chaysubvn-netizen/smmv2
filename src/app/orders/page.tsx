'use client';

import React, { useEffect, useState } from 'react';
import ClientLayout from '@/components/ClientLayout';
import { Table, Typography, Card, Input, Button, Tag, Space, Select, Modal, Dropdown } from 'antd';import { message } from '@/lib/antd-message';
import { CopyOutlined, LinkOutlined, MessageOutlined, RightOutlined, PlusOutlined, SearchOutlined, SettingOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import Link from 'next/link';

const { Title, Text } = Typography;
const isEnabledFlag = (value: unknown) => value === true || value === 1 || value === '1';

export default function OrdersPage() {
  const [orders, setOrders] = useState<any[]>([]);
  const [counts, setCounts] = useState<any>({});
  const [loading, setLoading] = useState(true);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [config, setConfig] = useState<any>({ currency: 'VND' });
  const [activeTab, setActiveTab] = useState('all');
  const [searchText, setSearchText] = useState('');
  const [searchBy, setSearchBy] = useState<'id' | 'link'>('id');
  const [commentOrder, setCommentOrder] = useState<any>(null);

  const getComments = (order: any) => {
    const comments = order?.input_data?.comments;
    return Array.isArray(comments) ? comments.join('\n') : String(comments || '');
  };

  const isCustomCommentsOrder = (order: any) =>
    String(order?.service?.type || '').trim().replace(/\s+/g, ' ').toLowerCase() === 'custom comments'
    && Boolean(getComments(order).trim());

  useEffect(() => {
    fetchConfig();
  }, []);

  useEffect(() => {
    fetchOrders(1, searchText);
  }, [activeTab]);

  const fetchConfig = async () => {
    try {
      const res = await api.get('/client/config');
      if (res.data.status) {
        setConfig(res.data.data);
      }
    } catch (e) {}
  };

  const formatCurrency = (amount: number | string) => {
    if (!amount) return config.currency === 'VND' ? '0 đ' : '$0.0000';
    const val = typeof amount === 'string' ? parseFloat(amount) : amount;
    if (isNaN(val)) return config.currency === 'VND' ? '0 đ' : '$0.0000';
    if (config.currency === 'VND') {
      return Math.round(val).toLocaleString('vi-VN') + ' đ';
    }
    return '$' + val.toFixed(4);
  };

  const fetchOrders = async (page: number, search: string = searchText) => {
    setLoading(true);
    try {
      const response = await api.get('/client/orders', {
        params: { page, status: activeTab, search, search_by: searchBy },
      });
      if (response.data.status) {
        setOrders(response.data.data.data);
        if (response.data.counts) {
          setCounts(response.data.counts);
        }
        setPagination({
          ...pagination,
          current: response.data.data.current_page,
          total: response.data.data.total,
          pageSize: response.data.data.per_page,
        });
      }
    } catch (error) {
      message.error('Không thể tải lịch sử đơn hàng');
    } finally {
      setLoading(false);
    }
  };

  const handleActionOrder = async (id: number, type: 'cancel' | 'refill') => {
    try {
      const response = await api.post(`/client/orders/action`, { id, action: type });
      if (response.data.status) {
        message.success(response.data.message || `Yêu cầu ${type === 'cancel' ? 'hủy' : 'bảo hành'} thành công`);
        fetchOrders(pagination.current, searchText);
      } else {
        message.error(response.data.message || 'Có lỗi xảy ra');
      }
    } catch (e: any) {
      message.error(e.response?.data?.message || 'Có lỗi xảy ra');
    }
  };

  const handleTableChange = (newPagination: any) => {
    fetchOrders(newPagination.current);
  };


  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Completed': return 'success';
      case 'Processing': return 'purple';
      case 'In progress': return 'processing';
      case 'Pending': return 'warning';
      case 'Canceled': return 'error';
      case 'Partial': return 'cyan';
      default: return 'default';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'Completed': return 'Hoàn thành';
      case 'Processing': return 'Đang xử lý';
      case 'In progress': return 'Đang chạy';
      case 'Pending': return 'Chờ xử lý';
      case 'Canceled': return 'Đã hủy';
      case 'Partial': return 'Một phần';
      default: return status;
    }
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    message.success('Đã copy ID!');
  };

  const columns = [
    {
      title: 'Order ID',
      dataIndex: 'id',
      key: 'id',
      width: 80,
      render: (id: number) => (
        <Tag color="blue" style={{ cursor: 'pointer' }} onClick={() => copyToClipboard(id.toString())}>
          #{id} <CopyOutlined style={{ marginLeft: 4, opacity: 0.7 }} />
        </Tag>
      ),
    },
    {
      title: 'DỊCH VỤ',
      dataIndex: 'service_name',
      key: 'service_name',
      render: (text: string, record: any) => (
        <div style={{ maxWidth: 350 }}>
          <div className="service-title">
            {record.service?.name || 'Unknown'}
          </div>
          <div className="service-category">
            Dịch vụ {record.service?.category?.name ? `- ${record.service.category.name}` : ''}
          </div>
          {isCustomCommentsOrder(record) && (
            <Button type="link" size="small" icon={<MessageOutlined />} style={{ padding: 0, height: 24 }} onClick={() => setCommentOrder(record)}>
              Xem bình luận
            </Button>
          )}
        </div>
      ),
    },
    {
      title: 'LIÊN KẾT',
      dataIndex: 'link',
      key: 'link',
      width: 200,
      render: (text: string) => (
        <a href={text} target="_blank" rel="noreferrer" style={{ color: '#3b82f6', fontSize: '13px' }} className="hover:underline">
          <span style={{ maxWidth: 150, display: 'inline-block', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', verticalAlign: 'middle' }}>
            {text}
          </span>
          <LinkOutlined style={{ marginLeft: 4 }} />
        </a>
      )
    },
    {
      title: 'Số Lượng',
      dataIndex: 'quantity',
      key: 'quantity',
      width: 80,
      align: 'center' as const,
      render: (qty: number | string) => (
        <span className="font-medium">{Number(qty || 0).toLocaleString('vi-VN')}</span>
      )
    },
    {
      title: 'BẮT ĐẦU',
      dataIndex: 'start_count',
      key: 'start_count',
      width: 90,
      align: 'center' as const,
      render: (count: number) => <span>{count ? count.toLocaleString('vi-VN') : 0}</span>
    },
    {
      title: 'CÒN LẠI',
      dataIndex: 'remains',
      key: 'remains',
      width: 90,
      align: 'center' as const,
      render: (remains: number) => <span>{remains ? remains.toLocaleString('vi-VN') : 0}</span>
    },
    {
      title: 'CHI PHÍ',
      dataIndex: 'total',
      key: 'total',
      width: 100,
      align: 'right' as const,
      render: (price: number) => (
        <span style={{ color: '#ef4444', fontWeight: 600 }}>{formatCurrency(price)}</span>
      ),
    },
    {
      title: 'TRẠNG THÁI',
      dataIndex: 'status',
      key: 'status',
      width: 120,
      align: 'center' as const,
      render: (status: string) => (
        <Tag color={getStatusColor(status)} style={{ borderRadius: '4px', fontWeight: 500 }}>
          {getStatusText(status)}
        </Tag>
      ),
    },
    {
      title: 'NGÀY ĐẶT',
      dataIndex: 'created_at',
      key: 'created_at',
      width: 140,
      render: (date: string) => {
        const d = new Date(date);
        const time = d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        const day = d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: '2-digit' });
        return <span style={{ color: '#6b7280', fontSize: '13px' }}>{time} {day}</span>;
      },
    },
    {
      title: 'THAO TÁC',
      key: 'action',
      width: 130,
      align: 'right' as const,
      render: (text: string, record: any) => {
        const items = [];

        if (isEnabledFlag(record.refill)) {
          items.push({
            key: 'refill',
            label: 'Yêu cầu bảo hành',
            onClick: () => handleActionOrder(record.id, 'refill'),
          });
        }

        if (isEnabledFlag(record.cancel) && record.status !== 'Canceled' && record.status !== 'Completed') {
          items.push({
            key: 'cancel',
            danger: true,
            label: 'Yêu cầu huỷ',
            onClick: () => handleActionOrder(record.id, 'cancel'),
          });
        }

        return items.length ? (
          <Dropdown menu={{ items }} trigger={['click']} placement="bottomRight">
            <Button size="small" shape="circle" icon={<SettingOutlined />} aria-label={`Thao tác đơn hàng #${record.id}`} />
          </Dropdown>
        ) : (
          <Button size="small" shape="circle" icon={<SettingOutlined />} disabled aria-label={`Đơn hàng #${record.id} không có thao tác`} />
        );
      }
    },
    {
      title: '',
      key: 'detail',
      width: 56,
      align: 'center' as const,
      render: (_: unknown, record: { id: number }) => (
        <Link href={`/orders/${record.id}`} aria-label={`Xem chi tiết đơn hàng #${record.id}`}>
          <RightOutlined style={{ color: '#64748b', padding: 10 }} />
        </Link>
      ),
    }
  ];

  const tabs = [
    { key: 'all', label: 'Tất cả' },
    { key: 'Pending', label: 'Chờ xử lý', color: '#f59e0b' },
    { key: 'Processing', label: 'Đang xử lý', color: '#8b5cf6' },
    { key: 'In progress', label: 'Đang chạy', color: '#3b82f6' },
    { key: 'Completed', label: 'Hoàn thành', color: '#10b981' },
    { key: 'Partial', label: 'Hoàn 1 phần', color: '#06b6d4' },
    { key: 'Canceled', label: 'Đã huỷ', color: '#ef4444' },
  ];

  return (
    <ClientLayout>
      <div className="mb-6 d-flex justify-content-between align-items-center">
        <div>
          <Title level={3} style={{ margin: 0, fontWeight: 600 }}>Lịch sử đơn hàng</Title>
          <Text type="secondary">Theo dõi trạng thái các đơn hàng của bạn</Text>
        </div>
        <Link href="/">
          <Button type="primary" size="large" icon={<PlusOutlined />} style={{ borderRadius: '8px', fontWeight: 500 }}>
            Đặt đơn mới
          </Button>
        </Link>
      </div>

      <Card 
        className="shadow-sm rounded-lg border-0" 
        styles={{ body: { padding: 0 } }}
      >
        <div className="d-flex flex-wrap justify-content-between align-items-center p-4 border-bottom">
          <Space size="middle" className="mb-3 mb-md-0" wrap>
            {tabs.map(tab => (
              <Button 
                key={tab.key}
                type={activeTab === tab.key ? 'primary' : 'default'}
                shape="round"
                onClick={() => setActiveTab(tab.key)}
                style={{ 
                  boxShadow: 'none',
                  border: activeTab === tab.key ? 'none' : '1px solid #e5e7eb',
                  fontWeight: 500,
                  display: 'flex',
                  alignItems: 'center',
                  gap: '6px'
                }}
              >
                {tab.label}
                <span style={{
                    backgroundColor: activeTab === tab.key ? '#fff' : (tab.color || '#9ca3af'),
                    color: activeTab === tab.key ? '#1f2937' : '#fff',
                    borderRadius: '12px',
                    padding: '2px 8px',
                    fontSize: '12px',
                    fontWeight: 600,
                    lineHeight: '1'
                }}>
                    {counts[tab.key] || 0}
                </span>
              </Button>
            ))}
          </Space>
          
          <Space.Compact size="large" className="order-search">
            <Select
              value={searchBy}
              onChange={(value) => setSearchBy(value)}
              style={{ width: 145 }}
              options={[{ value: 'id', label: 'Order ID' }, { value: 'link', label: 'Link' }]}
            />
            <Input
              placeholder={searchBy === 'id' ? 'Nhập Order ID...' : 'Nhập liên kết...'}
              prefix={<SearchOutlined style={{ color: '#9ca3af' }} />}
              style={{ width: 250 }}
              value={searchText}
              onChange={e => setSearchText(e.target.value)}
              onPressEnter={() => fetchOrders(1, searchText)}
              allowClear
            />
            <Button type="primary" icon={<SearchOutlined />} onClick={() => fetchOrders(1, searchText)}>Tìm</Button>
          </Space.Compact>
        </div>

        <Table 
          columns={columns} 
          dataSource={orders} 
          rowKey="id" 
          pagination={{
            ...pagination,
            showSizeChanger: true,
            showTotal: (total, range) => `${total} đơn hàng`,
            className: 'px-4 mb-4 mt-4'
          }}
          onChange={handleTableChange}
          loading={loading}
          scroll={{ x: 1200 }}
          className="custom-order-table"
        />
      </Card>

      <Modal
        open={Boolean(commentOrder)}
        title={`Nội dung bình luận · Đơn #${commentOrder?.id || ''}`}
        footer={[
          <Button key="copy" icon={<CopyOutlined />} onClick={() => { void navigator.clipboard.writeText(getComments(commentOrder)); message.success('Đã sao chép nội dung bình luận.'); }}>
            Sao chép
          </Button>,
          <Button key="close" type="primary" onClick={() => setCommentOrder(null)}>Đóng</Button>,
        ]}
        onCancel={() => setCommentOrder(null)}
        width={720}
      >
        <Text type="secondary">{getComments(commentOrder).split(/\r?\n/).filter(line => line.trim()).length.toLocaleString('vi-VN')} bình luận</Text>
        <Input.TextArea value={getComments(commentOrder)} readOnly rows={12} style={{ marginTop: 12 }} />
      </Modal>

      <style>{`
        .custom-order-table .ant-table-thead > tr > th::before {
          display: none !important;
        }
        
        .service-title {
          font-weight: 600;
          color: #1f2937;
          font-size: 13px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        
        .service-category {
          font-size: 12px;
          color: #6b7280;
          margin-top: 2px;
        }

        [data-bs-theme="dark"] .service-title {
          color: #e5e7eb !important;
        }
        
        [data-bs-theme="dark"] .service-category {
          color: #9ca3af !important;
        }

        /* Adapt Ant Design to theme dark mode */
        [data-bs-theme="dark"] .ant-card,
        [data-bs-theme="dark"] .ant-table-wrapper .ant-table,
        [data-bs-theme="dark"] .ant-table-thead > tr > th,
        [data-bs-theme="dark"] .ant-table-tbody > tr > td {
          background-color: transparent !important;
          color: #e5e7eb !important;
          border-color: #374151 !important;
        }
        
        [data-bs-theme="dark"] .ant-card {
          background-color: #1f2937 !important;
          border-color: #374151 !important;
        }

        [data-bs-theme="dark"] .ant-pagination-item a,
        [data-bs-theme="dark"] .ant-pagination-prev button,
        [data-bs-theme="dark"] .ant-pagination-next button,
        [data-bs-theme="dark"] .ant-pagination-jump-prev .ant-pagination-item-container .ant-pagination-item-ellipsis,
        [data-bs-theme="dark"] .ant-pagination-jump-next .ant-pagination-item-container .ant-pagination-item-ellipsis,
        [data-bs-theme="dark"] .ant-btn-default {
          background-color: #374151 !important;
          color: #e5e7eb !important;
          border-color: #4b5563 !important;
        }
        
        [data-bs-theme="dark"] .ant-pagination-total-text {
          color: #9ca3af !important;
        }
        
        [data-bs-theme="dark"] .ant-pagination-item-active {
          background-color: transparent !important;
          border-color: #3b82f6 !important;
        }
        
        [data-bs-theme="dark"] .ant-select-selector {
          background-color: #374151 !important;
          color: #e5e7eb !important;
          border-color: #4b5563 !important;
        }
        
        [data-bs-theme="dark"] .ant-select-selection-item {
          color: #e5e7eb !important;
        }
        
        [data-bs-theme="dark"] .ant-select-dropdown {
          background-color: #1f2937 !important;
          border: 1px solid #374151;
        }
        
        [data-bs-theme="dark"] .ant-select-item {
          color: #e5e7eb !important;
        }
        
        [data-bs-theme="dark"] .ant-select-item-option-active {
          background-color: #374151 !important;
        }
        
        [data-bs-theme="dark"] .ant-select-arrow {
          color: #9ca3af !important;
        }

        [data-bs-theme="dark"] .ant-table-tbody > tr:hover > td {
          background-color: rgba(255, 255, 255, 0.04) !important;
        }
        
        [data-bs-theme="dark"] .ant-input-affix-wrapper,
        [data-bs-theme="dark"] .ant-input {
          background-color: #1f2937 !important;
          border-color: #374151 !important;
          color: #e5e7eb !important;
        }

        [data-bs-theme="dark"] .ant-typography {
          color: #f3f4f6 !important;
        }
        
        [data-bs-theme="dark"] .ant-typography.ant-typography-secondary {
          color: #9ca3af !important;
        }

        .custom-order-table .ant-table-thead > tr > th {
          background-color: #f8fafc;
          color: #64748b;
          font-weight: 600;
          font-size: 12px;
          text-transform: uppercase;
          white-space: nowrap;
          word-break: keep-all;
          border-bottom: 1px solid #e2e8f0;
          padding: 16px;
        }
        .custom-order-table .ant-table-tbody > tr > td {
          border-bottom: 1px solid #e2e8f0;
          padding: 16px;
        }
        .custom-order-table .ant-table-tbody > tr:hover > td {
          background-color: #f8fafc;
        }
        
        [data-bs-theme="dark"] .custom-order-table .ant-table-thead > tr > th,
        [data-bs-theme="dark"] .custom-order-table .ant-table-tbody > tr > td {
          border-bottom: 1px solid #374151 !important;
        }
        
        /* Tag styling for dark mode */
        [data-bs-theme="dark"] .ant-tag {
          background-color: #374151 !important;
          border-color: #4b5563 !important;
          color: #e5e7eb !important;
        }
        [data-bs-theme="dark"] .ant-tag-success {
          background-color: rgba(16, 185, 129, 0.2) !important;
          border-color: rgba(16, 185, 129, 0.5) !important;
          color: #34d399 !important;
        }
        [data-bs-theme="dark"] .ant-tag-processing {
          background-color: rgba(59, 130, 246, 0.2) !important;
          border-color: rgba(59, 130, 246, 0.5) !important;
          color: #60a5fa !important;
        }
        [data-bs-theme="dark"] .ant-tag-warning {
          background-color: rgba(245, 158, 11, 0.2) !important;
          border-color: rgba(245, 158, 11, 0.5) !important;
          color: #fbbf24 !important;
        }
        [data-bs-theme="dark"] .ant-tag-error {
          background-color: rgba(239, 68, 68, 0.2) !important;
          border-color: rgba(239, 68, 68, 0.5) !important;
          color: #f87171 !important;
        }
        [data-bs-theme="dark"] .ant-tag-blue {
          background-color: rgba(59, 130, 246, 0.2) !important;
          border-color: rgba(59, 130, 246, 0.5) !important;
          color: #93c5fd !important;
        }
      `}</style>
    </ClientLayout>
  );
}
