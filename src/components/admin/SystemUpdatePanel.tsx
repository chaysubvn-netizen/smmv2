'use client';

import { useEffect, useState } from 'react';
import { CheckCircleOutlined, CloudDownloadOutlined, CodeOutlined, ReloadOutlined, ThunderboltOutlined } from '@ant-design/icons';
import { Alert, Button, Card, Modal, Progress, Space, Spin, Statistic, Steps, Typography } from 'antd';
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
type Release = {
  version: string;
  download_url: string;
  sha256: string;
  changelog?: string;
};
type UpdateStep = { message: string; status: 'success' | 'error'; time: string };

export default function SystemUpdatePanel() {
  const [status, setStatus] = useState<Status | null>(null);
  const [release, setRelease] = useState<Release | null>(null);
  const [checking, setChecking] = useState(false);
  const [installing, setInstalling] = useState(false);
  const [steps, setSteps] = useState<UpdateStep[]>([]);

  const loadStatus = async () => {
    const response = await api.get('/admin/system-update');
    setStatus(response.data.data);
  };

  useEffect(() => {
    void Promise.resolve().then(loadStatus).catch(() => message.error('Không thể tải thông tin hệ thống.'));
  }, []);

  const check = async () => {
    setChecking(true);
    try {
      const response = await api.post('/admin/system-update/check');
      setRelease(response.data.update_available ? response.data.data : null);
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
      title: `Cập nhật toàn hệ thống lên ${release.version}?`,
      content: 'Laravel và Next.js sẽ được cập nhật trong cùng một phiên. Website có thể tạm ngưng vài phút.',
      okText: 'Cập nhật ngay',
      cancelText: 'Hủy',
      onOk: async () => {
        setInstalling(true); setSteps([]);
        try {
          const response = await api.post('/admin/system-update/install', release, { timeout: 30 * 60 * 1000 });
          setSteps(response.data.steps || []);
          setRelease(null);
          message.success(response.data.message);
          await loadStatus();
        } catch (error: unknown) {
          const detail = error as { response?: { data?: { message?: string; steps?: UpdateStep[] } } };
          setSteps(detail.response?.data?.steps || []);
          message.error(detail.response?.data?.message || 'Cập nhật thất bại; hệ thống đã thử khôi phục bản cũ.');
        } finally {
          setInstalling(false);
        }
      },
    });
  };

  if (!status) return <div className={styles.loading}><Spin size="large" /></div>;
  const ready = Object.values(status.requirements).every(Boolean);

  return <main className={styles.page}>
    <header className={styles.heading}>
      <div><Title level={2}>Cập nhật hệ thống</Title><Text type="secondary">Cập nhật đồng bộ Laravel và Next.js trong một lần</Text></div>
      <Button icon={<ReloadOutlined />} loading={checking} onClick={() => void check()}>Kiểm tra cập nhật</Button>
    </header>

    <div className={styles.stats}>
      <Card><Statistic title="Phiên bản" value={status.version} prefix={<CloudDownloadOutlined />} /></Card>
      <Card><Statistic title="PHP" value={status.php_version} prefix={<CodeOutlined />} /></Card>
      <Card><Statistic title="Laravel" value={status.laravel_version} prefix={<ThunderboltOutlined />} /></Card>
      <Card><Statistic title="Next.js" value={status.next_version || 'Không rõ'} prefix={<CheckCircleOutlined />} /></Card>
    </div>

    {!ready && <Alert type="error" showIcon message="Máy chủ chưa sẵn sàng cập nhật" description="Hãy kiểm tra PHP Zip và quyền ghi của thư mục backend/Next.js." />}
    {release ? <Alert type="info" showIcon
      message={`Có phiên bản mới ${release.version}`}
      description={<Space direction="vertical"><span>{release.changelog || 'Gói này cập nhật đồng thời PHP và Next.js.'}</span><Button type="primary" icon={<CloudDownloadOutlined />} disabled={!ready} loading={installing} onClick={install}>Cập nhật cả hai</Button></Space>}
    /> : <Alert type="success" showIcon message={`Hệ thống đang dùng phiên bản ${status.version}`} description="Bấm “Kiểm tra cập nhật” để tìm bản phát hành mới." />}

    <Card title="Tiến trình cập nhật" className={styles.progressCard}>
      {installing ? <><Progress percent={45} status="active" /><Text>Đang tải, sao lưu, cài Laravel, build Next.js và chạy migration…</Text></> :
        steps.length ? <Steps direction="vertical" items={steps.map(step => ({ title: step.message, status: step.status === 'error' ? 'error' : 'finish' }))} /> :
        <div className={styles.empty}><CloudDownloadOutlined /><Text type="secondary">Chưa có tác vụ cập nhật</Text></div>}
    </Card>
  </main>;
}
