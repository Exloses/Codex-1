<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'subtotal_usd' => 'decimal:2',
            'shipping_cost_usd' => 'decimal:2',
            'discount_usd' => 'decimal:2',
            'total_usd' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'total_buyer_currency' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->items();
    }

    public function dropshipOrders(): HasMany
    {
        return $this->hasMany(DropshipOrder::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(OrderTrackingEvent::class)->chronological();
    }

    public function latestTrackingEvent(): HasOne
    {
        return $this->hasOne(OrderTrackingEvent::class)->latestOfMany('occurred_at');
    }

    public function affiliateCommission(): HasOne
    {
        return $this->hasOne(AffiliateCommission::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }
}
