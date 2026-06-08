<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\LeadReminderController;
use App\Http\Controllers\CRM\TaskController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SYSTEM
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | CRM DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/crm/dashboard', [
        \App\Http\Controllers\CRM\DashboardController::class,
        'index'
    ])->name('crm.dashboard');

    /*
    |--------------------------------------------------------------------------
    | REPORT MODULE (NEW)
    |--------------------------------------------------------------------------
    | NOTE: tidak perlu group admin dulu biar bisa diakses internal user report
    |--------------------------------------------------------------------------
    */
    Route::prefix('crm/reports')
        ->name('crm.reports.')
        ->group(function () {

            Route::get('/users', [
                \App\Http\Controllers\CRM\ReportController::class,
                'users'
            ])->name('users');

            Route::get('/users/export', [
                \App\Http\Controllers\CRM\ReportController::class,
                'exportUserReport'
            ])->name('users.export');
        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN / SUPERADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::middleware(['group:admin,superadmin'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Route::resource('users', UserController::class);

        /*
        |--------------------------------------------------------------------------
        | CRM MODULE
        |--------------------------------------------------------------------------
        */
        Route::prefix('crm')->name('crm.')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | LEADS
            |--------------------------------------------------------------------------
            */
            Route::resource('leads', LeadController::class);

            Route::patch('/leads/{lead}/status', [
                LeadController::class,
                'updateStatus'
            ])->name('leads.status');

            /*
            |--------------------------------------------------------------------------
            | TASKS
            |--------------------------------------------------------------------------
            */
            Route::resource('tasks', TaskController::class)->except(['show']);

            Route::patch('/tasks/{task}/status', [
                TaskController::class,
                'updateStatus'
            ])->name('tasks.status');

            /*
            |--------------------------------------------------------------------------
            | LEAD REMINDERS
            |--------------------------------------------------------------------------
            */
            Route::prefix('leads/{lead}')
                ->name('leads.')
                ->group(function () {

                    Route::get('/reminders/create', [
                        LeadReminderController::class,
                        'create'
                    ])->name('reminders.create');

                    Route::post('/reminders', [
                        LeadReminderController::class,
                        'store'
                    ])->name('reminders.store');

                    /*
                    |--------------------------------------------------------------------------
                    | LEAD TRANSACTIONS
                    |--------------------------------------------------------------------------
                    */
                    Route::get('/transactions/create', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'create'
                    ])->name('transactions.create');

                    Route::post('/transactions', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'store'
                    ])->name('transactions.store');
                });

            /*
            |--------------------------------------------------------------------------
            | REMINDERS MANAGEMENT
            |--------------------------------------------------------------------------
            */
            Route::prefix('reminders')
                ->name('reminders.')
                ->group(function () {

                    Route::get('/', [LeadReminderController::class, 'index'])->name('index');
                    Route::get('/{reminder}', [LeadReminderController::class, 'show'])->name('show');
                    Route::get('/{reminder}/edit', [LeadReminderController::class, 'edit'])->name('edit');

                    Route::put('/{reminder}', [LeadReminderController::class, 'update'])->name('update');
                    Route::delete('/{reminder}', [LeadReminderController::class, 'destroy'])->name('destroy');

                    Route::patch('/{reminder}/done', [LeadReminderController::class, 'done'])->name('done');
                    Route::patch('/{reminder}/cancel', [LeadReminderController::class, 'cancel'])->name('cancel');
                    Route::patch('/{reminder}/reopen', [LeadReminderController::class, 'reopen'])->name('reopen');

                    Route::patch('/{reminder}/assign', [LeadReminderController::class, 'assign'])->name('assign');
                    Route::patch('/{reminder}/unassign', [LeadReminderController::class, 'unassign'])->name('unassign');
                });

            /*
            |--------------------------------------------------------------------------
            | TRANSACTIONS (GLOBAL)
            |--------------------------------------------------------------------------
            */
            Route::prefix('transactions')
                ->name('transactions.')
                ->group(function () {

                    Route::get('/', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'index'
                    ])->name('index');

                    Route::get('/{transaction}', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'show'
                    ])->name('show');

                    Route::get('/{transaction}/edit', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'edit'
                    ])->name('edit');

                    Route::put('/{transaction}', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'update'
                    ])->name('update');

                    Route::delete('/{transaction}', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'destroy'
                    ])->name('destroy');

                    Route::patch('/{transaction}/approve', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'approve'
                    ])->name('approve');

                    Route::patch('/{transaction}/reject', [
                        \App\Http\Controllers\CRM\LeadTransactionController::class,
                        'reject'
                    ])->name('reject');
                });
 
            /*
            |--------------------------------------------------------------------------
            | CUSTOMERS (CORE CRM)
            |--------------------------------------------------------------------------
            */
            Route::resource('customers', \App\Http\Controllers\CRM\CustomerController::class);

            Route::prefix('customers')->name('customers.')->group(function () {

                Route::get('/{customer}/transactions', [
                    \App\Http\Controllers\CRM\CustomerController::class,
                    'transactions'
                ])->name('transactions');

                Route::patch('/{customer}/status', [
                    \App\Http\Controllers\CRM\CustomerController::class,
                    'updateStatus'
                ])->name('status');
            });

        });
    });
});

/*
|--------------------------------------------------------------------------
| AUTH (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';