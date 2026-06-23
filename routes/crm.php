<?php

use Illuminate\Support\Facades\Route;

use App\CRM\Controllers\LeadController;
use App\CRM\Controllers\LeadDetailController;
use App\CRM\Controllers\TaskController;
use App\Services\RegionService;

Route::middleware(['auth', 'canAccess:crm.access'])
->prefix('crm')
->name('crm.')
->group(function () {
    /*
        |--------------------------------------------------------------------------
        | TASK
        |--------------------------------------------------------------------------
        */

    // create task (dari lead detail modal)
    Route::post('/leads/tasks', [LeadDetailController::class, 'storeTask'])
    ->name('leads.tasks.store');

    // start task
    Route::patch('/tasks/{task}/start', [TaskController::class, 'start'])
    ->name('tasks.start');

    // complete task
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])
    ->name('tasks.complete');

    // cancel task (optional tapi bagus ada)
    Route::patch('/tasks/{task}/cancel', [TaskController::class, 'cancel'])
    ->name('tasks.cancel');
    
    
    

/*
|--------------------------------------------------------------------------
| REGION (AJAX DROPDOWN)
|--------------------------------------------------------------------------
*/

Route::get('/regions/cities/{provinceCode}', function (
    string $provinceCode,
    RegionService $region
) {
    return response()->json(
        $region->regencies($provinceCode)
    );
})->name('regions.cities');

Route::get('/regions/districts/{cityCode}', function (
    string $cityCode,
    RegionService $region
) {
    return response()->json(
        $region->districts($cityCode)
    );
})->name('regions.districts');

    /*
        |--------------------------------------------------------------------------
        | LEADS (CRUD)
        |--------------------------------------------------------------------------
        */

    Route::get('/leads', [LeadController::class, 'index'])
    ->name('leads.index');

    Route::get('/leads/create', [LeadController::class, 'create'])
    ->name('leads.create');

    Route::post('/leads', [LeadController::class, 'store'])
    ->name('leads.store');

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
        | LEAD DETAIL
        |--------------------------------------------------------------------------
        */

    Route::get('/leads/{id}', [LeadDetailController::class, 'show'])
    ->name('leads.show');


    Route::post('/leads/{lead}/change-stage', [LeadDetailController::class, 'changeStage'])
    ->name('leads.change-stage');

    /*
        |--------------------------------------------------------------------------
        | WHATSAPP ACTION
        |--------------------------------------------------------------------------
        */

    Route::post('/leads/{lead}/whatsapp', [LeadDetailController::class, 'whatsapp'])
    ->name('leads.whatsapp');
});