<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the storefront catalog home.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        $query = Product::where('is_active', true)
            ->with([
                'category',
                'brand',
                'images',
                'variants' => function ($q) {
                    $q->where('is_active', true);
                },
                'variants.attributeValues',
                'reviews'
            ]);

        // Filter by search query
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', function ($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category slug or id
        if ($categoryParam = $request->input('category')) {
            $query->whereHas('category', function ($c) use ($categoryParam) {
                $c->where('slug', $categoryParam)->orWhere('id', $categoryParam);
            });
        }

        // Filter by brand slug or id
        if ($brandParam = $request->input('brand')) {
            $query->whereHas('brand', function ($b) use ($brandParam) {
                $b->where('slug', $brandParam)->orWhere('id', $brandParam);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->withMin('variants', 'price')->orderBy('variants_min_price', 'asc');
                break;
            case 'price_high':
                $query->withMax('variants', 'price')->orderBy('variants_max_price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.home', compact('products', 'categories', 'brands'));
    }
}
