<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Admin::class);
        
        $admins = Admin::with('roles', 'permissions')->latest()->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Admin::class);
        $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
        
        return view('admin.admins.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Admin::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'is_active' => 'nullable|boolean',
            'permission_groups' => 'nullable|array',
            'permission_groups.*' => 'exists:permission_groups,id',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] === 'super_admin' ? RoleEnum::SUPER_ADMIN : RoleEnum::ADMIN,
            'is_active' => $request->has('is_active') ? true : false,
            'email_verified_at' => now(),
        ]);

        // تعيين الدور من Spatie Permission
        $spatieRole = Role::where('name', $validated['role'] === 'super_admin' ? 'super-admin' : 'admin')
            ->where('guard_name', 'web')
            ->first();
        
        if ($spatieRole) {
            $admin->assignRole($spatieRole);
        }

        // جمع الصلاحيات من المجموعات فقط
        $allPermissions = [];
        
        if ($request->has('permission_groups') && is_array($request->permission_groups)) {
            foreach ($request->permission_groups as $groupId) {
                $group = \App\Models\PermissionGroup::with('permissions')->find($groupId);
                if ($group) {
                    foreach ($group->permissions as $permission) {
                        $allPermissions[] = $permission->id;
                    }
                }
            }
        }
        
        // تعيين الصلاحيات
        if (!empty($allPermissions)) {
            $permissions = Permission::whereIn('id', $allPermissions)
                ->where('guard_name', 'web')
                ->get();
            
            $admin->syncPermissionsWithNotification($permissions);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم إضافة المشرف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        $this->authorize('view', $admin);
        $admin->load('roles', 'permissions');
        $userPermissions = $admin->getAllPermissions();
        $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
        
        return view('admin.admins.show', compact('admin', 'userPermissions', 'permissionGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        $this->authorize('update', $admin);
        $permissionGroups = \App\Models\PermissionGroup::active()->with('permissions')->get();
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'other';
            });
        
        $userPermissions = $admin->getAllPermissions()->pluck('id')->toArray();
        
        $userGroups = [];
        foreach ($permissionGroups as $group) {
            $groupPermissionIds = $group->permissions->pluck('id')->toArray();
            if (!empty($groupPermissionIds) && count(array_intersect($groupPermissionIds, $userPermissions)) === count($groupPermissionIds)) {
                $userGroups[] = $group->id;
            }
        }
        
        return view('admin.admins.edit', compact('admin', 'permissionGroups', 'permissions', 'userPermissions', 'userGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $this->authorize('update', $admin);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin',
            'is_active' => 'nullable|boolean',
            'permission_groups' => 'nullable|array',
            'permission_groups.*' => 'exists:permission_groups,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->role = $validated['role'] === 'super_admin' ? RoleEnum::SUPER_ADMIN : RoleEnum::ADMIN;
        $admin->is_active = $request->has('is_active') ? true : false;
        
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }
        
        $admin->save();

        // تحديث الدور
        $spatieRole = Role::where('name', $validated['role'] === 'super_admin' ? 'super-admin' : 'admin')
            ->where('guard_name', 'web')
            ->first();
        
        if ($spatieRole) {
            $admin->syncRoles([$spatieRole]);
        }

        // جمع الصلاحيات
        $allPermissions = [];
        
        if ($request->has('permission_groups') && is_array($request->permission_groups)) {
            foreach ($request->permission_groups as $groupId) {
                $group = \App\Models\PermissionGroup::with('permissions')->find($groupId);
                if ($group) {
                    foreach ($group->permissions as $permission) {
                        $allPermissions[] = $permission->id;
                    }
                }
            }
        }
        
        if ($request->has('permissions') && is_array($request->permissions)) {
            foreach ($request->permissions as $permissionId) {
                if (!in_array($permissionId, $allPermissions)) {
                    $allPermissions[] = $permissionId;
                }
            }
        }
        
        // تحديث الصلاحيات
        if (!empty($allPermissions)) {
            $permissions = Permission::whereIn('id', $allPermissions)
                ->where('guard_name', 'web')
                ->get();
            
            $admin->syncPermissionsWithNotification($permissions);
        } else {
            $admin->syncPermissions([]);
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم تحديث المشرف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $this->authorize('delete', $admin);
        $admin->delete();
        
        return redirect()->route('admin.admins.index')
            ->with('success', 'تم حذف المشرف بنجاح');
    }

    /**
     * صفحة إدارة مجموعات الصلاحيات والصلاحيات
     */
    public function managePermissions()
    {
        $this->authorize('viewAny', Admin::class);
        
        $permissionGroups = \App\Models\PermissionGroup::with('permissions')->latest()->get();
        
        // تصفية الصلاحيات الإدارية التي لا يجب عرضها
        $permissions = Permission::where('guard_name', 'web')
            ->whereRaw("name NOT LIKE 'dashboard.%'") // إخفاء صلاحيات Dashboard
            ->whereRaw("name NOT LIKE 'permission-groups.%'") // إخفاء صلاحيات مجموعات الصلاحيات
            ->whereRaw("name NOT LIKE 'permissions.%'") // إخفاء صلاحيات إدارة الصلاحيات
            ->whereRaw("name NOT LIKE 'roles.%'") // إخفاء صلاحيات إدارة الأدوار
            ->where('name', '!=', 'content.manage') // إخفاء صلاحية إدارة المحتوى (غير مستخدمة حالياً)
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'other';
            });
        
        return view('admin.admins.permissions', compact('permissionGroups', 'permissions'));
    }
}
