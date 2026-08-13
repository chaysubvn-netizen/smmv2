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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('symbol')->nullable();
            $table->decimal('exchange_rate', 15, 2)->default(1);
            $table->string('thousand_separator')->default(',');
            $table->string('decimal_separator')->default('.');
            $table->unsignedInteger('decimal_places')->default(2);
            $table->enum('position', ['left', 'right'])->default('right');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
