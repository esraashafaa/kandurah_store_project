<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignsSeeder extends Seeder
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

        $designOptions = DesignOption::all();
        $sizes = Size::all();
        
        if ($designOptions->isEmpty() || $sizes->isEmpty()) {
            $this->command->warn('⚠️  لا توجد خيارات تصميم أو مقاسات! يرجى تشغيل DesignOptionsSeeder و SizesSeeder أولاً.');
            return;
        }

        // أسماء التصاميم التجريبية
        $designNames = [
            ['en' => 'Classic White Kandura', 'ar' => 'كندورة بيضاء كلاسيكية'],
            ['en' => 'Elegant Navy Blue', 'ar' => 'أزرق داكن أنيق'],
            ['en' => 'Luxury Burgundy', 'ar' => 'عنابي فاخر'],
            ['en' => 'Modern Beige', 'ar' => 'بيج عصري'],
            ['en' => 'Traditional Grey', 'ar' => 'رمادي تقليدي'],
            ['en' => 'Premium Black', 'ar' => 'أسود مميز'],
            ['en' => 'Royal Blue', 'ar' => 'أزرق ملكي'],
            ['en' => 'Sophisticated Brown', 'ar' => 'بني راقي'],
            ['en' => 'Chic Cream', 'ar' => 'كريمي أنيق'],
            ['en' => 'Timeless Charcoal', 'ar' => 'فحمي خالد'],
        ];

        $descriptions = [
            ['en' => 'A classic and elegant kandura design perfect for formal occasions.', 'ar' => 'تصميم كندورة كلاسيكي وأنيق مثالي للمناسبات الرسمية.'],
            ['en' => 'Modern design with traditional touches, suitable for all occasions.', 'ar' => 'تصميم عصري بلمسات تقليدية، مناسب لجميع المناسبات.'],
            ['en' => 'Luxury kandura with premium fabric and exquisite details.', 'ar' => 'كندورة فاخرة بقماش مميز وتفاصيل راقية.'],
            ['en' => 'Comfortable and stylish design for everyday wear.', 'ar' => 'تصميم مريح وأنيق للارتداء اليومي.'],
            ['en' => 'Traditional design with modern comfort features.', 'ar' => 'تصميم تقليدي بميزات راحة عصرية.'],
        ];

        $createdDesigns = 0;

        // إنشاء 2-4 تصاميم لكل مستخدم
        foreach ($users as $user) {
            $designsCount = rand(2, 4);
            
            for ($i = 0; $i < $designsCount; $i++) {
                $name = $designNames[array_rand($designNames)];
                $description = $descriptions[array_rand($descriptions)];
                $price = rand(50, 500);

                $design = Design::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'is_active' => rand(0, 10) > 1, // 90% نشطة
                ]);

                // ربط التصميم بخيارات التصميم (2-4 خيارات عشوائية)
                $optionsCount = min(rand(2, 4), $designOptions->count());
                $selectedOptions = $designOptions->shuffle()->take($optionsCount);
                $design->designOptions()->attach($selectedOptions->pluck('id'));

                // ربط التصميم بالمقاسات (3-6 مقاسات)
                $sizesCount = min(rand(3, 6), $sizes->count());
                $selectedSizes = $sizes->shuffle()->take($sizesCount);
                $design->sizes()->attach($selectedSizes->pluck('id'));

                $createdDesigns++;
            }
        }

        $this->command->info('✅ تم إنشاء التصاميم بنجاح!');
        $this->command->info("   - إجمالي التصاميم: {$createdDesigns}");
        $this->command->info('   - التصاميم النشطة: ' . Design::where('is_active', true)->count());
    }
}
