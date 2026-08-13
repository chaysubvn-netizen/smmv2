<?php

return [
    'version' => '1.0.0',
    'server_url' => env('UPDATE_SERVER_URL', 'https://api.cmsbvq.com/update.php'),
    'next_path' => env('NEXTJS_PATH', dirname(base_path())),
    'next_restart_command' => env('NEXTJS_RESTART_COMMAND'),
    'composer_binary' => env('COMPOSER_BINARY', 'composer'),
    'npm_binary' => env('NPM_BINARY', 'npm'),
    'keep_backups' => (int) env('UPDATE_KEEP_BACKUPS', 3),
    'exclude' => [
        '.env',
        'storage/*',
        'public/uploads/*',
        'config/updater.php',
    ],
    'next_exclude' => [
        '.env',
        '.env.*',
        'node_modules/*',
        '.next/*',
        '.git/*',
        'backend/*',
    ],
];
