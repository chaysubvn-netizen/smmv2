import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Nạp tiền', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
