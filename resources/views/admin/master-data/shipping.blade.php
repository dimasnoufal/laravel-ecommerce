@extends('layouts.admin')

@section('title', 'Master Ekspedisi & Layanan Pengiriman')

@section('styles')
<style>
    /* Carrier Cells */
    .carrier-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .carrier-logo-box {
        width: 42px;
        height: 42px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #ffffff;
        font-weight: 800;
        font-size: 0.8125rem;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .carrier-meta {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .carrier-fullname {
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.9375rem;
    }
    
    /* Tracking URL cell */
    .tracking-template-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        max-width: 320px;
    }
    .tracking-url-text {
        font-size: 0.75rem;
        font-family: ui-monospace, monospace;
        color: var(--text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        background: var(--bg-body);
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        border: 1px solid var(--border-color);
        max-width: 210px;
    }
    .tbl-btn-test-url {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.5rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .tbl-btn-test-url:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
    }

    /* Services Count Button Badge */
    .badge-services-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .badge-services-btn:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Status Pill Toggle */
    .status-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .status-pill-btn:hover {
        transform: translateY(-1px);
        filter: brightness(0.95);
    }
    .status-pill-success {
        background: var(--success-bg);
        color: var(--success);
    }
    .status-pill-danger {
        background: var(--danger-bg);
        color: var(--danger);
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: currentColor;
    }

    /* Subtle Primary Button */
    .tbl-btn-primary-subtle {
        background: var(--primary-light);
        color: var(--primary);
        border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .tbl-btn-primary-subtle:hover {
        background: var(--primary);
        color: #ffffff;
    }

    /* Services Drawer Sub-Table */
    .service-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        margin-bottom: 0.65rem;
        transition: all 0.2s ease;
    }
    .service-item-row:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
    .service-meta-left {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .service-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .service-name-text {
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.875rem;
    }
    .service-duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>Master Data</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Shipping & Logistics</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Ekspedisi & Layanan Pengiriman</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola master mitra kurir pengiriman barang, format pelacakan nomor resi, serta tingkatan layanan ongkir.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn-primary" onclick="openCreateCarrierDrawer()">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Tambah Kurir Baru</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <x-kpi-card 
            title="Total Kurir Ekspedisi" 
            value="{{ number_format($totalCarriers) }}" 
            subtext="Mitra logistik terintegrasi" 
            icon="truck" 
            color="primary" 
        />
        <x-kpi-card 
            title="Kurir Aktif" 
            value="{{ number_format($activeCarriers) }}" 
            subtext="Tersedia di opsi pengiriman toko" 
            icon="check-circle" 
            color="success" 
        />
        <x-kpi-card 
            title="Total Layanan Ongkir" 
            value="{{ number_format($totalServices) }}" 
            subtext="Reguler, Express, Sameday, Kargo" 
            icon="layers" 
            color="info" 
        />
        <x-kpi-card 
            title="Layanan Aktif" 
            value="{{ number_format($activeServices) }}" 
            subtext="Dapat dipilih oleh pembeli" 
            icon="zap" 
            color="warning" 
        />
    </div>

    <!-- Main Table Panel -->
    <div class="panel-card" style="padding: 1.5rem;">
        <div class="panel-header" style="margin-bottom: 1.25rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.125rem;">Daftar Mitra Kurir (Carriers)</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Klik tombol <strong>Layanan</strong> untuk mengonfigurasi jenis service dan estimasi hari pengantaran.
                </p>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label for="filterStatus" style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted);">Status:</label>
                <select id="filterStatus" class="form-control" style="width: auto; min-width: 140px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;" onchange="carriersTable.ajax.reload()">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif Saja</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table id="carriersTable" class="dataTable display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Ekspedisi & Kode</th>
                            <th>Template Lacak Resi (Tracking URL)</th>
                            <th style="text-align: center;">Jumlah Layanan</th>
                            <th style="width: 100px; text-align: center;">Status</th>
                            <th style="width: 180px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Slide-Over Drawer for Create / Edit Carrier -->
<x-drawer id="carrierDrawer" title="Tambah Kurir Baru" width="480px">
    <form id="carrierForm" onsubmit="handleCarrierSubmit(event)">
        <input type="hidden" id="carrierId" name="id">

        <div class="form-group">
            <label for="carrierCode" class="form-label">Kode Ekspedisi (Carrier Code) <span style="color: var(--danger);">*</span></label>
            <input type="text" id="carrierCode" name="code" class="form-control" placeholder="Contoh: JNE, JNT, SICEPAT, POS" required style="text-transform: uppercase;">
            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; display: block;">
                Gunakan kode singkat unik (huruf kapital/angka).
            </span>
            <span id="carrierCodeError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="carrierName" class="form-label">Nama Ekspedisi (Carrier Name) <span style="color: var(--danger);">*</span></label>
            <input type="text" id="carrierName" name="name" class="form-control" placeholder="Contoh: JNE Express, SiCepat Ekspres" required>
            <span id="carrierNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="carrierTrackingUrl" class="form-label">Template URL Pelacakan Resi</label>
            <input type="text" id="carrierTrackingUrl" name="tracking_url_template" class="form-control" placeholder="https://track.jne.co.id/?awb={tracking_number}">
            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block; line-height: 1.4;">
                Gunakan placeholder <code>{tracking_number}</code> yang otomatis diganti nomor resi paket saat pelanggan melacak pesanan.
            </span>
            <span id="carrierTrackingUrlError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label class="form-label">Status Operasional</label>
            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; background: var(--bg-body); padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <input type="checkbox" id="carrierIsActive" name="is_active" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                <div>
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Kurir Aktif</span>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Kurir dapat dipilih dalam opsi pengiriman toko.</p>
                </div>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('carrierDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveCarrierBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveCarrierBtnText">Simpan Kurir</span>
            </button>
        </div>
    </form>
</x-drawer>

<!-- Master-Detail Slide-Over Drawer: Manage Carrier Services -->
<x-drawer id="servicesDrawer" title="Kelola Layanan Pengiriman" width="580px">
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        
        <!-- Carrier Summary Header Box in Drawer -->
        <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="carrier-logo-box" id="drawerCarrierLogo" style="width: 36px; height: 36px; font-size: 0.75rem;">JNE</div>
                <div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--text-main);" id="drawerCarrierName">JNE Express</div>
                    <code class="code-pill" id="drawerCarrierCode">JNE</code>
                </div>
            </div>
            <button type="button" class="btn-primary" onclick="openCreateServiceModal()" style="padding: 0.5rem 0.9rem; font-size: 0.8125rem;">
                <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                <span>Tambah Layanan</span>
            </button>
        </div>

        <!-- Services List Container -->
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Daftar Layanan Tersedia</span>
                <span id="drawerServicesCount" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">0 Layanan</span>
            </div>

            <div id="servicesListWrapper">
                <!-- Populated via AJAX -->
            </div>
        </div>

    </div>
</x-drawer>

<!-- Modal: Create / Edit Service -->
<x-modal id="serviceModal" title="Tambah Layanan Pengiriman" width="480px">
    <form id="serviceForm" onsubmit="handleServiceSubmit(event)">
        <input type="hidden" id="serviceCarrierId" name="carrier_id">
        <input type="hidden" id="serviceId" name="id">

        <div class="form-group">
            <label for="serviceCode" class="form-label">Kode Layanan (Service Code) <span style="color: var(--danger);">*</span></label>
            <input type="text" id="serviceCode" name="code" class="form-control" placeholder="Contoh: REG, YES, OKE, EZ" required style="text-transform: uppercase;">
            <span id="serviceCodeError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="serviceName" class="form-label">Nama Layanan <span style="color: var(--danger);">*</span></label>
            <input type="text" id="serviceName" name="name" class="form-control" placeholder="Contoh: Layanan Reguler, Yakin Esok Sampai" required>
            <span id="serviceNameError" class="form-error" style="display: none;"></span>
        </div>

        <!-- Duration Estimates -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="serviceMinDays" class="form-label">Estimasi Min (Hari) <span style="color: var(--danger);">*</span></label>
                <input type="number" id="serviceMinDays" name="estimated_min_days" class="form-control" min="0" value="1" required oninput="updateEstimatePreview()">
                <span id="serviceMinDaysError" class="form-error" style="display: none;"></span>
            </div>
            <div class="form-group">
                <label for="serviceMaxDays" class="form-label">Estimasi Max (Hari) <span style="color: var(--danger);">*</span></label>
                <input type="number" id="serviceMaxDays" name="estimated_max_days" class="form-control" min="0" value="3" required oninput="updateEstimatePreview()">
                <span id="serviceMaxDaysError" class="form-error" style="display: none;"></span>
            </div>
        </div>

        <!-- Live Estimate Preview Badge -->
        <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="clock" style="width: 16px; height: 16px; color: var(--primary);"></i>
            <span style="font-size: 0.8125rem; color: var(--text-main);">
                Preview Teks: <strong id="estimatePreviewText" style="color: var(--primary);">1 - 3 Hari Kerja</strong>
            </span>
        </div>

        <div class="form-group">
            <label class="form-label">Status Layanan</label>
            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; background: var(--bg-body); padding: 0.65rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <input type="checkbox" id="serviceIsActive" name="is_active" value="1" checked style="width: 16px; height: 16px; accent-color: var(--primary);">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-main);">Layanan Aktif</span>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeModal('serviceModal')">Batal</button>
            <button type="submit" class="btn-primary" id="saveServiceBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveServiceBtnText">Simpan Layanan</span>
            </button>
        </div>
    </form>
</x-modal>

<!-- Delete Carrier Modal -->
<x-confirm-delete-modal 
    id="deleteCarrierModal" 
    title="Hapus Kurir Ekspedisi" 
    message="Apakah Anda yakin ingin menghapus kurir ini beserta seluruh layanannya?" 
    onConfirm="confirmDeleteCarrier()" 
/>

<!-- Delete Service Modal -->
<x-confirm-delete-modal 
    id="deleteServiceModal" 
    title="Hapus Layanan Pengiriman" 
    message="Apakah Anda yakin ingin menghapus layanan pengiriman ini?" 
    onConfirm="confirmDeleteService()" 
/>

@endsection

@push('scripts')
<script>
    let carriersTable;
    let currentSelectedCarrierId = null;
    let targetDeleteCarrierId = null;
    let targetDeleteServiceId = null;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        carriersTable = $('#carriersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.shipping.index') }}",
                data: function(d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'carrier_info', name: 'name' },
                { data: 'tracking_preview', name: 'tracking_url_template' },
                { data: 'services_count_badge', name: 'services_count', searchable: false, className: 'text-center' },
                { data: 'status_pill', name: 'is_active', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: createDataTableLanguage('Memuat Data Kurir', 'Mengambil daftar ekspedisi dan layanan pengiriman...', {
                searchPlaceholder: "Cari nama atau kode kurir..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    });

    /* =========================================================================
       CARRIER CRUD HANDLERS
       ========================================================================= */
    function openCreateCarrierDrawer() {
        $('#carrierForm')[0].reset();
        $('#carrierId').val('');
        $('#carrierIsActive').prop('checked', true);
        $('.form-error').hide();
        $('#saveCarrierBtn').prop('disabled', false);
        $('#saveCarrierBtnText').text('Simpan Kurir');
        openDrawer('carrierDrawer', 'Tambah Kurir Baru', 'Daftarkan mitra ekspedisi pengiriman paket');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editCarrier(id) {
        showPreloader('Mengambil Data Kurir', 'Menyiapkan formulir edit kurir...');
        $.get(`/admin/master-data/shipping/carriers/${id}/edit`, function(response) {
            hidePreloader();
            let c = response.data;

            $('#carrierForm')[0].reset();
            $('.form-error').hide();
            $('#carrierId').val(c.id);
            $('#carrierCode').val(c.code);
            $('#carrierName').val(c.name);
            $('#carrierTrackingUrl').val(c.tracking_url_template || '');
            $('#carrierIsActive').prop('checked', c.is_active);

            $('#saveCarrierBtn').prop('disabled', false);
            $('#saveCarrierBtnText').text('Perbarui Kurir');

            openDrawer('carrierDrawer', 'Edit Kurir: ' + c.name, 'Perbarui informasi dan format pelacakan resi');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data kurir.');
        });
    }

    function handleCarrierSubmit(e) {
        e.preventDefault();

        let id = $('#carrierId').val();
        $('#saveCarrierBtn').prop('disabled', true);
        $('#saveCarrierBtnText').text('Menyimpan...');
        $('.form-error').hide();

        showPreloader(id ? 'Memperbarui Kurir' : 'Menyimpan Kurir', 'Menyimpan data kurir ke database...');

        let url = id ? `/admin/master-data/shipping/carriers/${id}` : `{{ route('admin.shipping.carriers.store') }}`;
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $('#carrierForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeDrawer('carrierDrawer');
                showToast('success', 'Berhasil', response.message);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveCarrierBtn').prop('disabled', false);
                $('#saveCarrierBtnText').text(id ? 'Perbarui Kurir' : 'Simpan Kurir');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.code) $('#carrierCodeError').text(errs.code[0]).show();
                    if (errs.name) $('#carrierNameError').text(errs.name[0]).show();
                    if (errs.tracking_url_template) $('#carrierTrackingUrlError').text(errs.tracking_url_template[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function toggleCarrierStatus(id) {
        showPreloader('Mengubah Status', 'Memperbarui status aktif kurir...');
        $.ajax({
            url: `/admin/master-data/shipping/carriers/${id}/toggle-status`,
            type: 'PATCH',
            success: function(response) {
                hidePreloader();
                showToast('success', 'Berhasil', response.message);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat mengubah status kurir.');
            }
        });
    }

    function deleteCarrier(id, name) {
        targetDeleteCarrierId = id;
        $('#deleteCarrierModal .confirm-dialog-msg').text(`Apakah Anda yakin ingin menghapus kurir "${name}" beserta seluruh layanannya?`);
        openConfirmDeleteModal('deleteCarrierModal');
    }

    function confirmDeleteCarrier() {
        if (!targetDeleteCarrierId) return;

        showPreloader('Menghapus Kurir', 'Sedang memproses penghapusan data kurir...');
        $.ajax({
            url: `/admin/master-data/shipping/carriers/${targetDeleteCarrierId}`,
            type: 'DELETE',
            success: function(response) {
                hidePreloader();
                closeConfirmDeleteModal('deleteCarrierModal');
                showToast('success', 'Kurir Dihapus', response.message);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                closeConfirmDeleteModal('deleteCarrierModal');
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat menghapus kurir.');
            }
        });
    }

    /* =========================================================================
       SERVICES MASTER-DETAIL DRAWER & CRUD HANDLERS
       ========================================================================= */
    function openServicesDrawer(carrierId, carrierName, carrierCode) {
        currentSelectedCarrierId = carrierId;
        $('#drawerCarrierName').text(carrierName);
        $('#drawerCarrierCode').text(carrierCode);
        $('#drawerCarrierLogo').text(carrierCode.substring(0, 3).toUpperCase());

        loadCarrierServices(carrierId);
        openDrawer('servicesDrawer', 'Layanan: ' + carrierName, 'Kelola tingkatan layanan dan estimasi hari kirim');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function loadCarrierServices(carrierId) {
        $('#servicesListWrapper').html(`
            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                <i data-lucide="loader-2" class="spin" style="width: 24px; height: 24px; margin-bottom: 0.5rem;"></i>
                <p style="font-size: 0.8125rem;">Memuat daftar layanan...</p>
            </div>
        `);
        if (typeof lucide !== 'undefined') lucide.createIcons();

        $.get(`/admin/master-data/shipping/carriers/${carrierId}/services`, function(response) {
            let services = response.services;
            $('#drawerServicesCount').text(`${services.length} Layanan`);

            if (services.length === 0) {
                $('#servicesListWrapper').html(`
                    <div style="text-align: center; padding: 2.5rem 1rem; background: var(--bg-body); border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                        <i data-lucide="package-x" style="width: 32px; height: 32px; color: var(--text-light); margin-bottom: 0.5rem;"></i>
                        <p style="font-size: 0.875rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Belum Ada Layanan</p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">Kurir ini belum memiliki opsi layanan pengiriman.</p>
                        <button type="button" class="btn-primary" onclick="openCreateServiceModal()" style="font-size: 0.75rem; padding: 0.4rem 0.8rem;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                            <span>Tambah Layanan Pertama</span>
                        </button>
                    </div>
                `);
                if (typeof lucide !== 'undefined') lucide.createIcons();
                return;
            }

            let html = '';
            services.forEach(function(s) {
                let statusClass = s.is_active ? 'status-pill-success' : 'status-pill-danger';
                let statusText = s.is_active ? 'Aktif' : 'Nonaktif';
                let safeName = s.name.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                let escapedName = $('<div>').text(s.name).html();
                let escapedCode = $('<div>').text(s.code).html();

                html += `
                    <div class="service-item-row">
                        <div class="service-meta-left">
                            <div class="service-title-wrap">
                                <code class="code-pill">${escapedCode}</code>
                                <span class="service-name-text">${escapedName}</span>
                            </div>
                            <div class="service-duration-badge">
                                <i data-lucide="clock" style="width: 12px; height: 12px; color: var(--primary);"></i>
                                <span>${s.duration_text}</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                            <button type="button" class="status-pill-btn ${statusClass}" onclick="toggleServiceStatus(${s.id})" title="Ubah status layanan">
                                <span class="status-dot"></span>
                                <span>${statusText}</span>
                            </button>
                            
                            <div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editService(${s.id})" title="Edit Layanan">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteService(${s.id}, '${safeName}')" title="Hapus Layanan">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#servicesListWrapper').html(html);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }).fail(function() {
            $('#servicesListWrapper').html(`
                <div style="text-align: center; padding: 2rem; color: var(--danger);">
                    <p style="font-size: 0.8125rem;">Gagal memuat layanan kurir.</p>
                </div>
            `);
        });
    }

    function updateEstimatePreview() {
        let min = parseInt($('#serviceMinDays').val()) || 0;
        let max = parseInt($('#serviceMaxDays').val()) || 0;

        if (min < 0) min = 0;
        if (max < min) max = min;

        let text = '';
        if (min === max) {
            text = min === 0 ? 'Hari yang sama (Same Day)' : `${min} Hari Kerja`;
        } else {
            text = `${min} - ${max} Hari Kerja`;
        }

        $('#estimatePreviewText').text(text);
    }

    function openCreateServiceModal() {
        if (!currentSelectedCarrierId) return;

        $('#serviceForm')[0].reset();
        $('#serviceCarrierId').val(currentSelectedCarrierId);
        $('#serviceId').val('');
        $('#serviceMinDays').val(1);
        $('#serviceMaxDays').val(3);
        $('#serviceIsActive').prop('checked', true);
        $('.form-error').hide();
        updateEstimatePreview();

        $('#saveServiceBtn').prop('disabled', false);
        $('#saveServiceBtnText').text('Simpan Layanan');

        openModal('serviceModal');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editService(id) {
        showPreloader('Mengambil Data Layanan', 'Menyiapkan formulir edit layanan...');
        $.get(`/admin/master-data/shipping/services/${id}/edit`, function(response) {
            hidePreloader();
            let s = response.data;

            $('#serviceForm')[0].reset();
            $('.form-error').hide();
            $('#serviceCarrierId').val(s.carrier_id);
            $('#serviceId').val(s.id);
            $('#serviceCode').val(s.code);
            $('#serviceName').val(s.name);
            $('#serviceMinDays').val(s.estimated_min_days);
            $('#serviceMaxDays').val(s.estimated_max_days);
            $('#serviceIsActive').prop('checked', s.is_active);
            updateEstimatePreview();

            $('#saveServiceBtn').prop('disabled', false);
            $('#saveServiceBtnText').text('Perbarui Layanan');

            openModal('serviceModal');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data layanan.');
        });
    }

    function handleServiceSubmit(e) {
        e.preventDefault();

        let id = $('#serviceId').val();
        $('#saveServiceBtn').prop('disabled', true);
        $('#saveServiceBtnText').text('Menyimpan...');
        $('.form-error').hide();

        showPreloader(id ? 'Memperbarui Layanan' : 'Menyimpan Layanan', 'Menyimpan data layanan ke database...');

        let url = id ? `/admin/master-data/shipping/services/${id}` : `{{ route('admin.shipping.services.store') }}`;
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $('#serviceForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeModal('serviceModal');
                showToast('success', 'Berhasil', response.message);
                loadCarrierServices(currentSelectedCarrierId);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveServiceBtn').prop('disabled', false);
                $('#saveServiceBtnText').text(id ? 'Perbarui Layanan' : 'Simpan Layanan');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.code) $('#serviceCodeError').text(errs.code[0]).show();
                    if (errs.name) $('#serviceNameError').text(errs.name[0]).show();
                    if (errs.estimated_min_days) $('#serviceMinDaysError').text(errs.estimated_min_days[0]).show();
                    if (errs.estimated_max_days) $('#serviceMaxDaysError').text(errs.estimated_max_days[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function toggleServiceStatus(id) {
        showPreloader('Mengubah Status', 'Memperbarui status aktif layanan...');
        $.ajax({
            url: `/admin/master-data/shipping/services/${id}/toggle-status`,
            type: 'PATCH',
            success: function(response) {
                hidePreloader();
                showToast('success', 'Berhasil', response.message);
                loadCarrierServices(currentSelectedCarrierId);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat mengubah status layanan.');
            }
        });
    }

    function deleteService(id, name) {
        targetDeleteServiceId = id;
        $('#deleteServiceModal .confirm-dialog-msg').text(`Apakah Anda yakin ingin menghapus layanan "${name}"?`);
        openConfirmDeleteModal('deleteServiceModal');
    }

    function confirmDeleteService() {
        if (!targetDeleteServiceId) return;

        showPreloader('Menghapus Layanan', 'Sedang memproses penghapusan layanan...');
        $.ajax({
            url: `/admin/master-data/shipping/services/${targetDeleteServiceId}`,
            type: 'DELETE',
            success: function(response) {
                hidePreloader();
                closeConfirmDeleteModal('deleteServiceModal');
                showToast('success', 'Layanan Dihapus', response.message);
                loadCarrierServices(currentSelectedCarrierId);
                carriersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                closeConfirmDeleteModal('deleteServiceModal');
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat menghapus layanan.');
            }
        });
    }
</script>
@endpush
