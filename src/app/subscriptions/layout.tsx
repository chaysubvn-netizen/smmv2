import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Subscriptions', description: 'Trang Subscriptions - Hệ thống quản lý dịch vụ chuyên nghiệp', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
