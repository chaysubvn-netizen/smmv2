<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'mobile_bottom_nav_status')) {
                if (Schema::hasColumn('configs', 'auto_update')) {
                    $table->boolean('mobile_bottom_nav_status')->default(1)->after('auto_update');
                } else {
                    $table->boolean('mobile_bottom_nav_status')->default(1);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (Schema::hasColumn('configs', 'mobile_bottom_nav_status')) {
                $table->dropColumn('mobile_bottom_nav_status');
            }
        });
    }
};

