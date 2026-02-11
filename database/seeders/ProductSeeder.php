<?php

namespace Database\Seeders;

use App\Enums\CurrencyCode;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id')->values();

        // Kategori yoksa ürün seed etmeyelim
        if ($categoryIds->isEmpty()) {
            return;
        }

        $products = [
            [
                'name' => 'Kablosuz Kulaklık',
                'description' => 'Günlük kullanım için pratik, uzun pil ömürlü kablosuz kulaklık.',
                'external_url' => 'https://example.com/urun/kablosuz-kulaklik',
                'price' => 1499.90,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(3),
                'availability_ends_at' => now()->addDays(30),
                'availability_forever' => false,
                'rating_stars' => 5,
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Akıllı Saat',
                'description' => 'Bildirim, sağlık takibi ve spor modları ile günlük kullanım için ideal.',
                'external_url' => 'https://example.com/urun/akilli-saat',
                'price' => 2199.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(7),
                'availability_ends_at' => null,
                'availability_forever' => true,
                'rating_stars' => 4,
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Taşınabilir Şarj Cihazı',
                'description' => 'Hızlı şarj destekli taşınabilir powerbank.',
                'external_url' => 'https://example.com/urun/powerbank',
                'price' => 899.50,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(1),
                'availability_ends_at' => now()->addDays(60),
                'availability_forever' => false,
                'rating_stars' => 4,
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Bluetooth Hoparlör',
                'description' => 'Güçlü ses çıkışı ve taşınabilir tasarım.',
                'external_url' => 'https://example.com/urun/bluetooth-hoparlor',
                'price' => 1299.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => null,
                'availability_ends_at' => null,
                'availability_forever' => true,
                'rating_stars' => 3,
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Kablo Seti',
                'description' => 'Farklı uçlarla uyumlu dayanıklı kablo seti.',
                'external_url' => 'https://example.com/urun/kablo-seti',
                'price' => 199.90,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now(),
                'availability_ends_at' => null,
                'availability_forever' => true,
                'rating_stars' => 4,
                'sort_order' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Spor Ayakkabı',
                'description' => 'Hafif, konforlu ve günlük kullanım için uygun.',
                'external_url' => 'https://example.com/urun/spor-ayakkabi',
                'price' => 1899.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(2),
                'availability_ends_at' => now()->addDays(15),
                'availability_forever' => false,
                'rating_stars' => 5,
                'sort_order' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Basic Tişört',
                'description' => 'Rahat kalıp ve günlük kombinler için uygun basic tişört.',
                'external_url' => 'https://example.com/urun/basic-tisort',
                'price' => 249.90,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => null,
                'availability_ends_at' => null,
                'availability_forever' => true,
                'rating_stars' => 4,
                'sort_order' => 70,
                'is_active' => true,
            ],
            [
                'name' => 'Kışlık Mont',
                'description' => 'Soğuk hava koşulları için sıcak tutan kışlık mont.',
                'external_url' => 'https://example.com/urun/kislik-mont',
                'price' => 2999.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(10),
                'availability_ends_at' => now()->addDays(90),
                'availability_forever' => false,
                'rating_stars' => 5,
                'sort_order' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Kurulum Hizmeti',
                'description' => 'Yerinde kurulum ve temel ayarlar hizmeti.',
                'external_url' => 'https://example.com/hizmet/kurulum',
                'price' => 499.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => null,
                'availability_ends_at' => null,
                'availability_forever' => true,
                'rating_stars' => 3,
                'sort_order' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Bakım Paketi',
                'description' => 'Periyodik bakım ve kontrol hizmet paketi.',
                'external_url' => 'https://example.com/hizmet/bakim-paketi',
                'price' => 799.00,
                'currency' => CurrencyCode::TRY ->value,
                'availability_starts_at' => now()->subDays(5),
                'availability_ends_at' => now()->addDays(365),
                'availability_forever' => false,
                'rating_stars' => 4,
                'sort_order' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($products as $i => $row) {
            $categoryId = $categoryIds[$i % $categoryIds->count()];

            Product::firstOrCreate(
                // Seed tekrar çalışınca duplicate oluşmasın
                ['name' => $row['name']],
                [
                    'category_id' => $categoryId,

                    'description' => $row['description'] ?? null,

                    'image' => null,
                    'video' => null,

                    'external_url' => $row['external_url'] ?? null,

                    'availability_starts_at' => $row['availability_starts_at'] ?? null,
                    'availability_ends_at' => $row['availability_ends_at'] ?? null,
                    'availability_forever' => (bool) ($row['availability_forever'] ?? true),

                    'rating_stars' => (int) ($row['rating_stars'] ?? 0),

                    'sort_order' => (int) ($row['sort_order'] ?? 0),

                    'price' => $row['price'] ?? 0,
                    'currency' => $row['currency'] ?? CurrencyCode::TRY ->value,

                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]
            );
        }
    }
}
