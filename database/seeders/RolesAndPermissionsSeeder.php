<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view', 'permissions.update',
            'dashboard.access',
            'content.manage',
            'locations.view', 'locations.manage',
            'orders.view', 'orders.manage',
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

        $this->command->info('✅ تم إنشاء الأدوار والصلاحيات بنجاح!');
        $this->command->info('   - Web Guard: ' . count($webPermissions) . ' صلاحية');
        $this->command->info('   - API Guard: ' . count($apiPermissions) . ' صلاحية');
    }
}
