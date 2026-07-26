import type { NextRequest } from 'next/server';
import { NextResponse } from 'next/server';

type InstallStatusResponse = {
  status?: boolean;
  data?: {
    installed?: boolean;
  };
};

export async function proxy(request: NextRequest) {
  const apiBaseUrl = (
    process.env.API_URL ||
    process.env.NEXT_PUBLIC_API_URL ||
    'http://127.0.0.1:8000/api'
  ).replace(/\/+$/, '');
  const siteHost = (
    request.headers.get('x-forwarded-host') ||
    request.headers.get('host') ||
    request.nextUrl.hostname
  ).split(',')[0].trim().replace(/:\d+$/, '');

  try {
    const response = await fetch(`${apiBaseUrl}/install/status`, {
      headers: {
        Accept: 'application/json',
        'X-Site-Host': siteHost,
      },
      cache: 'no-store',
    });

    if (!response.ok) {
      return NextResponse.next();
    }

    const result = await response.json() as InstallStatusResponse;
    if (result.status && result.data?.installed === false) {
      return NextResponse.redirect(new URL('/install', request.url));
    }
  } catch {
    // Keep the website reachable when the installation status API is unavailable.
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
    '/((?!install|api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt|.*\\..*).*)',
  ],
};
