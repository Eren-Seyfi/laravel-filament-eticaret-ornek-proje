<?php

namespace App\Models;

use App\Enums\TrackEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'sort_order',
        'show_on_homepage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $category) {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (self $category) {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Track events (polymorphic)
     */
    public function events(): MorphMany
    {
        return $this->morphMany(TrackEvent::class, 'trackable');
    }

    /**
     * Görüntüleme (view) eventleri
     */
    public function views(): MorphMany
    {
        return $this->events()->where('event', TrackEventType::View->value);
    }

    /**
     * Tıklama (click) eventleri
     */
    public function clicks(): MorphMany
    {
        return $this->events()->where('event', TrackEventType::Click->value);
    }

    /**
     * Event kayıt helper'ları
     *
     * Kullanım:
     * - $category->trackView();
     * - $category->trackClick();
     */
    public function trackEvent(TrackEventType $type, ?Request $request = null, array $extra = []): TrackEvent
    {
        $request ??= request();

        return $this->events()->create(array_merge([
            'event' => $type, // enum cast: TrackEventType::class

            // opsiyonel metadata
            'page_key' => $extra['page_key'] ?? null,
            'event_id' => $extra['event_id'] ?? null, // gerekiyorsa

            // request bilgileri
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'user_id' => optional($request->user())->id,

            'referrer' => $request->headers->get('referer'),
            'url' => $request->fullUrl(),

            // süre alanları (isteğe bağlı)
            'started_at' => $extra['started_at'] ?? null,
            'ended_at' => $extra['ended_at'] ?? null,
            'duration_ms' => $extra['duration_ms'] ?? null,
        ], $extra));
    }

    public function trackView(?Request $request = null, array $extra = []): TrackEvent
    {
        return $this->trackEvent(TrackEventType::View, $request, $extra);
    }

    public function trackClick(?Request $request = null, array $extra = []): TrackEvent
    {
        return $this->trackEvent(TrackEventType::Click, $request, $extra);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
