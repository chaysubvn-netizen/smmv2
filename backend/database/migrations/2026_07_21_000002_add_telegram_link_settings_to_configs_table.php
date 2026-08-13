<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'tele_bot_username')) $table->string('tele_bot_username')->nullable();
            if (!Schema::hasColumn('configs', 'telegram_webhook_url')) $table->string('telegram_webhook_url', 500)->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (Schema::hasColumn('configs', 'telegram_webhook_url')) $table->dropColumn('telegram_webhook_url');
            if (Schema::hasColumn('configs', 'tele_bot_username')) $table->dropColumn('tele_bot_username');
        });
    }
};
