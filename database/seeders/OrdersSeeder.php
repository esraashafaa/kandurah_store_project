<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Design;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $designs = Design::where('is_active', true)->get();
        $sizes = Size::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('⚠️  لا يوجد مستخدمين! يرجى تشغيل UserSeeder أولاً.');
            return;
        }

        if ($designs->isEmpty()) {
            $this->command->warn('⚠️  لا توجد تصاميم! يرجى تشغيل DesignsSeeder أولاً.');
            return;
        }

        if ($sizes->isEmpty()) {
            $this->command->warn('⚠️  لا توجد مقاسات! يرجى تشغيل SizesSeeder أولاً.');
            return;
        }

        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::PAID,
            OrderStatus::CONFIRMED,
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED,
        ];

        $createdOrders = 0;

        // إنشاء طلبات خلال الـ 6 أشهر الماضية
        for ($month = 0; $month < 6; $month++) {
            $ordersInMonth = rand(8, 20);
            
            for ($i = 0; $i < $ordersInMonth; $i++) {
                $user = $users->shuffle()->first();
                $userLocations = Location::where('user_id', $user->id)->get();
                
                if ($userLocations->isEmpty()) {
                    continue; // تخطي إذا لم يكن للمستخدم مواقع
                }

                $location = $userLocations->shuffle()->first();
                
                // تاريخ عشوائي في الشهر
                $date = now()->subMonths($month)->subDays(rand(0, 28))->subHours(rand(0, 23));
                
                // اختيار 1-3 تصاميم للطلب
                $designsCount = min(rand(1, 3), $designs->count());
                $selectedDesigns = $designs->shuffle()->take($designsCount);
                
                $subtotal = 0;
                $orderItems = [];

                foreach ($selectedDesigns as $design) {
                    // اختيار مقاس متاح للتصميم
                    $designSizes = $design->sizes;
                    if ($designSizes->isEmpty()) {
                        continue;
                    }
                    
                    $size = $designSizes->shuffle()->first();
                    $quantity = rand(1, 3);
                    $price = $design->price;
                    $itemSubtotal = $price * $quantity;
                    $subtotal += $itemSubtotal;

                    // اختيار خيارات التصميم
                    $designOptions = $design->designOptions;
                    $optionsCount = min(rand(1, 3), $designOptions->count());
                    $selectedOptions = $designOptions->shuffle()->take($optionsCount);
                    
                    $orderItems[] = [
                        'design_id' => $design->id,
                        'size_id' => $size->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'selected_options' => $selectedOptions->pluck('id')->toArray(),
                    ];
                }

                if (empty($orderItems)) {
                    continue; // تخطي إذا لم يكن هناك عناصر
                }

                // حساب الخصم (10% من الطلبات لديها كوبون)
                $discountAmount = 0;
                if (rand(1, 100) <= 10) {
                    $discountAmount = $subtotal * (rand(5, 20) / 100);
                }

                $totalAmount = $subtotal - $discountAmount;
                $status = $statuses[array_rand($statuses)] ?? OrderStatus::PENDING;

                $order = Order::create([
                    'user_id' => $user->id,
                    'location_id' => $location->id,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'notes' => rand(1, 100) <= 30 ? 'ملاحظات خاصة للطلب' : null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // إنشاء عناصر الطلب
                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'design_id' => $item['design_id'],
                        'size_id' => $item['size_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'selected_options' => $item['selected_options'],
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }

                $createdOrders++;
            }
        }

        // إنشاء طلبات اليوم (5-10 طلبات)
        $todayOrders = rand(5, 10);
        for ($i = 0; $i < $todayOrders; $i++) {
            $user = $users->shuffle()->first();
            $userLocations = Location::where('user_id', $user->id)->get();
            
            if ($userLocations->isEmpty()) {
                continue;
            }

            $location = $userLocations->shuffle()->first();
            $designsCount = min(rand(1, 2), $designs->count());
            $selectedDesigns = $designs->shuffle()->take($designsCount);
            
            $subtotal = 0;
            $orderItems = [];

            foreach ($selectedDesigns as $design) {
                $designSizes = $design->sizes;
                if ($designSizes->isEmpty()) {
                    continue;
                }
                
                $size = $designSizes->shuffle()->first();
                $quantity = rand(1, 2);
                $price = $design->price;
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                $designOptions = $design->designOptions;
                $optionsCount = min(rand(1, 2), $designOptions->count());
                $selectedOptions = $designOptions->shuffle()->take($optionsCount);
                
                $orderItems[] = [
                    'design_id' => $design->id,
                    'size_id' => $size->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'selected_options' => $selectedOptions->pluck('id')->toArray(),
                ];
            }

            if (empty($orderItems)) {
                continue;
            }

            $totalAmount = $subtotal;
            $todayStatuses = [OrderStatus::PENDING, OrderStatus::PAID, OrderStatus::CONFIRMED];
            $status = $todayStatuses[array_rand($todayStatuses)];
            $date = now()->subHours(rand(1, 23));

            $order = Order::create([
                'user_id' => $user->id,
                'location_id' => $location->id,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'status' => $status,
                'notes' => null,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'design_id' => $item['design_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'selected_options' => $item['selected_options'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            $createdOrders++;
        }

        $this->command->info('✅ تم إنشاء الطلبات بنجاح!');
        $this->command->info("   - إجمالي الطلبات: {$createdOrders}");
        $this->command->info('   - عناصر الطلبات: ' . OrderItem::count());
        
        // إحصائيات حسب الحالة
        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->count();
            if ($count > 0) {
                $this->command->info("   - {$status->label()}: {$count}");
            }
        }
    }
}
