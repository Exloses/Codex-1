<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\WishlistRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(): Response
    {
        $items = Wishlist::query()
            ->select(['id', 'user_id', 'product_id', 'created_at'])
            ->with([
                'product' => fn ($query) => $query
                    ->select($this->productColumns())
                    ->with([
                        'category:id,name,name_id,slug,icon,image',
                        'vendor:id,store_name,slug,is_approved',
                        'variants:id,product_id,combination,sku,price,stock,image',
                    ])
                    ->where('is_active', true),
            ])
            ->where('user_id', auth()->id())
            ->whereHas('product', fn ($query) => $query->where('is_active', true))
            ->latest()
            ->get();

        return Inertia::render('Account/Wishlist', [
            'items' => $items,
            'wishlistProductIds' => $items->pluck('product_id')->values(),
        ]);
    }

    public function store(WishlistRequest $request): JsonResponse|RedirectResponse
    {
        $wishlist = Wishlist::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->integer('product_id'),
        ]);

        return $this->wishlistResponse($request, true, 'Product added to your wishlist.', [
            'wishlist_id' => $wishlist->id,
        ], 201);
    }

    public function destroy(Request $request, Wishlist $wishlist): JsonResponse|RedirectResponse
    {
        abort_unless($wishlist->user_id === $request->user()->id, 404);

        $productId = $wishlist->product_id;
        $wishlist->delete();

        return $this->wishlistResponse($request, false, 'Product removed from your wishlist.', [
            'product_id' => $productId,
        ]);
    }

    public function toggle(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $wishlist = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            return $this->wishlistResponse($request, false, 'Product removed from your wishlist.', [
                'product_id' => $product->id,
            ]);
        }

        $wishlist = Wishlist::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
        ]);

        return $this->wishlistResponse($request, true, 'Product added to your wishlist.', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ], 201);
    }

    public function moveToCart(Request $request, Wishlist $wishlist): JsonResponse|RedirectResponse
    {
        abort_unless($wishlist->user_id === $request->user()->id, 404);

        $wishlist->load([
            'product' => fn ($query) => $query
                ->select(['id', 'is_active', 'stock'])
                ->with(['variants:id,product_id,stock']),
        ]);

        abort_if(! $wishlist->product || ! $wishlist->product->is_active, 404);
        $variant = $wishlist->product->variants->where('stock', '>', 0)->first();
        abort_if($wishlist->product->variants->isNotEmpty() && ! $variant, 422, 'This product is currently out of stock.');
        abort_if($wishlist->product->variants->isEmpty() && $wishlist->product->stock < 1, 422, 'This product is currently out of stock.');

        CartItem::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $wishlist->product_id,
                'product_variant_id' => $variant?->id,
            ],
            ['quantity' => 1],
        );

        $productId = $wishlist->product_id;
        $wishlist->delete();

        return $this->wishlistResponse($request, false, 'Product moved to your cart.', [
            'product_id' => $productId,
        ], 201);
    }

    private function wishlistResponse(Request $request, bool $wishlisted, string $message, array $data = [], int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return $this->ok(array_merge(['wishlisted' => $wishlisted], $data), $status);
        }

        return back(303)->with('status', $message);
    }

    private function productColumns(): array
    {
        return [
            'id',
            'vendor_id',
            'category_id',
            'name',
            'name_id',
            'slug',
            'description',
            'description_id',
            'selling_price',
            'compare_price',
            'stock',
            'weight',
            'sku',
            'size_guide_id',
            'is_active',
            'is_featured',
            'total_sales',
            'average_rating',
            'videos',
            'created_at',
            'updated_at',
        ];
    }
}
