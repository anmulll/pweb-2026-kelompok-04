<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/seller', function () {
    return view('seller.dashboard');
})->name('seller.dashboard');

// Kategori
Route::get('/seller/categories', function () {
    return view('seller.categories.index');
})->name('seller.categories.index');

Route::get('/seller/categories/create', function () {
    return view('seller.categories.form');
})->name('seller.categories.create');

// Produk
Route::get('/seller/products', function () {
    return view('seller.products.index');
})->name('seller.products.index');

Route::get('/seller/products/create', function () {
    return view('seller.products.form');
})->name('seller.products.create');

// Promosi
Route::get('/seller/promotions', function () {
    return view('seller.promotions.index');
})->name('seller.promotions.index');

Route::get('/seller/promotions/create', function () {
    return view('seller.promotions.form');
})->name('seller.promotions.create');

// Pesanan
Route::get('/seller/orders', function () {
    return view('seller.orders.index');
})->name('seller.orders.index');

Route::get('/seller/orders/{id}', function ($id) {
    return view('seller.orders.show');
})->name('seller.orders.show');

// Pembayaran
Route::get('/seller/payments', function () {
    return view('seller.payments.index');
})->name('seller.payments.index');

// Laporan
Route::get('/seller/reports', function () {
    return view('seller.reports.index');
})->name('seller.reports.index');