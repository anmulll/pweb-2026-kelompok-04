<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | Default gateway provider and sync throttles.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'doku_sandbox'),

    'sync_throttle_seconds' => 15,
];
