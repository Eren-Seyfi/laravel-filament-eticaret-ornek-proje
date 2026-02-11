<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();


            $table->string('name');
            $table->string('slug')->unique();

            // Basit yönetim alanları
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // Anasayfada listelensin mi?
            $table->boolean('show_on_homepage')->default(false)->index();

            $table->timestamps();
            $table->softDeletes();

            // Sık sorgulanan alanlara index
            $table->index(['is_active', 'show_on_homepage', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
