@extends('layouts.storefront')

@section('title', 'Checkout Pengiriman & Pembayaran')

@push('styles')
<style>
    .checkout-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2.5rem;
        align-items: start;
    }
    @media (max-width: 960px) {
        .checkout-layout {
            grid-template-columns: 1fr;
        }
    }

    .checkout-step-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        padding: 1.75rem;
        margin-bottom: 1.75rem;
    }

    .step-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-color);
    }
    .step-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .step-badge {
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        background: var(--primary);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Address Cards */
    .address-card-option {
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.15rem;
        margin-bottom: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
    }
    .address-card-option:hover {
        border-color: var(--primary);
    }
    .address-card-option.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    /* Shipping Courier Cards */
    .carrier-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    .shipping-option-card {
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1rem 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
        background: var(--card-bg);
    }
    .shipping-option-card:hover {
        border-color: var(--primary);
    }
    .shipping-option-card.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    /* Payment Radio Card */
    .payment-option-card {
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.85rem 1.15rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
        background: var(--card-bg);
    }
    .payment-option-card:hover {
        border-color: var(--primary);
    }
    .payment-option-card.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    /* Sticky Summary Card */
    .checkout-summary-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-sm);
        padding: 1.75rem;
        position: sticky;
        top: 96px;
    }

    /* Address Modal */
    .form-group-field {
        margin-bottom: 1rem;
    }
    .form-group-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .form-group-input {
        width: 100%;
        padding: 0.65rem 0.85rem;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-md);
        background: var(--bg-body);
        color: var(--text-main);
        font-size: 0.9375rem;
        outline: none;
    }
    .form-group-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
</style>
@endpush

@section('content')

    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.35rem;">
            Checkout & Pembayaran
        </h1>
        <p style="font-size: 0.875rem; color: var(--text-muted);">
            Lengkapi alamat pengiriman dan pilih metode pembayaran favorit Anda.
        </p>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <input type="hidden" name="address_id" id="selectedAddressId" value="{{ $addresses->first()?->id }}">
        <input type="hidden" name="shipping_service_id" id="selectedShippingServiceId" value="">
        <input type="hidden" name="payment_provider" id="selectedPaymentProvider" value="BCA_VA">

        <div class="checkout-layout">
            <!-- Left: Steps -->
            <div>
                <!-- 1. Delivery Address Step -->
                <div class="checkout-step-card">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">1</div>
                            <span>Alamat Pengiriman</span>
                        </div>
                        <button type="button" class="btn-auth-outline" onclick="openAddressModal()" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">
                            <i data-lucide="plus" style="width: 14px; height: 14px; display: inline;"></i> Tambah Alamat Baru
                        </button>
                    </div>

                    @if($addresses->isEmpty())
                        <div style="text-align: center; padding: 2.5rem 1rem; border: 2px dashed var(--border-color); border-radius: var(--radius-lg);">
                            <i data-lucide="map-pin" style="width: 42px; height: 42px; margin: 0 auto 0.75rem auto; opacity: 0.35;"></i>
                            <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.4rem;">Belum ada alamat pengiriman</h4>
                            <p style="font-size: 0.84375rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                                Tambahkan alamat pengiriman Anda dengan sistem data wilayah Indonesia lengkap.
                            </p>
                            <button type="button" class="btn-auth-solid" onclick="openAddressModal()">
                                + Tambah Alamat Sekarang
                            </button>
                        </div>
                    @else
                        <div>
                            @foreach($addresses as $addr)
                                @php
                                    $v = $addr->village;
                                    $d = $v?->district;
                                    $r = $d?->regency;
                                    $p = $r?->province;
                                    $fullLocation = implode(', ', array_filter([
                                        $v?->name,
                                        $d?->name,
                                        $r?->name,
                                        $p?->name,
                                        $v?->postal_code
                                    ]));
                                @endphp
                                <div class="address-card-option {{ $loop->first ? 'selected' : '' }}" 
                                     id="addr-card-{{ $addr->id }}"
                                     onclick="selectAddress({{ $addr->id }})">
                                    <input type="radio" name="temp_address" value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }} style="margin-top: 4px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                                            <strong style="font-size: 0.9375rem;">{{ $addr->recipient_name }}</strong>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">({{ $addr->phone }})</span>
                                            <span style="font-size: 0.7rem; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.15rem 0.5rem; border-radius: var(--radius-sm); font-weight: 700;">
                                                {{ $addr->label }}
                                            </span>
                                            @if($addr->is_default)
                                                <span style="font-size: 0.7rem; background: var(--success-bg); color: var(--success); padding: 0.15rem 0.5rem; border-radius: var(--radius-sm); font-weight: 700;">
                                                    Utama
                                                </span>
                                            @endif
                                        </div>
                                        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.45;">
                                            {{ $addr->address_line }}
                                        </p>
                                        <p style="font-size: 0.8125rem; color: var(--text-light); margin-top: 0.2rem;">
                                            {{ $fullLocation }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 2. Shipping Courier Step -->
                <div class="checkout-step-card">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">2</div>
                            <span>Pilih Kurir Pengiriman</span>
                        </div>
                    </div>

                    <div class="carrier-grid">
                        @php $firstServiceId = null; @endphp
                        @foreach($carriers as $carrier)
                            @foreach($carrier->services as $service)
                                @php
                                    $svcRate = match(strtoupper($service->code)) {
                                        'YES', 'ONS', 'BEST' => 32000,
                                        'REG' => 18000,
                                        'OKE' => 12000,
                                        'CARGO', 'JTR' => 40000,
                                        default => 20000
                                    };
                                    if (!$firstServiceId) $firstServiceId = $service->id;
                                @endphp

                                <div class="shipping-option-card {{ $firstServiceId === $service->id ? 'selected' : '' }}" 
                                     id="shipping-card-{{ $service->id }}"
                                     onclick="selectShipping({{ $service->id }}, {{ $svcRate }}, '{{ $carrier->name }} - {{ $service->name }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <input type="radio" name="temp_shipping" value="{{ $service->id }}" {{ $firstServiceId === $service->id ? 'checked' : '' }}>
                                        <div>
                                            <div style="font-size: 0.9375rem; font-weight: 700; color: var(--text-main);">
                                                {{ $carrier->name }} - {{ $service->name }}
                                            </div>
                                            <span style="font-size: 0.78125rem; color: var(--text-muted);">
                                                Estimasi: {{ $service->estimated_min_days }}-{{ $service->estimated_max_days }} hari kerja
                                            </span>
                                        </div>
                                    </div>
                                    <div style="font-size: 0.95rem; font-weight: 800; color: var(--primary);">
                                        Rp {{ number_format($svcRate, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <!-- 3. Payment Method Step -->
                <div class="checkout-step-card">
                    <div class="step-header">
                        <div class="step-title">
                            <div class="step-badge">3</div>
                            <span>Metode Pembayaran</span>
                        </div>
                    </div>

                    @foreach($paymentMethods as $group)
                        <div style="margin-bottom: 1.25rem;">
                            <h4 style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.65rem; letter-spacing: 0.04em;">
                                {{ $group['group'] }}
                            </h4>
                            @foreach($group['options'] as $opt)
                                <div class="payment-option-card {{ $opt['code'] === 'BCA_VA' ? 'selected' : '' }}"
                                     id="pay-card-{{ $opt['code'] }}"
                                     onclick="selectPayment('{{ $opt['code'] }}')">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <input type="radio" name="temp_payment" value="{{ $opt['code'] }}" {{ $opt['code'] === 'BCA_VA' ? 'checked' : '' }}>
                                        <i data-lucide="{{ $opt['icon'] }}" style="width: 18px; height: 18px; color: var(--primary);"></i>
                                        <span style="font-size: 0.9375rem; font-weight: 600;">{{ $opt['name'] }}</span>
                                    </div>
                                    <span style="font-size: 0.75rem; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.2rem 0.5rem; border-radius: var(--radius-sm); font-weight: 600;">
                                        {{ $opt['badge'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Order Summary Breakdown -->
            <div>
                <div class="checkout-summary-card">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
                        Ringkasan Pesanan
                    </h3>

                    <!-- Brief items list -->
                    <div style="max-height: 240px; overflow-y: auto; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem; padding-right: 0.35rem;">
                        @foreach($cart['items'] as $item)
                            <div style="display: flex; gap: 0.75rem; align-items: center;">
                                <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--bg-body); border: 1px solid var(--border-color); overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                    @if($item['image'])
                                        <img src="{{ $item['image'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i data-lucide="image" style="width: 18px; height: 18px; opacity: 0.4;"></i>
                                    @endif
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="font-size: 0.8125rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $item['product_name'] }}
                                    </p>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <span style="font-size: 0.8125rem; font-weight: 700;">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.65rem;">
                        <span style="color: var(--text-muted);">Subtotal Produk</span>
                        <strong id="summarySubtotal" data-value="{{ $cart['subtotal'] }}">
                            {{ $cart['formatted_subtotal'] }}
                        </strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.65rem;">
                        <span style="color: var(--text-muted);">Biaya Pengiriman</span>
                        <strong id="summaryShippingCost" style="color: var(--primary);">Rp 0</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.65rem;">
                        <span style="color: var(--text-muted);">Biaya Layanan</span>
                        <strong style="color: var(--success);">Gratis</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; margin-top: 1.25rem; padding-top: 1rem; border-top: 1.5px dashed var(--border-color);">
                        <span>Total Pembayaran</span>
                        <span style="color: var(--primary);" id="summaryTotalAmount">
                            {{ $cart['formatted_subtotal'] }}
                        </span>
                    </div>

                    <button type="submit" class="btn-checkout-primary" id="btnPlaceOrder" style="margin-top: 1.5rem;">
                        <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                        <span>Bayar Sekarang</span>
                    </button>

                    <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 0.85rem; line-height: 1.4;">
                        Dengan mengklik Bayar Sekarang, Anda menyetujui Syarat & Ketentuan transaksi yang berlaku.
                    </p>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Tambah Alamat Baru dengan Cascading Kemendagri -->
    <div class="modal-overlay" id="newAddressModalOverlay">
        <div class="modal-card" style="max-width: 540px;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.15rem; font-weight: 700;">Tambah Alamat Pengiriman Baru</h3>
                <button type="button" class="icon-btn" onclick="closeAddressModal()" style="width: 32px; height: 32px; border: none;">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>

            <!-- Nested form for new address -->
            <form id="newAddressForm" style="padding: 1.5rem; max-height: 70vh; overflow-y: auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-field">
                        <label class="form-group-label">Nama Penerima</label>
                        <input type="text" id="modalRecipientName" class="form-group-input" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="form-group-field">
                        <label class="form-group-label">Nomor Telepon / WhatsApp</label>
                        <input type="tel" id="modalPhone" class="form-group-input" placeholder="081234567890" required>
                    </div>
                </div>

                <div class="form-group-field">
                    <label class="form-group-label">Label Alamat</label>
                    <input type="text" id="modalLabel" class="form-group-input" placeholder="Rumah, Kantor, Kos, dll" value="Rumah" required>
                </div>

                <!-- Cascading Wilayah 4 Level -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-field">
                        <label class="form-group-label">Provinsi</label>
                        <select id="modalProvinceSelect" class="form-group-input" required onchange="handleProvinceChange()">
                            <option value="">Pilih Provinsi...</option>
                        </select>
                    </div>
                    <div class="form-group-field">
                        <label class="form-group-label">Kota / Kabupaten</label>
                        <select id="modalRegencySelect" class="form-group-input" required onchange="handleRegencyChange()" disabled>
                            <option value="">Pilih Kota/Kabupaten...</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-field">
                        <label class="form-group-label">Kecamatan</label>
                        <select id="modalDistrictSelect" class="form-group-input" required onchange="handleDistrictChange()" disabled>
                            <option value="">Pilih Kecamatan...</option>
                        </select>
                    </div>
                    <div class="form-group-field">
                        <label class="form-group-label">Desa / Kelurahan</label>
                        <select id="modalVillageSelect" class="form-group-input" required disabled>
                            <option value="">Pilih Desa/Kelurahan...</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-field">
                    <label class="form-group-label">Alamat Lengkap (Jalan, RT/RW, No. Rumah)</label>
                    <textarea id="modalAddressLine" class="form-group-input" rows="3" placeholder="Jl. Mawar No. 12, RT 02/RW 05..." required></textarea>
                </div>

                <button type="button" class="btn-auth-solid" style="width: 100%; padding: 0.85rem; font-weight: 700; margin-top: 0.5rem;" onclick="saveNewAddressInline()">
                    Simpan Alamat & Gunakan
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    let currentShippingCost = 0;
    const subtotal = parseFloat(document.getElementById('summarySubtotal').dataset.value) || 0;

    function selectAddress(id) {
        document.getElementById('selectedAddressId').value = id;
        document.querySelectorAll('.address-card-option').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById(`addr-card-${id}`);
        if (card) {
            card.classList.add('selected');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    }

    function selectShipping(serviceId, cost, name) {
        document.getElementById('selectedShippingServiceId').value = serviceId;
        currentShippingCost = cost;

        document.querySelectorAll('.shipping-option-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById(`shipping-card-${serviceId}`);
        if (card) {
            card.classList.add('selected');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        // Update summary
        document.getElementById('summaryShippingCost').innerText = 'Rp ' + cost.toLocaleString('id-ID');
        const total = subtotal + cost;
        document.getElementById('summaryTotalAmount').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function selectPayment(code) {
        document.getElementById('selectedPaymentProvider').value = code;
        document.querySelectorAll('.payment-option-card').forEach(c => c.classList.remove('selected'));
        const card = document.getElementById(`pay-card-${code}`);
        if (card) {
            card.classList.add('selected');
            const radio = card.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    }

    // Initialize first courier on load
    document.addEventListener('DOMContentLoaded', () => {
        const firstShipping = document.querySelector('.shipping-option-card');
        if (firstShipping) {
            firstShipping.click();
        }
    });

    // Cascading Address Modal Logic
    const newAddressModalOverlay = document.getElementById('newAddressModalOverlay');

    function openAddressModal() {
        newAddressModalOverlay?.classList.add('active');
        loadProvinces();
    }

    function closeAddressModal() {
        newAddressModalOverlay?.classList.remove('active');
    }

    async function loadProvinces() {
        const select = document.getElementById('modalProvinceSelect');
        if (select.children.length > 1) return; // already loaded

        try {
            const res = await fetch("{{ route('api.regions.provinces') }}");
            const data = await res.json();
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.innerText = p.name;
                select.appendChild(opt);
            });
        } catch (e) {
            console.error('Failed to load provinces', e);
        }
    }

    async function handleProvinceChange() {
        const pId = document.getElementById('modalProvinceSelect').value;
        const regSelect = document.getElementById('modalRegencySelect');
        const distSelect = document.getElementById('modalDistrictSelect');
        const vilSelect = document.getElementById('modalVillageSelect');

        regSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
        distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
        vilSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';

        regSelect.disabled = true;
        distSelect.disabled = true;
        vilSelect.disabled = true;

        if (!pId) return;

        try {
            const res = await fetch(`/api/regions/provinces/${pId}/regencies`);
            const data = await res.json();
            data.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.innerText = r.name;
                regSelect.appendChild(opt);
            });
            regSelect.disabled = false;
        } catch (e) {
            console.error('Failed to load regencies', e);
        }
    }

    async function handleRegencyChange() {
        const rId = document.getElementById('modalRegencySelect').value;
        const distSelect = document.getElementById('modalDistrictSelect');
        const vilSelect = document.getElementById('modalVillageSelect');

        distSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
        vilSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';

        distSelect.disabled = true;
        vilSelect.disabled = true;

        if (!rId) return;

        try {
            const res = await fetch(`/api/regions/regencies/${rId}/districts`);
            const data = await res.json();
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id;
                opt.innerText = d.name;
                distSelect.appendChild(opt);
            });
            distSelect.disabled = false;
        } catch (e) {
            console.error('Failed to load districts', e);
        }
    }

    async function handleDistrictChange() {
        const dId = document.getElementById('modalDistrictSelect').value;
        const vilSelect = document.getElementById('modalVillageSelect');

        vilSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';
        vilSelect.disabled = true;

        if (!dId) return;

        try {
            const res = await fetch(`/api/regions/districts/${dId}/villages`);
            const data = await res.json();
            data.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.innerText = v.name;
                vilSelect.appendChild(opt);
            });
            vilSelect.disabled = false;
        } catch (e) {
            console.error('Failed to load villages', e);
        }
    }

    function saveNewAddressInline() {
        const recipient = document.getElementById('modalRecipientName').value.trim();
        const phone = document.getElementById('modalPhone').value.trim();
        const label = document.getElementById('modalLabel').value.trim();
        const villageId = document.getElementById('modalVillageSelect').value;
        const addressLine = document.getElementById('modalAddressLine').value.trim();

        if (!recipient || !phone || !villageId || !addressLine) {
            alert('Mohon lengkapi semua isian alamat termasuk provinsi hingga kelurahan.');
            return;
        }

        // Inject hidden inputs to the main checkout form
        const form = document.getElementById('checkoutForm');
        document.getElementById('selectedAddressId').value = ''; // clear existing

        // Create new address inputs
        const fields = {
            'new_address[recipient_name]': recipient,
            'new_address[phone]': phone,
            'new_address[label]': label,
            'new_address[village_id]': villageId,
            'new_address[address_line]': addressLine
        };

        for (const [key, val] of Object.entries(fields)) {
            let input = document.getElementById('hidden_' + key);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.id = 'hidden_' + key;
                form.appendChild(input);
            }
            input.value = val;
        }

        closeAddressModal();
        showToast('Alamat baru berhasil ditambahkan untuk pesanan ini.', 'success');
    }
</script>
@endpush
