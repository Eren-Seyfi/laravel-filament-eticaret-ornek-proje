<?php

namespace Database\Seeders;

use App\Enums\AdvertPlacement;
use App\Models\Advert;
use Illuminate\Database\Seeder;

class AdvertSeeder extends Seeder
{
      public function run(): void
      {
            $adverts = [
                  [
                        'title' => 'Hoş Geldiniz Duyurusu',
                        'content' => 'Yeni ürünlerimizi keşfetmeyi unutmayın.',
                        'external_url' => 'https://example.com/kampanya/hos-geldiniz',
                        'placement' => AdvertPlacement::Popup->value,
                        'priority' => 100,
                        'starts_at' => now()->subDays(1),
                        'ends_at' => now()->addDays(7),
                        'is_forever' => false,
                        'is_active' => true,
                  ],
                  [
                        'title' => 'Sağ Banner - Kampanya',
                        'content' => 'Bu hafta sonu tüm ürünlerde %20 indirim!',
                        'external_url' => 'https://example.com/kampanya/hafta-sonu',
                        'placement' => AdvertPlacement::Right->value,
                        'priority' => 80,
                        'starts_at' => null,
                        'ends_at' => null,
                        'is_forever' => true,
                        'is_active' => true,
                  ],
                  [
                        'title' => 'Sol Banner - Ücretsiz Kargo',
                        'content' => '1500 TL ve üzeri alışverişlerde ücretsiz kargo.',
                        'external_url' => 'https://example.com/kargo',
                        'placement' => AdvertPlacement::Left->value,
                        'priority' => 70,
                        'starts_at' => now()->subDays(3),
                        'ends_at' => null,
                        'is_forever' => true,
                        'is_active' => true,
                  ],
                  [
                        'title' => 'Üst Bar - Yeni Gelenler',
                        'content' => 'Yeni gelen ürünleri hemen inceleyin.',
                        'external_url' => 'https://example.com/yeni-gelenler',
                        'placement' => AdvertPlacement::Top->value,
                        'priority' => 60,
                        'starts_at' => now()->subDays(2),
                        'ends_at' => now()->addDays(30),
                        'is_forever' => false,
                        'is_active' => true,
                  ],
                  [
                        'title' => 'Alt Bar - Mobil Uygulama',
                        'content' => 'Mobil uygulamamızı indirerek ekstra fırsatları yakalayın.',
                        'external_url' => 'https://example.com/app',
                        'placement' => AdvertPlacement::Bottom->value,
                        'priority' => 50,
                        'starts_at' => null,
                        'ends_at' => null,
                        'is_forever' => true,
                        'is_active' => true,
                  ],
                  [
                        'title' => 'Ön Alan - Vitrin Kampanyası',
                        'content' => 'Sezon fırsatlarını kaçırmayın! Vitrin kampanyaları burada.',
                        'external_url' => 'https://example.com/vitrin',
                        'placement' => AdvertPlacement::Front->value,
                        'priority' => 90,
                        'starts_at' => now()->subDays(5),
                        'ends_at' => now()->addDays(10),
                        'is_forever' => false,
                        'is_active' => true,
                  ],
            ];

            foreach ($adverts as $row) {
                  Advert::firstOrCreate(
                        // Seed tekrar çalışınca aynı başlık + konum için duplicate olmasın
                        ['title' => $row['title'], 'placement' => $row['placement']],
                        [
                              'content' => $row['content'] ?? null,
                              'image' => null,
                              'video' => null,
                              'external_url' => $row['external_url'] ?? null,

                              'priority' => (int) ($row['priority'] ?? 0),

                              'starts_at' => $row['starts_at'] ?? null,
                              'ends_at' => $row['ends_at'] ?? null,
                              'is_forever' => (bool) ($row['is_forever'] ?? true),

                              'is_active' => (bool) ($row['is_active'] ?? true),
                        ]
                  );
            }
      }
}
