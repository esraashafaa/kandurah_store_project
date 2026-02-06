<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء User عادي (web guard - سيستخدم API عبر Sanctum)
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'phone' => '0500000003',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. إنشاء مستخدمين إضافيين للاختبار
        $testUsers = [
            [
                'name' => 'Test User 1',
                'email' => 'testuser1@example.com',
                'phone' => '0500000006',
            ],
            [
                'name' => 'Test User 2',
                'email' => 'testuser2@example.com',
                'phone' => '0500000007',
            ],
        ];

        foreach ($testUsers as $testUserData) {
            User::firstOrCreate(
                ['email' => $testUserData['email']],
                [
                    'name' => $testUserData['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $testUserData['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('تم إنشاء المستخدمين بنجاح!');
        $this->command->info('User: user@example.com / password123');
    }
}
