<?php

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
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{id}', [CatalogController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Authentication Fallback Route (untuk redirect middleware guest)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return response('Silakan masuk ke akun Anda.', 200);
})->name('login');

/*
|--------------------------------------------------------------------------
| Seller Master Data & Catalog Routes (RBAC Protected: role:seller)
|--------------------------------------------------------------------------
*/
Route::prefix('seller')->name('seller.')->middleware(['auth', 'role:seller'])->group(function () {
    // Categories
    Route::resource('categories', SellerCategoryController::class)->except(['show']);
    Route::post('categories/{category}/toggle', [SellerCategoryController::class, 'toggle'])->name('categories.toggle');

    // Products
    Route::resource('products', SellerProductController::class)->except(['show']);
    Route::post('products/{product}/toggle', [SellerProductController::class, 'toggle'])->name('products.toggle');

    // Promotions
    Route::resource('promotions', SellerPromotionController::class)->except(['show']);
    Route::post('promotions/{promotion}/toggle', [SellerPromotionController::class, 'toggle'])->name('promotions.toggle');
});
