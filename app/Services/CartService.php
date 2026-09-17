<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Enums\CartStatus;
use Illuminate\Support\Facades\DB;

class CartService extends BaseService
{
    /**
     * Get or create active cart for user.
     */
    public function getActiveUserCart(User $user): Cart
    {
        return Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'status' => CartStatus::ACTIVE->value,
            ]
        );
    }

    /**
     * Get full cart details including items, variant data, and totals.
     */
    public function getCartDetails(?User $user = null): array
    {
        $items = [];
        $totalItems = 0;
        $subtotal = 0;

        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->where('status', CartStatus::ACTIVE->value)
                ->with(['items.productVariant.product.images', 'items.productVariant.attributeValues'])
                ->first();

            if ($cart) {
                foreach ($cart->items as $cartItem) {
                    $variant = $cartItem->productVariant;
                    if (!$variant || !$variant->product) {
                        continue;
                    }

                    $primaryImage = $variant->product->images->where('is_primary', true)->first()
                        ?: $variant->product->images->first();

                    $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
                    if ($primaryImage && str_starts_with($primaryImage->image_path, 'http')) {
                        $imagePath = $primaryImage->image_path;
                    }

                    $itemSubtotal = (float) $variant->price * $cartItem->quantity;
                    $subtotal += $itemSubtotal;
                    $totalItems += $cartItem->quantity;

                    $items[] = [
                        'id' => $cartItem->id,
                        'variant_id' => $variant->id,
                        'product_id' => $variant->product->id,
                        'product_name' => $variant->product->name,
                        'product_slug' => $variant->product->slug,
                        'sku' => $variant->sku,
                        'price' => (float) $variant->price,
                        'stock' => (int) $variant->stock,
                        'quantity' => (int) $cartItem->quantity,
                        'subtotal' => $itemSubtotal,
                        'image' => $imagePath,
                        'attributes' => $variant->attributeValues->pluck('value')->implode(' / '),
                    ];
                }
            }
        } else {
            // Guest session cart
            $sessionCart = session()->get('cart', []);
            if (!empty($sessionCart)) {
                $variantIds = array_keys($sessionCart);
                $variants = ProductVariant::whereIn('id', $variantIds)
                    ->with(['product.images', 'attributeValues'])
                    ->get()
                    ->keyBy('id');

                foreach ($sessionCart as $variantId => $itemData) {
                    $variant = $variants->get($variantId);
                    if (!$variant || !$variant->product) {
                        continue;
                    }

                    $qty = min((int) ($itemData['quantity'] ?? 1), max(1, $variant->stock));
                    $primaryImage = $variant->product->images->where('is_primary', true)->first()
                        ?: $variant->product->images->first();

                    $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : null;
                    if ($primaryImage && str_starts_with($primaryImage->image_path, 'http')) {
                        $imagePath = $primaryImage->image_path;
                    }

                    $itemSubtotal = (float) $variant->price * $qty;
                    $subtotal += $itemSubtotal;
                    $totalItems += $qty;

                    $items[] = [
                        'id' => $variant->id, // Use variant ID as item ID for guest
                        'variant_id' => $variant->id,
                        'product_id' => $variant->product->id,
                        'product_name' => $variant->product->name,
                        'product_slug' => $variant->product->slug,
                        'sku' => $variant->sku,
                        'price' => (float) $variant->price,
                        'stock' => (int) $variant->stock,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'image' => $imagePath,
                        'attributes' => $variant->attributeValues->pluck('value')->implode(' / '),
                    ];
                }
            }
        }

        return [
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
        ];
    }

    /**
     * Add an item to cart.
     */
    public function addItem(int $variantId, int $quantity = 1, ?User $user = null): array
    {
        $variant = ProductVariant::with('product')->find($variantId);
        if (!$variant || !$variant->is_active || !$variant->product?->is_active) {
            return $this->error('Produk atau varian ini saat ini tidak tersedia.');
        }

        if ($variant->stock <= 0) {
            return $this->error('Maaf, stok produk ini sedang habis.');
        }

        if ($user) {
            $cart = $this->getActiveUserCart($user);
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->first();

            $currentQty = $cartItem ? $cartItem->quantity : 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $variant->stock) {
                return $this->error("Jumlah melebihi stok yang tersedia (Maksimal: {$variant->stock}).");
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variantId,
                    'quantity' => $newQty,
                ]);
            }
        } else {
            // Guest session
            $cart = session()->get('cart', []);
            $currentQty = isset($cart[$variantId]) ? (int) $cart[$variantId]['quantity'] : 0;
            $newQty = $currentQty + $quantity;

            if ($newQty > $variant->stock) {
                return $this->error("Jumlah melebihi stok yang tersedia (Maksimal: {$variant->stock}).");
            }

            $cart[$variantId] = [
                'variant_id' => $variantId,
                'quantity' => $newQty,
            ];
            session()->put('cart', $cart);
        }

        $cartDetails = $this->getCartDetails($user);
        return $this->success($cartDetails, 'Produk berhasil ditambahkan ke keranjang belanja.');
    }

    /**
     * Update quantity of an item in cart.
     */
    public function updateItem($itemId, int $quantity, ?User $user = null): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId, $user);
        }

        if ($user) {
            $cart = $this->getActiveUserCart($user);
            $cartItem = CartItem::where('cart_id', $cart->id)->find($itemId);

            if (!$cartItem) {
                return $this->error('Item keranjang tidak ditemukan.');
            }

            $variant = $cartItem->productVariant;
            if ($quantity > $variant->stock) {
                return $this->error("Jumlah melebihi stok yang tersedia ({$variant->stock}).");
            }

            $cartItem->update(['quantity' => $quantity]);
        } else {
            $variantId = (int) $itemId;
            $variant = ProductVariant::find($variantId);

            if (!$variant) {
                return $this->error('Varian produk tidak ditemukan.');
            }

            if ($quantity > $variant->stock) {
                return $this->error("Jumlah melebihi stok yang tersedia ({$variant->stock}).");
            }

            $cart = session()->get('cart', []);
            if (isset($cart[$variantId])) {
                $cart[$variantId]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }

        $cartDetails = $this->getCartDetails($user);
        return $this->success($cartDetails, 'Jumlah belanja berhasil diperbarui.');
    }

    /**
     * Remove an item from cart.
     */
    public function removeItem($itemId, ?User $user = null): array
    {
        if ($user) {
            $cart = $this->getActiveUserCart($user);
            CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
        } else {
            $variantId = (int) $itemId;
            $cart = session()->get('cart', []);
            if (isset($cart[$variantId])) {
                unset($cart[$variantId]);
                session()->put('cart', $cart);
            }
        }

        $cartDetails = $this->getCartDetails($user);
        return $this->success($cartDetails, 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Clear all items in cart.
     */
    public function clearCart(?User $user = null): void
    {
        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->where('status', CartStatus::ACTIVE->value)
                ->first();

            if ($cart) {
                $cart->items()->delete();
            }
        }

        session()->forget('cart');
    }

    /**
     * Merge guest session cart items into user's database cart upon login/register.
     */
    public function mergeSessionCartToUser(User $user): void
    {
        $sessionCart = session()->get('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $userCart = $this->getActiveUserCart($user);

        foreach ($sessionCart as $variantId => $item) {
            $variant = ProductVariant::find($variantId);
            if (!$variant || !$variant->is_active || $variant->stock <= 0) {
                continue;
            }

            $cartItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_variant_id', $variantId)
                ->first();

            $qtyToAdd = (int) ($item['quantity'] ?? 1);

            if ($cartItem) {
                $newQty = min($cartItem->quantity + $qtyToAdd, $variant->stock);
                $cartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'cart_id' => $userCart->id,
                    'product_variant_id' => $variantId,
                    'quantity' => min($qtyToAdd, $variant->stock),
                ]);
            }
        }

        session()->forget('cart');
    }

    /**
     * Get total quantity count of cart items.
     */
    public function getCartCount(?User $user = null): int
    {
        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->where('status', CartStatus::ACTIVE->value)
                ->first();

            return $cart ? (int) $cart->items()->sum('quantity') : 0;
        }

        $sessionCart = session()->get('cart', []);
        return (int) array_sum(array_column($sessionCart, 'quantity'));
    }
}
