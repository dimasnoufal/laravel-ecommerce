<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\AuditLog;
use App\Enums\AuditLogAction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Core system roles that cannot be deleted or renamed maliciously.
     */
    protected array $systemRoles = ['admin', 'customer'];

    /**
     * Display a listing of roles with server-side DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::withCount('users');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name_badge', function ($row) {
                    $isSystem = in_array($row->slug, $this->systemRoles);
                    $iconName = match ($row->slug) {
                        'admin' => 'shield-check',
                        'customer' => 'user-check',
                        default => 'user-cog',
                    };
                    $systemPill = $isSystem 
                        ? '<span class="badge-role badge-role-system" title="Core System Role"><i data-lucide="lock" style="width: 10px; height: 10px;"></i> System</span>' 
                        : '';

                    $desc = $row->description 
                        ? '<span class="role-desc-text">' . e($row->description) . '</span>' 
                        : '<span class="role-desc-text text-muted" style="font-style: italic;">Tidak ada deskripsi</span>';

                    return '<div class="role-cell">
                                <div class="role-icon-box ' . ($isSystem ? 'system-icon-box' : '') . '">
                                    <i data-lucide="' . $iconName . '" style="width: 18px; height: 18px;"></i>
                                </div>
                                <div class="role-meta">
                                    <div class="role-title-row">
                                        <span class="role-name-text">' . e($row->name) . '</span>
                                        ' . $systemPill . '
                                    </div>
                                    ' . $desc . '
                                </div>
                            </div>';
                })
                ->addColumn('slug_pill', function ($row) {
                    return '<code class="code-pill">' . e($row->slug) . '</code>';
                })
                ->addColumn('users_count_badge', function ($row) {
                    return '<span class="badge-user-count">
                                <i data-lucide="users" style="width: 12px; height: 12px;"></i>
                                <span>' . number_format($row->users_count) . ' User</span>
                            </span>';
                })
                ->addColumn('action', function ($row) {
                    $isSystem = in_array($row->slug, $this->systemRoles);
                    $safeName = addslashes(htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'));
                    
                    $deleteBtn = $isSystem
                        ? '<button type="button" class="tbl-btn tbl-btn-disabled" title="Role sistem terlindungi dan tidak dapat dihapus" disabled>
                                <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                                <span>Locked</span>
                           </button>'
                        : ($row->users_count > 0 
                            ? '<button type="button" class="tbl-btn tbl-btn-disabled" title="Role masih digunakan oleh ' . $row->users_count . ' user" disabled>
                                    <i data-lucide="alert-circle" style="width: 14px; height: 14px;"></i>
                                    <span>Digunakan</span>
                               </button>'
                            : '<button type="button" class="tbl-btn tbl-btn-delete" onclick="deleteRole(' . $row->id . ', \'' . $safeName . '\')" title="Hapus Role">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Hapus</span>
                               </button>');

                    return '<div class="table-actions">
                                <button type="button" class="tbl-btn tbl-btn-edit" onclick="editRole(' . $row->id . ')" title="Edit Role">
                                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                    <span>Edit</span>
                                </button>
                                ' . $deleteBtn . '
                            </div>';
                })
                ->rawColumns(['name_badge', 'slug_pill', 'users_count_badge', 'action'])
                ->make(true);
        }

        $totalRoles = Role::count();
        $systemRolesCount = Role::whereIn('slug', $this->systemRoles)->count();
        $customRolesCount = Role::whereNotIn('slug', $this->systemRoles)->count();

        return view('admin.roles.index', compact('totalRoles', 'systemRolesCount', 'customRolesCount'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'slug' => 'nullable|string|max:100|unique:roles,slug',
            'description' => 'nullable|string|max:255',
        ]);

        $slug = $request->filled('slug') 
            ? Str::slug($request->slug) 
            : Str::slug($request->name);

        // Check again after slugify
        if (Role::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Slug untuk role ini sudah digunakan. Silakan gunakan nama atau slug lain.',
                'errors' => ['slug' => ['Slug sudah terdaftar.']]
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        // Audit Trail
        AuditLog::log(
            AuditLogAction::CREATE,
            "Super Admin membuat role baru: {$role->name} ({$role->slug})",
            $role,
            ['slug' => $role->slug, 'description' => $role->description]
        );

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' berhasil dibuat.",
            'data' => $role
        ], 201);
    }

    /**
     * Show the form for editing the specified role (JSON for modal).
     */
    public function edit(Role $role)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'is_system' => in_array($role->slug, $this->systemRoles),
            ]
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $isSystem = in_array($role->slug, $this->systemRoles);

        $rules = [
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ];

        if (!$isSystem) {
            $rules['slug'] = 'required|string|max:100|unique:roles,slug,' . $role->id;
        }

        $request->validate($rules);

        $oldData = $role->toArray();

        $role->name = $request->name;
        $role->description = $request->description;

        if (!$isSystem) {
            $role->slug = Str::slug($request->slug);
        }

        $role->save();

        // Audit Trail
        AuditLog::log(
            AuditLogAction::UPDATE,
            "Super Admin memperbarui role: {$role->name} ({$role->slug})",
            $role,
            ['old' => $oldData, 'new' => $role->toArray()]
        );

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' berhasil diperbarui.",
            'data' => $role
        ]);
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->slug, $this->systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Role bawaan sistem terlindungi dan tidak dapat dihapus.',
            ], 422);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Role ini tidak dapat dihapus karena masih digunakan oleh akun aktif.',
            ], 422);
        }

        $roleName = $role->name;
        $roleSlug = $role->slug;

        $role->delete();

        // Audit Trail
        AuditLog::log(
            AuditLogAction::DELETE,
            "Super Admin menghapus role: {$roleName} ({$roleSlug})",
            $role
        );

        return response()->json([
            'success' => true,
            'message' => "Role '{$roleName}' berhasil dihapus.",
        ]);
    }
}
