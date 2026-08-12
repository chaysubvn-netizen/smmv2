export async function clearFrontendSession(): Promise<void> {
  // Delete any credential left by older builds, then clear HttpOnly cookies
  // through a same-origin Route Handler.
  localStorage.removeItem('token');
  await fetch('/api/auth/session', { method: 'DELETE' }).catch(() => undefined);
}
