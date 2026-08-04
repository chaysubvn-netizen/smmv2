import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Lịch sử dòng tiền', description: 'Trang Lịch sử dòng tiền - Hệ thống quản lý dịch vụ chuyên nghiệp',
  robots: { index: true, follow: true },
};

export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) {
  return children;
}
