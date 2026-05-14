<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (Review $review) => StorefrontCache::invalidateProducts($review->product));
        static::deleted(fn (Review $review) => StorefrontCache::invalidateProducts($review->product));
    }

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
