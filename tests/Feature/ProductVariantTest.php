<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_returns_variant_attributes_and_safe_variant_data(): void
    {
        [$product, $variant] = $this->createVariantProduct();

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('Color', false)
            ->assertSee('Red', false)
            ->assertSee($variant->sku, false)
            ->assertSee('59.00', false)
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('22.00', false);
    }

    public function test_cannot_add_variant_product_to_cart_without_variant_selection(): void
    {
        [$product] = $this->createVariantProduct();

        $this->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_variant_id');
    }

    public function test_cannot_add_variant_that_belongs_to_another_product(): void
    {
        [$product] = $this->createVariantProduct(['slug' => 'variant-product-a']);
        [, $otherVariant] = $this->createVariantProduct(['slug' => 'variant-product-b'], ['sku' => 'OTHER-VARIANT']);

        $this->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $otherVariant->id,
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_variant_id');
    }

    public function test_authenticated_buyer_can_add_selected_variant_to_cart(): void
    {
        [$product, $variant] = $this->createVariantProduct();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->postJson(route('cart.store'), [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'quantity' => 2,
            ])
            ->assertCreated()
            ->assertJsonPath('item.product_variant_id', $variant->id);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    public function test_guest_can_add_selected_variant_to_session_cart(): void
    {
        [$product, $variant] = $this->createVariantProduct();

        $this->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ])
            ->assertCreated()
            ->assertJsonPath('item.product_variant_id', $variant->id);

        $this->assertSame($variant->id, session('guest_cart')[$product->id.'-'.$variant->id]['product_variant_id']);
    }

    public function test_cart_displays_and_preserves_variant_selection(): void
    {
        [$product, $variant] = $this->createVariantProduct();
        $buyer = User::factory()->create();

        CartItem::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($buyer)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Red', false)
            ->assertSee('XL', false)
            ->assertDontSee('vendor_price', false);
    }

    public function test_checkout_creates_order_item_with_selected_product_variant(): void
    {
        [$product, $variant] = $this->createVariantProduct();
        $buyer = User::factory()->create(['currency' => 'USD']);

        CartItem::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($buyer)
            ->post(route('checkout.store'), [
                'payment_method' => 'stripe',
                'shipping_cost_usd' => 12,
                'buyer_currency' => 'USD',
            ])
            ->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();
        $item = $order->items->first();

        $this->assertSame($variant->id, $item->product_variant_id);
        $this->assertSame('59.00', $item->price_usd);
        $this->assertSame('71.00', $order->total_usd);
    }

    public function test_quantity_cannot_exceed_variant_stock(): void
    {
        [$product, $variant] = $this->createVariantProduct(variantOverrides: ['stock' => 1]);

        $this->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');
    }

    private function createVariantProduct(array $productOverrides = [], array $variantOverrides = []): array
    {
        $suffix = uniqid();
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Variant Vendor '.$suffix,
            'slug' => 'variant-vendor-'.$suffix,
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Variant Category '.$suffix,
            'slug' => 'variant-category-'.$suffix,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product = Product::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Variant Product '.$suffix,
            'slug' => 'variant-product-'.$suffix,
            'description' => 'A product with selectable variants.',
            'vendor_price' => 25,
            'selling_price' => 49,
            'compare_price' => 69,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'SKU-VARIANT-'.$suffix,
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ], $productOverrides));

        $color = ProductAttribute::query()->create([
            'product_id' => $product->id,
            'name' => 'Color',
            'sort_order' => 0,
        ]);
        ProductAttributeValue::query()->create([
            'attribute_id' => $color->id,
            'value' => 'Red',
            'color_hex' => '#DC2626',
            'sort_order' => 0,
        ]);
        $size = ProductAttribute::query()->create([
            'product_id' => $product->id,
            'name' => 'Size',
            'sort_order' => 1,
        ]);
        ProductAttributeValue::query()->create([
            'attribute_id' => $size->id,
            'value' => 'XL',
            'sort_order' => 0,
        ]);

        $variant = ProductVariant::query()->create(array_merge([
            'product_id' => $product->id,
            'combination' => ['Color' => 'Red', 'Size' => 'XL'],
            'sku' => 'SKU-VARIANT-RED-XL-'.$suffix,
            'price' => 59,
            'vendor_price' => 22,
            'stock' => 3,
            'image' => 'https://example.com/red-xl.jpg',
        ], $variantOverrides));

        return [$product, $variant];
    }
}
