@extends('layouts.admin')

@section('title', 'System Activity Logs & Audit Trail')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Page Header -->
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
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 0.45rem 0.875rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="shield" style="width: 16px; height: 16px; color: var(--primary);"></i>
                <span>Total Rekaman: <strong>{{ number_format($totalLogs) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="panel-card" style="padding: 1.5rem;">
        
        <!-- Filter Bar -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <!-- Filter Action -->
                <div>
                    <select id="filterAction" class="form-control" style="width: auto; min-width: 170px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Tipe Aksi</option>
                        <option value="CREATE">CREATE (Tambah Data)</option>
                        <option value="UPDATE">UPDATE (Ubah Data)</option>
                        <option value="DELETE">DELETE (Hapus Data)</option>
                        <option value="STOCK_ADJUSTMENT">STOCK_ADJUSTMENT (Mutasi Stok)</option>
                        <option value="LOGIN">LOGIN</option>
                        <option value="LOGOUT">LOGOUT</option>
                    </select>
                </div>

                <!-- Filter User -->
                <div>
                    <select id="filterUser" class="form-control" style="width: auto; min-width: 170px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Date Range -->
                <div>
                    <input type="text" id="filterLogDateRange" class="form-control" placeholder="Pilih Rentang Tanggal..." style="width: 220px; padding: 0.45rem 0.75rem; font-size: 0.8125rem; background: var(--bg-body);" readonly>
                </div>

                <button type="button" class="btn-secondary" onclick="resetLogFilters()" style="padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- DataTable Logs -->
        <div class="table-responsive">
            <table id="activityLogsTable" class="display custom-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Waktu Kejadian</th>
                        <th>Pengguna / Pelaku</th>
                        <th>Aksi</th>
                        <th>Deskripsi Aktivitas</th>
                        <th>IP Address</th>
                        <th style="width: 80px; text-align: center;">Metadata</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

</div>

<!-- Payload JSON Viewer Modal -->
<x-modal id="payloadModal" title="Detail Metadata Activity Log">
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="font-size: 0.8125rem; color: var(--text-muted);">
            Payload data perubahan atribut sebelum dan sesudah mutasi / aksi sistem:
        </div>
        <pre id="payloadJsonPre" style="background: #0F172A; color: #38BDF8; padding: 1.25rem; border-radius: var(--radius-md); font-size: 0.8125rem; overflow-x: auto; max-height: 400px; line-height: 1.5; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;"></pre>
    </div>

    <x-slot name="footer">
        <button type="button" class="btn-secondary" onclick="closeModal('payloadModal')">Tutup</button>
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
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'user_info', name: 'user.name' },
                { data: 'action_badge', name: 'action' },
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
        if ($('#filterLogDateRange')[0]._flatpickr) {
            $('#filterLogDateRange')[0]._flatpickr.clear();
        }
        activityLogsDataTable.ajax.reload();
    }

    function viewPayload(logId, jsonStr) {
        try {
            const parsed = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
            $('#payloadJsonPre').text(JSON.stringify(parsed, null, 4));
        } catch (e) {
            $('#payloadJsonPre').text(jsonStr);
        }
        openModal('payloadModal');
    }
</script>
@endpush
