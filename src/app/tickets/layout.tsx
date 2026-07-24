import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Yêu cầu hỗ trợ', robots: { index: false, follow: false } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
