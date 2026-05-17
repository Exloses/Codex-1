<?php

namespace App\Enums;

enum OrderTrackingStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromValue(string $value): self
    {
        return self::tryFrom($value) ?? throw new \InvalidArgumentException("Unsupported tracking status [{$value}].");
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Order placed',
            self::Paid => 'Payment received',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::InTransit => 'In transit',
            self::OutForDelivery => 'Out for delivery',
            self::Delivered => 'Delivered',
            self::Failed => 'Delivery issue',
            self::Returned => 'Returned',
            self::Cancelled => 'Cancelled',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Pending => 'Your order has been created and is awaiting the next update.',
            self::Paid => 'Payment has been confirmed for this order.',
            self::Processing => 'The order is being prepared for fulfillment.',
            self::Shipped => 'The package has been handed to the carrier.',
            self::InTransit => 'The package is moving through the carrier network.',
            self::OutForDelivery => 'The package is out for final delivery.',
            self::Delivered => 'The package has been delivered.',
            self::Failed => 'The carrier reported a delivery issue.',
            self::Returned => 'The package has been returned.',
            self::Cancelled => 'The order or shipment was cancelled.',
        };
    }

    public function orderStatus(): ?string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Paid => 'paid',
            self::Processing => 'processing',
            self::Shipped, self::InTransit, self::OutForDelivery => 'shipped',
            self::Delivered => 'delivered',
            self::Failed => 'failed',
            self::Returned => 'returned',
            self::Cancelled => 'cancelled',
        };
    }

    public function dropshipStatus(): ?string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Paid => null,
            self::Processing => 'processing',
            self::Shipped, self::InTransit, self::OutForDelivery => 'shipped',
            self::Delivered => 'delivered',
            self::Failed => 'failed',
            self::Returned => 'returned',
            self::Cancelled => 'cancelled',
        };
    }
}
