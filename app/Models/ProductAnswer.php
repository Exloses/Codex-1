<?php

namespace App\Models;

use App\Services\StorefrontCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAnswer extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn (ProductAnswer $answer) => StorefrontCache::invalidateProducts($answer->question?->product));
        static::deleted(fn (ProductAnswer $answer) => StorefrontCache::invalidateProducts($answer->question?->product));
    }

    protected function casts(): array
    {
        return [
            'is_vendor' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductQuestion::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
