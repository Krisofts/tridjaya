<?php

use Illuminate\Support\Facades\Route;
use App\User\Controllers\UserController;
use App\User\Controllers\ProfileController;
use App\Product\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |---------------------------------------------------
    | DASHBOARD
    |---------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
 
    /*
    |---------------------------------------------------
    | USERS MODULE
    |---------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // update profile
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    /*
    |---------------------------------------------------
    | PRODUCTS MODULE
    |---------------------------------------------------
    */
    Route::resource('products', ProductController::class);

});



require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| CRM ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__ . '/crm.php';