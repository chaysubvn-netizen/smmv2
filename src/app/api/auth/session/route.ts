import { NextResponse } from 'next/server';
import { signSessionJwt } from '@/lib/session-jwt';

const SESSION_COOKIE = 'smm_next_session';
const BACKEND_TOKEN_COOKIE = 'smm_backend_token';
const LEGACY_COOKIE = 'smm_frontend_token';
const MAX_TOKEN_LENGTH = 4096;
const SESSION_SECONDS = 30 * 60;

type BackendUser = {
  id?: number | string;
  username?: string;
  role?: string;
  domain?: string | null;
};

function apiBaseUrl(): string {
  let url = (
    process.env.API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    'http://127.0.0.1:8000/api'
  );
  while (url.endsWith('/')) url = url.slice(0, -1);
  return url;
}

function requestHost(request: Request): string {
  const host = (
    request.headers.get('x-forwarded-host') ||
    request.headers.get('host') ||
    new URL(request.url).hostname
  ).split(',')[0].trim().toLowerCase();
  return host.split(':')[0];
}

function normalizedDomain(value: string): string {
  const domain = value.toLowerCase().split(':')[0];
  return domain.startsWith('api.') ? domain.slice(4) : domain;
}

function sameOrigin(request: Request): boolean {
  const origin = request.headers.get('origin');
  if (!origin) return true;
  try {
    return new URL(origin).host === new URL(request.url).host;
  } catch {
    return false;
  }
}

function clearSessionCookies(response: NextResponse): void {
  response.cookies.delete(SESSION_COOKIE);
  response.cookies.delete(BACKEND_TOKEN_COOKIE);
  response.cookies.delete(LEGACY_COOKIE);
}

export async function POST(request: Request) {
  if (!sameOrigin(request)) {
    return NextResponse.json({ status: false, message: 'Origin khÃƒÂ´ng hÃ¡Â»Â£p lÃ¡Â»â€¡.' }, { status: 403 });
  }

  const body = await request.json().catch(() => null) as { token?: unknown } | null;
  const token = typeof body?.token === 'string' ? body.token.trim() : '';
  if (!token || token.length > MAX_TOKEN_LENGTH) {
    return NextResponse.json({ status: false, message: 'Token khÃƒÂ´ng hÃ¡Â»Â£p lÃ¡Â»â€¡.' }, { status: 422 });
  }

  const host = normalizedDomain(requestHost(request));
  const backendResponse = await fetch(apiBaseUrl() + '/auth/api/me', {
    headers: {
      Accept: 'application/json',
      Authorization: 'Bearer ' + token,
      'X-Site-Host': host,
    },
    cache: 'no-store',
  }).catch(() => null);

  if (!backendResponse?.ok) {
    return NextResponse.json({ status: false, message: 'PhiÃƒÂªn Laravel khÃƒÂ´ng hÃ¡Â»Â£p lÃ¡Â»â€¡.' }, { status: 401 });
  }

  const result = await backendResponse.json() as { user?: BackendUser };
  const user = result.user;
  const userDomain = normalizedDomain(String(user?.domain || host));
  if (!user?.id || !user.role || userDomain !== host) {
    return NextResponse.json({ status: false, message: 'TÃƒÂ i khoÃ¡ÂºÂ£n khÃƒÂ´ng thuÃ¡Â»â„¢c website nÃƒÂ y.' }, { status: 403 });
  }

  let session: string;
  try {
    session = await signSessionJwt({
      sub: String(user.id),
      role: user.role,
      domain: userDomain,
      username: user.username,
    }, SESSION_SECONDS);
  } catch {
    return NextResponse.json({ status: false, message: 'MÃƒÂ¡y chÃ¡Â»Â§ Next chÃ†Â°a cÃ¡ÂºÂ¥u hÃƒÂ¬nh NEXT_AUTH_SECRET.' }, { status: 500 });
  }

  const response = NextResponse.json({
    status: true,
    user: { id: user.id, username: user.username, role: user.role, domain: userDomain },
  });
  const cookieOptions = {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax' as const,
    path: '/',
    maxAge: SESSION_SECONDS,
    priority: 'high' as const,
  };
  response.cookies.set(SESSION_COOKIE, session, cookieOptions);
  response.cookies.set(BACKEND_TOKEN_COOKIE, token, cookieOptions);
  response.cookies.delete(LEGACY_COOKIE);
  return response;
}

export async function DELETE(request: Request) {
  if (!sameOrigin(request)) {
    return NextResponse.json({ status: false, message: 'Origin khÃƒÂ´ng hÃ¡Â»Â£p lÃ¡Â»â€¡.' }, { status: 403 });
  }
  const response = NextResponse.json({ status: true });
  clearSessionCookies(response);
  return response;
}
