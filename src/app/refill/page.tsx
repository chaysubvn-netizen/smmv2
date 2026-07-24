'use client';

import React, { useEffect, useState } from 'react';
import ClientLayout from '@/components/ClientLayout';
import { Table, Typography, Card, Empty, Spin, Tag, Select, Input, Button, Row, Col, Space } from 'antd';import { message } from '@/lib/antd-message';
import { FilterOutlined } from '@ant-design/icons';
import api from '@/lib/axios';

const { Title } = Typography;
const { Option } = Select;

export default function RefillPage() {
  const [orders, setOrders] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [status, setStatus] = useState('');
  const [search, setSearch] = useState('');

  useEffect(() => {
    fetchOrders(1);
  }, []);

  const fetchOrders = async (page: number) => {
    setLoading(true);
    try {
      const response = await api.get(`/client/refills?page=${page}&status=${status}&search=${search}`);
      if (response.data.status) {
        setOrders(response.data.data.data || []);
        setPagination({
          ...pagination,
          current: response.data.data.current_page || 1,
          total: response.data.data.total || 0,
          pageSize: response.data.data.per_page || 15,
        });
      }
    } catch (error) {
      // Ignore API error since it might not be implemented yet
      setOrders([]);
    } finally {
      setLoading(false);
    }
  };

  const handleTableChange = (newPagination: any) => {
    fetchOrders(newPagination.current);
  };

  const handleFilter = () => {
    fetchOrders(1);
  };

  const getStatusColor = (status: string) => {
    switch (status?.toLowerCase()) {
      case 'completed': return 'green';
      case 'processing': return 'blue';
      case 'pending': return 'orange';
      case 'canceled': return 'red';
      case 'in progress': return 'cyan';
      default: return 'default';
    }
  };

  const getStatusText = (status: string) => {
    switch (status?.toLowerCase()) {
      case 'completed': return 'Hoàn thành';
      case 'processing': return 'Đang xử lý';
      case 'pending': return 'Chờ xử lý';
      case 'canceled': return 'Đã hủy';
      case 'in progress': return 'Đang thực hiện';
      default: return status || 'N/A';
    }
  };

  const columns = [
    {
      title: 'Mã đơn',
      dataIndex: 'id',
      key: 'id',
      width: 100,
    },
    {
      title: 'Ngày tạo',
      dataIndex: 'created_at',
      key: 'created_at',
      width: 160,
      render: (date: string) => date ? new Date(date).toLocaleString('vi-VN') : 'N/A',
    },
    {
      title: 'Trạng thái',
      dataIndex: 'status_refill',
      key: 'status_refill',
      width: 120,
      render: (status: string) => (
        <Tag color={getStatusColor(status)}>
          {getStatusText(status)}
        </Tag>
      ),
    },
  ];

  return (
    <ClientLayout>
      <Title level={3} className="mb-6">Đơn hàng bảo hành</Title>
      <Card className="shadow-sm mb-4">
        <Row gutter={[16, 16]}>
          <Col xs={24} lg={8}>
            <Select
              className="w-full"
              placeholder="Tất cả trạng thái"
              value={status || undefined}
              onChange={(val) => setStatus(val)}
              allowClear
            >
              <Option value="Pending">Chờ xử lý</Option>
              <Option value="Processing">Đang xử lý</Option>
              <Option value="In progress">Đang thực hiện</Option>
              <Option value="Completed">Hoàn thành</Option>
              <Option value="Canceled">Hủy</Option>
            </Select>
          </Col>
          <Col xs={24} lg={16}>
            <Space.Compact className="w-full flex">
              <Input
                style={{ width: 'calc(100% - 120px)' }}
                placeholder="Tìm kiếm theo mã đơn hàng"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                onPressEnter={handleFilter}
              />
              <Button type="primary" style={{ width: '120px' }} icon={<FilterOutlined />} onClick={handleFilter}>
                Tìm kiếm
              </Button>
            </Space.Compact>
          </Col>
        </Row>
      </Card>

      <Card className="shadow-sm">
        <Table 
          columns={columns} 
          dataSource={orders} 
          rowKey="id" 
          pagination={pagination}
          onChange={handleTableChange}
          loading={loading}
          scroll={{ x: 'max-content' }}
          locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description="Không có đơn hàng nào" /> }}
        />
      </Card>
    </ClientLayout>
  );
}
