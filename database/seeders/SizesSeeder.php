<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * الأحجام الثابتة التي لا يمكن تعديلها
     * Fixed sizes as per requirements: XS, S, M, L, XL, XXL
     */
    private array $sizes = [
        ['code' => 'XS',  'name' => 'Extra Small', 'sort_order' => 1],
        ['code' => 'S',   'name' => 'Small',       'sort_order' => 2],
        ['code' => 'M',   'name' => 'Medium',      'sort_order' => 3],
        ['code' => 'L',   'name' => 'Large',       'sort_order' => 4],
        ['code' => 'XL',  'name' => 'Extra Large', 'sort_order' => 5],
        ['code' => 'XXL', 'name' => 'Double XL',   'sort_order' => 6],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->sizes as $size) {
            Size::updateOrCreate(
                ['code' => $size['code']], // البحث بالكود
                [
                    'name' => $size['name'],
                    'sort_order' => $size['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✓ Sizes seeded successfully: XS, S, M, L, XL, XXL');
    }
}

