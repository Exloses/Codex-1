<?php

namespace App\Models;

use App\Enums\ReturnRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'status' => ReturnRequestStatus::class,
            'refund_amount_usd' => 'decimal:2',
            'resolved_at' => 'datetime',
            'refund_processed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusValue(): string
    {
        return $this->status instanceof ReturnRequestStatus ? $this->status->value : (string) $this->status;
    }

    public function isActive(): bool
    {
        return in_array($this->statusValue(), ReturnRequestStatus::activeValues(), true);
    }
}
