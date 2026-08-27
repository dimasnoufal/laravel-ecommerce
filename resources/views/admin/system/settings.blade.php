@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Toko')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    /* Settings Nav Tabs */
    .settings-nav-tabs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.5rem;
        overflow-x: auto;
    }
    .settings-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.25rem;
        background: transparent;
        border: none;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .settings-tab-btn:hover {
        background: var(--bg-body);
        color: var(--text-main);
    }
    .settings-tab-btn.active {
        background: var(--primary-light);
        color: var(--primary);
    }

    /* Leaflet Map Box Container */
    .map-picker-box {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        margin-top: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }
    #warehouseMap {
        width: 100%;
        height: 380px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: var(--radius-md);
        font-family: inherit;
    }

    /* Cache Action Card */
    .cache-action-card {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        transition: all 0.2s ease;
    }
    .cache-action-card:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
    .cache-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* System Info Diagnostic Grid */
    .diagnostic-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.85rem;
    }
    @media (max-width: 900px) {
        .diagnostic-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 600px) {
        .diagnostic-grid {
            grid-template-columns: 1fr;
        }
    }

    .diagnostic-item {
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .diagnostic-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .diagnostic-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-main);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Standardized Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>System & Compliance</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Settings</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Pengaturan Sistem & Toko</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola identitas profil toko, titik koordinat GPS gudang pengiriman, parameter transaksi, dan cache sistem.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="server" style="width: 16px; height: 16px; color: var(--primary);"></i>
                <span>PHP {{ $systemInfo['php_version'] }} • Laravel {{ $systemInfo['laravel_version'] }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs Bar -->
    <div class="settings-nav-tabs">
        <button type="button" class="settings-tab-btn active" onclick="switchSettingsTab('store', this)">
            <i data-lucide="store" style="width: 16px; height: 16px;"></i>
            <span>Profil Toko & Lokasi Gudang</span>
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('transaction', this)">
            <i data-lucide="credit-card" style="width: 16px; height: 16px;"></i>
            <span>Transaksi & Pajak</span>
        </button>
        <button type="button" class="settings-tab-btn" onclick="switchSettingsTab('system', this)">
            <i data-lucide="cpu" style="width: 16px; height: 16px;"></i>
            <span>Pemeliharaan & Cache Sistem</span>
        </button>
    </div>

    <!-- TAB 1: Store Profile & Warehouse Map -->
    <div id="tabContent-store" class="tab-pane-settings">
        <div class="panel-card" style="padding: 1.75rem;">
            <div class="panel-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <div>
                    <h2 class="panel-title" style="font-size: 1.25rem;">Informasi Identitas & Lokasi Gudang Utama</h2>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                        Data ini digunakan pada faktur/invoice penjualan, email notifikasi, dan rujukan koordinat GPS logistik.
                    </p>
                </div>
            </div>

            <form id="storeSettingsForm" method="POST" action="{{ route('admin.settings.update') }}" onsubmit="handleSettingsSave(event, 'storeSettingsForm', 'submitStoreBtn')">
                @csrf
                <input type="hidden" name="group" value="general">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Nama Toko Online <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="store_name" class="form-control" value="{{ $settings['general']['store_name'] ?? 'Laravel E-Commerce' }}" required placeholder="Contoh: Toko Online Saya">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Slogan / Tagline Toko</label>
                        <input type="text" name="store_tagline" class="form-control" value="{{ $settings['general']['store_tagline'] ?? '' }}" placeholder="Contoh: Belanja Mudah & Cepat">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Email Resmi Customer Support <span style="color: var(--danger);">*</span></label>
                        <input type="email" name="store_email" class="form-control" value="{{ $settings['general']['store_email'] ?? 'support@ecommerce.local' }}" required placeholder="cs@domain.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp / Telepon CS <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="store_phone" class="form-control" value="{{ $settings['general']['store_phone'] ?? '081234567890' }}" required placeholder="081234567890">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap Kantor / Gudang Utama <span style="color: var(--danger);">*</span></label>
                        <textarea name="store_address" class="form-control" rows="2" required placeholder="Masukkan alamat lengkap kantor atau gudang pengiriman...">{{ $settings['general']['store_address'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kota Asal Logistik</label>
                        <input type="text" name="warehouse_city" class="form-control" value="{{ $settings['general']['warehouse_city'] ?? 'Jakarta Selatan' }}" placeholder="Contoh: Jakarta Selatan">
                        <small style="color: var(--text-muted); font-size: 0.72rem; margin-top: 0.2rem; display: block;">Rujukan tarif ongkir kurir.</small>
                    </div>
                </div>

                <!-- Leaflet Interactive Map Picker Container -->
                <div class="map-picker-box">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; font-size: 0.875rem; color: var(--text-main);">
                                <i data-lucide="map-pin" style="width: 16px; height: 16px; color: var(--primary);"></i>
                                <span>Peta Titik Lokasi Gudang (Google Maps Traffic Layer)</span>
                            </div>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.15rem 0 0 0;">
                                Geser pin marker merah atau klik di peta untuk menentukan koordinat presisi gudang asal pengiriman.
                            </p>
                        </div>

                        <!-- Map Quick Action Buttons -->
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" class="btn-secondary" onclick="locateUserGPS()" style="padding: 0.4rem 0.75rem; font-size: 0.78125rem;">
                                <i data-lucide="navigation" style="width: 13px; height: 13px; color: var(--primary);"></i>
                                <span>Deteksi GPS Saya</span>
                            </button>
                            <button type="button" class="btn-secondary" onclick="resetDefaultLocation()" style="padding: 0.4rem 0.75rem; font-size: 0.78125rem;">
                                <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i>
                                <span>Reset Titik</span>
                            </button>
                        </div>
                    </div>

                    <!-- Map DOM Container -->
                    <div id="warehouseMap"></div>

                    <!-- Coordinates Input Row (2-Way Synced) -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.25rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.75rem;">Latitude (Garis Lintang)</label>
                            <input type="text" id="warehouseLatInput" name="warehouse_latitude" class="form-control" value="{{ $settings['general']['warehouse_latitude'] ?? '-6.2297465' }}" style="font-family: monospace; font-size: 0.8125rem;" onchange="handleManualCoordinateInput()">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-size: 0.75rem;">Longitude (Garis Bujur)</label>
                            <input type="text" id="warehouseLngInput" name="warehouse_longitude" class="form-control" value="{{ $settings['general']['warehouse_longitude'] ?? '106.8164494' }}" style="font-family: monospace; font-size: 0.8125rem;" onchange="handleManualCoordinateInput()">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" id="submitStoreBtn" class="btn-primary" style="padding: 0.6rem 1.5rem;">
                        <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                        <span>Simpan Profil & Koordinat Toko</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: Transactions & Tax -->
    <div id="tabContent-transaction" class="tab-pane-settings" style="display: none;">
        <div class="panel-card" style="padding: 1.75rem;">
            <div class="panel-header" style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                <div>
                    <h2 class="panel-title" style="font-size: 1.25rem;">Parameter Transaksi, Pembayaran & Pajak</h2>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                        Atur mata uang, persentase pajak pertambahan nilai, batas waktu pembayaran, dan batas minimum belanja.
                    </p>
                </div>
            </div>

            <form id="transactionSettingsForm" method="POST" action="{{ route('admin.settings.update') }}" onsubmit="handleSettingsSave(event, 'transactionSettingsForm', 'submitTxBtn')">
                @csrf
                <input type="hidden" name="group" value="transaction">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Kode Mata Uang <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="currency_code" class="form-control" value="{{ $settings['transaction']['currency_code'] ?? 'IDR' }}" required placeholder="IDR">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Simbol Mata Uang <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['transaction']['currency_symbol'] ?? 'Rp' }}" required placeholder="Rp">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Persentase Pajak PPN (%) <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="tax_percentage" class="form-control" value="{{ $settings['transaction']['tax_percentage'] ?? 0 }}" min="0" max="100" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Batas Waktu Bayar Expired (Jam) <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="payment_expiry_hours" class="form-control" value="{{ $settings['transaction']['payment_expiry_hours'] ?? 24 }}" min="1" max="168" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Minimal Belanja (Rp) <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="min_order_amount" class="form-control" value="{{ $settings['transaction']['min_order_amount'] ?? 10000 }}" min="0" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" id="submitTxBtn" class="btn-primary" style="padding: 0.6rem 1.5rem;">
                        <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                        <span>Simpan Parameter Transaksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 3: System Tools & Maintenance -->
    <div id="tabContent-system" class="tab-pane-settings" style="display: none;">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- Cache Cleaner Tools Panel -->
            <div class="panel-card" style="padding: 1.75rem;">
                <div class="panel-header" style="margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h2 class="panel-title" style="font-size: 1.25rem;">Pemeliharaan & Pembersihan Cache Sistem</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Gunakan tombol di bawah untuk membersihkan cache aplikasi dan mengoptimalkan performa.
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    
                    <!-- Total Optimize Clear -->
                    <div class="cache-action-card" style="grid-column: span 2; background: var(--primary-light); border-color: rgba(37, 99, 235, 0.3);">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="cache-icon-box" style="background: var(--primary); color: #ffffff;">
                                <i data-lucide="zap" style="width: 22px; height: 22px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.95rem; color: var(--text-main); display: block;">Bersihkan Seluruh Cache Sistem (Optimize Clear)</strong>
                                <span style="font-size: 0.78125rem; color: var(--text-muted);">Membersihkan cache aplikasi, kompilasi Blade view, cache rute, dan config secara tuntas.</span>
                            </div>
                        </div>
                        <button type="button" class="btn-primary" onclick="triggerClearCache('all')" style="flex-shrink: 0; padding: 0.55rem 1.15rem;">
                            <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                            <span>Jalankan Optimize Clear</span>
                        </button>
                    </div>

                    <!-- Clear App & Setting Cache -->
                    <div class="cache-action-card">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div class="cache-icon-box" style="background: var(--info-bg); color: var(--info);">
                                <i data-lucide="database" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.875rem; color: var(--text-main); display: block;">Cache Aplikasi & Setting</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Reset cache key-value & settings</span>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="triggerClearCache('app')" style="flex-shrink: 0; font-size: 0.78125rem; padding: 0.45rem 0.85rem;">
                            <span>Bersihkan</span>
                        </button>
                    </div>

                    <!-- Clear View Cache -->
                    <div class="cache-action-card">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div class="cache-icon-box" style="background: var(--success-bg); color: var(--success);">
                                <i data-lucide="layout" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.875rem; color: var(--text-main); display: block;">Kompilasi Blade Views</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Reset seluruh compiled blade files</span>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="triggerClearCache('view')" style="flex-shrink: 0; font-size: 0.78125rem; padding: 0.45rem 0.85rem;">
                            <span>Bersihkan</span>
                        </button>
                    </div>

                    <!-- Clear Route Cache -->
                    <div class="cache-action-card">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div class="cache-icon-box" style="background: var(--warning-bg); color: var(--warning);">
                                <i data-lucide="map" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.875rem; color: var(--text-main); display: block;">Cache Rute (Route Cache)</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Reset daftar cache routing</span>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="triggerClearCache('route')" style="flex-shrink: 0; font-size: 0.78125rem; padding: 0.45rem 0.85rem;">
                            <span>Bersihkan</span>
                        </button>
                    </div>

                    <!-- Clear Config Cache -->
                    <div class="cache-action-card">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div class="cache-icon-box" style="background: var(--primary-light); color: var(--primary);">
                                <i data-lucide="sliders" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <strong style="font-size: 0.875rem; color: var(--text-main); display: block;">Cache Konfigurasi (Config)</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Reset cache konfigurasi .env</span>
                            </div>
                        </div>
                        <button type="button" class="btn-secondary" onclick="triggerClearCache('config')" style="flex-shrink: 0; font-size: 0.78125rem; padding: 0.45rem 0.85rem;">
                            <span>Bersihkan</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Server Diagnostics Card -->
            <div class="panel-card" style="padding: 1.75rem;">
                <div class="panel-header" style="margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h2 class="panel-title" style="font-size: 1.25rem;">Status Environment & Server Diagnostics</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                            Informasi teknis runtime environment PHP, Laravel, dan database sistem.
                        </p>
                    </div>
                </div>

                <div class="diagnostic-grid">
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Versi PHP</span>
                        <span class="diagnostic-value">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Versi Laravel Framework</span>
                        <span class="diagnostic-value">v{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Environment Mode</span>
                        <span class="diagnostic-value">{{ strtoupper($systemInfo['environment']) }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Driver Database</span>
                        <span class="diagnostic-value">{{ $systemInfo['database_driver'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Driver Cache</span>
                        <span class="diagnostic-value">{{ $systemInfo['cache_driver'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Memory Usage</span>
                        <span class="diagnostic-value">{{ $systemInfo['memory_usage'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Max File Upload</span>
                        <span class="diagnostic-value">{{ $systemInfo['upload_max_filesize'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Max Execution Time</span>
                        <span class="diagnostic-value">{{ $systemInfo['max_execution_time'] }}</span>
                    </div>
                    <div class="diagnostic-item">
                        <span class="diagnostic-label">Timezone Sistem</span>
                        <span class="diagnostic-value">{{ $systemInfo['timezone'] }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let warehouseMap;
    let warehouseMarker;
    const defaultLat = parseFloat("{{ $settings['general']['warehouse_latitude'] ?? '-6.2297465' }}") || -6.2297465;
    const defaultLng = parseFloat("{{ $settings['general']['warehouse_longitude'] ?? '106.8164494' }}") || 106.8164494;

    $(document).ready(function() {
        initWarehouseMap();
    });

    function initWarehouseMap() {
        const initialLat = parseFloat($('#warehouseLatInput').val()) || defaultLat;
        const initialLng = parseFloat($('#warehouseLngInput').val()) || defaultLng;

        warehouseMap = L.map('warehouseMap', {
            center: [initialLat, initialLng],
            zoom: 15,
            zoomControl: true
        });

        // Google Maps Traffic Tile Layer requested by User
        const googleTrafficLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Traffic'
        });

        googleTrafficLayer.addTo(warehouseMap);

        // Add Draggable Red Pin Marker
        warehouseMarker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(warehouseMap);

        warehouseMarker.bindPopup(`
            <div style="font-size: 0.8125rem; font-weight: 600; text-align: center;">
                <strong>Gudang & Kantor Utama</strong><br>
                <span style="color: #64748b; font-size: 0.75rem;">Geser pin ini untuk memindahkan titik</span>
            </div>
        `).openPopup();

        // Marker Drag Event
        warehouseMarker.on('dragend', function(e) {
            const position = warehouseMarker.getLatLng();
            updateCoordinateInputs(position.lat, position.lng);
        });

        // Map Click Event to Move Marker
        warehouseMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            warehouseMarker.setLatLng([lat, lng]);
            updateCoordinateInputs(lat, lng);
            warehouseMap.panTo([lat, lng]);
        });
    }

    function updateCoordinateInputs(lat, lng) {
        $('#warehouseLatInput').val(lat.toFixed(7));
        $('#warehouseLngInput').val(lng.toFixed(7));
    }

    function handleManualCoordinateInput() {
        const lat = parseFloat($('#warehouseLatInput').val());
        const lng = parseFloat($('#warehouseLngInput').val());

        if (!isNaN(lat) && !isNaN(lng) && warehouseMarker && warehouseMap) {
            warehouseMarker.setLatLng([lat, lng]);
            warehouseMap.setView([lat, lng], 15);
        }
    }

    function locateUserGPS() {
        if ("geolocation" in navigator) {
            showPreloader('Mendeteksi Lokasi GPS...', 'Mengambil koordinat perangkat Anda...');
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    hidePreloader();
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    warehouseMarker.setLatLng([lat, lng]);
                    warehouseMap.setView([lat, lng], 16);
                    updateCoordinateInputs(lat, lng);
                    warehouseMarker.bindPopup('<b>Lokasi GPS Anda Ditemukan!</b>').openPopup();
                    showToast('success', 'GPS Terdeteksi', `Latitude: ${lat.toFixed(5)}, Longitude: ${lng.toFixed(5)}`);
                },
                function(error) {
                    hidePreloader();
                    showToast('error', 'Gagal Deteksi GPS', 'Izin akses lokasi ditolak oleh browser.');
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        } else {
            showToast('error', 'Tidak Didukung', 'Browser Anda tidak mendukung Geolocation.');
        }
    }

    function resetDefaultLocation() {
        if (warehouseMarker && warehouseMap) {
            warehouseMarker.setLatLng([defaultLat, defaultLng]);
            warehouseMap.setView([defaultLat, defaultLng], 15);
            updateCoordinateInputs(defaultLat, defaultLng);
            warehouseMarker.bindPopup('<b>Titik Lokasi Direset</b>').openPopup();
        }
    }

    function switchSettingsTab(tabKey, btnElement) {
        $('.tab-pane-settings').hide();
        $('.settings-tab-btn').removeClass('active');

        $(`#tabContent-${tabKey}`).show();
        $(btnElement).addClass('active');

        if (tabKey === 'store' && warehouseMap) {
            setTimeout(function() {
                warehouseMap.invalidateSize();
            }, 100);
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function handleSettingsSave(e, formId, btnId) {
        e.preventDefault();
        const form = $(`#${formId}`);
        const btn = $(`#${btnId}`);

        btn.prop('disabled', true);
        showPreloader('Menyimpan Pengaturan...', 'Memperbarui database konfigurasi dan menyelaraskan cache...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                hidePreloader();
                btn.prop('disabled', false);
                showToast('success', 'Berhasil', response.message || 'Pengaturan berhasil disimpan.');
            },
            error: function(xhr) {
                hidePreloader();
                btn.prop('disabled', false);
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan pengaturan.');
            }
        });
    }

    function triggerClearCache(type) {
        showPreloader('Membersihkan Cache...', 'Mengeksekusi command pemeliharaan sistem...');

        $.ajax({
            url: "{{ route('admin.settings.clear-cache') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: type
            },
            success: function(response) {
                hidePreloader();
                showToast('success', 'Cache Dibersihkan', response.message);
            },
            error: function(xhr) {
                hidePreloader();
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Gagal membersihkan cache.');
            }
        });
    }
</script>
@endpush
