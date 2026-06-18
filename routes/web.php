<?php

use App\Http\Controllers\ProfileController;
use App\CRM\Controllers\LeadController;
use App\CRM\Controllers\TaskController;
use App\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WELCOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth'])
    ->prefix('crm')
    ->name('crm.')
    ->group(function () {


       Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('tasks.complete');

        /*
        |--------------------------------------------------------------------------
        | CRM LEADS
        |--------------------------------------------------------------------------
        */

        Route::get('/leads', [LeadController::class, 'index'])
            ->name('leads.index');

        Route::get('/leads/create', [LeadController::class, 'create'])
            ->name('leads.create');

        Route::post('/leads', [LeadController::class, 'store'])
            ->name('leads.store');

        Route::get('/leads/{lead}', [LeadController::class, 'show'])
            ->name('leads.show');

        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])
            ->name('leads.edit');

        Route::put('/leads/{lead}', [LeadController::class, 'update'])
            ->name('leads.update');

        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])
            ->name('leads.destroy');

        Route::post('/leads/{lead}/restore', [LeadController::class, 'restore'])
            ->name('leads.restore');

        Route::delete('/leads/{lead}/force-delete', [LeadController::class, 'forceDelete'])
            ->name('leads.forceDelete');

        /*
        |--------------------------------------------------------------------------
        | LEAD ACTIVITIES
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/leads/{lead}/activities',
            [LeadController::class, 'storeActivity']
        )->name('leads.activities.store');
    });

/*
|--------------------------------------------------------------------------
| AUTH PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT (ADMIN MODULE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {

    Route::get('/', [UserController::class, 'index'])
        ->name('index');

    Route::get('/create', [UserController::class, 'create'])
        ->name('create');

    Route::post('/', [UserController::class, 'store'])
        ->name('store');

    Route::get('/{user}', [UserController::class, 'show'])
        ->name('show');

    Route::get('/{user}/edit', [UserController::class, 'edit'])
        ->name('edit');

    Route::put('/{user}', [UserController::class, 'update'])
        ->name('update');

    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->name('destroy');

    Route::post('/{user}/restore', [UserController::class, 'restore'])
        ->name('restore');

    Route::delete('/{user}/force-delete', [UserController::class, 'forceDelete'])
        ->name('forceDelete');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';