'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, Form, Image, Input, Modal, Select, Space, Table, Tag, Typography, Upload } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, EditOutlined, EyeOutlined, PlusOutlined, ReloadOutlined, SearchOutlined, UploadOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import type { UploadFile } from 'antd/es/upload/interface';
import dayjs from 'dayjs';
import api from '@/lib/axios';
import styles from './posts.module.css';

const { Text, Title } = Typography;
const { TextArea } = Input;
const asset = (path?: string | null) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';

type Post = { id: number; title: string; slug?: string; content: string; image?: string | null; status: 'active' | 'inactive'; created_at: string; user?: { id: number; username: string } };
type PostForm = { title: string; content: string; status: 'active' | 'inactive'; image?: UploadFile[] };

export default function AdminPostsPage() {
  const [form] = Form.useForm<PostForm>();
  const [rows, setRows] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [editing, setEditing] = useState<Post | null | undefined>(undefined);
  const [viewing, setViewing] = useState<Post | null>(null);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState<string>();
  const [page, setPage] = useState({ current: 1, pageSize: 20, total: 0 });

  const load = useCallback(async (current = 1, pageSize = 20) => {
    setLoading(true);
    try {
      const response = await api.get('/admin/posts', { params: { page: current, per_page: pageSize, search: search.trim() || undefined, status } });
      const data = response.data.data;
      setRows(data.data || []);
      setPage({ current: data.current_page || current, pageSize: data.per_page || pageSize, total: data.total || 0 });
    } catch { message.error('Không thể tải danh sách bài viết.'); }
    finally { setLoading(false); }
  }, [search, status]);

  useEffect(() => { void Promise.resolve().then(() => load()); }, [load]);

  const create = () => {
    setEditing(null);
    form.resetFields();
    form.setFieldsValue({ status: 'active', image: [] });
  };
  const edit = (post: Post) => {
    setEditing(post);
    form.setFieldsValue({ title: post.title, content: post.content, status: post.status, image: [] });
  };
  const save = async () => {
    try {
      const values = await form.validateFields();
      setSaving(true);
      const body = new FormData();
      body.append('title', values.title);
      body.append('content', values.content);
      body.append('status', values.status);
      const file = values.image?.[0]?.originFileObj;
      if (file) body.append('image', file, file.name);
      const response = editing
        ? await api.post(`/admin/posts/${editing.id}`, body, { headers: { 'Content-Type': 'multipart/form-data' } })
        : await api.post('/admin/posts', body, { headers: { 'Content-Type': 'multipart/form-data' } });
      message.success(response.data.message || 'Đã lưu bài viết.');
      setEditing(undefined);
      await load(page.current, page.pageSize);
    } catch (error: unknown) {
      if (typeof error === 'object' && error && 'errorFields' in error) return;
      const detail = error as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const firstError = detail.response?.data?.errors ? Object.values(detail.response.data.errors)[0]?.[0] : undefined;
      message.error(firstError || detail.response?.data?.message || 'Không thể lưu bài viết.');
    } finally { setSaving(false); }
  };
  const remove = (post: Post) => Modal.confirm({
    title: `Xóa bài viết #${post.id}?`, content: post.title, okText: 'Xóa', cancelText: 'Hủy', okButtonProps: { danger: true },
    onOk: async () => { await api.delete(`/admin/posts/${post.id}`); message.success('Đã xóa bài viết.'); await load(page.current, page.pageSize); },
  });

  const columns = [
    { title: 'ID', dataIndex: 'id', width: 65 },
    { title: 'Thao tác', width: 145, render: (_: unknown, post: Post) => <Space size={4}><Button size="small" type="primary" icon={<EditOutlined />} onClick={() => edit(post)} /><Button size="small" icon={<EyeOutlined />} onClick={() => setViewing(post)} /><Button size="small" danger type="text" icon={<DeleteOutlined />} onClick={() => remove(post)} /></Space> },
    { title: 'Ảnh bìa', width: 150, render: (_: unknown, post: Post) => post.image ? <Image className={styles.cover} src={asset(post.image)} alt={post.title} width={100} height={58} /> : <div className={styles.noImage}>Không có ảnh</div> },
    { title: 'Tiêu đề', dataIndex: 'title', width: 380, render: (value: string, post: Post) => <button className={styles.titleLink} onClick={() => setViewing(post)}>{value}</button> },
    { title: 'Trạng thái', dataIndex: 'status', width: 125, render: (value: string) => <Tag color={value === 'active' ? 'success' : 'error'}>{value === 'active' ? 'Hiển thị' : 'Ẩn'}</Tag> },
    { title: 'Người viết', width: 150, render: (_: unknown, post: Post) => post.user?.username || <Text type="danger">Unknown</Text> },
    { title: 'Thời gian', dataIndex: 'created_at', width: 175, render: (value: string) => dayjs(value).format('DD/MM/YYYY HH:mm:ss') },
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Quản lý bài viết</Title><Text type="secondary">Tạo và quản lý nội dung hiển thị ngoài website</Text></div></header>
    <Card className={styles.card} title="Danh sách bài viết" extra={<Button type="primary" icon={<PlusOutlined />} onClick={create}>Thêm bài viết</Button>}>
      <div className={styles.filters}><Input allowClear value={search} prefix={<SearchOutlined />} placeholder="Tìm tiêu đề hoặc nội dung..." onChange={event => setSearch(event.target.value)} onPressEnter={() => void load(1, page.pageSize)} /><Select allowClear value={status} placeholder="Tất cả trạng thái" onChange={setStatus} options={[{ value: 'active', label: 'Hiển thị' }, { value: 'inactive', label: 'Ẩn' }]} /><Button icon={<ReloadOutlined />} onClick={() => void load(page.current, page.pageSize)}>Làm mới</Button></div>
      <Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1190 }} pagination={{ ...page, showSizeChanger: true, locale: { items_per_page: '/ trang' }, showTotal: total => `${total} bài viết` }} onChange={(next: TablePaginationConfig) => void load(next.current || 1, next.pageSize || 20)} />
    </Card>
    <Modal width={780} open={editing !== undefined} title={editing ? 'Chỉnh sửa bài viết' : 'Thêm bài viết'} okText="Lưu" cancelText="Đóng" confirmLoading={saving} onOk={() => void save()} onCancel={() => setEditing(undefined)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <Form.Item name="title" label="Tiêu đề bài viết" rules={[{ required: true, message: 'Nhập tiêu đề bài viết.' }]}><Input placeholder="Nhập tiêu đề bài viết" /></Form.Item>
        <Form.Item name="content" label="Nội dung bài viết" rules={[{ required: true, message: 'Nhập nội dung bài viết.' }]} extra="Có thể nhập nội dung HTML như giao diện Laravel."><TextArea className={styles.editor} rows={11} placeholder="Nhập nội dung bài viết..." /></Form.Item>
        <Form.Item name="image" label="Ảnh bìa" valuePropName="fileList" getValueFromEvent={event => event?.fileList} rules={editing ? [] : [{ required: true, message: 'Chọn ảnh bìa.' }]}><Upload beforeUpload={() => false} maxCount={1} accept="image/jpeg,image/png,image/gif" listType="picture"><Button icon={<UploadOutlined />}>Chọn ảnh</Button></Upload></Form.Item>
        {editing?.image && <div className={styles.currentImage}><Text type="secondary">Ảnh hiện tại</Text><Image src={asset(editing.image)} alt={editing.title} width={150} /></div>}
        <Form.Item name="status" label="Trạng thái" rules={[{ required: true }]}><Select options={[{ value: 'active', label: 'Hiển thị' }, { value: 'inactive', label: 'Ẩn' }]} /></Form.Item>
      </Form>
    </Modal>
    <Modal width={850} open={Boolean(viewing)} title={viewing?.title} footer={<Button onClick={() => setViewing(null)}>Đóng</Button>} onCancel={() => setViewing(null)}>
      {viewing?.image && <Image className={styles.detailCover} src={asset(viewing.image)} alt={viewing.title} />}
      {viewing && <div className={styles.detailMeta}><Tag color={viewing.status === 'active' ? 'success' : 'error'}>{viewing.status === 'active' ? 'Hiển thị' : 'Ẩn'}</Tag><span>{viewing.user?.username || 'Unknown'}</span><span>{dayjs(viewing.created_at).format('DD/MM/YYYY HH:mm:ss')}</span></div>}
      {viewing && <article className={styles.content} dangerouslySetInnerHTML={{ __html: viewing.content }} />}
    </Modal>
  </main>;
}
