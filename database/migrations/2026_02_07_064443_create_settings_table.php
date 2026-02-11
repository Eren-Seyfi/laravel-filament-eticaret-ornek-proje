<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Genel
            $table->string('site_name')->default('Site');
            $table->string('site_tagline')->nullable();

            // Medya
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('og_image_path')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();

            // ✅ Index / Robots
            // Arama motorlarına "indexleme" izni
            $table->boolean('search_engine_indexing')->default(true);
            // Sayfalara noindex/nofollow basılsın mı (indexing false iken genelde true yapılır)
            $table->boolean('seo_noindex')->default(false);
            $table->boolean('seo_nofollow')->default(false);

            // robots.txt yönetimi
            $table->boolean('robots_txt_enabled')->default(true);
            // Custom robots.txt yazmak istersen
            $table->boolean('robots_txt_custom')->default(false);
            $table->longText('robots_txt_content')->nullable();
            // robots.txt içine sitemap eklemek için
            $table->string('sitemap_url')->nullable();

            // İletişim / Sosyal
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_address')->nullable();
            $table->json('social_links')->nullable();

            // Script alanları
            $table->text('header_scripts')->nullable();
            $table->text('footer_scripts')->nullable();

            // Site durumu
            $table->boolean('maintenance_mode')->default(false);
            $table->longText('maintenance_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
