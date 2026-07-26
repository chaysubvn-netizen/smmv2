'use client';

import { useEffect, useState, useMemo } from 'react';
import { 
  Button, Card, Form, Input, InputNumber, Modal, Select, Space, 
  Table, Tag, Typography, Tabs, Switch, Dropdown, MenuProps, Row, Col, Divider, Spin
} from 'antd';
import { 
  BankOutlined, CloudServerOutlined, ControlOutlined, DollarOutlined, DownOutlined, 
  GlobalOutlined, InfoCircleOutlined, LinkOutlined, LoginOutlined, 
  SearchOutlined, SettingOutlined, ShopOutlined, TeamOutlined, UserOutlined 
} from '@ant-design/icons';
import type { ColumnsType, TablePaginationConfig } from 'antd/es/table';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from './childpanels.module.css';
import { formatCurrency } from '@/lib/utils';

const { Text, Title, Paragraph } = Typography;

type PanelRow = {
  id: number;
  domain: string;
  status: string;
  created_at: string;
  formatted_expires_at: string;
  days_remaining: number;
  admin_username: string;
  admin_email: string;
  total_orders: number;
  total_deposits: number;
  hierarchy_level: number;
  main_domain: string | null;
  hierarchy_color: string;
  status_badge_color: string;
  status_display_name: string;
  user: { id: number, username: string, email: string };
};

type Stats = {
  total: number;
  active: number;
  pending: number;
  expired: number;
  total_revenue: number;
  direct_child: number;
  grandchild: number;
};

export default function AdminChildPanelsPage() {
  const [form] = Form.useForm();
  
  // States
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [panels, setPanels] = useState<PanelRow[]>([]);
  const [stats, setStats] = useState<Stats | null>(null);
  const [isMainSite, setIsMainSite] = useState(false);
  
  // Table filters & pagination
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [levelFilter, setLevelFilter] = useState('all');
  const [pagination, setPagination] = useState({ current: 1, pageSize: 20, total: 0 });
  
  // Stats Modal
  const [statsModalOpen, setStatsModalOpen] = useState(false);
  const [loadingStats, setLoadingStats] = useState(false);
  const [activeStatsData, setActiveStatsData] = useState<any>(null);

  const fetchPanels = async (page = 1, pageSize = 20, q = search, s = statusFilter, l = levelFilter) => {
    setLoading(true);
    try {
      const res = await api.get('/admin/childpanels', {
        params: { page, per_page: pageSize, search: q, status: s, level: l }
      });
      const result = res.data.data;
      setPanels(result.panels.data);
      setStats(result.stats);
      setIsMainSite(result.isMainSite);
      setPagination({ 
        current: result.panels.current_page, 
        pageSize: result.panels.per_page, 
        total: result.panels.total 
      });
      
      // Auto-fill form if not touched
      if (!form.isFieldsTouched()) {
        form.setFieldsValue({
          child_panel_enabled: result.site_settings.child_panel_enabled,
          child_panel_monthly_price: result.site_settings.child_panel_monthly_price,
          cloudflare_email: result.site_settings.cloudflare_email,
          cloudflare_global_key: result.site_settings.cloudflare_global_key,
          cloudflare_account_id: result.site_settings.cloudflare_account_id,
          cloudflare_token: result.site_settings.cloudflare_token,
          cloudflare_ip_host: result.site_settings.cloudflare_ip_host,
          hosting_panel_type: result.site_settings.hosting_panel_type || 'cpanel',
          cpanel_host: result.site_settings.cpanel_host,
          cpanel_user: result.site_settings.cpanel_user,
          cpanel_pass: result.site_settings.cpanel_pass ? '********' : '',
        });
      }
    } catch (err: unknown) {
      const errorMessage = (err as { response?: { data?: { message?: string } } })
        .response?.data?.message;
      message.error(errorMessage || 'Không thể tải danh sách Child Panel.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPanels(1, 20);
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  const handleTableChange = (next: TablePaginationConfig) => {
    fetchPanels(next.current || 1, next.pageSize || 20);
  };

  const handleSearch = () => fetchPanels(1, pagination.pageSize);

  const handleStatusUpdate = async (id: number, newStatus: string) => {
    try {
      await api.post(`/admin/childpanels/${id}/status`, { status: newStatus });
      message.success('Cập nhật trạng thái thành công');
      fetchPanels(pagination.current, pagination.pageSize);
    } catch {
      message.error('Lỗi khi cập nhật trạng thái');
    }
  };

  const handleDelete = (row: PanelRow) => {
    Modal.confirm({
      title: 'Xác nhận xóa Website',
      content: `Bạn có chắc chắn muốn xóa website ${row.domain}? Hành động này sẽ xóa dữ liệu trên Cloudflare, cPanel và dữ liệu trong hệ thống.`,
      okText: 'Xóa ngay',
      okType: 'danger',
      cancelText: 'Hủy',
      onOk: async () => {
        try {
          const res = await api.delete(`/admin/childpanels/${row.id}`);
          message.success(res.data.message || 'Đã xóa thành công');
          fetchPanels(pagination.current, pagination.pageSize);
        } catch {
          message.error('Lỗi khi xóa Child Panel');
        }
      }
    });
  };

  const openStats = async (id: number) => {
    setStatsModalOpen(true);
    setLoadingStats(true);
    setActiveStatsData(null);
    try {
      const res = await api.get(`/admin/childpanels/${id}/statistics`);
      setActiveStatsData(res.data.data);
    } catch {
      message.error('Không thể tải thống kê');
      setStatsModalOpen(false);
    } finally {
      setLoadingStats(false);
    }
  };

  const loginAsAdmin = async (id: number) => {
    try {
      const res = await api.get(`/admin/childpanels/${id}/login`);
      window.open(res.data.url, '_blank');
    } catch {
      message.error('Không thể tạo phiên đăng nhập');
    }
  };

  const saveSettings = async (values: any) => {
    setSaving(true);
    try {
      if (values.cpanel_pass === '********') {
        delete values.cpanel_pass;
      }
      await api.post('/admin/childpanels/settings', values);
      message.success('Lưu cấu hình thành công!');
    } catch (err: any) {
      message.error(err.response?.data?.message || 'Không thể lưu cấu hình');
    } finally {
      setSaving(false);
    }
  };

  const getActionItems = (row: PanelRow): MenuProps['items'] => [
    { key: 'login', icon: <LoginOutlined />, label: 'Login as Admin', onClick: () => loginAsAdmin(row.id) },
    { key: 'stats', icon: <ShopOutlined />, label: 'Thống kê & Đơn hàng', onClick: () => openStats(row.id) },
    { type: 'divider' },
    ...(row.status !== 'active' ? [{ key: 'activate', label: 'Set: Active', onClick: () => handleStatusUpdate(row.id, 'active') }] : []),
    ...(row.status !== 'expired' ? [{ key: 'expire', label: 'Set: Expired', danger: true, onClick: () => handleStatusUpdate(row.id, 'expired') }] : []),
    { type: 'divider' },
    { key: 'delete', label: 'Xóa hệ thống', danger: true, onClick: () => handleDelete(row) },
  ];

  const columns: ColumnsType<PanelRow> = [
    {
      title: 'Tên miền',
      dataIndex: 'domain',
      render: (val, row) => (
        <div className={styles.domainCell}>
          <div className={styles.domainIcon} style={{ background: row.hierarchy_color || '#e2e8f0', color: '#fff' }}>
            {val.charAt(0).toUpperCase()}
          </div>
          <div>
            <div className={styles.domainName}>
              <a href={`http://${val}`} target="_blank" rel="noreferrer" style={{ color: 'inherit' }}>
                {val} <LinkOutlined style={{ fontSize: 12, opacity: 0.5 }} />
              </a>
            </div>
            {row.hierarchy_level > 0 && (
              <div className={styles.domainParent}>
                <GlobalOutlined /> Thuộc: {row.main_domain}
              </div>
            )}
          </div>
        </div>
      )
    },
    {
      title: 'Chủ sở hữu',
      key: 'owner',
      render: (_, row) => (
        <div>
          <div style={{ fontWeight: 500 }}>{row.user?.username || 'N/A'}</div>
          <div style={{ fontSize: 12, color: '#64748b' }}>{row.user?.email}</div>
        </div>
      )
    },
    {
      title: 'Thống kê GD',
      key: 'internal_stats',
      align: 'center',
      render: (_, row) => (
        <div className={styles.internalStats}>
          <div className={styles.internalStatBox}>
            <strong>{row.total_orders || 0}</strong>
            <span>Đơn</span>
          </div>
          <div className={styles.internalStatBox}>
            <strong>{formatCurrency(row.total_deposits || 0)}</strong>
            <span>Nạp</span>
          </div>
        </div>
      )
    },
    {
      title: 'Trạng thái',
      key: 'status',
      align: 'center',
      render: (_, row) => (
        <Tag color={row.status_badge_color || 'blue'}>{row.status_display_name || row.status}</Tag>
      )
    },
    {
      title: 'Hết hạn',
      key: 'expires',
      align: 'center',
      render: (_, row) => (
        <div style={{ textAlign: 'center' }}>
          <div>{row.formatted_expires_at || '-'}</div>
          {row.status === 'active' && row.days_remaining !== undefined && (
            <div style={{ fontSize: 11, color: row.days_remaining <= 5 ? '#ef4444' : '#64748b' }}>
              Còn {row.days_remaining} ngày
            </div>
          )}
        </div>
      )
    },
    {
      title: 'Hành động',
      key: 'actions',
      align: 'right',
      render: (_, row) => (
        <Dropdown menu={{ items: getActionItems(row) }} trigger={['click']}>
          <Button type="text">Thao tác <DownOutlined /></Button>
        </Dropdown>
      )
    }
  ];

  return (
    <main className={styles.page}>
      <header className={styles.heading}>
        <Title level={2}>Website đại lý</Title>
        <Text>Quản lý danh sách và cấu hình tự động tạo Child Panel</Text>
      </header>

      <Tabs 
        defaultActiveKey="list" 
        items={[
          {
            key: 'list',
            label: 'Danh sách đại lý',
            children: (
              <>
                {/* Stats Row */}
                <div className={styles.statsRow}>
                  <div className={styles.statCard}>
                    <div className={`${styles.statIcon} ${styles.iconInfo}`}><CloudServerOutlined /></div>
                    <div className={styles.statContent}>
                      <p className={styles.statTitle}>Tổng Child Panel</p>
                      <h4 className={styles.statValue}>{stats?.total || 0}</h4>
                      <div className={styles.statSub}>
                        <span style={{ color: '#10b981' }}>{stats?.active || 0} hoạt động</span>
                        <span style={{ margin: '0 4px' }}>·</span>
                        <span style={{ color: '#ef4444' }}>{stats?.expired || 0} hết hạn</span>
                      </div>
                    </div>
                  </div>
                  <div className={styles.statCard}>
                    <div className={`${styles.statIcon} ${styles.iconSuccess}`}><ShopOutlined /></div>
                    <div className={styles.statContent}>
                      <p className={styles.statTitle}>
                        Web Con <Tag color="success" style={{ marginInlineStart: 8, border: 'none', background: '#dcfce7', color: '#16a34a' }}>Cấp 1</Tag>
                      </p>
                      <h4 className={styles.statValue}>{stats?.direct_child || 0}</h4>
                      <div className={styles.statSub}>
                        Tạo từ {typeof window !== 'undefined' ? window.location.hostname : 'hệ thống'}
                      </div>
                    </div>
                  </div>
                  <div className={styles.statCard}>
                    <div className={`${styles.statIcon} ${styles.iconWarning}`}><BankOutlined /></div>
                    <div className={styles.statContent}>
                      <p className={styles.statTitle}>
                        Web Cháu/Chắt <Tag color="warning" style={{ marginInlineStart: 8, border: 'none', background: '#fef9c3', color: '#ca8a04' }}>Cấp 2+</Tag>
                      </p>
                      <h4 className={styles.statValue}>{stats?.grandchild || 0}</h4>
                      <div className={styles.statSub}>
                        Tạo từ các Web Con
                      </div>
                    </div>
                  </div>
                  <div className={styles.statCard}>
                    <div className={`${styles.statIcon} ${styles.iconInfo}`} style={{ borderRadius: '50%' }}><DollarOutlined /></div>
                    <div className={styles.statContent}>
                      <p className={styles.statTitle}>Tổng doanh thu thuê</p>
                      <h4 className={styles.statValue}>{formatCurrency(stats?.total_revenue || 0)}</h4>
                      <div className={styles.statSub}>
                        Tổng cộng tất cả cấp
                      </div>
                    </div>
                  </div>
                </div>

                {/* Main Table */}
                <Card className={styles.mainCard} variant="borderless">
                  <div className={styles.filterBar}>
                    <Input 
                      placeholder="Tìm theo tên miền, username, email..." 
                      className={styles.filterSearch}
                      prefix={<SearchOutlined />}
                      value={search}
                      onChange={e => setSearch(e.target.value)}
                      onPressEnter={handleSearch}
                    />
                    <Select 
                      className={styles.filterSelect} 
                      value={statusFilter} 
                      onChange={v => { setStatusFilter(v); fetchPanels(1, pagination.pageSize, search, v, levelFilter); }}
                    >
                      <Select.Option value="all">Tất cả trạng thái</Select.Option>
                      <Select.Option value="active">Đang hoạt động</Select.Option>
                      <Select.Option value="pending">Chờ xử lý</Select.Option>
                      <Select.Option value="expired">Đã hết hạn</Select.Option>
                      <Select.Option value="cancelled">Đã hủy</Select.Option>
                    </Select>
                    {isMainSite && (
                      <Select 
                        className={styles.filterSelect} 
                        value={levelFilter}
                        onChange={v => { setLevelFilter(v); fetchPanels(1, pagination.pageSize, search, statusFilter, v); }}
                      >
                        <Select.Option value="all">Tất cả cấp độ</Select.Option>
                        <Select.Option value="child">Đại lý F1</Select.Option>
                      </Select>
                    )}
                    <Button type="primary" onClick={handleSearch}>Tìm kiếm</Button>
                  </div>

                  <Table 
                    columns={columns} 
                    dataSource={panels} 
                    rowKey="id" 
                    loading={loading}
                    pagination={{ ...pagination, showSizeChanger: true }}
                    onChange={handleTableChange}
                    scroll={{ x: 1000 }}
                  />
                </Card>
              </>
            )
          },
          {
            key: 'settings',
            label: 'Cấu hình hệ thống',
            forceRender: true,
            children: (
              <Card className={styles.mainCard} variant="borderless">
                <div className={styles.settingsForm}>
                  <div className={styles.guideBox}>
                    <div className={styles.guideIcon}><InfoCircleOutlined /></div>
                    <div className={styles.guideContent}>
                      <h4>Hướng dẫn cài đặt tự động tạo Website Đại lý</h4>
                      <ul>
                        <li>Hệ thống sử dụng Cloudflare API để tự động thêm/xóa tên miền.</li>
                        <li>Sử dụng cPanel hoặc aaPanel API để tự động tạo Hosting, cài SSL và trỏ thư mục mã nguồn.</li>
                        <li>Bạn chỉ cần cấu hình chính xác các thông số bên dưới, hệ thống sẽ chạy hoàn toàn tự động 100%.</li>
                      </ul>
                    </div>
                  </div>

                  <Form layout="vertical" form={form} onFinish={saveSettings}>
                    <div className={styles.configCard}>
                      <div className={styles.configHeader}>
                        <Space><SettingOutlined /> Cài đặt chung</Space>
                        <Form.Item name="child_panel_enabled" valuePropName="checked" noStyle>
                          <Switch checkedChildren="Bật tính năng" unCheckedChildren="Tắt" />
                        </Form.Item>
                      </div>
                      <div className={styles.configBody}>
                        <Form.Item 
                          name="child_panel_monthly_price" 
                          label="Giá tạo website (VND / Tháng)"
                          rules={[{ required: true }]}
                        >
                          <InputNumber style={{ width: '100%' }} formatter={value => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')} />
                        </Form.Item>
                        <Paragraph type="secondary" style={{ fontSize: 13, marginTop: -16, marginBottom: 0 }}>
                          Đây là số tiền hệ thống sẽ trừ mỗi tháng vào tài khoản của khách hàng khi duy trì Website.
                        </Paragraph>
                      </div>
                    </div>

                    <div className={styles.configCard}>
                      <div className={styles.configHeader}>
                        <Space><CloudServerOutlined /> Cấu hình Cloudflare API</Space>
                      </div>
                      <div className={styles.configBody}>
                        <Row gutter={16}>
                          <Col span={12}>
                            <Form.Item name="cloudflare_email" label="Cloudflare Email" rules={[{ required: true }]}>
                              <Input />
                            </Form.Item>
                          </Col>
                          <Col span={12}>
                            <Form.Item name="cloudflare_global_key" label="Global API Key (Hoặc Token)" rules={[{ required: true }]}>
                              <Input.Password />
                            </Form.Item>
                          </Col>
                        </Row>
                        <Row gutter={16}>
                          <Col span={12}>
                            <Form.Item name="cloudflare_account_id" label="Account ID">
                              <Input />
                            </Form.Item>
                          </Col>
                          <Col span={12}>
                            <Form.Item name="cloudflare_ip_host" label="IP Host (Server IP)" rules={[{ required: true }]}>
                              <Input placeholder="192.168.1.1" />
                            </Form.Item>
                          </Col>
                        </Row>
                        <Paragraph type="secondary" style={{ fontSize: 12, marginTop: -8 }}>
                          Dùng Global API Key (ưu tiên) hoặc Token. Hệ thống sẽ tự động thêm miền, tạo DNS trỏ về IP Host khi khách tạo web.
                        </Paragraph>
                      </div>
                    </div>

                    <div className={styles.configCard}>
                      <div className={styles.configHeader}>
                        <Space><ControlOutlined /> Cấu hình Hosting Panel API</Space>
                      </div>
                      <div className={styles.configBody}>
                        <Form.Item name="hosting_panel_type" label="Loại Hosting">
                          <Select>
                            <Select.Option value="cpanel">cPanel (Khuyên dùng)</Select.Option>
                            <Select.Option value="aapanel">aaPanel</Select.Option>
                          </Select>
                        </Form.Item>
                        
                        <Row gutter={16}>
                          <Col span={24}>
                            <Form.Item name="cpanel_host" label="Host URL (Vd: https://server.com:2083)" rules={[{ required: true }]}>
                              <Input />
                            </Form.Item>
                          </Col>
                          <Col span={12}>
                            <Form.Item name="cpanel_user" label="Username / API Key" rules={[{ required: true }]}>
                              <Input />
                            </Form.Item>
                          </Col>
                          <Col span={12}>
                            <Form.Item name="cpanel_pass" label="Password / Security Key" rules={[{ required: true }]}>
                              <Input.Password placeholder="Nhập để thay đổi" />
                            </Form.Item>
                          </Col>
                        </Row>
                      </div>
                    </div>

                    <Button type="primary" htmlType="submit" size="large" block loading={saving}>
                      LƯU CẤU HÌNH HỆ THỐNG
                    </Button>
                  </Form>
                </div>
              </Card>
            )
          }
        ]} 
      />

      {/* Statistics Modal */}
      <Modal
        title={activeStatsData ? `Thống kê: ${activeStatsData.domain}` : 'Thống kê website'}
        open={statsModalOpen}
        onCancel={() => setStatsModalOpen(false)}
        footer={null}
        width={800}
      >
        {loadingStats || !activeStatsData ? (
          <div style={{ textAlign: 'center', padding: '40px' }}><Spin size="large" /></div>
        ) : (
          <div>
            <div style={{ display: 'flex', gap: 16, marginBottom: 24, padding: '16px', background: '#f8fafc', borderRadius: 8 }}>
              <div style={{ flex: 1 }}>
                <Text type="secondary">Trạng thái:</Text> <Tag color={activeStatsData.status_color}>{activeStatsData.status}</Tag>
                <br /><Text type="secondary">Gia hạn kế:</Text> <strong>{activeStatsData.expires_at}</strong> ({activeStatsData.days_remaining} ngày)
              </div>
              <div style={{ flex: 1 }}>
                <Text type="secondary">Admin Username:</Text> <strong>{activeStatsData.admin_username}</strong>
                <br /><Text type="secondary">Tài khoản chủ:</Text> <strong>{activeStatsData.owner}</strong>
              </div>
            </div>

            <Title level={5}>Doanh thu & Dòng tiền (F1)</Title>
            <div className={styles.statsModalRow}>
              <div className={styles.smStatBox}>
                <div className={`${styles.smStatIcon} ${styles.iconInfo}`}><DollarOutlined /></div>
                <h4>{formatCurrency(activeStatsData.finance?.total_deposits)}</h4>
                <p>Tổng nạp</p>
              </div>
              <div className={styles.smStatBox}>
                <div className={`${styles.smStatIcon} ${styles.iconPrimary}`}><ShopOutlined /></div>
                <h4>{formatCurrency(activeStatsData.finance?.total_spent)}</h4>
                <p>Tổng tiêu</p>
              </div>
              <div className={styles.smStatBox}>
                <div className={`${styles.smStatIcon} ${styles.iconSuccess}`}><UserOutlined /></div>
                <h4>{activeStatsData.users?.total}</h4>
                <p>Thành viên</p>
              </div>
              <div className={styles.smStatBox}>
                <div className={`${styles.smStatIcon} ${styles.iconWarning}`}><SearchOutlined /></div>
                <h4>{activeStatsData.orders?.total}</h4>
                <p>Tổng đơn hàng</p>
              </div>
            </div>

            <Row gutter={16}>
              <Col span={12}>
                <Card size="small" title="Đơn hàng" variant="borderless" style={{ background: '#f1f5f9' }}>
                  <p>Thành công: <strong>{activeStatsData.orders?.completed}</strong></p>
                  <p>Đang xử lý: <strong>{activeStatsData.orders?.pending}</strong></p>
                  <p>Bị hủy: <strong>{activeStatsData.orders?.cancelled}</strong></p>
                  <Divider style={{ margin: '8px 0' }} />
                  <p>7 ngày qua: <strong>+{activeStatsData.orders?.recent_7d} đơn</strong></p>
                </Card>
              </Col>
              <Col span={12}>
                <Card size="small" title="Doanh thu 7 ngày" variant="borderless" style={{ background: '#f1f5f9' }}>
                  <Title level={3} style={{ margin: 0, color: '#16a34a' }}>
                    +{formatCurrency(activeStatsData.finance?.recent_revenue_7d)}
                  </Title>
                  <p style={{ marginTop: 8, color: '#64748b' }}>Doanh thu trong tuần qua.</p>
                </Card>
              </Col>
            </Row>
          </div>
        )}
      </Modal>
    </main>
  );
}
