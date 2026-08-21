@extends('layouts.admin')

@section('title', 'Regional Data Management')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="panel-title" style="font-size: 1.25rem;">Data Master Wilayah (Read-Only)</h2>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                Direktori data hierarki wilayah administratif untuk alamat pengiriman dan kurir.
            </p>
        </div>
        <div>
            <span class="status-pill status-paid" style="padding: 0.4rem 0.85rem; font-size: 0.8125rem;">
                <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i>
                Standar Wilayah Terproteksi
            </span>
        </div>
    </div>
    
    <div class="panel-content">
        <!-- 5 Tabs Navigation -->
        <div class="nav-tabs-wrapper">
            <button type="button" class="tab-nav-link active" onclick="switchRegionTab('countries')">
                <i data-lucide="globe" style="width: 16px; height: 16px;"></i>
                <span>1. Negara (Countries)</span>
            </button>
            <button type="button" class="tab-nav-link" onclick="switchRegionTab('provinces')">
                <i data-lucide="map" style="width: 16px; height: 16px;"></i>
                <span>2. Provinsi (Provinces)</span>
            </button>
            <button type="button" class="tab-nav-link" onclick="switchRegionTab('regencies')">
                <i data-lucide="landmark" style="width: 16px; height: 16px;"></i>
                <span>3. Kota / Kabupaten (Regencies)</span>
            </button>
            <button type="button" class="tab-nav-link" onclick="switchRegionTab('districts')">
                <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                <span>4. Kecamatan (Districts)</span>
            </button>
            <button type="button" class="tab-nav-link" onclick="switchRegionTab('villages')">
                <i data-lucide="home" style="width: 16px; height: 16px;"></i>
                <span>5. Kelurahan / Desa (Villages)</span>
            </button>
        </div>

        <!-- Tab 1: Countries -->
        <div id="tab-countries" class="tab-pane active">
            <table id="countriesTable" class="dataTable display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode ISO</th>
                        <th>Nama Negara</th>
                        <th>Kode Telepon</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Tab 2: Provinces -->
        <div id="tab-provinces" class="tab-pane">
            <table id="provincesTable" class="dataTable display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama Provinsi</th>
                        <th>Negara</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Tab 3: Regencies -->
        <div id="tab-regencies" class="tab-pane">
            <table id="regenciesTable" class="dataTable display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama Kota / Kabupaten</th>
                        <th>Tipe</th>
                        <th>Provinsi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Tab 4: Districts -->
        <div id="tab-districts" class="tab-pane">
            <table id="districtsTable" class="dataTable display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama Kecamatan</th>
                        <th>Kota / Kabupaten</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Tab 5: Villages -->
        <div id="tab-villages" class="tab-pane">
            <table id="villagesTable" class="dataTable display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama Desa / Kelurahan</th>
                        <th>Tipe</th>
                        <th>Kode Pos</th>
                        <th>Kecamatan</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let datatables = {};
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const dtLanguage = {
            processing: "Memuat data wilayah...",
            search: "",
            searchPlaceholder: "Cari data...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        };

        // Initialize Countries Table First
        datatables.countries = $('#countriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.regions.index') }}?type=countries",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'code', name: 'code', render: function(d) { return '<code class="code-pill">' + (d || '-') + '</code>'; } },
                { data: 'name', name: 'name', render: function(d) { return '<strong>' + (d || '-') + '</strong>'; } },
                { data: 'formatted_phone', name: 'phone_code' }
            ],
            language: dtLanguage,
            drawCallback: function() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
        });
    });

    function switchRegionTab(type) {
        // Update tab buttons
        $('.tab-nav-link').removeClass('active');
        $(`.tab-nav-link[onclick="switchRegionTab('${type}')"]`).addClass('active');

        // Update tab panes
        $('.tab-pane').removeClass('active');
        $(`#tab-${type}`).addClass('active');

        const dtLanguage = {
            processing: "Memuat data...",
            search: "",
            searchPlaceholder: "Cari data...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        };

        // Lazy load datatable for tab when clicked
        if (!datatables[type]) {
            if (type === 'provinces') {
                datatables.provinces = $('#provincesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.regions.index') }}?type=provinces",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'code', name: 'code', render: function(d) { return '<code class="code-pill">' + (d || '-') + '</code>'; } },
                        { data: 'name', name: 'name', render: function(d) { return '<strong>' + (d || '-') + '</strong>'; } },
                        { data: 'country_name', name: 'country.name' }
                    ],
                    language: dtLanguage,
                    drawCallback: function() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
                });
            } else if (type === 'regencies') {
                datatables.regencies = $('#regenciesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.regions.index') }}?type=regencies",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'code', name: 'code', render: function(d) { return '<code class="code-pill">' + (d || '-') + '</code>'; } },
                        { data: 'name', name: 'name', render: function(d) { return '<strong>' + (d || '-') + '</strong>'; } },
                        { data: 'type_badge', name: 'type' },
                        { data: 'province_name', name: 'province.name' }
                    ],
                    language: dtLanguage,
                    drawCallback: function() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
                });
            } else if (type === 'districts') {
                datatables.districts = $('#districtsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.regions.index') }}?type=districts",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'code', name: 'code', render: function(d) { return '<code class="code-pill">' + (d || '-') + '</code>'; } },
                        { data: 'name', name: 'name', render: function(d) { return '<strong>' + (d || '-') + '</strong>'; } },
                        { data: 'regency_name', name: 'regency.name' }
                    ],
                    language: dtLanguage,
                    drawCallback: function() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
                });
            } else if (type === 'villages') {
                datatables.villages = $('#villagesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.regions.index') }}?type=villages",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'code', name: 'code', render: function(d) { return '<code class="code-pill">' + (d || '-') + '</code>'; } },
                        { data: 'name', name: 'name', render: function(d) { return '<strong>' + (d || '-') + '</strong>'; } },
                        { data: 'type', name: 'type', render: function(d) { return '<span class="status-pill status-processing">' + (d || 'DESA') + '</span>'; } },
                        { data: 'postal', name: 'postal_code' },
                        { data: 'district_name', name: 'district.name' }
                    ],
                    language: dtLanguage,
                    drawCallback: function() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
                });
            }
        } else {
            datatables[type].columns.adjust().draw();
        }
    }
</script>
@endpush
