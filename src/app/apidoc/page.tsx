'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { ApiOutlined, CheckOutlined, CopyOutlined, EyeInvisibleOutlined, EyeOutlined, ReloadOutlined } from '@ant-design/icons';
import { App, Button, Card, Modal, Spin, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import api from '@/lib/axios';
import ClientLayout from '@/components/ClientLayout';
import styles from './apidoc.module.css';

const { Text, Title } = Typography;

type DocItem = {
  id: string;
  label: string;
  title: string;
  description: string;
  params: { name: string; required?: boolean; description: string; example?: string }[];
  response: string;
  note?: string;
};

const docs: DocItem[] = [
  { id: 'services', label: 'Danh sách dịch vụ', title: 'Services', description: 'Lấy toàn bộ dịch vụ đang hoạt động cùng giá, giới hạn và thông tin bảo hành.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'services' }], response: `[
  {
    "service": 1,
    "name": "Youtube views",
    "type": "Default",
    "category": "Youtube",
    "rate": 2.5,
    "min": 200,
    "max": 10000,
    "refill": true,
    "cancel": false
  }
]` },
  { id: 'add', label: 'Tạo đơn hàng', title: 'Add order', description: 'Tạo một đơn hàng mới cho dịch vụ đã chọn.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'add' }, { name: 'service', required: true, description: 'ID dịch vụ', example: '123' }, { name: 'link', required: true, description: 'Liên kết cần chạy' }, { name: 'quantity', required: true, description: 'Số lượng cần đặt', example: '1000' }, { name: 'comments', description: 'Danh sách bình luận, mỗi dòng một nội dung' }], response: `{
  "order": 23501
}` },
  { id: 'status', label: 'Trạng thái đơn', title: 'Order status', description: 'Kiểm tra trạng thái của một đơn hàng.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'status' }, { name: 'order', required: true, description: 'ID đơn hàng', example: '23501' }], response: `{
  "23501": {
    "charge": 2.5,
    "start_count": 168,
    "status": "Completed",
    "remains": 0
  }
}`, note: 'Trạng thái: Pending, Processing, In progress, Completed, Partial, Canceled.' },
  { id: 'multistatus', label: 'Trạng thái nhiều đơn', title: 'Multiple orders status', description: 'Kiểm tra tối đa 100 đơn hàng trong một yêu cầu.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'status' }, { name: 'orders', required: true, description: 'Danh sách ID cách nhau bằng dấu phẩy', example: '123,456,789' }], response: `{
  "123": { "charge": 0.27, "status": "Partial", "remains": 157 },
  "789": { "charge": 1.44, "status": "In progress", "remains": 10 }
}` },
  { id: 'refill', label: 'Tạo bảo hành', title: 'Create refill', description: 'Gửi yêu cầu bảo hành cho một đơn đủ điều kiện.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'refill' }, { name: 'order', required: true, description: 'ID đơn hàng' }], response: `[
  { "order": 23501, "refill": true }
]` },
  { id: 'refills', label: 'Bảo hành nhiều đơn', title: 'Create multiple refill', description: 'Gửi yêu cầu bảo hành cho nhiều đơn trong một lần gọi.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'refill' }, { name: 'orders', required: true, description: 'Danh sách ID cách nhau bằng dấu phẩy', example: '123,456,789' }], response: `[
  { "order": 123, "refill": true },
  { "order": 456, "refill": { "error": "Refill not available" } }
]` },
  { id: 'refill_status', label: 'Trạng thái bảo hành', title: 'Refill status', description: 'Kiểm tra trạng thái một yêu cầu bảo hành.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'refill_status' }, { name: 'refill', required: true, description: 'ID bảo hành' }], response: `[
  { "refill": 123, "status": "Completed" }
]` },
  { id: 'refills_status', label: 'Trạng thái nhiều bảo hành', title: 'Multiple refill status', description: 'Kiểm tra trạng thái nhiều yêu cầu bảo hành.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'refill_status' }, { name: 'refills', required: true, description: 'Danh sách ID cách nhau bằng dấu phẩy', example: '123,456,789' }], response: `[
  { "refill": 123, "status": "Completed" },
  { "refill": 456, "status": { "error": "Incorrect ID" } }
]` },
  { id: 'balance', label: 'Số dư', title: 'Balance', description: 'Lấy số dư hiện tại của tài khoản API.', params: [{ name: 'key', required: true, description: 'API Key của bạn' }, { name: 'action', required: true, description: 'Tên hành động', example: 'balance' }], response: `{
  "balance": 68.6868,
  "currency": "USD"
}` },
];

export default function ApiDocPage() {
  const { modal } = App.useApp();
  const [active, setActive] = useState('services');
  const [apiKey, setApiKey] = useState('');
  const [showKey, setShowKey] = useState(false);
  const [regenerating, setRegenerating] = useState(false);
  const [loggedIn, setLoggedIn] = useState(false);
  const [authResolved, setAuthResolved] = useState(false);
  const [siteConfig, setSiteConfig] = useState<{ title?: string; logo?: string }>({});
  const apiUrl = `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/$/, '')}/v2`;
  const current = docs.find(item => item.id === active) || docs[0];
  const maskedKey = apiKey ? `${apiKey.slice(0, 6)}${'•'.repeat(10)}${apiKey.slice(-4)}` : 'Đăng nhập để xem API Key';

  useEffect(() => {
    void Promise.resolve().then(() => {
      api.get('/client/config').then(response => {
        if (response.data?.status) setSiteConfig(response.data.data || {});
      }).catch(() => undefined);
      const hasToken = Boolean(localStorage.getItem('token'));
      if (hasToken) {
        api.get('/client/profile')
          .then(response => { setApiKey(response.data?.data?.api_key || ''); setLoggedIn(true); })
          .catch(() => setLoggedIn(false))
          .finally(() => setAuthResolved(true));
      } else {
        setAuthResolved(true);
      }
      const hash = window.location.hash.slice(1);
      if (docs.some(item => item.id === hash)) setActive(hash);
    });
  }, []);

  const selectDoc = (id: string) => { setActive(id); window.history.replaceState(null, '', `#${id}`); };
  const copy = async (value: string, label: string) => { await navigator.clipboard.writeText(value); message.success(`Đã sao chép ${label}.`); };
  const regenerate = () => modal.confirm({
    title: 'Tạo API Key mới?', content: 'API Key hiện tại sẽ hết hiệu lực ngay lập tức.', okText: 'Tạo key mới', cancelText: 'Hủy',
    onOk: async () => { setRegenerating(true); try { const response = await api.post('/client/api-key/regenerate'); setApiKey(response.data.data.api_key); setShowKey(true); message.success(response.data.message); } finally { setRegenerating(false); } },
  });
  const logoUrl = siteConfig.logo?.startsWith('http')
    ? siteConfig.logo
    : siteConfig.logo
      ? `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${siteConfig.logo.startsWith('/') ? '' : '/'}${siteConfig.logo}`
      : '';

  if (!authResolved) return <div style={{ minHeight: '100vh', display: 'grid', placeItems: 'center', background: '#f5f7fb' }}><Spin size="large" /></div>;

  if (loggedIn) return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}><div><Title level={3}>Tài liệu API</Title><Text type="secondary">Kết nối hệ thống của bạn với SMM Panel qua API V2</Text></div><span><ApiOutlined /></span></div>
    <Card className={styles.connection} title="Thông tin kết nối">
      <div className={styles.connectionRow}><span>API URL</span><div><code>{apiUrl}</code><Button type="text" icon={<CopyOutlined />} onClick={() => copy(apiUrl, 'API URL')} /></div></div>
      <div className={styles.connectionRow}><span>API Key</span><div><code className={styles.key}>{showKey ? apiKey : maskedKey}</code><Button type="text" icon={showKey ? <EyeInvisibleOutlined /> : <EyeOutlined />} onClick={() => setShowKey(value => !value)} /><Button type="text" icon={<CopyOutlined />} disabled={!apiKey} onClick={() => copy(apiKey, 'API Key')} /><Button type="text" danger loading={regenerating} icon={<ReloadOutlined />} onClick={regenerate} /></div></div>
      <div className={styles.meta}><div><span>HTTP Method</span><Tag color="green">POST</Tag></div><div><span>Content-Type</span><b>application/x-www-form-urlencoded</b></div><div><span>Response</span><Tag color="blue">JSON</Tag></div></div>
    </Card>
    <Card className={styles.docsCard} styles={{ body: { padding: 0 } }}><div className={styles.docsLayout}>
      <nav className={styles.nav}>{docs.map(item => <button key={item.id} className={active === item.id ? styles.active : ''} onClick={() => selectDoc(item.id)}><span>{active === item.id ? <CheckOutlined /> : null}</span>{item.label}</button>)}</nav>
      <article className={styles.content}><div className={styles.title}><div><Tag color="green">POST</Tag><Title level={4}>{current.title}</Title></div><Text>{current.description}</Text></div><h3>Tham số yêu cầu</h3><div className={styles.tableWrap}><table><thead><tr><th>Tham số</th><th>Bắt buộc</th><th>Mô tả</th><th>Giá trị mẫu</th></tr></thead><tbody>{current.params.map(param => <tr key={param.name}><td><code>{param.name}</code></td><td>{param.required ? <Tag color="red">Bắt buộc</Tag> : <Tag>Tùy chọn</Tag>}</td><td>{param.description}</td><td>{param.example ? <code>{param.example}</code> : '—'}</td></tr>)}</tbody></table></div><div className={styles.responseHeader}><h3>Response mẫu</h3><Button size="small" icon={<CopyOutlined />} onClick={() => copy(current.response, 'response')}>Sao chép</Button></div><pre className={styles.code}><code>{current.response}</code></pre>{current.note ? <div className={styles.note}><b>Lưu ý:</b> {current.note}</div> : null}</article>
    </div></Card>
  </div></ClientLayout>;

  return <div className={styles.publicPage}>
    <header className={styles.publicHeader}>
      <Link href="/" className={styles.brand}>{logoUrl ? <img src={logoUrl} alt={siteConfig.title || 'Logo'} style={{ display: 'block', maxWidth: 160, maxHeight: 44, objectFit: 'contain' }} /> : <><span>S</span><b>{siteConfig.title || 'SMM Panel'}</b></>}</Link>
      <nav><Link href="/">Trang chủ</Link>{loggedIn ? <Link href="/new">Dashboard</Link> : <><Link href="/login">Đăng nhập</Link><Link href="/register" className={styles.register}>Đăng ký</Link></>}</nav>
    </header>
    <main className={`${styles.page} ${styles.contentWidth}`}>
    <div className={styles.heading}><div><Title level={3}>Tài liệu API</Title><Text type="secondary">Kết nối hệ thống của bạn với SMM Panel qua API V2</Text></div><span><ApiOutlined /></span></div>
    <Card className={styles.connection} title="Thông tin kết nối">
      <div className={styles.connectionRow}><span>API URL</span><div><code>{apiUrl}</code><Button type="text" icon={<CopyOutlined />} onClick={() => copy(apiUrl, 'API URL')} /></div></div>
      <div className={styles.connectionRow}><span>API Key</span><div><code className={styles.key}>{showKey && apiKey ? apiKey : maskedKey}</code>{loggedIn ? <><Button type="text" icon={showKey ? <EyeInvisibleOutlined /> : <EyeOutlined />} onClick={() => setShowKey(value => !value)} /><Button type="text" icon={<CopyOutlined />} disabled={!apiKey} onClick={() => copy(apiKey, 'API Key')} /><Button type="text" danger loading={regenerating} icon={<ReloadOutlined />} onClick={regenerate} /></> : <Link href="/login" className={styles.loginHint}>Lấy API Key</Link>}</div></div>
      <div className={styles.meta}><div><span>HTTP Method</span><Tag color="green">POST</Tag></div><div><span>Content-Type</span><b>application/x-www-form-urlencoded</b></div><div><span>Response</span><Tag color="blue">JSON</Tag></div></div>
    </Card>
    <Card className={styles.docsCard} styles={{ body: { padding: 0 } }}>
      <div className={styles.docsLayout}>
        <nav className={styles.nav}>{docs.map(item => <button key={item.id} className={active === item.id ? styles.active : ''} onClick={() => selectDoc(item.id)}><span>{active === item.id ? <CheckOutlined /> : null}</span>{item.label}</button>)}</nav>
        <article className={styles.content}>
          <div className={styles.title}><div><Tag color="green">POST</Tag><Title level={4}>{current.title}</Title></div><Text>{current.description}</Text></div>
          <h3>Tham số yêu cầu</h3>
          <div className={styles.tableWrap}><table><thead><tr><th>Tham số</th><th>Bắt buộc</th><th>Mô tả</th><th>Giá trị mẫu</th></tr></thead><tbody>{current.params.map(param => <tr key={param.name}><td><code>{param.name}</code></td><td>{param.required ? <Tag color="red">Bắt buộc</Tag> : <Tag>Tùy chọn</Tag>}</td><td>{param.description}</td><td>{param.example ? <code>{param.example}</code> : '—'}</td></tr>)}</tbody></table></div>
          <div className={styles.responseHeader}><h3>Response mẫu</h3><Button size="small" icon={<CopyOutlined />} onClick={() => copy(current.response, 'response')}>Sao chép</Button></div>
          <pre className={styles.code}><code>{current.response}</code></pre>
          {current.note ? <div className={styles.note}><b>Lưu ý:</b> {current.note}</div> : null}
        </article>
      </div>
    </Card>
    </main>
  </div>;
}
