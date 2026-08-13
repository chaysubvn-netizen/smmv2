<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campaign_configs', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('domain')->index(); $table->timestamps();
        });
        Schema::create('campaign_config_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('campaign_config_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity'); $table->unsignedInteger('delay_minutes')->default(0);
            $table->unsignedInteger('loop_count')->default(0); $table->unsignedInteger('loop_spacing')->default(0); $table->timestamps();
        });
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_config_id')->constrained()->cascadeOnDelete();
            $table->string('channel_id', 64); $table->string('channel_url', 500)->nullable();
            $table->timestamp('expires_at'); $table->enum('status', ['active','paused','expired'])->default('active');
            $table->string('last_video_id', 32)->nullable(); $table->timestamp('last_checked_at')->nullable();
            $table->text('last_error')->nullable(); $table->string('domain')->index(); $table->timestamps();
            $table->index(['status','expires_at']);
        });
        Schema::create('campaign_runs', function (Blueprint $table) {
            $table->id(); $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('video_id', 32); $table->string('video_url', 500); $table->json('results')->nullable();
            $table->enum('status', ['success','partial','failed'])->default('success'); $table->timestamps();
            $table->unique(['campaign_id','video_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('campaign_runs'); Schema::dropIfExists('campaigns'); Schema::dropIfExists('campaign_config_items'); Schema::dropIfExists('campaign_configs'); }
};
