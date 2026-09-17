@extends('layouts.storefront')

@section('title', 'Daftar Pesanan Saya')

@push('styles')
<style>
    .order-status-tabs {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    .order-tab-link {
        padding: 0.6rem 1.25rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .order-tab-link:hover {
        color: var(--primary);
        background: var(--card-bg);
    }
    .order-tab-link.active {
        background: var(--primary);
        color: #fff;
    }

    .order-card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .order-card-box:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .order-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
</style>
@endpush

@section('content')

    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.35rem;">
            Daftar Pesanan Saya
        </h1>
        <p style="font-size: 0.875rem; color: var(--text-muted);">
            Pantau status proses, pengiriman, dan riwayat belanja Anda.
        </p>
    </div>

    <!-- Status Tabs -->
    <div class="order-status-tabs">
        <a href="{{ route('orders.index') }}" class="order-tab-link {{ !request('status') ? 'active' : '' }}">
            Semua Pesanan
        </a>
        <a href="{{ route('orders.index', ['status' => 'PENDING']) }}" class="order-tab-link {{ request('status') === 'PENDING' ? 'active' : '' }}">
            Menunggu Pembayaran
        </a>
        <a href="{{ route('orders.index', ['status' => 'CONFIRMED']) }}" class="order-tab-link {{ request('status') === 'CONFIRMED' ? 'active' : '' }}">
            Diproses
        </a>
        <a href="{{ route('orders.index', ['status' => 'SHIPPED']) }}" class="order-tab-link {{ request('status') === 'SHIPPED' ? 'active' : '' }}">
            Sedang Dikirim
        </a>
        <a href="{{ route('orders.index', ['status' => 'DELIVERED']) }}" class="order-tab-link {{ request('status') === 'DELIVERED' ? 'active' : '' }}">
            Selesai
        </a>
    </div>

    @if($orders->isEmpty())
        <div style="text-align: center; padding: 5rem 1rem; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
            <i data-lucide="package" style="width: 56px; height: 56px; margin: 0 auto 1rem auto; opacity: 0.35; color: var(--text-muted);"></i>
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;">Belum ada riwayat pesanan</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.75rem;">
                Temukan berbagai produk berkualitas dan mulai belanja hari ini!
            </p>
            <a href="{{ route('home') }}" class="btn-auth-solid" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.75rem;">
                <i data-lucide="shopping-bag" style="width: 18px; height: 18px;"></i>
                <span>Mulai Belanja</span>
            </a>
        </div>
    @else
        <div>
            @foreach($orders as $ord)
                @php
                    $status = is_object($ord->status) ? $ord->status->value : $ord->status;
                @endphp
                <div class="order-card-box">
                    <div class="order-card-header">
                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Nomor Pesanan</span>
                            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-main);">
                                {{ $ord->order_number }}
                            </h3>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $ord->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem;">
                            @if($status === 'PENDING')
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: var(--warning-bg); color: var(--warning); border: 1px solid var(--warning);">
                                    Menunggu Pembayaran
                                </span>
                            @elseif(in_array($status, ['CONFIRMED', 'PROCESSING']))
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: var(--success-bg); color: var(--success); border: 1px solid var(--success);">
                                    Dibayar / Diproses
                                </span>
                            @elseif($status === 'SHIPPED')
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: var(--info-bg); color: var(--info); border: 1px solid var(--info);">
                                    Sedang Dikirim
                                </span>
                            @else
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: var(--bg-body); border: 1px solid var(--border-color);">
                                    {{ $status }}
                                </span>
                            @endif

                            <a href="{{ route('orders.show', $ord->order_number) }}" class="btn-auth-solid" style="padding: 0.45rem 1rem; font-size: 0.8125rem; text-decoration: none;">
                                Detail Pesanan
                            </a>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="font-size: 0.875rem;">
                            <span style="color: var(--text-muted);">Barang:</span>
                            <strong>{{ $ord->items->first()?->product_name }}</strong>
                            @if($ord->items->count() > 1)
                                <span style="color: var(--text-muted); font-size: 0.8125rem;">+ {{ $ord->items->count() - 1 }} produk lainnya</span>
                            @endif
                        </div>

                        <div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">Total Tagihan:</span>
                            <strong style="font-size: 1.15rem; color: var(--primary);">
                                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; margin-top: 2rem;">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

@endsection
