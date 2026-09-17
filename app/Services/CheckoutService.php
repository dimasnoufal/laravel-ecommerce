<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Address;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
use App\Models\StockMovement;
use App\Models\ShippingService;
use App\Models\ProductVariant;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\OrderAddressType;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService extends BaseService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Calculate shipping cost based on service code and destination.
     */
    public function calculateShippingCost(ShippingService $service, ?Address $address = null): float
    {
        $code = strtoupper($service->code);

        // Realistic tiered calculation based on shipping service type
        $rates = [
            'YES' => 32000,
            'ONS' => 30000,
            'BEST' => 28000,
            'REG' => 18000,
            'OKE' => 12000,
            'CARGO' => 45000,
            'JTR' => 40000,
            'GOKIL' => 35000,
        ];

        return (float) ($rates[$code] ?? 20000);
    }

    /**
     * Process checkout and create complete order.
     */
    public function processCheckout(User $user, array $data): array
    {
        $cartDetails = $this->cartService->getCartDetails($user);

        if (empty($cartDetails['items'])) {
            return $this->error('Keranjang belanja Anda masih kosong.');
        }

        // Verify stock for all items
        foreach ($cartDetails['items'] as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant || !$variant->is_active || $variant->stock < $item['quantity']) {
                return $this->error("Stok untuk produk '{$item['product_name']}' tidak mencukupi atau sudah habis.");
            }
        }

        // Resolve Address
        $address = null;
        if (!empty($data['address_id'])) {
            $address = $user->addresses()->with('village.district.regency.province.country')->find($data['address_id']);
        }

        if (!$address && !empty($data['new_address'])) {
            $newAddr = $data['new_address'];
            $address = Address::create([
                'user_id' => $user->id,
                'label' => $newAddr['label'] ?? 'Alamat Utama',
                'recipient_name' => $newAddr['recipient_name'],
                'phone' => $newAddr['phone'],
                'address_line' => $newAddr['address_line'],
                'village_id' => $newAddr['village_id'],
                'is_default' => $user->addresses()->count() === 0,
            ]);
            $address->load('village.district.regency.province.country');
        }

        if (!$address) {
            return $this->error('Silakan pilih atau tambahkan alamat pengiriman.');
        }

        // Resolve Shipping Service
        $shippingService = ShippingService::with('carrier')->find($data['shipping_service_id'] ?? null);
        if (!$shippingService || !$shippingService->is_active) {
            return $this->error('Silakan pilih kurir dan layanan pengiriman yang valid.');
        }

        $shippingCost = $this->calculateShippingCost($shippingService, $address);
        $subtotal = $cartDetails['subtotal'];
        $discount = 0;
        $totalAmount = $subtotal + $shippingCost - $discount;

        // Payment Provider
        $paymentProvider = $data['payment_provider'] ?? 'BCA_VA';

        return DB::transaction(function () use (
            $user,
            $cartDetails,
            $address,
            $shippingService,
            $shippingCost,
            $subtotal,
            $discount,
            $totalAmount,
            $paymentProvider
        ) {
            // 1. Create Order
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'status' => OrderStatus::PENDING,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
            ]);

            // 2. Create Order Address Snapshot
            $village = $address->village;
            $district = $village?->district;
            $regency = $district?->regency;
            $province = $regency?->province;
            $country = $province?->country;

            OrderAddress::create([
                'order_id' => $order->id,
                'type' => OrderAddressType::SHIPPING->value,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'address_line' => $address->address_line,
                'country_name' => $country?->name ?? 'Indonesia',
                'province_name' => $province?->name ?? '-',
                'regency_name' => $regency?->name ?? '-',
                'district_name' => $district?->name ?? '-',
                'village_name' => $village?->name ?? '-',
                'postal_code' => $village?->postal_code ?? '10110',
            ]);

            // 3. Create Order Items & Stock Movements
            foreach ($cartDetails['items'] as $item) {
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                if ($variant->stock < $item['quantity']) {
                    throw new \RuntimeException("Stok {$item['product_name']} tidak mencukupi.");
                }

                // Decrement stock
                $variant->decrement('stock', $item['quantity']);

                // Create Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $item['product_name'],
                    'sku' => $variant->sku,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Create Stock Movement Audit
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => StockMovementType::OUT->value,
                    'quantity' => $item['quantity'],
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'description' => "Penjualan pesanan {$order->order_number}",
                ]);
            }

            // 4. Generate Payment & Virtual Account Reference
            $vaPrefixes = [
                'BCA_VA' => '8800',
                'MANDIRI_VA' => '8870',
                'BRI_VA' => '8810',
                'BNI_VA' => '8820',
                'QRIS' => 'QRIS-',
                'COD' => 'COD-',
            ];
            $prefix = $vaPrefixes[$paymentProvider] ?? '8899';
            $reference = str_starts_with($prefix, '88')
                ? $prefix . substr($user->phone ?: '081234567890', -8) . rand(10, 99)
                : $prefix . strtoupper(Str::random(10));

            Payment::create([
                'order_id' => $order->id,
                'provider' => $paymentProvider,
                'provider_reference' => $reference,
                'amount' => $totalAmount,
                'status' => PaymentStatus::PENDING->value,
                'expired_at' => now()->addHours(24),
            ]);

            // 5. Log Order Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::PENDING,
                'notes' => "Pesanan dibuat dengan kurir {$shippingService->carrier->name} ({$shippingService->name}). Menunggu pembayaran {$paymentProvider}.",
            ]);

            // 6. Clear user cart
            $this->cartService->clearCart($user);

            return $this->success([
                'order' => $order->load(['items', 'address', 'payment']),
                'order_number' => $order->order_number,
                'redirect_url' => route('orders.show', $order->order_number),
            ], 'Pesanan berhasil dibuat!');
        });
    }

    /**
     * Simulate instant payment for testing.
     */
    public function simulatePayment(Order $order): array
    {
        if ($order->status !== OrderStatus::PENDING) {
            return $this->error('Status pesanan ini tidak dalam posisi menunggu pembayaran.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::CONFIRMED,
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'status' => PaymentStatus::PAID->value,
                    'paid_at' => now(),
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => OrderStatus::CONFIRMED,
                'notes' => 'Pembayaran berhasil dikonfirmasi secara instan (Simulasi).',
            ]);
        });

        return $this->success($order->fresh(['payment', 'statusHistories']), 'Pembayaran berhasil disimulasikan!');
    }
}
