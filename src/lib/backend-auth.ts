import { NextRequest } from 'next/server';
import type { RowDataPacket } from 'mysql2';
import { one } from '@/lib/db';
import { verifySessionJwt } from '@/lib/session-jwt';

export const SESSION_COOKIE = 'smm_next_session';

export type AuthUser = RowDataPacket & {
  id: number; username: string; email: string; role: 'admin' | 'user'; domain: string;
  balance: number; total_deposit: number; level: string; status: string; currency: string;
};

export function siteHost(request: NextRequest): string {
  const raw = request.headers.get('x-forwarded-host') || request.headers.get('host') || request.nextUrl.hostname;
  const host = raw.split(',')[0].trim().toLowerCase().split(':')[0];
  return host.startsWith('api.') ? host.slice(4) : host;
}

export async function currentUser(request: NextRequest): Promise<AuthUser | null> {
  const token = request.cookies.get(SESSION_COOKIE)?.value;
  if (!token) return null;
  const claims = await verifySessionJwt(token);
  if (!claims || claims.domain !== siteHost(request)) return null;
  return one<AuthUser>('SELECT id,username,email,role,domain,balance,total_deposit,level,status,currency,phone,avatar,api_key,last_login,created_at FROM users WHERE id=? AND status=? LIMIT 1', [Number(claims.sub), 'active']);
}
