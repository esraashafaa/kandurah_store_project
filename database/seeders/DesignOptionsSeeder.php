<?php

namespace Database\Seeders;

use App\Enums\DesignOptionTypeEnum;
use App\Models\DesignOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignOptionsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * خيارات التصميم الافتراضية
     */
    private array $options = [
        // الألوان
        [
            'name' => ['en' => 'Black', 'ar' => 'أسود'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],
        [
            'name' => ['en' => 'White', 'ar' => 'أبيض'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],
        [
            'name' => ['en' => 'Navy Blue', 'ar' => 'أزرق داكن'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],
        [
            'name' => ['en' => 'Burgundy', 'ar' => 'عنابي'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],
        [
            'name' => ['en' => 'Beige', 'ar' => 'بيج'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],
        [
            'name' => ['en' => 'Grey', 'ar' => 'رمادي'],
            'type' => DesignOptionTypeEnum::COLOR,
        ],

        // أنواع القبة
        [
            'name' => ['en' => 'Round Dome', 'ar' => 'قبة دائرية'],
            'type' => DesignOptionTypeEnum::DOME_TYPE,
        ],
        [
            'name' => ['en' => 'V-Shaped Dome', 'ar' => 'قبة على شكل V'],
            'type' => DesignOptionTypeEnum::DOME_TYPE,
        ],
        [
            'name' => ['en' => 'Square Dome', 'ar' => 'قبة مربعة'],
            'type' => DesignOptionTypeEnum::DOME_TYPE,
        ],
        [
            'name' => ['en' => 'Pointed Dome', 'ar' => 'قبة مدببة'],
            'type' => DesignOptionTypeEnum::DOME_TYPE,
        ],

        // أنواع القماش
        [
            'name' => ['en' => 'Crepe', 'ar' => 'كريب'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],
        [
            'name' => ['en' => 'Chiffon', 'ar' => 'شيفون'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],
        [
            'name' => ['en' => 'Silk', 'ar' => 'حرير'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],
        [
            'name' => ['en' => 'Cotton', 'ar' => 'قطن'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],
        [
            'name' => ['en' => 'Linen', 'ar' => 'كتان'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],
        [
            'name' => ['en' => 'Velvet', 'ar' => 'مخمل'],
            'type' => DesignOptionTypeEnum::FABRIC_TYPE,
        ],

        // أنواع الأكمام
        [
            'name' => ['en' => 'Long Sleeves', 'ar' => 'أكمام طويلة'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
        [
            'name' => ['en' => 'Short Sleeves', 'ar' => 'أكمام قصيرة'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
        [
            'name' => ['en' => 'Bell Sleeves', 'ar' => 'أكمام جرس'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
        [
            'name' => ['en' => 'Cape Sleeves', 'ar' => 'أكمام كاب'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
        [
            'name' => ['en' => 'Butterfly Sleeves', 'ar' => 'أكمام فراشة'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
        [
            'name' => ['en' => 'Sleeveless', 'ar' => 'بدون أكمام'],
            'type' => DesignOptionTypeEnum::SLEEVE_TYPE,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->options as $option) {
            DesignOption::updateOrCreate(
                [
                    'type' => $option['type'],
                    // البحث باستخدام الاسم الإنجليزي للتعريف الفريد
                ],
                [
                    'name' => $option['name'],
                    'type' => $option['type'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✓ Design options seeded successfully');
        $this->command->info('  - Colors: 6');
        $this->command->info('  - Dome types: 4');
        $this->command->info('  - Fabric types: 6');
        $this->command->info('  - Sleeve types: 6');
    }
}

