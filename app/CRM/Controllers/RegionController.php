<?php

namespace App\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RegionService;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    public function __construct(
        protected RegionService $region, 
    ) {}

    public function cities(string $provinceCode): JsonResponse
    {
        return response()->json(
            $this->region->regencies($provinceCode)
        );
    }

    public function districts(string $cityCode): JsonResponse
    {
        return response()->json(
            $this->region->districts($cityCode)
        );
    }

    public function villages(string $districtCode): JsonResponse
    {
        return response()->json(
            $this->region->villages($districtCode)
        );
    }
}