<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CartRequest;
use App\Models\CartItem;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Storefront/Cart', [
            'items' => CartItem::query()
                ->with(['product:id,name,slug,selling_price,stock,weight', 'productVariant:id,product_id,combination,price,stock,image'])
                ->where('user_id', auth()->id())
                ->get(),
        ]);
    }

    public function store(CartRequest $request)
    {
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

    public function update(CartRequest $request, int $id)
    {
        $item = CartItem::query()->where('user_id', $request->user()->id)->findOrFail($id);
        $item->update($request->safe()->only(['quantity']));

        return $this->ok(['item' => $item]);
    }

    public function destroy(int $id)
    {
        CartItem::query()->where('user_id', auth()->id())->whereKey($id)->delete();

        return $this->ok();
    }
}
