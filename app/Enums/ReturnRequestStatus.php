<?php

namespace App\Enums;

enum ReturnRequestStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RefundPending = 'refund_pending';
    case Refunded = 'refunded';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function activeValues(): array
    {
        return [
            self::Pending->value,
            self::UnderReview->value,
            self::Approved->value,
            self::RefundPending->value,
        ];
    }

    public static function terminalValues(): array
    {
        return [
            self::Rejected->value,
            self::Refunded->value,
            self::Cancelled->value,
        ];
    }

    public static function fromValue(string $value): self
    {
        return self::tryFrom($value) ?? throw new \InvalidArgumentException("Unsupported return request status [{$value}].");
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::RefundPending => 'Refund pending',
            self::Refunded => 'Refunded',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this->value, self::terminalValues(), true);
    }
}
