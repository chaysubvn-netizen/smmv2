'use client';

import { App as AntdApp } from 'antd';
import type { MessageInstance } from 'antd/es/message/interface';
import { useEffect } from 'react';

let currentMessage: MessageInstance | null = null;

export const message = new Proxy({} as MessageInstance, {
  get(_target, property: keyof MessageInstance) {
    const value = currentMessage?.[property];
    return typeof value === 'function' ? value.bind(currentMessage) : value;
  },
});

export function AntdMessageBridge() {
  const { message: contextMessage } = AntdApp.useApp();
  useEffect(() => {
    currentMessage = contextMessage;
    return () => {
      if (currentMessage === contextMessage) currentMessage = null;
    };
  }, [contextMessage]);
  return null;
}
