import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Cài đặt Website', description: 'Trang Cài đặt Website - Hệ thống quản lý dịch vụ chuyên nghiệp',
  robots: { index: true, follow: true },
};

export default function InstallLayout({ children }: { children: React.ReactNode }) {
  return children;
}
