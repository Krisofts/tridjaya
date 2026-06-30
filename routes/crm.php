<?php

use App\CRM\Controllers\ActivityController;
use App\CRM\Controllers\DashboardController;
use App\CRM\Controllers\LeadController;
use App\CRM\Controllers\LostReasonController;
use App\CRM\Controllers\NotificationController;
use App\CRM\Controllers\ReportController;
use App\CRM\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('crm')->name('crm.')->middleware(['auth'])->group(function () {

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // -------------------------------------------------------------------------
    // Lost Reasons — Master Data
    // -------------------------------------------------------------------------
    Route::resource('lost-reasons', LostReasonController::class)
        ->except(['show']);

    Route::patch('lost-reasons/{lostReason}/toggle-active', [LostReasonController::class, 'toggleActive'])
        ->name('lost-reasons.toggle-active');

    // -------------------------------------------------------------------------
    // Leads — CRUD + lifecycle
    // -------------------------------------------------------------------------
    Route::resource('leads', LeadController::class);

    Route::patch('leads/{lead}/move-stage', [LeadController::class, 'moveStage'])
        ->name('leads.moveStage');

    Route::patch('leads/{lead}/won',    [LeadController::class, 'markWon'])->name('leads.markWon');
    Route::patch('leads/{lead}/lost',   [LeadController::class, 'markLost'])->name('leads.markLost');
    Route::patch('leads/{lead}/reopen', [LeadController::class, 'reopen'])->name('leads.reopen');
    Route::patch('leads/{id}/restore',  [LeadController::class, 'restore'])->name('leads.restore');

    // -------------------------------------------------------------------------
    // Activities
    // -------------------------------------------------------------------------
    Route::post('leads/{lead}/activities', [ActivityController::class, 'store'])
        ->name('leads.activities.store');

    Route::put('activities/{activity}',    [ActivityController::class, 'update'])
        ->name('activities.update');

    Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])
        ->name('activities.destroy');



    // -------------------------------------------------------------------------
    // Notifications
    // -------------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {

        Route::get('/',          [NotificationController::class, 'index'])->name('index');
        Route::get('/unread',    [NotificationController::class, 'unread'])->name('unread');
        Route::post('/',         [NotificationController::class, 'store'])->name('store');

        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/mark-all-read',       [NotificationController::class, 'markAllRead'])->name('mark-all-read');

        Route::delete('/destroy-read',        [NotificationController::class, 'destroyAllRead'])->name('destroy-read');
        Route::delete('/{notification}',      [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('leads',              [ReportController::class, 'leads'])->name('leads');
        Route::get('leads/export',       [ReportController::class, 'leadsExport'])->name('leads.export');

        Route::get('sales-performance',        [ReportController::class, 'salesPerformance'])->name('sales-performance');
        Route::get('sales-performance/export', [ReportController::class, 'salesPerformanceExport'])->name('sales-performance.export');

        Route::get('activities',         [ReportController::class, 'activities'])->name('activities');
        Route::get('activities/export',  [ReportController::class, 'activitiesExport'])->name('activities.export');
    });

    // -------------------------------------------------------------------------
    // Tasks
    // -------------------------------------------------------------------------
    Route::resource('tasks', TaskController::class)->except(['show']);

    Route::patch('tasks/{task}/done',   [TaskController::class, 'markDone'])->name('tasks.done');
    Route::patch('tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
    Route::patch('tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');

    // -------------------------------------------------------------------------
    // AJAX — Cascade dropdown
    // -------------------------------------------------------------------------
    Route::get('provinces/{province}/regencies', [LeadController::class, 'regenciesByProvince'])
        ->name('provinces.regencies');

    Route::get('regencies/{regency}/districts',  [LeadController::class, 'districtsByRegency'])
        ->name('regencies.districts');

    Route::get('activity-types/{type}/results',  [ActivityController::class, 'resultsByType'])
        ->name('activity-types.results');
});
