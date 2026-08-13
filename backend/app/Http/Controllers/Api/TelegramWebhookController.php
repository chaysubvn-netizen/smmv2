<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\TelegramCustom;
use App\Models\Config;
use App\Models\User;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $host = strtolower(preg_replace('/:\d+$/', '', $request->getHost()));
        $host = str_starts_with($host, 'api.') ? substr($host, 4) : $host;
        $config = Config::where('domain', $host)->first();
        if (! $config) return response()->json(['ok' => false], 404);
        $botToken = $config->telegram_bot ?: $config->tele_bot_token;
        if (! $botToken) return response()->json(['ok' => false], 503);
        $expectedSecret = hash_hmac('sha256', (string) $botToken, (string) config('app.key'));
        $providedSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');
        if (! hash_equals($expectedSecret, $providedSecret)) return response()->json(['ok' => false], 403);

        $chatId = data_get($request->input('message'), 'chat.id');
        $text = trim((string) data_get($request->input('message'), 'text', ''));
        if (!$chatId || !$text) return response()->json(['ok' => true]);
        $bot = new TelegramCustom((string) $chatId, $botToken);

        if (preg_match('/^\/lienket(?:@\w+)?\s+([a-f0-9]{32})$/i', $text, $matches)) {
            $user = User::where('api_key', $matches[1])->where('domain', $host)->first();
            if (!$user) $bot->sendMessage('Mã liên kết không hợp lệ.');
            else {
                User::where('telegram_chat_id', (string) $chatId)->where('id', '!=', $user->id)->update(['telegram_chat_id' => null]);
                $user->telegram_chat_id = (string) $chatId;
                $user->save();
                $bot->sendMessage('Liên kết Telegram với tài khoản <b>' . e($user->username) . '</b> thành công.');
            }
        } elseif (preg_match('/^\/huylienket(?:@\w+)?$/i', $text)) {
            $user = User::where('telegram_chat_id', (string) $chatId)->where('domain', $host)->first();
            if ($user && $user->two_factor_method === 'telegram') $bot->sendMessage('Hãy tắt Telegram Authenticator trên website trước khi hủy liên kết.');
            else {
                if ($user) { $user->telegram_chat_id = null; $user->save(); }
                $bot->sendMessage('Đã hủy liên kết Telegram.');
            }
        }
        return response()->json(['ok' => true]);
    }
}
