'use client';

import { useCallback, useEffect, useMemo, useState } from 'react';
import { useParams } from 'next/navigation';
import { Button, Card, Form, Input, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, EditOutlined, PlusOutlined, ReloadOutlined, SearchOutlined } from '@ant-design/icons';
import type { ColumnsType, TablePaginationConfig } from 'antd/es/table';
import api from '@/lib/axios';
import styles from './resource.module.css';

const { Text, Title } = Typography;
type Row = Record<string, unknown> & { id: number };
type ResourceMeta = { resource: string; columns: string[]; editable: string[]; read_only: boolean; can_create: boolean; can_delete: boolean };
type PageData = { data: Row[]; current_page: number; per_page: number; total: number };

const titles: Record<string, [string, string]> = {
  platforms: ['Nền tảng', 'Quản lý các nền tảng mạng xã hội'], categories: ['Phân loại dịch vụ', 'Quản lý danh mục và phân loại'],
  services: ['Dịch vụ SMM', 'Danh sách và cấu hình dịch vụ'], discounts: ['Mã giảm giá', 'Quản lý chương trình ưu đãi'],
  providers: ['Nhà cung cấp API', 'Kết nối và quản lý provider'], orders: ['Đơn hàng SMM', 'Theo dõi và xử lý đơn hàng'],
  products: ['Sản phẩm', 'Quản lý sản phẩm'], 'product-categories': ['Danh mục sản phẩm', 'Quản lý danh mục sản phẩm'],
  'product-orders': ['Đơn hàng sản phẩm', 'Theo dõi đơn mua sản phẩm'], banks: ['Tài khoản ngân hàng', 'Cấu hình tài khoản nhận tiền'],
  deposits: ['Lịch sử nạp tiền', 'Duyệt và theo dõi giao dịch nạp'], bonuses: ['Tiền thưởng', 'Quản lý mức thưởng nạp tiền'],
  posts: ['Bài viết', 'Quản lý Blog & Kiến thức'], affiliates: ['Tiếp thị liên kết', 'Danh sách người giới thiệu'],
  tickets: ['Tickets', 'Quản lý yêu cầu hỗ trợ'], childpanels: ['Website riêng', 'Quản lý website đại lý'],
  users: ['Thành viên', 'Quản lý tài khoản khách hàng'], transactions: ['Lịch sử giao dịch', 'Theo dõi biến động số dư'],
  currencies: ['Tiền tệ', 'Quản lý tiền tệ và tỷ giá'], 'facebook-tokens': ['Facebook Token', 'Quản lý token Facebook'],
  notifications: ['Cấu hình thông báo', 'Quản lý nội dung thông báo khách hàng'], settings: ['Cấu hình website', 'Thiết lập chung cho hệ thống'],
  telegram: ['Telegram & 2FA', 'Thiết lập bot và liên kết Telegram'], 'api-keys': ['Cấu hình API Key', 'Quản lý khóa kết nối thanh toán và dịch vụ'],
  system: ['Cập nhật hệ thống', 'Thông tin phiên bản và cấu hình hệ thống'],
};
const statusColors: Record<string, string> = { active: 'green', completed: 'green', success: 'green', inactive: 'default', pending: 'orange', processing: 'blue', canceled: 'red', failed: 'red' };
const labels: Record<string, string> = { name: 'Tên', title: 'Tiêu đề', status: 'Trạng thái', domain: 'Tên miền', created_at: 'Ngày tạo', updated_at: 'Cập nhật', username: 'Tài khoản', email: 'Email', balance: 'Số dư', total: 'Tổng tiền', amount: 'Số tiền', rate: 'Giá', quantity: 'Số lượng', content: 'Nội dung', description: 'Mô tả', slug: 'Đường dẫn', image: 'Hình ảnh', link: 'Liên kết' };

const displayValue = (value: unknown) => {
  if (value === null || value === undefined || value === '') return '-';
  if (typeof value === 'object') return JSON.stringify(value);
  return String(value);
};

export default function AdminResourcePage() {
  const { resource } = useParams<{ resource: string }>();
  const [form] = Form.useForm();
  const [rows, setRows] = useState<Row[]>([]);
  const [meta, setMeta] = useState<ResourceMeta | null>(null);
  const [pagination, setPagination] = useState({ current: 1, pageSize: 20, total: 0 });
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [editing, setEditing] = useState<Row | null>(null);
  const [modalOpen, setModalOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const title = titles[resource] || [resource, 'Quản lý dữ liệu hệ thống'];

  const load = useCallback(async (page = 1, pageSize = 20, keyword = search) => {
    setLoading(true);
    try {
      const response = await api.get(`/admin/resources/${resource}`, { params: { page, per_page: pageSize, search: keyword } });
      const result: PageData = response.data.data;
      setRows(result.data || []); setMeta(response.data.meta);
      setPagination({ current: result.current_page || 1, pageSize: result.per_page || pageSize, total: result.total || 0 });
    } catch { message.error(`Không thể tải module ${title[0]}.`); }
    finally { setLoading(false); }
  }, [resource, search, title]);

  useEffect(() => { void Promise.resolve().then(() => load(1, 20, '')); }, [resource]); // eslint-disable-line react-hooks/exhaustive-deps

  const openCreate = () => { setEditing(null); form.resetFields(); setModalOpen(true); };
  const openEdit = (row: Row) => {
    setEditing(row);
    const values = Object.fromEntries((meta?.editable || []).map(field => [field, typeof row[field] === 'object' && row[field] !== null ? JSON.stringify(row[field], null, 2) : row[field]]));
    form.setFieldsValue(values); setModalOpen(true);
  };
  const save = async () => {
    const values = await form.validateFields(); setSaving(true);
    try {
      if (editing) await api.put(`/admin/resources/${resource}/${editing.id}`, { data: values });
      else await api.post(`/admin/resources/${resource}`, { data: values });
      message.success(editing ? 'Đã cập nhật dữ liệu.' : 'Đã tạo dữ liệu.'); setModalOpen(false); await load(pagination.current, pagination.pageSize);
    } catch (error: unknown) { const detail = error as { response?: { data?: { message?: string } } }; message.error(detail.response?.data?.message || 'Không thể lưu dữ liệu.'); }
    finally { setSaving(false); }
  };
  const remove = (row: Row) => Modal.confirm({ title: 'Xóa dữ liệu này?', content: `ID #${row.id} sẽ bị xóa và không thể hoàn tác.`, okText: 'Xóa', okType: 'danger', cancelText: 'Hủy', onOk: async () => { await api.delete(`/admin/resources/${resource}/${row.id}`); message.success('Đã xóa dữ liệu.'); await load(pagination.current, pagination.pageSize); } });

  const columns = useMemo<ColumnsType<Row>>(() => {
    const available = (meta?.columns || []).filter(column => !['content', 'description', 'created_at', 'updated_at'].includes(column));
    const preferred = ['id', 'name', 'title', 'username', 'email', 'service_id', 'user_id', 'link', 'quantity', 'amount', 'total', 'balance', 'status', 'domain'];
    const chosen = [...preferred.filter(column => available.includes(column)), ...available.filter(column => !preferred.includes(column))].slice(0, 8);
    const result: ColumnsType<Row> = chosen.map(column => ({ title: labels[column] || column.replaceAll('_', ' '), dataIndex: column, key: column, ellipsis: true, render: (value: unknown) => column === 'status' ? <Tag color={statusColors[String(value).toLowerCase()] || 'blue'}>{displayValue(value)}</Tag> : <span className={styles.cell}>{displayValue(value)}</span> }));
    if (!meta?.read_only) result.push({ title: '', key: 'actions', fixed: 'right', width: 90, render: (_: unknown, row: Row) => <div className={styles.actions}><Button type="text" icon={<EditOutlined />} onClick={() => openEdit(row)} />{meta?.can_delete ? <Button type="text" danger icon={<DeleteOutlined />} onClick={() => remove(row)} /> : null}</div> });
    return result;
  }, [meta]); // eslint-disable-line react-hooks/exhaustive-deps

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>{title[0]}</Title><Text type="secondary">{title[1]}</Text></div><div className={styles.toolbar}><Input allowClear prefix={<SearchOutlined />} placeholder="Tìm kiếm..." value={search} onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, pagination.pageSize)} /><Button icon={<ReloadOutlined />} onClick={() => void load(pagination.current, pagination.pageSize)} />{meta?.can_create ? <Button type="primary" icon={<PlusOutlined />} onClick={openCreate}>Thêm mới</Button> : null}</div></header>
    <Card className={styles.card}><Table<Row> rowKey="id" loading={loading} columns={columns} dataSource={rows} scroll={{ x: 900 }} pagination={{ ...pagination, showSizeChanger: true, showTotal: total => `${total} dữ liệu` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} /></Card>
    <Modal width={760} open={modalOpen} title={editing ? `Chỉnh sửa #${editing.id}` : `Thêm ${title[0]}`} okText="Lưu" cancelText="Hủy" confirmLoading={saving} onOk={() => void save()} onCancel={() => setModalOpen(false)} destroyOnHidden>
      <Form form={form} layout="vertical"><div className={styles.formGrid}>{(meta?.editable || []).map(field => <Form.Item className={['content', 'description'].includes(field) ? styles.wide : undefined} name={field} label={labels[field] || field.replaceAll('_', ' ')} key={field}>{field === 'status' ? <Select allowClear options={['active', 'inactive', 'Pending', 'Processing', 'Completed', 'Canceled'].map(value => ({ value, label: value }))} /> : ['content', 'description', 'note'].includes(field) ? <Input.TextArea rows={6} /> : <Input />}</Form.Item>)}</div></Form>
    </Modal>
  </main>;
}
