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
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_cron_id')->constrained('user_crons')->onDelete('cascade');
            $table->integer('status_code')->nullable();
            $table->decimal('duration', 8, 4)->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
    }
};
