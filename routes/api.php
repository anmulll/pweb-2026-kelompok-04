<?php

use App\Http\Controllers\Api\DokuWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/payments/doku/notification', [DokuWebhookController::class, 'handle'])
    ->name('api.payments.doku.notification');
