@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Order Detail Grid */
    .order-detail-layout {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .order-detail-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Timeline Stepper */
    .timeline-wrap {
        display: flex;
        flex-direction: column;
        position: relative;
        padding-left: 1.5rem;
    }
    .timeline-wrap::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 2px;
        background: var(--border-color);
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -1.5rem;
        top: 0.15rem;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--card-bg);
        border: 2px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-dot.active {
        background: var(--primary);
        border-color: var(--primary);
    }
    .timeline-dot.active::after {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
    }
    .timeline-dot.cancelled {
        border-color: var(--danger);
        background: var(--danger);
    }
    .timeline-content {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    /* Summary Detail Box */
    .info-tile {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.85rem 1rem;
        background: var(--bg-body);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
    }
    .info-tile-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .info-tile-val {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Product Item Row */
    .order-product-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .order-product-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    /* Mini Delivery Map */
    #orderDeliveryMap {
        width: 100%;
        height: 220px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        margin-top: 0.75rem;
        z-index: 1;
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Top Navigation & Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <a href="{{ route('admin.orders.index') }}" style="color: var(--text-muted); text-decoration: none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Pesanan</a>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">#{{ $order->order_number }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Pesanan #{{ $order->order_number }}</h1>
                @php
                    $statusStr = $order->status instanceof \App\Enums\OrderStatus ? $order->status->value : (string) $order->status;
                    $statusClass = match($statusStr) {
                        'DELIVERED', 'SHIPPED' => 'status-paid',
                        'PROCESSING', 'CONFIRMED' => 'status-processing',
                        'PENDING' => 'status-pending',
                        default => 'status-cancelled',
                    };
                @endphp
                <span class="status-pill {{ $statusClass }}" style="font-size: 0.8125rem; font-weight: 700; padding: 0.35rem 0.85rem;">{{ $statusStr }}</span>
                @if ($order->payment)
                    <span class="status-pill {{ $order->payment->status === 'PAID' ? 'status-paid' : 'status-pending' }}" style="font-size: 0.75rem; font-weight: 700;">
                        {{ $order->payment->status }} ({{ strtoupper($order->payment->provider) }})
                    </span>
                @endif
            </div>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
                Waktu Pemesanan: <strong style="color: var(--text-main);">{{ ($order->placed_at ?? $order->created_at)->format('d F Y, H:i:s') }} WIB</strong>
            </p>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <a href="{{ route('admin.orders.index') }}" class="btn-secondary">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                <span>Kembali</span>
            </a>
            <button type="button" class="btn-primary" onclick="openUpdateStatusModal()">
                <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                <span>Ubah Status</span>
            </button>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn-secondary" title="Cetak Faktur Penjualan">
                <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                <span>Cetak Invoice</span>
            </a>
        </div>
    </div>

    <!-- Main Detail Grid -->
    <div class="order-detail-layout">

        <!-- Left Column: Items, Financial & Timeline Tracking -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- Ordered Items Panel Card -->
            <div class="panel-card" style="padding: 1.5rem;">
                <div class="panel-header" style="margin-bottom: 1rem;">
                    <div>
                        <h3 class="panel-title" style="font-size: 1.15rem;">Daftar Produk Dipesan</h3>
                        <p style="font-size: 0.78125rem; color: var(--text-muted); margin-top: 0.15rem;">
                            Total {{ $order->items->sum('quantity') }} item dalam pesanan ini
                        </p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column;">
                    @foreach ($order->items as $item)
                        @php
                            $variant = $item->productVariant;
                            $product = $variant?->product;
                            $thumb = $product?->images->first()?->image_url ?? asset('images/placeholder-product.png');
                        @endphp
                        <div class="order-product-row">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: var(--bg-body); border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    @if ($product && $product->images->isNotEmpty())
                                        <img src="{{ $product->images->first()->image_url }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i data-lucide="package" style="width: 22px; height: 22px; color: var(--text-muted);"></i>
                                    @endif
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                    <span style="font-weight: 700; font-size: 0.875rem; color: var(--text-main);">{{ $item->product_name }}</span>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted);">
                                        <span>SKU: <code class="code-pill">{{ $item->sku }}</code></span>
                                        <span>•</span>
                                        <span>Harga: Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1.5rem; text-align: right;">
                                <span style="font-size: 0.84375rem; font-weight: 600; color: var(--text-muted);">x {{ $item->quantity }}</span>
                                <strong style="font-size: 0.95rem; color: var(--text-main); min-width: 110px;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Breakdown -->
                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed var(--border-color); display: flex; flex-direction: column; gap: 0.6rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.84375rem; color: var(--text-muted);">
                        <span>Subtotal Produk</span>
                        <span style="font-weight: 600; color: var(--text-main);">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.84375rem; color: var(--text-muted);">
                        <span>Ongkos Kirim</span>
                        <span style="font-weight: 600; color: var(--text-main);">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div style="display: flex; justify-content: space-between; font-size: 0.84375rem; color: var(--success);">
                            <span>Diskon / Voucher</span>
                            <span style="font-weight: 700;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; color: var(--text-main); padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 0.25rem;">
                        <span>Total Pembayaran</span>
                        <span style="color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Visual Timeline Lifecycle Tracking Panel -->
            <div class="panel-card" style="padding: 1.5rem;">
                <div class="panel-header" style="margin-bottom: 1.25rem;">
                    <div>
                        <h3 class="panel-title" style="font-size: 1.15rem;">Riwayat & Lifecycle Status Pesanan</h3>
                        <p style="font-size: 0.78125rem; color: var(--text-muted); margin-top: 0.15rem;">
                            Log perpindahan status dan catatan perubahan waktu demi waktu.
                        </p>
                    </div>
                </div>

                <div class="timeline-wrap">
                    @forelse ($order->statusHistories as $index => $history)
                        @php
                            $isLatest = ($index === 0);
                            $hStatus = $history->status instanceof \App\Enums\OrderStatus ? $history->status->value : (string) $history->status;
                            $dotClass = $isLatest ? ($hStatus === 'CANCELLED' ? 'cancelled' : 'active') : '';
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $dotClass }}"></div>
                            <div class="timeline-content">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <span class="status-pill {{ $hStatus === 'CANCELLED' ? 'status-cancelled' : ($isLatest ? 'status-paid' : 'status-processing') }}" style="font-size: 0.75rem; font-weight: 700;">
                                        {{ $hStatus }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">
                                        {{ $history->created_at->format('d M Y, H:i:s') }} WIB
                                    </span>
                                </div>
                                <p style="font-size: 0.8125rem; color: var(--text-main); margin: 0.35rem 0 0 0; line-height: 1.4;">
                                    {{ $history->note ?? 'Status diperbarui oleh sistem.' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 0.8125rem; color: var(--text-muted); padding: 1rem 0; text-align: center;">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Customer, Address & Payment Info -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- Customer Information Card -->
            <div class="panel-card" style="padding: 1.35rem;">
                <div class="panel-header" style="margin-bottom: 0.85rem;">
                    <h3 class="panel-title" style="font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i data-lucide="user" style="width: 16px; height: 16px; color: var(--primary);"></i>
                        <span>Informasi Pelanggan</span>
                    </h3>
                </div>

                @if ($order->user)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800;">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <strong style="font-size: 0.875rem; color: var(--text-main);">{{ $order->user->name }}</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $order->user->email }}</span>
                            </div>
                        </div>
                        <div class="info-tile">
                            <span class="info-tile-label">No. Telepon</span>
                            <span class="info-tile-val">{{ $order->user->phone ?? ($order->address->phone ?? '-') }}</span>
                        </div>
                    </div>
                @else
                    <p style="font-size: 0.8125rem; color: var(--text-muted);">Akun pelanggan tidak ditemukan / Tamu.</p>
                @endif
            </div>

            <!-- Shipping Destination & Tracking Card -->
            <div class="panel-card" style="padding: 1.35rem;">
                <div class="panel-header" style="margin-bottom: 0.85rem;">
                    <h3 class="panel-title" style="font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i data-lucide="map-pin" style="width: 16px; height: 16px; color: var(--primary);"></i>
                        <span>Alamat & Peta Pengiriman</span>
                    </h3>
                </div>

                @if ($order->address)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="info-tile">
                            <span class="info-tile-label">Penerima</span>
                            <span class="info-tile-val">{{ $order->address->recipient_name }} ({{ $order->address->phone }})</span>
                        </div>
                        <div class="info-tile">
                            <span class="info-tile-label">Alamat Lengkap</span>
                            <span class="info-tile-val" style="line-height: 1.4;">
                                {{ $order->address->address_line }}<br>
                                {{ $order->address->village_name ? $order->address->village_name . ', ' : '' }}
                                {{ $order->address->district_name ? $order->address->district_name . ', ' : '' }}
                                {{ $order->address->regency_name ? $order->address->regency_name . ', ' : '' }}
                                {{ $order->address->province_name ? $order->address->province_name . ' ' : '' }}
                                {{ $order->address->postal_code }}
                            </span>
                        </div>
                    </div>
                @else
                    <p style="font-size: 0.8125rem; color: var(--text-muted);">Data alamat pengiriman tidak tersedia.</p>
                @endif

                <!-- Shipment Tracking Info -->
                @php $shipment = $order->shipments->first(); @endphp
                <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px dashed var(--border-color); display: flex; flex-direction: column; gap: 0.5rem;">
                    <div class="info-tile">
                        <span class="info-tile-label">Ekspedisi & Layanan</span>
                        <span class="info-tile-val">
                            {{ $shipment?->service?->carrier?->name ?? 'Kurir' }} — {{ $shipment?->service?->name ?? 'Standar' }}
                        </span>
                    </div>
                    <div class="info-tile">
                        <span class="info-tile-label">Nomor Resi (AWB)</span>
                        <span class="info-tile-val">
                            @if ($shipment && $shipment->tracking_number)
                                <code class="code-pill" style="font-size: 0.8125rem; font-weight: 700; color: var(--primary);">{{ $shipment->tracking_number }}</code>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.78125rem;">Belum ada nomor resi</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Leaflet Interactive Google Traffic Delivery Map -->
                <div style="margin-top: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Peta Rute & Lalu Lintas (Google Traffic)</span>
                        <span style="font-size: 0.7rem; color: var(--primary); font-weight: 600;">Realtime Traffic</span>
                    </div>
                    <div id="orderDeliveryMap"></div>
                </div>
            </div>

            <!-- Payment Information Card -->
            <div class="panel-card" style="padding: 1.35rem;">
                <div class="panel-header" style="margin-bottom: 0.85rem;">
                    <h3 class="panel-title" style="font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i data-lucide="credit-card" style="width: 16px; height: 16px; color: var(--primary);"></i>
                        <span>Rincian Pembayaran</span>
                    </h3>
                </div>

                @if ($order->payment)
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="info-tile">
                            <span class="info-tile-label">Status Pembayaran</span>
                            <div>
                                <span class="status-pill {{ $order->payment->status === 'PAID' ? 'status-paid' : 'status-pending' }}">
                                    {{ $order->payment->status }}
                                </span>
                            </div>
                        </div>
                        <div class="info-tile">
                            <span class="info-tile-label">Provider Gateway</span>
                            <span class="info-tile-val">{{ strtoupper($order->payment->provider) }}</span>
                        </div>
                        <div class="info-tile">
                            <span class="info-tile-label">Kode Referensi Transaksi</span>
                            <span class="info-tile-val"><code class="code-pill">{{ $order->payment->provider_reference ?? '-' }}</code></span>
                        </div>
                        @if ($order->payment->paid_at)
                            <div class="info-tile">
                                <span class="info-tile-label">Waktu Pembayaran Lunas</span>
                                <span class="info-tile-val">{{ $order->payment->paid_at->format('d M Y, H:i:s') }} WIB</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p style="font-size: 0.8125rem; color: var(--text-muted);">Belum ada data transaksi pembayaran.</p>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- Modal Update Order Status -->
<x-modal id="updateStatusModal" title="Ubah Status Pesanan #{{ $order->order_number }}" maxWidth="520px">
    <form id="updateStatusForm" method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" onsubmit="handleStatusSubmit(event)">
        @csrf
        <div class="form-group">
            <label class="form-label">Pilih Status Baru <span style="color: var(--danger);">*</span></label>
            <select id="newStatusSelect" name="status" class="form-control" required onchange="handleDetailModalStatusChange(this.value)">
                <option value="PENDING" {{ $statusStr === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu Pembayaran)</option>
                <option value="CONFIRMED" {{ $statusStr === 'CONFIRMED' ? 'selected' : '' }}>CONFIRMED (Terkonfirmasi)</option>
                <option value="PROCESSING" {{ $statusStr === 'PROCESSING' ? 'selected' : '' }}>PROCESSING (Sedang Dipersiapkan)</option>
                <option value="SHIPPED" {{ $statusStr === 'SHIPPED' ? 'selected' : '' }}>SHIPPED (Telah Diserahkan ke Kurir / Dikirim)</option>
                <option value="DELIVERED" {{ $statusStr === 'DELIVERED' ? 'selected' : '' }}>DELIVERED (Telah Diterima Pelanggan / Selesai)</option>
                <option value="CANCELLED" {{ $statusStr === 'CANCELLED' ? 'selected' : '' }}>CANCELLED (Batalkan Pesanan & Kembalikan Stok)</option>
            </select>
        </div>

        <!-- Dynamic Inputs for SHIPPED status -->
        <div id="detailShippedFieldsContainer" style="{{ $statusStr === 'SHIPPED' ? 'display:block;' : 'display:none;' }} background: var(--primary-light); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem;">
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="truck" style="width: 15px; height: 15px;"></i>
                <span>Informasi Resi & Ekspedisi Pengiriman</span>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Resi Pengiriman (AWB / Tracking Number)</label>
                <input type="text" id="detailTrackingNumber" name="tracking_number" class="form-control" value="{{ $shipment?->tracking_number ?? '' }}" placeholder="Contoh: JNE88291039912">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Layanan Ekspedisi Kurir</label>
                <select id="detailShippingService" name="shipping_service_id" class="form-control">
                    <option value="">Pilih Layanan Kurir...</option>
                    @foreach ($carriers as $c)
                        <optgroup label="{{ $c->name }} ({{ $c->code }})">
                            @foreach ($c->services as $s)
                                <option value="{{ $s->id }}" {{ ($shipment?->service_id == $s->id) ? 'selected' : '' }}>{{ $c->name }} - {{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Warning for CANCELLED status -->
        <div id="detailCancelledWarningBox" style="{{ $statusStr === 'CANCELLED' ? 'display:block;' : 'display:none;' }} background: var(--danger-bg); border-left: 3px solid var(--danger); padding: 0.85rem; border-radius: var(--radius-sm); font-size: 0.78125rem; color: var(--text-main); margin-bottom: 1rem;">
            <strong>Perhatian:</strong> Membatalkan pesanan akan secara otomatis mengembalikan jumlah kuantitas stok produk ke inventaris sistem (*Stock Movement Return*).
        </div>

        <div class="form-group">
            <label class="form-label">Catatan Pembaruan Status (*Optional*)</label>
            <textarea id="detailStatusNote" name="note" class="form-control" rows="2" placeholder="Tuliskan catatan alasan perubahan status..."></textarea>
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" class="btn-secondary" onclick="closeModal('updateStatusModal')">Batal</button>
        <button type="button" id="submitDetailStatusBtn" class="btn-primary" onclick="handleStatusSubmit(event)">
            <i data-lucide="check" style="width: 15px; height: 15px;"></i>
            <span>Simpan Perubahan</span>
        </button>
    </x-slot>
</x-modal>

@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let orderDeliveryMap;
    const storeLat = parseFloat("{{ \App\Models\SystemSetting::get('warehouse_latitude', '-6.2297465') }}") || -6.2297465;
    const storeLng = parseFloat("{{ \App\Models\SystemSetting::get('warehouse_longitude', '106.8164494') }}") || 106.8164494;

    $(document).ready(function() {
        initOrderDeliveryMap();
    });

    function initOrderDeliveryMap() {
        const destLat = storeLat + 0.015; // Destination offset for visualization
        const destLng = storeLng + 0.020;

        orderDeliveryMap = L.map('orderDeliveryMap', {
            center: [storeLat, storeLng],
            zoom: 13,
            zoomControl: true
        });

        // Google Maps Traffic Tile Layer
        L.tileLayer('https://mt1.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Traffic'
        }).addTo(orderDeliveryMap);

        // Store Origin Marker
        const originMarker = L.marker([storeLat, storeLng]).addTo(orderDeliveryMap);
        originMarker.bindPopup(`
            <div style="font-size: 0.78125rem; font-weight: 600;">
                <strong style="color: var(--primary);">Gudang Asal Pengiriman</strong><br>
                <span>{{ \App\Models\SystemSetting::get('store_name', 'Toko') }}</span>
            </div>
        `);

        // Destination Marker
        const destMarker = L.marker([destLat, destLng]).addTo(orderDeliveryMap);
        destMarker.bindPopup(`
            <div style="font-size: 0.78125rem; font-weight: 600;">
                <strong style="color: #16a34a;">Tujuan Pengiriman</strong><br>
                <span>{{ $order->address?->recipient_name ?? 'Penerima' }}</span>
            </div>
        `).openPopup();

        // Fit bounds
        const group = new L.featureGroup([originMarker, destMarker]);
        orderDeliveryMap.fitBounds(group.getBounds().pad(0.2));
    }

    function openUpdateStatusModal() {
        openModal('updateStatusModal');
    }

    function handleDetailModalStatusChange(newStatus) {
        if (newStatus === 'SHIPPED') {
            $('#detailShippedFieldsContainer').slideDown(150);
            $('#detailCancelledWarningBox').slideUp(150);
        } else if (newStatus === 'CANCELLED') {
            $('#detailShippedFieldsContainer').slideUp(150);
            $('#detailCancelledWarningBox').slideDown(150);
        } else {
            $('#detailShippedFieldsContainer').slideUp(150);
            $('#detailCancelledWarningBox').slideUp(150);
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function handleStatusSubmit(e) {
        if (e) e.preventDefault();
        $('#submitDetailStatusBtn').prop('disabled', true);
        showPreloader('Memperbarui Status...', 'Menyimpan riwayat status, data kurir, dan memproses log...');

        $.ajax({
            url: "{{ route('admin.orders.update-status', $order->id) }}",
            type: 'POST',
            data: $('#updateStatusForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeModal('updateStatusModal');
                showToast('success', 'Berhasil', response.message || 'Status pesanan berhasil diperbarui.');
                setTimeout(() => window.location.reload(), 600);
            },
            error: function(xhr) {
                hidePreloader();
                $('#submitDetailStatusBtn').prop('disabled', false);
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Gagal memperbarui status pesanan.');
            }
        });
    }
</script>
@endpush
