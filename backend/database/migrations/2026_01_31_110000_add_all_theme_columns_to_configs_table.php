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
            
            // Add theme color columns if they don't exist
            if (!Schema::hasColumn('configs', 'theme_color_primary')) {
                $table->string('theme_color_primary')->default('#0d6efd')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_primary_rgb')) {
                $table->string('theme_color_primary_rgb')->default('13, 110, 253')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_primary_text')) {
                $table->string('theme_color_primary_text')->default('#ffffff')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_link')) {
                $table->string('theme_color_link')->default('#0d6efd')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_link_hover')) {
                $table->string('theme_color_link_hover')->default('#0a58ca')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_sidebar')) {
                $table->string('theme_color_sidebar')->default('#ffffff')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_sidebar_border')) {
                $table->string('theme_color_sidebar_border')->default('#dee2e6')->nullable();
            }
            if (!Schema::hasColumn('configs', 'theme_color_menu_item')) {
                $table->string('theme_color_menu_item')->default('#212529')->nullable();
            }

            // Add theme effect if it doesn't exist
            if (!Schema::hasColumn('configs', 'theme_effect')) {
                // If theme_color_menu_item exists, add after it, otherwise just add it
                if (Schema::hasColumn('configs', 'theme_color_menu_item')) {
                     $table->string('theme_effect')->nullable()->after('theme_color_menu_item');
                } else {
                     $table->string('theme_effect')->nullable();
                }
            }

            // Add theme mode if it doesn't exist
            if (!Schema::hasColumn('configs', 'theme_mode')) {
                // If theme_effect exists, add after it
                if (Schema::hasColumn('configs', 'theme_effect')) {
                    $table->string('theme_mode')->default('dark')->after('theme_effect');
                } else {
                    $table->string('theme_mode')->default('dark');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            $columns = [
                'theme_color_primary',
                'theme_color_primary_rgb',
                'theme_color_primary_text',
                'theme_color_link',
                'theme_color_link_hover',
                'theme_color_sidebar',
                'theme_color_sidebar_border',
                'theme_color_menu_item',
                'theme_effect',
                'theme_mode'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
