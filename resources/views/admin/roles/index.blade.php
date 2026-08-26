@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses & Peran')

@section('styles')
<style>
    .role-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .role-icon-box {
        width: 38px;
        height: 38px;
        border-radius: var(--radius-md);
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .role-icon-box.system-icon-box {
        background: var(--warning-bg);
        color: var(--warning);
    }
    .role-meta {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    .role-title-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .role-name-text {
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.875rem;
    }
    .role-desc-text {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .badge-role-system {
        background: var(--warning-bg);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.25);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .badge-user-count {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        padding: 0.25rem 0.65rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-main);
    }
</style>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Page Header -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.25rem;">
                <span>User Management</span>
                <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                <span style="color: var(--primary); font-weight: 600;">Roles & Permissions</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Manajemen Hak Akses & Peran</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola master peran sistem (RBAC) untuk mendefinisikan tanggung jawab Administrator, Staff, dan Customer.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn-primary" onclick="openCreateRoleDrawer()">
                <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <x-kpi-card 
            title="Total Role Sistem" 
            value="{{ number_format($totalRoles) }}" 
            subtext="Semua tingkat otoritas terdaftar" 
            icon="shield" 
            color="primary" 
        />
        <x-kpi-card 
            title="Core System Roles" 
            value="{{ number_format($systemRolesCount) }}" 
            subtext="Peran permanen (Admin, Customer)" 
            icon="lock" 
            color="warning" 
        />
        <x-kpi-card 
            title="Custom Staff Roles" 
            value="{{ number_format($customRolesCount) }}" 
            subtext="Peran kustom operasional" 
            icon="user-check" 
            color="success" 
        />
    </div>

    <!-- Main Table Panel -->
    <div class="panel-card" style="padding: 1.5rem;">
        <div class="panel-header" style="margin-bottom: 1.25rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.125rem;">Daftar Peran & Otoritas</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Peran dengan tanda <code>System</code> tidak dapat dihapus untuk menjamin integritas autentikasi.
                </p>
            </div>
        </div>

        <div class="panel-content">
            <div class="table-responsive">
                <table id="rolesTable" class="dataTable display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Peran & Deskripsi</th>
                            <th>Kode Slug (Identifier)</th>
                            <th>Jumlah Pengguna</th>
                            <th style="width: 140px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Slide-Over Drawer for Create / Edit Role -->
<x-drawer id="roleDrawer" title="Tambah Role Baru" width="460px">
    <form id="roleForm" onsubmit="handleRoleSubmit(event)">
        <input type="hidden" id="roleId" name="id">

        <div class="form-group">
            <label for="roleName" class="form-label">Nama Peran (Role Name) <span style="color: var(--danger);">*</span></label>
            <input type="text" id="roleName" name="name" class="form-control" placeholder="Contoh: Inventory Staff, Finance Manager" required oninput="handleRoleNameInput(this)">
            <span id="roleNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="roleSlug" class="form-label">Slug URL / Identifier <span style="color: var(--danger);">*</span></label>
            <input type="text" id="roleSlug" name="slug" class="form-control" placeholder="contoh: inventory-staff" required>
            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">
                Digunakan untuk pengecekan middleware peran (contoh: <code>role:admin</code>).
            </span>
            <span id="roleSlugError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="roleDescription" class="form-label">Deskripsi Tanggung Jawab</label>
            <textarea id="roleDescription" name="description" class="form-control" rows="3" placeholder="Jelaskan cakupan wewenang atau hak akses peran ini..."></textarea>
            <span id="roleDescError" class="form-error" style="display: none;"></span>
        </div>

        <div id="systemRoleNotice" style="display: none; background: var(--warning-bg); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: var(--radius-md); padding: 0.75rem; font-size: 0.8125rem; color: var(--text-main); margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem; font-weight: 700; color: var(--warning); margin-bottom: 0.2rem;">
                <i data-lucide="shield-alert" style="width: 15px; height: 15px;"></i>
                <span>Core System Role</span>
            </div>
            Role bawaan sistem ini memiliki slug permanen yang tidak dapat diubah agar tidak memutus izin middleware rute.
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('roleDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveRoleBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveRoleBtnText">Simpan Role</span>
            </button>
        </div>
    </form>
</x-drawer>

<!-- Delete Confirmation Modal -->
<x-confirm-delete-modal 
    id="deleteRoleModal" 
    title="Hapus Role Otoritas" 
    message="Apakah Anda yakin ingin menghapus role ini? Role hanya dapat dihapus jika tidak ada user yang menggunakannya." 
    onConfirm="confirmDeleteRole()" 
/>

@endsection

@push('scripts')
<script>
    let rolesTable;
    let targetDeleteRoleId = null;
    let isSlugManual = false;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        rolesTable = $('#rolesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.roles.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name_badge', name: 'name' },
                { data: 'slug_pill', name: 'slug' },
                { data: 'users_count_badge', name: 'users_count', searchable: false, className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: createDataTableLanguage('Memuat Data Role', 'Mengambil daftar hak akses dan peran sistem...', {
                searchPlaceholder: "Cari nama atau slug role..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        $('#roleSlug').on('input', function() {
            isSlugManual = true;
        });
    });

    function slugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    }

    function handleRoleNameInput(input) {
        if (!isSlugManual && !$('#roleId').val()) {
            $('#roleSlug').val(slugify(input.value));
        }
    }

    function openCreateRoleDrawer() {
        $('#roleForm')[0].reset();
        $('#roleId').val('');
        isSlugManual = false;
        $('.form-error').hide();
        $('#roleSlug').prop('readonly', false).css('background', 'var(--bg-body)');
        $('#systemRoleNotice').hide();
        $('#saveRoleBtn').prop('disabled', false);
        $('#saveRoleBtnText').text('Simpan Role');
        openDrawer('roleDrawer', 'Tambah Role Baru', 'Buat peran baru untuk klasifikasi wewenang pengguna');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editRole(id) {
        showPreloader('Mengambil Data Role', 'Menyiapkan formulir edit peran...');
        $.get(`/admin/roles/${id}/edit`, function(response) {
            hidePreloader();
            let r = response.data;

            $('#roleForm')[0].reset();
            $('.form-error').hide();
            $('#roleId').val(r.id);
            $('#roleName').val(r.name);
            $('#roleSlug').val(r.slug);
            $('#roleDescription').val(r.description || '');

            if (r.is_system) {
                $('#roleSlug').prop('readonly', true).css('background', 'rgba(0,0,0,0.05)');
                $('#systemRoleNotice').show();
            } else {
                $('#roleSlug').prop('readonly', false).css('background', 'var(--bg-body)');
                $('#systemRoleNotice').hide();
            }

            $('#saveRoleBtn').prop('disabled', false);
            $('#saveRoleBtnText').text('Perbarui Role');

            openDrawer('roleDrawer', 'Edit Role: ' + r.name, 'Perbarui nama dan rincian wewenang peran');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }).fail(function() {
            hidePreloader();
            showToast('error', 'Gagal', 'Tidak dapat mengambil data role.');
        });
    }

    function handleRoleSubmit(e) {
        e.preventDefault();

        let id = $('#roleId').val();
        $('#saveRoleBtn').prop('disabled', true);
        $('#saveRoleBtnText').text('Menyimpan...');
        $('.form-error').hide();

        showPreloader(id ? 'Memperbarui Role' : 'Menyimpan Role', 'Menyimpan perubahan peran ke database...');

        let url = id ? `/admin/roles/${id}` : `{{ route('admin.roles.store') }}`;
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $('#roleForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeDrawer('roleDrawer');
                showToast('success', 'Berhasil', response.message);
                rolesTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveRoleBtn').prop('disabled', false);
                $('#saveRoleBtnText').text(id ? 'Perbarui Role' : 'Simpan Role');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.name) $('#roleNameError').text(errs.name[0]).show();
                    if (errs.slug) $('#roleSlugError').text(errs.slug[0]).show();
                    if (errs.description) $('#roleDescError').text(errs.description[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function deleteRole(id, name) {
        targetDeleteRoleId = id;
        $('#deleteRoleModal .confirm-dialog-msg').text(`Apakah Anda yakin ingin menghapus role "${name}"?`);
        openConfirmDeleteModal('deleteRoleModal');
    }

    function confirmDeleteRole() {
        if (!targetDeleteRoleId) return;

        showPreloader('Menghapus Role', 'Sedang memproses penghapusan role...');
        $.ajax({
            url: `/admin/roles/${targetDeleteRoleId}`,
            type: 'DELETE',
            success: function(response) {
                hidePreloader();
                closeConfirmDeleteModal('deleteRoleModal');
                showToast('success', 'Role Dihapus', response.message);
                rolesTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                closeConfirmDeleteModal('deleteRoleModal');
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat menghapus role.');
            }
        });
    }
</script>
@endpush
