'use client';

import { App as AntdApp } from 'antd';
import { AntdMessageBridge } from '@/lib/antd-message';

export default function AntdAppProvider({ children }: { children: React.ReactNode }) {
  return <AntdApp><AntdMessageBridge />{children}</AntdApp>;
}
