'use client';
import { useCallback, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Button, Card, Form, Input, InputNumber, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { ApiOutlined, CheckCircleOutlined, CloudDownloadOutlined, DeleteOutlined, DollarOutlined, EditOutlined, EyeInvisibleOutlined, FilterOutlined, MenuOutlined, PlusOutlined, ReloadOutlined, SearchOutlined, UnorderedListOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import api from '@/lib/axios';
import styles from './services.module.css';
const { Text, Title } = Typography;
const money = (v: number) => `${Math.round(Number(v || 0)).toLocaleString('vi-VN')}đ`;
const attributeOptions = [
    { value: 'best_seller', label: 'Bán chạy nhất' }, { value: 'new', label: 'Mới nhất' }, { value: 'run_now', label: 'Chạy ngay lập tức' },
    { value: 'fast', label: 'Nhanh nhất' }, { value: 'speed', label: 'Tốc độ cao' }, { value: 'slow', label: 'Chậm nhất' },
    { value: 'cancel_button', label: 'Có thể huỷ' }, { value: 'no_refill', label: 'Không bảo hành' }, { value: 'refill', label: 'Có bảo hành' },
    { value: 'refill_7_days', label: 'Bảo hành 7 ngày' }, { value: 'refill_15_days', label: 'Bảo hành 15 ngày' },
    { value: 'refill_30_days', label: 'Bảo hành 30 ngày' }, { value: 'refill_60_days', label: 'Bảo hành 60 ngày' },
    { value: 'refill_90_days', label: 'Bảo hành 90 ngày' }, { value: 'refill_365_days', label: 'Bảo hành 365 ngày' },
    { value: 'refill_1_year', label: 'Bảo hành 1 năm' }, { value: 'refill_lifetime', label: 'Bảo hành trọn đời' },
    { value: 'exclusive', label: 'Độc quyền' }, { value: 'suggested', label: 'Đề xuất sử dụng' }, { value: 'recommended', label: 'Được đề xuất' },
    { value: 'real_user', label: 'Người dùng thật' }, { value: 'self_produced', label: 'Tự sản xuất' },
];
type Option = {
    id: number;
    name: string;
};
type Service = {
    id: number;
    sort_order: number;
    name: string;
    provider?: number;
    provider_id: string;
    category_id: number;
    category?: Option;
    api_provider?: Option;
    type: string;
    rate: number;
    price_collaborator: number;
    price_agency: number;
    price_distributor: number;
    rate_original: number;
    min: number;
    max: number;
    status: string;
    mode: string;
    refill: boolean;
    cancel: boolean;
    warranty: number;
    attributes?: string[];
    note?: string;
};
export default function ServicesPage() {
    const router = useRouter(), [serviceForm] = Form.useForm(), [priceForm] = Form.useForm();
    const [rows, setRows] = useState<Service[]>([]), [categories, setCategories] = useState<Option[]>([]), [providers, setProviders] = useState<Option[]>([]), [stats, setStats] = useState({ total: 0, active: 0, inactive: 0, api: 0 }), [loading, setLoading] = useState(true), [saving, setSaving] = useState(false);
    const [page, setPage] = useState({ current: 1, pageSize: 10, total: 0 }), [pageSizeChoice, setPageSizeChoice] = useState<number | 'all'>(10), [selected, setSelected] = useState<React.Key[]>([]), [category, setCategory] = useState<number | undefined>(), [search, setSearch] = useState(''), [editing, setEditing] = useState<Service | null | undefined>(undefined), [pricing, setPricing] = useState<Service | null>(null);
    const load = useCallback(async (current = 1, pageSize: number | 'all' = 10) => { setLoading(true); try {
        const r = await api.get('/admin/services', { params: { page: current, per_page: pageSize, category_id: category, search } }), d = r.data.data, m = r.data.meta;
        setRows(d.data || []);
        setPage({ current: d.current_page || current, pageSize: Number(d.per_page) || 10, total: d.total || 0 });
        setCategories(m.categories || []);
        setProviders(m.providers || []);
        setStats({ total: m.total || 0, active: m.active || 0, inactive: m.inactive || 0, api: m.api || 0 });
    }
    catch {
        message.error('Không thể tải danh sách dịch vụ.');
    }
    finally {
        setLoading(false);
    } }, [category, search]);
    useEffect(() => { const timer = setTimeout(() => void load(1, pageSizeChoice), 0); return () => clearTimeout(timer); }, [load, pageSizeChoice]);
    const openCreate = () => { setEditing(null); serviceForm.resetFields(); serviceForm.setFieldsValue({ mode: 'option', type: 'Default', status: 'active', min: 10, max: 100000, rate: 0, refill: false, cancel: false, warranty: 0, attributes: [] }); };
    const openEdit = (x: Service) => { setEditing(x); serviceForm.setFieldsValue({ ...x, provider: x.provider || x.api_provider?.id, attributes: x.attributes || [] }); };
    const openPrice = (x: Service) => { setPricing(x); priceForm.setFieldsValue({ rate: x.rate, price_collaborator: x.price_collaborator, price_agency: x.price_agency, price_distributor: x.price_distributor }); };
    const saveService = async () => { try {
        const values = await serviceForm.validateFields();
        setSaving(true);
        if (editing)
            await api.put(`/admin/resources/services/${editing.id}`, { data: values });
        else
            await api.post('/admin/resources/services', { data: values });
        message.success(editing ? 'Đã cập nhật dịch vụ.' : 'Đã thêm dịch vụ.');
        setEditing(undefined);
        await load(page.current, pageSizeChoice);
    }
    catch (e: unknown) {
        if (typeof e === 'object' && e && 'errorFields' in e)
            return;
        message.error('Không thể lưu dịch vụ.');
    }
    finally {
        setSaving(false);
    } };
    const savePrice = async () => { if (!pricing)
        return; try {
        const values = await priceForm.validateFields();
        setSaving(true);
        await api.put(`/admin/resources/services/${pricing.id}`, { data: values });
        message.success('Đã cập nhật giá dịch vụ.');
        setPricing(null);
        await load(page.current, pageSizeChoice);
    }
    finally {
        setSaving(false);
    } };
    const remove = (ids: React.Key[]) => Modal.confirm({ title: `Xoá ${ids.length} dịch vụ?`, okText: 'Xoá', okButtonProps: { danger: true }, cancelText: 'Huỷ', onOk: async () => { await Promise.all(ids.map(id => api.delete(`/admin/resources/services/${String(id)}`))); setSelected([]); await load(page.current, pageSizeChoice); } });
    const sort = async (x: Service, v: number | null) => { await api.put(`/admin/resources/services/${x.id}`, { data: { sort_order: v ?? 0 } }); };
    const removeAll = () => Modal.confirm({ title: 'Xoá tất cả dịch vụ?', content: 'Hành động này không thể hoàn tác.', okText: 'Xoá tất cả', okButtonProps: { danger: true }, cancelText: 'Huỷ', onOk: async () => { await api.delete('/admin/services/all'); setSelected([]); message.success('Đã xoá tất cả dịch vụ.'); await load(1, pageSizeChoice); } });
    const cards = [[<UnorderedListOutlined key="1"/>, 'Tổng dịch vụ', stats.total, 'Tất cả'], [<CheckCircleOutlined key="2"/>, 'Đang hiển thị', stats.active, 'Public'], [<EyeInvisibleOutlined key="3"/>, 'Đang ẩn', stats.inactive, 'Private'], [<ApiOutlined key="4"/>, 'Dịch vụ API', stats.api, 'Connected']];
    const columns = [{ title: '', width: 38, render: () => <MenuOutlined className={styles.handle}/> }, { title: 'ID', dataIndex: 'id', width: 72 }, { title: 'Ưu tiên', dataIndex: 'sort_order', width: 110, render: (v: number, x: Service) => <InputNumber value={v} min={0} onChange={n => void sort(x, n)} className={styles.order}/> }, { title: 'Thao tác', width: 90, render: (_: unknown, x: Service) => <Space size={4}><Button size="small" icon={<EditOutlined />} onClick={() => openEdit(x)}/><Button size="small" className={styles.priceButton} icon={<DollarOutlined />} onClick={() => openPrice(x)}/></Space> }, { title: 'Tên dịch vụ', dataIndex: 'name', width: 270, render: (v: string) => <Text strong className={styles.serviceName}>{v}</Text> }, { title: 'Nguồn', width: 165, render: (_: unknown, x: Service) => x.mode === 'option' ? <ul className={styles.source}><li>{x.api_provider?.name || 'Provider'}</li><li className={styles.red}>{x.provider_id}</li><li>Rate USD: ${Number(x.rate_original || 0).toFixed(3)}</li><li className={styles.blue}>Rate VND: {money(x.rate_original)}</li></ul> : <Tag>Thủ công</Tag> }, { title: 'Phân loại', width: 280, render: (_: unknown, x: Service) => <div className={styles.category}><Tag color="blue">{x.category?.name || 'Chưa phân loại'}</Tag><small>Cấp 1</small></div> }, { title: 'Loại', dataIndex: 'type', width: 90, render: (v: string) => <Tag>{v || 'Default'}</Tag> }, { title: 'Giá (1K)', width: 155, render: (_: unknown, x: Service) => <div className={styles.rates}><span>Member: <b>{money(x.rate)}</b></span><span>Silver: <b>{money(x.price_collaborator)}</b></span><span>Gold: <b>{money(x.price_agency)}</b></span><span>Plat: <b>{money(x.price_distributor)}</b></span></div> }, { title: 'Min/Max', width: 130, render: (_: unknown, x: Service) => `${x.min.toLocaleString('vi-VN')}/${x.max.toLocaleString('vi-VN')}` }, { title: 'Ghi chú', dataIndex: 'note', width: 90, render: (v: string) => v || '-' }, { title: 'Trạng thái', dataIndex: 'status', width: 110, render: (v: string) => <Tag color={v === 'active' ? 'green' : 'red'}>{v === 'active' ? 'Hiển thị' : 'Ẩn'}</Tag> }];
    const serviceFields = <Form form={serviceForm} layout="vertical"><Form.Item name="category_id" label="Phân loại" rules={[{ required: true }]}><Select showSearch optionFilterProp="label" options={categories.map(x => ({ value: x.id, label: x.name }))}/></Form.Item><Form.Item name="name" label="Tên dịch vụ" rules={[{ required: true }]}><Input /></Form.Item><div className={styles.formGrid}><Form.Item name="mode" label="Chế độ"><Select options={[{ value: 'option', label: 'Option' }, { value: 'manual', label: 'Manual' }]}/></Form.Item><Form.Item name="provider" label="API Provider"><Select allowClear options={providers.map(x => ({ value: x.id, label: x.name }))}/></Form.Item><Form.Item name="provider_id" label="ID dịch vụ"><Input /></Form.Item><Form.Item name="type" label="Type"><Select options={['Default', 'Custom Comments', 'Package', 'Subscriptions'].map(value => ({ value, label: value }))}/></Form.Item><Form.Item name="min" label="Mua tối thiểu"><InputNumber min={0}/></Form.Item><Form.Item name="max" label="Mua tối đa"><InputNumber min={0}/></Form.Item><Form.Item name="rate" label="Giá trên 1000/Lượt"><InputNumber min={0}/></Form.Item><Form.Item name="status" label="Trạng thái"><Select options={[{ value: 'active', label: 'Hoạt động' }, { value: 'inactive', label: 'Ngừng hoạt động' }]}/></Form.Item><Form.Item name="refill" label="Refill"><Select options={[{ value: true, label: 'Có' }, { value: false, label: 'Không' }]}/></Form.Item><Form.Item name="cancel" label="Cancel"><Select options={[{ value: true, label: 'Có' }, { value: false, label: 'Không' }]}/></Form.Item><Form.Item name="warranty" label="Bảo hành"><InputNumber min={0}/></Form.Item></div><Form.Item name="attributes" label="Thêm thuộc tính"><Select mode="multiple" showSearch optionFilterProp="label" placeholder="Chọn thuộc tính" options={attributeOptions}/></Form.Item><Form.Item name="note" label="Ghi chú"><Input.TextArea rows={3}/></Form.Item></Form>;
    return <main className={styles.page}><header className={styles.heading}><div><Title level={2}>Danh sách dịch vụ</Title><Text type="secondary">Quản lý, thiết lập giá và cấu hình toàn bộ dịch vụ của hệ thống</Text></div><Space><Button icon={<CloudDownloadOutlined />} onClick={() => router.push('/admin/services/import-services')}>Nhập dịch vụ</Button><Button type="primary" icon={<PlusOutlined />} onClick={openCreate}>Thêm mới</Button></Space></header><section className={styles.stats}>{cards.map((x, i) => <Card className={styles.stat} key={String(x[1])}><span className={`${styles.statIcon} ${styles[`tone${i}`]}`}>{x[0]}</span><div><small>{x[1]}</small><strong>{Number(x[2]).toLocaleString('vi-VN')}</strong><em className={styles[`tone${i}`]}>{x[3]}</em></div></Card>)}</section>
<Card className={styles.card} title="Danh sách dịch vụ" extra={<Space wrap><Button className={styles.yellow} disabled={!selected.length} icon={<EditOutlined />} onClick={() => router.push(`/admin/services/update-attributes?ids=${selected.join(',')}`)}>Sửa thuộc tính <b className={styles.countBadge}>{selected.length}</b></Button><Button className={styles.green} disabled={!selected.length} icon={<EditOutlined />} onClick={() => router.push(`/admin/services/update-content?ids=${selected.join(',')}`)}>Sửa nội dung <b className={styles.countBadge}>{selected.length}</b></Button><Button danger disabled={!selected.length} icon={<DeleteOutlined />} onClick={() => remove(selected)}>Xoá <b className={styles.countBadge}>{selected.length}</b></Button><Button danger icon={<DeleteOutlined />} onClick={removeAll}>Xoá tất cả</Button></Space>}>
<div className={styles.filters}><div><b><FilterOutlined /> Lọc theo phân loại</b><Select allowClear value={category} onChange={setCategory} placeholder="Chọn phân loại" options={categories.map(x => ({ value: x.id, label: x.name }))}/></div><Button icon={<ReloadOutlined />} onClick={() => void load(1, pageSizeChoice)}>Làm mới</Button></div>
<div className={styles.tableTools}><span>Xem <Select value={pageSizeChoice} onChange={setPageSizeChoice} options={[{ value: 10, label: '10' }, { value: 25, label: '25' }, { value: 50, label: '50' }, { value: 100, label: '100' }, { value: 1000, label: '1000' }, { value: 'all', label: 'Tất cả' }]}/> mục</span><Input value={search} onChange={e => setSearch(e.target.value)} onPressEnter={() => void load(1, pageSizeChoice)} prefix={<SearchOutlined />}/></div>
<Table rowKey="id" loading={loading} dataSource={rows} columns={columns} rowSelection={{ selectedRowKeys: selected, onChange: setSelected }} scroll={{ x: 1550 }} pagination={pageSizeChoice === 'all' ? false : { ...page, showSizeChanger: false }} onChange={(p: TablePaginationConfig) => void load(p.current || 1, pageSizeChoice)}/></Card><Modal width={980} open={editing !== undefined} title={editing ? 'Chỉnh sửa dịch vụ' : 'Thêm dịch vụ mới'} okText={editing ? 'Cập nhật' : 'Thêm mới'} cancelText="Quay lại" confirmLoading={saving} onOk={() => void saveService()} onCancel={() => setEditing(undefined)}>{serviceFields}</Modal><Modal open={Boolean(pricing)} title={`Cập nhật giá dịch vụ #${pricing?.id || ''}`} okText="Cập nhật giá" cancelText="Huỷ" confirmLoading={saving} onOk={() => void savePrice()} onCancel={() => setPricing(null)}><Form form={priceForm} layout="vertical"><Form.Item name="rate" label="Giá Member" rules={[{ required: true }]}><InputNumber min={0} style={{ width: '100%' }}/></Form.Item><Form.Item name="price_collaborator" label="Giá Silver"><InputNumber min={0} style={{ width: '100%' }}/></Form.Item><Form.Item name="price_agency" label="Giá Gold"><InputNumber min={0} style={{ width: '100%' }}/></Form.Item><Form.Item name="price_distributor" label="Giá Platinum"><InputNumber min={0} style={{ width: '100%' }}/></Form.Item></Form></Modal></main>;
}
