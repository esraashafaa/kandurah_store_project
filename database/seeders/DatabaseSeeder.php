<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. إنشاء الأدوار والصلاحيات
            RolesAndPermissionsSeeder::class,
            
            // 2. إنشاء المستخدمين
            UserSeeder::class,
            
            // 3. إنشاء المقاسات الثابتة (XS, S, M, L, XL, XXL)
            SizesSeeder::class,
            
            // 4. إنشاء خيارات التصميم الافتراضية
            DesignOptionsSeeder::class,
        ]);
    }
}
