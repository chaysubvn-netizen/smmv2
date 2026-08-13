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
        Schema::create('cron_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0); // Price per month
            $table->string('region')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('limit_req')->default(0); // 0 = unlimited
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('user_crons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('server_id')->constrained('cron_servers')->onDelete('cascade');
            $table->string('url');
            $table->string('method')->default('GET');
            $table->integer('loop_time')->default(60); // Seconds
            $table->string('status')->default('active'); // active, expired, stopped
            $table->decimal('price_paid', 15, 2)->default(0);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_crons');
        Schema::dropIfExists('cron_servers');
    }
};
