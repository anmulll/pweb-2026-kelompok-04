<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Buyer\CartController as BuyerCartController;
use App\Http\Controllers\Buyer\CheckoutController as BuyerCheckoutController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Buyer\PaymentController as BuyerPaymentController;
use App\Http\Controllers\Buyer\ProfileController as BuyerProfileController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Seller\CategoryController as SellerCategoryController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\PaymentController as SellerPaymentController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\PromotionController as SellerPromotionController;
use App\Http\Controllers\Seller\ReportController as SellerReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Public Storefront Routes
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
| 2. Authentication Routes
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
| 3. Buyer Routes (RBAC Protected: role:buyer)
|--------------------------------------------------------------------------
*/

Route::prefix('buyer')
    ->name('buyer.')
    ->middleware(['auth', 'role:buyer'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('buyer.dashboard');
        })->name('dashboard');

        // Profile
        Route::get('/profile', [BuyerProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [BuyerProfileController::class, 'update'])->name('profile.update');

        // Persistent Cart
        Route::get('/cart', [BuyerCartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [BuyerCartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cartItem}', [BuyerCartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [BuyerCartController::class, 'destroy'])->name('cart.destroy');

        // Two-Phase Checkout
        Route::get('/checkout', [BuyerCheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [BuyerCheckoutController::class, 'store'])->name('checkout.store');

        // Orders & Cancellation & Confirmation
        Route::get('/orders', [BuyerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [BuyerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [BuyerOrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/confirm-receipt', [BuyerOrderController::class, 'confirmReceipt'])->name('orders.confirm-receipt');

        // Payment Polling & Actions
        Route::get('/orders/{order}/payment/status', [BuyerPaymentController::class, 'status'])
            ->middleware('throttle:30,1')
            ->name('orders.payment.status');
        Route::post('/orders/{order}/payment/pay', [BuyerPaymentController::class, 'pay'])->name('orders.payment.pay');
        Route::post('/orders/{order}/payment/sync', [BuyerPaymentController::class, 'sync'])->name('orders.payment.sync');
    });

/*
|--------------------------------------------------------------------------
| 4. Seller Routes (RBAC Protected: role:seller)
|--------------------------------------------------------------------------
*/

Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('seller.dashboard');
        });

        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

        // Categories CRUD & toggle
        Route::resource('categories', SellerCategoryController::class)->except(['show']);
        Route::match(['post', 'patch'], 'categories/{category}/toggle', [SellerCategoryController::class, 'toggle'])->name('categories.toggle');

        // Products CRUD & toggle
        Route::resource('products', SellerProductController::class)->except(['show']);
        Route::match(['post', 'patch'], 'products/{product}/toggle', [SellerProductController::class, 'toggle'])->name('products.toggle');

        // Promotions CRUD & toggle
        Route::resource('promotions', SellerPromotionController::class)->except(['show']);
        Route::match(['post', 'patch'], 'promotions/{promotion}/toggle', [SellerPromotionController::class, 'toggle'])->name('promotions.toggle');

        // Orders Management
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::match(['post', 'patch'], '/orders/{order}/process', [SellerOrderController::class, 'process'])->name('orders.process');
        Route::match(['post', 'patch'], '/orders/{order}/ship', [SellerOrderController::class, 'ship'])->name('orders.ship');
        Route::match(['post', 'patch'], '/orders/{order}/cancel', [SellerOrderController::class, 'cancel'])->name('orders.cancel');

        // Payments (Read-Only & Sync & Status)
        Route::get('/payments', [SellerPaymentController::class, 'index'])->name('payments.index');
        Route::match(['post', 'patch'], '/payments/{order}/sync', [SellerPaymentController::class, 'sync'])->name('payments.sync');
        Route::get('/orders/{order}/payment/status', [SellerPaymentController::class, 'status'])
            ->middleware('throttle:30,1')
            ->name('orders.payment.status');

        // Reports & PDF
        Route::get('/reports', [SellerReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [SellerReportController::class, 'pdf'])->name('reports.pdf');
    });
