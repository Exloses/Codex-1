<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VendorProductController extends Controller
{
    public function index(): Response
    {
        $vendor = $this->currentVendor();

        return Inertia::render('Vendor/Products/Index', [
            'products' => $vendor->products()
                ->select(['id', 'vendor_id', 'category_id', 'name', 'slug', 'selling_price', 'stock', 'is_active', 'updated_at'])
                ->with('category:id,name,slug')
                ->latest()
                ->paginate(20),
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

        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::query()->create($data);
            $this->syncVariantData($product, $request);

            return $product;
        });

        return redirect()->route('vendor.products.edit', $product);
    }

    public function show(Product $product): Response
    {
        $this->authorize('manage', $product);

        return Inertia::render('Vendor/Products/Edit', [
            'product' => $product->load([
                'category:id,name,slug',
                'variants:id,product_id,combination,sku,price,vendor_price,stock,image,updated_at',
                'attributes:id,product_id,name,sort_order',
                'attributes.values:id,attribute_id,value,color_hex,sort_order',
            ]),
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
        DB::transaction(function () use ($request, $product) {
            $product->update($this->productData($request, $product));
            $this->syncVariantData($product, $request);
        });

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
        $data = Arr::except($request->validated(), ['attributes', 'variants']);
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

    private function syncVariantData(Product $product, VendorProductRequest $request): void
    {
        if ($request->has('attributes')) {
            $product->attributes()->delete();

            foreach ($request->input('attributes', []) as $attributeIndex => $attributeData) {
                if (! filled($attributeData['name'] ?? null)) {
                    continue;
                }

                $attribute = ProductAttribute::query()->create([
                    'product_id' => $product->id,
                    'name' => $attributeData['name'],
                    'sort_order' => $attributeData['sort_order'] ?? $attributeIndex,
                ]);

                foreach ($attributeData['values'] ?? [] as $valueIndex => $valueData) {
                    if (! filled($valueData['value'] ?? null)) {
                        continue;
                    }

                    $attribute->values()->create([
                        'value' => $valueData['value'],
                        'color_hex' => $valueData['color_hex'] ?? null,
                        'sort_order' => $valueData['sort_order'] ?? $valueIndex,
                    ]);
                }
            }
        }

        if (! $request->has('variants')) {
            return;
        }

        $keptIds = [];
        $attributeNames = collect($request->input('attributes', []))
            ->pluck('name')
            ->filter()
            ->map(fn ($name) => (string) $name)
            ->values();

        foreach ($request->input('variants', []) as $variantData) {
            $combination = collect($variantData['combination'] ?? [])
                ->when($attributeNames->isNotEmpty(), fn ($values) => $values->only($attributeNames->all()))
                ->filter(fn ($value, $key) => filled($key) && filled($value))
                ->mapWithKeys(fn ($value, $key) => [(string) $key => (string) $value])
                ->sortKeys()
                ->all();

            if ($combination === []) {
                continue;
            }

            $payload = [
                'combination' => $combination,
                'sku' => $variantData['sku'] ?? null,
                'price' => $variantData['price'] ?? null,
                'vendor_price' => $variantData['vendor_price'] ?? null,
                'stock' => $variantData['stock'] ?? 0,
                'image' => $variantData['image'] ?? null,
            ];

            $variant = null;

            if (filled($variantData['id'] ?? null)) {
                $variant = $product->variants()->whereKey($variantData['id'])->first();
            }

            if ($variant) {
                $variant->update($payload);
            } else {
                $variant = $product->variants()->create($payload);
            }

            $keptIds[] = $variant->id;
        }

        ProductVariant::query()
            ->where('product_id', $product->id)
            ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
            ->delete();
    }
}
