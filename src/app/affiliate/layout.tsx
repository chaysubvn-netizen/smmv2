import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Tiếp thị liên kết', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
