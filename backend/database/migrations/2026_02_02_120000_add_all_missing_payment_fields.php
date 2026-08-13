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
            if (!Schema::hasColumn('configs', 'binance_api_key')) {
                $table->string('binance_api_key')->nullable();
            }
            if (!Schema::hasColumn('configs', 'binance_merchant_id')) {
                $table->string('binance_merchant_id')->nullable()->after('binance_api_key');
            }
            if (!Schema::hasColumn('configs', 'binance_secret_key')) {
                $table->string('binance_secret_key')->nullable()->after('binance_merchant_id');
            }
            if (!Schema::hasColumn('configs', 'fpayaz_key')) {
                $table->string('fpayaz_key')->nullable();
            }
            if (!Schema::hasColumn('configs', 'fpayaz_secret')) {
                $table->string('fpayaz_secret')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $columns = ['binance_api_key', 'binance_merchant_id', 'binance_secret_key', 'fpayaz_key', 'fpayaz_secret'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
