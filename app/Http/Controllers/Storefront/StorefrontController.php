<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Services\StorefrontCache;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function home(): Response
    {
        $payload = StorefrontCache::remember(StorefrontCache::HOME, StorefrontCache::HOME_TTL, fn () => [
            'banners' => StorefrontCache::remember(StorefrontCache::BANNERS, StorefrontCache::CATALOG_TTL, fn () => Banner::query()
                ->select(['id', 'title', 'title_id', 'image', 'link', 'is_active', 'sort_order', 'updated_at'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()),
            'categories' => StorefrontCache::remember(StorefrontCache::CATEGORIES, StorefrontCache::CATALOG_TTL, fn () => Category::query()
                ->select(['id', 'name', 'name_id', 'slug', 'icon', 'image', 'parent_id', 'is_active', 'sort_order', 'updated_at'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()),
            'featuredProducts' => Product::query()
                ->select($this->productColumns())
                ->with([
                    'category:id,name,name_id,slug,icon,image',
                    'vendor:id,store_name,slug,is_approved',
                    'variants:id,product_id,combination,sku,price,stock,image',
                ])
                ->withCount('variants')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->limit(12)
                ->get(),
        ]);

        return Inertia::render('Storefront/Home', [
            ...$payload,
            'wishlistProductIds' => $this->wishlistProductIds($payload['featuredProducts']->pluck('id')),
        ]);
    }

    private function productColumns(): array
    {
        return ['id', 'vendor_id', 'category_id', 'name', 'name_id', 'slug', 'description', 'description_id', 'selling_price', 'compare_price', 'stock', 'weight', 'sku', 'size_guide_id', 'is_active', 'is_featured', 'total_sales', 'average_rating', 'videos', 'created_at', 'updated_at'];
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
