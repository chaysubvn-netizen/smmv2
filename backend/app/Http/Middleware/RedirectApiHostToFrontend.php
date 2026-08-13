<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectApiHostToFrontend
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (! str_starts_with($host, 'api.') || $request->is('api/*', 'up', 'CronJobs/RunAll')) {
            return $next($request);
        }

        $frontendUrl = $request->getScheme().'://'.substr($host, 4);

        $path = '/'.ltrim($request->getRequestUri(), '/');

        return redirect()->away($frontendUrl.$path, 308);
    }
}
