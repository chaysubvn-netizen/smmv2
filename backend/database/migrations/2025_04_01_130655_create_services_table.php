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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->longText('name');
            $table->longText('description')->nullable();
            $table->longText('icon')->nullable();
            $table->longText('code')->nullable();
            $table->string('mode')->nullable(); // Manual, Option
            $table->longText('rate_original')->nullable();
            $table->longText('rate')->nullable();
            $table->longText('min')->nullable();
            $table->longText('max')->nullable();
            $table->longText('provider')->nullable();
            $table->longText('provider_id')->nullable();
            $table->string('type')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('warranty')->default(0);
            $table->boolean('refill')->default(false);
            $table->boolean('cancel')->default(false);
            $table->boolean('dripfeed')->default(false); // Dripfeed
            $table->longText('average_time')->nullable();
            $table->json('attributes')->nullable();
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('services');
    }
};
