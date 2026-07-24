'use client';

import { ClockCircleOutlined, LogoutOutlined, ToolOutlined } from '@ant-design/icons';
import { Button, Image } from 'antd';
import styles from './MaintenanceScreen.module.css';

type Props = {
  title?: string;
  logo?: string;
  onLogout: () => void;
};

export default function MaintenanceScreen({ title, logo, onLogout }: Props) {
  return <main className={styles.page}>
    <div className={styles.glow} />
    <section className={styles.card}>
      {logo ? <Image preview={false} className={styles.logo} src={logo} alt={title || 'Website'} /> : null}
      <div className={styles.icon}><ToolOutlined /></div>
      <span className={styles.badge}><ClockCircleOutlined /> Đang bảo trì hệ thống</span>
      <h1>Chúng tôi sẽ sớm quay lại</h1>
      <p>{title || 'Hệ thống'} đang được nâng cấp để mang đến trải nghiệm tốt hơn. Trong thời gian này, bạn chưa thể đặt đơn, mua sản phẩm hoặc thực hiện giao dịch.</p>
      <div className={styles.progress}><span /></div>
      <small>Vui lòng quay lại sau ít phút. Cảm ơn bạn đã kiên nhẫn.</small>
      <Button icon={<LogoutOutlined />} onClick={onLogout}>Đăng xuất</Button>
    </section>
  </main>;
}
