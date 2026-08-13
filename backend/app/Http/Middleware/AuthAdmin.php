<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use the user resolved by the active guard, such as Sanctum for API routes.
        $user = $request->user();

        if (! $user) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->route('auth.login');
        }

        abort_unless($user->role === 'admin', 403, 'Administrator access required.');

        // Legacy Blade admin routes are reserved for the configured site owner.
        // API routes are already tenant-scoped by AuthSite and Sanctum.
        if (! $request->is('api/*')) {
            abort_unless(
                (string) $user->getAuthIdentifier() === (string) s('user_id'),
                403,
                'Administrator access required for this site.'
            );
        }

        return $next($request);
    }
}
