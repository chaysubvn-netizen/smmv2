<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'footer_text')) $table->string('footer_text')->nullable();
            if (!Schema::hasColumn('configs', 'system_version')) $table->string('system_version', 50)->nullable();
            if (!Schema::hasColumn('configs', 'developer_name')) $table->string('developer_name')->nullable();
            if (!Schema::hasColumn('configs', 'developer_url')) $table->string('developer_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['footer_text', 'system_version', 'developer_name', 'developer_url'],
                fn (string $column) => Schema::hasColumn('configs', $column)
            ));
            if ($columns !== []) $table->dropColumn($columns);
        });
    }
};
