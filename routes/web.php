<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

 /*
        |------------------------------------------
        | USERS
        |------------------------------------------
        */
        Route::resource('users', UserController::class);


require __DIR__.'/auth.php';
require __DIR__.'/crm.php';   