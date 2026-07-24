import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Tài liệu API' };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
