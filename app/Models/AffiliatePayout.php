<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliatePayout extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount_usd' => 'decimal:2',
            'fee_usd' => 'decimal:2',
            'net_amount_usd' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function payoutMethod(): BelongsTo
    {
        return $this->belongsTo(AffiliatePayoutMethod::class, 'payout_method_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'payout_id');
    }
}
