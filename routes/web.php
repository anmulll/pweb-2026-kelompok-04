<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\ProfileController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Seller\CategoryController as SellerCategoryController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\PromotionController as SellerPromotionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::get('/products', function () {
    return redirect()->route('catalog.index');
})->name('products');

Route::get('/products/{id}', [CatalogController::class, 'show'])
    ->name('products.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
*/

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

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        // Buyer Orders
        Route::get('/orders', function () {
            return view('buyer.orders');
        })->name('orders');

        Route::get('/orders/{order}', function ($order) {
            return response("Detail Pesanan {$order}", 200);
        })->name('orders.show');

        // Persistent Cart
        Route::get('/cart', [CartController::class, 'index'])
            ->name('cart.index');

        Route::post('/cart', [CartController::class, 'store'])
            ->name('cart.store');

        Route::patch('/cart/{cartItem}', [CartController::class, 'update'])
            ->name('cart.update');

        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])
            ->name('cart.destroy');

        // Two-Phase Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])
            ->name('checkout.index');

        Route::post('/checkout', [CheckoutController::class, 'store'])
            ->name('checkout.store');
    });

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        // Seller Dashboard
        Route::get('/dashboard', function () {
            return response('Seller Dashboard', 200);
        })->name('dashboard');

        // Category Management
        Route::resource('categories', SellerCategoryController::class)
            ->except(['show']);

        Route::post(
            'categories/{category}/toggle',
            [SellerCategoryController::class, 'toggle']
        )->name('categories.toggle');

        // Product Management
        Route::resource('products', SellerProductController::class)
            ->except(['show']);

        Route::post(
            'products/{product}/toggle',
            [SellerProductController::class, 'toggle']
        )->name('products.toggle');

        // Promotion Management
        Route::resource('promotions', SellerPromotionController::class)
            ->except(['show']);

        Route::post(
            'promotions/{promotion}/toggle',
            [SellerPromotionController::class, 'toggle']
        )->name('promotions.toggle');
    });
