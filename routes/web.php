<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('buyer')
    ->name('buyer.')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return response('Buyer Dashboard', 200);
        })->name('dashboard');

        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

        // Checkout routes
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        // Placeholder for Order & Payment routes
        Route::get('/orders/{order}', function ($order) {
            return response("Detail Pesanan {$order}", 200);
        })->name('orders.show');
    });

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return response('Seller Dashboard', 200);
        })->name('dashboard');
    });
