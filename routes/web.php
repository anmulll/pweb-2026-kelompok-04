<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home');
});

Route::get('/products', function () {
    return view('public.catalog');
});

Route::get('/products/{id}', function ($id) {
    return view('public.product-detail');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});