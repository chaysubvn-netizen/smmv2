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
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable();
            $table->string('request_id')->nullable();
            $table->string('method')->nullable();
            $table->string('type')->nullable();
            $table->string('amount')->nullable();
            $table->string('real_amount')->nullable();
            $table->string('bonus')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};
