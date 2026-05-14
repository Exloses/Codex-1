<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\StorefrontCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StorefrontPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_cached_storefront_pages_still_render(): void
    {
        $product = $this->createStorefrontProduct();

        Banner::query()->create([
            'title' => 'Spring Marketplace',
            'image' => 'https://example.com/banner.jpg',
            'link' => '/products',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Faq::query()->create([
            'category' => 'Orders',
            'question' => 'How do I track an order?',
            'answer' => 'Use the order tracking page.',
            'language' => 'en',
            'is_active' => true,
        ]);

        $this->get(route('home'))->assertOk();
        $this->get(route('home'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.show', $product->slug))->assertOk();
        $this->get(route('faq.index'))->assertOk();
        $this->getJson(route('api.currency.rates', ['amount' => 2, 'currency' => 'IDR']))
            ->assertOk()
            ->assertJsonPath('base', 'USD');

        $this->assertTrue(Cache::has(StorefrontCache::HOME));
        $this->assertTrue(Cache::has(StorefrontCache::CATEGORIES));
    }

    public function test_storefront_cache_versions_change_when_public_data_changes(): void
    {
        $product = $this->createStorefrontProduct();
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Size' => 'M'],
            'sku' => 'SKU-PERF-001-M',
            'price' => 49,
            'vendor_price' => 25,
            'stock' => 5,
        ]);

        $beforeProductKey = StorefrontCache::productShowKey($product->slug);
        $this->get(route('products.show', $product->slug))->assertOk();

        $product->update(['selling_price' => 59]);
        $afterProductKey = StorefrontCache::productShowKey($product->slug);

        $this->assertNotSame($beforeProductKey, $afterProductKey);

        $beforeVariantKey = StorefrontCache::productShowKey($product->slug);
        $variant->update(['stock' => 3]);
        $afterVariantKey = StorefrontCache::productShowKey($product->slug);

        $this->assertNotSame($beforeVariantKey, $afterVariantKey);

        $beforeCategoryKey = StorefrontCache::categoryProductsKey($product->category->slug, 1);
        $product->category->update(['sort_order' => 9]);
        $afterCategoryKey = StorefrontCache::categoryProductsKey($product->category->slug, 1);

        $this->assertNotSame($beforeCategoryKey, $afterCategoryKey);

        $faq = Faq::query()->create([
            'category' => 'Returns',
            'question' => 'Can I return this?',
            'answer' => 'Returns are reviewed case by case.',
            'language' => 'en',
            'is_active' => true,
        ]);

        $beforeFaqKey = StorefrontCache::faqKey(request());
        $faq->update(['answer' => 'Returns are reviewed by support.']);
        $afterFaqKey = StorefrontCache::faqKey(request());

        $this->assertNotSame($beforeFaqKey, $afterFaqKey);
    }

    private function createStorefrontProduct(): Product
    {
        $user = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $user->id,
            'store_name' => 'Performance Vendor',
            'slug' => 'performance-vendor',
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Performance Category',
            'slug' => 'performance-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Product::query()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Cached Product',
            'slug' => 'cached-product',
            'description' => 'A product used to verify cached storefront rendering.',
            'vendor_price' => 25,
            'selling_price' => 49,
            'compare_price' => 69,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'SKU-PERF-001',
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ]);
    }
}
