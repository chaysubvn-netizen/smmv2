<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use App\Models\Version;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Paginator::useBootstrapFive();
        // Use a closure with a static variable to cache the result for the request lifecycle
        $getVersion = function() {
            static $version = null;
            static $checked = false;
            if (!$checked) {
                if (Schema::hasTable('versions')) {
                    $latestVersion = Version::where('is_latest', true)->latest()->first();
                    $version = $latestVersion?->version;
                }
                $checked = true;
            }
            return $version;
        };

        \Illuminate\Support\Facades\View::composer('*', function ($view) use ($getVersion) {
            $view->with('latestVersion', $getVersion());
            // Share all currencies globally to avoid hardcoding in views
            $view->with('currencies', \App\Models\Currency::all());
        });
    }
}
