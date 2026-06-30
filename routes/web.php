<?php

use App\Dashboard\Sales\Controllers\SalesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Sales\Controllers\SalesPerformanceController;
use App\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WELCOME
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        Route::get('/', fn () => view('dashboard'))
            ->name('home');

        Route::prefix('sales')->group(function () {
            Route::get('/', [SalesController::class, 'index'])
                ->name('sales');
        });

    });

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get('/',    [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',  [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/',                        [UserController::class, 'index'])->name('index');
        Route::get('/create',                  [UserController::class, 'create'])->name('create');
        Route::post('/',                       [UserController::class, 'store'])->name('store');
        Route::get('/{user}',                  [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit',             [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',                  [UserController::class, 'update'])->name('update');
        Route::delete('/{user}',               [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/restore',         [UserController::class, 'restore'])->name('restore');
        Route::delete('/{user}/force-delete',  [UserController::class, 'forceDelete'])->name('forceDelete');
    });

/*
|--------------------------------------------------------------------------
| NOTIFICATIONS (global — semua modul)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/',           [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all',  [NotificationController::class, 'markAllRead'])->name('read-all');
    });

/*
|--------------------------------------------------------------------------
| SALES PERFORMANCE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('sales')
    ->name('sales.')
    ->group(function () {
        Route::get('performance',        [SalesPerformanceController::class, 'index'])->name('performance');
        Route::get('performance/{slug}', [SalesPerformanceController::class, 'show'])->name('performance.show');
        Route::get('analytics',          [\App\Sales\Controllers\SalesAnalyticsController::class, 'index'])->name('analytics');
    });

/*
|--------------------------------------------------------------------------
| EXTERNAL ROUTE FILES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/crm.php';
require __DIR__ . '/ecommerce.php';