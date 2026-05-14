<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::invalidateFaqs());
        static::deleted(fn () => StorefrontCache::invalidateFaqs());
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
