'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { ArrowLeftOutlined, ArrowRightOutlined, CalendarOutlined, FileTextOutlined } from '@ant-design/icons';
import { Button, Result, Spin, Typography } from 'antd';import { message } from '@/lib/antd-message';
import { useParams } from 'next/navigation';
import axios from 'axios';
import ClientLayout from '@/components/ClientLayout';
import api from '@/lib/axios';
import styles from '../posts.module.css';

const { Text, Title } = Typography;
type Post = { id: number; title: string; slug: string; content: string; image?: string | null; created_at?: string | null };

const assetUrl = (path?: string | null) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  const base = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '');
  return `${base}${path.startsWith('/') ? '' : '/'}${path}`;
};

export default function PostDetailPage() {
  const { slug } = useParams<{ slug: string }>();
  const [post, setPost] = useState<Post | null>(null);
  const [related, setRelated] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);

  useEffect(() => {
    if (!slug) return;
    const loadPost = async () => {
      try {
        const [postResponse, postsResponse] = await Promise.all([
          api.get(`/client/posts/${encodeURIComponent(slug)}`),
          api.get('/client/posts'),
        ]);
        setPost(postResponse.data.data);
        setRelated((postsResponse.data.data || []).filter((item: Post) => item.slug !== slug).slice(0, 3));
      } catch (error: unknown) {
        if (axios.isAxiosError(error) && error.response?.status === 404) setNotFound(true);
        else message.error('Không thể tải bài viết.');
      } finally {
        setLoading(false);
      }
    };
    void loadPost();
  }, [slug]);

  return (
    <ClientLayout>
      <main className={styles.page}>
        <div className={styles.container}>
          {loading ? <div className={styles.loading}><Spin size="large" /></div> : null}
          {notFound ? <Result status="404" title="Không tìm thấy bài viết" extra={<Link href="/posts"><Button type="primary">Về danh sách bài viết</Button></Link>} /> : null}
          {post ? (
            <div className={styles.detailGrid}>
              <article className={styles.article}>
                {post.image ? <img className={styles.cover} src={assetUrl(post.image)} alt={post.title} /> : null}
                <div className={styles.articleBody}>
                  <Link href="/posts"><Button type="link" icon={<ArrowLeftOutlined />} style={{ paddingLeft: 0 }}>Blog &amp; Kiến thức</Button></Link>
                  <header className={styles.articleHeader}>
                    <Title level={2}>{post.title}</Title>
                    {post.created_at ? <Text type="secondary"><CalendarOutlined /> {new Date(post.created_at).toLocaleDateString('vi-VN')}</Text> : null}
                  </header>
                  <div className={styles.content} dangerouslySetInnerHTML={{ __html: post.content }} />
                </div>
              </article>
              <aside className={styles.relatedCard}>
                <Title level={4}>Có thể bạn quan tâm</Title>
                <div className={styles.relatedList}>
                  {related.map(item => (
                    <Link href={`/posts/${item.slug}`} className={styles.relatedItem} key={item.id}>
                      <span className={styles.relatedImage}>
                        {item.image ? <img src={assetUrl(item.image)} alt="" /> : <FileTextOutlined />}
                      </span>
                      <span className={styles.relatedInfo}>
                        <strong>{item.title}</strong>
                        {item.created_at ? <small><CalendarOutlined /> {new Date(item.created_at).toLocaleDateString('vi-VN')}</small> : null}
                      </span>
                    </Link>
                  ))}
                </div>
                <Link href="/posts" className={styles.allPosts}>Xem tất cả bài viết <ArrowRightOutlined /></Link>
              </aside>
            </div>
          ) : null}
        </div>
      </main>
    </ClientLayout>
  );
}
