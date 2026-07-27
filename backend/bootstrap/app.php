<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function(){
            // Subdomain API routing (e.g., api.minsmm.net/v2)
            $host = request()->getHost();
            if (strpos($host, 'api.') === 0) {
                Route::middleware('api')
                    ->group(base_path('routes/api.php'));
            }

            Route::prefix('admin')->middleware(['web', 'admin'])->group(base_path('routes/admin.php'));
            Route::prefix('auth')->middleware(['web', 'guest'])->group(base_path('routes/auth.php'));
            Route::prefix('cron')->middleware(['web'])->group(base_path('routes/cron.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\GoogleConfigMiddleware::class,
            \App\Http\Middleware\XssMiddleware::class,
            \App\Http\Middleware\DdosProtection::class,
        ]);
        
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\AuthenticateWithCookie::class,
            \App\Http\Middleware\AuthSite::class,
            \App\Http\Middleware\DemoModeGuard::class,
            \App\Http\Middleware\MaintenanceModeGuard::class,
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\VerifyAuth::class,
            'guest' => \App\Http\Middleware\VerifyGuest::class,
            'admin' => \App\Http\Middleware\AuthAdmin::class,
            'config' => \App\Http\Middleware\HandleConfigSite::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'fpayaz-callback',
        ]);
        
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            return url('/auth/login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.', 'success' => false], 401);
            }
        });
    })->create();
