<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductQuestion extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (ProductQuestion $question) => StorefrontCache::invalidateProducts($question->product));
        static::deleted(fn (ProductQuestion $question) => StorefrontCache::invalidateProducts($question->product));
    }

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ProductAnswer::class, 'question_id');
    }
}
