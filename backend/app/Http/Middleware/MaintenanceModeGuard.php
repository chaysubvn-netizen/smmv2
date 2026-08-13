<?php

namespace App\Http\Middleware;

use App\Models\Config;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodSafe() || !$this->isCustomerMutation($request)) {
            return $next($request);
        }

        $host = $request->getHost();
        $domain = str_starts_with($host, 'api.') ? substr($host, 4) : $host;
        $maintenance = Config::where('domain', $domain)->value('maintenance_mode') === 'on';

        if (!$maintenance) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'success' => false,
            'code' => 'MAINTENANCE_MODE',
            'message' => 'Hệ thống đang bảo trì. Các giao dịch mua hàng và thay đổi dữ liệu tạm thời không khả dụng.',
        ], 503);
    }

    private function isCustomerMutation(Request $request): bool
    {
        if ($request->is('api/admin/*', 'api/auth/api/login', 'api/auth/api/logout')) return false;

        return $request->is(
            'api/client/*',
            'request-order',
            'request-product',
            'request-action',
            'check-discount'
        );
    }
}
