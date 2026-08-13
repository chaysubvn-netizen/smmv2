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
        Schema::table('api_providers', function (Blueprint $table) {
            $table->boolean('sync_services')->default(false)->after('note');
            $table->boolean('auto_update_price')->default(false)->after('sync_services');
            $table->integer('increase_rate_collaborator')->default(0)->after('auto_update_price');
            $table->integer('increase_rate_agency')->default(0)->after('increase_rate_collaborator');
            $table->integer('increase_rate_distributor')->default(0)->after('increase_rate_agency');
            $table->string('price_format')->default('1000')->after('increase_rate_distributor');
            $table->boolean('auto_update_name')->default(false)->after('price_format');
            $table->boolean('filter_html')->default(false)->after('auto_update_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_providers', function (Blueprint $table) {
            //
        });
    }
};
