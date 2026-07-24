'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { ArrowRightOutlined, CalendarOutlined, FileTextOutlined, ReadOutlined } from '@ant-design/icons';
import { Empty, Spin, Typography } from 'antd';import { message } from '@/lib/antd-message';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from './posts.module.css';

const { Text, Title } = Typography;

type Post = { id: number; title: string; slug: string; content: string; image?: string | null; created_at?: string | null };

const assetUrl = (path?: string | null) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  const base = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
  return `${base}${path.startsWith('/') ? '' : '/'}${path}`;
};

const excerpt = (html: string) => {
  const text = html.replace(/<[^>]*>/g, ' ').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
  return text.length > 145 ? `${text.slice(0, 145).trim()}...` : text;
};

export default function PostsPage() {
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadPosts = async () => {
      try {
        const response = await api.get('/client/posts');
        setPosts(response.data.data || []);
      } catch {
        message.error('Không thể tải danh sách bài viết.');
      } finally {
        setLoading(false);
      }
    };
    void loadPosts();
  }, []);

  return (
    <ClientLayout>
      <main className={styles.page}>
        <div className={styles.container}>
          <header className={styles.hero}>
            <Title level={2}><ReadOutlined /> Blog &amp; Kiến thức</Title>
            <Text>Cập nhật những kiến thức mới nhất về Social Media Marketing, tips &amp;<br className={styles.desktopBreak} /> tricks tăng tương tác và xu hướng mạng xã hội</Text>
          </header>
          {loading ? <div className={styles.loading}><Spin size="large" /></div> : null}
          {!loading && posts.length === 0 ? <div className={styles.empty}><Empty description="Chưa có bài viết nào" /></div> : null}
          <section className={styles.grid}>
            {posts.map(post => (
              <article className={styles.card} key={post.id}>
                <Link className={styles.imageLink} href={`/posts/${post.slug}`}>
                  {post.image ? <img src={assetUrl(post.image)} alt={post.title} /> : <span className={styles.placeholder}><FileTextOutlined /></span>}
                </Link>
                <div className={styles.body}>
                  <Link href={`/posts/${post.slug}`}><h2 className={styles.title}>{post.title}</h2></Link>
                  <p className={styles.excerpt}>{excerpt(post.content)}</p>
                  <div className={styles.cardFooter}>
                    {post.created_at ? <time><CalendarOutlined /> {new Date(post.created_at).toLocaleDateString('vi-VN')}</time> : <span />}
                    <Link className={styles.more} href={`/posts/${post.slug}`}>Đọc thêm <ArrowRightOutlined /></Link>
                  </div>
                </div>
              </article>
            ))}
          </section>
        </div>
      </main>
    </ClientLayout>
  );
}
