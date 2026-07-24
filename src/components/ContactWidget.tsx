'use client';

import { useEffect, useRef, useState } from 'react';
import { CloseOutlined, MessageOutlined, RightOutlined } from '@ant-design/icons';
import styles from './ContactWidget.module.css';

export type ContactWidgetItem = { id: number; name: string; url: string; image?: string | null };
const asset = (path?: string | null) => path ? (/^https?:/.test(path) ? path : `${(process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/api\/?$/, '')}${path.startsWith('/') ? '' : '/'}${path}`) : '';
const href = (url: string) => /^(https?:\/\/|mailto:|tel:)/i.test(url) ? url : `https://${url}`;

export default function ContactWidget({ items }: { items?: ContactWidgetItem[] }) {
  const [open, setOpen] = useState(false);
  const root = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const close = (event: MouseEvent) => { if (root.current && !root.current.contains(event.target as Node)) setOpen(false); };
    document.addEventListener('mousedown', close);
    return () => document.removeEventListener('mousedown', close);
  }, []);
  if (!items?.length) return null;
  return <div className={styles.root} ref={root}>
    {open ? <div className={styles.panel}>
      <div className={styles.title}>Liên hệ với chúng tôi</div>
      <div className={styles.list}>{items.map(item => <a key={item.id} className={styles.item} href={href(item.url)} target="_blank" rel="noreferrer">
        <span className={styles.image}>{item.image ? <img src={asset(item.image)} alt="" /> : <MessageOutlined />}</span>
        <strong>{item.name}</strong><RightOutlined className={styles.arrow} />
      </a>)}</div>
    </div> : null}
    <button className={styles.trigger} type="button" onClick={() => setOpen(value => !value)} aria-label={open ? 'Đóng liên hệ' : 'Mở liên hệ'} aria-expanded={open}>{open ? <CloseOutlined /> : <MessageOutlined />}</button>
  </div>;
}
