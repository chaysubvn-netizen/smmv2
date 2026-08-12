export async function saveFrontendSession(token: string): Promise<void> {
  const response = await fetch('/api/auth/session', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ token }),
  });

  if (!response.ok) throw new Error('Không thể tạo phiên đăng nhập.');
}

export async function clearFrontendSession(): Promise<void> {
  localStorage.removeItem('token');
  await fetch('/api/auth/session', { method: 'DELETE' }).catch(() => undefined);
}
