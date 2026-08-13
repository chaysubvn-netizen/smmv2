<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    // Include sanctum if you use cookie-based auth
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'], 

    'allowed_methods' => ['*'],

    // Explicitly add your Next.js local development and production URLs
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'http://127.0.0.1:3000',
    ],

    'allowed_origins_patterns' => array_values(array_filter(explode(',', (string) env('CORS_ALLOWED_ORIGIN_PATTERNS', '')))),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // CRITICAL: Must be true if Next.js passes cookies/sessions (e.g., Laravel Sanctum)
    'supports_credentials' => true,

];
