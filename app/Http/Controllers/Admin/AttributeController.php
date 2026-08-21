<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Attribute::with('values')->select('attributes.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name_badge', function ($row) {
                    return '<div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="category-icon-box" style="background: var(--primary-light); color: var(--primary);">
                                    <i data-lucide="sliders" style="width: 16px; height: 16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main);">' . e($row->name) . '</div>
                                    <code class="code-pill" style="font-size: 0.7rem; padding: 0.1rem 0.35rem;">' . e($row->slug) . '</code>
                                </div>
                            </div>';
                })
                ->addColumn('values_pills', function ($row) {
                    if ($row->values->isEmpty()) {
                        return '<span style="color: var(--text-light); font-size: 0.8125rem;">Belum ada nilai</span>';
                    }
                    
                    $html = '<div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">';
                    foreach ($row->values->take(6) as $val) {
                        $html .= '<span class="status-pill" style="background: var(--bg-hover); color: var(--text-main); font-weight: 500; font-size: 0.75rem;">' . e($val->value) . '</span>';
                    }
                    if ($row->values->count() > 6) {
                        $extra = $row->values->count() - 6;
                        $html .= '<span class="status-pill" style="background: var(--primary-light); color: var(--primary); font-weight: 600; font-size: 0.75rem;">+' . $extra . ' lainnya</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editAttribute(' . $row->id . ')" title="Edit Atribut">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteAttribute(' . $row->id . ', \'' . $safeName . '\')" title="Hapus Atribut">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>';
                })
                ->rawColumns(['name_badge', 'values_pills', 'action'])
                ->make(true);
        }

        return view('admin.master-data.attributes');
    }

    /**
     * Store a newly created attribute with values.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'values' => 'nullable|array',
            'values.*' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $attribute = Attribute::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            if ($request->has('values') && is_array($request->values)) {
                $uniqueValues = array_unique(array_filter(array_map('trim', $request->values)));
                foreach ($uniqueValues as $val) {
                    if (!empty($val)) {
                        AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value' => $val,
                            'slug' => Str::slug($val),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atribut berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan atribut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit(Attribute $attribute)
    {
        $attribute->load('values');
        return response()->json([
            'success' => true,
            'data' => $attribute
        ]);
    }

    /**
     * Update the specified attribute and its values.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'values' => 'nullable|array',
            'values.*' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $attribute->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            if ($request->has('values') && is_array($request->values)) {
                $submittedValues = array_unique(array_filter(array_map('trim', $request->values)));
                $existingValues = $attribute->values()->pluck('value', 'id')->toArray();
                
                // Add new values that don't exist
                foreach ($submittedValues as $val) {
                    if (!in_array($val, $existingValues)) {
                        AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value' => $val,
                            'slug' => Str::slug($val),
                        ]);
                    }
                }

                // Delete values that were removed, if not used in variant pivot
                foreach ($existingValues as $existingId => $existingVal) {
                    if (!in_array($existingVal, $submittedValues)) {
                        $isUsed = DB::table('product_variant_attribute_values')
                            ->where('attribute_value_id', $existingId)
                            ->exists();

                        if (!$isUsed) {
                            AttributeValue::where('id', $existingId)->delete();
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atribut berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui atribut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(Attribute $attribute)
    {
        try {
            DB::beginTransaction();

            $valueIds = $attribute->values()->pluck('id')->toArray();
            
            $isUsed = DB::table('product_variant_attribute_values')
                ->whereIn('attribute_value_id', $valueIds)
                ->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus atribut ini karena nilainya masih digunakan oleh varian produk.'
                ], 422);
            }

            $attribute->values()->delete();
            $attribute->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atribut berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus atribut.'
            ], 500);
        }
    }

    /**
     * Add a quick value on-the-fly (for product form modal).
     */
    public function storeValue(Request $request, Attribute $attribute)
    {
        $request->validate([
            'value' => 'required|string|max:255'
        ]);

        $val = trim($request->value);
        $attrVal = AttributeValue::firstOrCreate([
            'attribute_id' => $attribute->id,
            'value' => $val,
        ], [
            'slug' => Str::slug($val),
        ]);

        return response()->json([
            'success' => true,
            'data' => $attrVal,
            'message' => 'Nilai atribut berhasil ditambahkan.'
        ]);
    }
}
