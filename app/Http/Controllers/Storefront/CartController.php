<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\GuestCartService;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(GuestCartService $guestCart): Response
    {
        return Inertia::render('Storefront/Cart', [
            'items' => auth()->check()
                ? CartItem::query()
                    ->select(['id', 'user_id', 'product_id', 'product_variant_id', 'quantity', 'updated_at'])
                    ->with(['product:id,name,slug,selling_price,stock,weight', 'productVariant:id,product_id,combination,price,stock,image'])
                    ->where('user_id', auth()->id())
                    ->get()
                : $guestCart->items(),
        ]);
    }

    public function store(CartRequest $request, GuestCartService $guestCart)
    {
        if (! $request->user()) {
            $this->abortIfQuantityExceedsStock(
                $request->integer('product_id'),
                $request->filled('product_variant_id') ? $request->integer('product_variant_id') : null,
                $request->integer('quantity', 1),
            );

            $line = $guestCart->put(
                $request->integer('product_id'),
                $request->filled('product_variant_id') ? $request->integer('product_variant_id') : null,
                $request->integer('quantity', 1),
                $request->input('custom_note'),
            );

            return $this->ok([
                'item' => $guestCart->items()->firstWhere('id', $line['id']),
            ], 201);
        }

        $item = CartItem::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->validated('product_id'),
                'product_variant_id' => $request->validated('product_variant_id'),
            ],
            ['quantity' => $request->integer('quantity', 1)],
        );

        return $this->ok([
            'item' => $item->load([
                'product:id,name,slug,selling_price,stock,weight',
                'productVariant:id,product_id,combination,price,stock,image',
            ]),
        ], 201);
    }

    public function update(CartRequest $request, string $id, GuestCartService $guestCart)
    {
        if (! $request->user()) {
            $existingLine = $guestCart->all()[$id] ?? null;
            abort_if(! $existingLine, 404);

            $this->abortIfQuantityExceedsStock(
                (int) $existingLine['product_id'],
                $existingLine['product_variant_id'] ? (int) $existingLine['product_variant_id'] : null,
                $request->integer('quantity', 1),
            );

            $line = $guestCart->update($id, $request->integer('quantity', 1), $request->input('custom_note'));
            abort_if(! $line, 404);

            return $this->ok([
                'item' => $guestCart->items()->firstWhere('id', $line['id']),
            ]);
        }

        $item = CartItem::query()->where('user_id', $request->user()->id)->findOrFail($id);
        $this->abortIfQuantityExceedsStock($item->product_id, $item->product_variant_id, $request->integer('quantity', 1));
        $item->update($request->safe()->only(['quantity']));

        return $this->ok(['item' => $item->load([
            'product:id,name,slug,selling_price,stock,weight',
            'productVariant:id,product_id,combination,price,stock,image',
        ])]);
    }

    public function destroy(string $id, GuestCartService $guestCart)
    {
        if (! auth()->check()) {
            $guestCart->remove($id);

            return $this->ok();
        }

        CartItem::query()->where('user_id', auth()->id())->whereKey($id)->delete();

        return $this->ok();
    }

    private function abortIfQuantityExceedsStock(int $productId, ?int $variantId, int $quantity): void
    {
        if ($variantId) {
            $variant = ProductVariant::query()
                ->select(['id', 'product_id', 'stock'])
                ->whereKey($variantId)
                ->first();

            abort_if(! $variant || $variant->product_id !== $productId, 422, 'The selected product variant is invalid for this product.');
            abort_if($quantity > $variant->stock, 422, 'The requested quantity exceeds the selected variant stock.');

            return;
        }

        $product = Product::query()
            ->select(['id', 'stock'])
            ->with('variants:id,product_id')
            ->findOrFail($productId);

        abort_if($product->variants->isNotEmpty(), 422, 'Please select a product variant before adding this item to cart.');
        abort_if($quantity > $product->stock, 422, 'The requested quantity exceeds product stock.');
    }
}
