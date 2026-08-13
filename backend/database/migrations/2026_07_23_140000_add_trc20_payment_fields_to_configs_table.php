<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'trc20_status')) $table->string('trc20_status', 20)->default('inactive');
            if (!Schema::hasColumn('configs', 'trc20_wallet')) $table->string('trc20_wallet', 50)->nullable();
            if (!Schema::hasColumn('configs', 'trongrid_api_key')) $table->text('trongrid_api_key')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            foreach (['trc20_status', 'trc20_wallet', 'trongrid_api_key'] as $column) {
                if (Schema::hasColumn('configs', $column)) $table->dropColumn($column);
            }
        });
    }
};
