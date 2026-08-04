import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Yêu cầu hỗ trợ', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
