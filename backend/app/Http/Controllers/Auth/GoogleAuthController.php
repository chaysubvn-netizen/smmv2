<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth page
     */
    public function redirectToGoogle(Request $request)
    {
        // Check if Google OAuth is enabled
        if (function_exists('s') && s('google_login_status') !== 'active') {
            return $this->redirectToFrontend('Google Login hiện không khả dụng.');
        }
        
        // Ensure configuration is valid
        if (!config('services.google.client_id') || !config('services.google.client_secret')) {
             return $this->redirectToFrontend('Google OAuth chưa được cấu hình đúng trong Admin Panel.');
        }
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists by Google ID
            $domain = $request->getHost();
            if (str_starts_with($domain, 'api.')) {
                $domain = substr($domain, 4);
            }

            $user = User::where('google_id', $googleUser->getId())
                ->where('domain', $domain)
                ->first();
            
            if ($user) {
                return $this->completeLogin($user);
            }
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())
                ->where('domain', $domain)
                ->first();
            
            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                
                return $this->completeLogin($user);
            }
            
            // Create new user
            $user = User::create([
                'username' => $this->generateUniqueUsername($googleUser->getName()),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => null, // No password for Google users
                'role' => 'user',
                'level' => 'member',
                'balance' => 0,
                'status' => 'active',
                'timezone' => 'Asia/Ho_Chi_Minh',
                'language' => 'vn',
                'currency' => 'VND',
                'referral_code' => Str::random(10),
                'api_key' => Str::random(32),
                'domain' => $domain,
            ]);

            return $this->completeLogin($user);
            
        } catch (Throwable $e) {
            report($e);
            return $this->redirectToFrontend('Đăng nhập Google thất bại. Vui lòng thử lại.');
        }
    }

    private function completeLogin(User $user)
    {
        if ($user->status !== 'active') {
            return $this->redirectToFrontend('Tài khoản của bạn đã bị khóa.');
        }

        $code = Str::random(64);
        Cache::put('google_auth_code:' . $code, $user->id, now()->addMinutes(2));

        return redirect()->away(rtrim((string) env('FRONTEND_URL', 'http://localhost:3000'), '/') . '/login/google/callback?code=' . urlencode($code));
    }

    private function redirectToFrontend(string $error)
    {
        return redirect()->away(rtrim((string) env('FRONTEND_URL', 'http://localhost:3000'), '/') . '/login?google_error=' . urlencode($error));
    }
    
    /**
     * Generate unique username from Google name
     */
    private function generateUniqueUsername($name)
    {
        // Remove spaces and special characters
        $username = Str::slug($name, '');
        $originalUsername = $username;
        $counter = 1;
        
        // Check if username exists, add number if needed
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}
