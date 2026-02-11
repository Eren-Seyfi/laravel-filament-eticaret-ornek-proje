<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id(); // Kayıt ID

            $table->string('title'); // Reklam/Duyuru başlığı
            $table->longText('content')->nullable(); // Reklam içeriği (metin/HTML/embed)

            $table->json('image')->nullable();// Görsel dosya yolu
            $table->string('video')->nullable(); // Video dosya yolu
            $table->string('external_url', 2048)->nullable(); // Tıklayınca gidilecek link

            $table->string('placement', 20)->index(); // Reklam konumu (front/top/bottom/left/right/popup)

            $table->unsignedInteger('priority')->default(0)->index(); // Aynı konumdaki sıralama önceliği (büyük olan üstte)

            $table->dateTime('starts_at')->nullable()->index(); // Yayın başlangıç tarihi
            $table->dateTime('ends_at')->nullable()->index(); // Yayın bitiş tarihi
            $table->boolean('is_forever')->default(true)->index(); // Süresiz mi? (true ise tarih zorunlu değil)

            $table->boolean('is_active')->default(true)->index(); // Aktif/Pasif durumu

            $table->timestamps(); // created_at / updated_at

            $table->index(['placement', 'is_active', 'priority']); // Hızlı listeleme için birleşik index
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
