<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (ProductVariant $variant) => StorefrontCache::invalidateProducts($variant->product));
        static::deleted(fn (ProductVariant $variant) => StorefrontCache::invalidateProducts($variant->product));
    }

    protected function casts(): array
    {
        return [
            'combination' => 'array',
            'price' => 'decimal:2',
            'vendor_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
