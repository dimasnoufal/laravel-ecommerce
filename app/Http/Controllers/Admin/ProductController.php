<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with(['category', 'brand', 'images', 'variants'])->select('products.*');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_info', function ($row) {
                    $primaryImg = $row->images->where('is_primary', true)->first() ?? $row->images->first();
                    $imgSrc = $primaryImg ? asset('storage/' . $primaryImg->image_path) : null;
                    
                    $imgHtml = $imgSrc 
                        ? '<img src="' . e($imgSrc) . '" alt="' . e($row->name) . '" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">'
                        : '<div class="category-icon-box" style="width: 44px; height: 44px; background: var(--primary-light); color: var(--primary); border-radius: 8px; font-size: 18px;">
                                <i data-lucide="package"></i>
                           </div>';

                    return '<div style="display: flex; align-items: center; gap: 0.85rem;">
                                ' . $imgHtml . '
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 0.9375rem;">' . e($row->name) . '</div>
                                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
                                        <code class="code-pill" style="font-size: 0.7rem; padding: 0.1rem 0.35rem;">' . e($row->slug) . '</code>
                                    </div>
                                </div>
                            </div>';
                })
                ->addColumn('category_name', function ($row) {
                    if ($row->category) {
                        return '<span class="status-pill" style="background: var(--info-bg); color: var(--info);">' . e($row->category->name) . '</span>';
                    }
                    return '<span style="color: var(--text-light); font-size: 0.8125rem;">-</span>';
                })
                ->addColumn('brand_name', function ($row) {
                    if ($row->brand) {
                        return '<span class="status-pill" style="background: var(--warning-bg); color: var(--warning);">' . e($row->brand->name) . '</span>';
                    }
                    return '<span style="color: var(--text-light); font-size: 0.8125rem;">-</span>';
                })
                ->addColumn('price_range', function ($row) {
                    if ($row->variants->isEmpty()) {
                        return '<span style="color: var(--text-light); font-size: 0.8125rem;">Rp 0</span>';
                    }
                    
                    $minPrice = $row->variants->min('price');
                    $maxPrice = $row->variants->max('price');
                    
                    if ($minPrice == $maxPrice) {
                        return '<span style="font-weight: 600; color: var(--text-main);">Rp ' . number_format($minPrice, 0, ',', '.') . '</span>';
                    }
                    
                    return '<span style="font-weight: 600; color: var(--text-main);">Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.') . '</span>';
                })
                ->addColumn('total_stock', function ($row) {
                    $totalStock = $row->variants->sum('stock');
                    $variantCount = $row->variants->count();
                    $badgeStyle = $totalStock > 0 ? 'color: var(--success);' : 'color: var(--danger);';
                    
                    return '<div>
                                <div style="font-weight: 600; ' . $badgeStyle . '">' . number_format($totalStock, 0, ',', '.') . ' unit</div>
                                <div style="font-size: 0.725rem; color: var(--text-muted);">' . $variantCount . ' varian SKU</div>
                            </div>';
                })
                ->addColumn('status_pill', function ($row) {
                    $isActive = (bool)$row->is_active;
                    $bg = $isActive ? 'var(--success-bg)' : 'var(--danger-bg)';
                    $color = $isActive ? 'var(--success)' : 'var(--danger)';
                    $label = $isActive ? 'AKTIF' : 'NONAKTIF';
                    return '<span class="status-pill" style="background: ' . $bg . '; color: ' . $color . ';">' . $label . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    $editUrl = route('admin.products.edit', $row->id);
                    return '<div class="table-actions">
                                <a href="' . $editUrl . '" class="tbl-btn tbl-btn-edit" title="Edit Produk" onclick="showPreloader(\'Mengambil Data Produk\', \'Memuat katalog, galeri foto, dan matriks varian...\')">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </a>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteProduct(' . $row->id . ', \'' . $safeName . '\')" title="Hapus Produk">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>';
                })
                ->rawColumns(['product_info', 'category_name', 'brand_name', 'price_range', 'total_stock', 'status_pill', 'action'])
                ->make(true);
        }

        return view('admin.master-data.products');
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();
        $product = null;

        return view('admin.master-data.product-form', compact('categories', 'brands', 'attributes', 'product'));
    }

    /**
     * Store a newly created product with images, attributes, and variants.
     */
    public function store(Request $request)
    {
        if ($request->filled('single_price')) {
            $request->merge(['single_price' => preg_replace('/[^0-9]/', '', (string)$request->single_price)]);
        }
        if ($request->has('variants') && is_array($request->variants)) {
            $variants = $request->variants;
            foreach ($variants as $k => $v) {
                if (isset($v['price'])) {
                    $variants[$k]['price'] = preg_replace('/[^0-9]/', '', (string)$v['price']);
                }
            }
            $request->merge(['variants' => $variants]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'product_type' => 'required|in:single,variant',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'primary_image_index' => 'nullable|integer',
            
            // Single product validation
            'single_sku' => 'required_if:product_type,single|nullable|string|max:100|unique:product_variants,sku',
            'single_price' => 'required_if:product_type,single|nullable|numeric|min:0',
            'single_stock' => 'required_if:product_type,single|nullable|integer|min:0',

            // Multi-variant validation
            'variants' => 'required_if:product_type,variant|array|min:1',
            'variants.*.sku' => 'required_if:product_type,variant|string|max:100|distinct|unique:product_variants,sku',
            'variants.*.price' => 'required_if:product_type,variant|numeric|min:0',
            'variants.*.stock' => 'required_if:product_type,variant|integer|min:0',
            'variants.*.attribute_values' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Handle Images
            if ($request->hasFile('images')) {
                $primaryIndex = (int)$request->input('primary_image_index', 0);
                foreach ($request->file('images') as $idx => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $idx,
                        'is_primary' => ($idx === $primaryIndex),
                    ]);
                }
            }

            // Handle Variants
            if ($request->product_type === 'single') {
                $sku = $request->single_sku ?: strtoupper(Str::slug($product->name)) . '-SKU';
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => $request->single_price ?? 0,
                    'stock' => $request->single_stock ?? 0,
                    'is_active' => true,
                ]);
            } else {
                foreach ($request->variants as $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'] ?? 0,
                        'stock' => $variantData['stock'] ?? 0,
                        'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
                    ]);

                    if (!empty($variantData['attribute_values']) && is_array($variantData['attribute_values'])) {
                        $variant->attributeValues()->sync($variantData['attribute_values']);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat beserta foto dan varian.',
                'redirect_url' => route('admin.products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product)
    {
        $product->load(['images', 'variants.attributeValues']);
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.master-data.product-form', compact('categories', 'brands', 'attributes', 'product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        if ($request->filled('single_price')) {
            $request->merge(['single_price' => preg_replace('/[^0-9]/', '', (string)$request->single_price)]);
        }
        if ($request->has('variants') && is_array($request->variants)) {
            $variants = $request->variants;
            foreach ($variants as $k => $v) {
                if (isset($v['price'])) {
                    $variants[$k]['price'] = preg_replace('/[^0-9]/', '', (string)$v['price']);
                }
            }
            $request->merge(['variants' => $variants]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'product_type' => 'required|in:single,variant',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'primary_image_id' => 'nullable|integer',
            'delete_image_ids' => 'nullable|array',
            
            // Single product validation
            'single_sku' => 'required_if:product_type,single|nullable|string|max:100',
            'single_price' => 'required_if:product_type,single|nullable|numeric|min:0',
            'single_stock' => 'required_if:product_type,single|nullable|integer|min:0',

            // Multi-variant validation
            'variants' => 'required_if:product_type,variant|array|min:1',
            'variants.*.sku' => 'required_if:product_type,variant|string|max:100|distinct',
            'variants.*.price' => 'required_if:product_type,variant|numeric|min:0',
            'variants.*.stock' => 'required_if:product_type,variant|integer|min:0',
            'variants.*.attribute_values' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Handle deleted existing images
            if ($request->has('delete_image_ids') && is_array($request->delete_image_ids)) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $request->delete_image_ids)
                    ->get();
                
                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            // Upload new images
            if ($request->hasFile('images')) {
                $primaryNewIndex = $request->filled('primary_image_index') ? (int)$request->primary_image_index : null;
                if ($primaryNewIndex !== null && !$request->filled('primary_image_id')) {
                    ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                }

                foreach ($request->file('images') as $idx => $file) {
                    $path = $file->store('products', 'public');
                    $isPrimary = ($primaryNewIndex !== null && $idx === $primaryNewIndex && !$request->filled('primary_image_id'));
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'sort_order' => $idx + 1,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }

            // Update primary image
            if ($request->filled('primary_image_id')) {
                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                ProductImage::where('id', $request->primary_image_id)
                    ->where('product_id', $product->id)
                    ->update(['is_primary' => true]);
            } else {
                // Ensure at least 1 image is primary if images exist
                if ($product->images()->count() > 0 && !$product->images()->where('is_primary', true)->exists()) {
                    $product->images()->first()->update(['is_primary' => true]);
                }
            }

            // Handle Variants
            if ($request->product_type === 'single') {
                // Delete old variants that are not single
                $product->variants()->delete();

                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $request->single_sku,
                    'price' => $request->single_price ?? 0,
                    'stock' => $request->single_stock ?? 0,
                    'is_active' => true,
                ]);
            } else {
                // For multi-variant: update or recreate
                $existingVariantIds = $product->variants()->pluck('id')->toArray();
                $submittedVariantIds = [];

                foreach ($request->variants as $variantData) {
                    $variantId = $variantData['id'] ?? null;
                    
                    if ($variantId && in_array($variantId, $existingVariantIds)) {
                        $variant = ProductVariant::find($variantId);
                        $variant->update([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'] ?? 0,
                            'stock' => $variantData['stock'] ?? 0,
                            'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
                        ]);
                        $submittedVariantIds[] = $variant->id;
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'] ?? 0,
                            'stock' => $variantData['stock'] ?? 0,
                            'is_active' => isset($variantData['is_active']) ? (bool)$variantData['is_active'] : true,
                        ]);
                        $submittedVariantIds[] = $variant->id;
                    }

                    if (!empty($variantData['attribute_values']) && is_array($variantData['attribute_values'])) {
                        $variant->attributeValues()->sync($variantData['attribute_values']);
                    }
                }

                // Delete variants removed from form
                $variantsToRemove = array_diff($existingVariantIds, $submittedVariantIds);
                if (!empty($variantsToRemove)) {
                    ProductVariant::whereIn('id', $variantsToRemove)->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui.',
                'redirect_url' => route('admin.products.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $variantIds = $product->variants()->pluck('id')->toArray();
            
            $hasOrders = DB::table('order_items')
                ->whereIn('product_variant_id', $variantIds)
                ->exists();

            if ($hasOrders) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus produk ini karena sudah memiliki riwayat transaksi pesanan.'
                ], 422);
            }

            $product->variants()->delete();
            $product->images()->delete();
            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
