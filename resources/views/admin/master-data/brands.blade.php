@extends('layouts.admin')

@section('title', 'Brand Management')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="panel-title" style="font-size: 1.25rem;">Brand Management</h2>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola master data merek / brand produk katalog toko.
            </p>
        </div>
        <button type="button" class="btn-primary" onclick="openCreateBrandDrawer()">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            <span>Tambah Brand</span>
        </button>
    </div>
    
    <div class="panel-content">
        <div class="table-responsive">
            <table id="brandsTable" class="dataTable display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Brand</th>
                        <th>Slug URL</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Slide-Over Drawer for Create / Edit Brand -->
<x-drawer id="brandDrawer" title="Tambah Brand Baru" width="460px">
    <form id="brandForm" onsubmit="handleBrandSubmit(event)">
        <input type="hidden" id="brandId" name="id">
        
        <div class="form-group">
            <label for="brandName" class="form-label">Nama Brand <span style="color: var(--danger);">*</span></label>
            <input type="text" id="brandName" name="name" class="form-control" placeholder="Contoh: Nike, Apple, Samsung" required>
            <span id="brandNameError" class="form-error" style="display: none;"></span>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('brandDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveBrandBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveBrandBtnText">Simpan</span>
            </button>
        </div>
    </form>
</x-drawer>
@endsection

@push('scripts')
<script>
    let brandsTable;
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        brandsTable = $('#brandsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.brands.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name_badge', name: 'name' },
                { data: 'slug_pill', name: 'slug' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                processing: "Memuat data brand...",
                search: "",
                searchPlaceholder: "Cari brand...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ brand",
                infoEmpty: "Tidak ada data brand",
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

    function openCreateBrandDrawer() {
        $('#brandForm')[0].reset();
        $('#brandId').val('');
        $('#brandNameError').hide();
        $('#saveBrandBtn').prop('disabled', false);
        $('#saveBrandBtnText').text('Simpan');
        openDrawer('brandDrawer', 'Tambah Brand Baru', 'Masukkan nama brand untuk katalog');
    }

    function editBrand(id) {
        showPreloader('Mengambil Data', 'Mohon tunggu sebentar...');
        $.get(`/admin/master-data/brands/${id}/edit`, function(response) {
            hidePreloader();
            $('#brandForm')[0].reset();
            $('#brandNameError').hide();
            $('#saveBrandBtn').prop('disabled', false);
            $('#saveBrandBtnText').text('Perbarui');
            
            $('#brandId').val(response.data.id);
            $('#brandName').val(response.data.name);
            
            openDrawer('brandDrawer', 'Edit Brand', 'Perbarui nama brand katalog');
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data brand.');
        });
    }

    function handleBrandSubmit(e) {
        e.preventDefault();
        $('#saveBrandBtn').prop('disabled', true);
        $('#saveBrandBtnText').text('Menyimpan...');
        $('#brandNameError').hide();
        
        let id = $('#brandId').val();
        let url = id ? `/admin/master-data/brands/${id}` : `{{ route('admin.brands.store') }}`;
        let type = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: type,
            data: $('#brandForm').serialize(),
            success: function(response) {
                closeDrawer('brandDrawer');
                showToast('success', 'Berhasil', response.message);
                brandsTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                $('#saveBrandBtn').prop('disabled', false);
                $('#saveBrandBtnText').text(id ? 'Perbarui' : 'Simpan');
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                    $('#brandNameError').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    showToast('error', 'Gagal', 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function deleteBrand(id, name) {
        showDeleteConfirm({
            title: 'Konfirmasi Hapus Brand',
            itemName: name,
            showReason: false,
            confirmBtnText: 'Ya, Hapus Brand',
            onConfirm: function(reason) {
                showPreloader('Menghapus Data', 'Memproses penghapusan brand...');
                $.ajax({
                    url: `/admin/master-data/brands/${id}`,
                    type: 'DELETE',
                    data: { reason: reason },
                    success: function(response) {
                        hidePreloader();
                        showToast('success', 'Berhasil', response.message);
                        brandsTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        hidePreloader();
                        let msg = 'Gagal menghapus brand.';
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
