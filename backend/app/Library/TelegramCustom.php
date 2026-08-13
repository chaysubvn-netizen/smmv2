<?php

namespace App\Library;

use Telegram\Bot\Api;

class TelegramCustom
{
    protected $telegram;

    private $chat_id = "";
    private $bot_token = "";

    public function __construct($chat_id = "", $bot_token = "")
    {
        $this->chat_id = $chat_id;
        $this->bot_token = $bot_token;
        $this->telegram = new Api($this->bot_token);
    }

    public function sendMessage($message)
    {
        try {
            if ($this->chat_id && $this->bot_token) {
                $this->telegram->sendMessage([
                    'chat_id' => $this->chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
                return true;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Telegram Error: ' . $e->getMessage());
            return false;
        }
    }
}
