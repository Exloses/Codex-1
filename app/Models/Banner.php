<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::invalidateBanners());
        static::deleted(fn () => StorefrontCache::invalidateBanners());
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
