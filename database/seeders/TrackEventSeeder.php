<?php

namespace Database\Seeders;

use App\Enums\TrackEventType;
use App\Models\Advert;
use App\Models\Category;
use App\Models\Product;
use App\Models\TrackEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrackEventSeeder extends Seeder
{
      public function run(): void
      {
            $now = now();

            $daysToSeed = 30;

            $products = Product::query()->pluck('id')->all();
            $adverts = Advert::query()->pluck('id')->all();
            $categories = Category::query()->pluck('id')->all();

            for ($dayOffset = 0; $dayOffset < $daysToSeed; $dayOffset++) {
                  $dayStart = $now->copy()->subDays($dayOffset)->startOfDay();

                  // -----------------------------
                  // 1) HOME page_view
                  // -----------------------------
                  $minimumDailyHomeVisitors = $dayOffset === 0 ? 80 : 20;
                  $maximumDailyHomeVisitors = $dayOffset === 0 ? 200 : 120;
                  $dailyHomeVisitorsCount = rand($minimumDailyHomeVisitors, $maximumDailyHomeVisitors);

                  for ($visitorIndex = 0; $visitorIndex < $dailyHomeVisitorsCount; $visitorIndex++) {
                        $createdAt = $dayStart->copy()->addMinutes(rand(0, 23 * 60 + 59));
                        $durationInMilliseconds = rand(5_000, 360_000);

                        $this->createEvent(
                              createdAt: $createdAt,
                              event: TrackEventType::PageView,
                              pageKey: 'home',
                              trackableType: null,
                              trackableId: null,
                              url: 'http://localhost:8000/',
                              referrer: null,
                              durationInMilliseconds: $durationInMilliseconds
                        );
                  }

                  // -----------------------------
                  // 2) CATEGORY traffic (view + click + page_view)
                  // -----------------------------
                  if (!empty($categories)) {
                        $minimumDailyCategoryVisitors = $dayOffset === 0 ? 30 : 10;
                        $maximumDailyCategoryVisitors = $dayOffset === 0 ? 120 : 60;
                        $dailyCategoryVisitorsCount = rand($minimumDailyCategoryVisitors, $maximumDailyCategoryVisitors);

                        for ($i = 0; $i < $dailyCategoryVisitorsCount; $i++) {
                              $categoryId = $categories[array_rand($categories)];
                              $createdAt = $dayStart->copy()->addMinutes(rand(0, 23 * 60 + 59));

                              // category view (trackable = Category)
                              $this->createEvent(
                                    createdAt: $createdAt,
                                    event: TrackEventType::View,
                                    pageKey: 'category_detail',
                                    trackableType: Category::class,
                                    trackableId: $categoryId,
                                    url: "http://localhost:8000/categories/{$categoryId}",
                                    referrer: 'http://localhost:8000/',
                                    durationInMilliseconds: 0
                              );

                              // category page_view (kalma süresi gibi düşünebilirsin)
                              if (rand(1, 100) <= 70) {
                                    $durationInMilliseconds = rand(3_000, 240_000);
                                    $this->createEvent(
                                          createdAt: $createdAt->copy()->addSeconds(rand(0, 20)),
                                          event: TrackEventType::PageView,
                                          pageKey: 'category_detail',
                                          trackableType: Category::class,
                                          trackableId: $categoryId,
                                          url: "http://localhost:8000/categories/{$categoryId}",
                                          referrer: 'http://localhost:8000/',
                                          durationInMilliseconds: $durationInMilliseconds
                                    );
                              }

                              // category click (örnek: kategoriden bir yere tıklama)
                              if (rand(1, 100) <= 25) {
                                    $this->createEvent(
                                          createdAt: $createdAt->copy()->addSeconds(rand(0, 40)),
                                          event: TrackEventType::Click,
                                          pageKey: 'category_click',
                                          trackableType: Category::class,
                                          trackableId: $categoryId,
                                          url: "http://localhost:8000/categories/{$categoryId}",
                                          referrer: "http://localhost:8000/categories/{$categoryId}",
                                          durationInMilliseconds: 0
                                    );
                              }
                        }
                  }

                  // -----------------------------
                  // 3) PRODUCT traffic (view + click + page_view)
                  // -----------------------------
                  if (!empty($products)) {
                        $minimumDailyProductVisitors = $dayOffset === 0 ? 40 : 10;
                        $maximumDailyProductVisitors = $dayOffset === 0 ? 160 : 80;
                        $dailyProductVisitorsCount = rand($minimumDailyProductVisitors, $maximumDailyProductVisitors);

                        for ($i = 0; $i < $dailyProductVisitorsCount; $i++) {
                              $productId = $products[array_rand($products)];
                              $createdAt = $dayStart->copy()->addMinutes(rand(0, 23 * 60 + 59));

                              // product view
                              $this->createEvent(
                                    createdAt: $createdAt,
                                    event: TrackEventType::View,
                                    pageKey: 'product_detail',
                                    trackableType: Product::class,
                                    trackableId: $productId,
                                    url: "http://localhost:8000/products/{$productId}",
                                    referrer: 'http://localhost:8000/',
                                    durationInMilliseconds: 0
                              );

                              // product page_view (kalma süresi)
                              if (rand(1, 100) <= 75) {
                                    $durationInMilliseconds = rand(4_000, 300_000);
                                    $this->createEvent(
                                          createdAt: $createdAt->copy()->addSeconds(rand(0, 15)),
                                          event: TrackEventType::PageView,
                                          pageKey: 'product_detail',
                                          trackableType: Product::class,
                                          trackableId: $productId,
                                          url: "http://localhost:8000/products/{$productId}",
                                          referrer: 'http://localhost:8000/',
                                          durationInMilliseconds: $durationInMilliseconds
                                    );
                              }

                              // product click (dış link vs.)
                              if (rand(1, 100) <= 35) {
                                    $this->createEvent(
                                          createdAt: $createdAt->copy()->addSeconds(rand(0, 35)),
                                          event: TrackEventType::Click,
                                          pageKey: 'product_click',
                                          trackableType: Product::class,
                                          trackableId: $productId,
                                          url: "http://localhost:8000/products/{$productId}",
                                          referrer: "http://localhost:8000/products/{$productId}",
                                          durationInMilliseconds: 0
                                    );
                              }
                        }
                  }

                  // -----------------------------
                  // 4) ADVERT traffic (view + click)
                  // -----------------------------
                  if (!empty($adverts)) {
                        $minimumDailyAdvertVisitors = $dayOffset === 0 ? 20 : 5;
                        $maximumDailyAdvertVisitors = $dayOffset === 0 ? 120 : 50;
                        $dailyAdvertVisitorsCount = rand($minimumDailyAdvertVisitors, $maximumDailyAdvertVisitors);

                        for ($i = 0; $i < $dailyAdvertVisitorsCount; $i++) {
                              $advertId = $adverts[array_rand($adverts)];
                              $createdAt = $dayStart->copy()->addMinutes(rand(0, 23 * 60 + 59));

                              // advert view
                              $this->createEvent(
                                    createdAt: $createdAt,
                                    event: TrackEventType::View,
                                    pageKey: 'advert_detail',
                                    trackableType: Advert::class,
                                    trackableId: $advertId,
                                    url: "http://localhost:8000/adverts/{$advertId}",
                                    referrer: 'http://localhost:8000/',
                                    durationInMilliseconds: 0
                              );

                              // advert click (kampanya linki gibi)
                              if (rand(1, 100) <= 30) {
                                    $this->createEvent(
                                          createdAt: $createdAt->copy()->addSeconds(rand(0, 45)),
                                          event: TrackEventType::Click,
                                          pageKey: 'advert_click',
                                          trackableType: Advert::class,
                                          trackableId: $advertId,
                                          url: "http://localhost:8000/adverts/{$advertId}",
                                          referrer: "http://localhost:8000/adverts/{$advertId}",
                                          durationInMilliseconds: 0
                                    );
                              }
                        }
                  }
            }

            // Ek olarak "şimdi" için home page_view de üretelim (unique'e takılmasın diye session benzersiz)
            TrackEvent::create([
                  'event' => TrackEventType::PageView->value,
                  'event_id' => (string) Str::ulid(),
                  'page_key' => 'home',

                  'trackable_type' => null,
                  'trackable_id' => null,

                  'session_id' => 'seed-home-now-' . Str::ulid(),
                  'ip' => '127.0.0.1',
                  'user_agent' => 'Seeder/Local',
                  'user_id' => null,

                  'referrer' => null,
                  'url' => 'http://localhost:8000/',

                  'started_at' => $now->copy()->subMinutes(5),
                  'ended_at' => $now->copy()->subMinutes(2),
                  'duration_ms' => 180000,
            ]);
      }

      private function createEvent(
            \Carbon\Carbon $createdAt,
            TrackEventType $event,
            ?string $pageKey,
            ?string $trackableType,
            ?int $trackableId,
            string $url,
            ?string $referrer,
            int $durationInMilliseconds
      ): void {
            $startedAt = null;
            $endedAt = null;

            if ($event === TrackEventType::PageView) {
                  $startedAt = $createdAt->copy();
                  $endedAt = $createdAt->copy()->addMilliseconds($durationInMilliseconds);
            }

            $trackEvent = new TrackEvent([
                  'event' => $event->value,
                  'event_id' => (string) Str::ulid(),
                  'page_key' => $pageKey,

                  'trackable_type' => $trackableType,
                  'trackable_id' => $trackableId,

                  // ✅ her kayıt farklı session (unique kuralına takılma)
                  'session_id' => 'seed-session-' . Str::ulid(),

                  'ip' => '127.0.0.1',
                  'user_agent' => 'Seeder/Local',
                  'user_id' => null,

                  'referrer' => $referrer,
                  'url' => $url,

                  'started_at' => $startedAt,
                  'ended_at' => $endedAt,
                  'duration_ms' => $durationInMilliseconds,
            ]);

            $trackEvent->forceFill([
                  'created_at' => $createdAt,
                  'updated_at' => $createdAt,
            ]);

            $trackEvent->save();
      }
}
