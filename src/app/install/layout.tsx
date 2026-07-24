import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Cài đặt Website',
  robots: { index: false, follow: false },
};

export default function InstallLayout({ children }: { children: React.ReactNode }) {
  return children;
}
