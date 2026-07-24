'use client';

import { useEffect, useState } from 'react';
import { Alert, Breadcrumb, Button, Card, Empty, Form, Input, Modal, Select, Space, Table, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { CheckSquareOutlined, CloudDownloadOutlined, CloseSquareOutlined, ReloadOutlined, SearchOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import styles from './import.module.css';

const { Text, Title } = Typography;
type Option = { id: number; name: string };
type Source = { category_name: string; category: string; platform: string };
const errorText = (value: unknown, fallback = 'Đã xảy ra lỗi.') => {
  if (typeof value === 'string') return value;
  if (value && typeof value === 'object' && 'message' in value) return String((value as { message?: unknown }).message || fallback);
  return fallback;
};

export default function ImportCategoriesPage() {
  const [form] = Form.useForm();
  const [platforms, setPlatforms] = useState<Option[]>([]);
  const [providers, setProviders] = useState<Option[]>([]);
  const [rows, setRows] = useState<Source[]>([]);
  const [selected, setSelected] = useState<React.Key[]>([]);
  const [loading, setLoading] = useState(false);
  const [importing, setImporting] = useState(false);
  const [error, setError] = useState('Chưa đầy đủ thông tin');

  useEffect(() => {
    const timer = setTimeout(() => {
      Promise.all([
        api.get('/admin/categories', { params: { per_page: 10 } }),
        api.get('/admin/resources/providers', { params: { per_page: 100 } }),
      ]).then(([categories, providerRows]) => {
        setPlatforms(categories.data.meta?.platforms || []);
        setProviders(providerRows.data.data?.data || []);
      }).catch(() => message.error('Không thể tải cấu hình nguồn.'));
    }, 0);
    return () => clearTimeout(timer);
  }, []);

  const fetchSource = async (refresh = false) => {
    try {
      const values = await form.validateFields();
      setLoading(true);
      const response = await api.get('/admin/category-import/source', { params: { ...values, refresh: refresh ? 1 : 0 } });
      const items = Array.isArray(response.data.data) ? response.data.data : [];
      setRows(items);
      setSelected([]);
      setError(items.length ? '' : errorText(response.data.message, 'Không có dữ liệu phù hợp.'));
      message.success(errorText(response.data.message, 'Đã tải danh mục từ nguồn.'));
    } catch (reason: unknown) {
      if (reason && typeof reason === 'object' && 'errorFields' in reason) {
        setError('Chưa đầy đủ thông tin');
        return;
      }
      const responseMessage = (reason as { response?: { data?: { message?: unknown } } })?.response?.data?.message;
      setError(errorText(responseMessage, 'Không thể tải dữ liệu từ Provider.'));
    } finally {
      setLoading(false);
    }
  };

  const importSelected = () => {
    if (!selected.length) return message.warning('Chọn ít nhất một danh mục.');
    Modal.confirm({
      title: 'Xác nhận nhập', content: `Nhập ${selected.length} danh mục đã chọn và các dịch vụ tương ứng?`, okText: 'Nhập ngay', cancelText: 'Huỷ',
      onOk: async () => {
        const values = form.getFieldsValue(); setImporting(true);
        try {
          const response = await api.post('/admin/category-import', { platform_id: values.platform_id, provider_id: values.provider_id, service_ids: selected, type: 'categories' });
          if (response.data.success === false) throw new Error(errorText(response.data.message));
          message.success(errorText(response.data.message, 'Nhập danh mục thành công.')); setSelected([]);
        } catch (reason: unknown) { message.error(reason instanceof Error ? reason.message : 'Không thể nhập danh mục.'); }
        finally { setImporting(false); }
      },
    });
  };

  const columns = [
    { title: 'STT', width: 70, render: (_: unknown, __: Source, index: number) => index + 1 },
    { title: 'Tên danh mục (Gốc)', dataIndex: 'category_name', render: (value: string) => <Text strong>{value}</Text> },
    { title: 'Loại (Category)', dataIndex: 'category', render: (value: string) => <span className={styles.blueBadge}>{value}</span> },
    { title: 'Nền tảng', dataIndex: 'platform', render: (value: string) => <span className={styles.cyanBadge}>{value}</span> },
  ];

  return <main className={styles.page}>
    <Breadcrumb items={[{ title: 'Trang Quản Trị' }, { title: 'Import Categories' }]} className={styles.breadcrumb} />
    <header className={styles.heading}><div><Title level={2}>Import Categories</Title><Text type="secondary">Đồng bộ và nhập hàng loạt danh mục từ API Provider</Text></div><div className={styles.actions}>{rows.length > 0 && <Button type="primary" icon={<CloudDownloadOutlined />} loading={importing} disabled={!selected.length} onClick={importSelected}>Nhập danh mục đã chọn ({selected.length})</Button>}<Button icon={<ReloadOutlined />} onClick={() => void fetchSource(true)} loading={loading}>Làm mới dữ liệu</Button></div></header>
    <Alert className={styles.notice} type="warning" showIcon title="Lưu ý: Quá trình tải dữ liệu trực tiếp từ nguồn có thể mất vài giây. Hệ thống sẽ lưu tạm (cache) dữ liệu trong 5 phút để tăng tốc độ." />
    <Card className={styles.card} title="Cấu hình nguồn tải"><Form form={form} layout="vertical"><div className={styles.formGrid}><Form.Item name="platform_id" label="Nền tảng" rules={[{ required: true, message: 'Chọn nền tảng.' }]}><Select showSearch optionFilterProp="label" placeholder="Chọn nền tảng" options={platforms.map(item => ({ value: item.id, label: item.name }))} /></Form.Item><Form.Item name="keyword" label="Từ khoá lọc" rules={[{ required: true, message: 'Nhập từ khoá lọc.' }]}><Input prefix={<SearchOutlined />} placeholder="Nhập từ khoá lọc..." /></Form.Item><Form.Item name="provider_id" label="API Providers" rules={[{ required: true, message: 'Chọn API Provider.' }]}><Select showSearch optionFilterProp="label" placeholder="Chọn API PROVIDERS" options={providers.map(item => ({ value: item.id, label: item.name }))} /></Form.Item></div><Button block type="primary" icon={<CloudDownloadOutlined />} loading={loading} onClick={() => void fetchSource(false)}>Tải danh mục từ nguồn</Button></Form></Card>
    <Card className={`${styles.card} ${styles.results}`} title="Danh sách danh mục từ nguồn" extra={rows.length > 0 && <Space><Button icon={<CheckSquareOutlined />} disabled={selected.length === rows.length} onClick={() => setSelected(rows.map(item => item.category_name))}>Chọn tất cả ({rows.length})</Button><Button icon={<CloseSquareOutlined />} disabled={!selected.length} onClick={() => setSelected([])}>Bỏ chọn</Button></Space>}>{error && <Alert type="error" showIcon banner title={error} />}<Table rowKey="category_name" loading={loading} columns={columns} dataSource={rows} rowSelection={{ selectedRowKeys: selected, onChange: setSelected }} pagination={{ pageSize: 20, showSizeChanger: true }} locale={{ emptyText: <Empty description={'Chưa có dữ liệu. Vui lòng cấu hình nguồn và nhấn "Tải danh mục".'} /> }} /></Card>
  </main>;
}
