<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CartRequest;
use App\Models\CartItem;
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
            $line = $guestCart->update($id, $request->integer('quantity', 1), $request->input('custom_note'));
            abort_if(! $line, 404);

            return $this->ok([
                'item' => $guestCart->items()->firstWhere('id', $line['id']),
            ]);
        }

        $item = CartItem::query()->where('user_id', $request->user()->id)->findOrFail($id);
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
}
