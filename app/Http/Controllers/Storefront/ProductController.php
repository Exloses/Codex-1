<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\StorefrontCache;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = StorefrontCache::remember(
            StorefrontCache::productIndexKey($request),
            StorefrontCache::CATALOG_TTL,
            fn () => $this->query($request)->paginate(24)->withQueryString()
        );

        return Inertia::render('Storefront/ProductIndex', [
            'products' => $products,
            'categories' => $this->activeCategories(),
            'filters' => $request->only(['q', 'category', 'sort']),
            'wishlistProductIds' => $this->wishlistProductIds($products->getCollection()->pluck('id')),
        ]);
    }

    public function show(string $slug): Response
    {
        $payload = StorefrontCache::remember(
            StorefrontCache::productShowKey($slug),
            StorefrontCache::PRODUCT_TTL,
            function () use ($slug) {
                $product = Product::query()
                    ->select($this->columns())
                    ->with([
                        'category:id,name,name_id,slug,icon,image,parent_id,is_active,sort_order',
                        'sizeGuide:id,category_id,name,columns,rows,notes',
                        'attributes:id,product_id,name,sort_order',
                        'attributes.values:id,attribute_id,value,color_hex,sort_order',
                        'variants:id,product_id,combination,sku,price,stock,image,created_at,updated_at',
                        'reviews:id,user_id,product_id,rating,title,comment,images,is_verified,created_at',
                        'reviews.user:id,name',
                        'questions' => fn ($query) => $query
                            ->select(['id', 'product_id', 'user_id', 'question', 'is_public', 'created_at'])
                            ->where('is_public', true)
                            ->latest(),
                        'questions.answers:id,question_id,user_id,answer,is_vendor,is_verified,helpful_count,created_at',
                        'questions.answers.user:id,name',
                    ])
                    ->where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

                $product->variants->each->makeHidden('vendor_price');

                return [
                    'product' => $product,
                    'relatedProducts' => Product::query()
                        ->select($this->columns())
                        ->with('category:id,name,name_id,slug,icon,image')
                        ->where('category_id', $product->category_id)
                        ->whereKeyNot($product->id)
                        ->where('is_active', true)
                        ->limit(8)
                        ->get(),
                ];
            }
        );

        $visibleProductIds = collect([$payload['product']->id])
            ->merge($payload['relatedProducts']->pluck('id'))
            ->unique()
            ->values();

        return Inertia::render('Storefront/ProductShow', [
            ...$payload,
            'wishlistProductIds' => $this->wishlistProductIds($visibleProductIds),
        ]);
    }

    public function search(Request $request): Response
    {
        return $this->index($request);
    }

    private function query(Request $request)
    {
        return Product::query()
            ->select($this->columns())
            ->with('category:id,name,name_id,slug,icon,image')
            ->where('is_active', true)
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))
            ->when($request->input('sort') === 'price_asc', fn ($query) => $query->orderBy('selling_price'))
            ->when($request->input('sort') === 'price_desc', fn ($query) => $query->orderByDesc('selling_price'))
            ->latest();
    }

    private function columns(): array
    {
        return ['id', 'vendor_id', 'category_id', 'name', 'name_id', 'slug', 'description', 'description_id', 'selling_price', 'compare_price', 'stock', 'weight', 'sku', 'size_guide_id', 'is_active', 'is_featured', 'total_sales', 'average_rating', 'videos', 'created_at', 'updated_at'];
    }

    private function activeCategories()
    {
        return StorefrontCache::remember(StorefrontCache::CATEGORIES, StorefrontCache::CATALOG_TTL, fn () => Category::query()
            ->select(['id', 'name', 'name_id', 'slug', 'icon', 'image', 'parent_id', 'is_active', 'sort_order', 'updated_at'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get());
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
