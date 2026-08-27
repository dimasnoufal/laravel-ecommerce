@extends('layouts.admin')

@section('title', 'Dokumen Laporan & Arsip')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Standardized Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>Analytics & Finance</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Dokumen Laporan</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Pusat Dokumen Laporan & Ekspor</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola arsip dokumen laporan berkala, buat laporan baru dalam format CSV/PDF, dan unduh rekapan data.
            </p>
        </div>

        <!-- Top Action Buttons -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Direct Link to Live Analytics Dashboard -->
            <a href="{{ route('admin.analytics.index') }}" class="btn-secondary" onclick="showPreloader('Membuka Live Analytics', 'Menyiapkan visualisasi data interaktif...');" title="Buka Dashboard Analitik Interaktif">
                <i data-lucide="bar-chart-3" style="width: 16px; height: 16px;"></i>
                <span>Buka Live Analytics</span>
            </a>

            <!-- Generate & Archive Modal Trigger -->
            <button type="button" class="btn-primary" onclick="openCreateReportModal()">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                <span>Generate Laporan Baru</span>
            </button>
        </div>
    </div>

    <!-- Main Panel Card with Standardized DataTables Structure -->
    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="panel-title" style="font-size: 1.25rem;">Daftar Dokumen Laporan Terarsip</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Seluruh riwayat pembuatan laporan tersimpan di database dan berkas CSV pada server storage.
                </p>
            </div>
            <button type="button" class="btn-primary" onclick="openCreateReportModal()">
                <i data-lucide="file-plus" style="width: 16px; height: 16px;"></i>
                <span>Buat Laporan Baru</span>
            </button>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table id="reportsTable" class="dataTable display nowrap" style="width:100%" data-preloader-title="Memuat Dokumen Laporan" data-preloader-subtext="Mengambil daftar riwayat arsip laporan dari server...">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th>Dokumen Laporan</th>
                            <th style="text-align: center;">Tipe Laporan</th>
                            <th>Rentang Periode</th>
                            <th>Dibuat Oleh</th>
                            <th>Waktu Generate</th>
                            <th style="width: 180px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Standardized Component: Modal Generate Report -->
<x-modal id="generateReportModal" title="Generate Dokumen Laporan Baru" maxWidth="520px">
    <form id="generateReportForm" method="POST" action="{{ route('admin.reports.generate') }}" onsubmit="handleGenerateReportSubmit(event)">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama / Judul Dokumen Laporan <span style="color: var(--danger);">*</span></label>
            <input type="text" id="reportNameInput" name="name" class="form-control" value="Laporan Penjualan - {{ date('d M Y') }}" required placeholder="Contoh: Laporan Omzet Q3 2026">
            <span id="reportNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label class="form-label">Tipe Laporan <span style="color: var(--danger);">*</span></label>
            <select id="reportTypeSelect" name="type" class="form-control" required>
                <option value="SALES" selected>Sales & Revenue (Penjualan)</option>
                <option value="ORDERS">Orders & Fulfillment (Pesanan)</option>
                <option value="INVENTORY">Inventory Valuation (Stok & Nilai Aset)</option>
                <option value="PAYMENTS">Payments & Gateways (Pembayaran)</option>
                <option value="CUSTOMERS">Customers & VIPs (Pelanggan)</option>
            </select>
            <span id="reportTypeError" class="form-error" style="display: none;"></span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <div class="form-group">
                <label class="form-label">Tanggal Mulai <span style="color: var(--danger);">*</span></label>
                <input type="text" id="reportStartDatePicker" name="start_date" class="form-control" value="{{ date('Y-m-01') }}" required readonly>
                <span id="reportStartDateError" class="form-error" style="display: none;"></span>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Selesai <span style="color: var(--danger);">*</span></label>
                <input type="text" id="reportEndDatePicker" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required readonly>
                <span id="reportEndDateError" class="form-error" style="display: none;"></span>
            </div>
        </div>

        <div style="background: var(--info-bg); border-left: 3px solid var(--info); padding: 0.85rem; border-radius: var(--radius-sm); font-size: 0.78125rem; color: var(--text-main); line-height: 1.4; margin-top: 0.5rem;">
            <strong>Informasi:</strong> Dokumen CSV akan digenerate secara streaming, disimpan ke server storage, dan snapshot agregasi metrik dicatat ke database.
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" class="btn-secondary" onclick="closeModal('generateReportModal')">Batal</button>
        <button type="button" id="submitGenerateBtn" class="btn-primary" onclick="handleGenerateReportSubmit(event)">
            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
            <span id="submitGenerateBtnText">Generate Sekarang</span>
        </button>
    </x-slot>
</x-modal>

<!-- Standardized Component: Modal Metadata Snapshot Inspector -->
<x-modal id="metadataInspectorModal" title="Snapshot Metadata Laporan" maxWidth="480px">
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 0.25rem;">
            Nilai metrik kunci pada saat dokumen laporan ini di-generate:
        </p>
        <div id="metadataInspectorBody" style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 360px; overflow-y: auto;">
            <!-- Rendered dynamically via JavaScript -->
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" class="btn-primary" onclick="closeModal('metadataInspectorModal')">Tutup</button>
    </x-slot>
</x-modal>

@endsection

@push('scripts')
<script>
    let reportsTable;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize DataTable matching Products & Master Data
        reportsTable = $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.reports.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'report_info', name: 'name' },
                { data: 'type_badge', name: 'type', className: 'text-center' },
                { data: 'date_range', name: 'start_date' },
                { data: 'generator_name', name: 'generator.name' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[5, 'desc']],
            language: createDataTableLanguage('Memuat Dokumen Laporan', 'Mengambil daftar riwayat arsip laporan dari server...', {
                searchPlaceholder: "Cari nama laporan, tipe..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // Initialize Flatpickr for Modal Date Inputs
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#reportStartDatePicker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                defaultDate: "{{ date('Y-m-01') }}"
            });

            flatpickr("#reportEndDatePicker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                defaultDate: "{{ date('Y-m-d') }}"
            });
        }
    });

    function openCreateReportModal() {
        $('#generateReportForm')[0].reset();
        $('.form-error').hide();
        $('#reportNameInput').val('Laporan Penjualan - {{ date('d M Y') }}');
        $('#reportTypeSelect').val('SALES');
        $('#submitGenerateBtn').prop('disabled', false);
        $('#submitGenerateBtnText').text('Generate Sekarang');
        openModal('generateReportModal');
    }

    function handleGenerateReportSubmit(e) {
        if (e) e.preventDefault();

        const form = document.getElementById('generateReportForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        $('#submitGenerateBtn').prop('disabled', true);
        $('#submitGenerateBtnText').text('Meng-generate...');
        $('.form-error').hide();

        showPreloader('Meng-generate Laporan...', 'Memproses agregasi data, menyusun file CSV, dan menyimpan arsip ke database...');

        $.ajax({
            url: "{{ route('admin.reports.generate') }}",
            type: "POST",
            data: $('#generateReportForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeModal('generateReportModal');
                showToast('success', 'Berhasil', response.message || 'Laporan berhasil di-generate.');
                reportsTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#submitGenerateBtn').prop('disabled', false);
                $('#submitGenerateBtnText').text('Generate Sekarang');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.name) $('#reportNameError').text(errs.name[0]).show();
                    if (errs.type) $('#reportTypeError').text(errs.type[0]).show();
                    if (errs.start_date) $('#reportStartDateError').text(errs.start_date[0]).show();
                    if (errs.end_date) $('#reportEndDateError').text(errs.end_date[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem saat generate laporan.');
                }
            }
        });
    }

    function openMetadataInspector(meta, title) {
        document.getElementById('metadataInspectorModalTitle').textContent = 'Snapshot: ' + title;
        const container = document.getElementById('metadataInspectorBody');
        container.innerHTML = '';

        if (!meta || Object.keys(meta).length === 0) {
            container.innerHTML = '<p style="color: var(--text-muted); font-size: 0.8125rem; text-align: center; padding: 1.5rem 0;">Tidak ada metadata snapshot tersimpan.</p>';
        } else {
            for (const [key, value] of Object.entries(meta)) {
                const formattedKey = key.replace(/_/g, ' ').toUpperCase();
                let formattedVal = value;
                if (typeof value === 'number' && (key.includes('sales') || key.includes('amount') || key.includes('value') || key.includes('spend') || key.includes('aov'))) {
                    formattedVal = 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }

                const item = document.createElement('div');
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.justifyContent = 'space-between';
                item.style.padding = '0.65rem 0.85rem';
                item.style.background = 'var(--bg-body)';
                item.style.borderRadius = 'var(--radius-md)';
                item.style.border = '1px solid var(--border-color)';
                item.innerHTML = `
                    <span style="font-weight: 700; font-size: 0.75rem; color: var(--text-muted);">${formattedKey}</span>
                    <span style="font-weight: 800; font-size: 0.875rem; color: var(--text-main);">${formattedVal}</span>
                `;
                container.appendChild(item);
            }
        }

        openModal('metadataInspectorModal');
    }

    function deleteReportArchive(reportId, reportName) {
        showDeleteConfirm({
            title: 'Hapus Dokumen Laporan',
            itemName: reportName,
            message: `Apakah Anda yakin ingin menghapus dokumen laporan ini? File fisik dan riwayat pada database akan dihapus permanen.`,
            confirmBtnText: 'Ya, Hapus Dokumen',
            onConfirm: function() {
                showPreloader('Menghapus Dokumen...', 'Menghapus berkas dari server dan database...');
                $.ajax({
                    url: `/admin/reports/${reportId}`,
                    type: 'DELETE',
                    success: function(response) {
                        hidePreloader();
                        showToast('success', 'Berhasil', response.message || 'Dokumen laporan berhasil dihapus.');
                        reportsTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        hidePreloader();
                        showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat menghapus dokumen laporan.');
                    }
                });
            }
        });
    }
</script>
@endpush
