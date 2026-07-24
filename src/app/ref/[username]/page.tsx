'use client';

import { useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { Spin } from 'antd';

export default function ReferralPage() {
  const params = useParams<{ username: string }>();
  const router = useRouter();

  useEffect(() => {
    const username = decodeURIComponent(params.username || '').trim();
    if (username) localStorage.setItem('ref_username', username);
    router.replace('/?register=1');
  }, [params.username, router]);

  return <div style={{ minHeight: '100vh', display: 'grid', placeItems: 'center' }}><Spin size="large" description="Đang chuyển đến trang đăng ký..." /></div>;
}
