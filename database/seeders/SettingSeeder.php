<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
      public function run(): void
      {
            Setting::query()->updateOrCreate(
                  ['id' => 1],
                  [
                        // Genel
                        'site_name' => config('app.name', 'Site'),
                        'site_tagline' => 'Kısa ve net bir slogan',

                        // ✅ public/images/logo.svg ve public/images/favicon.svg
                        // DB’de public/ yazılmaz, disk root’a göre relative yazılır:
                        'logo_path' => 'images/logo.svg',
                        'favicon_path' => 'images/favicon.svg',
                        'og_image_path' => 'images/og-image.svg',


                        // SEO
                        'seo_title' => config('app.name', 'Site'),
                        'seo_description' => 'Site açıklaması',
                        'seo_keywords' => 'süt, lojistik, çiftlik',

                        // Index / Robots
                        'search_engine_indexing' => true,
                        'seo_noindex' => false,
                        'seo_nofollow' => false,
                        'robots_txt_enabled' => true,
                        'robots_txt_custom' => false,
                        'robots_txt_content' => null,
                        'sitemap_url' => url('/sitemap.xml'),

                        // İletişim / Sosyal
                        'contact_email' => 'info@example.com',
                        'contact_phone' => '+90 555 000 00 00',
                        'contact_address' => 'Türkiye',
                        'social_links' => [
                              ['platform' => 'instagram', 'url' => 'https://instagram.com/yourpage'],
                              ['platform' => 'x', 'url' => 'https://x.com/yourpage'],
                        ],

                        // Scriptler
                        'header_scripts' => null,
                        'footer_scripts' => null,

                        // Bakım
                        'maintenance_mode' => false,
                        'maintenance_message' => null,
                  ]
            );
      }
}
