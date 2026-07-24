<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use App\Library\TelegramCustom;
use App\Models\Config;
use App\Models\AffiliateRef;
use Illuminate\Support\Facades\Cache;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required|string|min:6|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => false,
                'message' => $valid->errors()->first(),
                'errors' => $valid->errors()
            ], 422);
        }

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        
        $host = $this->siteHost($request);
        $user->domain = $host;
        $user->timezone = 'Asia/Ho_Chi_Minh';
        $user->language = 'vi';
        $user->currency = 'USD';
        $user->status = 'active';
        $user->referral_code = strtoupper(Str::random(8));
        $user->api_key = bin2hex(random_bytes(16));
        $user->save();

        if ($request->filled('ref_username')) {
            $referrer = User::where('username', $request->input('ref_username'))
                ->where('domain', $host)
                ->where('id', '!=', $user->id)
                ->first();
            if ($referrer) {
                $user->ref_by_code = $referrer->referral_code;
                $user->save();
                AffiliateRef::firstOrCreate(
                    ['user_id' => $user->id],
                    ['ref_user_id' => $referrer->id, 'referral_code' => $referrer->referral_code, 'domain' => $host]
                );
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng ký thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => false,
                'message' => $valid->errors()->first(),
            ], 422);
        }

        $host = $this->siteHost($request);
        $legacyApiHost = 'api.' . $host;
        $user = User::where('username', $request->username)
            ->where(function ($query) use ($host, $legacyApiHost) {
                $query->whereIn('domain', [$host, $legacyApiHost])
                    ->orWhereNull('domain')
                    ->orWhere('domain', '');
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản hoặc mật khẩu không đúng',
            ], 401);
        }

        if ($user->status != 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa',
            ], 403);
        }

        // Older installs made through the API subdomain stored api.example.com
        // instead of the actual site domain. Normalize them after authentication.
        if (blank($user->domain) || $user->domain === $legacyApiHost) {
            $oldDomain = $user->domain;
            $user->domain = $host;
            $user->save();

            if ($oldDomain === $legacyApiHost && !Config::where('domain', $host)->exists()) {
                Config::where('user_id', $user->id)
                    ->where('domain', $legacyApiHost)
                    ->update(['domain' => $host]);
            }
        }

        $twoFactorMethod = $user->two_factor_method ?: ($user->two_factor_secret ? 'google' : null);
        if ($twoFactorMethod === 'google') {
            if (!$request->filled('two_factor_code')) {
                return response()->json(['status' => false, 'two_factor_auth' => true, 'message' => 'Vui lòng nhập mã Google Authenticator.'], 422);
            }
            if (!(new Google2FA())->verifyKey($user->two_factor_secret, $request->two_factor_code)) {
                return response()->json(['status' => false, 'two_factor_auth' => true, 'message' => 'Mã xác thực không đúng.'], 422);
            }
        }

        if ($twoFactorMethod === 'telegram') {
            if (!$request->filled('two_factor_code')) {
                if (!Cache::has("telegram_login_cooldown:{$user->id}")) {
                    $code = (string) random_int(100000, 999999);
                    Cache::put("telegram_login_code:{$user->id}", Hash::make($code), now()->addMinutes(5));
                    Cache::put("telegram_login_cooldown:{$user->id}", true, now()->addSeconds(60));
                    $config = Config::where('domain', $user->domain)->first() ?? Config::first();
                    $token = $config?->telegram_bot ?: $config?->tele_bot_token;
                    $sent = $token && $user->telegram_chat_id
                        ? (new TelegramCustom($user->telegram_chat_id, $token))->sendMessage("Mã đăng nhập của bạn là: <b>{$code}</b>\nMã có hiệu lực trong 5 phút.")
                        : false;
                    if (!$sent) {
                        Cache::forget("telegram_login_code:{$user->id}");
                        Cache::forget("telegram_login_cooldown:{$user->id}");
                        return response()->json(['status' => false, 'message' => 'Không thể gửi mã Telegram. Vui lòng liên hệ quản trị viên.'], 503);
                    }
                }
                return response()->json(['status' => false, 'two_factor_auth' => true, 'two_factor_method' => 'telegram', 'message' => 'Mã xác thực đã được gửi qua Telegram.'], 422);
            }
            $hash = Cache::get("telegram_login_code:{$user->id}");
            if (!$hash || !Hash::check($request->two_factor_code, $hash)) {
                return response()->json(['status' => false, 'two_factor_auth' => true, 'two_factor_method' => 'telegram', 'message' => 'Mã Telegram không đúng hoặc đã hết hạn.'], 422);
            }
            Cache::forget("telegram_login_code:{$user->id}");
        }

        $user->last_login = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function exchangeGoogleCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:64'],
        ]);

        $userId = Cache::pull('google_auth_code:' . $validated['code']);
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Liên kết đăng nhập Google không hợp lệ hoặc đã hết hạn.',
            ], 422);
        }

        $user = User::find($userId);
        if (!$user || $user->status !== 'active') {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản không tồn tại hoặc đã bị khóa.',
            ], 403);
        }

        $user->last_login = now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập Google thành công',
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    private function siteHost(Request $request): string
    {
        $host = strtolower(trim((string) $request->header('X-Site-Host', $request->getHost())));
        $host = preg_replace('/:\d+$/', '', $host) ?: $request->getHost();

        if (str_starts_with($host, 'api.')) {
            $host = substr($host, 4);
        }

        abort_unless(
            filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)
                || in_array($host, ['localhost', '127.0.0.1'], true),
            422,
            'Invalid site domain.'
        );

        return $host;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
