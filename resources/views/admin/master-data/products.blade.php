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
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
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
                    @if(isset($initialProducts) && $initialProducts->count() > 0)
                        @foreach($initialProducts as $product)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        $primaryImg = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                        $imgSrc = $primaryImg ? asset('storage/' . $primaryImg->image_path) : null;
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        @if($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="{{ $product->name }}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);">
                                        @else
                                            <div class="category-icon-box" style="width: 44px; height: 44px; background: var(--primary-light); color: var(--primary); border-radius: 8px; font-size: 18px;">
                                                <i data-lucide="package"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-main); font-size: 0.9375rem;">{{ $product->name }}</div>
                                            <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
                                                <code class="code-pill" style="font-size: 0.7rem; padding: 0.1rem 0.35rem;">{{ $product->slug }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="status-pill" style="background: var(--info-bg); color: var(--info);">{{ $product->category->name }}</span>
                                    @else
                                        <span style="color: var(--text-light); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->brand)
                                        <span class="status-pill" style="background: var(--warning-bg); color: var(--warning);">{{ $product->brand->name }}</span>
                                    @else
                                        <span style="color: var(--text-light); font-size: 0.8125rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $minPrice = $product->min_price;
                                        $maxPrice = $product->max_price;
                                    @endphp
                                    @if($minPrice === null && $maxPrice === null)
                                        <span style="color: var(--text-light); font-size: 0.8125rem;">Rp 0</span>
                                    @elseif($minPrice == $maxPrice)
                                        <span style="font-weight: 600; color: var(--text-main);">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                                    @else
                                        <span style="font-weight: 600; color: var(--text-main);">Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalStock = (int) ($product->total_stock ?? 0);
                                        $variantCount = (int) ($product->variant_count ?? 0);
                                        $badgeStyle = $totalStock > 0 ? 'color: var(--success);' : 'color: var(--danger);';
                                    @endphp
                                    <div>
                                        <div style="font-weight: 600; {{ $badgeStyle }}">{{ number_format($totalStock, 0, ',', '.') }} unit</div>
                                        <div style="font-size: 0.725rem; color: var(--text-muted);">{{ $variantCount }} varian SKU</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($product->is_active)
                                        <span class="status-pill" style="background: var(--success-bg); color: var(--success);">AKTIF</span>
                                    @else
                                        <span class="status-pill" style="background: var(--danger-bg); color: var(--danger);">NONAKTIF</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="tbl-btn tbl-btn-edit" title="Edit Produk" onclick="showPreloader('Mengambil Data Produk', 'Memuat katalog, galeri foto, dan matriks varian...')">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteProduct({{ $product->id }}, '{{ addslashes(htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8')) }}')" title="Hapus Produk">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
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
            deferLoading: {{ $totalProducts ?? 0 }},
            order: [],
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
            language: createDataTableLanguage('Memuat Data Produk', 'Mengambil daftar produk, varian, dan status stok...', {
                searchPlaceholder: "Cari nama produk..."
            }),
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
                showPreloader('Menghapus Produk', 'Memproses penghapusan katalog dan varian dari database...');
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
