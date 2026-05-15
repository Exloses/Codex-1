<?php

namespace App\Support;

class TawkSettings
{
    public static function publicConfig(): array
    {
        $propertyId = config('services.tawk.property_id');
        $widgetId = config('services.tawk.widget_id');

        if (! self::enabled($propertyId, $widgetId)) {
            return [
                'enabled' => false,
                'propertyId' => null,
                'widgetId' => null,
            ];
        }

        return [
            'enabled' => true,
            'propertyId' => $propertyId,
            'widgetId' => $widgetId,
        ];
    }

    public static function enabled(?string $propertyId, ?string $widgetId): bool
    {
        if (app()->runningUnitTests()) {
            return false;
        }

        return self::credentialsAreUsable($propertyId, $widgetId);
    }

    public static function credentialsAreUsable(?string $propertyId, ?string $widgetId): bool
    {
        return self::valueIsUsable($propertyId) && self::valueIsUsable($widgetId);
    }

    private static function valueIsUsable(?string $value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        $normalized = strtoupper($value);

        return ! str_contains($normalized, 'YOUR_')
            && ! str_contains($normalized, 'PLACEHOLDER')
            && ! str_contains($normalized, 'CHANGE_ME')
            && ! str_contains($normalized, 'EXAMPLE')
            && ! str_starts_with($normalized, 'XXX');
    }
}
