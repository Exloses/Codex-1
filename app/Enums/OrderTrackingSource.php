<?php

namespace App\Enums;

enum OrderTrackingSource: string
{
    case System = 'system';
    case Admin = 'admin';
    case Vendor = 'vendor';
    case Carrier = 'carrier';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromValue(string $value): self
    {
        return self::tryFrom($value) ?? throw new \InvalidArgumentException("Unsupported tracking source [{$value}].");
    }
}
