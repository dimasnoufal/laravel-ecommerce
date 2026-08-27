@extends('layouts.admin')

@section('title', 'Audit Trail & Activity Logs')

@section('styles')
<style>
    /* Custom Flatpickr Input Wrap & Icon Positioning */
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
        min-width: 270px;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        background: var(--bg-body) !important;
        color: var(--text-main) !important;
        cursor: pointer;
    }

    /* Diff Visual Table Styles */
    .diff-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }
    .diff-table th {
        background: var(--bg-body);
        padding: 0.65rem 0.85rem;
        text-align: left;
        font-weight: 700;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .diff-table td {
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .diff-val-old {
        background: rgba(239, 68, 68, 0.1);
        color: #DC2626;
        padding: 0.25rem 0.55rem;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(239, 68, 68, 0.2);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        word-break: break-all;
        display: inline-block;
    }
    .diff-val-new {
        background: rgba(16, 185, 129, 0.1);
        color: #16A34A;
        padding: 0.25rem 0.55rem;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(16, 185, 129, 0.2);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        word-break: break-all;
        display: inline-block;
        font-weight: 700;
    }
    .diff-key-badge {
        font-weight: 700;
        color: var(--text-main);
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        background: var(--bg-body);
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
        border: 1px solid var(--border-color);
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
                <span style="color: var(--primary); font-weight: 600;">Activity Logs</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Audit Trail & System Logs</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Rekam jejak digital seluruh aktivitas sistem, login pengguna, modifikasi master data, dan penyesuaian stok.
            </p>
        </div>

        <!-- Quick Stats Badges -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="activity" style="width: 16px; height: 16px; color: var(--success);"></i>
                <span>Hari Ini: <strong>{{ number_format($todayLogs) }}</strong></span>
            </div>
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="shield-check" style="width: 16px; height: 16px; color: var(--primary);"></i>
                <span>Total Audit Trail: <strong>{{ number_format($totalLogs) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Filter & Table Panel Card -->
    <div class="panel-card">
        <div class="panel-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.25rem;">Daftar Rekaman Audit & Log Aktivitas</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                    Gunakan filter di bawah untuk menyaring riwayat berdasarkan aksi, user, atau rentang tanggal.
                </p>
            </div>

            <!-- Filter Controls -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem;">
                <!-- Filter Action -->
                <div>
                    <select id="filterAction" class="form-control" style="width: auto; min-width: 160px; padding: 0.5rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Tipe Aksi</option>
                        <option value="CREATE">CREATE (Tambah Data)</option>
                        <option value="UPDATE">UPDATE (Ubah Data)</option>
                        <option value="DELETE">DELETE (Hapus Data)</option>
                        <option value="STOCK_ADJUSTMENT">STOCK_ADJUSTMENT (Mutasi Stok)</option>
                        <option value="LOGIN">LOGIN</option>
                        <option value="LOGOUT">LOGOUT</option>
                        <option value="ORDER_CANCELLED">ORDER_CANCELLED</option>
                    </select>
                </div>

                <!-- Filter User -->
                <div>
                    <select id="filterUser" class="form-control" style="width: auto; min-width: 170px; padding: 0.5rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Date Range with Fixed Layout & Wide Padding -->
                <div class="filter-datepicker-box">
                    <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                    <input type="text" id="filterLogDateRange" class="form-control" placeholder="Pilih Rentang Tanggal..." readonly>
                </div>

                <button type="button" class="btn-secondary" onclick="resetLogFilters()" title="Reset Semua Filter" style="padding: 0.5rem 0.85rem; font-size: 0.8125rem;">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table id="activityLogsTable" class="dataTable display nowrap" style="width: 100%;" data-preloader-title="Memuat Log Aktivitas" data-preloader-subtext="Mengambil riwayat audit trail dari database...">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th>Waktu Kejadian</th>
                            <th>Pengguna / Pelaku</th>
                            <th style="text-align: center;">Aksi</th>
                            <th>Deskripsi Aktivitas</th>
                            <th>IP Address</th>
                            <th style="width: 130px; text-align: center;">Metadata Diff</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Standardized Component: Modal Diff & Metadata Inspector -->
<x-modal id="diffInspectorModal" title="Inspeksi Metadata & Diff Perubahan" maxWidth="720px">
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        
        <!-- Summary Banner -->
        <div id="diffModalHeaderBanner" style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; display: flex; flex-direction: column; gap: 0.35rem;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span id="diffModalActionBadge" class="status-badge" style="font-weight: 700; font-size: 0.75rem; background: var(--primary-light); color: var(--primary);">-</span>
                <span id="diffModalLogId" style="font-size: 0.75rem; color: var(--text-muted); font-family: ui-monospace, SFMono-Regular, monospace; font-weight: 600;">Log #</span>
            </div>
            <p id="diffModalDesc" style="font-size: 0.84375rem; font-weight: 600; color: var(--text-main); margin: 0; line-height: 1.4;"></p>
        </div>

        <!-- Tab Switching (Visual Table vs Raw JSON) -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" id="tabBtnVisual" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="switchDiffTab('visual')">
                    <i data-lucide="table" style="width: 13px; height: 13px;"></i>
                    <span>Tampilan Visual Diff</span>
                </button>
                <button type="button" id="tabBtnRaw" class="btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="switchDiffTab('raw')">
                    <i data-lucide="code" style="width: 13px; height: 13px;"></i>
                    <span>Raw JSON</span>
                </button>
            </div>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">
                <i data-lucide="lock" style="width: 11px; height: 11px; display: inline-block; vertical-align: middle;"></i>
                Data sensitif (password/token) otomatis disaring
            </span>
        </div>

        <!-- Visual Table View Container -->
        <div id="diffVisualContainer" style="max-height: 380px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <!-- Rendered dynamically -->
        </div>

        <!-- Raw JSON Container (Hidden by default) -->
        <div id="diffRawContainer" style="display: none;">
            <pre id="diffRawPre" style="background: #0F172A; color: #38BDF8; padding: 1.25rem; border-radius: var(--radius-md); font-size: 0.8125rem; overflow-x: auto; max-height: 360px; line-height: 1.5; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; margin: 0;"></pre>
        </div>

    </div>

    <x-slot name="footer">
        <button type="button" class="btn-primary" onclick="closeModal('diffInspectorModal')">Tutup</button>
    </x-slot>
</x-modal>

@endsection

@push('scripts')
<script>
    let activityLogsDataTable;

    $(document).ready(function() {
        initActivityLogsTable();
        initLogDateRangePicker();

        $('#filterAction, #filterUser').on('change', function() {
            activityLogsDataTable.ajax.reload();
        });

        // Delegated click handler for View Diff Button (Safe from quote breaking)
        $(document).on('click', '.btn-view-diff', function(e) {
            e.preventDefault();
            const btn = $(this);
            const logId = btn.attr('data-log-id');
            const action = btn.attr('data-action');
            const desc = btn.attr('data-desc');
            let payload = btn.attr('data-payload');

            let metadata = {};
            if (payload) {
                try {
                    metadata = JSON.parse(payload);
                } catch (err) {
                    console.error('Failed to parse metadata payload JSON', err);
                }
            }

            openDiffInspectorModal(logId, action, desc, metadata);
        });
    });

    function initActivityLogsTable() {
        activityLogsDataTable = $('#activityLogsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.activity-logs.index') }}",
                data: function(d) {
                    d.action_filter = $('#filterAction').val();
                    d.user_id = $('#filterUser').val();

                    const dateVal = $('#filterLogDateRange').val();
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
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'user_info', name: 'user.name' },
                { data: 'action_badge', name: 'action', className: 'text-center' },
                { data: 'description_display', name: 'description' },
                { data: 'ip_address_display', name: 'ip_address' },
                { data: 'payload_action', name: 'metadata', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[1, 'desc']],
            language: createDataTableLanguage('Memuat Log Aktivitas', 'Merekap riwayat audit trail dan aktivitas sistem...', {
                searchPlaceholder: "Cari deskripsi, IP, user..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    function initLogDateRangePicker() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#filterLogDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onClose: function(selectedDates, dateStr, instance) {
                    activityLogsDataTable.ajax.reload();
                }
            });
        }
    }

    function resetLogFilters() {
        $('#filterAction').val('');
        $('#filterUser').val('');
        $('#filterLogDateRange').val('');
        if ($('#filterLogDateRange')[0] && $('#filterLogDateRange')[0]._flatpickr) {
            $('#filterLogDateRange')[0]._flatpickr.clear();
        }
        activityLogsDataTable.ajax.reload();
    }

    function switchDiffTab(mode) {
        if (mode === 'visual') {
            $('#diffVisualContainer').show();
            $('#diffRawContainer').hide();
            $('#tabBtnVisual').removeClass('btn-secondary').addClass('btn-primary');
            $('#tabBtnRaw').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            $('#diffVisualContainer').hide();
            $('#diffRawContainer').show();
            $('#tabBtnVisual').removeClass('btn-primary').addClass('btn-secondary');
            $('#tabBtnRaw').removeClass('btn-secondary').addClass('btn-primary');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function openDiffInspectorModal(logId, actionType, description, metadata) {
        $('#diffModalLogId').text('Log #' + logId);
        $('#diffModalActionBadge').text(actionType);
        $('#diffModalDesc').text(description);
        $('#diffRawPre').text(JSON.stringify(metadata, null, 4));

        const container = document.getElementById('diffVisualContainer');
        container.innerHTML = '';

        if (metadata && metadata.changes && Object.keys(metadata.changes).length > 0) {
            // Render Diff Table (Old vs New)
            let html = `
                <table class="diff-table">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Atribut / Kolom</th>
                            <th style="width: 36%;">Nilai Sebelumnya (Old)</th>
                            <th style="width: 36%;">Nilai Sesudah (New)</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            for (const [field, val] of Object.entries(metadata.changes)) {
                const oldDisplay = (val.old === null || val.old === undefined) ? '<em style="color:var(--text-muted); font-size:0.75rem;">null</em>' : val.old;
                const newDisplay = (val.new === null || val.new === undefined) ? '<em style="color:var(--text-muted); font-size:0.75rem;">null</em>' : val.new;

                html += `
                    <tr>
                        <td><span class="diff-key-badge">${field}</span></td>
                        <td><span class="diff-val-old">${oldDisplay}</span></td>
                        <td><span class="diff-val-new">${newDisplay}</span></td>
                    </tr>
                `;
            }

            html += `</tbody></table>`;
            container.innerHTML = html;
        } else if (metadata && metadata.attributes && Object.keys(metadata.attributes).length > 0) {
            // Render Attributes Table (Create or Delete snapshot)
            let html = `
                <table class="diff-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Atribut / Kolom</th>
                            <th style="width: 70%;">Nilai Rekaman</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            for (const [field, val] of Object.entries(metadata.attributes)) {
                const valDisplay = (val === null || val === undefined) ? '<em style="color:var(--text-muted); font-size:0.75rem;">null</em>' : (typeof val === 'object' ? JSON.stringify(val) : val);

                html += `
                    <tr>
                        <td><span class="diff-key-badge">${field}</span></td>
                        <td><span style="font-family: ui-monospace, monospace; font-size: 0.78125rem; color: var(--text-main); font-weight: 600;">${valDisplay}</span></td>
                    </tr>
                `;
            }

            html += `</tbody></table>`;
            container.innerHTML = html;
        } else if (metadata && Object.keys(metadata).length > 0) {
            // Generic key-value table
            let html = `
                <table class="diff-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Kunci Metadata</th>
                            <th style="width: 70%;">Detail Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            for (const [field, val] of Object.entries(metadata)) {
                const valDisplay = (val === null || val === undefined) ? '<em style="color:var(--text-muted); font-size:0.75rem;">null</em>' : (typeof val === 'object' ? JSON.stringify(val) : val);

                html += `
                    <tr>
                        <td><span class="diff-key-badge">${field}</span></td>
                        <td><span style="font-family: ui-monospace, monospace; font-size: 0.78125rem; color: var(--text-main);">${valDisplay}</span></td>
                    </tr>
                `;
            }

            html += `</tbody></table>`;
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p style="text-align:center; padding: 2rem; color: var(--text-muted); font-size: 0.8125rem;">Tidak ada payload metadata tersimpan.</p>';
        }

        switchDiffTab('visual');
        openModal('diffInspectorModal');
    }
</script>
@endpush
