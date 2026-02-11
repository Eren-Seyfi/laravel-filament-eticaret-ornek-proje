<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use Illuminate\Support\Facades\Response;



Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/category/{slug}', 'pages::category')->name('category');


Route::get('/robots.txt', function () {
    $s = Setting::query()->first() ?? new Setting([
        'robots_txt_enabled' => true,
        'robots_txt_custom' => false,
        'search_engine_indexing' => true,
    ]);

    if (!($s->robots_txt_enabled ?? true)) {
        return Response::make('', 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    if ($s->robots_txt_custom && filled($s->robots_txt_content)) {
        return Response::make($s->robots_txt_content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    // otomatik üret
    $lines = [
        "User-agent: *",
        ($s->search_engine_indexing ? "Disallow:" : "Disallow: /"),
    ];

    if (filled($s->sitemap_url)) {
        $lines[] = "Sitemap: {$s->sitemap_url}";
    }

    return Response::make(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
});
