<?php

namespace Database\Seeders;

use App\Models\User;
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
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@kandura.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'phone' => '0501234567',
                'role' => RoleEnum::SUPER_ADMIN->value,
                'is_active' => true,
                'email_verified_at' => now(),
                'wallet_balance' => 0.00,
            ]
        );

        // تعيين دور Super Admin من Spatie Permission
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdminRole && !$superAdmin->hasRole($superAdminRole)) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@kandura.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '0501234568',
                'role' => RoleEnum::ADMIN->value,
                'is_active' => true,
                'email_verified_at' => now(),
                'wallet_balance' => 0.00,
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

