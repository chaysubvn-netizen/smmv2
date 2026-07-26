'use client';

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { ConfigProvider, Spin } from 'antd';import { message } from '@/lib/antd-message';
import { useRouter, usePathname } from 'next/navigation';
import axios from 'axios';
import api from '@/lib/axios';
import {
  HomeOutlined,
  PlusCircleOutlined,
  PlusOutlined,
  ShoppingCartOutlined,
  AppstoreAddOutlined,
  AppstoreOutlined,
  ShopOutlined,
  DesktopOutlined,
  UnorderedListOutlined,
  SyncOutlined,
  RetweetOutlined,
  ReloadOutlined,
  WalletOutlined,
  LineChartOutlined,
  UsergroupAddOutlined,
  GlobalOutlined,
  PieChartOutlined,
  MessageOutlined,
  CodeOutlined,
  SettingOutlined,
  LogoutOutlined,
  HistoryOutlined,
  FileTextOutlined,
} from '@ant-design/icons';
import LanguageSwitcher from './LanguageSwitcher';
import MaintenanceScreen from './MaintenanceScreen';
import ContactWidget from './ContactWidget';

function readableTextColor(background: string) {
  const red = parseInt(background.slice(1, 3), 16);
  const green = parseInt(background.slice(3, 5), 16);
  const blue = parseInt(background.slice(5, 7), 16);
  const luminance = (red * 299 + green * 587 + blue * 114) / 1000;
  return luminance > 150 ? '#253044' : '#f4f6fa';
}

export default function ClientLayout({ children, loading = false }: { children: React.ReactNode; loading?: boolean }) {
  const [user, setUser] = useState<any>(null);
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const [isSidebarHidden, setIsSidebarHidden] = useState(false);
  const [isProfileOpen, setIsProfileOpen] = useState(false);
  const [currencies, setCurrencies] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [config, setConfig] = useState<any>(null);
  const [configLoaded, setConfigLoaded] = useState(false);
  const [indicatorStyle, setIndicatorStyle] = useState<React.CSSProperties>({ 
    opacity: 0, 
    top: 0, 
    height: 45 
  });
  const router = useRouter();
  const pathname = usePathname();
  const primaryColor = /^#[0-9a-f]{6}$/i.test(config?.theme_color_primary || '') ? config.theme_color_primary : '#1677ff';
  const sidebarColor = /^#[0-9a-f]{6}$/i.test(config?.theme_color_sidebar || '') ? config.theme_color_sidebar : '#10131a';
  const primaryText = readableTextColor(primaryColor);
  const sidebarText = readableTextColor(sidebarColor);

  useEffect(() => {
    const updateIndicator = () => {
      const activeItem = document.querySelector('.pc-navbar .pc-item.active') as HTMLElement;
      if (activeItem) {
        setIndicatorStyle({
          top: activeItem.offsetTop,
          height: activeItem.offsetHeight,
          opacity: 1,
        });
      } else {
        setIndicatorStyle({ opacity: 0, top: 0, height: 45 });
      }
    };
    
    updateIndicator();
    const t1 = setTimeout(updateIndicator, 100);
    const t2 = setTimeout(updateIndicator, 500);
    
    window.addEventListener('resize', updateIndicator);
    
    return () => {
      clearTimeout(t1);
      clearTimeout(t2);
      window.removeEventListener('resize', updateIndicator);
    };
  }, [pathname]);

  const fetchCurrencies = async () => {
    try {
      const res = await api.get('/client/currencies');
      if (res.data.status) {
        setCurrencies(res.data.data);
      }
    } catch (e) {}
  };

  const toImageUrl = (path: string | undefined) => {
    if (!path) return undefined;
    if (path.startsWith('http')) return path;
    const baseUrl = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
    return `${baseUrl}${path.startsWith('/') ? '' : '/'}${path}`;
  };

  const formatBalance = (amount: number | string | undefined) => {
    const currencyCode = user?.currency || 'VND';
    const selectedCurrency = currencies.find(currency => currency.code === currencyCode);
    const vndCurrency = currencies.find(currency => currency.code === 'VND');
    const selectedRate = Number(
      selectedCurrency?.rate ?? selectedCurrency?.exchange_rate ?? selectedCurrency?.currency_rate ?? selectedCurrency?.value ?? 1
    );
    const vndRate = Number(
      vndCurrency?.rate ?? vndCurrency?.exchange_rate ?? vndCurrency?.currency_rate ?? vndCurrency?.value ?? 1
    );
    const converted = vndRate > 0 ? (Number(amount || 0) / vndRate) * selectedRate : Number(amount || 0);

    if (currencyCode === 'VND') return `${Math.round(converted).toLocaleString('vi-VN')} đ`;
    return converted.toLocaleString('en-US', { style: 'currency', currency: currencyCode });
  };

  
  const fetchCategories = async () => {
    try {
      const res = await api.get('/client/categories');
      if (res.data && res.data.status) {
        setCategories(res.data.data);
      }
    } catch (e) {}
  };

  const fetchConfig = async () => {
    try {
      const res = await api.get('/client/config', { timeout: 10000 });
      if (res.data.status) {
        setConfig(res.data.data);
      }
    } catch (e) {
    } finally {
      setConfigLoaded(true);
    }
  };

  const fetchCurrentUser = async () => {
    const endpoints = ['/auth/api/me', '/auth/api/user', '/client/profile'];

    for (const endpoint of endpoints) {
      try {
        const res = await api.get(endpoint);
        const currentUser = res.data?.user || res.data?.data || res.data;

        if (currentUser && typeof currentUser === 'object' && 'balance' in currentUser) {
          setUser(currentUser);
          localStorage.setItem('user', JSON.stringify(currentUser));
          return;
        }
      } catch {
        // Thử endpoint tương thích tiếp theo
      }
    }
  };

  const handleChangeCurrency = async (code: string) => {
    try {
      const res = await api.post('/client/change-currency', { currency: code });
      if (res.data.status) {
        message.success(res.data.message || 'Đổi tiền tệ thành công');
        const updatedUser = {
          ...user,
          ...(res.data?.user || {}),
          currency: code,
          balance: res.data?.user?.balance ?? user?.balance,
        };

        setUser(updatedUser);
        localStorage.setItem('user', JSON.stringify(updatedUser));
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        message.error(res.data.message || 'Lỗi khi đổi tiền tệ');
      }
    } catch (e: any) {
      if (e.response && e.response.status === 422) {
        message.error('Mã tiền tệ không hợp lệ hoặc không tồn tại');
      } else {
        message.error(e.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại');
      }
    }
  };

  useEffect(() => {
    // Run feather replace to render data-feather icons
    if (typeof window !== 'undefined' && (window as any).feather) {
      setTimeout(() => {
        (window as any).feather.replace();
      }, 100);
    }
    // Auto-close sidebar on mobile when navigating
    setIsSidebarOpen(false);
  }, [pathname]);

  useEffect(() => {
    const token = localStorage.getItem('token');
    const userData = localStorage.getItem('user');
    void fetchConfig();
    void fetchCategories();

    if (!token || !userData) {
      setConfigLoaded(true);
      router.replace('/login');
      return;
    }

    try {
      const parsedUser = JSON.parse(userData);
      if (!parsedUser || typeof parsedUser !== 'object') throw new Error('Invalid user data');
      setUser(parsedUser);
    } catch {
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      setConfigLoaded(true);
      router.replace('/login');
      return;
    }
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    axios.defaults.headers.common['Accept'] = 'application/json';
    
    fetchCurrencies();
    fetchCurrentUser();
    const configTimer = window.setInterval(fetchConfig, 30000);
    const maintenanceListener = () => void fetchConfig();
    const balanceListener = () => void fetchCurrentUser();
    window.addEventListener('maintenance-mode-enabled', maintenanceListener);
    window.addEventListener('user-balance-updated', balanceListener);
    return () => {
      window.clearInterval(configTimer);
      window.removeEventListener('maintenance-mode-enabled', maintenanceListener);
      window.removeEventListener('user-balance-updated', balanceListener);
    };
  }, [router]);

  useEffect(() => {
    if (!config) return;
    const root = document.documentElement;
    root.style.setProperty('--client-primary', primaryColor);
    root.style.setProperty('--client-primary-text', primaryText);
    root.style.setProperty('--client-link', primaryColor);
    root.style.setProperty('--client-link-hover', primaryColor);
    root.style.setProperty('--client-sidebar', sidebarColor);
    root.style.setProperty('--client-sidebar-text', sidebarText);
  }, [config, primaryColor, primaryText, sidebarColor, sidebarText]);

  const handleLogout = async (e?: React.MouseEvent) => {
    e?.preventDefault();
    try {
      await api.post('/auth/api/logout');
    } catch (e) {}
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  };

  const isLayoutLoading = !user || !configLoaded || loading;

  if (configLoaded && config?.maintenance_mode === 'on') {
    return <MaintenanceScreen title={config.title} logo={toImageUrl(config.logo)} onLogout={() => void handleLogout()} />;
  }

  const renderNav1 = () => (
    <ul className="pc-navbar" style={{ position: 'relative', zIndex: 1 }}>
      <li 
        className="nav-active-indicator"
        style={{
          position: 'absolute',
          left: '15px',
          right: '15px',
          borderRadius: '8px',
          backgroundColor: 'color-mix(in srgb, var(--client-primary) 15%, transparent)',
          transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
          zIndex: -1,
          pointerEvents: 'none',
          ...indicatorStyle
        }}
      />
    
        <li className={`pc-item ${pathname === '/new' ? 'active' : ''}`}>
        <Link href="/new" className="pc-link">
          <span className="pc-micon">
            <PlusCircleOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext"> Đặt Hàng</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/mass' ? 'active' : ''}`}>
        <Link href="/mass" className="pc-link">
          <span className="pc-micon">
            <ShoppingCartOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Đặt hàng Sll</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/services' ? 'active' : ''}`}>
        <Link href="/services" className="pc-link">
          <span className="pc-micon">
            <AppstoreOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Bảng giá dịch vụ</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname.startsWith('/products') ? 'active' : ''}`}>
        <Link href="/products" className="pc-link">
          <span className="pc-micon">
            <ShoppingCartOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Sản phẩm</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/orders' ? 'active' : ''}`}>
        <Link href="/orders" className="pc-link">
          <span className="pc-micon">
            <UnorderedListOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Lịch sử đơn hàng</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/subscriptions' ? 'active' : ''}`}>
        <Link href="/subscriptions" className="pc-link">
          <span className="pc-micon">
            <SyncOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Subscriptions</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/drip-feed' ? 'active' : ''}`}>
        <Link href="/drip-feed" className="pc-link">
          <span className="pc-micon">
            <RetweetOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Đơn lặp lại</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/refill' ? 'active' : ''}`}>
        <Link href="/refill" className="pc-link">
          <span className="pc-micon">
            <ReloadOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Bảo hành</span>
        </Link>
      </li>
      <li className={`pc-item ${pathname === '/addfunds' ? 'active' : ''}`}>
        <Link href="/addfunds" className="pc-link">
          <span className="pc-micon">
            <WalletOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Nạp tiền</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/cashflow' ? 'active' : ''}`}>
        <Link href="/cashflow" className="pc-link">
          <span className="pc-micon">
            <LineChartOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Lịch sử dòng tiền</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/affiliate' ? 'active' : ''}`}>
        <Link href="/affiliate" className="pc-link">
          <span className="pc-micon">
            <UsergroupAddOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Tiếp thị liên kết</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/childpanel' ? 'active' : ''}`}>
        <Link href="/childpanel" className="pc-link">
          <span className="pc-micon">
            <GlobalOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext"> Tạo Website riêng</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/statistics' ? 'active' : ''}`}>
        <Link href="/statistics" className="pc-link">
          <span className="pc-micon">
            <PieChartOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Thống kê</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/update' ? 'active' : ''}`}>
        <Link href="/update" className="pc-link">
          <span className="pc-micon">
            <HistoryOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Cập nhật dịch vụ</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname.startsWith('/posts') ? 'active' : ''}`}>
        <Link href="/posts" className="pc-link">
          <span className="pc-micon">
            <FileTextOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Bài viết</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/tickets' ? 'active' : ''}`}>
        <Link href="/tickets" className="pc-link">
          <span className="pc-micon">
            <MessageOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Tạo ticket hỗ trợ</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/apidoc' ? 'active' : ''}`}>
        <Link href="/apidoc" className="pc-link">
          <span className="pc-micon">
            <CodeOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Tài liệu API</span>
        </Link>
      </li>

      <li className={`pc-item ${pathname === '/settings' ? 'active' : ''}`}>
        <Link href="/settings" className="pc-link">
          <span className="pc-micon">
            <SettingOutlined style={{ fontSize: '20px' }} />
          </span>
          <span className="pc-mtext">Tài khoản</span>
        </Link>
        </li>
        {user?.role === 'admin' ? <li className={`pc-item ${pathname.startsWith('/admin') ? 'active' : ''}`}>
          <Link href="/admin" className="pc-link">
            <span className="pc-micon"><SettingOutlined style={{ fontSize: '20px', color: '#ff4d4f' }} /></span>
            <span className="pc-mtext" style={{ color: '#ff4d4f', fontWeight: 600 }}>Quản trị hệ thống</span>
          </Link>
        </li> : null}
      </ul>
  );

  
  const getImageUrl = (path: string | null | undefined) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    const baseUrl = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000').replace(/\/api\/?$/, '');
    return baseUrl + path;
  };

  const renderAdminIcon = (icon: string | null | undefined, className: string, alt = '') => {
    if (!icon) return null;
    if (icon.startsWith('preset:')) {
      const preset: Record<string, React.ReactNode> = {
      
      };
      return <div className={`nav2-icon-preset d-flex align-items-center justify-content-center text-white`} style={{ backgroundColor: 'var(--client-primary)', borderRadius: '8px' }}>{preset[icon.slice(7)] || <AppstoreOutlined />}</div>;
    }
    return <img src={getImageUrl(icon)} alt={alt} className="nav2-icon-img" onError={event => { event.currentTarget.style.display = 'none'; }} />;
  };

  const renderNav2 = () => {
    const navItems = [
      { id: 'new', label: 'Đặt hàng', path: '/new', icon: <PlusCircleOutlined /> },
      { id: 'mass', label: 'Đặt hàng Sll', path: '/mass', icon: <ShoppingCartOutlined /> },
      { id: 'services', label: 'Bảng giá', path: '/services', icon: <AppstoreOutlined /> },
      { id: 'products', label: 'Sản phẩm', path: '/products', icon: <ShoppingCartOutlined /> },
      { id: 'orders', label: 'Lịch sử', path: '/orders', icon: <UnorderedListOutlined /> },
      { id: 'subscriptions', label: 'Gói định kỳ', path: '/subscriptions', icon: <SyncOutlined /> },
      { id: 'drip-feed', label: 'Đơn lặp', path: '/drip-feed', icon: <RetweetOutlined /> },
      { id: 'refill', label: 'Bảo hành', path: '/refill', icon: <ReloadOutlined /> },
      { id: 'addfunds', label: 'Nạp tiền', path: '/addfunds', icon: <WalletOutlined /> },
      { id: 'cashflow', label: 'Dòng tiền', path: '/cashflow', icon: <LineChartOutlined /> },
      { id: 'affiliate', label: 'Affiliate', path: '/affiliate', icon: <UsergroupAddOutlined /> },
      { id: 'childpanel', label: 'Tạo Web', path: '/childpanel', icon: <GlobalOutlined /> },
      { id: 'statistics', label: 'Thống kê', path: '/statistics', icon: <PieChartOutlined /> },
      { id: 'update', label: 'Cập nhật DV', path: '/update', icon: <HistoryOutlined /> },
      { id: 'posts', label: 'Bài viết', path: '/posts', icon: <FileTextOutlined /> },
      { id: 'tickets', label: 'Hỗ trợ', path: '/tickets', icon: <MessageOutlined /> },
      { id: 'apidoc', label: 'API', path: '/apidoc', icon: <CodeOutlined /> },
      { id: 'settings', label: 'Tài khoản', path: '/settings', icon: <SettingOutlined /> }
    ];

    const groupedPlatforms: Record<string, any> = {};
    categories.forEach(c => {
      if (c.platform) {
        if (!groupedPlatforms[c.platform.id]) {
          groupedPlatforms[c.platform.id] = { ...c.platform, categories: [] };
        }
        groupedPlatforms[c.platform.id].categories.push(c);
      }
    });
    
    const platforms = Object.values(groupedPlatforms).sort((a: any, b: any) => (a.sort_order || 0) - (b.sort_order || 0));

    const ItemBox = ({ item, isCategory = false }: { item: any, isCategory?: boolean }) => {
      const isActive = item.path ? (item.path !== '#' && (item.path === '/' ? pathname === '/' : pathname.startsWith(item.path))) : (pathname === '/new' && typeof window !== 'undefined' && new URLSearchParams(window.location.search).get('category_id') == item.id);
      
      const content = (
        <div style={{
          display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
          gap: 6, padding: '12px 4px', borderRadius: 12, cursor: 'pointer',
          backgroundColor: isActive ? 'color-mix(in srgb, var(--client-primary) 15%, transparent)' : 'color-mix(in srgb, var(--client-sidebar-text) 6%, transparent)',
          border: isActive ? '1px solid var(--client-primary)' : '1px solid transparent',
          color: isActive ? 'var(--client-primary)' : 'var(--client-sidebar-text)',
          transition: 'all 0.2s', height: '100%', textAlign: 'center'
        }} className="nav2-itembox">
          <div style={{ fontSize: 22, display: 'flex', alignItems: 'center', justifyContent: 'center', width: 28, height: 28, opacity: isActive ? 1 : 0.8 }}>
            {isCategory ? renderAdminIcon(item.icon, '', item.name) : item.icon}
          </div>
          <div style={{ fontSize: 11, fontWeight: isActive ? 600 : 500, lineHeight: 1.2, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis', width: '100%', padding: '0 4px', opacity: isActive ? 1 : 0.7 }}>
            {item.name || item.label}
          </div>
        </div>
      );
      
      if (item.path) {
        return <Link href={item.path} style={{ textDecoration: 'none' }}>{content}</Link>;
      }
      return <Link href={`/new?category_id=${item.id}`} style={{ textDecoration: 'none' }}>{content}</Link>;
    };

    return (
      <div style={{ padding: '0 15px', display: 'flex', flexDirection: 'column', gap: 24, paddingBottom: 50 }}>
        <style>{`
          .nav2-icon-img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            border-radius: 6px;
          }
          .nav2-icon-preset {
            width: 28px;
            height: 28px;
            font-size: 18px;
          }
          .nav2-itembox:hover {
            background-color: color-mix(in srgb, var(--client-sidebar-text) 12%, transparent) !important;
          }
        `}</style>
        <div>
          <div style={{ fontSize: 11, fontWeight: 700, color: 'color-mix(in srgb, var(--client-sidebar-text) 50%, transparent)', marginBottom: 12, textTransform: 'uppercase', letterSpacing: '0.5px' }}>MENU</div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 8 }}>
            {navItems.map(item => <ItemBox key={item.id} item={item} />)}
            {user?.role === 'admin' && (
              <ItemBox item={{ id: 'admin', label: 'Admin', path: '/admin', icon: <SettingOutlined /> }} />
            )}
          </div>
        </div>
        

        <div style={{ marginTop: 'auto', paddingTop: 20 }}>
          <Link href="/addfunds" className="btn w-100 d-flex align-items-center justify-content-center gap-2 mb-3" style={{borderRadius: 12, fontWeight: 600, backgroundColor: 'color-mix(in srgb, var(--client-primary) 15%, transparent)', color: 'var(--client-primary)'}}>
            <WalletOutlined /> Nạp tiền
          </Link>
          
          <div className="d-flex align-items-center gap-3 p-3 rounded-3" style={{ backgroundColor: 'color-mix(in srgb, var(--client-sidebar-text) 3%, transparent)', border: '1px solid color-mix(in srgb, var(--client-sidebar-text) 8%, transparent)' }}>
            <div className="avtar avtar-s rounded-circle fs-5 fw-bold" style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', overflow: 'hidden' }}>
              <img src={`https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(user?.name || user?.username || 'User')}`} alt="avatar" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
            </div>
            <div className="flex-grow-1 overflow-hidden">
              <div className="fw-bold text-truncate" style={{color: 'var(--client-sidebar-text)'}}>{user?.name || user?.username || 'Khách'}</div>
              <div className="small text-truncate" style={{fontSize: 12, color: 'color-mix(in srgb, var(--client-sidebar-text) 60%, transparent)'}}>{user?.email || ''}</div>
            </div>
          </div>
        </div>
      </div>
    );
  };

  return (
    <ConfigProvider theme={{ token: { colorPrimary: primaryColor, colorLink: primaryColor, colorLinkHover: primaryColor, colorTextLightSolid: primaryText, colorText: '#172033', colorTextSecondary: '#718096', colorBorder: '#d9dee8', borderRadius: 10, borderRadiusLG: 14, controlHeight: 40, controlHeightLG: 44, fontFamily: 'Inter, Be Vietnam Pro, Arial, sans-serif', fontSize: 14 }, components: { Input: { activeShadow: `0 0 0 3px ${primaryColor}22`, hoverBorderColor: primaryColor }, Select: { optionSelectedBg: `${primaryColor}12`, optionActiveBg: `${primaryColor}0d` }, Form: { labelFontSize: 14, labelColor: '#172033', itemMarginBottom: 18 }, Button: { borderRadius: 10, controlHeight: 40 } } }}>
    <>
      <nav className={`pc-sidebar ${isSidebarOpen ? 'mob-sidebar-active' : ''} ${isSidebarHidden ? 'pc-sidebar-hide' : ''}`}>
        <div className="navbar-wrapper">
          <div className="m-header">
            <Link href="/" className="b-brand text-primary">
              {configLoaded ? (
                <img
                  src={toImageUrl(config?.logo)}
                  className="img-fluid logo-lg"
                  alt={config?.title || "logo"}
                  style={{ width: '100%', maxWidth: '220px', height: '60px', objectFit: 'contain' }}
                />
              ) : (
                <span className="client-logo-loading" aria-hidden="true" />
              )}
            </Link>
          </div>
          <div className="navbar-content" style={config?.client_nav_style === 'nav2' ? { position: 'relative', height: 'calc(100vh - 70px)', overflowY: 'auto', paddingRight: '0 !important', scrollbarWidth: 'none' } : {}}>
            {!configLoaded ? (
              <div className="client-nav-loading" aria-hidden="true">
                {Array.from({ length: 15 }, (_, index) => <span key={index} />)}
              </div>
            ) : config?.client_nav_style === 'nav2' ? renderNav2() : renderNav1()}
          </div>
        </div>
      </nav>

      <header className="pc-header">
        <div className="header-wrapper">
          <div className="me-auto pc-mob-drp">
            <ul className="list-unstyled">
              <li className="pc-h-item pc-sidebar-collapse">
                <a href="#!" className="pc-head-link ms-0" id="sidebar-hide" onClick={(e) => { e.preventDefault(); setIsSidebarHidden(!isSidebarHidden); }}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M3 6h18v2H3V6m0 5h18v2H3v-2m0 5h18v2H3v-2z"/></svg>
                </a>
              </li>
              <li className="pc-h-item pc-sidebar-popup">
                <a href="#!" className="pc-head-link ms-0" id="mobile-collapse" onClick={(e) => { e.preventDefault(); setIsSidebarOpen(!isSidebarOpen); }}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M3 6h18v2H3V6m0 5h18v2H3v-2m0 5h18v2H3v-2z"/></svg>
                </a>
              </li>
            </ul>
          </div>
          <div className="ms-auto">
            <ul className="list-unstyled">
              <li className="pc-h-item">
                <LanguageSwitcher
                  currencies={currencies}
                  activeCurrency={user?.currency || 'VND'}
                  balance={user?.balance || 0}
                  onCurrencyChange={handleChangeCurrency}
                  onOpen={() => void fetchCurrencies()}
                />
              </li>
              <li className={`dropdown pc-h-item header-user-profile ${isProfileOpen ? 'show' : ''}`}>
                <a
                  className="pc-head-link dropdown-toggle arrow-none me-0"
                  href="#"
                  role="button"
                  aria-haspopup="true"
                  aria-expanded={isProfileOpen}
                  onClick={(event) => {
                    event.preventDefault();
                    setIsProfileOpen((open) => !open);
                  }}
                >
                  <img src={`https://ui-avatars.com/api/?background=random&name=${user?.username || 'User'}`} alt="user-image" className="user-avtar rounded-circle" width="40" style={{ position: 'relative', zIndex: 2 }} />
                </a>
                <div className={`dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown rounded-3 bg-gradient-1 ${isProfileOpen ? 'show' : ''}`} style={{ minWidth: 340 }}>
                  <div className="dropdown-body p-3">
                    <div className="position-relative">
                      <div className="d-flex mb-1">
                        <div className="flex-shrink-0">
                          <img src={`https://ui-avatars.com/api/?background=random&name=${user?.username || 'User'}`} alt="user-image" className="rounded-circle" height="35" width="35" />
                        </div>
                        <div className="flex-grow-1 ms-2" style={{ lineHeight: 1.2 }}>
                          <h6 className="mb-0">
                            {user?.username}
                          </h6>
                          <span className="f-12 text-muted fw-medium">{user?.email || user?.username}</span>
                        </div>
                      </div>
                      <div style={{ borderBottom: '1px dashed var(--dh-border-color, #dee2e6)', margin: '12px 0' }}></div>
                      <div className="row g-3 mb-2 ">
                        <div className="col-6 ">
                          <div className="bg-light px-3 py-2 rounded-4" style={{ border: '1px solid var(--dh-border-color, #dee2e6)' }}>
                            <small className="text-muted d-block fw-semibold">Số dư</small>
                            <span className="fw-bold f-14 text-primary dhAmount">{formatBalance(user?.balance)}</span>
                          </div>
                        </div>
                        <div className="col-6">
                          <div className="bg-light px-3 py-2 rounded-4" style={{ border: '1px solid var(--dh-border-color, #dee2e6)' }}>
                            <small className="text-muted d-block fw-semibold">Cấp độ</small>
                            <div className="fw-bold f-14 text-warning text-truncate w-100" style={{ textTransform: 'capitalize' }}>
                                {user?.level || 'Thành viên'}
                            </div>
                          </div>
                        </div>
                      </div>
                      <div className="row g-2 text-center mt-3">
                        <div className="col-4 menu-item">
                          <Link href="/settings" className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-user fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Tài khoản</p>
                          </Link>
                        </div>
                        <div className="col-4 menu-item">
                          <Link href="/settings" className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-settings fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Cài đặt</p>
                          </Link>
                        </div>
                        <div className="col-4 menu-item">
                          <Link href="/addfunds" className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-credit-card fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Nạp tiền</p>
                          </Link>
                        </div>
                        <div className="col-4 menu-item">
                          <Link href="/cashflow" className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-activity fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Nhật ký</p>
                          </Link>
                        </div>
                        <div className="col-4 menu-item">
                          <Link href="/settings" className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-lock fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Đổi mật khẩu</p>
                          </Link>
                        </div>
                        <div className="col-4 menu-item">
                          <a href="#!" onClick={(e) => { e.preventDefault(); handleLogout(); }} className="text-decoration-none d-block">
                            <div className="avtar avtar-s btn-light-primary rounded-4 ts-bg-ease cursor-pointer mx-auto">
                              <i className="ti ti-logout fs-4"></i>
                            </div>
                            <p className="mt-2 mb-0 f-11 text-muted fw-semibold text-nowrap">Đăng xuất</p>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </header>

      <div className="pc-container">
        <div className="pc-content">
          {isLayoutLoading ? (
            <div
              className="d-flex align-items-center justify-content-center"
              style={{ minHeight: 'calc(100vh - 190px)' }}
            >
              <Spin size="large" />
            </div>
          ) : children}
        </div>
      </div>

      {isSidebarOpen && (
        <div 
          className="pc-menu-overlay"
          onClick={() => setIsSidebarOpen(false)}
        />
      )}

      <ContactWidget items={config?.contact_widgets} />

      <footer className="pc-footer">
        <div className="footer-wrapper container-fluid">
          <div className="row">
            <div className="col my-1">
              <p className="m-0 fw-semibold">{config?.footer_text || `© ${new Date().getFullYear()} ${config?.title || 'SMM Panel'}. All rights reserved.`}</p>
            </div>
            <div className="col-auto my-1 d-flex align-items-center gap-3 text-muted small">
              {config?.system_version ? <span>v{config.system_version}</span> : null}
              {config?.developer_name ? config?.developer_url
                ? <a href={config.developer_url} target="_blank" rel="noopener noreferrer">Phát triển bởi {config.developer_name}</a>
                : <span>Phát triển bởi {config.developer_name}</span> : null}
            </div>
          </div>
        </div>
      </footer>
      <style jsx global>{`
        .pc-sidebar {
          background: var(--client-sidebar) !important;
          border-right: 1px solid color-mix(in srgb, var(--client-sidebar-text) 12%, transparent) !important;
          box-shadow: 8px 0 28px rgba(2, 6, 23, 0.18);
        }
        .pc-sidebar .m-header {
          background: transparent !important;
          border-bottom: 1px solid rgba(255, 255, 255, 0.06);
          padding: 4px 18px !important;
        }
        .pc-sidebar .m-header .b-brand {
          display: flex;
          width: 100%;
          align-items: center;
          justify-content: center;
        }
        .client-logo-loading,
        .client-nav-loading span {
          display: block;
          background: color-mix(in srgb, var(--client-sidebar-text) 12%, transparent);
          animation: client-nav-pulse 1.2s ease-in-out infinite;
        }
        .client-logo-loading {
          width: 132px;
          height: 30px;
          border-radius: 8px;
        }
        .client-nav-loading {
          display: grid;
          grid-template-columns: repeat(3, minmax(0, 1fr));
          gap: 8px;
          padding: 18px 15px;
        }
        .client-nav-loading span {
          height: 70px;
          border-radius: 12px;
        }
        @keyframes client-nav-pulse {
          0%, 100% { opacity: .45; }
          50% { opacity: .9; }
        }
        .pc-sidebar .pc-navbar {
          padding: 8px 10px 20px !important;
        }
        .pc-sidebar .nav-active-indicator {
          display: none !important;
        }
        .pc-sidebar .pc-caption {
          margin: 14px 13px 6px !important;
          padding: 0 !important;
        }
        .pc-sidebar .pc-caption label {
          color: color-mix(in srgb, var(--client-sidebar-text) 62%, transparent) !important;
          font-size: 10px !important;
          font-weight: 700 !important;
          letter-spacing: 0.09em;
          text-transform: uppercase;
        }
        .pc-sidebar .pc-item:not(.pc-caption) {
          margin: 3px 0 !important;
        }
        .pc-sidebar .pc-navbar .pc-link {
          display: flex !important;
          flex-direction: row !important;
          align-items: center !important;
          gap: 11px !important;
          width: 100% !important;
          min-height: 40px !important;
          padding: 8px 12px !important;
          border-radius: 10px !important;
          color: var(--client-sidebar-text) !important;
          white-space: nowrap !important;
          transition: background-color .18s ease, color .18s ease, transform .18s ease !important;
        }
        .pc-sidebar .pc-navbar .pc-link:hover {
          color: var(--client-sidebar-text) !important;
          background: color-mix(in srgb, var(--client-sidebar-text) 9%, transparent) !important;
          transform: translateX(2px);
        }
        .pc-sidebar .pc-navbar .pc-item.active > .pc-link {
          background: linear-gradient(135deg, color-mix(in srgb, var(--client-primary) 82%, white) 0%, var(--client-primary) 100%) !important;
          color: var(--client-primary-text) !important;
          box-shadow: 0 8px 20px rgba(22, 119, 255, 0.28);
        }
        .pc-sidebar .pc-navbar .pc-micon {
          position: static !important;
          display: inline-flex !important;
          flex: 0 0 20px !important;
          width: 20px !important;
          min-width: 20px !important;
          height: 20px !important;
          align-items: center !important;
          justify-content: center !important;
          color: color-mix(in srgb, var(--client-sidebar-text) 80%, transparent) !important;
        }
        .pc-sidebar .pc-navbar .pc-item.active > .pc-link .pc-micon,
        .pc-sidebar .pc-navbar .pc-link:hover .pc-micon {
          color: currentColor !important;
        }
        .pc-sidebar .pc-navbar .pc-micon .anticon {
          display: inline-flex !important;
          font-size: 16px !important;
          stroke-width: 1.5;
        }
        .pc-sidebar .pc-navbar .pc-mtext {
          display: block !important;
          flex: 1 1 auto !important;
          width: auto !important;
          margin: 0 !important;
          color: inherit !important;
          font-size: 13.5px !important;
          font-weight: 500 !important;
          line-height: 20px !important;
          letter-spacing: -0.01em;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        
        /* Cho phép cuộn dọc sidebar */
        .navbar-content {
          overflow-y: auto !important;
          overflow-x: hidden !important;
          height: calc(100vh - 70px) !important;
        }
        
        /* Tùy chỉnh thanh cuộn (scrollbar) cho sidebar */
        .navbar-content::-webkit-scrollbar {
          width: 5px;
        }
        .navbar-content::-webkit-scrollbar-track {
          background: transparent;
        }
        .navbar-content::-webkit-scrollbar-thumb {
          background: rgba(100, 116, 139, 0.4);
          border-radius: 10px;
        }
        .navbar-content::-webkit-scrollbar-thumb:hover {
          background: rgba(100, 116, 139, 0.7);
        }
      `}</style>
      <style>{`@media(max-width:768px){.order-search{display:flex!important;width:100%;max-width:100%;overflow:hidden}.order-search .ant-select{width:82px!important;flex:0 0 82px}.order-search .ant-input-affix-wrapper{width:auto!important;min-width:0;flex:1}.order-search .ant-btn{flex:0 0 78px;padding:0 10px}.order-search .ant-select-selector{padding:0 8px!important}}`}</style>
      {Number(config?.mobile_bottom_nav_status) === 1 ? <><style>{`.mobile-bottom-nav{display:none}@media(max-width:768px){.mobile-bottom-nav{position:fixed;display:flex;z-index:1000;bottom:0;left:0;right:0;height:68px;background:#fff;border-top:1px solid #e5e7eb;justify-content:space-around;align-items:center;box-shadow:0 -5px 20px #00000012}.mobile-bottom-nav a{display:flex;flex-direction:column;align-items:center;gap:4px;color:#8b8f98;text-decoration:none;font-size:11px}.mobile-bottom-nav a.active{color:var(--client-primary);font-weight:600}.mobile-bottom-nav svg{font-size:20px}.mobile-bottom-order{margin-top:-25px;background:var(--client-primary);color:#fff!important;width:54px;height:54px;border-radius:50%;justify-content:center;box-shadow:0 5px 15px #0003}.mobile-bottom-order span{display:inline}body{padding-bottom:68px}}`}</style><div className="mobile-bottom-nav"><Link href="/new"><HomeOutlined /><span>Home</span></Link><Link href="/orders"><UnorderedListOutlined /><span>Đơn hàng</span></Link><Link href="/new" className="mobile-bottom-order"><PlusOutlined /><span>Đặt đơn</span></Link><Link href="/addfunds"><WalletOutlined /><span>Nạp tiền</span></Link><Link href="/settings"><SettingOutlined /><span>Tài khoản</span></Link></div></> : null}
    </>
    </ConfigProvider>
  );
}
