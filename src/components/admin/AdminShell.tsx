'use client';

import { useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { Avatar, Dropdown, Layout, Menu, Spin, Typography } from 'antd';import { message } from '@/lib/antd-message';
import {
  ApiOutlined, AppstoreOutlined, BankOutlined, BellOutlined, DashboardOutlined,
  DollarOutlined, FileTextOutlined, GlobalOutlined, HistoryOutlined,
  MessageOutlined, PercentageOutlined, SettingOutlined,
  ShoppingCartOutlined, TagsOutlined, TeamOutlined, UserOutlined, WalletOutlined, CustomerServiceOutlined,
} from '@ant-design/icons';
import type { MenuProps } from 'antd';
import api from '@/lib/axios';
import styles from './AdminShell.module.css';

const { Content, Header, Sider } = Layout;
const { Text } = Typography;

const link = (href: string, label: string) => <Link href={href}>{label}</Link>;

const menuItems: MenuProps['items'] = [
  { key: '/admin/dashboard', icon: <DashboardOutlined />, label: link('/admin/dashboard', 'Bảng thống kê') },
  { key: 'smm', icon: <AppstoreOutlined />, label: 'Dịch vụ SMM', children: [
    { key: '/admin/platforms', label: link('/admin/platforms', 'Nền tảng') },
    { key: '/admin/categories', label: link('/admin/categories', 'Phân loại') },
    { key: '/admin/services', label: link('/admin/services', 'Dịch vụ') },
    { key: '/admin/discounts', label: link('/admin/discounts', 'Mã giảm giá') },
    { key: '/admin/providers', label: link('/admin/providers', 'Kết nối API') },
    { key: '/admin/services/update-rates', label: link('/admin/services/update-rates', 'Cập nhật giá') },
    { key: '/admin/orders', label: link('/admin/orders', 'Đơn hàng') },
  ] },
  { key: 'products', icon: <ShoppingCartOutlined />, label: 'Sản phẩm', children: [
    { key: '/admin/products', label: link('/admin/products', 'Danh sách sản phẩm') },
    { key: '/admin/product-categories', label: link('/admin/product-categories', 'Danh mục sản phẩm') },
    { key: '/admin/product-orders', label: link('/admin/product-orders', 'Đơn hàng sản phẩm') },
  ] },
  { key: 'payments', icon: <WalletOutlined />, label: 'Nạp tiền', children: [
    { key: '/admin/banks', label: link('/admin/banks', 'Tài khoản ngân hàng') },
    { key: '/admin/deposits', label: link('/admin/deposits', 'Lịch sử nạp tiền') },
    { key: '/admin/api-keys', label: link('/admin/api-keys', 'Cấu hình API Key') },
  ] },
  { key: '/admin/bonuses', icon: <PercentageOutlined />, label: link('/admin/bonuses', 'Tiền thưởng') },
  { key: '/admin/posts', icon: <FileTextOutlined />, label: link('/admin/posts', 'Bài viết') },
  { key: '/admin/affiliates', icon: <TeamOutlined />, label: link('/admin/affiliates', 'Giới thiệu') },
  { key: '/admin/tickets', icon: <MessageOutlined />, label: link('/admin/tickets', 'Tickets') },
  { key: '/admin/childpanels', icon: <GlobalOutlined />, label: link('/admin/childpanels', 'Website riêng') },
  { key: '/admin/facebook-tokens', icon: <ApiOutlined />, label: link('/admin/facebook-tokens', 'Facebook Token') },
  { key: '/admin/users', icon: <UserOutlined />, label: link('/admin/users', 'Thành viên') },
  { key: '/admin/transactions', icon: <HistoryOutlined />, label: link('/admin/transactions', 'Giao dịch') },
  { type: 'divider' },
  { key: '/admin/notifications', icon: <BellOutlined />, label: link('/admin/notifications', 'Thông báo') },
  { key: '/admin/contact-widgets', icon: <CustomerServiceOutlined />, label: link('/admin/contact-widgets', 'Widget liên hệ') },
  { key: '/admin/settings', icon: <SettingOutlined />, label: link('/admin/settings', 'Cấu hình website') },
  { key: '/admin/telegram', icon: <ApiOutlined />, label: link('/admin/telegram', 'Telegram & 2FA') },
  { key: '/admin/currencies', icon: <DollarOutlined />, label: link('/admin/currencies', 'Tiền tệ') },
  { key: '/admin/system', icon: <TagsOutlined />, label: link('/admin/system', 'Cập nhật hệ thống') },
];

export default function AdminShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const [collapsed, setCollapsed] = useState(false);
  const [mobile, setMobile] = useState(false);
  const [ready, setReady] = useState(false);
  const [user, setUser] = useState<{ username?: string; role?: string; avatar?: string } | null>(null);
  const [config, setConfig] = useState<{ title?: string; logo?: string } | null>(null);

  useEffect(() => {
    const verify = async () => {
      try {
        const response = await api.get('/auth/api/me');
        const current = response.data.user;
        if (current?.role !== 'admin') throw new Error('forbidden');
        setUser(current);
        
        api.get('/client/config').then(res => {
          if (res.data?.status) setConfig(res.data.data);
        }).catch(() => undefined);

        setReady(true);
      } catch {
        message.error('Vui lòng đăng nhập bằng tài khoản quản trị.');
        router.replace('/login');
      }
    };
    void verify();
  }, [router]);

  const selected = useMemo(() => [pathname], [pathname]);
  const initialOpenKeys = useMemo(() => {
    if (pathname.includes('/platforms') || pathname.includes('/categories') || pathname.includes('/services') || pathname.includes('/discounts') || pathname.includes('/providers') || pathname.includes('/orders')) return ['smm'];
    if (pathname.includes('/products') || pathname.includes('/product-categories') || pathname.includes('/product-orders')) return ['products'];
    if (pathname.includes('/banks') || pathname.includes('/deposits') || pathname.includes('/api-keys')) return ['payments'];
    return [];
  }, []);

  if (!ready) return <div className={styles.loading}><Spin size="large" /></div>;

  const logoUrl = config?.logo ? (config.logo.startsWith('http') ? config.logo : (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000').replace(/\/api\/?$/, '') + config.logo) : '';

  return <Layout className={styles.root}>
    <Sider className={styles.sider} width={260} collapsedWidth={mobile ? 0 : 80} collapsed={collapsed} breakpoint="md" onBreakpoint={(value) => { setMobile(value); setCollapsed(value); }} theme="dark">
      <div className={styles.brand}>
        {logoUrl ? (
          <img src={logoUrl} alt="Logo" style={{ maxHeight: 36, maxWidth: collapsed ? 40 : 180, objectFit: 'contain' }} />
        ) : (
          <><span className={styles.brandIcon}><BankOutlined /></span>{!collapsed ? (config?.title || 'SMM Admin') : null}</>
        )}
      </div>
      <Menu
        theme="dark"
        mode="inline"
        selectedKeys={selected}
        defaultOpenKeys={initialOpenKeys}
        items={menuItems}
        onClick={() => {
          if (mobile) setCollapsed(true);
        }}
      />
    </Sider>
    {mobile && !collapsed ? (
      <button
        type="button"
        className={styles.mobileBackdrop}
        aria-label="Đóng menu quản trị"
        onClick={() => setCollapsed(true)}
      />
    ) : null}
    <Layout className={`${styles.main} ${collapsed ? styles.collapsedMain : ''}`}>
      <Header className={styles.header}>
        <div className={styles.headerRight}>
          <div className={styles.user}><Text strong>{user?.username}</Text><small>Quản trị viên</small></div>
          <Dropdown menu={{ items: [{ key: 'client', label: <Link href="/new">Về trang khách hàng</Link> }, { key: 'logout', danger: true, label: 'Đăng xuất', onClick: () => { localStorage.removeItem('token'); localStorage.removeItem('user'); router.replace('/login'); } }] }}>
            <Avatar src={user?.avatar} icon={<UserOutlined />} />
          </Dropdown>
        </div>
      </Header>
      <Content className={styles.content}>{children}</Content>
    </Layout>
  </Layout>;
}
