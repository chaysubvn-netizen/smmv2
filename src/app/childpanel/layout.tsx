import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Child Panel', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
