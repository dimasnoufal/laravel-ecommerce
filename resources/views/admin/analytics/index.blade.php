@extends('layouts.admin')

@section('title', 'Analytics & Business Intelligence')

@section('styles')
<style>
    /* Type Selection Navigation Bar */
    .analytics-type-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
        scrollbar-width: none;
    }
    .analytics-type-nav::-webkit-scrollbar {
        display: none;
    }

    .analytics-type-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.25rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-muted);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .analytics-type-pill:hover {
        background: var(--bg-body);
        color: var(--text-main);
        border-color: var(--text-light);
    }
    .analytics-type-pill.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 4px 10px -2px rgba(37, 99, 235, 0.35);
    }

    /* Period Banner & Filter Bar */
    .period-filter-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .period-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .active-period-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        background: var(--primary-light);
        color: var(--primary);
        padding: 0.45rem 0.95rem;
        border-radius: var(--radius-md);
        border: 1px solid rgba(37, 99, 235, 0.2);
        font-weight: 700;
        font-size: 0.875rem;
        letter-spacing: -0.01em;
    }

    .active-period-badge span.period-text {
        color: var(--text-main);
        font-weight: 800;
    }

    .active-period-badge span.period-days {
        background: var(--primary);
        color: #ffffff;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Preset Buttons */
    .preset-pills-group {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    .preset-pill-btn {
        padding: 0.45rem 0.85rem;
        border-radius: var(--radius-md);
        font-size: 0.78125rem;
        font-weight: 600;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .preset-pill-btn:hover {
        color: var(--text-main);
        background: var(--card-bg);
        border-color: var(--text-light);
    }
    .preset-pill-btn.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    /* Flatpickr Date Picker Wrap */
    .custom-datepicker-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .flatpickr-input-box {
        position: relative;
        display: flex;
        align-items: center;
    }
    .flatpickr-input-box i {
        position: absolute;
        left: 0.85rem;
        color: var(--primary);
        pointer-events: none;
        z-index: 2;
    }
    .flatpickr-input-box input {
        padding-left: 2.5rem !important;
        min-width: 290px;
        font-size: 0.84375rem !important;
        font-weight: 700 !important;
        color: var(--text-main) !important;
        background: var(--bg-body) !important;
        cursor: pointer;
    }

    .cache-pill-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--success);
        background: var(--success-bg);
        padding: 0.4rem 0.75rem;
        border-radius: var(--radius-md);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    /* KPI Summary Cards Grid */
    .kpi-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }
    @media (max-width: 1024px) {
        .kpi-stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .kpi-stat-grid {
            grid-template-columns: 1fr;
        }
    }

    .kpi-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: all 0.25s ease;
    }
    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: rgba(37, 99, 235, 0.3);
    }

    .kpi-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.85rem;
    }
    .kpi-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .kpi-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .kpi-stat-icon.primary { background: var(--primary-light); color: var(--primary); }
    .kpi-stat-icon.success { background: var(--success-bg); color: var(--success); }
    .kpi-stat-icon.warning { background: var(--warning-bg); color: var(--warning); }
    .kpi-stat-icon.danger  { background: var(--danger-bg); color: var(--danger); }
    .kpi-stat-icon.info    { background: var(--info-bg); color: var(--info); }

    .kpi-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        line-height: 1.2;
        margin-bottom: 0.35rem;
    }
    .kpi-stat-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Charts & Breakdown Grid */
    .chart-layout-grid {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 1.25rem;
    }
    @media (max-width: 992px) {
        .chart-layout-grid {
            grid-template-columns: 1fr;
        }
    }

    .chart-box-container {
        position: relative;
        height: 290px;
        width: 100%;
    }

    .breakdown-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 290px;
        overflow-y: auto;
        padding-right: 0.25rem;
    }
    .breakdown-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.65rem 0.85rem;
        background: var(--bg-body);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        transition: background 0.15s ease;
    }
    .breakdown-item:hover {
        background: var(--card-bg);
    }
    .breakdown-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-main);
    }
    .breakdown-sub {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .breakdown-val {
        font-size: 0.875rem;
        font-weight: 800;
        color: var(--text-main);
        text-align: right;
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Standardized Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>Analytics & Finance</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Analytics</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Analytics & Business Intelligence</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Monitoring performa bisnis real-time, visualisasi tren penjualan, dan ringkasan metrik komprehensif.
            </p>
        </div>

        <!-- Top Action Buttons -->
        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
            <!-- Force Refresh Cache -->
            <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['refresh' => 1])) }}" class="btn-secondary" onclick="showPreloader('Memperbarui Data...', 'Mengambil data terbaru langsung dari database PostgreSQL...');" title="Bypass Redis Cache & Hitung Ulang Data">
                <i data-lucide="rotate-cw" style="width: 15px; height: 15px;"></i>
                <span>Refresh Data</span>
            </a>

            <!-- Quick Export Streamed CSV -->
            <a href="{{ route('admin.analytics.export-live', ['type' => $reportType, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn-secondary" onclick="showToast('info', 'Mulai Mengunduh', 'File CSV analitik sedang disiapkan...');" title="Unduh CSV Langsung">
                <i data-lucide="download" style="width: 15px; height: 15px;"></i>
                <span>Export CSV</span>
            </a>

            <!-- Print / PDF Preview -->
            <a href="{{ route('admin.analytics.print-live', ['type' => $reportType, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" target="_blank" class="btn-secondary" title="Buka Tampilan Siap Cetak / PDF">
                <i data-lucide="printer" style="width: 15px; height: 15px;"></i>
                <span>Print / PDF</span>
            </a>

            <!-- Go to Reports Archive -->
            <a href="{{ route('admin.reports.index') }}" class="btn-primary" onclick="showPreloader('Membuka Arsip...', 'Memuat riwayat dokumen laporan tersimpan...');">
                <i data-lucide="file-text" style="width: 15px; height: 15px;"></i>
                <span>Arsip Laporan</span>
            </a>
        </div>
    </div>

    <!-- Type Selection Navigation Bar -->
    <div class="analytics-type-nav">
        <a href="{{ route('admin.analytics.index', array_merge(request()->except(['type', 'page']), ['type' => 'SALES'])) }}" class="analytics-type-pill {{ $reportType === 'SALES' ? 'active' : '' }}" onclick="showPreloader('Memuat Metrik Penjualan', 'Menghitung omzet, AOV, dan produk terlaris...');">
            <i data-lucide="trending-up" style="width: 15px; height: 15px;"></i>
            <span>Sales & Revenue</span>
        </a>
        <a href="{{ route('admin.analytics.index', array_merge(request()->except(['type', 'page']), ['type' => 'ORDERS'])) }}" class="analytics-type-pill {{ $reportType === 'ORDERS' ? 'active' : '' }}" onclick="showPreloader('Memuat Metrik Pesanan', 'Menghitung volume, status pesanan, dan fulfillment...');">
            <i data-lucide="shopping-cart" style="width: 15px; height: 15px;"></i>
            <span>Orders & Fulfillment</span>
        </a>
        <a href="{{ route('admin.analytics.index', array_merge(request()->except(['type', 'page']), ['type' => 'INVENTORY'])) }}" class="analytics-type-pill {{ $reportType === 'INVENTORY' ? 'active' : '' }}" onclick="showPreloader('Memuat Valuasi Stok', 'Menghitung total aset inventaris dan stok kritis...');">
            <i data-lucide="layers" style="width: 15px; height: 15px;"></i>
            <span>Inventory Valuation</span>
        </a>
        <a href="{{ route('admin.analytics.index', array_merge(request()->except(['type', 'page']), ['type' => 'PAYMENTS'])) }}" class="analytics-type-pill {{ $reportType === 'PAYMENTS' ? 'active' : '' }}" onclick="showPreloader('Memuat Metrik Pembayaran', 'Menganalisis transaksi per payment gateway...');">
            <i data-lucide="credit-card" style="width: 15px; height: 15px;"></i>
            <span>Payments & Gateways</span>
        </a>
        <a href="{{ route('admin.analytics.index', array_merge(request()->except(['type', 'page']), ['type' => 'CUSTOMERS'])) }}" class="analytics-type-pill {{ $reportType === 'CUSTOMERS' ? 'active' : '' }}" onclick="showPreloader('Memuat Metrik Pelanggan', 'Menganalisis pengguna baru dan pelanggan VIP...');">
            <i data-lucide="users" style="width: 15px; height: 15px;"></i>
            <span>Customers & VIPs</span>
        </a>
    </div>

    <!-- Enhanced Period Banner & Filter Control Card -->
    <div class="period-filter-card">
        <!-- Top Row: Active Date Range Preview & Status Badges -->
        <div class="period-top-row">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <div class="active-period-badge" title="Rentang tanggal aktif data analitik">
                    <i data-lucide="calendar-range" style="width: 18px; height: 18px;"></i>
                    <span>Periode Aktif:</span>
                    <span class="period-text">{{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}</span>
                    <span class="period-days">{{ $startDate->diffInDays($endDate) + 1 }} Hari</span>
                </div>

                <div class="cache-pill-tag" title="Metrik disimpan di Redis Cache dengan TTL 30 menit">
                    <i data-lucide="zap" style="width: 13px; height: 13px;"></i>
                    <span>Redis Cache (30m)</span>
                </div>
            </div>

            <!-- Custom Flatpickr Date Range Form (Never Truncated!) -->
            <form id="analyticsDateFilterForm" method="GET" action="{{ route('admin.analytics.index') }}" class="custom-datepicker-wrap">
                <input type="hidden" name="type" value="{{ $reportType }}">
                <input type="hidden" name="preset" value="custom">
                <input type="hidden" id="startDateHidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" id="endDateHidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">

                <div class="flatpickr-input-box">
                    <i data-lucide="calendar" style="width: 17px; height: 17px;"></i>
                    <input type="text" id="analyticsDateRangePicker" class="form-control" placeholder="Pilih Rentang Tanggal Kustom..." readonly>
                </div>

                <button type="submit" class="btn-primary" style="padding: 0.65rem 1.15rem;">
                    <i data-lucide="filter" style="width: 15px; height: 15px;"></i>
                    <span>Terapkan</span>
                </button>
            </form>
        </div>

        <!-- Bottom Row: Quick Presets Bar -->
        <div style="display: flex; align-items: center; gap: 0.6rem; border-top: 1px solid var(--border-color); padding-top: 0.85rem; flex-wrap: wrap;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.25rem;">
                Filter Cepat (Preset):
            </span>
            <div class="preset-pills-group">
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'today'])) }}" class="preset-pill-btn {{ $preset === 'today' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik hari ini...');">
                    <i data-lucide="clock" style="width: 13px; height: 13px;"></i>
                    <span>Hari Ini</span>
                </a>
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'yesterday'])) }}" class="preset-pill-btn {{ $preset === 'yesterday' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik kemarin...');">
                    <span>Kemarin</span>
                </a>
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'last_7_days'])) }}" class="preset-pill-btn {{ $preset === 'last_7_days' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik 7 hari terakhir...');">
                    <span>7 Hari Terakhir</span>
                </a>
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'last_30_days'])) }}" class="preset-pill-btn {{ $preset === 'last_30_days' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik 30 hari terakhir...');">
                    <span>30 Hari Terakhir</span>
                </a>
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'this_month'])) }}" class="preset-pill-btn {{ $preset === 'this_month' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik bulan ini...');">
                    <span>Bulan Ini</span>
                </a>
                <a href="{{ route('admin.analytics.index', array_merge(request()->except(['preset', 'start_date', 'end_date', 'page']), ['preset' => 'last_month'])) }}" class="preset-pill-btn {{ $preset === 'last_month' ? 'active' : '' }}" onclick="showPreloader('Memfilter Data...', 'Memuat data analitik bulan lalu...');">
                    <span>Bulan Lalu</span>
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards Grid -->
    <div class="kpi-stat-grid">
        @foreach ($reportData['kpis'] as $kpi)
            <div class="kpi-stat-card">
                <div class="kpi-stat-top">
                    <span class="kpi-stat-label">{{ $kpi['label'] }}</span>
                    <div class="kpi-stat-icon {{ $kpi['color'] ?? 'primary' }}">
                        <i data-lucide="{{ $kpi['icon'] ?? 'activity' }}" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
                <div>
                    <div class="kpi-stat-value">{{ $kpi['value'] }}</div>
                    <div class="kpi-stat-desc">
                        <i data-lucide="info" style="width: 12px; height: 12px; flex-shrink: 0;"></i>
                        <span>{{ $kpi['desc'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts & Breakdown Grid -->
    <div class="chart-layout-grid">
        <!-- Main Interactive Chart Panel -->
        <div class="panel-card" style="padding: 1.5rem;">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">{{ $reportData['title'] }} — Visualisasi Tren</h3>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                        Data tren performa pada periode <strong style="color: var(--text-main);">{{ $startDate->format('d M Y') }}</strong> s/d <strong style="color: var(--text-main);">{{ $endDate->format('d M Y') }}</strong>
                    </p>
                </div>
            </div>
            <div class="chart-box-container">
                <canvas id="analyticsMainChart"></canvas>
            </div>
        </div>

        <!-- Dynamic Breakdown Panel -->
        <div class="panel-card" style="padding: 1.5rem;">
            <div class="panel-header">
                <h3 class="panel-title">Ringkasan Breakdown</h3>
            </div>

            @if ($reportType === 'SALES' && isset($reportData['top_products']))
                <div class="breakdown-list">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.2rem;">Top 5 Produk Terlaris</div>
                    @forelse ($reportData['top_products'] as $prod)
                        <div class="breakdown-item">
                            <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                                <span class="breakdown-title">{{ Str::limit(data_get($prod, 'product_name', 'N/A'), 20) }}</span>
                                <span class="breakdown-sub">SKU: {{ data_get($prod, 'sku', '-') }} • {{ data_get($prod, 'units_sold', 0) }} terjual</span>
                            </div>
                            <div class="breakdown-val">Rp {{ number_format(data_get($prod, 'total_revenue', 0), 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <p style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; margin-top: 2.5rem;">Belum ada data penjualan pada periode ini.</p>
                    @endforelse
                </div>

            @elseif ($reportType === 'ORDERS' && isset($reportData['status_distribution']))
                <div class="breakdown-list">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.2rem;">Distribusi Status Pesanan</div>
                    @foreach ($reportData['status_distribution'] as $statusName => $count)
                        <div class="breakdown-item">
                            <span class="breakdown-title">{{ $statusName }}</span>
                            <span class="breakdown-val">{{ number_format($count) }} pesanan</span>
                        </div>
                    @endforeach
                </div>

            @elseif ($reportType === 'INVENTORY' && isset($reportData['low_stock_items']))
                <div class="breakdown-list">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--danger); text-transform: uppercase; margin-bottom: 0.2rem;">Perlu Re-Stock Segera</div>
                    @forelse ($reportData['low_stock_items'] as $item)
                        <div class="breakdown-item">
                            <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                                <span class="breakdown-title">{{ Str::limit(data_get($item, 'product_name', 'N/A'), 20) }}</span>
                                <span class="breakdown-sub">SKU: {{ data_get($item, 'sku', '-') }}</span>
                            </div>
                            <div class="breakdown-val" style="color: {{ data_get($item, 'stock', 0) == 0 ? 'var(--danger)' : 'var(--warning)' }};">
                                {{ data_get($item, 'stock', 0) }} tersisa
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; margin-top: 2.5rem;">Semua stok dalam kondisi aman (> 5 unit).</p>
                    @endforelse
                </div>

            @elseif ($reportType === 'PAYMENTS' && isset($reportData['providers']))
                <div class="breakdown-list">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.2rem;">Volume per Payment Gateway</div>
                    @forelse ($reportData['providers'] as $prv)
                        <div class="breakdown-item">
                            <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                                <span class="breakdown-title">{{ strtoupper(data_get($prv, 'provider', 'Direct')) }}</span>
                                <span class="breakdown-sub">{{ data_get($prv, 'count', 0) }} transaksi</span>
                            </div>
                            <div class="breakdown-val">Rp {{ number_format(data_get($prv, 'total_amount', 0), 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <p style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; margin-top: 2.5rem;">Belum ada pembayaran pada periode ini.</p>
                    @endforelse
                </div>

            @elseif ($reportType === 'CUSTOMERS' && isset($reportData['top_spenders']))
                <div class="breakdown-list">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.2rem;">Top VIP Spenders</div>
                    @forelse ($reportData['top_spenders'] as $vip)
                        <div class="breakdown-item">
                            <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                                <span class="breakdown-title">{{ Str::limit(data_get($vip, 'name', 'N/A'), 18) }}</span>
                                <span class="breakdown-sub">{{ data_get($vip, 'orders_count', 0) }} order</span>
                            </div>
                            <div class="breakdown-val">Rp {{ number_format(data_get($vip, 'total_spent', 0), 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <p style="font-size: 0.8125rem; color: var(--text-muted); text-align: center; margin-top: 2.5rem;">Belum ada transaksi customer pada periode ini.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Live Data Table Panel Card -->
    <div class="panel-card" style="padding: 1.5rem;">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Rincian Data Transaksi (Live Data Preview)</h3>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Menampilkan 50 entri per halaman • Total {{ $detailRecords->total() }} baris data ditemukan
                </p>
            </div>
            <a href="{{ route('admin.analytics.export-live', ['type' => $reportType, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn-secondary" style="font-size: 0.8125rem; padding: 0.45rem 0.85rem;" onclick="showToast('info', 'Download Dimulai', 'File CSV detail data sedang disiapkan...');">
                <i data-lucide="file-spreadsheet" style="width: 15px; height: 15px;"></i>
                <span>Export Full CSV</span>
            </a>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table class="dataTable display nowrap" style="width: 100%;">
                    <thead>
                        @if ($reportType === 'SALES')
                            <tr>
                                <th>Order #</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        @elseif ($reportType === 'ORDERS')
                            <tr>
                                <th>Order #</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Total Item</th>
                                <th>Subtotal</th>
                                <th>Ongkir</th>
                                <th>Diskon</th>
                                <th>Total Bayar</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        @elseif ($reportType === 'INVENTORY')
                            <tr>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Harga Jual</th>
                                <th>Stok Fisik</th>
                                <th>Total Nilai Aset</th>
                                <th style="text-align: center;">Status Stok</th>
                                <th>Terakhir Update</th>
                            </tr>
                        @elseif ($reportType === 'PAYMENTS')
                            <tr>
                                <th>ID Bayar</th>
                                <th>Order #</th>
                                <th>Provider Gateway</th>
                                <th>Kode Referensi</th>
                                <th>Nominal</th>
                                <th style="text-align: center;">Status</th>
                                <th>Waktu Lunas</th>
                            </tr>
                        @elseif ($reportType === 'CUSTOMERS')
                            <tr>
                                <th>ID User</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Jumlah Order</th>
                                <th>Terdaftar Sejak</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @if ($reportType === 'SALES')
                            @forelse ($detailRecords as $item)
                                <tr>
                                    <td><strong style="color: var(--primary);">{{ $item->order->order_number ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->order->created_at ? $item->order->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $item->order->user->name ?? 'Guest/Deleted' }}</td>
                                    <td><strong>{{ $item->product_name }}</strong></td>
                                    <td><code class="code-pill">{{ $item->sku }}</code></td>
                                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td><strong>{{ $item->quantity }}</strong></td>
                                    <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                                    <td style="text-align: center;">
                                        <span class="status-pill status-{{ $item->order->status === 'DELIVERED' ? 'paid' : ($item->order->status === 'CANCELLED' ? 'cancelled' : 'processing') }}">
                                            {{ $item->order->status ?? 'UNKNOWN' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">Tidak ada data transaksi penjualan ditemukan pada periode ini.</td></tr>
                            @endforelse

                        @elseif ($reportType === 'ORDERS')
                            @forelse ($detailRecords as $order)
                                <tr>
                                    <td><strong style="color: var(--primary);">{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $order->user->name ?? 'Deleted User' }}</td>
                                    <td>{{ $order->orderItems->sum('quantity') }} items</td>
                                    <td>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                    <td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                    <td style="text-align: center;">
                                        <span class="status-pill status-{{ $order->status === 'DELIVERED' ? 'paid' : ($order->status === 'CANCELLED' ? 'cancelled' : 'processing') }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">Tidak ada pesanan ditemukan pada periode ini.</td></tr>
                            @endforelse

                        @elseif ($reportType === 'INVENTORY')
                            @forelse ($detailRecords as $var)
                                <tr>
                                    <td><code class="code-pill">{{ $var->sku }}</code></td>
                                    <td><strong>{{ $var->product->name ?? 'N/A' }}</strong></td>
                                    <td>Rp {{ number_format($var->price, 0, ',', '.') }}</td>
                                    <td><strong>{{ $var->stock }}</strong> unit</td>
                                    <td>Rp {{ number_format($var->stock * $var->price, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @if ($var->stock == 0)
                                            <span class="status-pill status-cancelled">Habis (0)</span>
                                        @elseif ($var->stock <= 5)
                                            <span class="status-pill status-pending">Menipis ({{ $var->stock }})</span>
                                        @else
                                            <span class="status-pill status-paid">Tersedia</span>
                                        @endif
                                    </td>
                                    <td>{{ $var->updated_at ? $var->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">Tidak ada varian produk ditemukan.</td></tr>
                            @endforelse

                        @elseif ($reportType === 'PAYMENTS')
                            @forelse ($detailRecords as $pay)
                                <tr>
                                    <td>#{{ $pay->id }}</td>
                                    <td><strong style="color: var(--primary);">{{ $pay->order->order_number ?? 'N/A' }}</strong></td>
                                    <td><code class="code-pill">{{ strtoupper($pay->provider) }}</code></td>
                                    <td><code class="code-pill">{{ $pay->provider_reference }}</code></td>
                                    <td><strong>Rp {{ number_format($pay->amount, 0, ',', '.') }}</strong></td>
                                    <td style="text-align: center;">
                                        <span class="status-pill status-{{ $pay->status === 'PAID' ? 'paid' : ($pay->status === 'PENDING' ? 'pending' : 'cancelled') }}">
                                            {{ $pay->status }}
                                        </span>
                                    </td>
                                    <td>{{ $pay->paid_at ? $pay->paid_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">Tidak ada pembayaran pada periode ini.</td></tr>
                            @endforelse

                        @elseif ($reportType === 'CUSTOMERS')
                            @forelse ($detailRecords as $cust)
                                <tr>
                                    <td>#{{ $cust->id }}</td>
                                    <td><strong>{{ $cust->name }}</strong></td>
                                    <td>{{ $cust->email }}</td>
                                    <td><strong>{{ $cust->orders_count }}</strong> transaksi</td>
                                    <td>{{ $cust->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">Tidak ada pelanggan terdaftar ditemukan.</td></tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
            {{ $detailRecords->appends(request()->query())->links() }}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();

        // 1. Initialize Standard Flatpickr Date Range Picker
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#analyticsDateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                defaultDate: ["{{ $startDate->format('Y-m-d') }}", "{{ $endDate->format('Y-m-d') }}"],
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const start = instance.formatDate(selectedDates[0], "Y-m-d");
                        const end = instance.formatDate(selectedDates[1], "Y-m-d");
                        document.getElementById('startDateHidden').value = start;
                        document.getElementById('endDateHidden').value = end;
                    }
                }
            });
        }

        // 2. Attach Preloader to Date Filter Form submission
        const filterForm = document.getElementById('analyticsDateFilterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                showPreloader('Memproses Data...', 'Menghitung ulang data analitik berdasarkan rentang tanggal...');
            });
        }

        // 3. Initialize Chart.js
        @if (isset($reportData['chart']))
            const ctx = document.getElementById('analyticsMainChart');
            if (ctx) {
                const chartConfig = @json($reportData['chart']);

                if (chartConfig.type === 'mixed') {
                    new Chart(ctx.getContext('2d'), {
                        data: {
                            labels: chartConfig.labels,
                            datasets: [
                                {
                                    type: 'line',
                                    label: chartConfig.datasets[0].name,
                                    data: chartConfig.datasets[0].data,
                                    borderColor: chartConfig.datasets[0].color,
                                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    yAxisID: 'y'
                                },
                                {
                                    type: 'bar',
                                    label: chartConfig.datasets[1].name,
                                    data: chartConfig.datasets[1].data,
                                    backgroundColor: chartConfig.datasets[1].color,
                                    borderRadius: 4,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                                tooltip: {
                                    callbacks: {
                                        label: function (c) {
                                            if (c.dataset.label.includes('Revenue')) {
                                                return ' Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(c.raw);
                                            }
                                            return ' Orders: ' + c.raw + ' items';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    type: 'linear',
                                    position: 'left',
                                    ticks: {
                                        callback: function(v) {
                                            if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'M';
                                            if (v >= 1000) return 'Rp ' + (v/1000).toFixed(0) + 'k';
                                            return 'Rp ' + v;
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });
                } else if (chartConfig.type === 'doughnut' || chartConfig.type === 'pie') {
                    new Chart(ctx.getContext('2d'), {
                        type: chartConfig.type,
                        data: {
                            labels: chartConfig.labels,
                            datasets: [{
                                data: chartConfig.data,
                                backgroundColor: chartConfig.colors || ['#2563EB', '#10B981', '#F59E0B', '#EF4444'],
                                borderWidth: 2,
                                borderColor: 'var(--card-bg)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } }
                            }
                        }
                    });
                } else {
                    const datasets = chartConfig.datasets.map(ds => ({
                        label: ds.name,
                        data: ds.data,
                        backgroundColor: ds.color,
                        borderRadius: 4
                    }));

                    new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: chartConfig.labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
            }
        @endif
    });
</script>
@endpush
