import { NextResponse } from 'next/server';

const SESSION_COOKIE = 'smm_next_session';
const BACKEND_TOKEN_COOKIE = 'smm_backend_token';
const LEGACY_COOKIE = 'smm_frontend_token';

function sameOrigin(request: Request): boolean {
  const origin = request.headers.get('origin');
  if (!origin) return request.headers.get('sec-fetch-site') !== 'cross-site';
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

// Tokens are never accepted from browser JavaScript. Authentication is handled
// by /api/backend/auth/* and stored directly in HttpOnly cookies there.
export async function POST() {
  return NextResponse.json(
    { status: false, message: 'Client-side token sessions are disabled.' },
    { status: 410, headers: { 'Cache-Control': 'no-store' } },
  );
}

export async function DELETE(request: Request) {
  if (!sameOrigin(request)) {
    return NextResponse.json({ status: false, message: 'Invalid request origin.' }, { status: 403 });
  }
  const response = NextResponse.json({ status: true }, { headers: { 'Cache-Control': 'no-store' } });
  clearSessionCookies(response);
  return response;
}
