<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('configs')) {
            return;
        }

        Schema::table('configs', function (Blueprint $table) {
            if (! Schema::hasColumn('configs', 'ocb_account_number')) {
                $table->longText('ocb_account_number')->nullable();
            }
            if (! Schema::hasColumn('configs', 'ocb_username')) {
                $table->longText('ocb_username')->nullable();
            }
            if (! Schema::hasColumn('configs', 'ocb_password')) {
                $table->longText('ocb_password')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            foreach (['ocb_account_number', 'ocb_username', 'ocb_password'] as $column) {
                if (Schema::hasColumn('configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
