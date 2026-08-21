<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RegionController extends Controller
{
    /**
     * Display a listing of regional resources with server-side DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->query('type', 'countries');

            if ($type === 'countries') {
                $data = Country::query();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('formatted_phone', function ($row) {
                        return '+' . ltrim($row->phone_code, '+');
                    })
                    ->make(true);
            }

            if ($type === 'provinces') {
                $data = Province::with('country')->select('provinces.*');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('country_name', function ($row) {
                        return $row->country ? $row->country->name : '-';
                    })
                    ->make(true);
            }

            if ($type === 'regencies') {
                $data = Regency::with('province')->select('regencies.*');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('province_name', function ($row) {
                        return $row->province ? $row->province->name : '-';
                    })
                    ->addColumn('type_badge', function ($row) {
                        $isCity = ($row->type === 'CITY' || (is_object($row->type) && $row->type->value === 'CITY'));
                        $bg = $isCity ? 'var(--info-bg)' : 'var(--warning-bg)';
                        $color = $isCity ? 'var(--info)' : 'var(--warning)';
                        $label = is_object($row->type) ? $row->type->value : $row->type;
                        return '<span class="status-pill" style="background: ' . $bg . '; color: ' . $color . ';">' . e($label) . '</span>';
                    })
                    ->rawColumns(['type_badge'])
                    ->make(true);
            }

            if ($type === 'districts') {
                $data = District::with('regency')->select('districts.*');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('regency_name', function ($row) {
                        return $row->regency ? $row->regency->name : '-';
                    })
                    ->make(true);
            }

            if ($type === 'villages') {
                $data = Village::with('district')->select('villages.*');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('district_name', function ($row) {
                        return $row->district ? $row->district->name : '-';
                    })
                    ->addColumn('postal', function ($row) {
                        return $row->postal_code ? '<span style="font-family: monospace; font-weight: 600;">' . e($row->postal_code) . '</span>' : '-';
                    })
                    ->rawColumns(['postal'])
                    ->make(true);
            }

            return response()->json(['error' => 'Invalid region type'], 400);
        }

        return view('admin.master-data.regions');
    }
}
