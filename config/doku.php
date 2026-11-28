<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DOKU Checkout Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for DOKU Checkout Sandbox. Production mode is strictly
    | disabled in iStore v3.1 per ADR-01 and FR-PAY-01.
    |
    */

    'client_id' => env('DOKU_CLIENT_ID', 'MALLID_SANDBOX_TEST'),
    'shared_key' => env('DOKU_SHARED_KEY', 'SHARED_KEY_SANDBOX_TEST'),
    'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),
    'is_production' => filter_var(env('DOKU_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
    'notification_path' => env('DOKU_NOTIFICATION_PATH', '/api/payments/doku/notification'),
];
