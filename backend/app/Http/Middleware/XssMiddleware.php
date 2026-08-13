<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssMiddleware
{
    /** Rich-text fields retain a small formatting allowlist. */
    private const RICH_TEXT_FIELDS = [
        'content', 'message', 'description', 'short_description',
        'notice_modal', 'notice_service', 'notice_recharge',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key): void {
            if (! is_string($value)) return;
            $value = in_array((string) $key, self::RICH_TEXT_FIELDS, true)
                ? $this->sanitizeRichText($value)
                : strip_tags($value);
        });
        $request->merge($input);

        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:; font-src 'self' data: https:; connect-src 'self' https:; upgrade-insecure-requests");

        return $response;
    }

    private function sanitizeRichText(string $value): string
    {
        $value = strip_tags($value, '<p><br><b><strong><i><em><u><ul><ol><li><blockquote><code><pre><a>');
        $value = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $value) ?? '';
        $value = preg_replace('/\s+(?:style|srcdoc)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $value) ?? '';
        $value = preg_replace_callback('/\s+href\s*=\s*(["\'])(.*?)\1/iu', static function (array $match): string {
            $url = html_entity_decode(trim($match[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return preg_match('/^(https?:|mailto:|tel:|\/|#)/i', $url)
                ? ' href="'.e($url).'" rel="noopener noreferrer"'
                : '';
        }, $value) ?? '';
        return $value;
    }
}