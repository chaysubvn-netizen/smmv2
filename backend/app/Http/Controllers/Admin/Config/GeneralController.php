<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;

class GeneralController extends Controller
{
    public function index()
    {
        $config = Config::where('domain', request()->getHost())->first();
        return view('admin.config.general.index', compact('config'));
    }

    public function update(Request $request)
    {
        // ✅ Kiểm tra demo
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }

        $config = Config::where('domain', $request->getHost())->firstOrFail();

        $data = $request->except('_token');

        // If migration has not run yet, ignore the new field to prevent SQL error.
        if (!Schema::hasColumn('configs', 'mobile_bottom_nav_status')) {
            unset($data['mobile_bottom_nav_status']);
        }
        if (!Schema::hasColumn('configs', 'client_nav_style')) {
            unset($data['client_nav_style']);
        }
        
        // Handle checkbox for auto_update
        if (!isset($data['auto_update'])) {
            $data['auto_update'] = 0;
        }

        if (!isset($data['antiddos_status'])) {
            $data['antiddos_status'] = 'inactive';
        }

        $fillable = $config->getFillable();

        foreach ($data as $key => $value) {
            if (!in_array($key, $fillable)) {
                continue;
            }
            
            // Xử lý upload file
            if (in_array($key, ['logo', 'favicon', 'og_image']) && $request->hasFile($key)) {
                $file = $request->file($key);
                $filename = time() . '_' . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('uploads'), $filename);
                $config->$key = '/uploads/' . $filename;
            } else {
                // Cập nhật các trường thông thường
                $config->$key = $value;
            }
        }

    

 
        $config->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    }
    public function apiKeys(Request $request)
    {
        return view('admin.config.api-keys.index');
    }

    public function telegramLink()
    {
        $config = Config::where('domain', request()->getHost())->first() ?? Config::firstOrFail();
        $defaultWebhookUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . '/api/telegram/webhook';
        return view('admin.config.telegram-link.index', compact('config', 'defaultWebhookUrl'));
    }

    public function updateTelegramLink(Request $request)
    {
        if (status_demo()) return response()->json(['success' => false, 'message' => 'Không thể sử dụng trên website demo.']);
        $validated = $request->validate([
            'tele_bot_username' => ['nullable', 'string', 'max:100'],
            'telegram_link' => ['nullable', 'url', 'max:255'],
            'telegram_bot' => ['required', 'string', 'max:255'],
            'telegram_status' => ['required', 'in:active,inactive'],
            'telegram_webhook_url' => ['nullable', 'url', 'max:500'],
        ]);
        $validated['tele_bot_username'] = ltrim((string) ($validated['tele_bot_username'] ?? ''), '@');
        if (!$validated['telegram_link'] && $validated['tele_bot_username']) {
            $validated['telegram_link'] = 'https://t.me/' . $validated['tele_bot_username'];
        }
        if (!$validated['tele_bot_username']) {
            try {
                $botInfo = Http::timeout(15)->get("https://api.telegram.org/bot{$validated['telegram_bot']}/getMe")->json();
            } catch (\Throwable) {
                return response()->json(['success' => false, 'message' => 'Không thể kết nối Telegram API.'], 503);
            }
            if (!data_get($botInfo, 'ok')) {
                return response()->json(['success' => false, 'message' => data_get($botInfo, 'description', 'Bot Token không hợp lệ.')], 422);
            }
            $validated['tele_bot_username'] = data_get($botInfo, 'result.username');
            $validated['telegram_link'] = 'https://t.me/' . $validated['tele_bot_username'];
        }
        $config = Config::where('domain', $request->getHost())->first() ?? Config::firstOrFail();
        $config->fill($validated)->save();
        return response()->json(['success' => true, 'message' => 'Đã lưu cấu hình Telegram Link.']);
    }

    public function registerTelegramWebhook(Request $request)
    {
        if (status_demo()) return response()->json(['success' => false, 'message' => 'Không thể sử dụng trên website demo.']);
        $config = Config::where('domain', $request->getHost())->first() ?? Config::firstOrFail();
        $token = $config->telegram_bot ?: $config->tele_bot_token;
        $webhookUrl = $request->input('telegram_webhook_url') ?: $config->telegram_webhook_url;
        if (!$token || !$webhookUrl) return response()->json(['success' => false, 'message' => 'Thiếu Bot Token hoặc Webhook URL.'], 422);

        $botInfo = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getMe")->json();
        if (!data_get($botInfo, 'ok')) return response()->json(['success' => false, 'message' => data_get($botInfo, 'description', 'Bot Token không hợp lệ.')], 422);
        $result = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $webhookUrl])->json();
        if (!data_get($result, 'ok')) return response()->json(['success' => false, 'message' => data_get($result, 'description', 'Không thể đăng ký webhook.')], 422);

        $username = data_get($botInfo, 'result.username');
        $config->tele_bot_username = $username;

        $config->telegram_webhook_url = $webhookUrl;
        $config->telegram_status = 'active';
        $config->save();
        return response()->json(['success' => true, 'message' => 'Đăng ký webhook Telegram thành công.', 'data' => ['username' => $username, 'webhook_url' => $webhookUrl]]);
    }

    public function notifications()
    {
        return view('admin.config.notifications.index');
    }
}
