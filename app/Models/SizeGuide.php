<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SizeGuide extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => StorefrontCache::invalidateCategories());
        static::deleted(fn () => StorefrontCache::invalidateCategories());
    }

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'rows' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
