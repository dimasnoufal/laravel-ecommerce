<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use App\Enums\AuditLogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of users with server-side DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('roles')->select('users.*');

            // Filter by role slug
            if ($request->filled('role') && $request->role !== 'all') {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('is_active', $request->status === 'active');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user_info', function ($row) {
                    $initials = strtoupper(substr($row->name, 0, 2));
                    $phoneHtml = $row->phone 
                        ? '<span class="user-subtext"><i data-lucide="phone" style="width: 12px; height: 12px;"></i> ' . e($row->phone) . '</span>' 
                        : '';
                    $verifiedBadge = $row->email_verified_at 
                        ? '<span class="verified-icon" title="Email Verified"><i data-lucide="check-circle" style="width: 14px; height: 14px; color: var(--success);"></i></span>' 
                        : '';

                    return '<div class="user-cell">
                                <div class="user-avatar-circle">' . $initials . '</div>
                                <div class="user-meta">
                                    <div class="user-name-row">
                                        <span class="user-fullname">' . e($row->name) . '</span>
                                        ' . $verifiedBadge . '
                                    </div>
                                    <span class="user-subtext"><i data-lucide="mail" style="width: 12px; height: 12px;"></i> ' . e($row->email) . '</span>
                                    ' . $phoneHtml . '
                                </div>
                            </div>';
                })
                ->addColumn('roles_badge', function ($row) {
                    if ($row->roles->isEmpty()) {
                        return '<span class="badge-role badge-role-none">Tanpa Role</span>';
                    }

                    $badges = [];
                    foreach ($row->roles as $role) {
                        $roleClass = match ($role->slug) {
                            'admin' => 'badge-role-admin',
                            'customer' => 'badge-role-customer',
                            default => 'badge-role-custom',
                        };
                        $badges[] = '<span class="badge-role ' . $roleClass . '">' . e($role->name) . '</span>';
                    }
                    return '<div class="roles-wrapper">' . implode(' ', $badges) . '</div>';
                })
                ->addColumn('status_pill', function ($row) {
                    $isAuthUser = $row->id === auth()->id();
                    $statusText = $row->is_active ? 'Aktif' : 'Nonaktif';
                    $statusClass = $row->is_active ? 'status-pill-success' : 'status-pill-danger';
                    $titleTooltip = $isAuthUser ? 'Akun Anda sendiri (tidak dapat dinonaktifkan)' : 'Klik untuk mengubah status';

                    return '<button type="button" class="status-pill-btn ' . $statusClass . '" 
                                onclick="' . ($isAuthUser ? "showToast('warning', 'Aksi Ditolak', 'Anda tidak dapat menonaktifkan akun sendiri.');" : "toggleUserStatus(" . $row->id . ")") . '" 
                                title="' . $titleTooltip . '">
                                <span class="status-dot"></span>
                                <span>' . $statusText . '</span>
                            </button>';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return '<div style="font-size: 0.8125rem; color: var(--text-muted);">' . $row->created_at->format('d M Y, H:i') . '</div>';
                })
                ->addColumn('action', function ($row) {
                    $isAuthUser = $row->id === auth()->id();
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    
                    $deleteBtn = $isAuthUser 
                        ? '<button type="button" class="tbl-btn tbl-btn-disabled" title="Tidak dapat menghapus akun sendiri" disabled>
                                <i data-lucide="shield-alert" style="width: 14px; height: 14px;"></i>
                                <span>Diri Sendiri</span>
                           </button>'
                        : '<button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteUser(' . $row->id . ', \'' . $safeName . '\')" title="Hapus User">
                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                <span>Hapus</span>
                           </button>';

                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editUser(' . $row->id . ')" title="Edit User">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="tbl-btn tbl-btn-secondary" onclick="openResetPasswordModal(' . $row->id . ', \'' . $safeName . '\')" title="Reset Password">
                                    <i data-lucide="key" style="width: 14px; height: 14px;"></i>
                                    <span>Password</span>
                                </button>
                                ' . $deleteBtn . '
                            </div>';
                })
                ->rawColumns(['user_info', 'roles_badge', 'status_pill', 'created_at_formatted', 'action'])
                ->make(true);
        }

        // Metrics Summary
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminUsers = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count();
        $customerUsers = User::whereHas('roles', fn($q) => $q->where('slug', 'customer'))->count();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact(
            'totalUsers',
            'activeUsers',
            'adminUsers',
            'customerUsers',
            'roles'
        ));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:25',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower(trim($request->email)),
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', true),
                'email_verified_at' => now(),
            ]);

            $user->roles()->sync($request->roles);

            // Audit Trail
            AuditLog::log(
                AuditLogAction::CREATE,
                "Super Admin membuat akun user baru: {$user->name} ({$user->email})",
                $user,
                [
                    'user_id' => $user->id,
                    'roles' => $user->roles->pluck('slug')->toArray(),
                    'is_active' => $user->is_active,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan ke sistem.',
                'data' => $user->load('roles')
            ], 201);
        });
    }

    /**
     * Show the form for editing the specified user (JSON for modal).
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'role_ids' => $user->roles->pluck('id')->toArray(),
                'is_auth_user' => $user->id === auth()->id(),
            ]
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Self-protection check
        if ($user->id === auth()->id() && !$request->boolean('is_active', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang login.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $user) {
            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'roles' => $user->roles->pluck('slug')->toArray(),
            ];

            $user->update([
                'name' => $request->name,
                'email' => strtolower(trim($request->email)),
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $user->roles()->sync($request->roles);

            // Audit Trail
            AuditLog::log(
                AuditLogAction::UPDATE,
                "Super Admin memperbarui data user: {$user->name} ({$user->email})",
                $user,
                [
                    'old' => $oldData,
                    'new' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'is_active' => $user->is_active,
                        'roles' => $user->roles->pluck('slug')->toArray(),
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diperbarui.',
                'data' => $user->load('roles')
            ]);
        });
    }

    /**
     * Fast toggle active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        // Self-protection
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengubah status akun Anda sendiri.',
            ], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        // Audit Trail
        $statusText = $user->is_active ? 'AKTIF' : 'NONAKTIF';
        AuditLog::log(
            AuditLogAction::UPDATE,
            "Super Admin mengubah status akun {$user->name} ({$user->email}) menjadi {$statusText}",
            $user,
            ['is_active' => $user->is_active]
        );

        return response()->json([
            'success' => true,
            'message' => "Status user {$user->name} berhasil diubah menjadi {$statusText}.",
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * Super Admin direct password reset.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Audit Trail
        AuditLog::log(
            AuditLogAction::UPDATE,
            "Super Admin melakukan reset password untuk user {$user->name} ({$user->email})",
            $user
        );

        return response()->json([
            'success' => true,
            'message' => "Password user {$user->name} berhasil direset.",
        ]);
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(User $user)
    {
        // Self-protection
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Soft delete user
        $user->delete();

        // Audit Trail
        AuditLog::log(
            AuditLogAction::DELETE,
            "Super Admin menghapus akun user: {$userName} ({$userEmail})",
            $user
        );

        return response()->json([
            'success' => true,
            'message' => "User {$userName} berhasil dihapus dari sistem.",
        ]);
    }
}
