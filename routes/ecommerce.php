<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ECOMMERCE — ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'canAccess:ecommerce.access'])
    ->prefix('ecommerce')
    ->name('ecommerce.')
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('ecommerce.admin.dashboard');
        })->name('dashboard');

        // Produk
        Route::get('products', function () {
            return view('ecommerce.admin.products.index');
        })->name('products.index');

        // Kategori
        Route::get('categories', function () {
            return view('ecommerce.admin.categories.index');
        })->name('categories.index');

        // Order
        Route::get('orders', function () {
            return view('ecommerce.admin.orders.index');
        })->name('orders.index');

        // Customer
        Route::get('customers', function () {
            return view('ecommerce.admin.customers.index');
        })->name('customers.index');

        // Laporan
        Route::get('reports', function () {
            return view('ecommerce.admin.reports.index');
        })->name('reports.index');

    });

/*
|--------------------------------------------------------------------------
| ECOMMERCE — PUBLIC STORE
|--------------------------------------------------------------------------
*/

Route::prefix('store')
    ->name('store.')
    ->group(function () {

        // Home
        Route::get('/', function () {
            return view('ecommerce.store.home');
        })->name('home');

        // Produk
        Route::get('products', function () {
            return view('ecommerce.store.products.index');
        })->name('products.index');

        Route::get('products/{slug}', function ($slug) {
            return view('ecommerce.store.products.show');
        })->name('products.show');

        // Cart
        Route::get('cart', function () {
            return view('ecommerce.store.cart');
        })->name('cart.index');

        // Auth
        Route::get('login',    function () { return view('ecommerce.store.auth.login'); })->name('login');
        Route::get('register', function () { return view('ecommerce.store.auth.register'); })->name('register');

    });