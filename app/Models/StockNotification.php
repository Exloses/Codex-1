<?php

namespace App\Models;

use App\Services\ProductAlertService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockNotification extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'target_price_usd' => 'decimal:2',
            'is_notified' => 'boolean',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_notified', false);
    }

    public function scopeStockAlerts(Builder $query): Builder
    {
        return $query->where('type', ProductAlertService::TYPE_STOCK);
    }

    public function scopePriceAlerts(Builder $query): Builder
    {
        return $query->where('type', ProductAlertService::TYPE_PRICE);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
