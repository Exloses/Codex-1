<?php

namespace Tests\Feature;

use App\Jobs\SendEmailJob;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_item_to_session_cart_and_checkout_without_login(): void
    {
        Queue::fake();

        [$product, $variant] = $this->createCheckoutProduct();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertCreated();

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertDontSee('vendor_price', false);

        $this->get(route('checkout.index'))
            ->assertOk()
            ->assertDontSee('vendor_price', false);

        $this->post(route('checkout.guest'), $this->guestCheckoutPayload())
            ->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();

        $this->assertNull($order->user_id);
        $this->assertSame('guest@example.com', $order->guest_email);
        $this->assertSame('Guest Buyer', $order->guest_name);
        $this->assertSame('555-0100', $order->guest_phone);
        $this->assertSame('123 Market Street', $order->guest_address_line1);
        $this->assertSame('US', $order->guest_country);
        $this->assertSame('124.00', $order->total_usd);
        $this->assertCount(1, $order->items);
        $this->assertSame(2, $order->items->first()->quantity);

        $this->assertNull(session('guest_cart'));
        Queue::assertPushed(SendEmailJob::class);
    }

    public function test_guest_can_track_order_with_order_number_and_email(): void
    {
        $order = Order::query()->create([
            'user_id' => null,
            'order_number' => 'ORD-TRACK-GUEST',
            'guest_email' => 'guest@example.com',
            'guest_name' => 'Guest Buyer',
            'status' => 'pending',
            'subtotal_usd' => 50,
            'shipping_cost_usd' => 12,
            'discount_usd' => 0,
            'total_usd' => 62,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 62,
            'payment_status' => 'unpaid',
        ]);

        $this->postJson(route('track.order'), [
            'order_number' => $order->order_number,
            'email' => 'guest@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('order.order_number', 'ORD-TRACK-GUEST');
    }

    public function test_wrong_guest_email_cannot_track_order(): void
    {
        $order = Order::query()->create([
            'user_id' => null,
            'order_number' => 'ORD-TRACK-LOCKED',
            'guest_email' => 'guest@example.com',
            'guest_name' => 'Guest Buyer',
            'status' => 'pending',
            'subtotal_usd' => 50,
            'shipping_cost_usd' => 12,
            'discount_usd' => 0,
            'total_usd' => 62,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 62,
            'payment_status' => 'unpaid',
        ]);

        $this->postJson(route('track.order'), [
            'order_number' => $order->order_number,
            'email' => 'wrong@example.com',
        ])->assertNotFound();
    }

    public function test_authenticated_checkout_still_uses_user_cart(): void
    {
        [$product, $variant] = $this->createCheckoutProduct();
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

        $order = Order::query()->firstOrFail();

        $this->assertSame($buyer->id, $order->user_id);
        $this->assertNull($order->guest_email);
        $this->assertDatabaseMissing('cart_items', ['user_id' => $buyer->id]);
    }

    private function guestCheckoutPayload(): array
    {
        return [
            'guest_name' => 'Guest Buyer',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '555-0100',
            'guest_address_line1' => '123 Market Street',
            'guest_address_line2' => 'Suite 5',
            'guest_city' => 'San Francisco',
            'guest_state' => 'CA',
            'guest_postal_code' => '94105',
            'guest_country' => 'US',
            'payment_method' => 'stripe',
            'shipping_cost_usd' => 12,
            'buyer_currency' => 'USD',
        ];
    }

    private function createCheckoutProduct(): array
    {
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Guest Checkout Vendor',
            'slug' => 'guest-checkout-vendor',
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Guest Checkout Category',
            'slug' => 'guest-checkout-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product = Product::query()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Guest Checkout Product',
            'slug' => 'guest-checkout-product',
            'description' => 'A product used to test guest checkout.',
            'vendor_price' => 25,
            'selling_price' => 49,
            'compare_price' => 69,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'SKU-GUEST-001',
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ]);
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Size' => 'M'],
            'sku' => 'SKU-GUEST-001-M',
            'price' => 56,
            'vendor_price' => 25,
            'stock' => 5,
        ]);

        return [$product, $variant];
    }
}
