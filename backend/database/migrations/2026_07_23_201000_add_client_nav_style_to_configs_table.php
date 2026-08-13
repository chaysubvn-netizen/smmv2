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
        if (!Schema::hasColumn('configs', 'client_nav_style')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->string('client_nav_style', 20)->default('nav1')->after('mobile_bottom_nav_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('configs', 'client_nav_style')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->dropColumn('client_nav_style');
            });
        }
    }
};
