import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Lịch sử đơn hàng', description: 'Trang Lịch sử đơn hàng - Hệ thống quản lý dịch vụ chuyên nghiệp', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
