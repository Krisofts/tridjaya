<?php

use Illuminate\Support\Facades\Route;
use App\CRM\Controllers\RegionController;

Route::prefix('crm/regions')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | REGIONS API (WILAYAH.ID)
    |--------------------------------------------------------------------------
    */

    Route::get('/regencies/{provinceCode}', [RegionController::class, 'regencies']);

    Route::get('/districts/{regencyCode}', [RegionController::class, 'districts']);

    Route::get('/villages/{districtCode}', [RegionController::class, 'villages']);
});