<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'google_client_id')) {
                $table->string('google_client_id')->nullable();
            }
            if (!Schema::hasColumn('configs', 'google_client_secret')) {
                $table->string('google_client_secret')->nullable();
            }
            if (!Schema::hasColumn('configs', 'google_redirect_uri')) {
                $table->string('google_redirect_uri')->nullable();
            }
            if (!Schema::hasColumn('configs', 'google_login_status')) {
                $table->string('google_login_status')->default('inactive');
            }
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $columns = array_values(array_filter(
                [
                    'google_client_id',
                    'google_client_secret',
                    'google_redirect_uri',
                    'google_login_status',
                ],
                fn (string $column) => Schema::hasColumn('configs', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
