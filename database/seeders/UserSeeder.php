<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء Super Admin (web guard)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'phone' => '0500000001',
                'role' => RoleEnum::SUPER_ADMIN->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // تعيين دور Super Admin من Spatie Permission
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', 'web')->first();
        if ($superAdminRole && !$superAdmin->hasRole($superAdminRole)) {
            $superAdmin->assignRole($superAdminRole);
        }

        // 2. إنشاء Admin (web guard)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'phone' => '0500000002',
                'role' => RoleEnum::ADMIN->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // تعيين دور Admin من Spatie Permission
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        if ($adminRole && !$admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        // 3. إنشاء User عادي (web guard - سيستخدم API عبر Sanctum)
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'phone' => '0500000003',
                'role' => RoleEnum::USER->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // المستخدمون العاديون لا يحتاجون roles من Spatie
        // يكفي الـ role enum في الـ User model

        // 4. إنشاء Guest (web guard)
        $guest = User::firstOrCreate(
            ['email' => 'guest@example.com'],
            [
                'name' => 'Guest User',
                'password' => Hash::make('password123'),
                'phone' => '0500000004',
                'role' => RoleEnum::GUEST->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // المستخدمون العاديون لا يحتاجون roles من Spatie
        // يكفي الـ role enum في الـ User model

        // 5. إنشاء مستخدمين إضافيين للاختبار
        $testUsers = [
            [
                'name' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'phone' => '0500000005',
                'role' => RoleEnum::ADMIN->value,
                'spatie_role' => 'admin',
            ],
            [
                'name' => 'Test User 1',
                'email' => 'testuser1@example.com',
                'phone' => '0500000006',
                'role' => RoleEnum::USER->value,
                'spatie_role' => null, // Users لا يحتاجون Spatie roles
            ],
            [
                'name' => 'Test User 2',
                'email' => 'testuser2@example.com',
                'phone' => '0500000007',
                'role' => RoleEnum::USER->value,
                'spatie_role' => null,
            ],
        ];

        foreach ($testUsers as $testUserData) {
            $testUser = User::firstOrCreate(
                ['email' => $testUserData['email']],
                [
                    'name' => $testUserData['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $testUserData['phone'],
                    'role' => $testUserData['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            
            // تعيين الدور من Spatie Permission (للـ Admins فقط)
            if ($testUserData['spatie_role']) {
                $role = Role::where('name', $testUserData['spatie_role'])
                    ->where('guard_name', 'web')
                    ->first();
                
                if ($role && !$testUser->hasRole($role)) {
                    $testUser->assignRole($role);
                }
            }
        }

        $this->command->info('تم إنشاء المستخدمين بنجاح!');
        $this->command->info('Super Admin: superadmin@example.com / password123');
        $this->command->info('Admin: admin@example.com / password123');
        $this->command->info('User: user@example.com / password123');
        $this->command->info('Guest: guest@example.com / password123');
    }
}
