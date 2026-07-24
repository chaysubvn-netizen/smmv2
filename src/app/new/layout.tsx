import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Đặt đơn hàng', robots: { index: false, follow: false } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
