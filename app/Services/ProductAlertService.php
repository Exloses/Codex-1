<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ProductAlertService
{
    public const TYPE_STOCK = 'stock';

    public const TYPE_PRICE = 'price';

    public function createStockAlert(array $data, ?User $user = null): array
    {
        [$product, $variant] = $this->resolveProductAndVariant($data);
        $currentStock = $this->currentStock($product, $variant);

        if ($currentStock > 0) {
            throw ValidationException::withMessages([
                'product_id' => 'This product is already in stock.',
            ]);
        }

        $alert = $this->upsertAlert(
            type: self::TYPE_STOCK,
            product: $product,
            variant: $variant,
            user: $user,
            guestEmail: $this->guestEmail($data, $user),
            values: ['target_price_usd' => null],
        );

        return [
            'alert' => $alert,
            'message' => $alert->wasRecentlyCreated
                ? 'We will email you when this item is back in stock.'
                : 'Your stock notification is active again.',
        ];
    }

    public function createPriceAlert(array $data, ?User $user = null): array
    {
        [$product, $variant] = $this->resolveProductAndVariant($data);
        $currentPrice = $this->currentPrice($product, $variant);
        $targetPrice = round((float) $data['target_price_usd'], 2);

        if ($targetPrice >= $currentPrice) {
            throw ValidationException::withMessages([
                'target_price_usd' => 'Choose a target price lower than the current price.',
            ]);
        }

        $alert = $this->upsertAlert(
            type: self::TYPE_PRICE,
            product: $product,
            variant: $variant,
            user: $user,
            guestEmail: $this->guestEmail($data, $user),
            values: ['target_price_usd' => $targetPrice],
        );

        return [
            'alert' => $alert,
            'message' => $alert->wasRecentlyCreated
                ? 'We will email you if the price reaches your target.'
                : 'Your price alert has been updated.',
        ];
    }

    public function currentPrice(Product $product, ?ProductVariant $variant = null): float
    {
        if ($variant && $variant->price !== null) {
            return (float) $variant->price;
        }

        return (float) $product->selling_price;
    }

    public function currentStock(Product $product, ?ProductVariant $variant = null): int
    {
        if ($variant) {
            return (int) $variant->stock;
        }

        return (int) $product->stock;
    }

    public function notificationCurrentPrice(StockNotification $notification): ?float
    {
        if (! $notification->product || ! $notification->product->is_active) {
            return null;
        }

        if ($notification->product_variant_id && ! $this->notificationVariantIsValid($notification)) {
            return null;
        }

        return $this->currentPrice($notification->product, $notification->productVariant);
    }

    public function notificationIsInStock(StockNotification $notification): bool
    {
        if (! $notification->product || ! $notification->product->is_active) {
            return false;
        }

        if ($notification->product_variant_id && ! $this->notificationVariantIsValid($notification)) {
            return false;
        }

        return $this->currentStock($notification->product, $notification->productVariant) > 0;
    }

    public function notificationVariantIsValid(StockNotification $notification): bool
    {
        return $notification->productVariant
            && $notification->product
            && (int) $notification->productVariant->product_id === (int) $notification->product->id;
    }

    public function recipientEmail(StockNotification $notification): ?string
    {
        return $notification->user?->email ?: $notification->guest_email;
    }

    private function resolveProductAndVariant(array $data): array
    {
        $product = Product::query()
            ->whereKey($data['product_id'])
            ->where('is_active', true)
            ->first();

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => 'Choose an active product.',
            ]);
        }

        $variantId = Arr::get($data, 'product_variant_id');
        $variant = null;

        if ($variantId) {
            $variant = $product->variants()->whereKey($variantId)->first();

            if (! $variant) {
                throw ValidationException::withMessages([
                    'product_variant_id' => 'Choose a valid variant for this product.',
                ]);
            }
        }

        return [$product, $variant];
    }

    private function upsertAlert(
        string $type,
        Product $product,
        ?ProductVariant $variant,
        ?User $user,
        ?string $guestEmail,
        array $values = [],
    ): StockNotification {
        $identity = [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'type' => $type,
        ];

        if ($user) {
            $identity['user_id'] = $user->id;
        } else {
            $identity['user_id'] = null;
            $identity['guest_email'] = $guestEmail;
        }

        return StockNotification::query()->updateOrCreate($identity, array_merge($identity, $values, [
            'guest_email' => $user ? null : $guestEmail,
            'is_notified' => false,
        ]));
    }

    private function guestEmail(array $data, ?User $user): ?string
    {
        if ($user) {
            return null;
        }

        $email = strtolower(trim((string) Arr::get($data, 'guest_email')));

        if ($email === '') {
            throw ValidationException::withMessages([
                'guest_email' => 'Enter your email address.',
            ]);
        }

        return $email;
    }
}
