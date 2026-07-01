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
    // Reports — satu halaman dengan 3 tab (leads | sales | activities)
    // -------------------------------------------------------------------------
    Route::get('reports',        [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // -------------------------------------------------------------------------
    // Lost Reasons — Master Data
    // -------------------------------------------------------------------------
    Route::resource('lost-reasons', LostReasonController::class)
        ->except(['show']);

    Route::patch('lost-reasons/{lostReason}/toggle-active', [LostReasonController::class, 'toggleActive'])
        ->name('lost-reasons.toggle-active');

    // -------------------------------------------------------------------------
    // Leads — AJAX check-phone harus SEBELUM resource agar tidak bentrok {lead}
    // -------------------------------------------------------------------------
    Route::get('leads/check-phone', [LeadController::class, 'checkPhone'])
        ->name('leads.check-phone');

    Route::get('leads/my-leads', [LeadController::class, 'myLeads'])
        ->name('leads.my-leads');

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
    // Tasks
    // -------------------------------------------------------------------------
    Route::resource('tasks', TaskController::class)->except(['show']);

    Route::patch('tasks/{task}/done',   [TaskController::class, 'markDone'])->name('tasks.done');
    Route::patch('tasks/{task}/reopen', [TaskController::class, 'reopen'])->name('tasks.reopen');
    Route::patch('tasks/{task}/cancel', [TaskController::class, 'cancel'])->name('tasks.cancel');

    // -------------------------------------------------------------------------
    // Notifications
    // -------------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                         [NotificationController::class, 'index'])->name('index');
        Route::get('/unread',                   [NotificationController::class, 'unread'])->name('unread');
        Route::post('/',                        [NotificationController::class, 'store'])->name('store');
        Route::post('/mark-all-read',           [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{notification}/read',     [NotificationController::class, 'markRead'])->name('read');
        Route::delete('/destroy-read',          [NotificationController::class, 'destroyAllRead'])->name('destroy-read');
        Route::delete('/{notification}',        [NotificationController::class, 'destroy'])->name('destroy');
    });

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