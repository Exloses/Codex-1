<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\StorefrontCache;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function show(string $slug): Response
    {
        $page = max(1, (int) request()->query('page', 1));
        $payload = StorefrontCache::remember(
            StorefrontCache::categoryProductsKey($slug, $page),
            StorefrontCache::CATALOG_TTL,
            function () use ($slug) {
                $category = Category::query()
                    ->select(['id', 'name', 'name_id', 'slug', 'icon', 'image', 'parent_id', 'is_active', 'sort_order', 'updated_at'])
                    ->where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

                return [
                    'category' => $category,
                    'products' => Product::query()
                        ->select(['id', 'vendor_id', 'category_id', 'name', 'name_id', 'slug', 'description', 'description_id', 'selling_price', 'compare_price', 'stock', 'weight', 'sku', 'size_guide_id', 'is_active', 'is_featured', 'total_sales', 'average_rating', 'videos', 'created_at', 'updated_at'])
                        ->with([
                            'category:id,name,name_id,slug,icon,image',
                            'vendor:id,store_name,slug,is_approved',
                            'variants:id,product_id,combination,sku,price,stock,image',
                        ])
                        ->withCount('variants')
                        ->where('category_id', $category->id)
                        ->where('is_active', true)
                        ->paginate(24)
                        ->withQueryString(),
                    'categories' => StorefrontCache::remember(StorefrontCache::CATEGORIES, StorefrontCache::CATALOG_TTL, fn () => Category::query()
                        ->select(['id', 'name', 'name_id', 'slug', 'icon', 'image', 'parent_id', 'is_active', 'sort_order', 'updated_at'])
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get()),
                    'filters' => ['category' => $slug],
                ];
            }
        );

        return Inertia::render('Storefront/ProductIndex', [
            ...$payload,
            'wishlistProductIds' => $this->wishlistProductIds($payload['products']->getCollection()->pluck('id')),
        ]);
    }

    private function wishlistProductIds($productIds)
    {
        if (! auth()->check()) {
            return [];
        }

        return auth()->user()
            ->wishlists()
            ->whereIn('product_id', collect($productIds)->filter()->values())
            ->pluck('product_id')
            ->values();
    }
}
