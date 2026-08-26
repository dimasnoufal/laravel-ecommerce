@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Akun')

@section('styles')
<style>
    /* User Avatar & Cells */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .user-avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), #6366f1);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }
    .user-meta {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }
    .user-name-row {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .user-fullname {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.875rem;
    }
    .user-subtext {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Role Badges */
    .roles-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .badge-role {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .badge-role-admin {
        background: var(--primary-light);
        color: var(--primary);
        border: 1px solid rgba(37, 99, 235, 0.2);
    }
    .badge-role-customer {
        background: var(--success-bg);
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .badge-role-custom {
        background: var(--warning-bg);
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    .badge-role-none {
        background: var(--bg-body);
        color: var(--text-light);
        border: 1px solid var(--border-color);
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

    /* Filter Tabs */
    .role-filter-tab {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-muted);
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .role-filter-tab:hover {
        background: var(--bg-body);
        color: var(--text-main);
    }
    .role-filter-tab.active {
        background: var(--card-bg);
        color: var(--primary);
        border-color: var(--border-color);
        box-shadow: var(--shadow-sm);
        font-weight: 700;
    }
    .role-count-badge {
        font-size: 0.7rem;
        padding: 0.1rem 0.4rem;
        border-radius: 10px;
        background: var(--bg-body);
        color: var(--text-muted);
    }
    .role-filter-tab.active .role-count-badge {
        background: var(--primary-light);
        color: var(--primary);
    }

    /* Role selection cards in form */
    .role-select-card {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: var(--bg-body);
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }
    .role-select-card:hover {
        border-color: var(--primary);
        background: var(--card-bg);
    }
    .role-select-card input[type="checkbox"] {
        margin-top: 0.2rem;
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }
    .role-select-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-main);
        cursor: pointer;
    }
    .role-select-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.15rem;
        line-height: 1.3;
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
                <span style="color: var(--primary); font-weight: 600;">Users Directory</span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Manajemen Pengguna & Akun</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">
                Kelola kredensial, hak akses peran (roles), status akun, dan audit keamanan pengguna.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn-primary" onclick="openCreateUserDrawer()">
                <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        <x-kpi-card 
            title="Total Pengguna" 
            value="{{ number_format($totalUsers) }}" 
            subtext="Semua akun terdaftar di sistem" 
            icon="users" 
            color="primary" 
        />
        <x-kpi-card 
            title="Akun Aktif" 
            value="{{ number_format($activeUsers) }}" 
            subtext="Dapat login & berbelanja" 
            icon="user-check" 
            color="success" 
        />
        <x-kpi-card 
            title="Administrator & Staff" 
            value="{{ number_format($adminUsers) }}" 
            subtext="Memiliki akses dashboard admin" 
            icon="shield-check" 
            color="warning" 
        />
        <x-kpi-card 
            title="Pelanggan (Customers)" 
            value="{{ number_format($customerUsers) }}" 
            subtext="Pengguna aplikasi e-commerce" 
            icon="shopping-bag" 
            color="info" 
        />
    </div>

    <!-- Main Table Panel -->
    <div class="panel-card" style="padding: 1.5rem;">
        
        <!-- Filter Bar & Tabs -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            
            <!-- Role Tabs -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; background: var(--bg-body); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                <button type="button" class="role-filter-tab active" onclick="setRoleFilter('all', this)">
                    <span>Semua Pengguna</span>
                    <span class="role-count-badge">{{ $totalUsers }}</span>
                </button>
                <button type="button" class="role-filter-tab" onclick="setRoleFilter('admin', this)">
                    <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                    <span>Admin</span>
                    <span class="role-count-badge">{{ $adminUsers }}</span>
                </button>
                <button type="button" class="role-filter-tab" onclick="setRoleFilter('customer', this)">
                    <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                    <span>Customer</span>
                    <span class="role-count-badge">{{ $customerUsers }}</span>
                </button>
                @foreach($roles->whereNotIn('slug', ['admin', 'customer']) as $customRole)
                    <button type="button" class="role-filter-tab" onclick="setRoleFilter('{{ $customRole->slug }}', this)">
                        <span>{{ $customRole->name }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Status Filter -->
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label for="filterStatus" style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted);">Status:</label>
                <select id="filterStatus" class="form-control" style="width: auto; min-width: 130px; padding: 0.45rem 0.75rem; font-size: 0.8125rem;" onchange="applyFilters()">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- DataTable -->
        <div class="panel-content">
            <div class="table-responsive">
                <table id="usersTable" class="dataTable display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Profil Pengguna</th>
                            <th>Hak Akses (Role)</th>
                            <th style="width: 100px; text-align: center;">Status</th>
                            <th>Terdaftar Pada</th>
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

<!-- Slide-Over Drawer for Create / Edit User -->
<x-drawer id="userDrawer" title="Tambah Pengguna Baru" width="520px">
    <form id="userForm" onsubmit="handleUserSubmit(event)">
        <input type="hidden" id="userId" name="id">

        <div class="form-group">
            <label for="userName" class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
            <input type="text" id="userName" name="name" class="form-control" placeholder="Contoh: John Doe" required>
            <span id="userNameError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="userEmail" class="form-label">Alamat Email <span style="color: var(--danger);">*</span></label>
            <input type="email" id="userEmail" name="email" class="form-control" placeholder="user@example.com" required>
            <span id="userEmailError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="userPhone" class="form-label">Nomor Telepon / WhatsApp</label>
            <input type="text" id="userPhone" name="phone" class="form-control" placeholder="081234567890">
            <span id="userPhoneError" class="form-error" style="display: none;"></span>
        </div>

        <!-- Password Fields (Required on Create, Hidden on Edit) -->
        <div id="passwordFieldsGroup">
            <div class="form-group">
                <label for="userPassword" class="form-label">Password <span style="color: var(--danger);">*</span></label>
                <input type="password" id="userPassword" name="password" class="form-control" placeholder="Minimal 8 karakter">
                <span id="userPasswordError" class="form-error" style="display: none;"></span>
            </div>

            <div class="form-group">
                <label for="userPasswordConfirm" class="form-label">Konfirmasi Password <span style="color: var(--danger);">*</span></label>
                <input type="password" id="userPasswordConfirm" name="password_confirmation" class="form-control" placeholder="Ulangi password">
            </div>
        </div>

        <div id="editPasswordNote" style="display: none; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.75rem; font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-weight: 600; margin-bottom: 0.2rem;">
                <i data-lucide="info" style="width: 14px; height: 14px;"></i>
                <span>Ubah Password</span>
            </div>
            Untuk mengubah password pengguna, gunakan tombol <strong>Password</strong> di tabel aksi.
        </div>

        <!-- Role Assignment -->
        <div class="form-group">
            <label class="form-label">Penugasan Hak Akses (Roles) <span style="color: var(--danger);">*</span></label>
            <div style="max-height: 220px; overflow-y: auto; padding-right: 0.25rem;">
                @foreach($roles as $role)
                    <label class="role-select-card" for="role_check_{{ $role->id }}">
                        <input type="checkbox" id="role_check_{{ $role->id }}" name="roles[]" value="{{ $role->id }}" class="role-checkbox">
                        <div>
                            <div class="role-select-label">{{ $role->name }} (<code>{{ $role->slug }}</code>)</div>
                            <div class="role-select-desc">{{ $role->description ?: 'Tidak ada deskripsi' }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <span id="userRolesError" class="form-error" style="display: none;"></span>
        </div>

        <!-- Status Switch -->
        <div class="form-group" id="statusSwitchGroup">
            <label class="form-label">Status Akun</label>
            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; background: var(--bg-body); padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <input type="checkbox" id="userIsActive" name="is_active" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary);">
                <div>
                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Akun Aktif</span>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Pengguna dapat masuk dan bertransaksi di sistem.</p>
                </div>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeDrawer('userDrawer')">Batal</button>
            <button type="submit" class="btn-primary" id="saveUserBtn">
                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                <span id="saveUserBtnText">Simpan Pengguna</span>
            </button>
        </div>
    </form>
</x-drawer>

<!-- Modal Reset Password -->
<x-modal id="resetPasswordModal" title="Reset Password Pengguna" width="450px">
    <form id="resetPasswordForm" onsubmit="handleResetPasswordSubmit(event)">
        <input type="hidden" id="resetUserId">
        
        <div style="background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem; margin-bottom: 1.25rem;">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Target Pengguna</div>
            <div id="resetTargetName" style="font-size: 0.9375rem; font-weight: 700; color: var(--text-main); margin-top: 0.2rem;"></div>
        </div>

        <div class="form-group">
            <label for="newPassword" class="form-label">Password Baru <span style="color: var(--danger);">*</span></label>
            <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="Minimal 8 karakter" required>
            <span id="newPasswordError" class="form-error" style="display: none;"></span>
        </div>

        <div class="form-group">
            <label for="newPasswordConfirm" class="form-label">Ulangi Password Baru <span style="color: var(--danger);">*</span></label>
            <input type="password" id="newPasswordConfirm" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <button type="button" class="btn-secondary" onclick="closeModal('resetPasswordModal')">Batal</button>
            <button type="submit" class="btn-primary" id="saveResetPasswordBtn">
                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                <span id="saveResetPasswordBtnText">Simpan Password Baru</span>
            </button>
        </div>
    </form>
</x-modal>

<!-- Delete Confirmation Modal -->
<x-confirm-delete-modal 
    id="deleteUserModal" 
    title="Hapus Akun Pengguna" 
    message="Apakah Anda yakin ingin menghapus pengguna ini? Akun akan dinonaktifkan dan dihapus (soft-delete) dari sistem." 
    onConfirm="confirmDeleteUser()" 
/>

@endsection

@push('scripts')
<script>
    let usersTable;
    let selectedRoleFilter = 'all';
    let targetDeleteUserId = null;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        usersTable = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.users.index') }}",
                data: function(d) {
                    d.role = selectedRoleFilter;
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'user_info', name: 'name' },
                { data: 'roles_badge', name: 'roles.name', orderable: false },
                { data: 'status_pill', name: 'is_active', className: 'text-center' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: createDataTableLanguage('Memuat Data Pengguna', 'Mengambil direktori pengguna dan hak akses...', {
                searchPlaceholder: "Cari nama, email, no HP..."
            }),
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    });

    function setRoleFilter(role, element) {
        selectedRoleFilter = role;
        $('.role-filter-tab').removeClass('active');
        $(element).addClass('active');
        usersTable.ajax.reload();
    }

    function applyFilters() {
        usersTable.ajax.reload();
    }

    function openCreateUserDrawer() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('.role-checkbox').prop('checked', false);
        $('#userIsActive').prop('checked', true);
        
        $('.form-error').hide();
        $('#passwordFieldsGroup').show();
        $('#userPassword').prop('required', true);
        $('#userPasswordConfirm').prop('required', true);
        $('#editPasswordNote').hide();
        
        $('#saveUserBtn').prop('disabled', false);
        $('#saveUserBtnText').text('Simpan Pengguna');
        
        openDrawer('userDrawer', 'Tambah Pengguna Baru', 'Buat akun dan tetapkan hak akses peran sistem');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function editUser(id) {
        showPreloader('Mengambil Data Pengguna', 'Menyiapkan formulir edit pengguna...');
        $.get(`/admin/users/${id}/edit`, function(response) {
            hidePreloader();
            let u = response.data;

            $('#userForm')[0].reset();
            $('.form-error').hide();
            $('#userId').val(u.id);
            $('#userName').val(u.name);
            $('#userEmail').val(u.email);
            $('#userPhone').val(u.phone || '');
            $('#userIsActive').prop('checked', u.is_active);

            // Set role checkboxes
            $('.role-checkbox').prop('checked', false);
            if (u.role_ids && u.role_ids.length > 0) {
                u.role_ids.forEach(function(roleId) {
                    $(`#role_check_${roleId}`).prop('checked', true);
                });
            }

            // Hide password fields in edit mode
            $('#passwordFieldsGroup').hide();
            $('#userPassword').prop('required', false);
            $('#userPasswordConfirm').prop('required', false);
            $('#editPasswordNote').show();

            $('#saveUserBtn').prop('disabled', false);
            $('#saveUserBtnText').text('Perbarui Data');

            openDrawer('userDrawer', 'Edit Pengguna: ' + u.name, 'Perbarui informasi profil dan penugasan peran');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }).fail(function(xhr) {
            hidePreloader();
            showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat mengambil data pengguna.');
        });
    }

    function handleUserSubmit(e) {
        e.preventDefault();

        let id = $('#userId').val();
        $('#saveUserBtn').prop('disabled', true);
        $('#saveUserBtnText').text('Menyimpan...');
        $('.form-error').hide();

        showPreloader(id ? 'Memperbarui Pengguna' : 'Menyimpan Pengguna', 'Menyimpan data pengguna ke database...');

        let url = id ? `/admin/users/${id}` : `{{ route('admin.users.store') }}`;
        let type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: $('#userForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeDrawer('userDrawer');
                showToast('success', 'Berhasil', response.message);
                usersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveUserBtn').prop('disabled', false);
                $('#saveUserBtnText').text(id ? 'Perbarui Data' : 'Simpan Pengguna');

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.name) $('#userNameError').text(errs.name[0]).show();
                    if (errs.email) $('#userEmailError').text(errs.email[0]).show();
                    if (errs.phone) $('#userPhoneError').text(errs.phone[0]).show();
                    if (errs.password) $('#userPasswordError').text(errs.password[0]).show();
                    if (errs.roles) $('#userRolesError').text(errs.roles[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.');
                }
            }
        });
    }

    function toggleUserStatus(id) {
        showPreloader('Mengubah Status', 'Memperbarui status aktif pengguna...');
        $.ajax({
            url: `/admin/users/${id}/toggle-status`,
            type: 'PATCH',
            success: function(response) {
                hidePreloader();
                showToast('success', 'Berhasil', response.message);
                usersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat mengubah status pengguna.');
            }
        });
    }

    function openResetPasswordModal(id, name) {
        $('#resetPasswordForm')[0].reset();
        $('.form-error').hide();
        $('#resetUserId').val(id);
        $('#resetTargetName').text(name);
        $('#saveResetPasswordBtn').prop('disabled', false);
        $('#saveResetPasswordBtnText').text('Simpan Password Baru');
        openModal('resetPasswordModal');
    }

    function handleResetPasswordSubmit(e) {
        e.preventDefault();

        let id = $('#resetUserId').val();
        $('#saveResetPasswordBtn').prop('disabled', true);
        $('#saveResetPasswordBtnText').text('Menyimpan...');
        $('#newPasswordError').hide();

        showPreloader('Reset Password', 'Sedang mengenkripsi dan memperbarui password...');

        $.ajax({
            url: `/admin/users/${id}/reset-password`,
            type: 'POST',
            data: $('#resetPasswordForm').serialize(),
            success: function(response) {
                hidePreloader();
                closeModal('resetPasswordModal');
                showToast('success', 'Password Berhasil Direset', response.message);
            },
            error: function(xhr) {
                hidePreloader();
                $('#saveResetPasswordBtn').prop('disabled', false);
                $('#saveResetPasswordBtnText').text('Simpan Password Baru');

                if (xhr.status === 422 && xhr.responseJSON?.errors?.new_password) {
                    $('#newPasswordError').text(xhr.responseJSON.errors.new_password[0]).show();
                } else {
                    showToast('error', 'Gagal', xhr.responseJSON?.message || 'Gagal mereset password.');
                }
            }
        });
    }

    function deleteUser(id, name) {
        targetDeleteUserId = id;
        $('#deleteUserModal .confirm-dialog-msg').text(`Apakah Anda yakin ingin menghapus akun "${name}"?`);
        openConfirmDeleteModal('deleteUserModal');
    }

    function confirmDeleteUser() {
        if (!targetDeleteUserId) return;

        showPreloader('Menghapus Pengguna', 'Sedang memproses penghapusan akun pengguna...');
        $.ajax({
            url: `/admin/users/${targetDeleteUserId}`,
            type: 'DELETE',
            success: function(response) {
                hidePreloader();
                closeConfirmDeleteModal('deleteUserModal');
                showToast('success', 'Pengguna Dihapus', response.message);
                usersTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                hidePreloader();
                closeConfirmDeleteModal('deleteUserModal');
                showToast('error', 'Gagal', xhr.responseJSON?.message || 'Tidak dapat menghapus pengguna.');
            }
        });
    }
</script>
@endpush
