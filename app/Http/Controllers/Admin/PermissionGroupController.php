<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use App\Enums\RoleEnum;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionGroupController extends Controller
{
    /**
     * التحقق من أن المستخدم سوبر أدمن
     */
    private function checkSuperAdmin(): void
    {
        $admin = auth()->guard('admin')->user() ?? (auth()->user() instanceof \App\Models\Admin ? auth()->user() : null);
        
        if (!$admin || !($admin instanceof \App\Models\Admin)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        
        // التحقق من Spatie Permission Role أولاً
        $isSuperAdmin = $admin->hasRole('super-admin');
        
        // إذا لم يكن لديه الدور من Spatie، نتحقق من حقل role في الجدول
        if (!$isSuperAdmin) {
            $roleValue = $admin->role instanceof \App\Enums\RoleEnum ? $admin->role->value : $admin->role;
            $isSuperAdmin = ($roleValue === 'super_admin');
        }
        
        if (!$isSuperAdmin) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
    }

    /**
     * عرض قائمة مجموعات الصلاحيات
     */
    public function index(): View
    {
        $this->checkSuperAdmin();

        $groups = PermissionGroup::with('permissions')
            ->latest()
            ->paginate(15);

        return view('admin.permission-groups.index', compact('groups'));
    }

    /**
     * عرض صفحة إنشاء مجموعة جديدة
     */
    public function create(): View
    {
        $this->checkSuperAdmin();

        // الصلاحيات التي نريد إخفاءها (يمكن تعديلها حسب الحاجة)
        $permissions = Permission::where('guard_name', 'web')
            ->whereRaw("name NOT LIKE 'api.%'") // إخفاء صلاحيات API
            ->whereRaw("name NOT LIKE 'roles.%'") // إخفاء صلاحيات إدارة الأدوار
            ->whereRaw("name NOT LIKE 'permissions.%'") // إخفاء صلاحيات إدارة الصلاحيات
            ->whereRaw("name NOT LIKE 'permission-groups.%'") // إخفاء صلاحيات إدارة مجموعات الصلاحيات (للسوبر أدمن فقط)
            ->whereRaw("name NOT LIKE 'dashboard.%'") // إخفاء صلاحيات Dashboard (للسوبر أدمن فقط)
            ->where('name', '!=', 'content.manage') // إخفاء صلاحية إدارة المحتوى (غير مستخدمة حالياً)
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                // تجميع الصلاحيات حسب النوع (مثل: users, orders, designs)
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'other';
            });

        return view('admin.permission-groups.create', compact('permissions'));
    }

    /**
     * حفظ مجموعة صلاحيات جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'nullable|boolean',
        ]);

        $group = PermissionGroup::create([
            'name' => [
                'ar' => $validated['name_ar'],
                'en' => $validated['name_en'],
            ],
            'description' => [
                'ar' => $validated['description_ar'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ],
            'is_active' => $request->has('is_active'),
        ]);

        $group->syncPermissions($validated['permissions']);

        return redirect()
            ->route('admin.permission-groups.index')
            ->with('success', 'تم إنشاء مجموعة الصلاحيات بنجاح');
    }

    /**
     * عرض صفحة تعديل مجموعة
     */
    public function edit(PermissionGroup $permissionGroup): View
    {
        $this->checkSuperAdmin();

        // الصلاحيات التي نريد إخفاءها (يمكن تعديلها حسب الحاجة)
        $permissions = Permission::where('guard_name', 'web')
            ->whereRaw("name NOT LIKE 'api.%'") // إخفاء صلاحيات API
            ->whereRaw("name NOT LIKE 'roles.%'") // إخفاء صلاحيات إدارة الأدوار
            ->whereRaw("name NOT LIKE 'permissions.%'") // إخفاء صلاحيات إدارة الصلاحيات
            ->whereRaw("name NOT LIKE 'permission-groups.%'") // إخفاء صلاحيات إدارة مجموعات الصلاحيات (للسوبر أدمن فقط)
            ->whereRaw("name NOT LIKE 'dashboard.%'") // إخفاء صلاحيات Dashboard (للسوبر أدمن فقط)
            ->where('name', '!=', 'content.manage') // إخفاء صلاحية إدارة المحتوى (غير مستخدمة حالياً)
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'other';
            });

        $selectedPermissions = $permissionGroup->permissions->pluck('id')->toArray();

        return view('admin.permission-groups.edit', compact('permissionGroup', 'permissions', 'selectedPermissions'));
    }

    /**
     * تحديث مجموعة صلاحيات
     */
    public function update(Request $request, PermissionGroup $permissionGroup): RedirectResponse
    {
        $this->checkSuperAdmin();

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'nullable|boolean',
        ]);

        $permissionGroup->update([
            'name' => [
                'ar' => $validated['name_ar'],
                'en' => $validated['name_en'],
            ],
            'description' => [
                'ar' => $validated['description_ar'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ],
            'is_active' => $request->has('is_active'),
        ]);

        $permissionGroup->syncPermissions($validated['permissions']);

        return redirect()
            ->route('admin.permission-groups.index')
            ->with('success', 'تم تحديث مجموعة الصلاحيات بنجاح');
    }

    /**
     * حذف مجموعة صلاحيات
     */
    public function destroy(PermissionGroup $permissionGroup): RedirectResponse
    {
        $this->checkSuperAdmin();

        $permissionGroup->delete();

        return redirect()
            ->route('admin.permission-groups.index')
            ->with('success', 'تم حذف مجموعة الصلاحيات بنجاح');
    }
}
