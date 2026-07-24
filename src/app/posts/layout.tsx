import type { Metadata } from 'next';

export const metadata: Metadata = { title: 'Bài viết' };

export default function PostsLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return children;
}
