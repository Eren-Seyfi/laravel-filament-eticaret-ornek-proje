<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('track_events', function (Blueprint $table) {
            $table->id(); // Kayıt ID

            $table->string('event', 20)->index(); // view / click / page_view / heartbeat

            // Client tarafı üretebilir (tekil event)
            $table->ulid('event_id')->index();

            // Model'e bağlı olmayan sayfalar (örn: home, about, pricing)
            $table->string('page_key', 100)->nullable()->index();

            // ✅ NULL sorununu çözmek için normalize edilmiş kolon (generated)
            // NULL => '__none__' gibi sabit bir değere dönüşür
            $table->string('page_key_key', 100)
                ->storedAs("COALESCE(page_key, '__none__')")
                ->index();

            // Polymorphic ilişki: product veya advert gibi
            $table->string('trackable_type')->nullable();
            $table->unsignedBigInteger('trackable_id')->nullable();
            $table->index(['trackable_type', 'trackable_id']);

            // Ziyaret bilgileri
            $table->string('session_id', 100)->nullable()->index();
            $table->string('ip', 45)->nullable()->index();
            $table->string('user_agent', 512)->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Kaynak sayfa
            $table->string('referrer', 2048)->nullable();
            $table->string('url', 2048)->nullable();

            // Süre ölçümü
            $table->dateTime('started_at')->nullable()->index();
            $table->dateTime('ended_at')->nullable()->index();
            $table->unsignedInteger('duration_ms')->default(0)->index();

            $table->timestamps();

            // ✅ Gün bazlı tekilleştirme için generated event_date
            $table->date('event_date')
                ->storedAs('DATE(created_at)')
                ->index();

            // ✅ Asıl kural: aynı event + aynı session + aynı page_key + aynı gün => 1 kayıt
            $table->unique(['event', 'session_id', 'page_key_key', 'event_date'], 'uniq_event_session_page_day');

            // (Opsiyonel) Event ID tekil kalsın istiyorsan bırak:
            $table->unique(['event', 'event_id'], 'uniq_event_event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_events');
    }
};
