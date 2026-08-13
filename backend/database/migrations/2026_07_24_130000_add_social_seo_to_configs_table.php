<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The legacy configs table is close to InnoDB's inline row-size limit.
        // Moving existing variable-length metadata off-page frees room safely.
        Schema::table('configs', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->text('keywords')->nullable()->change();
            $table->text('logo')->nullable()->change();
            $table->text('favicon')->nullable()->change();
            $table->text('og_image')->nullable()->change();
        });

        Schema::table('configs', function (Blueprint $table) {
            $table->text('og_title')->nullable()->after('og_image');
            $table->text('og_description')->nullable()->after('og_title');
            $table->text('twitter_card_type')->nullable()->after('og_description');
            $table->text('twitter_title')->nullable()->after('twitter_card_type');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->text('twitter_image')->nullable()->after('twitter_description');
        });
    }

    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $table->dropColumn([
                'og_title', 'og_description', 'twitter_card_type',
                'twitter_title', 'twitter_description', 'twitter_image',
            ]);
        });
    }
};
