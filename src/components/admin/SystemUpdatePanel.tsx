'use client';

import { useEffect, useState } from 'react';
import {
  CloudDownloadOutlined, CodeOutlined, DeleteOutlined,
  FileTextOutlined, HistoryOutlined, ReloadOutlined, RocketOutlined, ThunderboltOutlined,
} from '@ant-design/icons';
import { Alert, Button, Card, Descriptions, Modal, Progress, Space, Spin, Statistic, Table, Tag, Typography } from 'antd';
import { message } from '@/lib/antd-message';
import api from '@/lib/axios';
import styles from './SystemUpdatePanel.module.css';

const { Text, Title } = Typography;

type Status = {
  version: string;
  php_version: string;
  laravel_version: string;
  next_version: string | null;
  last_checked_at: string | null;
  requirements: Record<string, boolean>;
};
type Release = { version: string; download_url: string; sha256: string; changelog?: string };
type UpdateStep = { message: string; status: 'success' | 'error'; time: string };
type UpdateLog = {
  id: number; from_version?: string; to_version?: string; status: string; file_name?: string;
  migrations: number; admin_name?: string; message?: string; log?: UpdateStep[]; created_at: string;
};

const formatDate = (value?: string | null) => value ? new Date(value).toLocaleString('vi-VN') : 'Chưa kiểm tra';

export default function SystemUpdatePanel() {
  const [status, setStatus] = useState<Status | null>(null);
  const [history, setHistory] = useState<UpdateLog[]>([]);
  const [release, setRelease] = useState<Release | null>(null);
  const [updateAvailable, setUpdateAvailable] = useState(false);
  const [checking, setChecking] = useState(false);
  const [installing, setInstalling] = useState(false);
  const [steps, setSteps] = useState<UpdateStep[]>([]);
  const [viewing, setViewing] = useState<UpdateLog | null>(null);

  const loadStatus = async () => {
    const response = await api.get('/admin/system-update');
    setStatus(response.data.data);
    setHistory(response.data.history || []);
  };

  useEffect(() => {
    void Promise.resolve().then(loadStatus).catch(() => message.error('Không thể tải thông tin hệ thống.'));
  }, []);

  const check = async () => {
    setChecking(true);
    try {
      const response = await api.get('/admin/system-update/check');
      setRelease(response.data.data);
      setUpdateAvailable(Boolean(response.data.update_available));
      message.success(response.data.update_available ? `Có phiên bản ${response.data.data.version}.` : 'Hệ thống đang ở phiên bản mới nhất.');
      await loadStatus();
    } catch (error: unknown) {
      const detail = error as { response?: { data?: { message?: string } } };
      message.error(detail.response?.data?.message || 'Không thể kiểm tra cập nhật.');
    } finally {
      setChecking(false);
    }
  };

  const install = () => {
    if (!release) return;
    Modal.confirm({
      title: updateAvailable ? `Cập nhật lên phiên bản ${release.version}?` : `Cài đặt lại phiên bản ${release.version}?`,
      content: 'Hệ thống sẽ sao lưu, bật bảo trì, cập nhật Laravel và build Next.js trong cùng một lần.',
      okText: updateAvailable ? 'Cập nhật ngay' : 'Cài đặt lại',
      cancelText: 'Hủy',
      onOk: async () => {
        setInstalling(true); setSteps([]);
        try {
          const response = await api.post('/admin/system-update/install', release, { timeout: 30 * 60 * 1000 });
          setSteps(response.data.steps || []);
          setUpdateAvailable(false);
          message.success(response.data.message);
        } catch (error: unknown) {
          const detail = error as { response?: { data?: { message?: string; steps?: UpdateStep[] } } };
          setSteps(detail.response?.data?.steps || []);
          message.error(detail.response?.data?.message || 'Cập nhật thất bại; hệ thống đã thử khôi phục bản cũ.');
        } finally {
          setInstalling(false);
          await loadStatus();
        }
      },
    });
  };

  const removeLog = async (id: number) => {
    await api.delete(`/admin/system-update/history/${id}`);
    setHistory(current => current.filter(item => item.id !== id));
    message.success('Đã xóa nhật ký.');
  };

  const removeAll = () => Modal.confirm({
    title: 'Xóa toàn bộ lịch sử cập nhật?',
    okText: 'Xóa tất cả', okType: 'danger', cancelText: 'Hủy',
    onOk: async () => { await api.delete('/admin/system-update/history'); setHistory([]); },
  });

  if (!status) return <div className={styles.loading}><Spin size="large" /></div>;
  const ready = Object.values(status.requirements).every(Boolean);

  return <main className={styles.page}>
    <header className={styles.heading}>
      <div><Title level={2}>Cập nhật hệ thống</Title><Text type="secondary">Kiểm tra phiên bản và nâng cấp đồng bộ PHP, Laravel, Next.js</Text></div>
      <Button icon={<ReloadOutlined />} loading={checking} onClick={() => void check()}>Kiểm tra cập nhật</Button>
    </header>

    <div className={styles.stats}>
      <Card><Statistic title="PHIÊN BẢN" value={status.version} prefix={<RocketOutlined />} /></Card>
      <Card><Statistic title="LẦN CUỐI" value={formatDate(status.last_checked_at)} prefix={<HistoryOutlined />} /></Card>
      <Card><Statistic title="PHP" value={status.php_version} prefix={<CodeOutlined />} /></Card>
      <Card><Statistic title="LARAVEL / NEXT.JS" value={`${status.laravel_version} / ${status.next_version || 'N/A'}`} prefix={<ThunderboltOutlined />} /></Card>
    </div>

    {!ready ? <Alert type="error" showIcon message="Máy chủ chưa sẵn sàng cập nhật" description="Kiểm tra PHP Zip và quyền ghi của thư mục backend/Next.js." /> :
      updateAvailable ? <Alert type="info" showIcon message={`Có phiên bản mới ${release?.version}`} description={release?.changelog} /> :
      <Alert type="success" showIcon message={<>Hệ thống của bạn đã được cập nhật phiên bản mới nhất: <b>{status.version}</b></>} />}

    <Card title={<Space><CloudDownloadOutlined className={styles.blue} />Tiến trình cập nhật</Space>} className={styles.progressCard}>
      <Alert type="warning" showIcon message={<><b>Lưu ý:</b> Hệ thống sẽ tự động sao lưu dữ liệu và tạm thời vào chế độ bảo trì trong quá trình nâng cấp.</>} />
      {installing ? <div className={styles.running}><Progress percent={45} status="active" /><Text>Đang tải, sao lưu, cập nhật Laravel, build Next.js và chạy migrations…</Text></div> :
        release ? <Button block size="large" type="primary" icon={<CloudDownloadOutlined />} disabled={!ready} onClick={install}>
          {updateAvailable ? `Tải và cập nhật lên ${release.version}` : 'Tải và cài đặt lại hệ thống'}
        </Button> :
        <Button block size="large" type="primary" icon={<ReloadOutlined />} loading={checking} onClick={() => void check()}>Kiểm tra để tải bản cập nhật</Button>}
      {steps.length > 0 && <div className={styles.inlineLog}>{steps.map((step, index) => <div key={index} className={step.status === 'error' ? styles.error : styles.success}>{step.message}</div>)}</div>}
    </Card>

    <section>
      <div className={styles.historyHeading}><Title level={4}><HistoryOutlined /> Lịch sử cập nhật</Title><Button danger size="small" icon={<DeleteOutlined />} disabled={!history.length} onClick={removeAll}>Xóa tất cả</Button></div>
      <Table<UpdateLog> rowKey="id" dataSource={history} pagination={{ pageSize: 10 }} scroll={{ x: 850 }}
        columns={[
          { title: 'PHIÊN BẢN', render: (_, row) => <Space><Tag color="blue">{row.to_version || 'N/A'}</Tag><Text type="secondary">({row.from_version || 'N/A'})</Text></Space> },
          { title: 'TRẠNG THÁI', dataIndex: 'status', render: value => <span><i className={`${styles.dot} ${value === 'success' ? styles.dotSuccess : styles.dotError}`} />{value === 'success' ? 'Hoàn tất' : value === 'processing' ? 'Đang xử lý' : 'Thất bại'}</span> },
          { title: 'MIGRATIONS', dataIndex: 'migrations', render: value => value ? <Tag color="cyan">{value} Migration(s)</Tag> : '—' },
          { title: 'ADMIN', dataIndex: 'admin_name', render: value => value || '—' },
          { title: 'THỜI GIAN', dataIndex: 'created_at', render: formatDate },
          { title: '', width: 90, render: (_, row) => <Space><Button type="text" icon={<FileTextOutlined />} onClick={() => setViewing(row)} /><Button type="text" danger icon={<DeleteOutlined />} onClick={() => void removeLog(row.id)} /></Space> },
        ]} />
    </section>

    <Modal open={Boolean(viewing)} title="Nhật ký cập nhật" footer={null} width={720} onCancel={() => setViewing(null)}>
      {viewing && <><Descriptions bordered size="small" column={2} items={[
        { key: 'version', label: 'Phiên bản', children: viewing.to_version || 'N/A' },
        { key: 'status', label: 'Trạng thái', children: viewing.status === 'success' ? 'Hoàn tất' : 'Thất bại' },
        { key: 'file', label: 'File', children: viewing.file_name || 'N/A' },
        { key: 'time', label: 'Thời gian', children: formatDate(viewing.created_at) },
      ]} /><pre className={styles.log}>{(viewing.log || []).map(item => `[${formatDate(item.time)}] ${item.message}`).join('\n') || viewing.message}</pre></>}
    </Modal>
  </main>;
}
