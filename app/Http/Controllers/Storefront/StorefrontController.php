<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Storefront/Home', [
            'banners' => Banner::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'featuredProducts' => Product::query()
                ->select($this->productColumns())
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->limit(12)
                ->get(),
        ]);
    }

    private function productColumns(): array
    {
        return ['id', 'vendor_id', 'category_id', 'name', 'name_id', 'slug', 'description', 'description_id', 'selling_price', 'compare_price', 'stock', 'weight', 'sku', 'size_guide_id', 'is_active', 'is_featured', 'total_sales', 'average_rating', 'videos', 'created_at', 'updated_at'];
    }
}
