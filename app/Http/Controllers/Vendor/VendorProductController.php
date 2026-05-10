<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VendorProductController extends Controller
{
    public function index(): Response
    {
        $vendor = $this->currentVendor();

        return Inertia::render('Vendor/Products/Index', [
            'products' => $vendor->products()->with('category:id,name,slug')->latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Vendor/Products/Create', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(VendorProductRequest $request)
    {
        $vendor = $this->currentVendor();
        $data = $this->productData($request);
        $data['vendor_id'] = $vendor->id;

        $product = Product::query()->create($data);

        return redirect()->route('vendor.products.edit', $product);
    }

    public function show(Product $product): Response
    {
        $this->authorize('manage', $product);

        return Inertia::render('Vendor/Products/Edit', [
            'product' => $product->load('category', 'variants', 'attributes.values'),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function edit(Product $product): Response
    {
        return $this->show($product);
    }

    public function update(VendorProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $product->update($this->productData($request, $product));

        return redirect()->route('vendor.products.edit', $product);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()->route('vendor.products.index');
    }

    private function currentVendor()
    {
        $vendor = auth()->user()->vendor;
        abort_if(! $vendor, 403, 'Vendor profile not found.');
        $this->authorize('manage', $vendor);

        return $vendor;
    }

    private function productData(VendorProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();
        $name = $data['name'];
        $base = $data['slug'] ?? Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Product::query()
            ->where('slug', $slug)
            ->when($product, fn ($query) => $query->whereKeyNot($product->id))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        $data['slug'] = $slug;
        $data['description'] = $data['description'] ?? '';
        $data['weight'] = $data['weight'] ?? 0;
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return $data;
    }
}
