<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Wishlist', [
            'items' => Wishlist::query()
                ->with('product:id,name,slug,selling_price,compare_price,stock,average_rating')
                ->where('user_id', auth()->id())
                ->get(),
        ]);
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::query()->where('user_id', auth()->id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();

            return $this->ok(['wishlisted' => false]);
        }

        Wishlist::query()->create(['user_id' => auth()->id(), 'product_id' => $product->id]);

        return $this->ok(['wishlisted' => true]);
    }
}
