import axios from 'axios';
import { message } from '@/lib/antd-message';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Thêm interceptor để luôn đính kèm token vào request
api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    config.headers['X-Site-Host'] = window.location.hostname;
    const token = localStorage.getItem('token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

let lastDemoNotice = 0;
let lastMaintenanceNotice = 0;
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 423 && error.response?.data?.code === 'DEMO_MODE' && typeof window !== 'undefined') {
      const now = Date.now();
      if (now - lastDemoNotice > 800) {
        lastDemoNotice = now;
        window.setTimeout(() => {
          message.destroy();
          message.warning({ content: error.response.data.message, duration: 4, key: 'demo-mode' });
        }, 0);
      }
    }
    if (error.response?.status === 503 && error.response?.data?.code === 'MAINTENANCE_MODE' && typeof window !== 'undefined') {
      const now = Date.now();
      if (now - lastMaintenanceNotice > 800) {
        lastMaintenanceNotice = now;
        window.setTimeout(() => {
          message.destroy();
          message.warning({ content: error.response.data.message, duration: 4, key: 'maintenance-mode' });
          window.dispatchEvent(new CustomEvent('maintenance-mode-enabled'));
        }, 0);
      }
    }
    return Promise.reject(error);
  },
);

export default api;
