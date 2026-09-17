<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Models\ShippingCarrier;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService
    ) {}

    /**
     * Display checkout page.
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('cart.index', ['auth' => 'login'])
                ->with('info', 'Silakan masuk atau daftar terlebih dahulu untuk melanjutkan pesanan Anda.');
        }

        $user = auth()->user();
        $cart = $this->cartService->getCartDetails($user);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('warning', 'Keranjang belanja Anda kosong.');
        }

        // Saved addresses
        $addresses = $user->addresses()
            ->with('village.district.regency.province.country')
            ->orderBy('is_default', 'desc')
            ->latest()
            ->get();

        // Active Carriers & Services
        $carriers = ShippingCarrier::where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        // Payment Methods list
        $paymentMethods = [
            [
                'group' => 'Transfer Bank (Virtual Account)',
                'options' => [
                    ['code' => 'BCA_VA', 'name' => 'BCA Virtual Account', 'icon' => 'credit-card', 'badge' => 'Otomatis'],
                    ['code' => 'MANDIRI_VA', 'name' => 'Mandiri Virtual Account', 'icon' => 'credit-card', 'badge' => 'Otomatis'],
                    ['code' => 'BRI_VA', 'name' => 'BRI Virtual Account', 'icon' => 'credit-card', 'badge' => 'Otomatis'],
                    ['code' => 'BNI_VA', 'name' => 'BNI Virtual Account', 'icon' => 'credit-card', 'badge' => 'Otomatis'],
                ]
            ],
            [
                'group' => 'E-Wallet & QRIS Instant',
                'options' => [
                    ['code' => 'QRIS', 'name' => 'QRIS (GoPay, OVO, ShopeePay, Dana)', 'icon' => 'qr-code', 'badge' => 'Instan Bebas Biaya'],
                ]
            ],
            [
                'group' => 'Bayar di Tempat (COD)',
                'options' => [
                    ['code' => 'COD', 'name' => 'Cash On Delivery (Bayar Saat Barang Sampai)', 'icon' => 'truck', 'badge' => 'Tunai'],
                ]
            ]
        ];

        return view('storefront.checkout', compact('cart', 'addresses', 'carriers', 'paymentMethods'));
    }

    /**
     * Process order submission.
     */
    public function process(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|integer',
            'new_address.recipient_name' => 'required_without:address_id|nullable|string|max:255',
            'new_address.phone' => 'required_without:address_id|nullable|string|max:20',
            'new_address.address_line' => 'required_without:address_id|nullable|string',
            'new_address.village_id' => 'required_without:address_id|nullable|integer|exists:villages,id',
            'shipping_service_id' => 'required|integer|exists:shipping_services,id',
            'payment_provider' => 'required|string',
        ]);

        $user = auth()->user();
        $result = $this->checkoutService->processCheckout($user, $request->all());

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to($result['data']['redirect_url'])
            ->with('success', $result['message']);
    }
}
