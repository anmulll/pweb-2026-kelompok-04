
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Buyer\ProfileController;
use Illuminate\Support\Facades\Route;


// Public Storefront
Route::get('/', function () {
    return view('public.home');
})->name('home');

Route::get('/products', function () {
    return view('public.catalog');
})->name('products');

Route::get('/products/{id}', function ($id) {
    return view('public.product-detail');
})->name('products.show');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Buyer Dashboard
Route::prefix('buyer')
    ->name('buyer.')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('buyer.dashboard');
        })->name('dashboard');

        // Buyer Profile
        Route::get('/profile', function () {
            return view('buyer.profile');
        })->name('profile');

        // Update Buyer Profile
        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::get('/orders', function () {
             return view('buyer.orders');
        })->name('orders');

    });

// Seller Dashboard
Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return response('Seller Dashboard', 200);
        })->name('dashboard');
    });


