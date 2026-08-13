<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('system_update_logs', function (Blueprint $table) {
            $table->id();
            $table->string('from_version', 40)->nullable();
            $table->string('to_version', 40)->nullable();
            $table->string('status', 20)->default('processing');
            $table->string('file_name')->nullable();
            $table->unsignedInteger('migrations')->default(0);
            $table->string('admin_name')->nullable();
            $table->text('message')->nullable();
            $table->json('log')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_update_logs');
    }
};
