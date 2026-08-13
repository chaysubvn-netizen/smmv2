<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_crons', function (Blueprint $table) {
            $table->timestamp('last_run_at')->nullable();
            $table->integer('last_status_code')->nullable();
            $table->decimal('last_duration', 8, 4)->nullable(); // Seconds
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_crons', function (Blueprint $table) {
            $table->dropColumn(['last_run_at', 'last_status_code', 'last_duration']);
        });
    }
};
