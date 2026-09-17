<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    /**
     * Display listing of customer's orders.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Order::where('user_id', $user->id)
            ->with(['items.productVariant.product.images', 'payment'])
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('storefront.orders-index', compact('orders'));
    }

    /**
     * Display order invoice & payment instructions.
     */
    public function show(string $orderNumber): View
    {
        $user = auth()->user();
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with([
                'items.productVariant.product.images',
                'address',
                'payment',
                'statusHistories' => fn($q) => $q->latest()
            ])
            ->firstOrFail();

        return view('storefront.order-success', compact('order'));
    }

    /**
     * Simulate instant payment confirmation for testing.
     */
    public function simulatePayment(string $orderNumber)
    {
        $user = auth()->user();
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $result = $this->checkoutService->simulatePayment($order);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
