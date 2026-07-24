'use client';
/* eslint-disable @next/next/no-img-element -- Provider favicons come from arbitrary external domains. */

import { useCallback,useEffect,useState } from 'react';
import { Button,Card,Dropdown,Form,Input,InputNumber,Modal,Select,Space,Switch,Table,Tag,Typography } from 'antd'; import { message } from '@/lib/antd-message';
import { AppstoreOutlined,ArrowLeftOutlined,CheckCircleFilled,CustomerServiceOutlined,DatabaseOutlined,DeleteOutlined,DollarOutlined,EditOutlined,InfoCircleOutlined,KeyOutlined,LinkOutlined,MoreOutlined,PauseCircleFilled,PlusOutlined,SaveOutlined,ShoppingCartOutlined,SyncOutlined,WalletOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import api from '@/lib/axios';
import baseStyles from './providers.module.css';
import summaryStyles from './provider-summary.module.css';
import paginationStyles from './pagination.module.css';
import balanceStyles from './balance.module.css';

const { Text,Title }=Typography;
const styles={ ...baseStyles,...summaryStyles };
type Provider={ id: number; name: string; type: string; api_url: string; api_key?: string; balance: number|string; currency?: string; balance_format?: string; count_categories?: number; count_services?: number; count_orders?: number; revenue?: number; profit?: number; sync_services: boolean; auto_update_price: boolean; auto_update_name: boolean; filter_html: boolean; increase_rate: number; increase_rate_collaborator: number; increase_rate_agency: number; increase_rate_distributor: number; exchange_rate: number; rate_per: number; price_format?: string; status: string; note?: string; };
type ProviderForm=Omit<Provider,'id'|'balance'|'balance_format'|'count_categories'|'count_services'|'count_orders'|'revenue'|'profit'>;
type ProviderMeta={ total: number; active: number; inactive: number; total_balance_vnd: number; };
const money=(value: number|string|undefined) => `${Math.round(Number(value||0)).toLocaleString('vi-VN')}đ`;
const providerBalance=(provider: Provider) => {
  const balance=Number(provider.balance||0);
  const exchangeRate=Math.max(1,Number(provider.exchange_rate||1));
  const isVnd=(provider.currency||'').toUpperCase()==='VND';
  return {
    usd: isVnd? balance/exchangeRate:balance,
    vnd: isVnd? balance:balance*exchangeRate,
  };
};
const yesNo=(on: boolean) => <Tag className={styles.flag} color={on? 'success':'default'}>{on? 'ON':'OFF'}</Tag>;
const UnitInput=({ unit,min,value,onChange }: { unit: string; min?: number; value?: number|null; onChange?: (value: number|null) => void; }) => <Space.Compact block><InputNumber min={min} value={value} onChange={onChange} style={{ width: '100%' }} /><Button disabled>{unit}</Button></Space.Compact>;

function ProviderFavicon({ provider }: { provider: Provider; }) {
  const [favicon,setFavicon]=useState('');

  useEffect(() => {
    let objectUrl='';
    let active=true;
    api.get(`/admin/providers/${provider.id}/favicon`,{ responseType: 'blob' })
      .then(response => {
        if(!active) return;
        objectUrl=URL.createObjectURL(response.data);
        setFavicon(objectUrl);
      })
      .catch(() => undefined);
    return () => {
      active=false;
      if(objectUrl) URL.revokeObjectURL(objectUrl);
    };
  },[provider.id,provider.api_url]);

  return <div className={styles.providerIcon}>
    {favicon
      ? <img className={styles.providerFavicon} src={favicon} alt="" onError={() => setFavicon('')} />
      :(provider.name||provider.api_url||'A').slice(0,1).toUpperCase()}
  </div>;
}

export default function AdminProvidersPage() {
  const [form]=Form.useForm<ProviderForm>();
  const selectedCurrency=Form.useWatch('currency',form);
  const [rows,setRows]=useState<Provider[]>([]),[loading,setLoading]=useState(true),[saving,setSaving]=useState(false),[syncingBalances,setSyncingBalances]=useState(false);
  const [meta,setMeta]=useState<ProviderMeta>({ total: 0,active: 0,inactive: 0,total_balance_vnd: 0 });
  const [editing,setEditing]=useState<Provider|null|undefined>(undefined),[page,setPage]=useState({ current: 1,pageSize: 20,total: 0 });
  const load=useCallback(async (current=1,pageSize=20) => { setLoading(true); try { const response=await api.get('/admin/providers',{ params: { page: current,per_page: pageSize } }); const data=response.data.data; setRows(data.data||[]); setMeta(response.data.meta||{ total: data.total||0,active: 0,inactive: 0,total_balance_vnd: 0 }); setPage({ current: data.current_page||current,pageSize: data.per_page||pageSize,total: data.total||0 }); } catch { message.error('Không thể tải danh sách nhà cung cấp.'); } finally { setLoading(false); } },[]);
  useEffect(() => { const timer=setTimeout(() => void load(),0); return () => clearTimeout(timer); },[load]);

  const defaults: Partial<ProviderForm>={ type: 'standard',currency: 'USD',sync_services: true,auto_update_price: true,increase_rate: 0,increase_rate_collaborator: 0,increase_rate_agency: 0,increase_rate_distributor: 0,exchange_rate: 1,rate_per: 1000,price_format: '1000',auto_update_name: true,filter_html: true,status: 'active',note: '' };
  const create=() => { setEditing(null); form.resetFields(); form.setFieldsValue(defaults); };
  const edit=(item: Provider) => { setEditing(item); form.setFieldsValue({ ...defaults,...item }); };
  const save=async () => { try { const values=await form.validateFields(); setSaving(true); const response=editing? await api.put(`/admin/providers/${editing.id}`,{ data: values }):await api.post('/admin/providers',{ data: values }); if(response.data?.success===false) throw new Error(response.data.message); message.success(response.data?.message||'Đã lưu cấu hình API.'); setEditing(undefined); await load(page.current,page.pageSize); } catch(error: unknown) { if(typeof error==='object'&&error&&'errorFields' in error) return; const detail=error as { message?: string; response?: { data?: { message?: string; }; }; }; message.error(detail.response?.data?.message||detail.message||'Không thể lưu cấu hình API.'); } finally { setSaving(false); } };
  const toggle=async (item: Provider) => { try { await api.put(`/admin/resources/providers/${item.id}`,{ data: { status: item.status==='active'? 'inactive':'active' } }); await load(page.current,page.pageSize); } catch { message.error('Không thể cập nhật trạng thái.'); } };
  const action=(item: Provider,path: string,title: string) => Modal.confirm({ title,content: 'Bạn có chắc chắn muốn thực hiện thao tác này?',okText: 'Đồng ý',cancelText: 'Đóng',onOk: async () => { try { const response=await api.post(`/admin/providers/${item.id}/${path}`); if(response.data?.success===false) throw new Error(response.data.message); message.success(response.data?.message||'Thao tác thành công.'); await load(page.current,page.pageSize); } catch(error: unknown) { const detail=error as { message?: string; response?: { data?: { message?: string; }; }; }; message.error(detail.response?.data?.message||detail.message||'Không thể kết nối API Provider.'); } } });
  const remove=(item: Provider) => Modal.confirm({ title: `Xóa ${item.name}?`,content: 'Hành động này không thể hoàn tác.',okText: 'Xóa',okButtonProps: { danger: true },cancelText: 'Đóng',onOk: async () => { await api.delete(`/admin/resources/providers/${item.id}`); message.success('Đã xóa nhà cung cấp.'); await load(page.current,page.pageSize); } });
  const syncBalances=async () => {
    setSyncingBalances(true);
    try {
      const response=await api.post('/admin/providers/sync-balances');
      const failed=Number(response.data?.data?.failed||0);
      if(failed>0) message.warning(response.data.message);
      else message.success(response.data.message||'Đã đồng bộ số dư tất cả nhà cung cấp.');
      await load(page.current,page.pageSize);
    } catch(error: unknown) {
      const detail=error as { response?: { data?: { message?: string; }; }; };
      message.error(detail.response?.data?.message||'Không thể đồng bộ số dư nhà cung cấp.');
    } finally {
      setSyncingBalances(false);
    }
  };

  const columns=[
    { title: 'Nhà cung cấp',width: 300,render: (_: unknown,x: Provider) => <div className={styles.website}><ProviderFavicon provider={x} /><div className={styles.providerInfo}><b>{x.name||'API Provider'}</b><a href={x.api_url} target="_blank" rel="noreferrer"><LinkOutlined /> {x.api_url}</a><Text type="secondary"><KeyOutlined /> {x.api_key? `${x.api_key.slice(0,8)}••••${x.api_key.slice(-4)}`:'API key được bảo mật'}</Text></div></div> },
    { title: 'Loại',dataIndex: 'type',width: 105,render: (v: string) => <span className={styles.type}>{(v||'-').toUpperCase()}</span> },
    { title: 'Số dư',width: 165,render: (_: unknown,x: Provider) => { const balance=providerBalance(x); return <div className={`${styles.balanceBlock} ${balanceStyles.dualBalance}`}><b>${balance.usd.toLocaleString('en-US',{ minimumFractionDigits: 2,maximumFractionDigits: 2 })}</b><strong>≈ {Math.round(balance.vnd).toLocaleString('vi-VN')}đ</strong><span>Tỷ giá {Number(x.exchange_rate||1).toLocaleString('vi-VN')}đ/USD</span></div>; } },
    { title: 'Hoạt động',width: 245,render: (_: unknown,x: Provider) => <div className={styles.stats}><div><span><AppstoreOutlined /> Chuyên mục</span><b>{x.count_categories||0}</b></div><div><span><DatabaseOutlined /> Dịch vụ</span><b>{Number(x.count_services||0).toLocaleString('vi-VN')}</b></div><div><span><ShoppingCartOutlined /> Đơn hàng</span><b>{Number(x.count_orders||0).toLocaleString('vi-VN')}</b></div><div className={styles.moneyStat}><span>Doanh thu</span><b className={styles.revenue}>{money(x.revenue)}</b></div><div className={styles.moneyStat}><span>Lợi nhuận</span><b className={styles.profit}>{money(x.profit)}</b></div></div> },
    { title: 'Cấu hình đồng bộ',width: 255,render: (_: unknown,x: Provider) => <div className={styles.details}><span>Đồng bộ dịch vụ {yesNo(x.sync_services)}</span><span>Cập nhật giá {yesNo(x.auto_update_price)}</span><span>Cập nhật tên {yesNo(x.auto_update_name)}</span><span>Lọc HTML {yesNo(x.filter_html)}</span><div className={styles.rateInfo}><span>Tăng giá <b>{x.increase_rate||0}%</b></span><span>Tỷ giá <b>{Number(x.exchange_rate||1).toLocaleString('vi-VN')}đ</b></span></div></div> },
    { title: 'Trạng thái',width: 145,render: (_: unknown,x: Provider) => <div className={`${styles.status} ${x.status==='active'? styles.active:''}`}><Switch size="small" checked={x.status==='active'} onChange={() => void toggle(x)} /><span>{x.status==='active'? <><CheckCircleFilled /> Hoạt động</>:'Tạm dừng'}</span></div> },
    { title: '',width: 70,fixed: 'right' as const,render: (_: unknown,x: Provider) => <Dropdown trigger={['click']} placement="bottomRight" menu={{ items: [{ key: 'edit',icon: <EditOutlined />,label: 'Chỉnh sửa',onClick: () => edit(x) },{ key: 'balance',icon: <DollarOutlined />,label: 'Cập nhật số dư',onClick: () => action(x,'update-balance','Cập nhật số dư') },{ key: 'prices',icon: <SyncOutlined />,label: 'Tính lại giá dịch vụ',onClick: () => action(x,'update-prices','Tính lại giá dịch vụ') },{ type: 'divider' },{ key: 'delete',danger: true,icon: <DeleteOutlined />,label: 'Xóa',onClick: () => remove(x) }] }}><Button className={styles.moreButton} icon={<MoreOutlined />} aria-label={`Thao tác với ${x.name}`} /></Dropdown> },
  ];

  if(editing!==undefined) return <main className={styles.page}><Button className={styles.back} icon={<ArrowLeftOutlined />} onClick={() => setEditing(undefined)}>Quay lại</Button><div className={styles.editorLayout}><div><Card className={styles.section} title={<span className={styles.blueTitle}>⌁ Thông tin kết nối API</span>}><Form form={form} layout="vertical" requiredMark={false}><Form.Item name="type" label="Loại API *" rules={[{ required: true }]}><Select options={[{ value: 'standard',label: 'Standard (JAP, Smartpanel,...)' },{ value: 'custom',label: 'Custom System' }]} /></Form.Item><Form.Item name="api_url" label="Domain *" extra="Nhập đầy đủ URL kèm https:// hoặc http://" rules={[{ required: true,message: 'Nhập domain API.' },{ type: 'url',message: 'Domain API chưa hợp lệ.' }]}><Input prefix={<LinkOutlined />} placeholder="VD: https://domain.com/" /></Form.Item><Form.Item name="api_key" label="API Key *" rules={[{ required: true,message: 'Nhập API Key.' }]}><Input.Password placeholder="Nhập API Key" /></Form.Item><Form.Item name="currency" label="Loại tiền tệ *" rules={[{ required: true,message: 'Vui lòng chọn loại tiền tệ.' }]}><Select showSearch optionFilterProp="label" options={[{ value: 'USD',label: 'USD (Đô la Mỹ)' },{ value: 'VND',label: 'VND (Việt Nam Đồng)' }]} /></Form.Item><Form.Item name="name" label="Tên gợi nhớ *" rules={[{ required: true,message: 'Nhập tên gợi nhớ.' }]}><Input placeholder="Ví dụ: Site A, Site B..." /></Form.Item>{editing&&<Form.Item name="status" label="Trạng thái"><Select options={[{ value: 'active',label: 'Hoạt động' },{ value: 'inactive',label: 'Ngừng hoạt động' }]} /></Form.Item>}</Form></Card><Card className={styles.section} title={<span className={styles.greenTitle}>☷ Cài đặt đồng bộ dữ liệu</span>}><Form form={form} layout="vertical" requiredMark={false}><div className={styles.grid2}><Form.Item name="sync_services" label="Đồng bộ dịch vụ từ API *"><Select options={[{ value: true,label: 'ON - Đồng bộ tự động' },{ value: false,label: 'OFF - Tắt đồng bộ' }]} /></Form.Item><Form.Item name="auto_update_price" label="Cập nhật giá bán tự động *"><Select options={[{ value: true,label: 'ON - Cập nhật tự động' },{ value: false,label: 'OFF - Không cập nhật' }]} /></Form.Item></div><div className={styles.grid4}><Form.Item name="increase_rate" label="% Tăng giá bán lẻ"><UnitInput unit="%" /></Form.Item><Form.Item name="increase_rate_collaborator" label="% Tăng giá CTV"><UnitInput unit="%" /></Form.Item><Form.Item name="increase_rate_agency" label="% Tăng giá Đại lý"><UnitInput unit="%" /></Form.Item><Form.Item name="increase_rate_distributor" label="% Tăng giá NPP"><UnitInput unit="%" /></Form.Item></div><div className={styles.grid2}>{selectedCurrency!=='VND'&&<Form.Item name="exchange_rate" label="Tỷ giá tiền tệ quốc tế *" extra="Nếu giá API là USD, nhập tỷ giá (ví dụ: 25000)." rules={[{ required: true }]}><UnitInput unit="VND" min={1} /></Form.Item>}<Form.Item name="rate_per" label="Định dạng giá bán của API *"><Select options={[{ value: 1000,label: 'Giá của 1000 lượt' },{ value: 1,label: 'Giá của 1 lượt' }]} /></Form.Item><Form.Item name="auto_update_name" label="Cập nhật tên & mô tả tự động *"><Select options={[{ value: true,label: 'ON - Cập nhật tự động' },{ value: false,label: 'OFF - Giữ nguyên' }]} /></Form.Item><Form.Item name="filter_html" label="Lọc HTML trong nội dung API *"><Select options={[{ value: true,label: 'ON - Kích hoạt bảo vệ' },{ value: false,label: 'OFF - Tắt' }]} /></Form.Item></div><Form.Item name="note" label="Ghi chú quan trọng"><Input.TextArea rows={3} placeholder="Ghi chú nội bộ..." /></Form.Item><Button block type="primary" size="large" icon={<SaveOutlined />} loading={saving} onClick={() => void save()}>Lưu cấu hình API</Button></Form></Card></div><aside><Card className={styles.help} title={<span><InfoCircleOutlined /> Lưu ý</span>}><p>💡 <b>Mục đích:</b> Chức năng này cho phép bạn bán lại sản phẩm của website khác trên chính website của bạn.</p><p>⚠️ <b>Lưu ý quan trọng!</b><br />Nếu cấu hình đúng nhưng không hiện số dư, máy chủ có thể không kết nối được API đích.</p><Button block className={styles.guide}>▣ Xem hướng dẫn xử lý</Button></Card><Card className={styles.help}><p>🌐 <b>API SMMPANEL2</b><br /><Text type="success">Miễn phí</Text></p><hr /><p>💰 <b>API ngoài hệ sinh thái</b><br /><Text type="danger">Phí tích hợp: thương lượng</Text></p><Button block icon={<CustomerServiceOutlined />}>Liên hệ hỗ trợ kết nối API</Button></Card></aside></div></main>;

  return <main className={styles.page}><header className={styles.heading}><div><Title level={2}>API Providers</Title><Text type="secondary">Theo dõi số dư, dịch vụ và kết nối nhà cung cấp tại một nơi</Text></div><Space wrap><Button size="large" icon={<SyncOutlined spin={syncingBalances} />} loading={syncingBalances} onClick={() => void syncBalances()}>Đồng bộ số dư</Button><Button type="primary" size="large" icon={<PlusOutlined />} onClick={create}>Thêm provider</Button></Space></header><section className={styles.summaryCards}><Card><span className={styles.summaryIcon}><DatabaseOutlined /></span><div><small>Tổng nhà cung cấp</small><b>{meta.total.toLocaleString('vi-VN')}</b></div></Card><Card><span className={`${styles.summaryIcon} ${styles.summaryActive}`}><CheckCircleFilled /></span><div><small>Đang hoạt động</small><b>{meta.active.toLocaleString('vi-VN')}</b></div></Card><Card><span className={`${styles.summaryIcon} ${styles.summaryPaused}`}><PauseCircleFilled /></span><div><small>Tạm dừng</small><b>{meta.inactive.toLocaleString('vi-VN')}</b></div></Card><Card><span className={`${styles.summaryIcon} ${styles.summaryBalance}`}><WalletOutlined /></span><div><small>Tổng số dư (VND)</small><b>{money(meta.total_balance_vnd)}</b></div></Card></section><Card className={`${styles.tableCard} ${paginationStyles.compactPagination}`}><Table rowKey="id" columns={columns} dataSource={rows} loading={loading} scroll={{ x: 1265 }} pagination={{ ...page,showSizeChanger: true,showLessItems: true,locale: { items_per_page: '/ trang' } }} onChange={(p: TablePaginationConfig) => void load(p.current||1,p.pageSize||20)} /></Card></main>;
}
