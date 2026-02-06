<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionGroup;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ═══════════════════════════════════════════════════════
        // 📋 WEB GUARD PERMISSIONS (Dashboard)
        // ═══════════════════════════════════════════════════════
        $webPermissions = [
            // Users
            'users.view', 'users.create', 'users.update', 'users.delete',
            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.update', 'permissions.manage',
            'permission-groups.view', 'permission-groups.create', 'permission-groups.update', 'permission-groups.delete',
            // Dashboard
            'dashboard.access', 'dashboard.view_stats',
            // Orders
            'orders.view', 'orders.create', 'orders.update', 'orders.delete', 'orders.cancel', 'orders.manage',
            // Designs
            'designs.view', 'designs.create', 'designs.update', 'designs.delete', 'designs.approve',
            // Design Options
            'design-options.view', 'design-options.create', 'design-options.update', 'design-options.delete',
            // Coupons
            'coupons.view', 'coupons.create', 'coupons.update', 'coupons.delete',
            // Reviews
            'reviews.view', 'reviews.manage', 'reviews.delete',
            // Locations
            'locations.view', 'locations.create', 'locations.update', 'locations.delete', 'locations.manage',
            // Notifications
            'notifications.view', 'notifications.send',
            // Content
            'content.manage',
        ];

        foreach ($webPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ═══════════════════════════════════════════════════════
        // 📋 API GUARD PERMISSIONS (Mobile/API)
        // ═══════════════════════════════════════════════════════
        $apiPermissions = [
            'api.users.view', 'api.users.update',
            'api.locations.view', 'api.locations.create', 'api.locations.update', 'api.locations.delete',
            'api.orders.view', 'api.orders.create', 'api.orders.update',
            'api.designs.view', 'api.designs.create', 'api.designs.update', 'api.designs.delete',
        ];

        foreach ($apiPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // ═══════════════════════════════════════════════════════
        // 👑 WEB GUARD ROLES (Dashboard)
        // ═══════════════════════════════════════════════════════
        
        // 1. Super Admin (web)
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // Super Admin لديه جميع الصلاحيات تلقائياً

        // 2. Admin (web)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.update',
            'dashboard.access',
            'content.manage',
            'locations.view', 'locations.manage',
            'orders.view', 'orders.manage',
        ]);

        // ═══════════════════════════════════════════════════════
        // 📱 API GUARD ROLES (Mobile/API)
        // ═══════════════════════════════════════════════════════
        
        // 1. API User (api) - للمستخدمين العاديين
        $apiUser = Role::firstOrCreate(['name' => 'api-user', 'guard_name' => 'api']);
        $apiUser->syncPermissions([
            'api.users.view', 'api.users.update',
            'api.locations.view', 'api.locations.create', 'api.locations.update', 'api.locations.delete',
            'api.orders.view', 'api.orders.create', 'api.orders.update',
            'api.designs.view', 'api.designs.create', 'api.designs.update', 'api.designs.delete',
        ]);

        // 2. API Admin (api) - للمشرفين في API
        $apiAdmin = Role::firstOrCreate(['name' => 'api-admin', 'guard_name' => 'api']);
        // API Admin لديه جميع الصلاحيات
        $apiAdmin->syncPermissions($apiPermissions);
        
        // ملاحظة: المستخدمون العاديون (user, guest) يستخدمون RoleEnum
        // ولا يحتاجون roles من Spatie Permission
        // الصلاحيات تدار عبر Policies based على RoleEnum

        // ═══════════════════════════════════════════════════════
        // 📦 PERMISSION GROUPS (مجموعات الصلاحيات)
        // ═══════════════════════════════════════════════════════
        $this->createPermissionGroups($webPermissions);

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح!');
        $this->command->info('   - Web Guard: ' . count($webPermissions) . ' صلاحية');
        $this->command->info('   - API Guard: ' . count($apiPermissions) . ' صلاحية');
    }

    /**
     * إنشاء مجموعات الصلاحيات الافتراضية
     */
    private function createPermissionGroups(array $allPermissions): void
    {
        // الحصول على جميع الصلاحيات من قاعدة البيانات
        $permissions = Permission::where('guard_name', 'web')->get()->keyBy('name');

        // 1. إدارة الطلبات الكاملة
        $ordersGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة الطلبات الكاملة', 'en' => 'Full Orders Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة الطلبات (عرض، إنشاء، تعديل، حذف، إلغاء)',
                    'en' => 'Includes all order management permissions (view, create, update, delete, cancel)'
                ],
                'is_active' => true,
            ]
        );
        $ordersGroup->syncPermissions([
            $permissions['orders.view']->id ?? null,
            $permissions['orders.create']->id ?? null,
            $permissions['orders.update']->id ?? null,
            $permissions['orders.delete']->id ?? null,
            $permissions['orders.cancel']->id ?? null,
        ]);

        // 2. إدارة المستخدمين الكاملة
        $usersGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة المستخدمين الكاملة', 'en' => 'Full Users Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة المستخدمين (عرض، إنشاء، تعديل، حذف)',
                    'en' => 'Includes all user management permissions (view, create, update, delete)'
                ],
                'is_active' => true,
            ]
        );
        $usersGroup->syncPermissions([
            $permissions['users.view']->id ?? null,
            $permissions['users.create']->id ?? null,
            $permissions['users.update']->id ?? null,
            $permissions['users.delete']->id ?? null,
        ]);

        // 3. إدارة التصاميم الكاملة
        $designsGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة التصاميم الكاملة', 'en' => 'Full Designs Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة التصاميم (عرض، إنشاء، تعديل، حذف، موافقة)',
                    'en' => 'Includes all design management permissions (view, create, update, delete, approve)'
                ],
                'is_active' => true,
            ]
        );
        $designsGroup->syncPermissions([
            $permissions['designs.view']->id ?? null,
            $permissions['designs.create']->id ?? null,
            $permissions['designs.update']->id ?? null,
            $permissions['designs.delete']->id ?? null,
            $permissions['designs.approve']->id ?? null,
        ]);

        // 4. إدارة الكوبونات
        $couponsGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة الكوبونات', 'en' => 'Coupons Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة الكوبونات (عرض، إنشاء، تعديل، حذف)',
                    'en' => 'Includes all coupon management permissions (view, create, update, delete)'
                ],
                'is_active' => true,
            ]
        );
        $couponsGroup->syncPermissions([
            $permissions['coupons.view']->id ?? null,
            $permissions['coupons.create']->id ?? null,
            $permissions['coupons.update']->id ?? null,
            $permissions['coupons.delete']->id ?? null,
        ]);

        // 5. إدارة خيارات التصاميم
        $designOptionsGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة خيارات التصاميم', 'en' => 'Design Options Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة خيارات التصاميم (عرض، إنشاء، تعديل، حذف)',
                    'en' => 'Includes all design options management permissions (view, create, update, delete)'
                ],
                'is_active' => true,
            ]
        );
        $designOptionsGroup->syncPermissions([
            $permissions['design-options.view']->id ?? null,
            $permissions['design-options.create']->id ?? null,
            $permissions['design-options.update']->id ?? null,
            $permissions['design-options.delete']->id ?? null,
        ]);

        // 6. إدارة التقييمات
        $reviewsGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة التقييمات', 'en' => 'Reviews Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة التقييمات (عرض، إدارة، حذف)',
                    'en' => 'Includes all review management permissions (view, manage, delete)'
                ],
                'is_active' => true,
            ]
        );
        $reviewsGroup->syncPermissions([
            $permissions['reviews.view']->id ?? null,
            $permissions['reviews.manage']->id ?? null,
            $permissions['reviews.delete']->id ?? null,
        ]);

        // 7. إدارة المواقع
        $locationsGroup = PermissionGroup::firstOrCreate(
            ['name' => ['ar' => 'إدارة المواقع', 'en' => 'Locations Management']],
            [
                'description' => [
                    'ar' => 'يشمل جميع صلاحيات إدارة المواقع (عرض، إنشاء، تعديل، حذف)',
                    'en' => 'Includes all location management permissions (view, create, update, delete)'
                ],
                'is_active' => true,
            ]
        );
        $locationsGroup->syncPermissions([
            $permissions['locations.view']->id ?? null,
            $permissions['locations.create']->id ?? null,
            $permissions['locations.update']->id ?? null,
            $permissions['locations.delete']->id ?? null,
        ]);

        $this->command->info('✅ تم إنشاء مجموعات الصلاحيات الافتراضية بنجاح!');
    }
}
