import { NextRequest, NextResponse } from 'next/server';
import { signSessionJwt } from '@/lib/session-jwt';

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

const SESSION_COOKIE = 'smm_next_session';
const BACKEND_TOKEN_COOKIE = 'smm_backend_token';
const LEGACY_COOKIE = 'smm_frontend_token';
const SESSION_SECONDS = 30 * 60;
const LOGIN_WINDOW_MS = 15 * 60 * 1000;
const LOGIN_MAX_FAILURES = 5;

type BackendUser = {
  id?: number | string;
  username?: string;
  role?: string;
  domain?: string | null;
};

type LoginBucket = { failures: number; resetAt: number };

const globalRateLimit = globalThis as typeof globalThis & {
  smmLoginBuckets?: Map<string, LoginBucket>;
};
const loginBuckets = globalRateLimit.smmLoginBuckets ??= new Map<string, LoginBucket>();

function apiBaseUrl(): string {
  return (process.env.API_URL || process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api').replace(/\/+$/, '');
}

function normalizedDomain(value: string): string {
  const domain = value.toLowerCase().split(':')[0];
  return domain.startsWith('api.') ? domain.slice(4) : domain;
}

function requestHost(request: NextRequest): string {
  const raw = request.headers.get('x-forwarded-host') || request.headers.get('host') || request.nextUrl.hostname;
  return normalizedDomain(raw.split(',')[0].trim());
}

function sameOrigin(request: NextRequest): boolean {
  const origin = request.headers.get('origin');
  if (!origin) return request.headers.get('sec-fetch-site') !== 'cross-site';
  try {
    return new URL(origin).host === request.nextUrl.host;
  } catch {
    return false;
  }
}

function clientAddress(request: NextRequest): string {
  return (request.headers.get('x-real-ip') || request.headers.get('x-forwarded-for')?.split(',')[0] || 'unknown').trim();
}

function loginKey(request: NextRequest, body: ArrayBuffer | undefined): string {
  let username = '';
  try {
    if (body) username = String(JSON.parse(new TextDecoder().decode(body))?.username || '').toLowerCase().trim();
  } catch {}
  return `${requestHost(request)}:${clientAddress(request)}:${username}`;
}

function rateLimitResponse(retryAfter: number): NextResponse {
  return NextResponse.json(
    { status: false, message: 'Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau.' },
    { status: 429, headers: { 'Retry-After': String(retryAfter), 'Cache-Control': 'no-store' } },
  );
}

function checkLoginLimit(key: string): NextResponse | null {
  const now = Date.now();
  const bucket = loginBuckets.get(key);
  if (!bucket || bucket.resetAt <= now) {
    loginBuckets.delete(key);
    return null;
  }
  if (bucket.failures < LOGIN_MAX_FAILURES) return null;
  return rateLimitResponse(Math.max(1, Math.ceil((bucket.resetAt - now) / 1000)));
}

function recordLoginFailure(key: string): void {
  const now = Date.now();
  const current = loginBuckets.get(key);
  if (!current || current.resetAt <= now) {
    loginBuckets.set(key, { failures: 1, resetAt: now + LOGIN_WINDOW_MS });
    return;
  }
  current.failures += 1;
}

function backendAuthToken(response: Response, data: Record<string, unknown>): string {
  const jsonToken = typeof data.token === 'string'
    ? data.token
    : typeof data.access_token === 'string'
      ? data.access_token
      : '';
  if (jsonToken) return jsonToken.trim();

  const setCookie = response.headers.get('set-cookie') || '';
  const match = setCookie.match(/(?:^|,\s*)smm_auth_token=([^;,\s]+)/i);
  if (!match?.[1]) return '';
  try {
    return decodeURIComponent(match[1]);
  } catch {
    return match[1];
  }
}

function clearAuthCookies(response: NextResponse): void {
  response.cookies.delete(SESSION_COOKIE);
  response.cookies.delete(BACKEND_TOKEN_COOKIE);
  response.cookies.delete(LEGACY_COOKIE);
}

async function setAuthCookies(
  response: NextResponse,
  request: NextRequest,
  token: string,
  user: BackendUser,
): Promise<boolean> {
  const host = requestHost(request);
  const userDomain = normalizedDomain(String(user.domain || host));
  if (!user.id || !user.role || userDomain !== host || token.length > 4096) return false;

  const session = await signSessionJwt({
    sub: String(user.id),
    role: user.role,
    domain: userDomain,
    username: user.username,
  }, SESSION_SECONDS);
  const options = {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax' as const,
    path: '/',
    maxAge: SESSION_SECONDS,
    priority: 'high' as const,
  };
  response.cookies.set(SESSION_COOKIE, session, options);
  response.cookies.set(BACKEND_TOKEN_COOKIE, token, options);
  response.cookies.delete(LEGACY_COOKIE);
  return true;
}

async function handle(request: NextRequest, context: { params: Promise<{ path: string[] }> }) {
  const method = request.method.toUpperCase();
  if (!['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'].includes(method)) {
    return NextResponse.json({ status: false, message: 'Method not allowed.' }, { status: 405 });
  }
  if (!['GET', 'HEAD', 'OPTIONS'].includes(method) && !sameOrigin(request)) {
    return NextResponse.json({ status: false, message: 'Invalid request origin.' }, { status: 403 });
  }

  const { path } = await context.params;
  if (!path?.length || path.some(segment => !segment || segment === '.' || segment === '..')) {
    return NextResponse.json({ status: false, message: 'Invalid API path.' }, { status: 400 });
  }

  const backendPath = '/' + path.map(encodeURIComponent).join('/');
  const target = apiBaseUrl() + backendPath + request.nextUrl.search;
  const hasBody = !['GET', 'HEAD'].includes(method);
  const body = hasBody ? await request.arrayBuffer() : undefined;
  const isLogin = backendPath === '/auth/api/login' && method === 'POST';
  const key = isLogin ? loginKey(request, body) : '';
  if (isLogin) {
    const limited = checkLoginLimit(key);
    if (limited) return limited;
  }

  const headers = new Headers({
    Accept: request.headers.get('accept') || 'application/json',
    'X-Site-Host': requestHost(request),
  });
  const contentType = request.headers.get('content-type');
  if (contentType) headers.set('Content-Type', contentType);
  const backendToken = request.cookies.get(BACKEND_TOKEN_COOKIE)?.value;
  if (backendToken) headers.set('Authorization', 'Bearer ' + backendToken);

  let backendResponse: Response;
  try {
    backendResponse = await fetch(target, {
      method,
      headers,
      body: body && body.byteLength ? body : undefined,
      cache: 'no-store',
      redirect: 'manual',
    });
  } catch {
    return NextResponse.json({ status: false, message: 'Backend service is unavailable.' }, { status: 502 });
  }

  if (isLogin) {
    if (backendResponse.status === 401) recordLoginFailure(key);
    else if (backendResponse.ok) loginBuckets.delete(key);
  }

  const isAuthResponse = ['/auth/api/login', '/auth/api/register', '/auth/google/exchange'].includes(backendPath);
  if (isAuthResponse) {
    const data = await backendResponse.json().catch(() => ({ status: false, message: 'Invalid backend response.' })) as Record<string, unknown>;
    const token = backendAuthToken(backendResponse, data);
    delete data.token;
    delete data.access_token;

    if (backendResponse.ok) {
      if (!token || !data.user || typeof data.user !== 'object') {
        return NextResponse.json(
          { status: false, message: 'Backend login succeeded without a usable session token.' },
          { status: 502, headers: { 'Cache-Control': 'no-store' } },
        );
      }

      const response = NextResponse.json(data, {
        status: backendResponse.status,
        headers: { 'Cache-Control': 'no-store', 'X-SMM-Session': 'established' },
      });
      try {
        const valid = await setAuthCookies(response, request, token, data.user as BackendUser);
        if (!valid) return NextResponse.json({ status: false, message: 'Invalid account domain.' }, { status: 403 });
      } catch {
        return NextResponse.json({ status: false, message: 'Server session is not configured.' }, { status: 500 });
      }
      return response;
    }

    return NextResponse.json(data, { status: backendResponse.status, headers: { 'Cache-Control': 'no-store' } });
  }

  const responseHeaders = new Headers({ 'Cache-Control': 'no-store' });
  for (const name of ['content-type', 'content-disposition', 'content-length']) {
    const value = backendResponse.headers.get(name);
    if (value) responseHeaders.set(name, value);
  }
  const response = new NextResponse(method === 'HEAD' ? null : backendResponse.body, {
    status: backendResponse.status,
    headers: responseHeaders,
  });
  if (backendPath === '/auth/api/logout') clearAuthCookies(response);
  return response;
}

export const GET = handle;
export const HEAD = handle;
export const POST = handle;
export const PUT = handle;
export const PATCH = handle;
export const DELETE = handle;
export const OPTIONS = handle;
