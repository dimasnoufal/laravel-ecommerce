@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="panel-title" style="font-size: 1.25rem;">Product Management</h2>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola master katalog produk, galeri foto, kategori, brand, dan varian SKU harga & stok.
            </p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn-primary" onclick="showPreloader('Membuka Formulir', 'Menyiapkan form katalog & varian...')">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            <span>Tambah Produk</span>
        </a>
    </div>
    
    <div class="panel-content">
        <div class="table-responsive">
            <table id="productsTable" class="dataTable display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Brand</th>
                        <th>Rentang Harga</th>
                        <th>Total Stok</th>
                        <th style="text-align: center;">Status</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let productsTable;
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        productsTable = $('#productsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.products.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'product_info', name: 'name' },
                { data: 'category_name', name: 'category.name' },
                { data: 'brand_name', name: 'brand.name' },
                { data: 'price_range', name: 'price_range', orderable: false, searchable: false },
                { data: 'total_stock', name: 'total_stock', orderable: false, searchable: false },
                { data: 'status_pill', name: 'is_active', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                processing: "Memuat data produk...",
                search: "",
                searchPlaceholder: "Cari nama produk...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ produk",
                infoEmpty: "Tidak ada data produk",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    });

    function deleteProduct(id, name) {
        showDeleteConfirm({
            title: 'Konfirmasi Hapus Produk',
            itemName: name,
            showReason: false,
            confirmBtnText: 'Ya, Hapus Produk',
            onConfirm: function(reason) {
                showPreloader('Menghapus Produk', 'Memproses penghapusan katalog dan varian...');
                $.ajax({
                    url: `/admin/master-data/products/${id}`,
                    type: 'DELETE',
                    data: { reason: reason },
                    success: function(response) {
                        hidePreloader();
                        showToast('success', 'Berhasil', response.message);
                        productsTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        hidePreloader();
                        let msg = 'Gagal menghapus produk.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast('error', 'Gagal', msg);
                    }
                });
            }
        });
    }
</script>
@endpush
