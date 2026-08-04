import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Bảo hành đơn hàng', description: 'Trang Bảo hành đơn hàng - Hệ thống quản lý dịch vụ chuyên nghiệp', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
