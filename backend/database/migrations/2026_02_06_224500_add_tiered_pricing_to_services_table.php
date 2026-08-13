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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_collaborator', 20, 2)->nullable()->default(0)->after('rate');
            $table->decimal('price_agency', 20, 2)->nullable()->default(0)->after('price_collaborator');
            $table->decimal('price_distributor', 20, 2)->nullable()->default(0)->after('price_agency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_collaborator', 'price_agency', 'price_distributor']);
        });
    }
};
