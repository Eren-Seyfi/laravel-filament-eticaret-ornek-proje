<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'en güvendiklerimiz!', 'sort_order' => 1, 'show_on_homepage' => true],
            ['name' => 'Hoşgeldin Bonusları', 'sort_order' => 2, 'show_on_homepage' => true],
            ['name' => 'Tüm Sponsorlar', 'sort_order' => 3, 'show_on_homepage' => true],
            ['name' => 'Premium sponsorlar', 'sort_order' => 4, 'show_on_homepage' => true],
            ['name' => 'Ana sponsorlar', 'sort_order' => 5, 'show_on_homepage' => true],
        ];

        foreach ($categories as $row) {
            $slug = Str::slug($row['name']);

            Category::firstOrCreate(
                ['slug' => $slug],
                [

                    'name' => $row['name'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                    'show_on_homepage' => (bool) $row['show_on_homepage'],
                    
                ]
            );
        }
    }
}
