<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class HandleConfigSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if the site configuration is set
        // check language
        $language = s('language') ?: 'vi';
        
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->language) {
                $language = $user->language;
            }
        }

        // Standardize vn -> vi
        if ($language === 'vn') {
            $language = 'vi';
        }

        // Set locale if valid
        if (in_array($language, ['vi', 'en'])) {
            App::setLocale($language);
            Config::set('app.locale', $language);
        } else {
            App::setLocale(config('app.fallback_locale', 'vi'));
        }

        // dd(App::getLocale(), $language);

        return $next($request);
    }
}
