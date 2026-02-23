<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('host')->unique();          // örn: example.com (www'süz sakla)
            $table->boolean('is_active')->default(true);

            $table->boolean('is_canonical')->default(false); // tek tane true olsun istersen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
