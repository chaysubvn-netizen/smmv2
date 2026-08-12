const encoder = new TextEncoder();

export type SessionClaims = {
  sub: string;
  role: string;
  domain: string;
  username?: string;
  iat: number;
  exp: number;
  iss: 'smmv2-next';
  aud: 'smmv2-web';
};

function base64UrlEncode(input: Uint8Array | string): string {
  const bytes = typeof input === 'string' ? encoder.encode(input) : input;
  let binary = '';
  for (const byte of bytes) binary += String.fromCharCode(byte);
  let encoded = btoa(binary).split('+').join('-').split('/').join('_');
  while (encoded.endsWith('=')) encoded = encoded.slice(0, -1);
  return encoded;
}

function base64UrlDecode(input: string): ArrayBuffer {
  const base64 = input.replace(/-/g, '+').replace(/_/g, '/')
    .padEnd(Math.ceil(input.length / 4) * 4, '=');
  const binary = atob(base64);
  return Uint8Array.from(binary, character => character.charCodeAt(0)).buffer as ArrayBuffer;
}

async function signingKey(secret: string): Promise<CryptoKey> {
  return crypto.subtle.importKey(
    'raw',
    encoder.encode(secret),
    { name: 'HMAC', hash: 'SHA-256' },
    false,
    ['sign', 'verify'],
  );
}

function sessionSecret(): string {
  const secret = process.env.NEXT_AUTH_SECRET;
  if (!secret || secret.length < 32) throw new Error('NEXT_AUTH_SECRET must contain at least 32 characters.');
  return secret;
}

export async function signSessionJwt(
  claims: Omit<SessionClaims, 'iat' | 'exp' | 'iss' | 'aud'>,
  lifetimeSeconds = 30 * 60,
): Promise<string> {
  const now = Math.floor(Date.now() / 1000);
  const header = base64UrlEncode(JSON.stringify({ alg: 'HS256', typ: 'JWT' }));
  const payload = base64UrlEncode(JSON.stringify({
    ...claims,
    iat: now,
    exp: now + lifetimeSeconds,
    iss: 'smmv2-next',
    aud: 'smmv2-web',
  } satisfies SessionClaims));
  const unsigned = header + '.' + payload;
  const signature = await crypto.subtle.sign('HMAC', await signingKey(sessionSecret()), encoder.encode(unsigned));
  return unsigned + '.' + base64UrlEncode(new Uint8Array(signature));
}

export async function verifySessionJwt(token: string): Promise<SessionClaims | null> {
  try {
    const parts = token.split('.');
    if (parts.length !== 3) return null;
    const [header, payload, signature] = parts;
    const parsedHeader = JSON.parse(new TextDecoder().decode(base64UrlDecode(header))) as { alg?: string; typ?: string };
    if (parsedHeader.alg !== 'HS256' || parsedHeader.typ !== 'JWT') return null;

    const valid = await crypto.subtle.verify(
      'HMAC',
      await signingKey(sessionSecret()),
      base64UrlDecode(signature),
      encoder.encode(header + '.' + payload),
    );
    if (!valid) return null;

    const claims = JSON.parse(new TextDecoder().decode(base64UrlDecode(payload))) as Partial<SessionClaims>;
    const now = Math.floor(Date.now() / 1000);
    if (
      typeof claims.sub !== 'string' ||
      typeof claims.role !== 'string' ||
      typeof claims.domain !== 'string' ||
      typeof claims.iat !== 'number' ||
      typeof claims.exp !== 'number' ||
      claims.exp <= now ||
      claims.iat > now + 60 ||
      claims.iss !== 'smmv2-next' ||
      claims.aud !== 'smmv2-web'
    ) return null;

    return claims as SessionClaims;
  } catch {
    return null;
  }
}