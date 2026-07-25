'use client';

import { App as AntdApp, ConfigProvider } from 'antd';
import { AntdMessageBridge } from '@/lib/antd-message';

export default function AntdAppProvider({ children }: { children: React.ReactNode }) {
  return (
    <ConfigProvider virtual={false}>
      <AntdApp><AntdMessageBridge />{children}</AntdApp>
    </ConfigProvider>
  );
}
