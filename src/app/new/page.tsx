'use client';

import React, { useEffect, useRef, useState } from 'react';
import ClientLayout from '@/components/ClientLayout';
import { Form, Input, Button, Select, Card, Typography, Spin, Row, Col, Tag, Switch, Divider, DatePicker, Modal, Descriptions } from 'antd'; import { message } from '@/lib/antd-message';
import { AppstoreOutlined, BellOutlined, CheckCircleOutlined, ClockCircleOutlined, CloseCircleOutlined, CopyOutlined, CustomerServiceOutlined, FacebookFilled, GoogleOutlined, InfoCircleOutlined, InstagramOutlined, LinkOutlined, LockOutlined, SafetyCertificateOutlined, SearchOutlined, SendOutlined, ShopOutlined, ShoppingCartOutlined, TeamOutlined, WarningOutlined, RocketOutlined, SlidersOutlined, SyncOutlined, GlobalOutlined, PlayCircleFilled, PushpinFilled, CheckOutlined, MinusCircleOutlined, PlusCircleOutlined } from '@ant-design/icons';
import api from '@/lib/axios';
import { useSearchParams } from 'next/navigation';
import dayjs from 'dayjs';
import Link from 'next/link';
import Image from 'next/image';
import styles from './new.module.css';

const { Title, Text } = Typography;
const { Option } = Select;
const hasSameId = (left: unknown, right: unknown) => String(left) === String(right);
const isCustomCommentsService = (service?: { type?: string } | null) =>
  String(service?.type || '').trim().replace(/\s+/g, ' ').toLowerCase() === 'custom comments';

export default function DashboardPage() {
  const [platforms, setPlatforms] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [services, setServices] = useState<any[]>([]);
  const [selectedPlatform, setSelectedPlatform] = useState<any>(null);
  const [selectedCategory, setSelectedCategory] = useState<any>(null);
  const [selectedService, setSelectedService] = useState<any>(null);
  const [servicesLoading, setServicesLoading] = useState(false);
  const [loading, setLoading] = useState(true);
  const [form] = Form.useForm();
  const formConnected = useRef(false);
  const pendingInitialFields = useRef<Record<string, number> | null>(null);
  const [price, setPrice] = useState(0);
  const [quantityValue, setQuantityValue] = useState('');
  const [commentCount, setCommentCount] = useState(0);
  const [showServiceDetails, setShowServiceDetails] = useState(false);
  const [user, setUser] = useState<any>(null);
  const [config, setConfig] = useState<any>({ currency: 'VND' });
  const [currencies, setCurrencies] = useState<any[]>([]);
  const [allServices, setAllServices] = useState<any[]>([]);
  const [quickSearch, setQuickSearch] = useState('');
  const [quickSearchOpen, setQuickSearchOpen] = useState(false);
  const [serviceSearch, setServiceSearch] = useState('');
  const [serviceSearchOpen, setServiceSearchOpen] = useState(false);
  const searchParams = useSearchParams();
  const presetServiceId = searchParams.get('service');
  const [multiLink, setMultiLink] = useState(false);
  const [multiLinkText, setMultiLinkText] = useState('');
  const [scheduleEnabled, setScheduleEnabled] = useState(false);
  const [repeatEnabled, setRepeatEnabled] = useState(false);
  const [repeatTimes, setRepeatTimes] = useState(2);
  const [repeatInterval, setRepeatInterval] = useState(60);
  const [confirmVisible, setConfirmVisible] = useState(false);
  const [noticeOpen, setNoticeOpen] = useState(false);
  const [pendingValues, setPendingValues] = useState<any>(null);
  const [submitting, setSubmitting] = useState(false);
  const [isContentExpanded, setIsContentExpanded] = useState(false);
  const [showContentToggle, setShowContentToggle] = useState(false);
  const [isMobileSelect, setIsMobileSelect] = useState(false);
  const [siteDomain, setSiteDomain] = useState('');
  const contentRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setSiteDomain(window.location.hostname.replace(/^www\./i, '').toUpperCase());
    const mediaQuery = window.matchMedia('(max-width: 768px)');
    const syncMobileSelect = () => setIsMobileSelect(mediaQuery.matches);
    syncMobileSelect();
    mediaQuery.addEventListener('change', syncMobileSelect);
    return () => mediaQuery.removeEventListener('change', syncMobileSelect);
  }, []);

  useEffect(() => {
    if (contentRef.current) {
      setShowContentToggle(contentRef.current.scrollHeight > 80);
    }
    setIsContentExpanded(false);
  }, [selectedService]);

  useEffect(() => {
    api.get('/auth/api/me')
      .then(response => setUser(response.data?.user || null))
      .catch(() => setUser(null));
    // Load all data then auto-select if ?service= is in URL
    const loadData = async () => {
      const [cats, platData, allSvcs, currencyData] = await Promise.all([
        api.get('/client/categories').then(r => r.data.status ? r.data.data : []),
        api.get('/client/platforms').then(r => r.data.status ? r.data.data : []),
        api.get('/client/services').then(r => r.data.status ? r.data.data : []),
        api.get('/client/currencies').then(r => r.data.status ? r.data.data : []),
        fetchConfig(),
      ]);

      setCategories(cats);
      setPlatforms(platData);
      setAllServices(allSvcs);
      setCurrencies(currencyData);
      setLoading(false);

      // Auto-select service from ?service= param, otherwise select the first service
      // Use setTimeout to defer until after Form is mounted in DOM
      const defaultPlatform = platData[0];
      const defaultCategory = cats.find(
        (category: any) => hasSameId(category.platform_id, defaultPlatform?.id)
      );
      const requestedService = presetServiceId
        ? allSvcs.find((service: any) => service.id === parseInt(presetServiceId))
        : allSvcs.find(
          (service: any) => hasSameId(service.category_id, defaultCategory?.id)
        );

      if (requestedService) {
        const category = cats.find((c: any) => hasSameId(c.id, requestedService.category_id));
        const platform = platData.find((p: any) => hasSameId(p.id, category?.platform_id));

        if (!category || !platform) return;

        const catServices = allSvcs.filter((s: any) => hasSameId(s.category_id, requestedService.category_id));

        setSelectedPlatform(platform);
        setSelectedCategory(category);
        setServices(catServices);
        setSelectedService(requestedService);

        // Defer form.setFieldsValue until Form is mounted after setLoading(false) re-render
        const initialFields = {
          platform_id: platform.id,
          category_id: category.id,
          service_id: requestedService.id,
        };
        pendingInitialFields.current = initialFields;
        if (formConnected.current) form.setFieldsValue(initialFields);
      }
    };

    loadData();
  }, [presetServiceId]);

  const fetchConfig = async () => {
    try {
      const response = await api.get('/client/config');
      if (response.data.status) {
        setConfig(response.data.data);
        const hiddenUntil = Number(localStorage.getItem('service_notice_hidden_until') || 0);
        if (response.data.data?.notice_modal && Date.now() >= hiddenUntil) {
          setNoticeOpen(true);
        }
      }
    } catch (error) {
      // Ignore
    }
  };

  const formatCurrency = (amount: number | string) => {
    const val = typeof amount === 'string' ? parseFloat(amount) : amount;
    if (isNaN(val)) return config.currency === 'VND' ? '0 ₫' : '$0.0000';
    if (config.currency === 'VND') {
      return Math.round(val).toLocaleString('vi-VN') + ' ₫';
    }
    return '$' + val.toFixed(4);
  };

  const formatAccountBalance = (amount: number | string) => {
    const currencyCode = user?.currency || 'VND';
    const selectedCurrency = currencies.find(currency => currency.code === currencyCode);
    const vndCurrency = currencies.find(currency => currency.code === 'VND');
    const selectedRate = Number(selectedCurrency?.exchange_rate || 1);
    const vndRate = Number(vndCurrency?.exchange_rate || 1);
    const convertedBalance = vndRate > 0
      ? (Number(amount || 0) / vndRate) * selectedRate
      : Number(amount || 0);
    const symbol = selectedCurrency?.symbol || (currencyCode === 'VND' ? '₫' : currencyCode);

    return `${convertedBalance.toLocaleString('vi-VN', { maximumFractionDigits: 5 })} ${symbol}`;
  };

  const formatAverageTime = (value?: string | null) => {
    if (!value || !String(value).trim()) return 'N/A';
    const text = String(value).trim();
    const match = text.match(/^(\d+):(\d{1,2}):(\d{1,2})$/);
    if (!match) return text;
    const [, hours, minutes, seconds] = match;
    const parts = [];
    if (Number(hours) > 0) parts.push(`${Number(hours)}h`);
    if (Number(minutes) > 0) parts.push(`${Number(minutes)}p`);
    if (Number(seconds) > 0 || parts.length === 0) parts.push(`${Number(seconds)}s`);
    return parts.join(' ');
  };

  const getServiceAttribute = (attribute: string) => {
    const key = attribute.trim().toLowerCase().replace(/[\s-]+/g, '_');
    const attributeMap: Record<string, { label: string; color: string; icon?: string }> = {
      hot: { label: 'Hot', color: 'red', icon: '♨' },
      sale: { label: 'Giảm giá', color: 'green', icon: '◇' },
      fast: { label: 'Đang nhanh', color: 'success', icon: 'ϟ' },
      speed: { label: 'Đang nhanh', color: 'success', icon: 'ϟ' },
      instant: { label: 'Tức thì', color: 'cyan', icon: 'ϟ' },
      new: { label: 'Mới', color: 'blue', icon: '✦' },
      best_seller: { label: 'Bán chạy nhất', color: 'gold' },
      run_now: { label: 'Chạy ngay', color: 'cyan', icon: '▶' },
      slow: { label: 'Chạy chậm', color: 'orange', icon: '◷' },
      exclusive: { label: 'Độc quyền', color: 'purple', icon: '◆' },
      self_produced: { label: 'Tự sản xuất', color: 'geekblue', icon: '●' },
      refill: { label: 'Có bảo hành', color: 'green', icon: '↻' },
      no_refill: { label: 'Không bảo hành', color: 'red', icon: '⊘' },
      stable: { label: 'Ổn định', color: 'purple', icon: '✓' },
      recommended: { label: 'Đề xuất', color: 'gold', icon: '★' },
      cheap: { label: 'Giá rẻ', color: 'green', icon: '◇' },
      cheapest: { label: 'Rẻ nhất', color: 'green', icon: '◇' },
      high_quality: { label: 'Chất lượng cao', color: 'blue', icon: '✓' },
      premium: { label: 'Cao cấp', color: 'gold', icon: '★' },
      popular: { label: 'Phổ biến', color: 'cyan', icon: '●' },
      updated: { label: 'Đã cập nhật', color: 'blue', icon: '↻' },
      limited: { label: 'Giới hạn', color: 'orange', icon: '!' },
      organic: { label: 'Tự nhiên', color: 'green', icon: '♧' },
      real: { label: 'Người dùng thật', color: 'green', icon: '✓' },
      bot: { label: 'Tài khoản ảo', color: 'default', icon: '●' },
      cancel: { label: 'Có thể hủy', color: 'orange', icon: '×' },
      no_cancel: { label: 'Không thể hủy', color: 'red', icon: '×' },
      drip_feed: { label: 'Hỗ trợ chia nhỏ', color: 'blue', icon: '⇥' },
    };

    return attributeMap[key] || {
      label: attribute.replace(/[_-]+/g, ' '),
      color: 'blue',
      icon: '•',
    };
  };

  const getAttributeTagColors = (color: string) => {
    const colors: Record<string, string> = {
      red: '#ef3340',
      green: '#22c55e',
      success: '#22c55e',
      blue: '#2563eb',
      cyan: '#0891b2',
      purple: '#7c3aed',
      geekblue: '#3b5bdb',
      gold: '#d9a400',
      orange: '#f59e0b',
      default: '#64748b',
    };
    const backgroundColor = colors[color] || colors.blue;

    return {
      backgroundColor,
      borderColor: backgroundColor,
      color: '#ffffff',
    };
  };

  const fetchAllServices = async () => {
    try {
      const response = await api.get('/client/services');
      if (response.data.status) {
        setAllServices(response.data.data);
        return response.data.data; // return for chaining
      }
    } catch (error) { }
    return [];
  };

  const fetchCategories = async () => {
    try {
      const response = await api.get(`/client/categories`);
      if (response.data.status) {
        const cats = response.data.data;
        setCategories(cats);
      }

      const platRes = await api.get('/client/platforms');
      if (platRes.data.status) {
        setPlatforms(platRes.data.data);
      }
    } catch (error) {
      message.error('Không thể tải danh mục');
    } finally {
      setLoading(false);
    }
  };

  const fetchServices = async (categoryId: number) => {
    setSelectedService(null);
    setServices([]);
    form.setFieldsValue({ service_id: undefined, link: '', quantity: '', comments: undefined });
    setCommentCount(0);
    setServicesLoading(true);
    try {
      const categoryServices = allServices.filter(service => hasSameId(service.category_id, categoryId));
      const firstService = categoryServices[0];

      setServices(categoryServices);
      setSelectedService(firstService || null);
      form.setFieldsValue({ service_id: firstService?.id });
      calculatePrice(firstService, form.getFieldValue('quantity'));
    } catch (error) {
      message.error('Không thể tải dịch vụ');
    } finally {
      setServicesLoading(false);
    }
  };

  const handlePlatformChange = (value: number) => {
    const platform = platforms.find(p => hasSameId(p.id, value));
    const firstCategory = categories.find(category => hasSameId(category.platform_id, value));

    setSelectedPlatform(platform);
    setSelectedCategory(firstCategory || null);
    setSelectedService(null);
    setServices([]);
    form.setFieldsValue({
      category_id: firstCategory?.id,
      service_id: undefined,
      link: '',
      quantity: '',
      comments: undefined,
    });
    setCommentCount(0);
    setPrice(0);

    if (firstCategory) {
      void fetchServices(firstCategory.id);
    }
  };

  const handleQuickSearchSelect = (value: number) => {
    const service = allServices.find(s => hasSameId(s.id, value));
    if (!service) return;

    const category = categories.find(c => hasSameId(c.id, service.category_id));
    if (!category) return;

    const platform = platforms.find(p => hasSameId(p.id, category.platform_id));

    setSelectedPlatform(platform);
    setSelectedCategory(category);

    const catServices = allServices.filter(s => hasSameId(s.category_id, category.id));
    setServices(catServices);

    setSelectedService(service);
    if (!isCustomCommentsService(service)) {
      form.setFieldValue('comments', undefined);
      setCommentCount(0);
    }

    form.setFieldsValue({
      platform_id: platform?.id,
      category_id: category.id,
      service_id: service.id
    });

    calculatePrice(service, form.getFieldValue('quantity'));
  };

  const handleCategoryChange = (value: number) => {
    const category = categories.find(c => hasSameId(c.id, value));
    setSelectedCategory(category);
    fetchServices(value);
  };

  const handleServiceChange = (value: number) => {
    const service = services.find(s => hasSameId(s.id, value));
    setSelectedService(service);
    if (!isCustomCommentsService(service)) {
      form.setFieldValue('comments', undefined);
      setCommentCount(0);
    }
    calculatePrice(service, form.getFieldValue('quantity'));
  };

  const formatQuantity = (value: number | string | null | undefined) =>
    Number(value || 0).toLocaleString('vi-VN');

  const handleQuantityChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setQuantityValue(e.target.value);
    calculatePrice(selectedService, e.target.value);
  };

  const handleCommentsChange = (value: string) => {
    const count = value.split(/\r?\n/).filter(line => line.trim().length > 0).length;
    setCommentCount(count);
    setQuantityValue(String(count || ''));
    form.setFieldValue('quantity', count || undefined);
    calculatePrice(selectedService, String(count));
  };

  const calculatePrice = (service: any, qty: string) => {
    if (!service || !qty) {
      setPrice(0);
      return;
    }
    const quantity = parseInt(qty);
    if (isNaN(quantity)) return;

    const isPackage = String(service.type || '').toLowerCase() === 'package';
    const calculated = isPackage
      ? service.rate * quantity
      : (service.rate / 1000) * quantity;
    setPrice(calculated);
  };

  const handlePaste = async () => {
    try {
      const text = await navigator.clipboard.readText();
      form.setFieldsValue({ link: text });
    } catch (err) {
      message.error('Không thể dán từ clipboard');
    }
  };

  // Called when form is valid - show confirmation modal
  const onFormFinish = (values: any) => {
    const payload = isCustomCommentsService(selectedService)
      ? values
      : { ...values, comments: undefined };
    setPendingValues(payload);
    setConfirmVisible(true);
  };

  const onFinish = async (values: any) => {
    setSubmitting(true);
    try {
      const response = await api.post('/client/orders', values);
      if (response.data.success) {
        message.success('Đặt hàng thành công!');
        setConfirmVisible(false);
        form.resetFields(['link', 'quantity', 'comments']);
        setCommentCount(0);
        setQuantityValue('');
        // Cập nhật lại số dư user
        setUser((current: any) => current
          ? { ...current, balance: Number(current.balance || 0) - price }
          : current);
      } else {
        message.error(response.data.message || 'Đã có lỗi xảy ra');
      }
    } catch (error: any) {
      const errMsg = error.response?.data?.message || error.response?.data?.error || error.message;
      message.error(errMsg || 'Lỗi kết nối tới máy chủ');
    } finally {
      setSubmitting(false);
    }
  };

  const getImageUrl = (path: string) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    const baseUrl = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000').replace(/\/api\/?$/, '');
    return baseUrl + path;
  };

  const renderAdminIcon = (icon: string | null | undefined, className: string, alt = '') => {
    if (!icon) return null;
    if (icon.startsWith('preset:')) {
      const preset: Record<string, React.ReactNode> = {
        global: <GlobalOutlined />, facebook: <FacebookFilled />, instagram: <InstagramOutlined />,
        tiktok: <PlayCircleFilled />, youtube: <PlayCircleFilled />, telegram: <SendOutlined />,
        discord: <TeamOutlined />, google: <GoogleOutlined />, shop: <ShopOutlined />, app: <AppstoreOutlined />,
      };
      return <span className={`${className} grid place-items-center rounded-lg bg-blue-600 text-sm text-white`}>{preset[icon.slice(7)] || <AppstoreOutlined />}</span>;
    }
    return <img src={getImageUrl(icon)} alt={alt} className={className} onError={event => { event.currentTarget.style.display = 'none'; }} />;
  };

  const quickSearchKeyword = quickSearch.trim().toLowerCase();
  const quickSearchServices = allServices
    .filter(service => !quickSearchKeyword || `#${service.id} ${service.name}`.toLowerCase().includes(quickSearchKeyword))
    .slice(0, 100);
  const serviceSearchKeyword = serviceSearch.trim().toLowerCase();
  const filteredServices = services.filter(service =>
    !serviceSearchKeyword || `#${service.id} ${service.name}`.toLowerCase().includes(serviceSearchKeyword)
  );

  const hideNoticeForTwoHours = () => {
    localStorage.setItem('service_notice_hidden_until', String(Date.now() + (2 * 60 * 60 * 1000)));
    setNoticeOpen(false);
  };

  const serviceContent = selectedService?.note || selectedService?.description || '';
  const accountLevel: Record<string, string> = {
    member: 'JUNIOR MEMBER',
    silver: 'SILVER MEMBER',
    gold: 'GOLD MEMBER',
    platinum: 'PLATINUM MEMBER',
    diamond: 'DIAMOND MEMBER',
  };
  const displayName = user?.name || user?.username || 'Thành viên';
  const totalOrders = Number(config?.total_orders_system ?? 0);
  const totalSpent = Number(user?.total_spent ?? 0);

  return (
    <ClientLayout>
      <>
          <Modal
            open={noticeOpen && Boolean(config?.notice_modal)}
            onCancel={hideNoticeForTwoHours}
            closable={false}
            footer={null}
            width={750}
            styles={{ body: { padding: 0 } }}
            centered
            mask={{ closable: false }}
            className="custom-notice-modal"
          >
            <div style={{ backgroundColor: '#477fff', borderTopLeftRadius: 16, borderTopRightRadius: 16, padding: '20px 24px 35px', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'space-between', overflow: 'hidden' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12, position: 'relative', zIndex: 2 }}>
                <div style={{ backgroundColor: '#fff', color: '#477fff', padding: '6px', borderRadius: '8px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M11 5L6 9H2v6h4l5 4V5z"></path><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path></svg>
                </div>
                <h3 style={{ margin: 0, color: '#fff', fontSize: '18px', fontWeight: 700, textTransform: 'uppercase' }}>Thông báo từ quản trị viên</h3>
              </div>
              
              <div style={{ position: 'relative', zIndex: 2, display: 'flex', gap: '15px' }}>
                <div style={{ position: 'absolute', right: '45px', top: '-10px' }}>
                  <svg width="60" height="40" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10,60 C10,30 30,10 50,10 C70,10 90,30 90,60 Z" fill="#ffffff"/>
                    <circle cx="35" cy="40" r="4" fill="#000000"/>
                    <circle cx="65" cy="40" r="4" fill="#000000"/>
                    <path d="M45,45 Q50,50 55,45" stroke="#000000" strokeWidth="2" fill="none"/>
                    <ellipse cx="25" cy="45" rx="6" ry="3" fill="#ffb6c1" opacity="0.6"/>
                    <ellipse cx="75" cy="45" rx="6" ry="3" fill="#ffb6c1" opacity="0.6"/>
                  </svg>
                </div>
                <button onClick={hideNoticeForTwoHours} style={{ background: 'rgba(255, 255, 255, 0.2)', border: 'none', color: '#fff', cursor: 'pointer', borderRadius: '50%', width: 32, height: 32, display: 'flex', alignItems: 'center', justifyContent: 'center', transition: 'background 0.2s', zIndex: 2, position: 'relative' }}>
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
              </div>
            </div>
            
            <div style={{ backgroundColor: '#fff', borderTopLeftRadius: 16, borderTopRightRadius: 16, marginTop: '-20px', position: 'relative', padding: '30px 24px 24px', zIndex: 1, minHeight: '250px', display: 'flex', flexDirection: 'column' }}>
              <div className="py-2 leading-relaxed text-gray-800" style={{ fontSize: '15px', flex: 1 }} dangerouslySetInnerHTML={{ __html: config?.notice_modal || '' }} />
              
              <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: 24, paddingTop: 16 }}>
                <Button type="primary" size="large" onClick={hideNoticeForTwoHours} style={{ backgroundColor: '#477fff', borderRadius: '24px', fontWeight: 600, padding: '0 24px', height: '44px' }}>
                  Đóng trong 1h
                </Button>
              </div>
            </div>
          </Modal>
          <div className="mb-5 flex items-center justify-between">
            <div>
              <Title level={3} style={{ margin: 0 }}>Đặt đơn hàng</Title>
              <Text type="secondary">Chọn dịch vụ phù hợp và tạo đơn hàng mới</Text>
            </div>
            <span style={{ width: 48, height: 48, borderRadius: 14, background: 'var(--client-primary-soft)', color: 'var(--client-primary)', display: 'grid', placeItems: 'center', fontSize: 22 }}>
              <ShoppingCartOutlined />
            </span>
          </div>
          <section className={styles.accountOverview} aria-label="Tổng quan tài khoản">
            <div className={styles.welcomeCard}>
              <h2 className={styles.welcomeTitle}>
                Xin chào <span className={styles.username}>{displayName}</span>
                <span className={styles.verified} aria-label="Tài khoản đã xác minh">✓</span>
              </h2>
              <p className={styles.welcomeSubtitle}>Chào mừng đến với {siteDomain || 'SMM Panel'}</p>
              <div className={styles.stats}>
                <div className={styles.stat}>
                  <span className={styles.statIcon}>$</span>
                  <span><strong>{formatAccountBalance(user?.balance || 0)}</strong><small>Số dư</small></span>
                </div>
                <div className={styles.stat}>
                  <span className={styles.statIcon}><RocketOutlined /></span>
                  <span><strong>{formatAccountBalance(totalSpent)}</strong><small>Đã sử dụng</small></span>
                </div>
                <div className={styles.stat}>
                  <span className={styles.statIcon}><ShoppingCartOutlined /></span>
                  <span><strong>{totalOrders.toLocaleString('vi-VN')}</strong><small>Tổng đơn hàng</small></span>
                </div>
              </div>
              <Image className={styles.character} src="/man_book_green.png" width={596} height={372} alt="" aria-hidden="true" priority />
            </div>
            <div className={styles.rankCard}>
              <div className={styles.rankHeading}>
                <SafetyCertificateOutlined />
                <span>Cấp độ tài khoản</span>
                <span className={styles.rankBadge}>{accountLevel[user?.level] || 'JUNIOR MEMBER'}</span>
              </div>
              <p>
                Tìm hiểu cách bán lại các dịch vụ của chúng tôi và kiếm thu nhập ổn định hàng tháng!
                Nếu cần giúp đỡ hãy mở một <Link href="/tickets">Ticket</Link> và kết nối trực tiếp với nhóm của chúng tôi.
              </p>
              <svg className={styles.medal} viewBox="0 0 93 157" aria-hidden="true">
                <path d="M17 0h23l17 67H34z" fill="#20d77b"/><path d="M67 0h20L64 68H41z" fill="#08a855"/>
                <circle cx="52" cy="108" r="36" fill="#ffb62e" stroke="#ffd66d" strokeWidth="6"/>
                <path d="M52 87l6 12 14 2-10 10 3 14-13-7-12 7 2-14-10-10 14-2z" fill="#fff1b0"/>
              </svg>
            </div>
          </section>
          {config?.notice_service ? (
            <div className="mb-5 rounded-lg border border-[#91caff] bg-[#e6f4ff] px-4 py-3 shadow-sm animate-fade-in">
              <div className="text-[13px] leading-relaxed text-gray-800 [&>p]:mb-3 [&>p:last-child]:mb-0" dangerouslySetInnerHTML={{ __html: config.notice_service }} />
            </div>
          ) : null}
          <Row gutter={[24, 24]}>
            {/* CỘT TRÁI - FORM TẠO ĐƠN */}
            <Col xs={24} lg={16}>
              <Card className="shadow-sm border-gray-100 rounded-xl" variant="borderless">
                <Title level={4} style={{ margin: '0 0 24px', fontSize: 17, fontWeight: 800 }}>
                  KHỞI TẠO ĐƠN HÀNG NHANH
                </Title>
                <Form
                  form={form}
                  ref={(instance) => {
                    formConnected.current = Boolean(instance);
                    if (instance && pendingInitialFields.current) {
                      instance.setFieldsValue(pendingInitialFields.current);
                      pendingInitialFields.current = null;
                    }
                  }}
                  layout="vertical"
                  onFinish={onFormFinish}
                  size="large"
                  requiredMark={false}
                >
                  {/* TÌM NHANH DỊCH VỤ */}
                  <Form.Item label={<span className="text-sm font-bold text-gray-800">Tìm nhanh dịch vụ</span>}>
                    <div className="relative">
                      <Input
                        value={quickSearch}
                        placeholder="Nhập tên hoặc ID dịch vụ để tìm kiếm nhanh và tự động chọn"
                        suffix={<SearchOutlined className="text-gray-400" />}
                        onFocus={() => setQuickSearchOpen(true)}
                        onChange={event => {
                          setQuickSearch(event.target.value);
                          setQuickSearchOpen(true);
                        }}
                        onBlur={() => window.setTimeout(() => setQuickSearchOpen(false), 120)}
                      />
                      {quickSearchOpen && (
                        <div className="absolute left-0 right-0 top-[calc(100%+2px)] z-50 max-h-80 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-xl">
                          {quickSearchServices.length > 0 ? quickSearchServices.map(service => {
                            const category = categories.find(item => item.id === service.category_id);
                            const platform = platforms.find(item => item.id === category?.platform_id);
                            const icon = category?.icon || platform?.icon;
                            const isSelected = selectedService?.id === service.id;

                            return (
                              <button
                                key={service.id}
                                type="button"
                                onMouseDown={event => event.preventDefault()}
                                onClick={() => {
                                  handleQuickSearchSelect(service.id);
                                  setQuickSearch(service.name);
                                  setQuickSearchOpen(false);
                                }}
                                className={`flex w-full items-center gap-2 rounded-md px-3 py-2 text-left transition-colors ${isSelected ? 'bg-blue-100' : 'hover:bg-gray-100'}`}
                              >
                                {renderAdminIcon(icon, 'h-7 w-7 shrink-0 rounded-full object-contain')}
                                <span className="min-w-0 flex-1 truncate text-sm text-gray-800">
                                  <span className="font-semibold text-blue-500">#{service.id}</span> {service.name}
                                </span>
                                <span className="shrink-0 text-sm font-semibold text-emerald-500">{formatCurrency(service.rate)}</span>
                              </button>
                            );
                          }) : (
                            <div className="px-3 py-8 text-center text-sm text-gray-400">Không tìm thấy dịch vụ phù hợp</div>
                          )}
                        </div>
                      )}
                    </div>
                    <div className="text-xs text-gray-600 mt-1">Nhập tên hoặc ID dịch vụ để tìm kiếm nhanh và tự động chọn</div>
                  </Form.Item>

                  <Row gutter={16}>
                    <Col xs={24} md={12}>
                      <Form.Item
                        label={<span className="text-sm font-bold text-gray-800">Nền tảng</span>}
                        name="platform_id"
                        rules={[{ required: true, message: 'Vui lòng chọn nền tảng' }]}
                      >
                        <Select virtual={false} aria-label="Nền tảng" placeholder="Chọn nền tảng" onChange={handlePlatformChange} showSearch={!isMobileSelect} optionFilterProp="title" optionLabelProp="label">
                          {platforms.map(plat => (
                            <Option
                              key={plat.id}
                              value={plat.id}
                              title={plat.name}
                              label={
                                <div className="flex items-center">
                                  {renderAdminIcon(plat.icon, 'w-6 h-6 mr-2 shrink-0 object-contain rounded-full', plat.name)}
                                  {plat.name}
                                </div>
                              }
                            >
                              <div className="flex items-center">
                                {renderAdminIcon(plat.icon, 'w-7 h-7 mr-2 shrink-0 object-contain rounded-full', plat.name)}
                                {plat.name}
                              </div>
                            </Option>
                          ))}
                        </Select>
                      </Form.Item>
                    </Col>

                    <Col xs={24} md={12}>
                      <Form.Item
                        label={<span className="text-sm font-bold text-gray-800"> Phân loại</span>}
                        name="category_id"
                        rules={[{ required: true, message: 'Vui lòng chọn danh mục' }]}
                      >
                        <Select virtual={false} aria-label="Phân loại" placeholder="Chọn phân loại" onChange={handleCategoryChange} disabled={!selectedPlatform} showSearch={!isMobileSelect} optionFilterProp="title" optionLabelProp="label">
                          {categories.filter(cat => hasSameId(cat.platform_id, selectedPlatform?.id)).map(cat => (
                            <Option
                              key={cat.id}
                              value={cat.id}
                              title={cat.name}
                              label={
                                <div className="flex items-center">
                                  {renderAdminIcon(cat.icon, 'w-6 h-6 mr-2 shrink-0 object-contain rounded-full', cat.name)}
                                  {cat.name}
                                </div>
                              }
                            >
                              <div className="flex items-center">
                                {renderAdminIcon(cat.icon, 'w-7 h-7 mr-2 shrink-0 object-contain rounded-full', cat.name)}
                                {cat.name}
                              </div>
                            </Option>
                          ))}
                        </Select>
                      </Form.Item>
                    </Col>
                  </Row>


                  <Form.Item
                    label={<span className="font-semibold text-gray-700">Dịch vụ</span>}
                  >
                    <div className="relative">
                      <Form.Item
                        name="service_id"
                        rules={[{ required: true, message: 'Vui lòng chọn dịch vụ' }]}
                        hidden
                      >
                        <Input />
                      </Form.Item>
                      <Select
                        aria-label="Dịch vụ"
                        size="large"
                        showSearch={!isMobileSelect}
                        value={selectedService?.id}
                        placeholder="Chọn hoặc tìm kiếm dịch vụ"
                        className="w-full"
                        optionFilterProp="label"
                        onChange={(value) => { handleServiceChange(value); form.setFieldValue('service_id', value); }}
                        options={filteredServices.map(service => ({ 
                          value: service.id, 
                          title: service.name, 
                          label: (
                            <span className="svc-option flex min-w-0 items-center gap-2">
                              {renderAdminIcon(selectedPlatform?.icon, 'h-6 w-6 shrink-0 rounded-full object-contain')}
                              <span className="svc-id rounded-md bg-blue-50 px-1.5 py-0.5 text-xs font-bold text-blue-600">#{service.id}</span>
                              <span className="min-w-0 flex-1 truncate font-medium text-gray-800">{service.name}</span>
                              <span className="svc-price shrink-0 font-bold text-emerald-600">{formatCurrency(service.rate)}</span>
                            </span>
                          ) 
                        }))}
                      />
                      {serviceSearchOpen && !servicesLoading && (
                        <div className="absolute left-0 right-0 top-[calc(100%+2px)] z-50 max-h-80 overflow-y-auto rounded-lg border border-gray-200 bg-white p-1 shadow-xl">
                          {filteredServices.length > 0 ? filteredServices.map(service => {
                            const isSelected = selectedService?.id === service.id;
                            return (
                              <button
                                key={service.id}
                                type="button"
                                onMouseDown={event => event.preventDefault()}
                                onClick={() => {
                                  handleServiceChange(service.id);
                                  form.setFieldValue('service_id', service.id);
                                  setServiceSearch(service.name);
                                  setServiceSearchOpen(false);
                                }}
                                className={`flex w-full items-center gap-2 rounded-md px-3 py-2 text-left transition-colors ${isSelected ? 'bg-blue-100' : 'hover:bg-gray-100'}`}
                              >
                                {renderAdminIcon(selectedPlatform?.icon, 'h-7 w-7 shrink-0 rounded-full object-contain')}
                                <span className="flex min-w-0 flex-1 flex-wrap items-center gap-1 text-sm text-gray-800">
                                  <span className="font-semibold text-blue-500">#{service.id}</span>
                                  <span>{service.name}</span>
                                </span>
                                <span className="shrink-0 text-sm font-semibold text-emerald-500">{formatCurrency(service.rate)}</span>
                              </button>
                            );
                          }) : (
                            <div className="px-3 py-8 text-center text-sm text-gray-400">Không tìm thấy dịch vụ phù hợp</div>
                          )}
                        </div>
                      )}
                      {servicesLoading && (
                        <div className="pointer-events-none absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/90">
                          <Spin size="small" />
                        </div>
                      )}
                    </div>
                  </Form.Item>

                  {/* 4 BADGES */}
                  <Row gutter={[10, 10]} className="mb-6">
                    <Col xs={12} lg={6}>
                      <div className="flex min-h-14 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                          <CheckCircleOutlined />
                        </div>
                        <div className="min-w-0">
                          <div className="text-[8px] font-semibold uppercase tracking-wide text-gray-700">TRẠNG THÁI</div>
                          <div className="truncate text-xs font-bold text-emerald-600">Hoạt động</div>
                        </div>
                      </div>
                    </Col>
                    <Col xs={12} lg={6}>
                      <div className="flex min-h-14 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                          <ClockCircleOutlined />
                        </div>
                        <div className="min-w-0">
                          <div className="text-[8px] font-semibold uppercase tracking-wide text-gray-700">THỜI GIAN TB</div>
                          <div className="truncate text-xs font-bold text-blue-600">{formatAverageTime(selectedService?.average_time)}</div>
                        </div>
                      </div>
                    </Col>
                    <Col xs={12} lg={6}>
                      <div className="flex min-h-14 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-500">
                          <CloseCircleOutlined />
                        </div>
                        <div className="min-w-0">
                          <div className="text-[8px] font-semibold uppercase tracking-wide text-gray-700">HỦY ĐƠN</div>
                          <div className="truncate text-xs font-bold text-red-500">Không</div>
                        </div>
                      </div>
                    </Col>
                    <Col xs={12} lg={6}>
                      <div className="flex min-h-14 items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm">
                        <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-500">
                          <CloseCircleOutlined />
                        </div>
                        <div className="min-w-0">
                          <div className="text-[8px] font-semibold uppercase tracking-wide text-gray-700">BẢO HÀNH</div>
                          <div className="truncate text-xs font-bold text-red-500">Không</div>
                        </div>
                      </div>
                    </Col>
                  </Row>

                  {/* THÔNG TIN CHI TIẾT DỊCH VỤ */}
                  {selectedService && serviceContent && (
                    <div className="mb-6 rounded-lg border border-blue-200 bg-[#f2f9ff] p-4 text-[13px] text-gray-700">
                      <div 
                        className={`overflow-hidden transition-all duration-300 relative ${!isContentExpanded ? 'max-h-[72px]' : ''}`}
                      >
                        <div 
                          ref={contentRef}
                          className="leading-relaxed whitespace-pre-line break-words [&>ul]:list-none [&>ul>li]:relative [&>ul>li]:pl-4 [&>ul>li::before]:content-[''] [&>ul>li::before]:absolute [&>ul>li::before]:w-1.5 [&>ul>li::before]:h-1.5 [&>ul>li::before]:bg-blue-400 [&>ul>li::before]:rounded-full [&>ul>li::before]:left-0 [&>ul>li::before]:top-2 [&>p]:mb-2 [&_img]:max-w-full [&_img]:rounded-md"
                          dangerouslySetInnerHTML={{ __html: serviceContent }}
                        />
                        {!isContentExpanded && showContentToggle && (
                          <div className="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-[#f2f9ff] to-transparent" />
                        )}
                      </div>
                      
                      {showContentToggle && (
                        <div className="mt-3 border-t border-dashed border-blue-200 pt-3 text-center">
                          <button 
                            type="button" 
                            onClick={() => setIsContentExpanded(!isContentExpanded)}
                            className="text-blue-500 font-medium hover:text-blue-600 flex items-center justify-center gap-1 mx-auto transition-colors"
                          >
                            {isContentExpanded ? (
                              <><MinusCircleOutlined /> Thu gọn nội dung</>
                            ) : (
                              <><PlusCircleOutlined /> Xem thêm nội dung</>
                            )}
                          </button>
                        </div>
                      )}
                    </div>
                  )}
                  

                  {/* Label hàng riêng */}
                  <div className="flex justify-between items-center mb-2">
                    <span className="font-semibold text-gray-700 text-sm">Liên kết cần tăng</span>
                    <span className="text-sm text-gray-500 flex items-center gap-2">
                      Mua nhiều link
                      <Switch
                        aria-label="Mua nhiều link"
                        size="small"
                        checked={multiLink}
                        onChange={(val) => {
                          setMultiLink(val);
                          setMultiLinkText('');
                          form.setFieldsValue({ link: '' });
                          setPrice(0);
                        }}
                      />
                    </span>
                  </div>
                  <Form.Item
                    name="link"
                    colon={false}
                    rules={[{ required: !multiLink, message: 'Vui lòng nhập link' }]}
                  >
                    {!multiLink ? (
                      <Input
                        placeholder="Nhập liên kết cần tăng tương tác..."
                        suffix={<Button type="text" size="small" icon={<CopyOutlined />} onClick={handlePaste}>Dán</Button>}
                      />
                    ) : (
                      <div>
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2 text-xs text-blue-700">
                          <div className="font-semibold mb-1">Hướng dẫn nhập nhiều link:</div>
                          <div>Mỗi dòng là 1 link, mỗi link có thể kèm số lượng riêng.</div>
                          <div className="font-mono mt-1 bg-white rounded p-2 text-gray-600">
                            https://link1.com<br />
                            https://link2.com|500<br />
                            https://link3.com|1000
                          </div>
                          <div className="mt-1">Nếu không có số lượng, sẽ dùng giá trị mặc định ở ô Số lượng bên dướdi.</div>
                        </div>
                        <Input.TextArea
                          rows={6}
                          placeholder={`Mỗi dòng 1 link (tùy chọn kèm số lượng sau dấu |):\nhttps://link1.com\nhttps://link2.com|500\nhttps://link3.com|1000`}
                          value={multiLinkText}
                          onChange={(e) => {
                            setMultiLinkText(e.target.value);
                            // Count valid lines for price
                            const lines = e.target.value.split('\n').filter(l => l.trim());
                            const qty = parseInt(quantityValue || '0');
                            if (selectedService && qty > 0) {
                              const totalQty = lines.reduce((sum, line) => {
                                const parts = line.split('|');
                                return sum + (parts[1] ? parseInt(parts[1]) || qty : qty);
                              }, 0);
                              const calc = (selectedService.rate / 1000) * totalQty;
                              setPrice(calc);
                            }
                          }}
                          className="font-mono text-sm"
                        />
                        <div className="flex justify-between text-xs text-gray-500 mt-1">
                          <span>{multiLinkText.split('\n').filter(l => l.trim()).length} liên kết</span>
                          <span className="text-blue-500 cursor-pointer" onClick={() => setMultiLinkText('')}>Xóa tất cả</span>
                        </div>
                      </div>
                    )}
                  </Form.Item>
                  {isCustomCommentsService(selectedService) ? <Form.Item label={<span className="text-sm font-bold text-gray-800">Nội dung bình luận (Mỗi dòng 1 bình luận)</span>} name="comments" rules={[{ required: true, message: 'Vui lòng nhập nội dung bình luận' }, { validator: (_, value) => { const count = String(value || '').split(/\r?\n/).filter(line => line.trim()).length; return count >= 5 && count <= 300000 ? Promise.resolve() : Promise.reject(new Error('Số lượng comments phải từ 5 đến 300000')); } }]} extra={`Đã nhận diện: ${commentCount} bình luận`}><Input.TextArea rows={6} placeholder="Nhập nội dung bình luận, mỗi dòng là một bình luận riêng biệt..." onChange={event => handleCommentsChange(event.target.value)} /></Form.Item> : null}
                  <Form.Item
                    label={<span className="font-semibold text-gray-700">Số lượng</span>}
                    name="quantity"
                    rules={[{ required: true, message: 'Vui lòng nhập số lượng' }]}
                    className="mb-2"
                  >
                    <Input type="number" placeholder="Nhập số lượng" onChange={handleQuantityChange} disabled={!selectedService || isCustomCommentsService(selectedService)} />
                  </Form.Item>
                  <div className="text-xs text-gray-700 mb-4">
                   Tối Thiểu: <strong>{formatQuantity(selectedService?.min)}  · Tối đa:  {formatQuantity(selectedService?.max)}</strong>
                  </div>

                  {/* ĐẶT LỊCH CHẠY */}
                  <div className="flex justify-between items-center mb-3">
                    <span className="text-sm font-semibold text-gray-700 flex items-center">
                      <ClockCircleOutlined className="mr-2" /> Đặt lịch chạy
                    </span>
                    <Switch aria-label="Đặt lịch chạy" checked={scheduleEnabled} onChange={setScheduleEnabled} />
                  </div>
                  {scheduleEnabled && (
                    <div className="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                      <DatePicker
                        showTime
                        placeholder="Chọn ngày giờ chạy..."
                        className="w-full mb-1"
                        disabledDate={(current) => current && current < dayjs().startOf('day')}
                      />
                      <div className="text-xs text-gray-600 mt-1">Múi giờ: Asia/Ho_Chi_Minh</div>
                    </div>
                  )}

                  {/* LẶP LẠI ĐƠN HÀNG */}
                  <div className="flex justify-between items-center mb-3">
                    <span className="text-sm font-semibold text-gray-700 flex items-center">
                      <CopyOutlined className="mr-2" /> Lặp lại đơn hàng
                    </span>
                    <Switch aria-label="Lặp lại đơn hàng" checked={repeatEnabled} onChange={setRepeatEnabled} />
                  </div>
                  {repeatEnabled && (
                    <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                      <Row gutter={16} className="mb-3">
                        <Col span={12}>
                          <div className="text-xs font-bold text-gray-600 uppercase mb-2">SỐ LẦN CHẠY</div>
                          <Input
                            type="number" min={1} value={repeatTimes}
                            onChange={(e) => setRepeatTimes(parseInt(e.target.value) || 1)}
                          />
                        </Col>
                        <Col span={12}>
                          <div className="text-xs font-bold text-gray-600 uppercase mb-2">GIÃN CÁCH (PHÚT)</div>
                          <Input
                            type="number" min={1} value={repeatInterval}
                            onChange={(e) => setRepeatInterval(parseInt(e.target.value) || 1)}
                          />
                        </Col>
                      </Row>
                      <div className="border-t border-yellow-200 pt-3">
                        <div className="flex justify-between items-center">
                          <span className="text-sm font-bold text-gray-700">TỔNG SỐ LƯỢNG:</span>
                          <span className="text-blue-600 font-bold text-lg">
                            {((parseInt(quantityValue || '0') || 0) * repeatTimes).toLocaleString()}
                          </span>
                        </div>
                        <div className="text-xs text-gray-700 mt-2">
                          * Hệ thống sẽ chạy {repeatTimes} lần, mỗi lần {quantityValue || 0} đơn vị, cách nhau {repeatInterval} phút.
                        </div>
                      </div>
                    </div>
                  )}

                  <Divider className="my-4" />

                  <div className="flex justify-between items-center mb-4">
                    <span className="text-gray-600">Giá trị đơn hàng:</span>
                    <span className="text-gray-600 font-medium">{formatCurrency(price)}</span>
                  </div>
                  <div className="flex justify-between items-center mb-6 border-b border-dotted pb-4">
                    <span className="text-gray-800 font-bold">Tổng tiền cần thanh toán:</span>
                    <span className="text-2xl font-bold text-red-500">{formatCurrency(price)}</span>
                  </div>

                  <Button type="primary" htmlType="submit" className="w-full h-10 rounded-full font-semibold text-lg" disabled={!selectedService}>
                    Tạo đơn
                  </Button>
                </Form>

                {/* XÁC NHẬN ĐẶT ĐƠN */}
                <Modal
                  title={
                    <div className="flex items-center gap-2">
                      <CheckCircleOutlined className="text-blue-500 text-xl" />
                      <span>Xác nhận đặt đơn hàng</span>
                    </div>
                  }
                  open={confirmVisible}
                  onCancel={() => setConfirmVisible(false)}
                  footer={[
                    <Button key="cancel" onClick={() => setConfirmVisible(false)}>Huỷ</Button>,
                    <Button
                      key="confirm"
                      type="primary"
                      loading={submitting}
                      onClick={() => pendingValues && onFinish(pendingValues)}
                    >
                      Xác nhận đặt hàng
                    </Button>,
                  ]}
                  mask={false}
                  centered
                  width={480}
                  styles={{
                    container: { borderRadius: '12px', boxShadow: '0 20px 60px rgba(0,0,0,0.3)' },
                  }}
                >
                  <Descriptions column={1} bordered size="small" className="mt-3">
                    <Descriptions.Item label="Dịch vụ">
                      <span className="font-semibold text-blue-600">#{selectedService?.id}</span> {selectedService?.name}
                    </Descriptions.Item>
                    <Descriptions.Item label="Link">
                      <span className="text-gray-700 break-all text-xs">{pendingValues?.link || '—'}</span>
                    </Descriptions.Item>
                    <Descriptions.Item label="Số lượng">
                      <span className="font-semibold">{pendingValues?.quantity?.toLocaleString()}</span>
                    </Descriptions.Item>
                    <Descriptions.Item label={String(selectedService?.type || '').toLowerCase() === 'package' ? 'Giá gói' : 'Đơn giá / 1000'}>
                      {formatCurrency(selectedService?.rate)}
                    </Descriptions.Item>
                    <Descriptions.Item label={<span className="font-bold">Tổng tiền</span>}>
                      <span className="text-red-500 font-bold text-lg">{formatCurrency(price)}</span>
                    </Descriptions.Item>
                    <Descriptions.Item label="Số dư hiện tại">
                      <span className={user?.balance < price ? 'text-red-500 font-bold' : 'text-green-600 font-bold'}>
                        {formatAccountBalance(user?.balance || 0)}
                      </span>
                    </Descriptions.Item>
                  </Descriptions>
                  {user?.balance < price && (
                    <div className="mt-3 p-2 bg-red-50 border border-red-200 rounded text-red-600 text-sm">
                      Số dư không đủ để đặt đơn này. Vui lòng nạp thêm tiền.
                    </div>
                  )}
                </Modal>
              </Card>
            </Col>

            {/* CỘT PHẢI - THÔNG TIN */}
            <Col xs={24} lg={8}>
              {/* THÔNG TIN DỊCH VỤ */}
              <Card
                className="mb-4 overflow-hidden rounded-2xl border border-blue-100 shadow-sm"
                title={
                  <div className="flex items-center gap-2 text-sm font-bold text-gray-800">
                    <InfoCircleOutlined className="text-blue-500" />
                    Thông tin dịch vụ
                  </div>
                }
              >
                {selectedService ? (
                  <div className="overflow-hidden rounded-xl border border-gray-100 bg-gray-50/60 text-sm">
                    <div className="flex items-start justify-between border-b border-gray-100 px-3 py-2.5">
                      <span className="w-1/3 font-semibold text-gray-700">ID dịch vụ</span>
                      <span className="w-2/3 text-right font-bold text-blue-500">#{selectedService.id}</span>
                    </div>

                    {Array.isArray(selectedService.attributes) && selectedService.attributes.length > 0 ? (
                      <div className="flex items-start justify-between border-b border-gray-100 bg-white px-3 py-2.5">
                        <span className="w-1/3 font-semibold text-gray-700">Thuộc tính</span>
                        <div className="flex w-2/3 flex-wrap justify-end gap-1">
                          {selectedService.attributes.map((attribute: string, index: number) => {
                            const badge = getServiceAttribute(attribute);
                            return <Tag key={`${attribute}-${index}`} color={badge.color} className="m-0 text-[10px] leading-4">{badge.label}</Tag>;
                          })}
                        </div>
                      </div>
                    ) : null}

                    <div className="flex items-start justify-between border-b border-gray-100 bg-white px-3 py-2.5">
                      <span className="w-1/3 font-semibold text-gray-700">Tên dịch vụ</span>
                      <span className="w-2/3 text-right font-semibold text-gray-800">{selectedService.name}</span>
                    </div>

                    <div className="flex items-start justify-between border-b border-gray-100 px-3 py-2.5">
                      <span className="w-1/3 font-semibold text-gray-700">Loại dịch vụ</span>
                      <div className="w-2/3 text-right">
                        <Tag color="blue" className="m-0 rounded-full">{selectedService.type || 'Mặc định'}</Tag>
                      </div>
                    </div>

                    <div className="flex items-start justify-between border-b border-gray-100 bg-white px-3 py-2.5">
                      <span className="w-1/3 font-semibold text-gray-700">Giới hạn</span>
                      <span className="w-2/3 text-right font-semibold text-gray-800">{formatQuantity(selectedService.min)} - {formatQuantity(selectedService.max)}</span>
                    </div>

                    <div className="flex items-center justify-between px-3 py-3">
                      <span className="w-1/3 font-semibold text-gray-700">
                        {String(selectedService.type || '').toLowerCase() === 'package' ? 'Giá gói' : 'Giá mỗi 1000'}
                      </span>
                      <div className="flex w-2/3 items-center justify-end text-right">
                        <span className="mr-2 text-xs text-gray-500 line-through">{formatCurrency(selectedService.rate * 1.1)}</span>
                        <span className="font-bold text-red-500">{formatCurrency(selectedService.rate)}</span>
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="text-center text-gray-400 py-10 text-sm">
                    Vui lòng chọn dịch vụ để xem thông tin
                  </div>
                )}
              </Card>

              {/* LƯU Ý MUA HÀNG */}
              <Card
                className="overflow-hidden rounded-2xl border border-amber-200/70 bg-white shadow-sm"
                title={
                  <div className="flex items-center gap-3 py-1">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                      <WarningOutlined />
                    </div>
                    <div>
                      <div className="text-sm font-bold uppercase text-gray-900">Lưu ý mua hàng</div>
                      <div className="text-[11px] font-normal text-gray-600">Đọc kỹ trước khi tạo đơn</div>
                    </div>
                  </div>
                }
              >
                <div className="space-y-3 text-[13px] leading-relaxed text-gray-600">
                  <section className="rounded-xl border border-red-100 bg-red-50/60 p-3">
                    <div className="mb-2 flex items-start gap-2 font-semibold text-gray-900">
                      <SafetyCertificateOutlined className="mt-0.5 shrink-0 text-base text-red-500" />
                      <span>Quy định quan trọng</span>
                    </div>
                    <div className="space-y-2">
                      <p>
                        Nghiêm cấm mọi hành vi buff hoặc tạo đơn có nội dung vi phạm pháp luật,
                        chính trị nhạy cảm, đồi trụy, bạo lực, kích động hoặc phân biệt đối xử.
                      </p>
                      <ul className="space-y-1.5 rounded-lg bg-white/70 p-2">
                        <li className="flex gap-2"><CloseCircleOutlined className="mt-1 text-red-500" /> Trừ toàn bộ số dư mà không cần báo trước.</li>
                        <li className="flex gap-2"><LockOutlined className="mt-1 text-red-500" /> Khóa tài khoản vĩnh viễn và cấm tạo tài khoản mới.</li>
                        <li className="flex gap-2"><WarningOutlined className="mt-1 text-red-500" /> Chịu trách nhiệm trước pháp luật nếu gây hậu quả nghiêm trọng.</li>
                      </ul>
                    </div>
                  </section>

                  <section className="rounded-xl border border-amber-100 bg-amber-50/60 p-3">
                    <div className="mb-2 flex items-start gap-2 font-semibold text-gray-900">
                      <ClockCircleOutlined className="mt-0.5 shrink-0 text-base text-amber-500" />
                      <span>Không đặt đơn trùng</span>
                    </div>
                    <div className="space-y-2">
                      <p>Không đặt nhiều đơn chồng lên cùng một liên kết khi đơn cũ vẫn đang xử lý vì có thể:</p>
                      <ul className="grid gap-1 rounded-lg bg-white/70 p-2">
                        <li>Số lượng tăng không đều giữa các bên.</li>
                        <li>Bị hụt hoặc thiếu số lượng so với dự tính.</li>
                        <li>Khó đối soát vì dữ liệu thay đổi liên tục.</li>
                      </ul>
                      <p>
                        Trường hợp đặt trùng, hệ thống <strong className="text-gray-900">không chịu trách nhiệm</strong> hoàn tiền hoặc bù số lượng.
                      </p>
                    </div>
                  </section>

                  <div className="flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50/70 p-3">
                    <InfoCircleOutlined className="mt-0.5 shrink-0 text-base text-blue-500" />
                    <p>
                      Đơn sai link, sai ID, sai cấu hình hoặc tự ý chỉnh sửa trong lúc đang chạy sẽ
                      <strong className="text-gray-900"> không được hoàn tiền</strong>.
                    </p>
                  </div>

                  <section className="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3">
                    <div className="mb-2 flex items-start gap-2 font-semibold text-gray-900">
                      <CustomerServiceOutlined className="mt-0.5 shrink-0 text-base text-emerald-500" />
                      <span>Hỗ trợ khi có lỗi phát sinh</span>
                    </div>
                    <div className="space-y-2">
                      <p>Vui lòng cung cấp đầy đủ:</p>
                      <ul className="space-y-1.5 rounded-lg bg-white/70 p-2">
                        <li className="flex gap-2"><LinkOutlined className="mt-1" /> Link đơn hàng.</li>
                        <li className="flex gap-2"><InfoCircleOutlined className="mt-1" /> Ảnh chụp hoặc video bằng chứng lỗi.</li>
                        <li className="flex gap-2"><ClockCircleOutlined className="mt-1" /> Thời gian bắt đầu chạy.</li>
                      </ul>
                    </div>
                  </section>

                  <section className="rounded-xl border border-violet-100 bg-violet-50/60 p-3">
                    <div className="mb-1 flex items-start gap-2 font-semibold text-gray-900">
                      <CustomerServiceOutlined className="mt-0.5 shrink-0 text-base text-violet-500" />
                      <span>Kênh hỗ trợ chính thức</span>
                    </div>
                    <p>Truy cập mục Liên hệ hỗ trợ để nhận trợ giúp từ đội ngũ admin.</p>
                  </section>

                  <p className="pt-1 text-center text-[11px] font-semibold text-gray-700">
                    Tuân thủ đúng quy định giúp hệ thống hoạt động ổn định, an toàn và hiệu quả.
                  </p>
                </div>
              </Card>
            </Col>
          </Row>
      </>
    </ClientLayout>
  );
}
