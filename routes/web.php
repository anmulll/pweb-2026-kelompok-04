
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

        Route::post('/categories', function () {
            return redirect()->route('seller.categories.index')->with('success', 'Kategori berhasil disimpan.');
        })->name('categories.store');

        Route::get('/categories/{category}/edit', function ($category) {
            return view('seller.categories.form', ['category' => (object) ['id' => $category, 'name' => 'Kategori Contoh', 'slug' => 'kategori-contoh', 'description' => '', 'is_active' => true]]);
        })->name('categories.edit');

        Route::put('/categories/{category}', function ($category) {
            return redirect()->route('seller.categories.index')->with('success', 'Kategori berhasil diperbarui.');
        })->name('categories.update');

        Route::delete('/categories/{category}', function ($category) {
            return redirect()->route('seller.categories.index')->with('success', 'Kategori berhasil dihapus.');
        })->name('categories.destroy');

        Route::patch('/categories/{category}/toggle', function ($category) {
            return redirect()->route('seller.categories.index')->with('success', 'Status kategori berhasil diubah.');
        })->name('categories.toggle');

        // Produk
        Route::get('/products', function () {
            return view('seller.products.index');
        })->name('products.index');

        Route::get('/products/create', function () {
            return view('seller.products.form');
        })->name('products.create');

        Route::post('/products', function () {
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan.');
        })->name('products.store');

        Route::get('/products/{product}/edit', function ($product) {
            return view('seller.products.form', ['product' => (object) ['id' => $product, 'name' => 'iPhone 15 Pro 256GB', 'sku' => 'IP15P-256', 'model' => 'A3102', 'condition' => 'used', 'condition_grade' => 'A', 'battery_health' => 95, 'functional_status' => 'normal', 'defect_notes' => 'Mulus tanpa goresan', 'accessories' => 'Fullset Box Original', 'warranty_info' => 'Garansi Toko 30 Hari', 'price' => 16500000, 'stock' => 1, 'is_active' => true]]);
        })->name('products.edit');

        Route::put('/products/{product}', function ($product) {
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui.');
        })->name('products.update');

        Route::delete('/products/{product}', function ($product) {
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus.');
        })->name('products.destroy');

        Route::patch('/products/{product}/toggle', function ($product) {
            return redirect()->route('seller.products.index')->with('success', 'Status produk berhasil diubah.');
        })->name('products.toggle');

        // Promosi
        Route::get('/promotions', function () {
            return view('seller.promotions.index');
        })->name('promotions.index');

        Route::get('/promotions/create', function () {
            return view('seller.promotions.form');
        })->name('promotions.create');

        Route::post('/promotions', function () {
            return redirect()->route('seller.promotions.index')->with('success', 'Promosi berhasil dibuat.');
        })->name('promotions.store');

        Route::get('/promotions/{promotion}/edit', function ($promotion) {
            return view('seller.promotions.form', ['promotion' => (object) ['id' => $promotion, 'name' => 'Promo Spesial Apple', 'slug' => 'promo-spesial-apple', 'start_date' => now()->toDateString(), 'end_date' => now()->addDays(7)->toDateString(), 'description' => 'Diskon perangkat Apple pilihan.', 'is_active' => true]]);
        })->name('promotions.edit');

        Route::put('/promotions/{promotion}', function ($promotion) {
            return redirect()->route('seller.promotions.index')->with('success', 'Promosi berhasil diperbarui.');
        })->name('promotions.update');

        Route::delete('/promotions/{promotion}', function ($promotion) {
            return redirect()->route('seller.promotions.index')->with('success', 'Promosi berhasil dihapus.');
        })->name('promotions.destroy');

        Route::patch('/promotions/{promotion}/toggle', function ($promotion) {
            return redirect()->route('seller.promotions.index')->with('success', 'Status promosi berhasil diubah.');
        })->name('promotions.toggle');

        // Pesanan
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
                    ]
                ]),
                'payment' => (object) [
                    'status' => 'paid',
                    'method' => 'DOKU Checkout (QRIS / VA)',
                    'reference' => 'DOKU-PAY-' . $order,
                ]
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

        // Pembayaran
        Route::get('/payments', function () {
            return view('seller.payments.index');
        })->name('payments.index');

        Route::match(['post', 'patch'], '/payments/{payment}/sync', function ($payment) {
            return back()->with('success', 'Status pembayaran berhasil disinkronkan dengan gateway DOKU.');
        })->name('payments.sync');

        // Laporan
        Route::get('/reports', function () {
            return view('seller.reports.index');
        })->name('reports.index');

        Route::get('/reports/pdf', function () {
            return view('seller.reports.pdf');
        })->name('reports.pdf');

    });
