@extends('layouts.storefront')

@section('title', 'Detail Pesanan #' . $order->order_number)

@push('styles')
<style>
    .order-header-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .status-pill-lg {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .status-pending {
        background: var(--warning-bg);
        color: var(--warning);
        border: 1px solid var(--warning);
    }
    .status-paid {
        background: var(--success-bg);
        color: var(--success);
        border: 1px solid var(--success);
    }
    .status-shipped {
        background: var(--info-bg);
        color: var(--info);
        border: 1px solid var(--info);
    }

    /* VA Payment Box */
    .va-box {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(59, 130, 246, 0.03) 100%);
        border: 2px dashed var(--primary);
        border-radius: var(--radius-xl);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    .va-number {
        font-family: monospace;
        font-size: 2.15rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: 0.08em;
        margin: 0.75rem 0;
    }

    .order-grid-details {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }
    @media (max-width: 860px) {
        .order-grid-details {
            grid-template-columns: 1fr;
        }
    }

    .section-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.75rem;
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@section('content')

    <!-- Order Top Header Card -->
    <div class="order-header-card">
        <div>
            <span style="font-size: 0.8125rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">
                Pesanan Anda
            </span>
            <h1 style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em; margin-top: 0.2rem;">
                {{ $order->order_number }}
            </h1>
            <p style="font-size: 0.84375rem; color: var(--text-muted); margin-top: 0.25rem;">
                Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div>
            @php $status = is_object($order->status) ? $order->status->value : $order->status; @endphp

            @if($status === 'PENDING')
                <div class="status-pill-lg status-pending">
                    <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                    <span>Menunggu Pembayaran</span>
                </div>
            @elseif(in_array($status, ['CONFIRMED', 'PROCESSING']))
                <div class="status-pill-lg status-paid">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                    <span>Sudah Dibayar / Diproses</span>
                </div>
            @elseif($status === 'SHIPPED')
                <div class="status-pill-lg status-shipped">
                    <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                    <span>Sedang Dikirim</span>
                </div>
            @else
                <div class="status-pill-lg" style="background: var(--bg-body); border: 1px solid var(--border-color);">
                    <span>{{ $status }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Virtual Account or Paid Box -->
    @if($status === 'PENDING')
        <div class="va-box">
            <span style="font-size: 0.875rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                {{ str_replace('_', ' ', $order->payment->provider ?? 'Pembayaran') }}
            </span>
            
            <div class="va-number" id="vaNumberDisplay">
                {{ $order->payment->provider_reference ?? '88001234567890' }}
            </div>

            <div style="display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <button type="button" class="btn-auth-outline" onclick="copyVA()" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.9rem;">
                    <i data-lucide="copy" style="width: 15px; height: 15px;"></i>
                    <span id="copyBtnText">Salin Nomor Rekening / VA</span>
                </button>
            </div>

            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Total Tagihan: <strong style="font-size: 1.2rem; color: var(--text-main);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
            </p>

            <!-- Instant Simulation Payment Button -->
            <form action="{{ route('orders.simulate-pay', $order->order_number) }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn-auth-solid" style="background: var(--success); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35); font-weight: 700; padding: 0.75rem 1.75rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="check-check" style="width: 18px; height: 18px;"></i>
                    <span>Simulasi Bayar Sekarang (Testing Instan)</span>
                </button>
            </form>
        </div>
    @else
        <div style="background: var(--success-bg); border: 1.5px solid var(--success); border-radius: var(--radius-xl); padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle-2" style="width: 32px; height: 32px; color: var(--success); flex-shrink: 0;"></i>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--success); margin-bottom: 0.2rem;">
                    Pembayaran Berhasil Terverifikasi!
                </h3>
                <p style="font-size: 0.875rem; color: var(--text-main);">
                    Terima kasih atas pesanan Anda. Kami sedang mempersiapkan paket pesanan Anda untuk segera dikirimkan.
                </p>
            </div>
        </div>
    @endif

    <!-- Two Columns: Order Items & Delivery Address -->
    <div class="order-grid-details">
        <!-- Left: Items List -->
        <div class="section-card">
            <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                Rincian Barang yang Dipesan
            </h3>

            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                @foreach($order->items as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <h4 style="font-size: 0.9375rem; font-weight: 700;">{{ $item->product_name }}</h4>
                            <span style="font-size: 0.78125rem; color: var(--text-muted);">
                                SKU: {{ $item->sku }} • Qty: {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </strong>
                    </div>
                @endforeach
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Subtotal Produk</span>
                    <strong>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Ongkos Kirim</span>
                    <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1.5px dashed var(--border-color);">
                    <span>Total Tagihan</span>
                    <span style="color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Address & Timeline -->
        <div style="display: flex; flex-direction: column; gap: 1.75rem;">
            <!-- Delivery Address Card -->
            <div class="section-card">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 1rem; padding-bottom: 0.65rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.45rem;">
                    <i data-lucide="map-pin" style="width: 18px; height: 18px; color: var(--primary);"></i>
                    <span>Tujuan Pengiriman</span>
                </h3>

                @if($order->address)
                    <strong style="font-size: 0.9375rem; display: block; margin-bottom: 0.2rem;">
                        {{ $order->address->recipient_name }}
                    </strong>
                    <span style="font-size: 0.8125rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;">
                        {{ $order->address->phone }}
                    </span>
                    <p style="font-size: 0.875rem; color: var(--text-main); line-height: 1.45; margin-bottom: 0.4rem;">
                        {{ $order->address->address_line }}
                    </p>
                    <p style="font-size: 0.8125rem; color: var(--text-muted);">
                        {{ $order->address->village_name }}, {{ $order->address->district_name }}, {{ $order->address->regency_name }}, {{ $order->address->province_name }} ({{ $order->address->postal_code }})
                    </p>
                @endif
            </div>

            <!-- Status Timeline Card -->
            <div class="section-card">
                <h3 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 1rem; padding-bottom: 0.65rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.45rem;">
                    <i data-lucide="activity" style="width: 18px; height: 18px; color: var(--primary);"></i>
                    <span>Riwayat Status</span>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @foreach($order->statusHistories as $history)
                        <div style="display: flex; gap: 0.75rem; font-size: 0.8125rem;">
                            <div style="width: 8px; height: 8px; border-radius: 9999px; background: var(--primary); margin-top: 6px; flex-shrink: 0;"></div>
                            <div>
                                <p style="font-weight: 600; color: var(--text-main);">{{ $history->notes }}</p>
                                <span style="color: var(--text-muted); font-size: 0.75rem;">
                                    {{ $history->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Actions -->
    <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 4rem;">
        <a href="{{ route('orders.index') }}" class="btn-auth-outline" style="text-decoration: none;">
            Lihat Semua Pesanan Saya
        </a>
        <a href="{{ route('home') }}" class="btn-auth-solid" style="text-decoration: none;">
            Lanjut Belanja Produk Lain
        </a>
    </div>

@endsection

@push('scripts')
<script>
    function copyVA() {
        const text = document.getElementById('vaNumberDisplay').innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            document.getElementById('copyBtnText').innerText = 'Nomor VA Disalin!';
            showToast('Nomor Virtual Account berhasil disalin ke clipboard.', 'success');
            setTimeout(() => {
                document.getElementById('copyBtnText').innerText = 'Salin Nomor Rekening / VA';
            }, 3000);
        });
    }
</script>
@endpush
