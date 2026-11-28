<?php

return [
    'client_id' => env('DOKU_CLIENT_ID', ''),
    'shared_key' => env('DOKU_SHARED_KEY', env('DOKU_SECRET_KEY', '')),
    'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),
    'is_production' => filter_var(env('DOKU_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
];
