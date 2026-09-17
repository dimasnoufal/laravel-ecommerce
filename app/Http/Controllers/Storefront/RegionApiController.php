<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionApiController extends Controller
{
    /**
     * Get all active provinces.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name', 'asc')->get(['id', 'code', 'name']);
        return response()->json($provinces);
    }

    /**
     * Get regencies by province ID.
     */
    public function regencies($provinceId): JsonResponse
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name', 'type']);
        return response()->json($regencies);
    }

    /**
     * Get districts by regency ID.
     */
    public function districts($regencyId): JsonResponse
    {
        $districts = District::where('regency_id', $regencyId)
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name']);
        return response()->json($districts);
    }

    /**
     * Get villages by district ID.
     */
    public function villages($districtId): JsonResponse
    {
        $villages = Village::where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name', 'type', 'postal_code']);
        return response()->json($villages);
    }
}
