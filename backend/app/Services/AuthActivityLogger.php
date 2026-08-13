<?php

namespace App\Services;

use App\Models\AuthActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuthActivityLogger
{
    public static function record(
        Request $request,
        string $event,
        ?User $user,
        bool $success,
        ?string $message = null,
        array $metadata = [],
        ?string $username = null,
    ): void {
        try {
            if (!Schema::hasTable('auth_activity_logs')) {
                return;
            }

            $host = strtolower($request->getHost());
            if (str_starts_with($host, 'api.')) {
                $host = substr($host, 4);
            }

            AuthActivityLog::create([
                'user_id' => $user?->id,
                'username' => $user?->username ?? $username,
                'event' => $event,
                'success' => $success,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 2000),
                'domain' => $user?->domain ?: $host,
                'message' => $message,
                'metadata' => $metadata ?: null,
                'created_at' => now(),
            ]);
        } catch (Throwable $error) {
            report($error);
        }
    }
}
