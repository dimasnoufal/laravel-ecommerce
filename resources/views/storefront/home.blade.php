@extends('layouts.storefront')

@section('title', 'Katalog Produk & Promo Terbaru')

@push('styles')
<style>
    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
        border-radius: var(--radius-xl);
        padding: 3rem 3.5rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.03em;
        margin-bottom: 0.85rem;
        max-width: 600px;
    }
    .hero-subtitle {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.9);
        max-width: 520px;
        margin-bottom: 1.75rem;
        line-height: 1.5;
    }
    .hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #ffffff;
        color: #1E40AF;
        font-weight: 700;
        padding: 0.8rem 1.6rem;
        border-radius: var(--radius-md);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hero-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    }
    .hero-decoration {
        position: absolute;
        right: -30px;
        bottom: -40px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Perks Bar */
    .perks-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }
    @media (max-width: 860px) {
        .perks-bar {
            grid-template-columns: repeat(2, 1fr);
        }
        .hero-banner {
            padding: 2.25rem 2rem;
        }
        .hero-title {
            font-size: 1.85rem;
        }
    }
    @media (max-width: 540px) {
        .perks-bar {
            grid-template-columns: 1fr;
        }
    }
    .perk-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
    }
    .perk-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Catalog Section */
    .catalog-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .catalog-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .sort-select {
        padding: 0.5rem 0.85rem;
        border-radius: var(--radius-md);
        border: 1.5px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        font-size: 0.875rem;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }
    .sort-select:focus {
        border-color: var(--primary);
    }

    /* Product Grid */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    /* Product Card */
    .product-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.2s ease;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(37, 99, 235, 0.4);
    }
    .product-thumb-wrap {
        aspect-ratio: 1 / 1;
        width: 100%;
        background: var(--bg-body);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .product-card:hover .product-thumb {
        transform: scale(1.05);
    }
    .product-brand-tag {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(6px);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.55rem;
        border-radius: var(--radius-sm);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .product-body {
        padding: 1.15rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .product-category {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.35rem;
        text-transform: uppercase;
    }
    .product-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.35;
        margin-bottom: 0.65rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.6rem;
    }
    .product-price-row {
        margin-top: auto;
        display: flex;
        align-items: baseline;
        gap: 0.4rem;
        margin-bottom: 0.65rem;
    }
    .product-price {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: -0.02em;
    }
    .product-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78125rem;
        color: var(--text-muted);
        padding-top: 0.65rem;
        border-top: 1px solid var(--border-color);
        margin-bottom: 0.85rem;
    }
    .product-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: #F59E0B;
        font-weight: 700;
    }

    .btn-card-cart {
        width: 100%;
        padding: 0.65rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        background: var(--bg-body);
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.84375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-card-cart:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }
</style>
@endpush

@section('content')

    <!-- Hero Promotional Banner -->
    <div class="hero-banner">
        <div class="hero-decoration"></div>
        <div style="position: relative; z-index: 2;">
            <div class="hero-badge">
                <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i> Promo Eksklusif Hari Ini
            </div>
            <h1 class="hero-title">Belanja Produk Original Terlengkap & Bergaransi</h1>
            <p class="hero-subtitle">
                Temukan smartphone flagship, audio premium, sneakers, hingga kebutuhan harian dengan jaminan keaslian 100% dan bebas ongkir.
            </p>
            <a href="#katalog" class="hero-cta">
                <span>Mulai Belanja</span>
                <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
            </a>
        </div>
        <div style="display: none; align-items: center; justify-content: center;" class="lg-hero-img">
            <i data-lucide="package-check" style="width: 140px; height: 140px; color: rgba(255,255,255,0.85);"></i>
        </div>
    </div>

    <!-- Value Perks Bar -->
    <div class="perks-bar">
        <div class="perk-card">
            <div class="perk-icon-wrap">
                <i data-lucide="shield-check" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.15rem;">100% Original</h4>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Jaminan produk asli langsung dari brand resmi</p>
            </div>
        </div>

        <div class="perk-card">
            <div class="perk-icon-wrap">
                <i data-lucide="truck" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.15rem;">Pengiriman Cepat</h4>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Didukung ekspedisi JNE, SiCepat & kurir instan</p>
            </div>
        </div>

        <div class="perk-card">
            <div class="perk-icon-wrap">
                <i data-lucide="credit-card" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.15rem;">Pembayaran Aman</h4>
                <p style="font-size: 0.75rem; color: var(--text-muted);">BCA, Mandiri, QRIS & Bayar di Tempat (COD)</p>
            </div>
        </div>

        <div class="perk-card">
            <div class="perk-icon-wrap">
                <i data-lucide="headphones" style="width: 22px; height: 22px;"></i>
            </div>
            <div>
                <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.15rem;">Customer Care</h4>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Bantuan ramah & responsif setiap saat</p>
            </div>
        </div>
    </div>

    <!-- Catalog Section -->
    <div id="katalog">
        <div class="catalog-header">
            <div>
                <h2 class="catalog-title">
                    @if(request('q'))
                        Hasil Pencarian: "{{ request('q') }}"
                    @elseif(request('category'))
                        Kategori: {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Produk' }}
                    @else
                        Katalog Produk Unggulan
                    @endif
                </h2>
                <p style="font-size: 0.84375rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Menampilkan {{ $products->total() }} produk berkualitas
                </p>
            </div>

            <!-- Sort Filter -->
            <form method="GET" action="{{ route('home') }}" id="sortForm">
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif

                <select name="sort" class="sort-select" onchange="document.getElementById('sortForm').submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Terendah ke Tertinggi</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tertinggi ke Terendah</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A - Z</option>
                </select>
            </form>
        </div>

        @if($products->isEmpty())
            <div style="text-align: center; padding: 5rem 1rem; background: var(--card-bg); border-radius: var(--radius-xl); border: 1px solid var(--border-color);">
                <i data-lucide="package-x" style="width: 56px; height: 56px; margin: 0 auto 1rem auto; opacity: 0.35;"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem;">Tidak ada produk yang ditemukan</h3>
                <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Coba ganti kata kunci pencarian Anda atau reset filter yang dipilih.
                </p>
                <a href="{{ route('home') }}" class="btn-auth-solid" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                    <span>Tampilkan Semua Produk</span>
                </a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $product)
                    @php
                        $primaryVariant = $product->variants->first();
                        $minPrice = $product->variants->min('price');
                        $maxPrice = $product->variants->max('price');
                        $totalStock = $product->variants->sum('stock');
                        $primaryImage = $product->images->where('is_primary', true)->first() ?: $product->images->first();
                        $imageSrc = null;
                        if ($primaryImage) {
                            $imageSrc = str_starts_with($primaryImage->image_path, 'http')
                                ? $primaryImage->image_path
                                : asset('storage/' . $primaryImage->image_path);
                        }
                    @endphp

                    <div class="product-card">
                        <a href="{{ route('products.show', $product->slug) }}" class="product-thumb-wrap">
                            @if($product->brand)
                                <span class="product-brand-tag">{{ $product->brand->name }}</span>
                            @endif

                            @if($imageSrc)
                                <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="product-thumb" loading="lazy">
                            @else
                                <div style="color: var(--text-light); display: flex; flex-direction: column; align-items: center; gap: 0.4rem;">
                                    <i data-lucide="image" style="width: 36px; height: 36px; opacity: 0.4;"></i>
                                    <span style="font-size: 0.75rem;">Foto Produk</span>
                                </div>
                            @endif
                        </a>

                        <div class="product-body">
                            @if($product->category)
                                <span class="product-category">{{ $product->category->name }}</span>
                            @endif

                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="product-name" title="{{ $product->name }}">{{ $product->name }}</h3>
                            </a>

                            <div class="product-price-row">
                                <span class="product-price">
                                    Rp {{ number_format($minPrice ?: ($primaryVariant?->price ?? 0), 0, ',', '.') }}
                                </span>
                                @if($minPrice && $maxPrice && $minPrice < $maxPrice)
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">- {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                @endif
                            </div>

                            <div class="product-meta-row">
                                <div class="product-rating">
                                    <i data-lucide="star" style="width: 14px; height: 14px; fill: #F59E0B;"></i>
                                    <span>4.9</span>
                                    <span style="color: var(--text-light); font-weight: 500;">(24)</span>
                                </div>
                                <div>
                                    @if($totalStock > 0)
                                        <span style="color: var(--success); font-weight: 600;">Stok: {{ $totalStock }}</span>
                                    @else
                                        <span style="color: var(--danger); font-weight: 600;">Habis</span>
                                    @endif
                                </div>
                            </div>

                            @if($primaryVariant && $totalStock > 0)
                                <button type="button" class="btn-card-cart" onclick="addToCart({{ $primaryVariant->id }}, 1, true)">
                                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                                    <span>Tambah Keranjang</span>
                                </button>
                            @else
                                <button type="button" class="btn-card-cart" style="opacity: 0.6; cursor: not-allowed;" disabled>
                                    <span>Stok Habis</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="display: flex; justify-content: center; margin-top: 2rem;">
                {{ $products->links() }}
            </div>
        @endif
    </div>

@endsection
