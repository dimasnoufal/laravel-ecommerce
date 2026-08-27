@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('styles')
<style>
    /* KPI Stats Grid */
    .order-kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1200px) {
        .order-kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .order-kpi-grid {
            grid-template-columns: 1fr;
        }
    }

    .order-kpi-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.25rem 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }
    .order-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .order-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.65rem;
    }
    .order-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .order-kpi-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-kpi-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        line-height: 1.2;
    }

    /* Status Filter Tabs */
    .order-status-tabs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
        scrollbar-width: none;
    }
    .order-status-tabs::-webkit-scrollbar {
        display: none;
    }
    .order-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--text-muted);
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .order-tab-pill:hover {
        background: var(--bg-body);
        color: var(--text-main);
        border-color: var(--text-light);
    }
    .order-tab-pill.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 2px 8px -1px rgba(37, 99, 235, 0.35);
    }
    .order-tab-pill .tab-count {
        font-size: 0.7rem;
        background: rgba(0, 0, 0, 0.08);
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
    }
    .order-tab-pill.active .tab-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Filter Date Picker Wrapper with standard padding */
    .filter-datepicker-box {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .filter-datepicker-box i {
        position: absolute;
        left: 0.85rem;
        color: var(--primary);
        pointer-events: none;
        z-index: 2;
    }
    .filter-datepicker-box input.form-control,
    .filter-datepicker-box input.flatpickr-input,
    .filter-datepicker-box input.input {
        padding-left: 2.6rem !important;
        min-width: 260px;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        background: var(--bg-body) !important;
        color: var(--text-main) !important;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Standardized Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>Commerce & Sales</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Pesanan</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Manajemen Pesanan</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola transaksi masuk, pemrosesan pengiriman kurir, resi pengiriman, dan faktur penjualan.
            </p>
        </div>

        <!-- Quick Summary Omzet Badge -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 0.5rem 1.15rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                <i data-lucide="wallet" style="width: 16px; height: 16px; color: var(--primary);"></i>
                <span>Total Omzet: <strong style="color: var(--text-main);">Rp {{ number_format($kpis['total_revenue'], 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="order-kpi-grid">
        <div class="order-kpi-card">
            <div class="order-kpi-top">
                <span class="order-kpi-label">Semua Pesanan</span>
                <div class="order-kpi-icon" style="background: var(--primary-light); color: var(--primary);">
                    <i data-lucide="shopping-bag" style="width: 16px; height: 16px;"></i>
                </div>
            </div>
            <div class="order-kpi-val">{{ number_format($kpis['total_orders']) }}</div>
        </div>

        <div class="order-kpi-card">
            <div class="order-kpi-top">
                <span class="order-kpi-label">Menunggu Bayar</span>
                <div class="order-kpi-icon" style="background: var(--warning-bg); color: var(--warning);">
                    <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                </div>
            </div>
            <div class="order-kpi-val">{{ number_format($kpis['pending_count']) }}</div>
        </div>

        <div class="order-kpi-card">
            <div class="order-kpi-top">
                <span class="order-kpi-label">Diproses</span>
                <div class="order-kpi-icon" style="background: var(--info-bg); color: var(--info);">
                    <i data-lucide="package" style="width: 16px; height: 16px;"></i>
                </div>
            </div>
            <div class="order-kpi-val">{{ number_format($kpis['processing_count']) }}</div>
        </div>

        <div class="order-kpi-card">
            <div class="order-kpi-top">
                <span class="order-kpi-label">Sedang Dikirim</span>
                <div class="order-kpi-icon" style="background: var(--primary-light); color: var(--primary);">
                    <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
                </div>
            </div>
            <div class="order-kpi-val">{{ number_format($kpis['shipped_count']) }}</div>
        </div>

        <div class="order-kpi-card">
            <div class="order-kpi-top">
                <span class="order-kpi-label">Selesai / Terkirim</span>
                <div class="order-kpi-icon" style="background: var(--success-bg); color: var(--success);">
                    <i data-lucide="check-circle-2" style="width: 16px; height: 16px;"></i>
                </div>
            </div>
            <div class="order-kpi-val">{{ number_format($kpis['delivered_count']) }}</div>
        </div>
    </div>

    <!-- Status Tabs Bar -->
    <div class="order-status-tabs">
        <button type="button" class="order-tab-pill active" onclick="setOrderStatusFilter('ALL', this)">
            <span>Semua Pesanan</span>
            <span class="tab-count">{{ $kpis['total_orders'] }}</span>
        </button>
        <button type="button" class="order-tab-pill" onclick="setOrderStatusFilter('PENDING', this)">
            <span>Menunggu Bayar</span>
            <span class="tab-count">{{ $kpis['pending_count'] }}</span>
        </button>
        <button type="button" class="order-tab-pill" onclick="setOrderStatusFilter('PROCESSING', this)">
            <span>Diproses</span>
            <span class="tab-count">{{ $kpis['processing_count'] }}</span>
        </button>
        <button type="button" class="order-tab-pill" onclick="setOrderStatusFilter('SHIPPED', this)">
            <span>Dikirim</span>
            <span class="tab-count">{{ $kpis['shipped_count'] }}</span>
        </button>
        <button type="button" class="order-tab-pill" onclick="setOrderStatusFilter('DELIVERED', this)">
            <span>Selesai</span>
            <span class="tab-count">{{ $kpis['delivered_count'] }}</span>
        </button>
        <button type="button" class="order-tab-pill" onclick="setOrderStatusFilter('CANCELLED', this)">
            <span>Dibatalkan</span>
            <span class="tab-count">{{ $kpis['cancelled_count'] }}</span>
        </button>
    </div>

    <!-- Main Panel Card with DataTables -->
    <div class="panel-card">
        <div class="panel-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.25rem;">Daftar Transaksi Pesanan</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Pantau rincian setiap order, status pembayaran, dan status fulfillment.
                </p>
            </div>

            <!-- Filter Controls -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem;">
                <!-- Payment Status Filter -->
                <div>
                    <select id="filterPaymentStatus" class="form-control" style="width: auto; min-width: 160px; padding: 0.5rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Pembayaran</option>
                        <option value="PAID">PAID (Lunas)</option>
                        <option value="PENDING">PENDING (Menunggu)</option>
                        <option value="EXPIRED">EXPIRED (Kadaluarsa)</option>
                        <option value="FAILED">FAILED (Gagal)</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="filter-datepicker-box">
                    <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                    <input type="text" id="filterOrderDateRange" class="form-control" placeholder="Pilih Rentang Tanggal..." readonly>
                </div>

                <button type="button" class="btn-secondary" onclick="resetOrderFilters()" title="Reset Semua Filter" style="padding: 0.5rem 0.85rem; font-size: 0.8125rem;">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table id="ordersTable" class="dataTable display nowrap" style="width: 100%;" data-preloader-title="Memuat Daftar Pesanan" data-preloader-subtext="Mengambil data transaksi pesanan dari database...">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Waktu Pemesanan</th>
                            <th style="text-align: center;">Pembayaran</th>
                            <th style="text-align: center;">Status Order</th>
                            <th style="text-align: right;">Total Nilai</th>
                            <th style="width: 160px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Standardized Component: Modal Quick Update Order Status -->
<x-modal id="quickUpdateStatusModal" title="Ubah Status Pesanan" maxWidth="520px">
    <form id="quickUpdateStatusForm" method="POST" action="" onsubmit="handleQuickUpdateStatusSubmit(event)">
        @csrf
        <input type="hidden" id="modalOrderId" name="order_id" value="">

        <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Pesanan yang dipilih</div>
            <div id="modalOrderNumber" style="font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-top: 0.15rem;">#ORDER-NUM</div>
            <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.15rem;">Status Saat Ini: <span id="modalCurrentStatusBadge" class="status-pill status-processing" style="font-size: 0.72rem;">PROCESSING</span></div>
        </div>

        <div class="form-group">
            <label class="form-label">Pilih Status Baru <span style="color: var(--danger);">*</span></label>
            <select id="modalNewStatusSelect" name="status" class="form-control" required onchange="handleModalStatusChange(this.value)">
                <option value="PENDING">PENDING (Menunggu Pembayaran)</option>
                <option value="CONFIRMED">CONFIRMED (Terkonfirmasi)</option>
                <option value="PROCESSING">PROCESSING (Sedang Dipersiapkan)</option>
                <option value="SHIPPED">SHIPPED (Telah Diserahkan ke Kurir / Dikirim)</option>
                <option value="DELIVERED">DELIVERED (Telah Diterima Pelanggan / Selesai)</option>
                <option value="CANCELLED">CANCELLED (Batalkan Pesanan & Kembalikan Stok)</option>
            </select>
        </div>

        <!-- Dynamic Inputs for SHIPPED status -->
        <div id="shippedFieldsContainer" style="display: none; background: var(--primary-light); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1rem;">
            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--primary); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="truck" style="width: 15px; height: 15px;"></i>
                <span>Informasi Resi & Ekspedisi Pengiriman</span>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Resi Pengiriman (AWB / Tracking Number)</label>
                <input type="text" id="modalTrackingNumber" name="tracking_number" class="form-control" placeholder="Contoh: JNE88291039912">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Layanan Ekspedisi Kurir</label>
                <select id="modalShippingService" name="shipping_service_id" class="form-control">
                    <option value="">Pilih Layanan Kurir...</option>
                    @foreach ($carriers as $c)
                        <optgroup label="{{ $c->name }} ({{ $c->code }})">
                            @foreach ($c->services as $s)
                                <option value="{{ $s->id }}">{{ $c->name }} - {{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Warning for CANCELLED status -->
        <div id="cancelledWarningBox" style="display: none; background: var(--danger-bg); border-left: 3px solid var(--danger); padding: 0.85rem; border-radius: var(--radius-sm); font-size: 0.78125rem; color: var(--text-main); margin-bottom: 1rem;">
            <strong>Perhatian:</strong> Membatalkan pesanan akan secara otomatis mengembalikan jumlah kuantitas stok produk ke inventaris sistem (*Stock Movement Return*).
        </div>

        <div class="form-group">
            <label class="form-label">Catatan Pembaruan Status (*Optional*)</label>
            <textarea id="modalStatusNote" name="note" class="form-control" rows="2" placeholder="Tuliskan catatan alasan perubahan status..."></textarea>
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" class="btn-secondary" onclick="closeModal('quickUpdateStatusModal')">Batal</button>
        <button type="button" id="submitUpdateStatusBtn" class="btn-primary" onclick="handleQuickUpdateStatusSubmit(event)">
            <i data-lucide="check" style="width: 15px; height: 15px;"></i>
            <span>Simpan Perubahan</span>
        </button>
    </x-slot>
</x-modal>

@endsection

@push('scripts')
<script>
    let ordersDataTable;
    let currentActiveStatusTab = 'ALL';

    $(document).ready(function() {
        initOrdersTable();
        initOrderDateRangePicker();

        $('#filterPaymentStatus').on('change', function() {
            ordersDataTable.ajax.reload();
        });

        // Delegated click for Quick Update Status
        $(document).on('click', '.btn-update-status', function(e) {
            e.preventDefault();
            const btn = $(this);
            const orderId = btn.attr('data-order-id');
            const orderNumber = btn.attr('data-order-number');
            const currentStatus = btn.attr('data-current-status');

            openQuickUpdateStatusModal(orderId, orderNumber, currentStatus);
        });
    });

    function initOrdersTable() {
        ordersDataTable = $('#ordersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.orders.index') }}",
                data: function(d) {
                    d.status = currentActiveStatusTab;
                    d.payment_status = $('#filterPaymentStatus').val();

                    const dateVal = $('#filterOrderDateRange').val();
                    if (dateVal && dateVal.includes(' to ')) {
                        const parts = dateVal.split(' to ');
                        d.start_date = parts[0];
                        d.end_date = parts[1];
                    } else if (dateVal) {
                        d.start_date = dateVal;
                        d.end_date = dateVal;
                    }
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'order_info', name: 'order_number' },
                { data: 'customer_info', name: 'user.name' },
                { data: 'placed_at_formatted', name: 'created_at' },
                { data: 'payment_status_badge', name: 'payment.status', className: 'text-center' },
                { data: 'order_status_badge', name: 'status', className: 'text-center' },
                { data: 'total_amount_formatted', name: 'total_amount', className: 'text-right' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[3, 'desc']],
            language: createDataTableLanguage('Memuat Pesanan', 'Mengambil daftar transaksi pesanan dari database...', {
                searchPlaceholder: "Cari no pesanan, pelanggan..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    function initOrderDateRangePicker() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#filterOrderDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onClose: function(selectedDates, dateStr, instance) {
                    ordersDataTable.ajax.reload();
                }
            });
        }
    }

    function setOrderStatusFilter(status, btnElement) {
        currentActiveStatusTab = status;
        $('.order-tab-pill').removeClass('active');
        $(btnElement).addClass('active');
        ordersDataTable.ajax.reload();
    }

    function resetOrderFilters() {
        currentActiveStatusTab = 'ALL';
        $('.order-tab-pill').removeClass('active');
        $('.order-tab-pill:first').addClass('active');
        $('#filterPaymentStatus').val('');
        $('#filterOrderDateRange').val('');
        if ($('#filterOrderDateRange')[0] && $('#filterOrderDateRange')[0]._flatpickr) {
            $('#filterOrderDateRange')[0]._flatpickr.clear();
        }
        ordersDataTable.ajax.reload();
    }

    function openQuickUpdateStatusModal(orderId, orderNumber, currentStatus) {
        $('#modalOrderId').val(orderId);
        $('#modalOrderNumber').text('#' + orderNumber);
        $('#modalCurrentStatusBadge').text(currentStatus);
        $('#modalNewStatusSelect').val(currentStatus);
        $('#modalStatusNote').val('');
        $('#modalTrackingNumber').val('');

        handleModalStatusChange(currentStatus);
        openModal('quickUpdateStatusModal');
    }

    function handleModalStatusChange(newStatus) {
        if (newStatus === 'SHIPPED') {
            $('#shippedFieldsContainer').slideDown(150);
            $('#cancelledWarningBox').slideUp(150);
        } else if (newStatus === 'CANCELLED') {
            $('#shippedFieldsContainer').slideUp(150);
            $('#cancelledWarningBox').slideDown(150);
        } else {
            $('#shippedFieldsContainer').slideUp(150);
            $('#cancelledWarningBox').slideUp(150);
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function handleQuickUpdateStatusSubmit(e) {
        if (e) e.preventDefault();
        const orderId = $('#modalOrderId').val();
        if (!orderId) return;

        $('#submitUpdateStatusBtn').prop('disabled', true);
        showPreloader('Memperbarui Status...', 'Menyimpan riwayat status, data kurir, dan memproses log...');

        $.ajax({
            url: `/admin/orders/${orderId}/update-status`,
            type: 'POST',
            data: $('#quickUpdateStatusForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeModal('quickUpdateStatusModal');
                $('#submitUpdateStatusBtn').prop('disabled', false);
                showToast('success', 'Berhasil', response.message || 'Status pesanan berhasil diperbarui.');
                ordersDataTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#submitUpdateStatusBtn').prop('disabled', false);
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem saat memperbarui status.');
            }
        });
    }
</script>
@endpush
