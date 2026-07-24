'use client';

import { useCallback, useEffect, useState } from 'react';
import { Alert, Button, Card, Empty, Form, Input, Modal, Popconfirm, Steps, Table, Tag, Typography, Checkbox } from 'antd';
import { message } from '@/lib/antd-message';
import { CheckCircleOutlined, CopyOutlined, CrownOutlined, GlobalOutlined, KeyOutlined, PlusOutlined, ReloadOutlined, InfoCircleOutlined, SearchOutlined, ArrowRightOutlined, ArrowLeftOutlined } from '@ant-design/icons';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './childpanel.module.css';

const { Text, Title, Paragraph } = Typography;
type Panel = { id: number; domain: string; status: string; status_api: string; dns_ready: boolean; nameserver1?: string; nameserver2?: string; expired_at?: string; created_at: string };
type PanelConfig = { panels: Panel[]; monthly_fee: number; currency: string; default_nameservers: string[] };
const statusColor: Record<string, string> = { active: 'green', pending: 'gold', inactive: 'red', scam: 'red' };
const statusText: Record<string, string> = { active: 'Hoạt động', pending: 'Chờ xử lý', inactive: 'Ngừng', scam: 'Bị khóa' };

export default function ChildPanelPage() {
  const [data, setData] = useState<PanelConfig>({ panels: [], monthly_fee: 0, currency: 'VND', default_nameservers: [] });
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState<'list'|'create'>('list');
  const [step, setStep] = useState(0);
  const [domain, setDomain] = useState('');
  const [checkingDomain, setCheckingDomain] = useState(false);
  const [creating, setCreating] = useState(false);
  
  const [keyPanel, setKeyPanel] = useState<Panel | null>(null);
  const [revealedKey, setRevealedKey] = useState('');
  const [keyForm] = Form.useForm<{ password: string }>();

  // Success data
  const [successData, setSuccessData] = useState<{ns1?: string, ns2?: string, domain?: string}>({});

  const loadPanels = useCallback(async () => {
    setLoading(true);
    try { const response = await api.get('/client/child-panels'); if (response.data?.status) setData(response.data.data); }
    catch { message.error('Không thể tải danh sách Child Panel.'); }
    finally { setLoading(false); }
  }, []);
  
  useEffect(() => { void Promise.resolve().then(loadPanels); }, [loadPanels]);
  
  const money = (value: number) => `${Number(value).toLocaleString('vi-VN')} ${data.currency === 'VND' ? 'đ' : data.currency}`;
  
  const checkDomain = async () => {
    if (!domain) { message.warning('Vui lòng nhập tên miền.'); return; }
    setCheckingDomain(true);
    try {
      const res = await api.post('/client/child-panels/check-domain', { domain });
      if (res.data?.success) {
        message.success(res.data.message);
        setStep(1);
      }
    } catch (error: any) {
      message.error(error.response?.data?.message || 'Tên miền không hợp lệ hoặc đã tồn tại.');
    } finally {
      setCheckingDomain(false);
    }
  };

  const submitCreate = async () => {
    setCreating(true);
    try {
      const response = await api.post('/client/child-panels', { website: domain });
      if (response.data?.success) {
         setSuccessData({
           ns1: response.data.data.data?.nameserver1,
           ns2: response.data.data.data?.nameserver2,
           domain: response.data.data.name
         });
         setStep(2);
         await loadPanels();
      }
    } catch (error: any) {
      message.error(error.response?.data?.message || 'Không thể tạo Child Panel.');
    } finally {
      setCreating(false);
    }
  };

  const revealKey = async ({ password }: { password: string }) => {
    if (!keyPanel) return;
    try { const response = await api.post(`/client/child-panels/${keyPanel.id}/key`, { password }); if (response.data?.success) setRevealedKey(response.data.data.key); }
    catch (error: any) { message.error(error.response?.data?.message || 'Không thể lấy key.'); }
  };
  
  const renew = async (panel: Panel) => {
    try { const response = await api.post(`/client/child-panels/${panel.id}/renew`); if (response.data?.success) { message.success(response.data.message); await loadPanels(); } }
    catch (error: any) { message.error(error.response?.data?.message || 'Không thể gia hạn.'); }
  };
  
  const copy = async (value: string) => { await navigator.clipboard.writeText(value); message.success('Đã sao chép.'); };
  const closeKey = () => { setKeyPanel(null); setRevealedKey(''); keyForm.resetFields(); };
  
  const columns = [
    { title: 'ID', dataIndex: 'id', width: 70, render: (value: number) => <Text strong>#{value}</Text> },
    { title: 'Tên miền', dataIndex: 'domain', render: (value: string) => <a href={`https://${value}`} target="_blank" rel="noreferrer"><GlobalOutlined /> {value}</a> },
    { title: 'Trạng thái', dataIndex: 'status', render: (value: string) => <Tag color={statusColor[value] || 'default'}>{statusText[value] || value}</Tag> },
    { title: 'DNS', dataIndex: 'dns_ready', render: (value: boolean) => <Tag color={value ? 'blue' : 'default'}>{value ? 'Đã cấu hình' : 'Chưa cấu hình'}</Tag> },
    { title: 'Hạn sử dụng', dataIndex: 'expired_at', width: 145, render: (value?: string) => value ? new Date(value).toLocaleDateString('vi-VN') : '--/--/----' },
    { title: 'Thao tác', width: 210, render: (_: unknown, panel: Panel) => <div className={styles.actions}><Button icon={<KeyOutlined />} onClick={() => setKeyPanel(panel)}>Xem key</Button><Popconfirm title="Gia hạn Child Panel" description={`Gia hạn thêm 1 tháng với phí ${money(data.monthly_fee)}?`} okText="Gia hạn" cancelText="Hủy" onConfirm={() => renew(panel)}><Button type="primary" ghost icon={<ReloadOutlined />}>Gia hạn</Button></Popconfirm></div> },
  ];

  if (view === 'create') {
    return (
      <ClientLayout>
        <div className={styles.page}>
          <div className={styles.wizardHeaderCard}>
            <div className={styles.wizardHeaderLeft}>
              <CrownOutlined className={styles.wizardCrown} />
              <div>
                <h5>Tạo Website SMM của riêng bạn</h5>
                <p>Sở hữu website SMM Panel riêng với tên miền và giao diện tùy chỉnh</p>
              </div>
            </div>
            <div className={styles.wizardHeaderRight}>
              <p>Phí tạo</p>
              <h4>{money(data.monthly_fee)}/Tháng</h4>
            </div>
          </div>

          <Card className={styles.wizardStepsCard}>
             <Steps current={step} items={[
               { title: 'Chuẩn bị Domain' }, 
               { title: 'Nhập thông tin' }, 
               { title: 'Hoàn thành' }
             ]} />
          </Card>

          <Card className={styles.wizardContentCard}>
             {step === 0 && (
                <div className={styles.step0}>
                   <div className={styles.stepTitle}>
                      <GlobalOutlined /> Bước 1: Chuẩn bị Domain
                   </div>
                   <div className={styles.domainRequirements}>
                      <div className={styles.reqCol}>
                         <h6>Domain hợp lệ cần có:</h6>
                         <ul>
                            <li><CheckCircleOutlined className={styles.iconSuccess} /> Đã được đăng ký và kích hoạt</li>
                            <li><CheckCircleOutlined className={styles.iconSuccess} /> Có quyền quản lý DNS</li>
                            <li><CheckCircleOutlined className={styles.iconSuccess} /> Không dùng tên miền miễn phí (.tk, .ml...)</li>
                         </ul>
                      </div>
                      <div className={styles.reqCol}>
                         <h6>Gợi ý nhà cung cấp:</h6>
                         <div className={styles.tags}>
                            <Tag>Namecheap</Tag><Tag>GoDaddy</Tag><Tag>Mat Bao</Tag><Tag>Tenten</Tag>
                         </div>
                         <Alert type="warning" showIcon icon={<InfoCircleOutlined />} title="Hãy đảm bảo bạn đã sở hữu tên miền trước khi tiếp tục." />
                      </div>
                   </div>
                   <div className={styles.domainCheck}>
                      <h6>Kiểm tra tên miền của bạn <span className={styles.required}>*</span></h6>
                      <div className={styles.checkInput}>
                         <Input 
                            size="large" 
                            placeholder="example.com" 
                            value={domain}
                            onChange={(e) => setDomain(e.target.value)}
                            onPressEnter={checkDomain}
                         />
                         <Button type="primary" size="large" icon={<SearchOutlined />} onClick={checkDomain} loading={checkingDomain}>
                            Kiểm tra
                         </Button>
                      </div>
                   </div>
                   <div className={styles.stepFooter}>
                      <Button size="large" onClick={() => setView('list')}>Hủy</Button>
                      <Button size="large" type="primary" onClick={checkDomain} loading={checkingDomain}>Tiếp tục</Button>
                   </div>
                </div>
             )}

             {step === 1 && (
                <div className={styles.step1}>
                   <div className={styles.stepTitle}>
                      <GlobalOutlined /> Bước 2: Xác nhận tạo Website
                   </div>
                   <div className={styles.confirmBox}>
                       <h6>Tên miền đã kiểm tra</h6>
                       <Input size="large" value={domain} disabled />
                   </div>
                   
                   <Alert 
                      className={styles.infoAlert}
                      type="info" 
                      showIcon 
                      icon={<InfoCircleOutlined />} 
                      title={<b>Quy trình cài đặt</b>}
                      description={
                        <ol>
                           <li>Hệ thống tạo hosting + Cloudflare cho domain của bạn.</li>
                           <li>Trỏ Nameserver domain về Cloudflare.</li>
                           <li>Sau khi DNS trỏ xong, truy cập vào đường link cài đặt.</li>
                           <li>Nhập <b>API Key</b> của bạn làm <b>Mã kích hoạt</b> để hoàn tất.</li>
                        </ol>
                      }
                   />

                   <div className={styles.agreeBox}>
                      <Checkbox>Tôi đồng ý với điều khoản sử dụng và xác nhận domain đã trỏ nameserver đúng</Checkbox>
                   </div>

                   <div className={styles.stepFooterSpace}>
                      <Button size="large" icon={<ArrowLeftOutlined />} onClick={() => setStep(0)}>Quay lại</Button>
                      <Button size="large" type="primary" onClick={submitCreate} loading={creating}>Tạo Website</Button>
                   </div>
                </div>
             )}

             {step === 2 && (
                <div className={styles.step2}>
                   <div className={styles.successHead}>
                      <CheckCircleOutlined className={styles.successIconLg} />
                      <h4>Website đã được tạo thành công!</h4>
                      <p>Hệ thống đã thêm domain vào Cloudflare và Hosting. Hãy làm theo các bước dưới để hoàn tất.</p>
                   </div>
                   <div className={styles.nsContainer}>
                      <div className={styles.nsBox}>
                         <div className={styles.nsInner}>
                            <p>Nameserver 1</p>
                            <span>{successData.ns1 || 'Đang cập nhật...'}</span>
                         </div>
                         <Button type="text" icon={<CopyOutlined />} onClick={() => copy(successData.ns1 || '')}>Copy</Button>
                      </div>
                      <div className={styles.nsBox}>
                         <div className={styles.nsInner}>
                            <p>Nameserver 2</p>
                            <span>{successData.ns2 || 'Đang cập nhật...'}</span>
                         </div>
                         <Button type="text" icon={<CopyOutlined />} onClick={() => copy(successData.ns2 || '')}>Copy</Button>
                      </div>
                   </div>
                   
                   <div className={styles.guideBoxes}>
                       <div className={`${styles.guideBox} ${styles.guideBoxBlue}`}>
                           <div className={styles.guideNum}>1</div>
                           <div>
                               <h6>Trỏ Nameserver về Cloudflare</h6>
                               <p>Thay đổi Nameserver của tên miền về <b>2 NS ở trên</b>. Thao tác thường mất vài phút.</p>
                           </div>
                       </div>
                       <div className={`${styles.guideBox} ${styles.guideBoxOrange}`}>
                           <div className={styles.guideNum}>2</div>
                           <div>
                               <h6>Chờ DNS cập nhật</h6>
                               <p>Thời gian DNS lan truyền thường <b>5–30 phút</b>, tối đa 24h.</p>
                           </div>
                       </div>
                       <div className={`${styles.guideBox} ${styles.guideBoxGreen}`}>
                           <div className={styles.guideNum}>3</div>
                           <div>
                               <h6>Cài đặt Website</h6>
                               <p>Sau khi DNS cập nhật, truy cập đường link cài đặt:</p>
                               <code>https://{successData.domain}/install</code>
                           </div>
                       </div>
                   </div>

                   <div className={styles.stepFooterCenter}>
                      <Button size="large" type="primary" onClick={() => { setView('list'); setStep(0); setDomain(''); }}>Xem danh sách</Button>
                   </div>
                </div>
             )}
          </Card>
        </div>
      </ClientLayout>
    );
  }

  return <ClientLayout><div className={styles.page}>
    <div className={styles.heading}><div><Title level={3}>Child Panel của bạn</Title><Text type="secondary">Tạo và quản lý website SMM mang thương hiệu riêng</Text></div><Button type="primary" icon={<PlusOutlined />} onClick={() => { setView('create'); setStep(0); setDomain(''); }}>Tạo Child Panel mới</Button></div>
    <Card className={styles.feeCard}><div><CrownOutlined /><span><Text strong>Child Panel SMM</Text><Text type="secondary">Sở hữu website riêng với thương hiệu của bạn</Text></span></div><div><Text type="secondary">Phí duy trì hàng tháng</Text><b>{money(data.monthly_fee)}</b></div></Card>
    <Card className={styles.tableCard}><Table rowKey="id" columns={columns} dataSource={data.panels} loading={loading} pagination={false} scroll={{ x: 900 }} locale={{ emptyText: <Empty image={Empty.PRESENTED_IMAGE_SIMPLE} description={<span>Chưa có Child Panel nào<br/><Button type="link" onClick={() => setView('create')}>Tạo Child Panel đầu tiên</Button></span>} /> }} /></Card>

    <Modal open={Boolean(keyPanel)} onCancel={closeKey} footer={null} title={`Key kích hoạt - ${keyPanel?.domain || ''}`} destroyOnHidden styles={{ mask: { background: 'rgba(15, 23, 42, 0.10)' } }}>
      {revealedKey ? <div className={styles.keyBox}><Text code>{revealedKey}</Text><Button type="primary" icon={<CopyOutlined />} onClick={() => copy(revealedKey)}>Sao chép</Button></div> : <Form form={keyForm} layout="vertical" onFinish={revealKey}><Form.Item name="password" label="Nhập mật khẩu tài khoản để xem key" rules={[{ required: true }]}><Input.Password autoFocus /></Form.Item><Button type="primary" htmlType="submit" block>Xác nhận</Button></Form>}
    </Modal>
  </div></ClientLayout>;
}
