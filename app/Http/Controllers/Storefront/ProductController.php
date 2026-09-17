<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'brand',
                'images' => fn($q) => $q->orderBy('sort_order', 'asc'),
                'variants' => fn($q) => $q->where('is_active', true)->with('attributeValues.attribute'),
                'reviews' => fn($q) => $q->where('is_approved', true)->with(['user', 'images'])->latest(),
            ])
            ->firstOrFail();

        // Compute attributes map for this product
        $attributes = [];
        $variantsMap = [];

        foreach ($product->variants as $variant) {
            $attrValueIds = $variant->attributeValues->pluck('id')->sort()->values()->all();
            $variantsMap[] = [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'formatted_price' => 'Rp ' . number_format($variant->price, 0, ',', '.'),
                'stock' => (int) $variant->stock,
                'attribute_value_ids' => $attrValueIds,
                'attributes_summary' => $variant->attributeValues->pluck('value')->implode(' / '),
            ];

            foreach ($variant->attributeValues as $attrVal) {
                $attr = $attrVal->attribute;
                if ($attr) {
                    $attrId = $attr->id;
                    if (!isset($attributes[$attrId])) {
                        $attributes[$attrId] = [
                            'id' => $attr->id,
                            'name' => $attr->name,
                            'values' => [],
                        ];
                    }
                    $attributes[$attrId]['values'][$attrVal->id] = [
                        'id' => $attrVal->id,
                        'value' => $attrVal->value,
                    ];
                }
            }
        }

        // Related products in the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images', 'variants' => fn($q) => $q->where('is_active', true)])
            ->take(4)
            ->get();

        return view('storefront.product-detail', compact('product', 'attributes', 'variantsMap', 'relatedProducts'));
    }

    /**
     * API to query variant by attribute values.
     */
    public function getVariant(Request $request, int $productId): JsonResponse
    {
        $attributeValueIds = $request->input('attribute_value_ids', []);

        $variants = ProductVariant::where('product_id', $productId)
            ->where('is_active', true)
            ->with('attributeValues')
            ->get();

        foreach ($variants as $variant) {
            $currentIds = $variant->attributeValues->pluck('id')->all();
            sort($currentIds);
            sort($attributeValueIds);

            if ($currentIds == $attributeValueIds) {
                return response()->json([
                    'success' => true,
                    'variant' => [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => (float) $variant->price,
                        'formatted_price' => 'Rp ' . number_format($variant->price, 0, ',', '.'),
                        'stock' => (int) $variant->stock,
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Varian kombinasi ini tidak ditemukan.'
        ], 404);
    }
}
