<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (ProductAttribute $attribute) => StorefrontCache::invalidateProducts($attribute->product));
        static::deleted(fn (ProductAttribute $attribute) => StorefrontCache::invalidateProducts($attribute->product));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }
}
