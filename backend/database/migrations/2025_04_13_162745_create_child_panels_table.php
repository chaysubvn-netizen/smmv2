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
        Schema::create('child_panels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'expired'])->default('active');
            $table->string('status_api')->default('pending');
            $table->json('data')->nullable();
            $table->longText('api_key')->nullable();
            $table->timestamps();
            $table->string('main_domain')->nullable();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_panels');
    }
};
