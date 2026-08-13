<?php

namespace App\Http\Middleware;

use App\Models\Config;
use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DdosProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        if (request()->has('debug_ip')) { die("Detected IP: " . $ip); }
        $domain = $request->getHost();

        // Check if IP is explicitly blocked
        $isBlocked = \DB::table('blocked_ips')
            ->where('ip', $ip)
            ->where(function($query) use ($domain) {
                $query->where('domain', $domain)->orWhereNull('domain');
            })
            ->exists();

        if ($isBlocked) {
            abort(403, 'Địa chỉ IP của bạn đã bị chặn do nghi ngờ vi phạm chính sách của chúng tôi.');
        }

        // Check if Anti-DDoS mode is active globally
        $config = Config::where('domain', $domain)->first();
        if ($config && $config->antiddos_status === 'active') {
            // Optional: Implement stricter rate limiting here if needed
            // For now, it just ensures the table-based IP blocking is functional
        }

        return $next($request);
    }
}
