@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="panel-title" style="font-size: 1.25rem;">Category Management</h2>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola kategori dan hierarki sub-kategori produk katalog.
            </p>
        </div>
        <button type="button" class="btn-primary" onclick="openCreateCategoryDrawer()">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>
    
    <div class="panel-content">
        <div class="table-responsive">
            <table id="categoriesTable" class="dataTable display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Induk (Parent)</th>
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

<!-- Slide-Over Drawer for Create / Edit Category -->
<x-drawer id="categoryDrawer" title="Tambah Kategori Baru" width="480px">
    <form id="categoryForm" onsubmit="handleCategorySubmit(event)">
        <input type="hidden" id="categoryId" name="id">
        
        <div class="form-group">
            <label for="categoryName" class="form-label">Nama Kategori <span style="color: var(--danger);">*</span></label>
            <input type="text" id="categoryName" name="name" class="form-control" placeholder="Contoh: Pakaian Pria, Elektronik" required>
            <span id="categoryNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label class="form-label">Kategori Induk (Opsional)</label>
            @php
                $parentOptions = $parentCategories->pluck('name', 'id')->toArray();
            @endphp
            <x-searchable-select id="category_parent_id" name="parent_id" placeholder="-- Tanpa Induk (Root Category) --" :options="$parentOptions" />
            <span id="categoryParentError" class="form-error" style="display: none;"></span>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('categoryDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveCategoryBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveCategoryBtnText">Simpan</span>
            </button>
        </div>
    </form>
</x-drawer>
@endsection

@push('scripts')
<script>
    let categoriesTable;
    
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        categoriesTable = $('#categoriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.categories.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name_badge', name: 'name' },
                { data: 'parent_name', name: 'parent.name' },
                { data: 'slug_pill', name: 'slug' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                processing: "Memuat data kategori...",
                search: "",
                searchPlaceholder: "Cari kategori...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori",
                infoEmpty: "Tidak ada data kategori",
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

    function openCreateCategoryDrawer() {
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('#categoryNameError').hide();
        $('#categoryParentError').hide();
        setSearchableSelectValue('category_parent_id', '');
        $('#saveCategoryBtn').prop('disabled', false);
        $('#saveCategoryBtnText').text('Simpan');
        openDrawer('categoryDrawer', 'Tambah Kategori Baru', 'Tentukan nama dan hirarki kategori');
    }

    function editCategory(id) {
        showPreloader('Mengambil Data', 'Mohon tunggu sebentar...');
        $.get(`/admin/master-data/categories/${id}/edit`, function(response) {
            hidePreloader();
            $('#categoryForm')[0].reset();
            $('#categoryNameError').hide();
            $('#categoryParentError').hide();
            $('#saveCategoryBtn').prop('disabled', false);
            $('#saveCategoryBtnText').text('Perbarui');
            
            $('#categoryId').val(response.data.id);
            $('#categoryName').val(response.data.name);
            setSearchableSelectValue('category_parent_id', response.data.parent_id || '');
            
            openDrawer('categoryDrawer', 'Edit Kategori', 'Perbarui informasi kategori');
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data kategori.');
        });
    }

    function handleCategorySubmit(e) {
        e.preventDefault();
        $('#saveCategoryBtn').prop('disabled', true);
        $('#saveCategoryBtnText').text('Menyimpan...');
        $('#categoryNameError').hide();
        $('#categoryParentError').hide();
        
        let id = $('#categoryId').val();
        let url = id ? `/admin/master-data/categories/${id}` : `{{ route('admin.categories.store') }}`;
        let type = id ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: type,
            data: $('#categoryForm').serialize(),
            success: function(response) {
                closeDrawer('categoryDrawer');
                showToast('success', 'Berhasil', response.message);
                categoriesTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                $('#saveCategoryBtn').prop('disabled', false);
                $('#saveCategoryBtnText').text(id ? 'Perbarui' : 'Simpan');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    if (xhr.responseJSON.errors.name) {
                        $('#categoryNameError').text(xhr.responseJSON.errors.name[0]).show();
                    }
                    if (xhr.responseJSON.errors.parent_id) {
                        $('#categoryParentError').text(xhr.responseJSON.errors.parent_id[0]).show();
                    }
                } else {
                    showToast('error', 'Gagal', 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function deleteCategory(id, name) {
        showDeleteConfirm({
            title: 'Konfirmasi Hapus Kategori',
            itemName: name,
            showReason: false,
            confirmBtnText: 'Ya, Hapus Kategori',
            onConfirm: function(reason) {
                showPreloader('Menghapus Data', 'Memproses penghapusan kategori...');
                $.ajax({
                    url: `/admin/master-data/categories/${id}`,
                    type: 'DELETE',
                    data: { reason: reason },
                    success: function(response) {
                        hidePreloader();
                        showToast('success', 'Berhasil', response.message);
                        categoriesTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        hidePreloader();
                        let msg = 'Gagal menghapus kategori.';
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
