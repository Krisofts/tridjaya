<?php

namespace App\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RegionService;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $regionService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | REGENCIES (KOTA / KABUPATEN)
    |--------------------------------------------------------------------------
    */
    public function regencies(string $provinceCode)
    {
        return response()->json(
            $this->regionService->regencies($provinceCode) ?? []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DISTRICTS (KECAMATAN)
    |--------------------------------------------------------------------------
    */
    public function districts(string $regencyCode)
    {
        return response()->json(
            $this->regionService->districts($regencyCode) ?? []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VILLAGES (OPTIONAL)
    |--------------------------------------------------------------------------
    */
    public function villages(string $districtCode)
    {
        return response()->json(
            $this->regionService->villages($districtCode) ?? []
        );
    }
}