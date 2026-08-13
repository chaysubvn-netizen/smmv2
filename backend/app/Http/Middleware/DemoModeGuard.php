<?php

namespace App\Http\Middleware;

use App\Models\Config;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoModeGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (($request->isMethodSafe() && !$this->isMutatingGet($request)) || $this->isAllowedAction($request)) {
            return $next($request);
        }

        $host = $request->getHost();
        $domain = str_starts_with($host, 'api.') ? substr($host, 4) : $host;
        $demoEnabled = (int) Config::where('domain', $domain)->value('status_demo') === 1;

        if (!$demoEnabled) {
            return $next($request);
        }

        return response()->json([
            'status' => false,
            'success' => false,
            'code' => 'DEMO_MODE',
            'message' => 'Hệ thống đang ở chế độ Demo. Bạn chỉ có thể xem dữ liệu và không thể thực hiện thay đổi.',
        ], 423);
    }

    private function isAllowedAction(Request $request): bool
    {
        return $request->is(
            'api/auth/api/login',
            'api/auth/api/logout',
            'auth/login',
            'auth/logout',
            'api/admin/settings/demo-mode',
            'api/telegram/webhook',
            'fpayaz-callback'
        );
    }

    private function isMutatingGet(Request $request): bool
    {
        if (!$request->isMethod('GET')) return false;

        return $request->is(
            'cron/*',
            'auto-update',
            '*/delete/*',
            'admin/*/delete/*'
        );
    }
}
