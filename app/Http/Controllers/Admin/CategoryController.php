<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('parent')->select('categories.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_badge', function ($row) {
                    return '<div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="category-icon-box">
                                    <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
                                </div>
                                <span style="font-weight: 600; color: var(--text-main);">' . e($row->name) . '</span>
                            </div>';
                })
                ->addColumn('parent_name', function ($row) {
                    if ($row->parent) {
                        return '<span class="status-pill" style="background: var(--primary-light); color: var(--primary);">' . e($row->parent->name) . '</span>';
                    }
                    return '<span style="color: var(--text-light); font-size: 0.8125rem;">Root Category</span>';
                })
                ->addColumn('slug_pill', function ($row) {
                    return '<code class="code-pill">' . e($row->slug) . '</code>';
                })
                ->addColumn('action', function ($row) {
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editCategory(' . $row->id . ')" title="Edit Kategori">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteCategory(' . $row->id . ', \'' . $safeName . '\')" title="Hapus Kategori">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>';
                })
                ->rawColumns(['name_badge', 'parent_name', 'slug_pill', 'action'])
                ->make(true);
        }

        // Fetch categories for the parent dropdown
        $parentCategories = Category::all();
        
        return view('admin.master-data.categories', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        try {
            DB::beginTransaction();

            Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'parent_id' => $request->parent_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan kategori.'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id
        ]);

        try {
            DB::beginTransaction();

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'parent_id' => $request->parent_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kategori.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Check if this category has subcategories
            if (Category::where('parent_id', $category->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori ini karena memiliki sub-kategori.'
                ], 422);
            }

            // Check for related products
            if (Product::where('category_id', $category->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori ini karena masih digunakan oleh satu atau lebih produk.'
                ], 422);
            }

            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kategori.'
            ], 500);
        }
    }
}
