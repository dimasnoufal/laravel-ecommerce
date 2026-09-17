@extends('layouts.storefront')

@section('title', $product->name)

@push('styles')
<style>
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .breadcrumb-nav a:hover {
        color: var(--primary);
    }

    .pdp-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 3rem;
        margin-bottom: 3.5rem;
    }
    @media (max-width: 960px) {
        .pdp-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }

    /* Gallery */
    .gallery-main-wrap {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1rem;
    }
    .gallery-main-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 1.5rem;
        transition: transform 0.3s ease;
    }
    .gallery-main-img:hover {
        transform: scale(1.03);
    }
    .gallery-thumbs {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .thumb-item {
        width: 72px;
        height: 72px;
        border-radius: var(--radius-md);
        border: 2px solid var(--border-color);
        background: var(--card-bg);
        cursor: pointer;
        overflow: hidden;
        flex-shrink: 0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .thumb-item.active {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary-light);
    }
    .thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Product Info */
    .pdp-badge-brand {
        display: inline-block;
        padding: 0.3rem 0.65rem;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: var(--radius-sm);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.65rem;
    }
    .pdp-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.25;
        letter-spacing: -0.02em;
        margin-bottom: 0.75rem;
    }
    .pdp-rating-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .pdp-price-box {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
    }
    .pdp-price {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: -0.02em;
    }
    .pdp-sku {
        font-size: 0.8125rem;
        color: var(--text-muted);
        font-family: monospace;
    }

    /* Variant Selector */
    .variant-group {
        margin-bottom: 1.5rem;
    }
    .variant-group-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.65rem;
    }
    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .variant-pill {
        padding: 0.55rem 1rem;
        border: 1.5px solid var(--border-color);
        background: var(--card-bg);
        color: var(--text-main);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .variant-pill:hover {
        border-color: var(--primary);
    }
    .variant-pill.active {
        border-color: var(--primary);
        background: var(--primary-light);
        color: var(--primary);
    }

    /* Quantity and Actions */
    .qty-row {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .qty-picker {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        background: var(--card-bg);
        overflow: hidden;
    }
    .qty-btn {
        width: 38px;
        height: 38px;
        background: none;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-main);
        font-size: 1.1rem;
        transition: background 0.15s;
    }
    .qty-btn:hover {
        background: var(--bg-body);
    }
    .qty-input {
        width: 48px;
        height: 38px;
        text-align: center;
        border: none;
        outline: none;
        background: none;
        font-weight: 700;
        font-size: 0.9375rem;
        color: var(--text-main);
    }
    .stock-indicator {
        font-size: 0.84375rem;
        font-weight: 600;
    }

    .pdp-actions {
        display: flex;
        gap: 1rem;
    }
    .btn-buy-now {
        flex: 1;
        padding: 0.85rem 1.5rem;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: #fff;
        font-weight: 700;
        font-size: 0.9375rem;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        transition: opacity 0.2s, transform 0.2s;
    }
    .btn-buy-now:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .btn-add-cart-lg {
        flex: 1;
        padding: 0.85rem 1.5rem;
        border-radius: var(--radius-md);
        background: var(--card-bg);
        color: var(--primary);
        border: 2px solid var(--primary);
        font-weight: 700;
        font-size: 0.9375rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }
    .btn-add-cart-lg:hover {
        background: var(--primary-light);
    }

    /* Tabs & Content */
    .pdp-tabs-wrap {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 2rem;
        margin-bottom: 3.5rem;
    }
    .pdp-tabs-nav {
        display: flex;
        gap: 2rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.85rem;
        margin-bottom: 1.5rem;
    }
    .pdp-tab-item {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        position: relative;
        padding-bottom: 0.5rem;
    }
    .pdp-tab-item.active {
        color: var(--primary);
    }
    .pdp-tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -0.95rem;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary);
        border-radius: 9999px;
    }
</style>
@endpush

@section('content')

    <!-- Breadcrumb -->
    <nav class="breadcrumb-nav">
        <a href="{{ route('home') }}">Beranda</a>
        <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        @if($product->category)
            <a href="{{ route('home', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
        @endif
        <span style="color: var(--text-main); font-weight: 600;">{{ $product->name }}</span>
    </nav>

    <!-- Main PDP Grid -->
    <div class="pdp-grid">
        <!-- Left: Image Gallery -->
        <div>
            @php
                $firstImage = $product->images->where('is_primary', true)->first() ?: $product->images->first();
                $firstSrc = $firstImage ? (str_starts_with($firstImage->image_path, 'http') ? $firstImage->image_path : asset('storage/' . $firstImage->image_path)) : null;
            @endphp

            <div class="gallery-main-wrap">
                @if($firstSrc)
                    <img id="mainImage" src="{{ $firstSrc }}" alt="{{ $product->name }}" class="gallery-main-img">
                @else
                    <div style="color: var(--text-light); text-align: center;">
                        <i data-lucide="image" style="width: 64px; height: 64px; opacity: 0.35; margin-bottom: 0.5rem;"></i>
                        <p style="font-size: 0.875rem;">Foto Produk Belum Tersedia</p>
                    </div>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="gallery-thumbs">
                    @foreach($product->images as $img)
                        @php
                            $imgSrc = str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path);
                        @endphp
                        <div class="thumb-item {{ $loop->first ? 'active' : '' }}" onclick="switchMainImage('{{ $imgSrc }}', this)">
                            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" class="thumb-img">
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Trust Badges -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-top: 1.5rem;">
                <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; text-align: center;">
                    <i data-lucide="award" style="width: 20px; height: 20px; color: var(--primary); margin: 0 auto 0.35rem auto;"></i>
                    <p style="font-size: 0.75rem; font-weight: 700;">Garansi Resmi</p>
                </div>
                <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; text-align: center;">
                    <i data-lucide="rotate-ccw" style="width: 20px; height: 20px; color: var(--primary); margin: 0 auto 0.35rem auto;"></i>
                    <p style="font-size: 0.75rem; font-weight: 700;">Retur 7 Hari</p>
                </div>
                <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; text-align: center;">
                    <i data-lucide="shield-check" style="width: 20px; height: 20px; color: var(--primary); margin: 0 auto 0.35rem auto;"></i>
                    <p style="font-size: 0.75rem; font-weight: 700;">Original 100%</p>
                </div>
            </div>
        </div>

        <!-- Right: Info & Purchase -->
        <div>
            @if($product->brand)
                <span class="pdp-badge-brand">{{ $product->brand->name }}</span>
            @endif

            <h1 class="pdp-title">{{ $product->name }}</h1>

            <div class="pdp-rating-bar">
                <div style="display: flex; align-items: center; gap: 0.35rem; color: #F59E0B; font-weight: 700;">
                    <i data-lucide="star" style="width: 16px; height: 16px; fill: #F59E0B;"></i>
                    <span>4.9</span>
                </div>
                <span>•</span>
                <span>{{ $product->reviews->count() }} Ulasan Pelanggan</span>
                <span>•</span>
                <span style="color: var(--success); font-weight: 600;">Terverifikasi Original</span>
            </div>

            <!-- Price Box -->
            <div class="pdp-price-box">
                <span class="pdp-price" id="pdpPriceDisplay">
                    Rp {{ number_format($variantsMap[0]['price'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="pdp-sku" id="pdpSkuDisplay">
                    SKU: {{ $variantsMap[0]['sku'] ?? '-' }}
                </span>
            </div>

            <!-- Dynamic Variant Selectors -->
            @if(!empty($attributes))
                @foreach($attributes as $attr)
                    <div class="variant-group">
                        <h4 class="variant-group-title">Pilih {{ $attr['name'] }}:</h4>
                        <div class="variant-options">
                            @foreach($attr['values'] as $val)
                                <button type="button" 
                                        class="variant-pill" 
                                        data-attr-id="{{ $attr['id'] }}" 
                                        data-val-id="{{ $val['id'] }}"
                                        onclick="selectAttribute({{ $attr['id'] }}, {{ $val['id'] }}, this)">
                                    {{ $val['value'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Quantity & Stock -->
            <div class="qty-row">
                <div class="qty-picker">
                    <button type="button" class="qty-btn" onclick="adjustQty(-1)">-</button>
                    <input type="text" id="pdpQtyInput" class="qty-input" value="1" readonly>
                    <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
                </div>
                <div class="stock-indicator" id="pdpStockDisplay">
                    Memuat info stok...
                </div>
            </div>

            <!-- Actions -->
            <div class="pdp-actions">
                <button type="button" class="btn-add-cart-lg" id="btnAddCart" onclick="handleAddToCart()">
                    <i data-lucide="shopping-cart" style="width: 18px; height: 18px;"></i>
                    <span>Tambah ke Keranjang</span>
                </button>
                <button type="button" class="btn-buy-now" id="btnBuyNow" onclick="handleBuyNow()">
                    <i data-lucide="zap" style="width: 18px; height: 18px;"></i>
                    <span>Beli Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Description Tab -->
    <div class="pdp-tabs-wrap">
        <div class="pdp-tabs-nav">
            <div class="pdp-tab-item active">Deskripsi Produk</div>
            <div class="pdp-tab-item">Spesifikasi & Ulasan ({{ $product->reviews->count() }})</div>
        </div>

        <div style="font-size: 0.9375rem; line-height: 1.7; color: var(--text-main);">
            <p style="margin-bottom: 1.25rem;">{{ $product->description }}</p>

            <h4 style="font-size: 1.05rem; font-weight: 700; margin: 1.5rem 0 0.75rem 0;">Keunggulan Produk:</h4>
            <ul style="padding-left: 1.25rem; display: flex; flex-direction: column; gap: 0.4rem; color: var(--text-muted);">
                <li>100% Produk Original bergaransi resmi.</li>
                <li>Dipacking rapi dengan bubble wrap tebal standar industri.</li>
                <li>Mendukung opsi pengiriman ekspres dan reguler ke seluruh pelosok Indonesia.</li>
            </ul>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div style="margin-bottom: 3.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.25rem;">Produk Terkait Lainnya</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem;">
                @foreach($relatedProducts as $rel)
                    @php
                        $relPrice = $rel->variants->min('price');
                        $relImg = $rel->images->first();
                        $relSrc = $relImg ? (str_starts_with($relImg->image_path, 'http') ? $relImg->image_path : asset('storage/' . $relImg->image_path)) : null;
                    @endphp
                    <a href="{{ route('products.show', $rel->slug) }}" class="product-card" style="text-decoration: none;">
                        <div class="product-thumb-wrap">
                            @if($relSrc)
                                <img src="{{ $relSrc }}" alt="{{ $rel->name }}" class="product-thumb">
                            @else
                                <i data-lucide="image" style="width: 32px; height: 32px; opacity: 0.35;"></i>
                            @endif
                        </div>
                        <div class="product-body">
                            <h4 class="product-name">{{ $rel->name }}</h4>
                            <span class="product-price">Rp {{ number_format($relPrice ?: 0, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    const variantsMap = @json($variantsMap);
    let selectedAttributes = {};
    let currentVariant = null;

    function switchMainImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function initVariants() {
        if (variantsMap.length > 0) {
            currentVariant = variantsMap[0];
            
            // Auto-select first variant's attributes
            currentVariant.attribute_value_ids.forEach(valId => {
                const pill = document.querySelector(`.variant-pill[data-val-id="${valId}"]`);
                if (pill) {
                    const attrId = pill.getAttribute('data-attr-id');
                    selectedAttributes[attrId] = valId;
                    pill.classList.add('active');
                }
            });

            updateVariantDisplay();
        }
    }

    function selectAttribute(attrId, valId, el) {
        selectedAttributes[attrId] = valId;
        
        // Toggle active class within same group
        document.querySelectorAll(`.variant-pill[data-attr-id="${attrId}"]`).forEach(p => p.classList.remove('active'));
        el.classList.add('active');

        // Match variant from variantsMap
        const selectedValIds = Object.values(selectedAttributes).map(Number).sort();
        
        const matched = variantsMap.find(v => {
            const vIds = [...v.attribute_value_ids].sort();
            if (vIds.length !== selectedValIds.length) return false;
            return vIds.every((id, idx) => id === selectedValIds[idx]);
        });

        if (matched) {
            currentVariant = matched;
        } else if (variantsMap.length > 0) {
            // Fallback to first variant that has this attribute
            currentVariant = variantsMap.find(v => v.attribute_value_ids.includes(valId)) || variantsMap[0];
        }

        updateVariantDisplay();
    }

    function updateVariantDisplay() {
        if (!currentVariant) return;

        document.getElementById('pdpPriceDisplay').innerText = currentVariant.formatted_price;
        document.getElementById('pdpSkuDisplay').innerText = 'SKU: ' + currentVariant.sku;

        const stockEl = document.getElementById('pdpStockDisplay');
        const btnAdd = document.getElementById('btnAddCart');
        const btnBuy = document.getElementById('btnBuyNow');

        if (currentVariant.stock > 0) {
            stockEl.innerHTML = `<span style="color: var(--success);">Tersedia: ${currentVariant.stock} unit</span>`;
            btnAdd.disabled = false;
            btnBuy.disabled = false;
            btnAdd.style.opacity = '1';
            btnBuy.style.opacity = '1';
        } else {
            stockEl.innerHTML = `<span style="color: var(--danger);">Stok varian ini habis</span>`;
            btnAdd.disabled = true;
            btnBuy.disabled = true;
            btnAdd.style.opacity = '0.5';
            btnBuy.style.opacity = '0.5';
        }
    }

    function adjustQty(amount) {
        const input = document.getElementById('pdpQtyInput');
        let current = parseInt(input.value) || 1;
        let next = current + amount;
        if (next < 1) next = 1;
        if (currentVariant && next > currentVariant.stock) {
            next = currentVariant.stock;
            showToast(`Maksimal pembelian ${currentVariant.stock} unit.`, 'error');
        }
        input.value = next;
    }

    function handleAddToCart() {
        if (!currentVariant) return;
        const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
        addToCart(currentVariant.id, qty, true);
    }

    async function handleBuyNow() {
        if (!currentVariant) return;
        const qty = parseInt(document.getElementById('pdpQtyInput').value) || 1;
        
        // Add to cart first, then redirect to checkout
        try {
            const res = await fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ variant_id: currentVariant.id, quantity: qty })
            });
            const data = await res.json();
            if (data.success) {
                window.location.href = "{{ route('checkout.index') }}";
            } else {
                showToast(data.message, 'error');
            }
        } catch (e) {
            window.location.href = "{{ route('checkout.index') }}";
        }
    }

    document.addEventListener('DOMContentLoaded', initVariants);
</script>
@endpush
