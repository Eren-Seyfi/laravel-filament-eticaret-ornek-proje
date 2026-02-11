<?php

namespace App\Models;

use App\Enums\CurrencyCode;
use App\Enums\TrackEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',

        'name',
        'description',

        'image',
        'video',

        'external_url',

        'availability_starts_at',
        'availability_ends_at',
        'availability_forever',

        'rating_stars',

        'sort_order',

        'price',
        'currency',

        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',

        'availability_starts_at' => 'datetime',
        'availability_ends_at' => 'datetime',
        'availability_forever' => 'boolean',

        'rating_stars' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',

        // ✅ Enum cast
        'currency' => CurrencyCode::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $product) {
            $product->currency ??= CurrencyCode::TRY;
            $product->availability_forever ??= true;
            $product->rating_stars ??= 0;
            $product->sort_order ??= 0;
            $product->is_active ??= true;
        });

        static::updating(function (self $product) {
            $disk = Storage::disk('public_root');

            if ($product->isDirty('image')) {
                $oldImagePath = $product->getOriginal('image');
                if ($oldImagePath && $disk->exists($oldImagePath)) {
                    $disk->delete($oldImagePath);
                }
            }

            if ($product->isDirty('video')) {
                $oldVideoPath = $product->getOriginal('video');
                if ($oldVideoPath && $disk->exists($oldVideoPath)) {
                    $disk->delete($oldVideoPath);
                }
            }
        });

        static::deleting(function (self $product) {
            $disk = Storage::disk('public_root');

            foreach (['image', 'video'] as $field) {
                $path = $product->{$field};

                if ($path && $disk->exists($path)) {
                    $disk->delete($path);
                }
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Track events (polymorphic)
     */
    public function events(): MorphMany
    {
        return $this->morphMany(TrackEvent::class, 'trackable');
    }

    /**
     * Ürün görüntülemeleri (view)
     */
    public function views(): MorphMany
    {
        return $this->events()
            ->where('event', TrackEventType::View->value);
    }

    /**
     * Ürün tıklamaları (click)
     */
    public function clicks(): MorphMany
    {
        return $this->events()
            ->where('event', TrackEventType::Click->value);
    }
}
