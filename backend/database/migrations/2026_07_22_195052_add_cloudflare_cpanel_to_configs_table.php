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
            $table->boolean('child_panel_enabled')->default(1);
            $table->integer('child_panel_monthly_price')->default(100000);
            $table->string('cloudflare_email')->nullable();
            $table->string('cloudflare_global_key')->nullable();
            $table->string('cloudflare_account_id')->nullable();
            $table->string('cloudflare_token')->nullable();
            $table->string('cloudflare_ip_host')->nullable();
            $table->string('hosting_panel_type')->default('cpanel');
            $table->string('cpanel_host')->nullable();
            $table->string('cpanel_username')->nullable();
            $table->string('cpanel_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn([
                'child_panel_enabled',
                'child_panel_monthly_price',
                'cloudflare_email',
                'cloudflare_global_key',
                'cloudflare_account_id',
                'cloudflare_token',
                'cloudflare_ip_host',
                'hosting_panel_type',
                'cpanel_host',
                'cpanel_username',
                'cpanel_password'
            ]);
        });
    }
};
