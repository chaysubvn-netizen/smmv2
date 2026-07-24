'use client';

import React, { useEffect, useState } from 'react';
import ClientLayout from '@/components/ClientLayout';
import { Table, Typography, Card, Spin, Tag, Input, Row, Col, Badge, Button, Select, Space } from 'antd';import { message } from '@/lib/antd-message';
import { AppstoreOutlined, CrownOutlined, DeleteOutlined, FacebookFilled, GlobalOutlined, GoogleOutlined, InstagramOutlined, PlayCircleFilled, SafetyCertificateOutlined, SendOutlined, ShopOutlined, TeamOutlined, TrophyOutlined, WalletOutlined } from '@ant-design/icons';
import { useRouter } from 'next/navigation';
import api from '@/lib/axios';

const { Title, Text } = Typography;
const { Search } = Input;

export default function ServicesPage() {
  const [services, setServices] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [platforms, setPlatforms] = useState<any[]>([]);
  
  const [loading, setLoading] = useState(true);
  const [searchText, setSearchText] = useState('');
  const [selectedPlatform, setSelectedPlatform] = useState<any>(null);
  const [selectedCategory, setSelectedCategory] = useState<any>(null);
  
  const [config, setConfig] = useState<any>({ currency: 'VND' });
  const [user, setUser] = useState<any>(null);
  const router = useRouter();

  useEffect(() => {
    fetchConfig();
    fetchServices();
    
    const u = localStorage.getItem('user');
    if (u) {
      setUser(JSON.parse(u));
    }
  }, []);

  const fetchConfig = async () => {
    try {
      const res = await api.get('/client/config');
      if (res.data.status) {
        setConfig(res.data.data);
      }
    } catch (e) {}
  };

  const formatCurrency = (amount: number | string) => {
    if (!amount) return config.currency === 'VND' ? '0 ₫' : '$0.0000';
    const val = typeof amount === 'string' ? parseFloat(amount) : amount;
    if (isNaN(val)) return config.currency === 'VND' ? '0 ₫' : '$0.0000';
    if (config.currency === 'VND') {
      return Math.round(val).toLocaleString('vi-VN') + ' ₫';
    }
    return '$' + val.toFixed(4);
  };

  const fetchServices = async () => {
    try {
      const response = await api.get(`/client/services`);
      if (response.data.status) {
        setServices(response.data.data);
        setConfig((current: any) => ({ ...current, currency: response.data.currency || current.currency }));
      }
      
      const catRes = await api.get('/client/categories', { params: { _t: Date.now() } });
      if (catRes.data.status) {
        setCategories(catRes.data.data);
      }
      
      const platRes = await api.get('/client/platforms', { params: { _t: Date.now() } });
      if (platRes.data.status) {
        setPlatforms(platRes.data.data);
      }
    } catch (error) {
      message.error('Không thể tải danh sách dịch vụ');
    } finally {
      setLoading(false);
    }
  };

  const toImageUrl = (path: string) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    const baseUrl = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace('/api', '');
    return `${baseUrl}${path.startsWith('/') ? '' : '/'}${path}`;
  };

  const renderAdminIcon = (icon: string | null | undefined, size = 'w-4 h-4') => {
    if (!icon) return null;
    if (icon.startsWith('preset:')) {
      const presets: Record<string, React.ReactNode> = {
        global: <GlobalOutlined />, facebook: <FacebookFilled />, instagram: <InstagramOutlined />,
        tiktok: <PlayCircleFilled />, youtube: <PlayCircleFilled />, telegram: <SendOutlined />,
        discord: <TeamOutlined />, google: <GoogleOutlined />, shop: <ShopOutlined />, app: <AppstoreOutlined />,
      };
      return <span className={`${size} mr-2 grid shrink-0 place-items-center rounded bg-blue-600 text-[10px] text-white`}>{presets[icon.slice(7)] || <AppstoreOutlined />}</span>;
    }
    return <img src={toImageUrl(icon)} alt="" className={`${size} mr-2 shrink-0 rounded object-contain`} onError={event => { event.currentTarget.style.display = 'none'; }} />;
  };

  const columns = [
    {
      title: 'ID',
      dataIndex: 'id',
      key: 'id',
      width: 60,
      render: (text: string) => <span className="text-blue-500 font-bold text-sm">#{text}</span>,
    },
    {
      title: 'Dịch vụ',
      dataIndex: 'name',
      key: 'name',
      render: (text: string, record: any) => (
        <div className="flex items-center gap-2">
          {(record.category?.platform?.icon || record.category?.icon) && (
            <img 
              src={toImageUrl(record.category?.platform?.icon || record.category?.icon)} 
              alt={record.category?.platform?.name || record.category?.name || ''} 
              className="w-5 h-5 object-contain rounded-full"
              onError={(e) => { e.currentTarget.style.display = 'none' }}
            />
          )}
          <span className="font-semibold text-gray-700 text-[13px]">{text}</span>
        </div>
      ),
    },
    {
      title: 'Giá / 1K',
      dataIndex: 'rate',
      key: 'rate',
      width: 140,
      align: 'right' as const,
      render: (rate: string, record: any) => {
        const showDiscount = record.original_rate && record.original_rate !== record.rate;
        return (
          <div className="flex flex-col items-end justify-center leading-tight">
            {showDiscount ? (
              <>
                <span className="text-[11px] text-gray-400 line-through">
                  {formatCurrency(record.original_rate)}
                </span>
                <span className="text-[13px] font-bold text-red-500">
                  {formatCurrency(record.rate)}
                </span>
              </>
            ) : (
              <span className="text-[13px] font-bold text-red-500">
                {formatCurrency(record.rate)}
              </span>
            )}
          </div>
        );
      }
    },
    {
      title: 'Số lượng',
      key: 'quantity',
      width: 120,
      align: 'center' as const,
      render: (_: unknown, record: { id: number; min: number; max: number }) => (
        <span className="text-[12px] text-gray-600">
          {record.min} - {record.max}
        </span>
      )
    },
    {
      title: '',
      key: 'action',
      width: 100,
      align: 'center' as const,
      render: (_: unknown, record: { id: number; min: number; max: number }) => (
        <Button 
          type="primary" 
          size="small"
          className="bg-blue-500 rounded text-xs px-3"
          onClick={() => router.push(`/new?service=${record.id}`)}
        >
          Đặt hàng
        </Button>
      ),
    },
  ];

  const filteredServices = services.filter(service => {
    let match = true;
    if (searchText && !service.name.toLowerCase().includes(searchText.toLowerCase())) match = false;
    if (selectedCategory && service.category_id !== selectedCategory) match = false;
    if (selectedPlatform && service.category?.platform_id !== selectedPlatform) match = false;
    return match;
  });

  const renderRankCards = () => {
    const ranks = [
      {
        id: 'member',
        title: 'Khách lẻ',
        subtitle: 'Cấp bậc Khách lẻ - Cấp bậc cơ bản',
        priceText: 'Giá Khách lẻ',
        priceSub: 'Áp dụng giá tiêu chuẩn',
        minDeposit: '0 ₫',
        icon: <SafetyCertificateOutlined style={{ fontSize: '32px', color: '#3b82f6' }} />,
        borderColor: '#bfdbfe'
      },
      {
        id: 'silver',
        title: 'Cộng tác viên',
        subtitle: `Cấp bậc Cộng tác viên - Nạp từ ${formatCurrency(config?.ranks?.silver || 0)}`,
        priceText: 'Giá Cộng tác viên',
        priceSub: 'Ưu đãi đặc biệt',
        minDeposit: formatCurrency(config?.ranks?.silver || 0),
        icon: <TrophyOutlined style={{ fontSize: '32px', color: '#eab308' }} />,
        borderColor: '#fde047'
      },
      {
        id: 'gold',
        title: 'Đại lý',
        subtitle: `Cấp bậc Đại lý - Nạp từ ${formatCurrency(config?.ranks?.gold || 0)}`,
        priceText: 'Giá Đại lý',
        priceSub: 'Ưu đãi đặc biệt',
        minDeposit: formatCurrency(config?.ranks?.gold || 0),
        icon: <CrownOutlined style={{ fontSize: '32px', color: '#f59e0b' }} />,
        borderColor: '#fcd34d'
      },
      {
        id: 'platinum',
        title: 'Nhà phân phối',
        subtitle: `Cấp bậc Nhà phân phối - Nạp từ ${formatCurrency(config?.ranks?.platinum || config?.ranks?.diamond || 0)}`,
        priceText: 'Giá Nhà phân phối',
        priceSub: 'Ưu đãi đặc biệt',
        minDeposit: formatCurrency(config?.ranks?.platinum || config?.ranks?.diamond || 0),
        icon: <CrownOutlined style={{ fontSize: '32px', color: '#7c3aed' }} />,
        borderColor: '#c4b5fd'
      }
    ];

    return (
      <Row gutter={[16, 16]} className="mb-6">
        {ranks.map((rank) => {
          const isCurrent = user?.level === rank.id || (rank.id === 'member' && (!user || !user.level));
          
          return (
            <Col xs={24} sm={12} lg={6} key={rank.id}>
              <Badge.Ribbon text="Hiện tại" color="blue" style={{ display: isCurrent ? 'block' : 'none' }}>
                <Card 
                  className="h-full shadow-sm" 
                  styles={{ body: { padding: '20px' } }}
                  style={{
                    borderRadius: '12px',
                    border: isCurrent ? '2px solid #3b82f6' : '1px solid #f0f0f0',
                    transition: 'all 0.3s'
                  }}
                >
                  <div className="flex flex-col items-center text-center mb-4">
                    <div style={{
                      width: '80px',
                      height: '80px',
                      borderRadius: '50%',
                      border: `3px solid ${rank.borderColor}`,
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      marginBottom: '16px',
                      background: '#f8fafc'
                    }}>
                      {rank.icon}
                    </div>
                    <Title level={5} style={{ margin: 0, color: '#1e293b' }}>{rank.title}</Title>
                    <Text type="secondary" style={{ fontSize: '12px', marginTop: '4px' }}>
                      {rank.subtitle}
                    </Text>
                  </div>

                  <div className="bg-gray-50 rounded-lg p-3 mb-2 flex items-center gap-3">
                    <div className="bg-blue-100 p-2 rounded text-blue-500">
                      <WalletOutlined />
                    </div>
                    <div>
                      <div className="text-sm font-semibold text-gray-700">{rank.priceText}</div>
                      <div className="text-xs text-gray-500">{rank.priceSub}</div>
                    </div>
                  </div>

                  <div className="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                    <div className="bg-orange-100 p-2 rounded text-orange-500">
                      <WalletOutlined />
                    </div>
                    <div>
                      <div className="text-sm font-semibold text-gray-700">Nạp tối thiểu</div>
                      <div className="text-xs font-bold text-gray-800">{rank.minDeposit}</div>
                    </div>
                  </div>
                </Card>
              </Badge.Ribbon>
            </Col>
          );
        })}
      </Row>
    );
  };

  return (
    <ClientLayout>
      <Title level={3} className="mb-6">Danh sách dịch vụ</Title>
      {renderRankCards()}
      <Card className="shadow-sm" styles={{ body: { padding: 0 } }}>
        <div className="p-4 border-b border-gray-100 flex flex-wrap gap-2 items-center">
          <Select
            placeholder="Nền tảng"
            allowClear
            style={{ width: 150 }}
            value={selectedPlatform}
            onChange={(val) => {
              setSelectedPlatform(val);
              setSelectedCategory(null);
            }}
            optionLabelProp="label"
            showSearch
            optionFilterProp="title"
          >
            {platforms.map(p => (
              <Select.Option key={p.id} value={p.id} title={p.name} label={<span className="flex items-center">{renderAdminIcon(p.icon)}{p.name}</span>}>
                <div className="flex items-center">
                  {renderAdminIcon(p.icon)}
                  {p.name}
                </div>
              </Select.Option>
            ))}
          </Select>
          
          <Select
            placeholder="Tất cả phân loại"
            allowClear
            style={{ width: 220 }}
            value={selectedCategory}
            onChange={setSelectedCategory}
            optionLabelProp="label"
            showSearch
            optionFilterProp="title"
          >
            {categories
              .filter(c => !selectedPlatform || c.platform_id === selectedPlatform)
              .map(c => (
                <Select.Option key={c.id} value={c.id} title={c.name} label={<span className="flex items-center">{renderAdminIcon(c.icon)}{c.name}</span>}>
                  <div className="flex items-center">
                    {renderAdminIcon(c.icon)}
                    {c.name}
                  </div>
                </Select.Option>
              ))
            }
          </Select>
          
          <Input
            placeholder="Tên dịch vụ"
            allowClear
            value={searchText}
            onChange={(e) => setSearchText(e.target.value)}
            style={{ width: 200 }}
          />
          
          <Button 
            icon={<DeleteOutlined />} 
            onClick={() => {
              setSelectedPlatform(null);
              setSelectedCategory(null);
              setSearchText('');
            }}
          />
        </div>

        {loading ? (
          <div className="flex justify-center p-10"><Spin size="large" /></div>
        ) : (
          <Table 
            columns={columns} 
            dataSource={filteredServices} 
            rowKey="id" 
            pagination={{ pageSize: 20 }}
            scroll={{ x: 'max-content' }}
          />
        )}
      </Card>
    </ClientLayout>
  );
}
