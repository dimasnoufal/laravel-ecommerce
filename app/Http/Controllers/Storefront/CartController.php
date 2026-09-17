<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display full shopping cart page.
     */
    public function index(): View
    {
        $cart = $this->cartService->getCartDetails(auth()->user());
        return view('storefront.cart', compact('cart'));
    }

    /**
     * Add product variant to cart (supports AJAX or standard POST).
     */
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $variantId = (int) $request->input('variant_id');
        $quantity = (int) $request->input('quantity', 1);

        $result = $this->cartService->addItem($variantId, $quantity, auth()->user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Update item quantity in cart.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $quantity = (int) $request->input('quantity');
        $result = $this->cartService->updateItem($id, $quantity, auth()->user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return redirect()->route('cart.index')->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request, $id)
    {
        $result = $this->cartService->removeItem($id, auth()->user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    /**
     * Return JSON summary for header badge and slide-over drawer.
     */
    public function summary(): JsonResponse
    {
        $cart = $this->cartService->getCartDetails(auth()->user());
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'count' => $cart['total_items'],
        ]);
    }
}
