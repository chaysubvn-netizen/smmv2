import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Lịch sử dòng tiền',
  robots: { index: false, follow: false },
};

export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) {
  return children;
}
