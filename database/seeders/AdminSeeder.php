<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@kandura.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => RoleEnum::SUPER_ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // تعيين دور Super Admin من Spatie Permission
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdminRole && !$superAdmin->hasRole($superAdminRole)) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Admin
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@kandura.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => RoleEnum::ADMIN,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // تعيين دور Admin من Spatie Permission
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole && !$admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('✅ تم إنشاء حسابات المشرفين بنجاح!');
        $this->command->info('📧 Super Admin: superadmin@kandura.com / password');
        $this->command->info('📧 Admin: admin@kandura.com / password');
    }
}

