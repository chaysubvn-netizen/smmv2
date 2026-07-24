'use client';

import { ArrowLeftOutlined, DollarOutlined, LockOutlined, MinusCircleOutlined, PlusCircleOutlined, SaveOutlined, UserOutlined } from '@ant-design/icons';
import { Alert, Button, Card, Form, Input, InputNumber, Select, Spin, Table, Tag, Typography } from 'antd';
import type { ColumnsType } from 'antd/es/table';
import dayjs from 'dayjs';
import { useParams, useRouter } from 'next/navigation';
import { useCallback, useEffect, useState } from 'react';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from './user-edit.module.css';

const { Text, Title } = Typography;

type UserDetail = {
  id: number; username: string; email: string; phone?: string; api_key?: string;
  balance: number; total_deposit: number; level: string; role: string; status: string;
  currency: string; commission: number; total_commission: number; last_login?: string; created_at: string;
};
type Transaction = { id:number; transaction_code:string; type:'add'|'sub'; balance_before:number; amount:number; balance_after:number; description?:string; status:string; created_at:string };
type BalanceValues = { amount:number; reason?:string };

const money = (value: number) => `${Math.round(Number(value || 0)).toLocaleString('vi-VN')}đ`;
const levelOptions = [
  { value: 'member', label: 'Thành viên' }, { value: 'silver', label: 'Rank Bạc' },
  { value: 'gold', label: 'Rank Vàng' }, { value: 'platinum', label: 'Rank Bạch Kim' },
  { value: 'diamond', label: 'Rank Kim Cương' },
];
const statusOptions = [
  { value: 'active', label: 'Hoạt động' }, { value: 'inactive', label: 'Ngừng hoạt động' },
  { value: 'suspended', label: 'Tạm ngưng' },
];

export default function AdminUserEditPage() {
  const params = useParams<{ id: string }>();
  const router = useRouter();
  const [editForm] = Form.useForm();
  const [addForm] = Form.useForm<BalanceValues>();
  const [subForm] = Form.useForm<BalanceValues>();
  const [user, setUser] = useState<UserDetail | null>(null);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [balanceAction, setBalanceAction] = useState<'add'|'sub'|null>(null);
  const [error, setError] = useState('');

  const load = useCallback(async () => {
    setLoading(true); setError('');
    try {
      const response = await api.get(`/admin/users/${params.id}`);
      const result = response.data.data;
      setUser(result.user); setTransactions(result.transactions || []);
    } catch (requestError: any) {
      setError(requestError.response?.status === 404 ? 'Không tìm thấy tài khoản trong website này.' : 'Không thể tải thông tin thành viên.');
    } finally { setLoading(false); }
  }, [editForm, params.id]);

  useEffect(() => { void load(); }, [load]);
  useEffect(() => {
    if (!user) return;
    editForm.setFieldsValue({ level: user.level, status: user.status, total_commission: user.total_commission || 0, new_password: '' });
  }, [editForm, user]);

  const saveUser = async () => {
    setSaving(true);
    try {
      await api.put(`/admin/users/${params.id}`, await editForm.validateFields());
      message.success('Đã cập nhật tài khoản thành công.'); await load();
    } catch (requestError: any) {
      if (!requestError?.errorFields) message.error(requestError.response?.data?.message || 'Không thể lưu thay đổi.');
    } finally { setSaving(false); }
  };

  const updateBalance = async (action: 'add'|'sub', form: ReturnType<typeof Form.useForm<BalanceValues>>[0]) => {
    setBalanceAction(action);
    try {
      const values = await form.validateFields();
      await api.post(`/admin/users/${params.id}/balance`, { action, ...values });
      message.success(action === 'add' ? 'Đã cộng tiền tài khoản.' : 'Đã trừ tiền tài khoản.');
      form.resetFields(); await load();
    } catch (requestError: any) {
      if (!requestError?.errorFields) message.error(requestError.response?.data?.message || 'Không thể cập nhật số dư.');
    } finally { setBalanceAction(null); }
  };

  const columns: ColumnsType<Transaction> = [
    { title: '#', dataIndex: 'id', width: 70 },
    { title: 'Mã giao dịch', dataIndex: 'transaction_code', width: 150, render: value => <Text code>{value}</Text> },
    { title: 'Số dư trước', dataIndex: 'balance_before', width: 150, render: money },
    { title: 'Số tiền', dataIndex: 'amount', width: 145, render: (value, row) => <b className={row.type === 'add' ? styles.addMoney : styles.subMoney}>{row.type === 'add' ? '+' : '-'}{money(value)}</b> },
    { title: 'Số dư sau', dataIndex: 'balance_after', width: 150, render: money },
    { title: 'Nội dung', dataIndex: 'description', width: 260, render: value => value || '—' },
    { title: 'Trạng thái', dataIndex: 'status', width: 130, render: value => <Tag color={value === 'success' ? 'success' : value === 'pending' ? 'warning' : 'error'}>{value === 'success' ? 'Thành công' : value === 'pending' ? 'Đang xử lý' : 'Thất bại'}</Tag> },
    { title: 'Thời gian', dataIndex: 'created_at', width: 175, render: value => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  if (loading && !user) return <div className={styles.loading}><Spin size="large" /></div>;
  if (error || !user) return <Alert type="error" showIcon title={error || 'Không tìm thấy thành viên.'} action={<Button onClick={() => router.push('/admin/users')}>Quay lại</Button>} />;

  return <main className={styles.page}>
    <header className={styles.heading}><div><Button type="text" icon={<ArrowLeftOutlined />} onClick={() => router.push('/admin/users')}>Thành viên</Button><Title level={2}>Chỉnh sửa người dùng</Title><Text type="secondary">Quản lý tài khoản #{user.id} · {user.username}</Text></div></header>

    <Card className={styles.card} title={<span><UserOutlined /> Chỉnh sửa thông tin</span>}>
      <Form form={editForm} layout="vertical" requiredMark={false}>
        <div className={styles.infoGrid}>
          <Form.Item label="Tài khoản"><Input value={user.username} readOnly /></Form.Item>
          <Form.Item label="Địa chỉ Email"><Input value={user.email} readOnly /></Form.Item>
          <Form.Item label="Thời gian đăng ký"><Input value={dayjs(user.created_at).format('DD/MM/YYYY HH:mm:ss')} readOnly /></Form.Item>
          <Form.Item label="API Key"><Input.Password value={user.api_key || ''} readOnly /></Form.Item>
          <Form.Item label="Số dư"><Input value={money(user.balance)} readOnly /></Form.Item>
          <Form.Item label="Tổng nạp"><Input value={money(user.total_deposit)} readOnly /></Form.Item>
          <Form.Item name="level" label="Cấp bậc thành viên" rules={[{ required: true }]}><Select options={levelOptions} /></Form.Item>
          <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={statusOptions} /></Form.Item>
          <Form.Item label="Hoa hồng giới thiệu"><Input value={user.commission || 0} readOnly /></Form.Item>
          <Form.Item name="total_commission" label="Tổng số hoa hồng"><InputNumber min={0} style={{ width: '100%' }} /></Form.Item>
        </div>
        <Form.Item name="new_password" label="Mật khẩu mới"><Input.Password prefix={<LockOutlined />} placeholder="Nhập mật khẩu mới nếu muốn thay đổi" /></Form.Item>
        <Button type="primary" icon={<SaveOutlined />} loading={saving} onClick={() => void saveUser()}>Lưu thay đổi</Button>
      </Form>
    </Card>

    <section className={styles.balanceGrid}>
      <BalanceCard type="add" form={addForm} loading={balanceAction === 'add'} onSubmit={() => void updateBalance('add', addForm)} />
      <BalanceCard type="sub" form={subForm} loading={balanceAction === 'sub'} onSubmit={() => void updateBalance('sub', subForm)} />
    </section>

    <Card className={styles.card} title="Lịch sử hoạt động gần đây">
      <Table rowKey="id" columns={columns} dataSource={transactions} loading={loading} scroll={{ x: 1230 }} pagination={{ pageSize: 20, showSizeChanger: true, showTotal: total => `${total} giao dịch` }} />
    </Card>
  </main>;
}

function BalanceCard({ type, form, loading, onSubmit }: { type:'add'|'sub'; form:ReturnType<typeof Form.useForm<BalanceValues>>[0]; loading:boolean; onSubmit:()=>void }) {
  const adding = type === 'add';
  return <Card className={`${styles.card} ${adding ? styles.addCard : styles.subCard}`} title={<span>{adding ? <PlusCircleOutlined /> : <MinusCircleOutlined />} {adding ? 'Cộng tiền tài khoản' : 'Trừ tiền tài khoản'}</span>}>
    <Form form={form} layout="vertical" requiredMark={false}>
      <Form.Item name="amount" label="Số tiền" rules={[{ required:true, message:'Vui lòng nhập số tiền.' }]}><InputNumber min={1} precision={0} prefix={<DollarOutlined />} placeholder={adding ? 'Nhập số tiền muốn cộng' : 'Nhập số tiền muốn trừ'} style={{ width:'100%' }} /></Form.Item>
      <Form.Item name="reason" label="Lý do"><Input.TextArea rows={3} placeholder={adding ? 'Nhập lý do cộng tiền' : 'Nhập lý do trừ tiền'} /></Form.Item>
      <Button type="primary" danger={!adding} icon={adding ? <PlusCircleOutlined /> : <MinusCircleOutlined />} loading={loading} onClick={onSubmit}>{adding ? 'Cộng tiền' : 'Trừ tiền'}</Button>
    </Form>
  </Card>;
}
