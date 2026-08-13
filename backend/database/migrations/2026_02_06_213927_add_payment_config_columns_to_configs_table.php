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
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'transfer_code')) {
                $table->string('transfer_code')->nullable();
            }
            if (!Schema::hasColumn('configs', 'recharge_promotion')) {
                $table->integer('recharge_promotion')->default(0);
            }
            if (!Schema::hasColumn('configs', 'promotion_min')) {
                $table->bigInteger('promotion_min')->default(0);
            }
            if (!Schema::hasColumn('configs', 'bank_status')) {
                $table->string('bank_status')->default('active');
            }
            if (!Schema::hasColumn('configs', 'usdt_status')) {
                $table->string('usdt_status')->default('active');
            }
            if (!Schema::hasColumn('configs', 'binance_status')) {
                $table->string('binance_status')->default('active');
            }
            if (!Schema::hasColumn('configs', 'binance_id')) {
                $table->string('binance_id')->nullable();
            }
            if (!Schema::hasColumn('configs', 'binance_qr')) {
                $table->string('binance_qr')->nullable();
            }
            if (!Schema::hasColumn('configs', 'binance_merchant_id')) {
                $table->string('binance_merchant_id')->nullable();
            }
            if (!Schema::hasColumn('configs', 'binance_api_key')) {
                $table->text('binance_api_key')->nullable();
            }
            if (!Schema::hasColumn('configs', 'binance_secret_key')) {
                $table->text('binance_secret_key')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            //
        });
    }
};
