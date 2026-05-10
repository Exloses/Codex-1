<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = $this->query($request)->paginate(24)->withQueryString();

        return Inertia::render('Storefront/ProductIndex', [
            'products' => $products,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'filters' => $request->only(['q', 'category', 'sort']),
        ]);
    }

    public function show(string $slug): Response
    {
        $product = Product::query()
            ->select($this->columns())
            ->with(['category', 'sizeGuide', 'attributes.values', 'variants', 'reviews.user:id,name', 'questions.answers.user:id,name'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $product->variants->each->makeHidden('vendor_price');

        return Inertia::render('Storefront/ProductShow', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->select($this->columns())
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->where('is_active', true)
                ->limit(8)
                ->get(),
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
            ->with('category')
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
}
