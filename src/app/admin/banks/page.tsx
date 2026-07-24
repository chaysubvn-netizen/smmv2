'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Form, Image, Input, Modal, Select, Space, Table, Tag, Typography, Upload } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, EditOutlined, PlusOutlined, UploadOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import type { UploadFile } from 'antd/es/upload/interface';
import api from '@/lib/axios';
import styles from './banks.module.css';

const { Text, Title } = Typography;
const asset = (path?: string) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';
const bankOptions = [
  { value: 'mbbank', label: 'Mbbank' }, { value: 'vietcombank', label: 'Vietcombank' },
  { value: 'acb', label: 'ACB' }, { value: 'viettinbank', label: 'Viettinbank' },
  { value: 'ocb', label: 'OCB' },
];

type Bank = { id: number; icon?: string; bank_name: string; bank_code: string; account_number: string; account_name: string; branch: string; status: string; created_at: string };
type BankForm = Omit<Bank, 'id' | 'icon' | 'created_at'> & { icon?: UploadFile[] };

export default function AdminBanksPage() {
  const [form] = Form.useForm<BankForm>();
  const [rows, setRows] = useState<Bank[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [editing, setEditing] = useState<Bank | null | undefined>(undefined);
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/resources/banks', { params: { page: current, per_page: pageSize } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách ngân hàng.'); }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const create = () => { setEditing(null); form.resetFields(); form.setFieldsValue({ bank_name: 'mbbank', status: 'active', icon: [] }); };
  const edit = (bank: Bank) => { setEditing(bank); form.setFieldsValue({ bank_name: bank.bank_name, bank_code: bank.bank_code, account_number: bank.account_number, account_name: bank.account_name, branch: bank.branch, status: bank.status, icon: [] }); };
  const save = async () => {
    try {
      const values = await form.validateFields();
      setSaving(true);
      const body = new FormData();
      body.append('bank_name', values.bank_name); body.append('bank_code', values.bank_code);
      body.append('account_number', values.account_number); body.append('account_name', values.account_name);
      body.append('branch', values.branch); body.append('status', values.status);
      const file = values.icon?.[0]?.originFileObj;
      if (file) body.append('icon', file, file.name);
      const config = { headers: { 'Content-Type': 'multipart/form-data' } };
      const response = editing ? await api.post(`/admin/banks/${editing.id}`, body, config) : await api.post('/admin/banks', body, config);
      message.success(response.data.message || 'Đã lưu tài khoản ngân hàng.');
      setEditing(undefined); await load(page.current, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể lưu tài khoản ngân hàng.');
    } finally { setSaving(false); }
  };
  const remove = (bank: Bank) => Modal.confirm({ title: `Xóa tài khoản ${bank.account_number}?`, content: 'Dữ liệu và icon ngân hàng sẽ bị xóa vĩnh viễn.', okText: 'Xóa', okButtonProps: { danger: true }, cancelText: 'Hủy', onOk: async () => { await api.delete(`/admin/banks/${bank.id}`); message.success('Đã xóa tài khoản ngân hàng.'); await load(page.current, page.pageSize); } });

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 70 },
    { title: 'Thao tác', width: 125, render: (_: unknown, bank: Bank) => <Space><Button type="primary" size="small" icon={<EditOutlined />} onClick={() => edit(bank)} /><Button danger type="text" size="small" icon={<DeleteOutlined />} onClick={() => remove(bank)} /></Space> },
    { title: 'Icon', width: 110, render: (_: unknown, bank: Bank) => bank.icon ? <Image className={styles.bankIcon} preview={false} src={asset(bank.icon)} alt={bank.bank_name} width={54} height={42} /> : <span className={styles.iconFallback}>{bank.bank_code || bank.bank_name.slice(0, 3)}</span> },
    { title: 'Tên ngân hàng', dataIndex: 'bank_name', width: 180, render: (value: string) => <Tag color="blue">{value}</Tag> },
    { title: 'Chủ tài khoản', dataIndex: 'account_name', width: 220 },
    { title: 'Số tài khoản', dataIndex: 'account_number', width: 170 },
    { title: 'Chi nhánh', dataIndex: 'branch', width: 160 },
    { title: 'Trạng thái', dataIndex: 'status', width: 150, render: (value: string) => <Tag color={value === 'active' ? 'success' : 'error'}>{value === 'active' ? 'Kích hoạt' : 'Không kích hoạt'}</Tag> },
    { title: 'Ngày tạo', dataIndex: 'created_at', width: 140, render: (value: string) => new Date(value).toLocaleDateString('vi-VN') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Tài khoản ngân hàng</Title><Text type="secondary">Quản lý tài khoản nhận tiền chuyển khoản</Text></div></header>
    <Card className={styles.card} title="Danh sách ngân hàng" extra={<Button type="primary" icon={<PlusOutlined />} onClick={create}>Thêm mới</Button>}>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1325 }} pagination={{ ...page, showSizeChanger: true, showTotal: total => `${total} tài khoản` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
    <Modal width={560} open={editing !== undefined} title={editing ? 'Chỉnh sửa tài khoản ngân hàng' : 'Thêm thông tin mới'} okText="Lưu" cancelText="Đóng" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(undefined)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <Form.Item name="icon" label="Ảnh/Icon" valuePropName="fileList" getValueFromEvent={event => event?.fileList} rules={editing ? [] : [{ required: true, message: 'Vui lòng chọn ảnh/icon.' }]}><Upload beforeUpload={() => false} maxCount={1} accept="image/png,image/jpeg,image/gif,image/svg+xml,image/webp" listType="picture"><Button icon={<UploadOutlined />}>Chọn tệp</Button></Upload></Form.Item>
        {editing?.icon && <Image className={styles.preview} preview={false} src={asset(editing.icon)} alt={editing.bank_name} width={90} />}
        <Form.Item name="bank_name" label="Tên ngân hàng" rules={[{ required: true }]}><Select options={bankOptions} /></Form.Item>
        <Form.Item name="bank_code" label="Mã ngân hàng (VietQR)" extra="Xem mã tại vietqr.io/danh-sach-bin-ngan-hang" rules={[{ required: true, message: 'Nhập mã ngân hàng.' }]}><Input placeholder="Ví dụ: MB, VCB, ICB, ACB..." /></Form.Item>
        <Form.Item name="account_number" label="Số tài khoản" rules={[{ required: true, message: 'Nhập số tài khoản.' }]}><Input placeholder="Nhập số tài khoản" /></Form.Item>
        <Form.Item name="account_name" label="Tên tài khoản" rules={[{ required: true, message: 'Nhập tên tài khoản.' }]}><Input placeholder="Nhập tên tài khoản" /></Form.Item>
        <Form.Item name="branch" label="Chi nhánh" rules={[{ required: true, message: 'Nhập chi nhánh.' }]}><Input placeholder="Nhập chi nhánh" /></Form.Item>
        <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={[{ value: 'active', label: 'Hoạt động' }, { value: 'inactive', label: 'Ngừng hoạt động' }]} /></Form.Item>
      </Form>
    </Modal>
  </main>;
}
