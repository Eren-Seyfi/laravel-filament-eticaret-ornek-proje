<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        // Genel
        'site_name',
        'site_tagline',

        // Medya
        'logo_path',
        'favicon_path',
        'og_image_path',

        // SEO
        'seo_title',
        'seo_description',
        'seo_keywords',

        // Index / Robots
        'search_engine_indexing',
        'seo_noindex',
        'seo_nofollow',
        'robots_txt_enabled',
        'robots_txt_custom',
        'robots_txt_content',
        'sitemap_url',

        // İletişim / Sosyal
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_links',

        // Script alanları
        'header_scripts',
        'footer_scripts',

        // Site durumu
        'maintenance_mode',
        'maintenance_message',
    ];

    protected $casts = [
        'social_links' => 'array',

        'maintenance_mode' => 'boolean',

        // Index / Robots
        'search_engine_indexing' => 'boolean',
        'seo_noindex' => 'boolean',
        'seo_nofollow' => 'boolean',
        'robots_txt_enabled' => 'boolean',
        'robots_txt_custom' => 'boolean',

        // RichEditor ile bakım mesajı HTML içerebilir, bu yüzden array olarak cast ediyoruz.
        'maintenance_message' => 'array',

    ];

    /**
     * ✅ Ayarlar kaydolunca cache tamamen temizlensin
     * - created/updated/saved olaylarında çalışır
     */
    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('settings.current'));
        static::deleted(fn() => Cache::forget('settings.current'));
    }


    /**
     * Tek kayıt mantığı: yoksa oluştur, varsa onu dön.
     */
    public static function current(): self
    {
        $record = static::query()->latest('id')->first();

        if ($record) {
            return $record;
        }

        return static::query()->create([
            'site_name' => config('app.name', 'Site'),
            'search_engine_indexing' => true,
            'seo_noindex' => false,
            'seo_nofollow' => false,
            'robots_txt_enabled' => true,
            'robots_txt_custom' => false,
            'social_links' => [],
            'maintenance_mode' => false,
        ]);
    }


    public static function cached(): self
    {
        return Cache::rememberForever('settings.current', fn() => static::current());
    }




}
