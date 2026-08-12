import type { NextRequest } from 'next/server';
import { NextResponse } from 'next/server';
import { verifySessionJwt } from '@/lib/session-jwt';

type InstallStatusResponse = {
  status?: boolean;
  data?: { installed?: boolean };
};

type CurrentUserResponse = {
  user?: {
    id?: number | string;
    role?: string;
    domain?: string | null;
  };
};

const SESSION_COOKIE = 'smm_next_session';
const BACKEND_TOKEN_COOKIE = 'smm_backend_token';
const LEGACY_COOKIE = 'smm_frontend_token';

function normalizedDomain(value: string): string {
  const domain = value.toLowerCase().split(':')[0];
  return domain.startsWith('api.') ? domain.slice(4) : domain;
}

function clearAuthCookies(response: NextResponse): void {
  response.cookies.delete(SESSION_COOKIE);
  response.cookies.delete(BACKEND_TOKEN_COOKIE);
  response.cookies.delete(LEGACY_COOKIE);
}

function loginRedirect(request: NextRequest): NextResponse {
  const response = NextResponse.redirect(new URL('/login', request.url));
  clearAuthCookies(response);
  return response;
}

export async function proxy(request: NextRequest) {
  let apiBaseUrl = (
    process.env.API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    'http://127.0.0.1:8000/api'
  );
  while (apiBaseUrl.endsWith('/')) apiBaseUrl = apiBaseUrl.slice(0, -1);
  const siteHost = normalizedDomain((
    request.headers.get('x-forwarded-host') ||
    request.headers.get('host') ||
    request.nextUrl.hostname
  ).split(',')[0].trim());
  const pathname = request.nextUrl.pathname;
  const isAdminRoute = pathname === '/admin' || pathname.startsWith('/admin/');

  try {
    const installResponse = await fetch(apiBaseUrl + '/install/status', {
      headers: { Accept: 'application/json', 'X-Site-Host': siteHost },
      cache: 'no-store',
    });

    if (!installResponse.ok) {
      return isAdminRoute ? loginRedirect(request) : NextResponse.next();
    }

    const install = await installResponse.json() as InstallStatusResponse;
    if (install.status && install.data?.installed === false) {
      return NextResponse.redirect(new URL('/install', request.url));
    }

    if (isAdminRoute) {
      const sessionToken = request.cookies.get(SESSION_COOKIE)?.value;
      const backendToken = request.cookies.get(BACKEND_TOKEN_COOKIE)?.value;
      if (!sessionToken || !backendToken) return loginRedirect(request);

      const claims = await verifySessionJwt(sessionToken);
      if (
        !claims ||
        claims.role !== 'admin' ||
        claims.domain !== siteHost
      ) return loginRedirect(request);

      // Revalidate against Laravel on every admin navigation so token revocation,
      // account locking and role changes take effect immediately.
      const authResponse = await fetch(apiBaseUrl + '/auth/api/me', {
        headers: {
          Accept: 'application/json',
          Authorization: 'Bearer ' + backendToken,
          'X-Site-Host': siteHost,
        },
        cache: 'no-store',
      });
      const authResult = authResponse.ok
        ? await authResponse.json() as CurrentUserResponse
        : null;
      const backendUser = authResult?.user;
      const backendDomain = normalizedDomain(String(backendUser?.domain || siteHost));

      if (
        String(backendUser?.id || '') !== claims.sub ||
        backendUser?.role !== 'admin' ||
        backendDomain !== siteHost
      ) return loginRedirect(request);
    }
  } catch {
    if (isAdminRoute) return loginRedirect(request);
    // Public pages stay reachable when the backend is temporarily unavailable.
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
    '/((?!install|api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt|.*\\..*).*)',
  ],
};
