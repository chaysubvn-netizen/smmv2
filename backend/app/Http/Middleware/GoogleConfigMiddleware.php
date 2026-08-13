<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GoogleConfigMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ chạy nếu tính năng Google Login được bật và có hàm s()
        if (function_exists('s') && s('google_login_status') === 'active') {
            $clientId = s('google_client_id');
            $clientSecret = s('google_client_secret');
            $redirectUri = s('google_redirect_uri') ?: url('/auth/google/callback');

            if ($clientId && $clientSecret) {
                config([
                    'services.google.client_id' => $clientId,
                    'services.google.client_secret' => $clientSecret,
                    'services.google.redirect' => $redirectUri,
                ]);
            }
        }

        return $next($request);
    }
}
