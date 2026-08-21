<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Brand::query();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_badge', function ($row) {
                    return '<div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="brand-avatar-mini">' . strtoupper(substr($row->name, 0, 2)) . '</div>
                                <span style="font-weight: 600; color: var(--text-main);">' . e($row->name) . '</span>
                            </div>';
                })
                ->addColumn('slug_pill', function ($row) {
                    return '<code class="code-pill">' . e($row->slug) . '</code>';
                })
                ->addColumn('action', function ($row) {
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editBrand(' . $row->id . ')" title="Edit Brand">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteBrand(' . $row->id . ', \'' . $safeName . '\')" title="Delete Brand">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>';
                })
                ->rawColumns(['name_badge', 'slug_pill', 'action'])
                ->make(true);
        }

        return view('admin.master-data.brands');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
        ]);

        try {
            DB::beginTransaction();

            Brand::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan brand: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return response()->json([
            'success' => true,
            'data' => $brand
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
        ]);

        try {
            DB::beginTransaction();

            $brand->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui brand.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            DB::beginTransaction();

            // Check for related products
            if (Product::where('brand_id', $brand->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus brand ini karena masih digunakan oleh satu atau lebih produk.'
                ], 422);
            }

            $brand->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus brand.'
            ], 500);
        }
    }
}
