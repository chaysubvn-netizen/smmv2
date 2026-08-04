import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Subscriptions', robots: { index: true, follow: true } };
export default function Layout({ children }: Readonly<{ children: React.ReactNode }>) { return children; }
