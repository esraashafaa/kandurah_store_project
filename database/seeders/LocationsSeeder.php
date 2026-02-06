<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('⚠️  لا يوجد مستخدمين! يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        $cities = ['الرياض', 'جدة', 'الدمام', 'المدينة المنورة', 'الخبر', 'الطائف', 'بريدة', 'خميس مشيط'];
        $areas = [
            'حي النرجس', 'حي العليا', 'حي الملز', 'حي العريجاء', 
            'حي النهضة', 'حي السليمانية', 'حي المطار', 'حي العليا'
        ];
        $streets = [
            'شارع الملك فهد', 'شارع العليا', 'شارع التحلية', 'شارع الأمير سلطان',
            'شارع العروبة', 'شارع الجامعة', 'شارع الخليج', 'شارع الكورنيش'
        ];

        foreach ($users as $user) {
            // إنشاء موقع افتراضي واحد لكل مستخدم
            if (!Location::where('user_id', $user->id)->where('is_default', true)->exists()) {
                Location::create([
                    'user_id' => $user->id,
                    'city' => $cities[array_rand($cities)],
                    'area' => $areas[array_rand($areas)],
                    'street' => $streets[array_rand($streets)],
                    'house_number' => rand(100, 999),
                    'lat' => 24.7136 + (rand(-100, 100) / 1000),
                    'lng' => 46.6753 + (rand(-100, 100) / 1000),
                    'is_default' => true,
                ]);
            }

            // إنشاء موقع إضافي لبعض المستخدمين (30% منهم)
            if (rand(1, 100) <= 30 && Location::where('user_id', $user->id)->count() < 2) {
                Location::create([
                    'user_id' => $user->id,
                    'city' => $cities[array_rand($cities)],
                    'area' => $areas[array_rand($areas)],
                    'street' => $streets[array_rand($streets)],
                    'house_number' => rand(100, 999),
                    'lat' => 24.7136 + (rand(-100, 100) / 1000),
                    'lng' => 46.6753 + (rand(-100, 100) / 1000),
                    'is_default' => false,
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء المواقع بنجاح!');
        $this->command->info('   - إجمالي المواقع: ' . Location::count());
    }
}
