'use client';

import { useEffect, useRef } from 'react';
import { Spin } from 'antd';
import { useRouter } from 'next/navigation';
import api from '@/lib/axios';
import { message } from '@/lib/antd-message';
import styles from '../../../auth.module.css';

export default function GoogleCallbackPage() {
  const router = useRouter();
  const exchanging = useRef(false);

  useEffect(() => {
    if (exchanging.current) return;
    exchanging.current = true;
    const code = new URLSearchParams(window.location.search).get('code');
    if (!code) {
      message.error('Thiếu mã xác thực Google.');
      router.replace('/login');
      return;
    }

    api.post('/auth/google/exchange', { code })
      .then(response => {
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        message.success(response.data.message || 'Đăng nhập Google thành công');
        router.replace('/new');
      })
      .catch(error => {
        message.error(error.response?.data?.message || 'Không thể hoàn tất đăng nhập Google.');
        router.replace('/login');
      });
  }, [router]);

  return <main className={styles.callbackPage}><Spin size="large" tip="Đang hoàn tất đăng nhập Google..." /></main>;
}
