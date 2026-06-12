<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\LeadReminderController;
use App\Http\Controllers\CRM\LeadTransactionController;
use App\Http\Controllers\CRM\CustomerController;
use App\Http\Controllers\CRM\LeadTaskController;
use App\Http\Controllers\CRM\ReportController;

Route::middleware('rbac:group:admin|group:superadmin')
    ->prefix('crm')
    ->name('crm.')
    ->group(function () { 

        /*
        |------------------------------------------
        | LEADS
        |------------------------------------------
        */
        Route::resource('leads', LeadController::class);

        Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus'])
            ->name('leads.status');

        /*
        |------------------------------------------
        | TASKS
        |------------------------------------------
        */
        Route::prefix('tasks')
            ->name('tasks.')
            ->group(function () {

                Route::get('/', [LeadTaskController::class, 'index'])->name('index');
                Route::get('/create', [LeadTaskController::class, 'create'])->name('create');
                Route::post('/', [LeadTaskController::class, 'store'])->name('store');

                Route::get('/{task}', [LeadTaskController::class, 'show'])->name('show');
                Route::get('/{task}/edit', [LeadTaskController::class, 'edit'])->name('edit');
                Route::put('/{task}', [LeadTaskController::class, 'update'])->name('update');

                Route::post('/{task}/start', [LeadTaskController::class, 'start'])->name('start');
                Route::post('/{task}/complete', [LeadTaskController::class, 'complete'])->name('complete');
                Route::post('/{task}/cancel', [LeadTaskController::class, 'cancel'])->name('cancel');
                Route::post('/{task}/assign', [LeadTaskController::class, 'assign'])->name('assign');
                Route::post('/{task}/reopen', [LeadTaskController::class, 'reopen'])->name('reopen');

                Route::delete('/{task}', [LeadTaskController::class, 'destroy'])->name('destroy');
            });

        /*
        |------------------------------------------
        | REPORTS
        |------------------------------------------
        */
        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {

                Route::get('/users', [ReportController::class, 'users'])->name('users');

                Route::get('/users/export', [ReportController::class, 'exportUserReport'])
                    ->name('users.export');
            });

        /*
        |------------------------------------------
        | LEADS NESTED
        |------------------------------------------
        */
        Route::prefix('leads/{lead}')->group(function () {

            Route::prefix('reminders')
                ->name('leads.reminders.')
                ->group(function () {

                    Route::get('/create', [LeadReminderController::class, 'create'])->name('create');
                    Route::post('/', [LeadReminderController::class, 'store'])->name('store');
                });

            Route::prefix('transactions')
                ->name('leads.transactions.')
                ->group(function () {

                    Route::get('/create', [LeadTransactionController::class, 'create'])->name('create');
                    Route::post('/', [LeadTransactionController::class, 'store'])->name('store');
                });
        });

        /*
        |------------------------------------------
        | REMINDERS GLOBAL
        |------------------------------------------
        */
        Route::prefix('reminders')
            ->name('reminders.')
            ->group(function () {

                Route::get('/', [LeadReminderController::class, 'index'])->name('index');
                Route::get('/{reminder}', [LeadReminderController::class, 'show'])->name('show');
                Route::get('/{reminder}/edit', [LeadReminderController::class, 'edit'])->name('edit');

                Route::put('/{reminder}', [LeadReminderController::class, 'update'])->name('update');
                Route::delete('/{reminder}', [LeadReminderController::class, 'destroy'])->name('destroy');

                Route::patch('/{reminder}/done')->name('done');
                Route::patch('/{reminder}/cancel')->name('cancel');
                Route::patch('/{reminder}/reopen')->name('reopen');

                Route::patch('/{reminder}/assign')->name('assign');
                Route::patch('/{reminder}/unassign')->name('unassign');
            });

        /*
        |------------------------------------------
        | TRANSACTIONS GLOBAL
        |------------------------------------------
        */
        Route::prefix('transactions')
            ->name('transactions.')
            ->group(function () {

                Route::get('/', [LeadTransactionController::class, 'index'])->name('index');
                Route::get('/{transaction}', [LeadTransactionController::class, 'show'])->name('show');
                Route::get('/{transaction}/edit', [LeadTransactionController::class, 'edit'])->name('edit');

                Route::put('/{transaction}', [LeadTransactionController::class, 'update'])->name('update');
                Route::delete('/{transaction}', [LeadTransactionController::class, 'destroy'])->name('destroy');

                Route::patch('/{transaction}/approve')->name('approve');
                Route::patch('/{transaction}/reject')->name('reject');
            });

        /*
        |------------------------------------------
        | CUSTOMERS
        |------------------------------------------
        */
        Route::resource('customers', CustomerController::class);

        Route::prefix('customers')
            ->name('customers.')
            ->group(function () {

                Route::get('/{customer}/transactions', [CustomerController::class, 'transactions'])
                    ->name('transactions');

                Route::patch('/{customer}/status', [CustomerController::class, 'updateStatus'])
                    ->name('status');
            });
    });