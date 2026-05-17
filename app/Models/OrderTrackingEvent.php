<?php

namespace App\Models;

use App\Enums\OrderTrackingSource;
use App\Enums\OrderTrackingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTrackingEvent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'occurred_at' => 'datetime',
            'status' => OrderTrackingStatus::class,
            'source' => OrderTrackingSource::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function dropshipOrder(): BelongsTo
    {
        return $this->belongsTo(DropshipOrder::class);
    }

    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('occurred_at')->orderBy('id');
    }

    public function scopeLatestEvent(Builder $query): Builder
    {
        return $query->latest('occurred_at')->latest('id');
    }
}
