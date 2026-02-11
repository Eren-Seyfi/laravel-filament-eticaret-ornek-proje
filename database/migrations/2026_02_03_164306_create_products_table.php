<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Kategori ilişkisi
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            // Temel bilgiler
            $table->string('name');

            // İçerik
            $table->longText('description')->nullable();

            // Medya
            $table->string('image')->nullable();
            $table->string('video')->nullable();

            // Harici yönlendirme linki
            // NOT: utf8mb4 + uzun URL index limitini aşabileceği için index yok.
            $table->string('external_url', 2048)->nullable();

            // Görünürlük / geçerlilik
            $table->dateTime('availability_starts_at')->nullable();
            $table->dateTime('availability_ends_at')->nullable();
            $table->boolean('availability_forever')->default(true)->index();


            // Ürün puanlama (0-5)
            $table->unsignedTinyInteger('rating_stars')->default(0)->index();



            // Sıralama
            $table->unsignedInteger('sort_order')->default(0)->index();

            // Fiyat
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 3)->default('TRY');

            // Durum
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            // Sık kullanılan index
            $table->index(['category_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
