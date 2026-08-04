import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Cài đặt Website',
  robots: { index: true, follow: true },
};

export default function InstallLayout({ children }: { children: React.ReactNode }) {
  return children;
}
