import axios from 'axios';
import { message } from '@/lib/antd-message';

const api = axios.create({
  // Browser requests stay on the Next.js origin. The Route Handler attaches the
  // Laravel access token from an HttpOnly cookie on the server.
  baseURL: '/api/backend',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

api.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    // Remove tokens created by older builds. Never read or attach them in JS.
    localStorage.removeItem('token');
  }
  return config;
}, error => Promise.reject(error));

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
