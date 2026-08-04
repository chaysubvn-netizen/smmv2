import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Cài đặt tài khoản', description: 'Trang Cài đặt tài khoản - Hệ thống quản lý dịch vụ chuyên nghiệp', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
