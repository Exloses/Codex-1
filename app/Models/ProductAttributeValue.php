<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (ProductAttributeValue $value) => StorefrontCache::invalidateProducts($value->attribute?->product));
        static::deleted(fn (ProductAttributeValue $value) => StorefrontCache::invalidateProducts($value->attribute?->product));
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }
}
