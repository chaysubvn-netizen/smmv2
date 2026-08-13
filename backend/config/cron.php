<?php

return [
    'secret' => env('CRON_SECRET', ''),
    'lock_seconds' => (int) env('CRON_LOCK_SECONDS', 1800),
    'stale_seconds' => (int) env('CRON_STALE_SECONDS', 300),
];
