
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Buyer
Route::prefix('buyer')
    ->name('buyer.')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return response('Buyer Dashboard', 200);
        })->name('dashboard');
    });

// Seller
Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('dashboard');

        Route::get('/', function () {
            return redirect()->route('seller.dashboard');
        });

        // Kategori
        Route::get('/categories', function () {
            return view('seller.categories.index');
        })->name('categories.index');

        Route::get('/categories/create', function () {
            return view('seller.categories.form');
        })->name('categories.create');

        // Produk
        Route::get('/products', function () {
            return view('seller.products.index');
        })->name('products.index');

        Route::get('/products/create', function () {
            return view('seller.products.form');
        })->name('products.create');

        // Promosi
        Route::get('/promotions', function () {
            return view('seller.promotions.index');
        })->name('promotions.index');

        Route::get('/promotions/create', function () {
            return view('seller.promotions.form');
        })->name('promotions.create');

        // Pesanan
        Route::get('/orders', function () {
            return view('seller.orders.index');
        })->name('orders.index');

        Route::get('/orders/{id}', function ($id) {
            return view('seller.orders.show');
        })->name('orders.show');

        // Pembayaran
        Route::get('/payments', function () {
            return view('seller.payments.index');
        })->name('payments.index');

        // Laporan
        Route::get('/reports', function () {
            return view('seller.reports.index');
        })->name('reports.index');

    });
