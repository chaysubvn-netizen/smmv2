<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->after('two_factor_secret');
            $table->string('two_factor_method', 20)->nullable()->after('telegram_chat_id');
        });
        DB::table('users')->whereNotNull('two_factor_secret')->update(['two_factor_method' => 'google']);
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id', 'two_factor_method']);
        });
    }
};
