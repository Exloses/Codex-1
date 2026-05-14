<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (Product $product) => StorefrontCache::invalidateProducts($product));
        static::deleted(fn (Product $product) => StorefrontCache::invalidateProducts($product));
    }

    protected function casts(): array
    {
        return [
            'vendor_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'videos' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizeGuide(): BelongsTo
    {
        return $this->belongsTo(SizeGuide::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function isInStock(): bool
    {
        return $this->stock > 0 || $this->variants()->where('stock', '>', 0)->exists();
    }
}
