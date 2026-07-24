'use client';

import { useCallback, useEffect, useState } from 'react';
import { Button, Card, DatePicker, Form, Input, InputNumber, Modal, Select, Space, Table, Tag, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { DeleteOutlined, EditOutlined, PlusOutlined, ReloadOutlined, SearchOutlined } from '@ant-design/icons';
import type { TablePaginationConfig } from 'antd/es/table';
import dayjs, { type Dayjs } from 'dayjs';
import api from '@/lib/axios';
import styles from './discounts.module.css';

const { Text, Title } = Typography;
type Discount = { id:number; code:string; type:'percent'|'fixed'; amount:number; min_order_amount:number; max_discount_amount:number; usage_limit:number; used_count:number; expired_at:string|null; status:'active'|'inactive' };
type FormValues = Omit<Discount, 'id'|'used_count'|'expired_at'> & { expired_at?:Dayjs|null };
const money = (value:number) => `${Number(value || 0).toLocaleString('vi-VN')}đ`;

export default function AdminDiscountsPage() {
  const [form] = Form.useForm<FormValues>();
  const [rows,setRows] = useState<Discount[]>([]);
  const [loading,setLoading] = useState(true);
  const [saving,setSaving] = useState(false);
  const [open,setOpen] = useState(false);
  const [editing,setEditing] = useState<Discount|null>(null);
  const [search,setSearch] = useState('');
  const [page,setPage] = useState({current:1,pageSize:10,total:0});

  const load = useCallback(async(current=1,pageSize=10,keyword=search)=>{setLoading(true);try{const response=await api.get('/admin/resources/discounts',{params:{page,per_page:pageSize,search:keyword}});const data=response.data.data;setRows(data.data||[]);setPage({current:data.current_page||current,pageSize:data.per_page||pageSize,total:data.total||0})}catch{message.error('Không thể tải danh sách mã giảm giá.')}finally{setLoading(false)}},[search]);
  useEffect(()=>{const timer=setTimeout(()=>void load(1,10,''),0);return()=>clearTimeout(timer)},[]); // eslint-disable-line react-hooks/exhaustive-deps

  const create=()=>{setEditing(null);form.resetFields();form.setFieldsValue({type:'percent',amount:0,min_order_amount:0,max_discount_amount:0,usage_limit:0,status:'active'});setOpen(true)};
  const edit=(item:Discount)=>{setEditing(item);form.setFieldsValue({...item,expired_at:item.expired_at?dayjs(item.expired_at):null});setOpen(true)};
  const save=async()=>{try{const values=await form.validateFields();setSaving(true);const data={...values,code:values.code.trim().toUpperCase(),expired_at:values.expired_at?.format('YYYY-MM-DD HH:mm:ss')||null};if(editing)await api.put(`/admin/resources/discounts/${editing.id}`,{data});else await api.post('/admin/resources/discounts',{data});message.success(editing?'Đã cập nhật mã giảm giá.':'Đã thêm mã giảm giá.');setOpen(false);await load(page.current,page.pageSize)}catch(error:unknown){if(typeof error==='object'&&error&&'errorFields'in error)return;const detail=error as {response?:{data?:{message?:string}}};message.error(detail.response?.data?.message||'Không thể lưu mã giảm giá.')}finally{setSaving(false)}};
  const remove=(item:Discount)=>Modal.confirm({title:`Xóa mã ${item.code}?`,content:'Hành động này không thể hoàn tác.',okText:'Xóa',okButtonProps:{danger:true},cancelText:'Đóng',onOk:async()=>{await api.delete(`/admin/resources/discounts/${item.id}`);message.success('Đã xóa mã giảm giá.');await load(page.current,page.pageSize)}});
  const columns=[
    {title:'ID',dataIndex:'id',width:70,render:(v:number)=><b>#{v}</b>},
    {title:'Mã code',dataIndex:'code',width:150,render:(v:string)=><Text strong copyable>{v}</Text>},
    {title:'Loại',dataIndex:'type',width:150,render:(v:string)=><Tag color={v==='percent'?'cyan':'gold'}>{v==='percent'?'Phần trăm (%)':'Số tiền (VNĐ)'}</Tag>},
    {title:'Giá trị',width:130,render:(_:unknown,x:Discount)=><b>{x.type==='percent'?`${Number(x.amount)}%`:money(x.amount)}</b>},
    {title:'Đơn tối thiểu',dataIndex:'min_order_amount',width:160,render:(v:number)=>money(v)},
    {title:'Giảm tối đa',dataIndex:'max_discount_amount',width:160,render:(v:number)=>Number(v)>0?money(v):'Không giới hạn'},
    {title:'Lượt dùng',width:130,render:(_:unknown,x:Discount)=><span>{x.used_count || 0}/{x.usage_limit>0?x.usage_limit:'∞'}</span>},
    {title:'Hết hạn',dataIndex:'expired_at',width:175,render:(v:string|null)=>v?dayjs(v).format('HH:mm DD/MM/YYYY'):'Vĩnh viễn'},
    {title:'Trạng thái',dataIndex:'status',width:120,render:(v:string)=><Tag color={v==='active'?'green':'red'}>{v==='active'?'Active':'Inactive'}</Tag>},
    {title:'Thao tác',width:110,fixed:'right' as const,render:(_:unknown,x:Discount)=><Space><Button size="small" type="primary" icon={<EditOutlined/>} onClick={()=>edit(x)}/><Button size="small" danger icon={<DeleteOutlined/>} onClick={()=>remove(x)}/></Space>},
  ];

  return <main className={styles.page}>
    <header className={styles.heading}><div><Title level={2}>Danh sách mã giảm giá</Title><Text type="secondary">Quản lý mã ưu đãi áp dụng cho đơn hàng</Text></div><Button type="primary" icon={<PlusOutlined/>} onClick={create}>Thêm mới</Button></header>
    <Card className={styles.card} title="Danh sách mã giảm giá" extra={<Space><Input className={styles.search} value={search} onChange={e=>setSearch(e.target.value)} onPressEnter={()=>void load(1,page.pageSize)} allowClear prefix={<SearchOutlined/>} placeholder="Tìm mã code"/><Button icon={<ReloadOutlined/>} onClick={()=>void load(1,page.pageSize)}>Làm mới</Button></Space>}>
      <Table rowKey="id" loading={loading} dataSource={rows} columns={columns} scroll={{x:1350}} pagination={{...page,showSizeChanger:true,pageSizeOptions:[10,25,50,100],showTotal:total=>`${total} mã giảm giá`}} onChange={(p:TablePaginationConfig)=>void load(p.current||1,p.pageSize||10)}/>
    </Card>
    <Modal className={styles.modal} open={open} title={editing?'Sửa mã giảm giá':'Thêm mã giảm giá'} okText={editing?'Cập nhật':'Lưu'} cancelText="Đóng" confirmLoading={saving} onOk={()=>void save()} onCancel={()=>setOpen(false)}>
      <Form form={form} layout="vertical" requiredMark={false}>
        <Form.Item name="code" label="Mã Code" rules={[{required:true,message:'Nhập mã giảm giá.'},{max:100}]}><Input onInput={e=>{e.currentTarget.value=e.currentTarget.value.toUpperCase()}}/></Form.Item>
        <Form.Item name="type" label="Loại giảm giá" rules={[{required:true}]}><Select options={[{value:'percent',label:'Phần trăm (%)'},{value:'fixed',label:'Số tiền cố định (VNĐ)'}]}/></Form.Item>
        <Form.Item name="amount" label="Giá trị giảm" rules={[{required:true,message:'Nhập giá trị giảm.'}]}><InputNumber min={0} style={{width:'100%'}}/></Form.Item>
        <Form.Item name="min_order_amount" label="Đơn tối thiểu"><InputNumber min={0} style={{width:'100%'}}/></Form.Item>
        <Form.Item name="max_discount_amount" label="Giảm tối đa (Cho %)"><InputNumber min={0} style={{width:'100%'}}/></Form.Item>
        <Form.Item name="usage_limit" label="Giới hạn lượt dùng (0 = KGH)"><InputNumber min={0} precision={0} style={{width:'100%'}}/></Form.Item>
        <Form.Item name="expired_at" label="Ngày hết hạn"><DatePicker showTime format="DD/MM/YYYY HH:mm" placeholder="Chọn ngày hết hạn" style={{width:'100%'}}/></Form.Item>
        <Form.Item name="status" label="Trạng thái" rules={[{required:true}]}><Select options={[{value:'active',label:'Active'},{value:'inactive',label:'Inactive'}]}/></Form.Item>
      </Form>
    </Modal>
  </main>;
}
