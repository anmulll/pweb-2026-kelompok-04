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
        Route::get('/', function () {
            return redirect()->route('seller.dashboard');
        });

        // Seller Dashboard
        Route::get('/dashboard', function () {
            return view('seller.dashboard');
        })->name('dashboard');

        // Category Management
        Route::resource('categories', SellerCategoryController::class)
            ->except(['show']);

        Route::match(
            ['post', 'patch'],
            'categories/{category}/toggle',
            [SellerCategoryController::class, 'toggle']
        )->name('categories.toggle');

        // Product Management
        Route::resource('products', SellerProductController::class)
            ->except(['show']);

        Route::match(
            ['post', 'patch'],
            'products/{product}/toggle',
            [SellerProductController::class, 'toggle']
        )->name('products.toggle');

        // Promotion Management
        Route::resource('promotions', SellerPromotionController::class)
            ->except(['show']);

        Route::match(
            ['post', 'patch'],
            'promotions/{promotion}/toggle',
            [SellerPromotionController::class, 'toggle']
        )->name('promotions.toggle');

        // Seller Orders UI
        Route::get('/orders', function () {
            return view('seller.orders.index');
        })->name('orders.index');

        Route::get('/orders/{order}', function ($order) {
            return view('seller.orders.show', ['order' => (object) [
                'id' => $order,
                'order_number' => 'ORD-' . $order,
                'status' => 'paid',
                'payment_status' => 'paid',
                'buyer_name' => 'Budi Santoso',
                'buyer_email' => 'budi@example.com',
                'buyer_phone' => '081234567890',
                'shipping_address' => "Jl. Sudirman No. 45\nJakarta Pusat, 10220",
                'courier' => null,
                'tracking_number' => null,
                'total_amount' => 16500000,
                'items' => collect([
                    (object) [
                        'product_name' => 'iPhone 15 Pro 256GB Natural Titanium',
                        'sku' => 'IP15P-256-NAT',
                        'condition' => 'used',
                        'condition_grade' => 'A',
                        'battery_health' => 95,
                        'price' => 16500000,
                        'quantity' => 1,
                        'subtotal' => 16500000,
                    ],
                ]),
                'payment' => (object) [
                    'status' => 'paid',
                    'method' => 'DOKU Checkout (QRIS / VA)',
                    'reference' => 'DOKU-PAY-' . $order,
                ],
            ]]);
        })->name('orders.show');

        Route::patch('/orders/{order}/process', function ($order) {
            return back()->with('success', 'Pesanan berhasil ditandai sedang diproses.');
        })->name('orders.process');

        Route::patch('/orders/{order}/ship', function ($order) {
            return back()->with('success', 'Pesanan berhasil ditandai dikirim beserta nomor resi.');
        })->name('orders.ship');

        Route::patch('/orders/{order}/cancel', function ($order) {
            return back()->with('success', 'Pesanan berhasil dibatalkan dan stok telah dikembalikan.');
        })->name('orders.cancel');

        Route::get('/orders/{order}/payment/status', function ($order) {
            return response()->json([
                'order_id' => $order,
                'order_status' => 'paid',
                'payment_status' => 'paid',
                'updated_at' => now()->toIso8601String(),
            ]);
        })->name('orders.payment.status');

        // Seller Payments UI
        Route::get('/payments', function () {
            return view('seller.payments.index');
        })->name('payments.index');

        Route::match(['post', 'patch'], '/payments/{payment}/sync', function ($payment) {
            return back()->with('success', 'Status pembayaran berhasil disinkronkan dengan gateway DOKU.');
        })->name('payments.sync');

        // Seller Reports UI
        Route::get('/reports', function () {
            return view('seller.reports.index');
        })->name('reports.index');

        Route::get('/reports/pdf', function () {
            return view('seller.reports.pdf');
        })->name('reports.pdf');
    });
