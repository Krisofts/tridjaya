<?php

use App\CRM\Controllers\LeadController;
use App\CRM\Controllers\LeadDetailController;
use App\CRM\Controllers\MyLeadController;
use App\CRM\Controllers\NotificationController;
use App\CRM\Controllers\TaskController;
use App\CRM\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'canAccess:crm.access'])
    ->prefix('crm')
    ->name('crm.')
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | TRANSACTIONS
        |----------------------------------------------------------------------
        */

        Route::post('leads/{lead}/transactions',          [\App\CRM\Controllers\TransactionController::class, 'store'])->name('transactions.store');
        Route::patch('transactions/{transaction}/status', [\App\CRM\Controllers\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::delete('transactions/{transaction}',       [\App\CRM\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');

        /*
        |----------------------------------------------------------------------
        | LAPORAN
        |----------------------------------------------------------------------
        */

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('leads',        [\App\CRM\Controllers\LeadReportController::class, 'index'])->name('leads');
            Route::get('leads/export', [\App\CRM\Controllers\LeadReportController::class, 'export'])->name('leads.export');
        });

        /*
        |----------------------------------------------------------------------
        | OPERASIONAL (per user login)
        |----------------------------------------------------------------------
        */

        Route::get('my-leads',          [MyLeadController::class, 'index'])->name('my-leads.index');
        Route::get('my-leads/{lead}',   [MyLeadController::class, 'show'])->name('my-leads.show');
        Route::get('my-tasks',          [MyLeadController::class, 'tasks'])->name('my-tasks.index');

        /*
        |----------------------------------------------------------------------
        | LEADS
        |----------------------------------------------------------------------
        */

        Route::prefix('leads')->name('leads.')->group(function () {

            Route::get('/',              [LeadController::class, 'index'])->name('index');
            Route::get('/create',        [LeadController::class, 'create'])->name('create');
            Route::post('/',             [LeadController::class, 'store'])->name('store');
            Route::get('/{lead}/edit',   [LeadController::class, 'edit'])->name('edit');
            Route::put('/{lead}',        [LeadController::class, 'update'])->name('update');
            Route::delete('/{lead}',     [LeadController::class, 'destroy'])->name('destroy');
            Route::post('/{lead}/restore',       [LeadController::class, 'restore'])->name('restore');
            Route::delete('/{lead}/force-delete',[LeadController::class, 'forceDelete'])->name('forceDelete');

            // Lead Detail
            Route::get('/{lead}',               [LeadDetailController::class, 'show'])->name('show');
            Route::post('/{lead}/change-stage', [LeadDetailController::class, 'changeStage'])->name('change-stage');
            Route::post('/{lead}/whatsapp',     [LeadDetailController::class, 'whatsapp'])->name('whatsapp');

            // Tasks (dari lead detail)
            Route::post('/tasks',        [LeadDetailController::class, 'storeTask'])->name('tasks.store');

        });

        /*
        |----------------------------------------------------------------------
        | TASKS
        |----------------------------------------------------------------------
        */

        Route::prefix('tasks')->name('tasks.')->group(function () {

            Route::patch('/{task}/start',    [TaskController::class, 'start'])->name('start');
            Route::patch('/{task}/complete', [TaskController::class, 'complete'])->name('complete');
            Route::patch('/{task}/cancel',   [TaskController::class, 'cancel'])->name('cancel');

        });

        /*
        |----------------------------------------------------------------------
        | REGION (AJAX Dropdown Wilayah)
        |----------------------------------------------------------------------
        */

        Route::prefix('regions')->name('regions.')->group(function () {

            Route::get('/cities/{provinceCode}',   [RegionController::class, 'cities'])->name('cities')->where('provinceCode', '[0-9.]+');
            Route::get('/districts/{cityCode}',    [RegionController::class, 'districts'])->name('districts')->where('cityCode', '[0-9.]+');
            Route::get('/villages/{districtCode}', [RegionController::class, 'villages'])->name('villages')->where('districtCode', '[0-9.]+');

        });

    });