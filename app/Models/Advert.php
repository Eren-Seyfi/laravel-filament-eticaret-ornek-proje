<?php

namespace App\Models;

use App\Enums\AdvertPlacement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Advert extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'video',
        'external_url',
        'placement',
        'priority',
        'starts_at',
        'ends_at',
        'is_forever',
        'is_active',
    ];

    protected $casts = [
        'image' => 'array',
        'placement' => AdvertPlacement::class,
        'is_active' => 'boolean',
        'is_forever' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /* --------------------------
     | Scopes
     -------------------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPlacement(Builder $query, AdvertPlacement|string $placement): Builder
    {
        $value = $placement instanceof AdvertPlacement ? $placement->value : (string) $placement;

        return $query->where('placement', $value);
    }

    public function scopeCurrentlyVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('is_forever', true)
                ->orWhere(function (Builder $q2) {
                    $now = now();

                    $q2->where('is_forever', false)
                        ->where(function (Builder $q3) use ($now) {
                            $q3->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', $now);
                        })
                        ->where(function (Builder $q4) use ($now) {
                            $q4->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', $now);
                        });
                });
        });
    }

    /**
     * ✅ Gösterim sıralaması:
     * 1) priority (büyük üstte)
     * 2) starts_at (daha yeni üstte)
     * 3) created_at (stabil)
     * 4) id (son tie-breaker)
     */
    public function scopeOrderedForDisplay(Builder $query): Builder
    {
        return $query
            ->orderByDesc('priority')
            ->orderByDesc('starts_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /* --------------------------
     | Relations
     -------------------------- */

    public function events(): MorphMany
    {
        return $this->morphMany(\App\Models\TrackEvent::class, 'trackable');
    }

    public function views(): MorphMany
    {
        return $this->events()->where('event', 'view');
    }

    public function clicks(): MorphMany
    {
        return $this->events()->where('event', 'click');
    }

    /* --------------------------
     | Helpers
     -------------------------- */

    /**
     * Belirli bir placement için tek carousel slide listesi üretir (flatten).
     *
     * @return array<int, array{advert_id:int, img:string, external_url:?string}>
     */
    public static function slidesForPlacement(AdvertPlacement|string $placement): array
    {
        $adverts = self::query()
            ->active()
            ->currentlyVisible()
            ->forPlacement($placement)
            ->orderedForDisplay()
            ->get();

        $slides = [];

        foreach ($adverts as $advert) {
            $raw = $advert->image;

            $images = collect(is_array($raw) ? $raw : [])
                ->flatMap(function ($value, $key) {
                    // 1) value direkt string ise (["a.jpg","b.jpg"])
                    if (is_string($value) && $value !== '') {
                        return [$value];
                    }

                    // 2) key string ise ve value array/object ise ({"a.jpg": {...}})
                    if (is_string($key) && $key !== '') {
                        return [$key];
                    }

                    // 3) value array ise ( [{"path":"a.jpg"}] gibi )
                    if (is_array($value)) {
                        foreach (['path', 'url', 'file', 'src'] as $k) {
                            if (!empty($value[$k]) && is_string($value[$k])) {
                                return [$value[$k]];
                            }
                        }
                    }

                    return [];
                })
                ->filter(fn($p) => is_string($p) && $p !== '')
                ->values()
                ->all();

            foreach ($images as $img) {
                $slides[] = [
                    'advert_id' => (int) $advert->id,
                    'img' => $img,
                    'external_url' => $advert->external_url ?: null,
                ];
            }
        }

        return $slides;
    }



    public function normalizedImages(): array
    {
        $raw = $this->image;

        return collect(is_array($raw) ? $raw : [])
            ->flatMap(function ($value, $key) {
                // 1) value direkt string ise (["a.jpg","b.jpg"])
                if (is_string($value) && $value !== '') {
                    return [$value];
                }

                // 2) key string ise ve value array/object ise ({"a.jpg": {...}})
                if (is_string($key) && $key !== '') {
                    return [$key];
                }

                // 3) value array ise ( [{"path":"a.jpg"}] gibi )
                if (is_array($value)) {
                    foreach (['path', 'url', 'file', 'src'] as $k) {
                        if (!empty($value[$k]) && is_string($value[$k])) {
                            return [$value[$k]];
                        }
                    }
                }

                return [];
            })
            ->filter(fn($p) => is_string($p) && $p !== '')
            ->values()
            ->all();
    }

    /**
     * ✅ FRONT için: her advert ayrı swiper olacak şekilde slider grupları üretir.
     *
     * @return array<int, array{
     *   advert_id:int,
     *   title:?string,
     *   external_url:?string,
     *   uid:string,
     *   slides:array<int, array{img:string}>
     * }>
     */
    public static function frontSliders(): array
    {
        $adverts = self::query()
            ->active()
            ->currentlyVisible()
            ->forPlacement(AdvertPlacement::Front)
            ->orderedForDisplay()
            ->get();

        $out = [];

        foreach ($adverts as $advert) {
            $images = $advert->normalizedImages();

            if (empty($images)) {
                continue;
            }

            $out[] = [
                'advert_id' => (int) $advert->id,
                'title' => $advert->title ?: null,
                'external_url' => $advert->external_url ?: null,
                'uid' => 'frontSwiper_' . $advert->id . '_' . uniqid(),
                'slides' => array_map(fn($img) => ['img' => $img], $images),
            ];
        }

        return $out;
    }
    

}
