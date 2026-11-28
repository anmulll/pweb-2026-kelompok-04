<?php

use App\Http\Controllers\Api\DokuNotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/doku/notification', [DokuNotificationController::class, 'handle'])
    ->name('api.payments.doku.notification');
