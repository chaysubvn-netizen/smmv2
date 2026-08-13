<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update USD currency configuration to match reference site format
        DB::table('currencies')
            ->where('code', 'USD')
            ->update([
                'symbol' => '$',
                'position' => 'left',
                'thousand_separator' => 'comma',
                'decimal_separator' => 'dot',
                'decimal_places' => 2,
                'exchange_rate' => 1,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert USD to previous config (if needed)
        DB::table('currencies')
            ->where('code', 'USD')
            ->update([
                'thousand_separator' => 'dot',
                'decimal_separator' => 'comma',
                'updated_at' => now(),
            ]);
    }
};
