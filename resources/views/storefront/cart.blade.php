@extends('layouts.storefront')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2.5rem;
        align-items: start;
    }
    @media (max-width: 960px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }
    }

    .cart-table-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .cart-item-row {
        display: grid;
        grid-template-columns: 80px 1fr auto auto auto;
        gap: 1.25rem;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    .cart-item-row:last-child {
        border-bottom: none;
    }
    @media (max-width: 640px) {
        .cart-item-row {
            grid-template-columns: 70px 1fr;
            gap: 1rem;
        }
    }

    .cart-item-thumb {
        width: 80px;
        height: 80px;
        border-radius: var(--radius-md);
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cart-item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Summary Card */
    .summary-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        padding: 1.75rem;
        position: sticky;
        top: 96px;
    }
    .summary-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-color);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
        font-size: 0.9375rem;
    }
    .summary-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1.5px dashed var(--border-color);
        font-size: 1.15rem;
        font-weight: 800;
    }

    .btn-checkout-primary {
        width: 100%;
        padding: 0.95rem;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transition: opacity 0.2s, transform 0.2s;
        margin-top: 1.5rem;
    }
    .btn-checkout-primary:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')

    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.35rem;">
            Keranjang Belanja
        </h1>
        <p style="font-size: 0.875rem; color: var(--text-muted);">
            Periksa item pesanan Anda sebelum melanjutkan ke proses pembayaran.
        </p>
    </div>

    @if(empty($cart['items']))
        <div style="text-align: center; padding: 5rem 1rem; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
            <i data-lucide="shopping-bag" style="width: 64px; height: 64px; margin: 0 auto 1.25rem auto; opacity: 0.35; color: var(--text-muted);"></i>
            <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem;">Wah, keranjang belanjamu kosong!</h2>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin-bottom: 2rem;">
                Yuk isi dengan produk-produk pilihan original terbaik dari toko kami.
            </p>
            <a href="{{ route('home') }}" class="btn-auth-solid" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.75rem; text-decoration: none;">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                <span>Mulai Belanja Sekarang</span>
            </a>
        </div>
    @else
        <div class="cart-layout">
            <!-- Left: Items Table -->
            <div class="cart-table-card">
                <div style="padding: 1rem 1.5rem; background: var(--bg-body); border-bottom: 1px solid var(--border-color); font-size: 0.875rem; font-weight: 700; color: var(--text-muted); display: flex; justify-content: space-between;">
                    <span>Daftar Produk ({{ $cart['total_items'] }} barang)</span>
                    <a href="{{ route('home') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">+ Tambah Produk Lain</a>
                </div>

                @foreach($cart['items'] as $item)
                    <div class="cart-item-row" id="cart-row-{{ $item['id'] }}">
                        <div class="cart-item-thumb">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}">
                            @else
                                <i data-lucide="image" style="width: 24px; height: 24px; opacity: 0.4;"></i>
                            @endif
                        </div>

                        <div>
                            <a href="{{ route('products.show', $item['product_slug']) }}" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); text-decoration: none; line-height: 1.35;">
                                {{ $item['product_name'] }}
                            </a>
                            @if($item['attributes'])
                                <p style="font-size: 0.78125rem; color: var(--text-muted); margin-top: 0.25rem;">
                                    Varian: <strong>{{ $item['attributes'] }}</strong>
                                </p>
                            @endif
                            <p style="font-size: 0.8125rem; color: var(--primary); font-weight: 700; margin-top: 0.4rem;">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Quantity control -->
                        <div>
                            <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--card-bg);">
                                <button type="button" style="width: 30px; height: 30px; border: none; background: none; cursor: pointer; font-size: 1rem;" onclick="updateCartQty('{{ $item['id'] }}', {{ $item['quantity'] - 1 }})">-</button>
                                <span style="width: 36px; text-align: center; font-size: 0.875rem; font-weight: 700;">{{ $item['quantity'] }}</span>
                                <button type="button" style="width: 30px; height: 30px; border: none; background: none; cursor: pointer; font-size: 1rem;" onclick="updateCartQty('{{ $item['id'] }}', {{ $item['quantity'] + 1 }})">+</button>
                            </div>
                        </div>

                        <!-- Subtotal per item -->
                        <div style="font-size: 1rem; font-weight: 800; color: var(--text-main); min-width: 110px; text-align: right;">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>

                        <!-- Remove button -->
                        <div>
                            <button type="button" class="icon-btn" style="width: 34px; height: 34px; border: none; color: var(--text-muted);" onclick="removeCartItem('{{ $item['id'] }}')" title="Hapus Barang">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Right: Order Summary -->
            <div class="summary-card">
                <h3 class="summary-title">Ringkasan Belanja</h3>

                <div class="summary-row">
                    <span style="color: var(--text-muted);">Total Harga ({{ $cart['total_items'] }} barang)</span>
                    <strong style="color: var(--text-main);">{{ $cart['formatted_subtotal'] }}</strong>
                </div>

                <div class="summary-row">
                    <span style="color: var(--text-muted);">Estimasi Ongkos Kirim</span>
                    <span style="font-size: 0.8125rem; color: var(--success); font-weight: 600;">Dihitung saat Checkout</span>
                </div>

                <div class="summary-total-row">
                    <span>Total Tagihan</span>
                    <span style="color: var(--primary);">{{ $cart['formatted_subtotal'] }}</span>
                </div>

                @auth
                    <a href="{{ route('checkout.index') }}" class="btn-checkout-primary" style="text-decoration: none;">
                        <span>Lanjut ke Checkout</span>
                        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                    </a>
                @else
                    <!-- Guest triggers the auth modal wall! -->
                    <button type="button" class="btn-checkout-primary" onclick="openAuthModal('login')">
                        <span>Checkout (Masuk / Daftar)</span>
                        <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                    </button>
                    <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 0.65rem;">
                        Masuk atau buat akun dengan 1 klik untuk menyelesaikan pesanan Anda.
                    </p>
                @endauth
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    async function updateCartQty(itemId, newQty) {
        try {
            const res = await fetch(`/cart/update/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: newQty })
            });

            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('Gagal memperbarui jumlah item.', 'error');
        }
    }

    async function removeCartItem(itemId) {
        if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) return;

        try {
            const res = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                showToast(data.message, 'error');
            }
        } catch (err) {
            showToast('Gagal menghapus item.', 'error');
        }
    }
</script>
@endpush
