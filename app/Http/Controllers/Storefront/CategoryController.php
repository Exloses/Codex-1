<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function show(string $slug): Response
    {
        $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return Inertia::render('Storefront/ProductIndex', [
            'category' => $category,
            'products' => Product::query()
                ->select(['id', 'vendor_id', 'category_id', 'name', 'name_id', 'slug', 'description', 'description_id', 'selling_price', 'compare_price', 'stock', 'weight', 'sku', 'size_guide_id', 'is_active', 'is_featured', 'total_sales', 'average_rating', 'videos', 'created_at', 'updated_at'])
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->paginate(24),
        ]);
    }
}
