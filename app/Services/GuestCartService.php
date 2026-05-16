<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class GuestCartService
{
    private const SESSION_KEY = 'guest_cart';

    public function items(bool $forOrder = false): Collection
    {
        $lines = collect($this->all());

        if ($lines->isEmpty()) {
            return collect();
        }

        $productColumns = $forOrder
            ? ['id', 'vendor_id', 'name', 'slug', 'selling_price', 'stock', 'weight', 'is_active']
            : ['id', 'name', 'slug', 'selling_price', 'stock', 'weight', 'is_active'];

        $products = Product::query()
            ->select($productColumns)
            ->where('is_active', true)
            ->whereIn('id', $lines->pluck('product_id')->unique()->all())
            ->get()
            ->keyBy('id');

        $variants = ProductVariant::query()
            ->select(['id', 'product_id', 'combination', 'price', 'stock', 'image'])
            ->whereIn('id', $lines->pluck('product_variant_id')->filter()->unique()->all())
            ->get()
            ->keyBy('id');

        return $lines
            ->map(function (array $line) use ($products, $variants) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    return null;
                }

                $variant = $line['product_variant_id']
                    ? $variants->get($line['product_variant_id'])
                    : null;

                return [
                    'id' => $line['id'],
                    'product_id' => $line['product_id'],
                    'product_variant_id' => $line['product_variant_id'],
                    'quantity' => $line['quantity'],
                    'custom_note' => $line['custom_note'] ?? null,
                    'product' => $product,
                    'productVariant' => $variant,
                ];
            })
            ->filter()
            ->values();
    }

    public function put(int $productId, ?int $variantId, int $quantity, ?string $customNote = null): array
    {
        $cart = $this->all();
        $id = $this->lineId($productId, $variantId);

        $cart[$id] = [
            'id' => $id,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'quantity' => max(1, min(99, $quantity)),
            'custom_note' => $customNote,
        ];

        Session::put(self::SESSION_KEY, $cart);

        return $cart[$id];
    }

    public function update(string $id, int $quantity, ?string $customNote = null): ?array
    {
        $cart = $this->all();

        if (! isset($cart[$id])) {
            return null;
        }

        $cart[$id]['quantity'] = max(1, min(99, $quantity));
        $cart[$id]['custom_note'] = $customNote;
        Session::put(self::SESSION_KEY, $cart);

        return $cart[$id];
    }

    public function remove(string $id): void
    {
        $cart = $this->all();
        unset($cart[$id]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function all(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    private function lineId(int $productId, ?int $variantId): string
    {
        return $productId.'-'.($variantId ?: 'base');
    }
}
