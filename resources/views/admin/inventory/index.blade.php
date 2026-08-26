@extends('layouts.admin')

@section('title', 'Manajemen Inventory & Mutasi Stok')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>Analytics & Finance</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Inventory & Mutasi Stok</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Manajemen Inventory & Mutasi Stok</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Monitoring ketersediaan stok fisik per SKU, histori keluar-masuk barang, dan eksekusi penyesuaian stok manual (*Stock Opname*).
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn-primary" onclick="openAdjustmentDrawer()" style="display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="sliders-horizontal" style="width: 16px; height: 16px;"></i>
                <span>Penyesuaian Stok (Adjust)</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
        
        <!-- Total Unit Stok -->
        <div class="panel-card" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; padding: 1.5rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="layers" style="width: 26px; height: 26px;"></i>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Total Unit Stok</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1.2; margin-top: 0.2rem;">{{ number_format($totalStock) }} <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-muted);">unit</span></h3>
                <span style="font-size: 0.75rem; color: var(--success); font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.25rem; margin-top: 0.25rem;">
                    <i data-lucide="check" style="width: 12px; height: 12px;"></i> Tersebar di seluruh SKU
                </span>
            </div>
        </div>

        <!-- Total SKU Aktif -->
        <div class="panel-card" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; padding: 1.5rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="package" style="width: 26px; height: 26px;"></i>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Total Varian SKU</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); line-height: 1.2; margin-top: 0.2rem;">{{ number_format($totalSkus) }} <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-muted);">varian</span></h3>
                <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Katalog aktif terdaftar</span>
            </div>
        </div>

        <!-- Alert Stok Kritis -->
        <div class="panel-card" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.75rem; padding: 1.5rem;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: {{ ($lowStockCount + $outOfStockCount > 0) ? '#FEF2F2' : '#F0FDF4' }}; color: {{ ($lowStockCount + $outOfStockCount > 0) ? '#DC2626' : '#16A34A' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="{{ ($lowStockCount + $outOfStockCount > 0) ? 'alert-triangle' : 'shield-check' }}" style="width: 26px; height: 26px;"></i>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">Alert Stok Kritis</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: {{ ($lowStockCount + $outOfStockCount > 0) ? '#DC2626' : 'var(--text-main)' }}; line-height: 1.2; margin-top: 0.2rem;">
                    {{ number_format($lowStockCount + $outOfStockCount) }} <span style="font-size: 0.875rem; font-weight: 500; color: var(--text-muted);">SKU</span>
                </h3>
                <span style="font-size: 0.75rem; color: {{ ($lowStockCount + $outOfStockCount > 0) ? '#DC2626' : 'var(--success)' }}; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-top: 0.25rem;">
                    <span>{{ $lowStockCount }} Menipis (≤5)</span> &bull; <span>{{ $outOfStockCount }} Habis</span>
                </span>
            </div>
        </div>

    </div>

    <!-- Main Navigation Tabs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0;">
        <button type="button" class="tab-btn active" id="tabBtnStocks" onclick="switchInventoryTab('stocks')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; font-size: 0.9375rem; font-weight: 700; background: transparent; border: none; border-bottom: 2px solid var(--primary); margin-bottom: -2px; color: var(--primary); cursor: pointer; transition: all 0.2s ease;">
            <i data-lucide="package-check" style="width: 18px; height: 18px;"></i>
            <span>Status Stok SKU</span>
            <span style="background: var(--primary-light); color: var(--primary); padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">{{ $totalSkus }}</span>
        </button>
        <button type="button" class="tab-btn" id="tabBtnMovements" onclick="switchInventoryTab('movements')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; font-size: 0.9375rem; font-weight: 600; background: transparent; border: none; border-bottom: 2px solid transparent; margin-bottom: -2px; color: var(--text-muted); cursor: pointer; transition: all 0.2s ease;">
            <i data-lucide="history" style="width: 18px; height: 18px;"></i>
            <span>Buku Mutasi Stok (Ledger Log)</span>
        </button>
    </div>

    <!-- TAB 1: Real-time Stock Status per SKU -->
    <div id="sectionStocks" class="inventory-section">
        <div class="panel-card" style="padding: 1.5rem;">
            <!-- Filter Bar for Tab 1 -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                    <label style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted);">Filter Status:</label>
                    <select id="filterStockStatus" class="form-control" style="width: auto; min-width: 180px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                        <option value="">Semua Status Stok</option>
                        <option value="in_stock">In Stock (> 5 unit)</option>
                        <option value="low_stock">Stok Menipis (1 - 5 unit)</option>
                        <option value="out_of_stock">Stok Habis (0 unit)</option>
                    </select>
                    <button type="button" class="btn-secondary" onclick="resetStocksFilter()" style="padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <!-- DataTable Stocks -->
            <div class="table-responsive">
                <table id="stocksTable" class="display custom-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Produk & SKU</th>
                            <th>Varian / Spesifikasi</th>
                            <th>Harga Satuan</th>
                            <th>Stok Fisik</th>
                            <th>Status Stok</th>
                            <th style="text-align: right; width: 190px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Stock Movements Ledger History -->
    <div id="sectionMovements" class="inventory-section" style="display: none;">
        <div class="panel-card" style="padding: 1.5rem;">
            <!-- Filter Bar for Tab 2 -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                    <div>
                        <select id="filterMovementType" class="form-control" style="width: auto; min-width: 170px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                            <option value="">Semua Tipe Mutasi</option>
                            <option value="IN">Barang Masuk (IN)</option>
                            <option value="OUT">Barang Keluar (OUT)</option>
                            <option value="ADJUSTMENT">Penyesuaian (ADJUST)</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" id="filterDateRange" class="form-control" placeholder="Pilih Rentang Tanggal..." style="width: 230px; padding: 0.45rem 0.75rem; font-size: 0.8125rem; background: var(--bg-body);" readonly>
                    </div>
                    <div id="skuFilterBadgeContainer" style="display: none;">
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; background: var(--primary-light); color: var(--primary); padding: 0.35rem 0.65rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 600;">
                            <span id="skuFilterLabel">SKU: ...</span>
                            <button type="button" onclick="clearSkuMovementFilter()" style="background: transparent; border: none; color: var(--primary); cursor: pointer; display: flex; align-items: center; padding: 0;">
                                <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                            </button>
                        </span>
                    </div>
                    <button type="button" class="btn-secondary" onclick="resetMovementsFilter()" style="padding: 0.45rem 0.75rem; font-size: 0.8125rem;">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>

            <!-- DataTable Movements -->
            <div class="table-responsive">
                <table id="movementsTable" class="display custom-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Tanggal & Waktu</th>
                            <th>SKU & Produk</th>
                            <th>Tipe Mutasi</th>
                            <th>Jumlah Mutasi</th>
                            <th>Catatan & Referensi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Stock Adjustment Slide-Over Drawer -->
<x-drawer id="adjustmentDrawer" title="Penyesuaian Stok Manual (Stock Adjustment)" width="520px">
    <form id="adjustmentForm" onsubmit="handleAdjustmentSubmit(event)">
        <input type="hidden" id="adjustVariantId" name="product_variant_id" value="">

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- Target SKU Selection -->
            <div class="form-group">
                <label for="selectSkuAdjust" class="form-label">Pilih SKU / Varian Produk <span style="color: var(--danger);">*</span></label>
                <select id="selectSkuAdjust" class="form-control" onchange="onVariantSelected(this.value)" required>
                    <option value="">-- Pilih Varian Produk --</option>
                    @foreach($variants as $var)
                        @php
                            $attrs = $var->attributeValues->pluck('value')->implode(', ');
                            $spec = $attrs ? ' (' . $attrs . ')' : '';
                        @endphp
                        <option value="{{ $var->id }}" data-sku="{{ $var->sku }}" data-stock="{{ $var->stock }}" data-name="{{ $var->product->name ?? '' }}">
                            {{ $var->sku }} - {{ $var->product->name ?? 'Produk' }}{{ $spec }} [Stok: {{ $var->stock }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Current Stock Info Banner -->
            <div id="stockInfoBanner" style="display: none; padding: 0.875rem 1.15rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 500;">Stok Tercatat di Sistem:</span>
                    <span id="currentStockDisplay" style="font-size: 1.15rem; font-weight: 800; color: var(--primary);">0 unit</span>
                </div>
            </div>

            <!-- Mode Selection Cards (3 Modes) -->
            <div class="form-group">
                <label class="form-label">Tipe Penyesuaian Stok <span style="color: var(--danger);">*</span></label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; margin-top: 0.35rem;">
                    
                    <label class="adjust-type-card" id="cardTypeIN" style="border: 2px solid var(--primary); background: var(--primary-light); padding: 0.75rem 0.5rem; border-radius: var(--radius-md); cursor: pointer; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; transition: all 0.2s ease;">
                        <input type="radio" name="adjustment_type" value="IN" checked onchange="onAdjustmentTypeChange('IN')" style="display: none;">
                        <i data-lucide="plus-circle" style="width: 20px; height: 20px; color: var(--success);"></i>
                        <span style="font-weight: 700; font-size: 0.8125rem; color: var(--text-main);">Tambah (IN)</span>
                        <span style="font-size: 0.6875rem; color: var(--text-muted);">Restock / Retur</span>
                    </label>

                    <label class="adjust-type-card" id="cardTypeOUT" style="border: 2px solid var(--border-color); background: var(--card-bg); padding: 0.75rem 0.5rem; border-radius: var(--radius-md); cursor: pointer; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; transition: all 0.2s ease;">
                        <input type="radio" name="adjustment_type" value="OUT" onchange="onAdjustmentTypeChange('OUT')" style="display: none;">
                        <i data-lucide="minus-circle" style="width: 20px; height: 20px; color: var(--danger);"></i>
                        <span style="font-weight: 700; font-size: 0.8125rem; color: var(--text-main);">Kurang (OUT)</span>
                        <span style="font-size: 0.6875rem; color: var(--text-muted);">Rusak / Hilang</span>
                    </label>

                    <label class="adjust-type-card" id="cardTypeADJUST" style="border: 2px solid var(--border-color); background: var(--card-bg); padding: 0.75rem 0.5rem; border-radius: var(--radius-md); cursor: pointer; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.25rem; transition: all 0.2s ease;">
                        <input type="radio" name="adjustment_type" value="ADJUSTMENT" onchange="onAdjustmentTypeChange('ADJUSTMENT')" style="display: none;">
                        <i data-lucide="refresh-cw" style="width: 20px; height: 20px; color: #4F46E5;"></i>
                        <span style="font-weight: 700; font-size: 0.8125rem; color: var(--text-main);">Opname Fisik</span>
                        <span style="font-size: 0.6875rem; color: var(--text-muted);">Set Angka Aktual</span>
                    </label>

                </div>
            </div>

            <!-- Dynamic Input Field -->
            <div class="form-group" id="groupDeltaQty">
                <label id="labelDeltaQty" for="inputDeltaQty" class="form-label">Jumlah Unit Penambahan (+ IN) <span style="color: var(--danger);">*</span></label>
                <input type="number" id="inputDeltaQty" name="quantity" class="form-control" min="1" placeholder="Masukkan jumlah unit (misal: 10)" oninput="calculateEstimatedStock()">
            </div>

            <div class="form-group" id="groupTargetStock" style="display: none;">
                <label for="inputTargetStock" class="form-label">Stok Fisik Aktual Hasil Opname <span style="color: var(--danger);">*</span></label>
                <input type="number" id="inputTargetStock" name="target_stock" class="form-control" min="0" placeholder="Masukkan total stok fisik akhir (misal: 25)" oninput="calculateEstimatedStock()">
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Sistem akan otomatis menghitung selisih mutasi dari stok tercatat.</small>
            </div>

            <!-- Calculation Preview Box -->
            <div id="previewCalcBox" style="display: none; padding: 1rem; border-radius: var(--radius-lg); background: #F8FAFC; border: 1px dashed var(--primary); text-align: center;">
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.04em;">Simulasi Perubahan Stok</div>
                <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; margin-top: 0.5rem; font-size: 0.9375rem;">
                    <span style="color: var(--text-muted);" id="simOldStock">0</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px; color: var(--primary);"></i>
                    <span style="font-weight: 700; color: var(--text-main);" id="simDelta">+0</span>
                    <i data-lucide="arrow-right" style="width: 16px; height: 16px; color: var(--primary);"></i>
                    <span style="font-weight: 800; font-size: 1.15rem; color: var(--primary);" id="simNewStock">0 unit</span>
                </div>
            </div>

            <!-- Note / Reason (Required) -->
            <div class="form-group">
                <label for="adjustNote" class="form-label">Alasan / Catatan Penyesuaian <span style="color: var(--danger);">*</span></label>
                <textarea id="adjustNote" name="note" class="form-control" rows="3" placeholder="Contoh: Penerimaan restock batch supplier #INV-883, atau Penyesuaian fisik opname gudang Agustus..." required></textarea>
            </div>

        </div>

        <!-- Hidden submit for drawer footer trigger -->
        <button type="submit" id="hiddenSubmitBtn" style="display: none;"></button>
    </form>

    <x-slot name="footer">
        <button type="button" class="btn-secondary" onclick="closeDrawer('adjustmentDrawer')">Batal</button>
        <button type="button" class="btn-primary" id="submitAdjustBtn" onclick="$('#hiddenSubmitBtn').click()">
            <span id="submitAdjustBtnText">Simpan Penyesuaian</span>
        </button>
    </x-slot>
</x-drawer>

@endsection

@push('scripts')
<script>
    let stocksDataTable;
    let movementsDataTable;
    let currentSelectedStock = 0;
    let currentAdjustmentMode = 'IN';
    let filterVariantId = '';

    $(document).ready(function() {
        initStocksDataTable();
        initMovementsDataTable();
        initDateRangePicker();

        // Stock status filter event
        $('#filterStockStatus').on('change', function() {
            stocksDataTable.ajax.reload();
        });

        // Movement type filter event
        $('#filterMovementType').on('change', function() {
            movementsDataTable.ajax.reload();
        });
    });

    // Switch between Tab 1 and Tab 2
    function switchInventoryTab(tabName) {
        $('.tab-btn').removeClass('active').css({
            'border-bottom-color': 'transparent',
            'color': 'var(--text-muted)'
        });
        $('.inventory-section').hide();

        if (tabName === 'stocks') {
            $('#tabBtnStocks').addClass('active').css({
                'border-bottom-color': 'var(--primary)',
                'color': 'var(--primary)'
            });
            $('#sectionStocks').fadeIn(150);
            if (stocksDataTable) stocksDataTable.columns.adjust().draw();
        } else {
            $('#tabBtnMovements').addClass('active').css({
                'border-bottom-color': 'var(--primary)',
                'color': 'var(--primary)'
            });
            $('#sectionMovements').fadeIn(150);
            if (movementsDataTable) movementsDataTable.columns.adjust().draw();
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Initialize Stocks DataTable
    function initStocksDataTable() {
        stocksDataTable = $('#stocksTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory.index') }}",
                data: function(d) {
                    d.tab = 'stocks';
                    d.stock_status = $('#filterStockStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'product_info', name: 'product.name' },
                { data: 'variant_attributes', name: 'sku', orderable: false },
                { data: 'price_formatted', name: 'price' },
                { data: 'stock_display', name: 'stock' },
                { data: 'status_badge', name: 'stock', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[4, 'asc']], // Order by stock ascending (highlight low stock)
            language: createDataTableLanguage('Memuat Data Stok SKU', 'Mengambil status stok fisik seluruh varian produk...', {
                searchPlaceholder: "Cari SKU, nama produk..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // Initialize Movements DataTable
    function initMovementsDataTable() {
        movementsDataTable = $('#movementsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory.index') }}",
                data: function(d) {
                    d.tab = 'movements';
                    d.movement_type = $('#filterMovementType').val();
                    d.variant_id = filterVariantId;
                    
                    const dateVal = $('#filterDateRange').val();
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
                { data: 'sku_info', name: 'productVariant.sku' },
                { data: 'type_badge', name: 'type' },
                { data: 'quantity_formatted', name: 'quantity' },
                { data: 'note_display', name: 'note' }
            ],
            order: [[1, 'desc']], // Order by created_at desc
            language: createDataTableLanguage('Memuat Buku Mutasi', 'Mengambil riwayat arus keluar-masuk stok barang...', {
                searchPlaceholder: "Cari SKU, catatan mutasi..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // Initialize Date Range Picker
    function initDateRangePicker() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#filterDateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onClose: function(selectedDates, dateStr, instance) {
                    movementsDataTable.ajax.reload();
                }
            });
        }
    }

    function resetStocksFilter() {
        $('#filterStockStatus').val('');
        stocksDataTable.ajax.reload();
    }

    function resetMovementsFilter() {
        $('#filterMovementType').val('');
        $('#filterDateRange').val('');
        if ($('#filterDateRange')[0]._flatpickr) {
            $('#filterDateRange')[0]._flatpickr.clear();
        }
        clearSkuMovementFilter();
    }

    // Filter movements by specific SKU from Tab 1 button
    function filterMovementsBySku(variantId, sku) {
        filterVariantId = variantId;
        $('#skuFilterLabel').text('SKU: ' + sku);
        $('#skuFilterBadgeContainer').show();
        switchInventoryTab('movements');
        movementsDataTable.ajax.reload();
    }

    function clearSkuMovementFilter() {
        filterVariantId = '';
        $('#skuFilterBadgeContainer').hide();
        movementsDataTable.ajax.reload();
    }

    // Open Stock Adjustment Drawer
    function openAdjustmentDrawer(variantId = null, sku = null, stock = null) {
        $('#adjustmentForm')[0].reset();
        $('#previewCalcBox').hide();

        if (variantId) {
            $('#selectSkuAdjust').val(variantId);
            $('#adjustVariantId').val(variantId);
            currentSelectedStock = parseInt(stock) || 0;
            $('#currentStockDisplay').text(currentSelectedStock + ' unit');
            $('#stockInfoBanner').slideDown(150);
            openDrawer('adjustmentDrawer', 'Penyesuaian Stok: ' + sku, 'Update kuantitas stok fisik gudang');
        } else {
            $('#selectSkuAdjust').val('');
            $('#adjustVariantId').val('');
            currentSelectedStock = 0;
            $('#stockInfoBanner').hide();
            openDrawer('adjustmentDrawer', 'Penyesuaian Stok Manual', 'Pilih SKU dan tentukan mutasi penyesuaian');
        }

        onAdjustmentTypeChange('IN');
    }

    // When SKU selection dropdown changes
    function onVariantSelected(variantId) {
        if (!variantId) {
            $('#adjustVariantId').val('');
            $('#stockInfoBanner').slideUp(150);
            $('#previewCalcBox').slideUp(150);
            currentSelectedStock = 0;
            return;
        }

        const selectedOption = $(`#selectSkuAdjust option[value="${variantId}"]`);
        const stock = parseInt(selectedOption.data('stock')) || 0;
        const sku = selectedOption.data('sku');

        $('#adjustVariantId').val(variantId);
        currentSelectedStock = stock;
        $('#currentStockDisplay').text(stock + ' unit');
        $('#stockInfoBanner').slideDown(150);
        calculateEstimatedStock();
    }

    // Adjustment mode change (IN, OUT, ADJUSTMENT)
    function onAdjustmentTypeChange(mode) {
        currentAdjustmentMode = mode;
        $('.adjust-type-card').css({
            'border-color': 'var(--border-color)',
            'background': 'var(--card-bg)'
        });

        if (mode === 'IN') {
            $('#cardTypeIN').css({ 'border-color': 'var(--primary)', 'background': 'var(--primary-light)' });
            $('#groupDeltaQty').show();
            $('#groupTargetStock').hide();
            $('#labelDeltaQty').html('Jumlah Unit Penambahan (+ IN) <span style="color: var(--danger);">*</span>');
            $('#inputDeltaQty').attr('placeholder', 'Masukkan jumlah unit masuk (misal: 10)');
        } else if (mode === 'OUT') {
            $('#cardTypeOUT').css({ 'border-color': 'var(--danger)', 'background': '#FEF2F2' });
            $('#groupDeltaQty').show();
            $('#groupTargetStock').hide();
            $('#labelDeltaQty').html('Jumlah Unit Pengurangan (- OUT) <span style="color: var(--danger);">*</span>');
            $('#inputDeltaQty').attr('placeholder', 'Masukkan jumlah unit keluar (misal: 5)');
        } else { // ADJUSTMENT
            $('#cardTypeADJUST').css({ 'border-color': '#4F46E5', 'background': '#EEF2FF' });
            $('#groupDeltaQty').hide();
            $('#groupTargetStock').show();
        }

        calculateEstimatedStock();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Real-time calculation simulation
    function calculateEstimatedStock() {
        if (!$('#adjustVariantId').val()) {
            $('#previewCalcBox').hide();
            return;
        }

        let oldStock = currentSelectedStock;
        let delta = 0;
        let newStock = oldStock;

        if (currentAdjustmentMode === 'IN') {
            const qty = parseInt($('#inputDeltaQty').val()) || 0;
            if (qty > 0) {
                delta = qty;
                newStock = oldStock + delta;
                $('#simOldStock').text(oldStock + ' unit');
                $('#simDelta').text('+' + delta + ' unit').css('color', 'var(--success)');
                $('#simNewStock').text(newStock + ' unit');
                $('#previewCalcBox').slideDown(100);
            } else {
                $('#previewCalcBox').slideUp(100);
            }
        } else if (currentAdjustmentMode === 'OUT') {
            const qty = parseInt($('#inputDeltaQty').val()) || 0;
            if (qty > 0) {
                delta = -qty;
                newStock = oldStock + delta;
                $('#simOldStock').text(oldStock + ' unit');
                $('#simDelta').text('-' + qty + ' unit').css('color', 'var(--danger)');
                $('#simNewStock').text(newStock + ' unit');
                $('#previewCalcBox').slideDown(100);
            } else {
                $('#previewCalcBox').slideUp(100);
            }
        } else { // ADJUSTMENT
            const targetStr = $('#inputTargetStock').val();
            if (targetStr !== '') {
                const target = parseInt(targetStr) || 0;
                delta = target - oldStock;
                newStock = target;
                $('#simOldStock').text(oldStock + ' unit');
                let deltaText = delta >= 0 ? '+' + delta + ' unit' : delta + ' unit';
                let deltaColor = delta >= 0 ? 'var(--success)' : 'var(--danger)';
                $('#simDelta').text(deltaText).css('color', deltaColor);
                $('#simNewStock').text(newStock + ' unit');
                $('#previewCalcBox').slideDown(100);
            } else {
                $('#previewCalcBox').slideUp(100);
            }
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Handle AJAX Form Submit
    function handleAdjustmentSubmit(e) {
        e.preventDefault();

        const variantId = $('#adjustVariantId').val();
        if (!variantId) {
            showToast('warning', 'Peringatan', 'Silakan pilih varian SKU terlebih dahulu.');
            return;
        }

        $('#submitAdjustBtn').prop('disabled', true);
        $('#submitAdjustBtnText').text('Memproses...');
        showPreloader('Menyimpan Penyesuaian', 'Sedang mengupdate stok dan mencatat buku mutasi...');

        const formData = new FormData($('#adjustmentForm')[0]);

        $.ajax({
            url: "{{ route('admin.inventory.adjust') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                hidePreloader();
                $('#submitAdjustBtn').prop('disabled', false);
                $('#submitAdjustBtnText').text('Simpan Penyesuaian');
                closeDrawer('adjustmentDrawer');
                showToast('success', 'Berhasil', response.message);

                // Reload data
                stocksDataTable.ajax.reload(null, false);
                movementsDataTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#submitAdjustBtn').prop('disabled', false);
                $('#submitAdjustBtnText').text('Simpan Penyesuaian');

                let errorMsg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        errorMsg = xhr.responseJSON.errors[firstKey][0];
                    }
                }
                showToast('error', 'Gagal Menyesuaikan Stok', errorMsg);
            }
        });
    }
</script>
@endpush
