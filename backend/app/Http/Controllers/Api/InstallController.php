<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChildPanel;
use App\Models\Config;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $host = $this->siteHost($request);

        return response()->json([
            'status' => true,
            'data' => [
                'installed' => Config::where('domain', $host)->exists(),
                'is_main_site' => $this->isMainSite($host),
                'host' => $host,
            ],
        ]);
    }

    public function install(Request $request): JsonResponse
    {
        $host = $this->siteHost($request);
        $isMainSite = $this->isMainSite($host);

        if (Config::where('domain', $host)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Hệ thống đã được cài đặt. Không thể cài đặt lại.',
                'code' => 'ALREADY_INSTALLED',
            ], 409);
        }

        $rules = [
            'username' => 'required|string|min:6|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password',
            'condition' => 'required|accepted',
        ];

        if (!$isMainSite) {
            $rules['api_key'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $childPanel = null;
        if (!$isMainSite) {
            $childPanel = ChildPanel::where('api_key', (string) $request->string('api_key'))
                ->where('name', $host)
                ->first();

            if (!$childPanel) {
                return response()->json(['status' => false, 'message' => 'API Key không đúng.'], 422);
            }

            if ($childPanel->status !== ChildPanel::STATUS_ACTIVE) {
                return response()->json(['status' => false, 'message' => 'Child Panel chưa được kích hoạt hoặc không còn khả dụng.'], 409);
            }
        }

        DB::transaction(function () use ($request, $host, $isMainSite, $childPanel): void {
            $user = User::create([
                'username' => (string) $request->string('username'),
                'email' => (string) $request->string('email'),
                'password' => Hash::make((string) $request->string('password')),
                'role' => 'admin',
                'domain' => $host,
                'timezone' => 'Asia/Ho_Chi_Minh',
                'language' => 'vi',
                'status' => 'active',
                'currency' => 'USD',
                'referral_code' => strtoupper(Str::random(8)),
                'ref_by_code' => null,
                'api_key' => bin2hex(random_bytes(16)),
            ]);

            $config = new Config();
            $config->user_id = $user->id;
            $config->domain_main = $isMainSite ? env('MAIN_SITE') : $childPanel->domain;
            $config->domain = $host;
            $config->timezone = 'Asia/Ho_Chi_Minh';
            $config->currency = 'VND';
            $config->status = 'active';
            $config->last_payment_time = $isMainSite ? null : now();
            $config->title = $isMainSite ? 'Chưa cấu hình' : 'SMM Panel';
            $config->description = $isMainSite ? 'Chưa cấu hình' : 'Hệ thống SMM Panel';
            $config->save();

            Currency::firstOrCreate(['code' => 'USD'], [
                'name' => 'Đô la Mỹ', 'symbol' => '$', 'exchange_rate' => 1,
                'thousand_separator' => 'comma', 'decimal_separator' => 'dot',
                'decimal_places' => 2, 'position' => 'left', 'status' => 'active',
            ]);
            Currency::firstOrCreate(['code' => 'VND'], [
                'name' => 'Việt Nam Đồng', 'symbol' => '₫', 'exchange_rate' => 25000,
                'thousand_separator' => 'dot', 'decimal_separator' => 'comma',
                'decimal_places' => 0, 'position' => 'right', 'status' => 'active',
            ]);

            if ($childPanel) {
                $childPanel->status = ChildPanel::STATUS_ACTIVE;
                $childPanel->save();
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Cài đặt website thành công.',
            'redirect' => '/login',
        ], 201);
    }

    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        if (str_starts_with($host, 'api.')) {
            $host = substr($host, 4);
        }

        abort_unless(filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) || in_array($host, ['localhost', '127.0.0.1'], true), 422, 'Tên miền không hợp lệ.');

        return $host;
    }

    private function isMainSite(string $host): bool
    {
        $mainSite = strtolower((string) env('MAIN_SITE', '127.0.0.1'));

        return $host === $mainSite;
    }
}
