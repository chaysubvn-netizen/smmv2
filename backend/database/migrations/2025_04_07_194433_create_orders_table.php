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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->longText('link')->nullable();
            $table->longText('quantity')->nullable();
            $table->longText('rate')->nullable();
            $table->longText('payment')->nullable();
            $table->longText('total')->nullable();
            $table->longText('discount')->nullable();
            $table->json('input_data')->nullable();
            $table->json('response_data')->nullable();
            $table->longText('root_id')->nullable();
            $table->longText('next_id')->nullable();
            $table->integer('start_count')->default(0);
            $table->integer('remains')->default(0);
            $table->boolean('refill')->default(0);
            $table->string('status_refill')->nullable();
            $table->boolean('cancel')->default(0);
            $table->boolean('dripfeed')->default(0);
            $table->string('dripfeed_status')->nullable();
            $table->string('status')->default("Pending");
            $table->longText('provider')->nullable();
            $table->longText('loop_quantity')->nullable();
            $table->longText('loop_spacing')->nullable();
            $table->timestamp('schedule_time')->nullable();
            $table->longText('run')->nullable();
            $table->longText('start_time')->nullable();
            $table->longText('success_time')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->string('domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
