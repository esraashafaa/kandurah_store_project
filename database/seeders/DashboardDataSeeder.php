<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\Design;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;

class DashboardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 جاري إضافة البيانات التجريبية...');
        
        // إنشاء مستخدمين تجريبيين
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            // تحقق من وجود المستخدم
            $existingUser = User::where('email', "user$i@test.com")->first();
            if ($existingUser) {
                $users[] = $existingUser;
                continue;
            }
            
            $users[] = User::create([
                'name' => "مستخدم تجريبي $i",
                'email' => "user$i@test.com",
                'phone' => '05' . rand(1000000, 9999999),
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => rand(0, 1) == 1,
                'wallet_balance' => rand(0, 1000),
                'email_verified_at' => now(),
            ]);
        }

        // إنشاء مواقع تجريبية
        $locations = [];
        foreach ($users as $user) {
            // تحقق من وجود موقع افتراضي للمستخدم
            $existingLocation = Location::where('user_id', $user->id)->where('is_default', true)->first();
            if ($existingLocation) {
                $locations[] = $existingLocation;
                continue;
            }
            
            $locations[] = Location::create([
                'user_id' => $user->id,
                'city' => 'الرياض',
                'area' => 'حي النرجس',
                'street' => 'شارع الملك فهد',
                'house_number' => rand(100, 999),
                'lat' => 24.7136 + (rand(-100, 100) / 1000),
                'lng' => 46.6753 + (rand(-100, 100) / 1000),
                'is_default' => true,
            ]);
        }

        // إنشاء تصاميم تجريبية
        $designs = [];
        foreach ($users as $user) {
            for ($j = 0; $j < rand(1, 3); $j++) {
                $designs[] = Design::create([
                    'user_id' => $user->id,
                    'name' => "تصميم تجريبي $j",
                    'description' => "وصف التصميم التجريبي رقم $j",
                    'price' => rand(50, 500),
                    'is_active' => true,
                ]);
            }
        }

        // إنشاء طلبات تجريبية
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        // طلبات خلال الـ 6 أشهر الماضية
        for ($month = 0; $month < 6; $month++) {
            $ordersInMonth = rand(5, 15);
            
            for ($i = 0; $i < $ordersInMonth; $i++) {
                $user = $users[array_rand($users)];
                $location = $locations[array_rand($locations)];
                
                // تاريخ عشوائي في الشهر
                $date = now()->subMonths($month)->subDays(rand(0, 28));
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'total_amount' => rand(100, 2000),
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => 'طلب تجريبي',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }

        // طلبات اليوم
        for ($i = 0; $i < 5; $i++) {
            $user = $users[array_rand($users)];
            $location = $locations[array_rand($locations)];
            
            Order::create([
                'user_id' => $user->id,
                'location_id' => $location->id,
                'total_amount' => rand(100, 2000),
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'طلب تجريبي - اليوم',
                'created_at' => now()->subHours(rand(1, 23)),
                'updated_at' => now()->subHours(rand(1, 23)),
            ]);
        }

        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('📊 الإحصائيات:');
        $this->command->info('   - المستخدمين: ' . User::count());
        $this->command->info('   - الطلبات: ' . Order::count());
        $this->command->info('   - التصاميم: ' . Design::count());
        $this->command->info('   - المواقع: ' . Location::count());
    }
}

