<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerifyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guard = empty($guards) ? null : $guards[0];
        
        if (!Auth::guard($guard)->check()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.', 'success' => false], 401);
            }
            return redirect()->route('auth.login');
        }

        // Set the default guard so $request->user() works
        if ($guard) {
            Auth::shouldUse($guard);
        }

        // If authenticated, proceed with the request
        return $next($request);
    }
}
