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
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type')->nullable();
            $table->string('code')->nullable();
            $table->longText('api_url')->nullable();
            $table->longText('api_key')->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->decimal('exchange_rate', 10, 2)->nullable();
            $table->integer('increase_rate')->default(0);
            $table->string('rate_per')->default('1000');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->longText('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_providers');
    }
};
