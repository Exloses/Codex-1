<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockNotification;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\PriceDropNotification;
use App\Notifications\StockAvailableNotification;
use App\Services\ProductAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StockPriceAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_stock_notification_with_email_for_out_of_stock_product(): void
    {
        $product = $this->createProduct(['stock' => 0]);

        $response = $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'guest_email' => '  Buyer@Example.COM ',
        ])->assertCreated()
            ->assertJsonPath('alert.type', ProductAlertService::TYPE_STOCK)
            ->assertJsonPath('alert.status', 'active');

        $payload = $response->getContent();
        $this->assertStringNotContainsString('guest_email', $payload);
        $this->assertStringNotContainsString('user_id', $payload);
        $this->assertStringNotContainsString('vendor_price', $payload);
        $this->assertStringNotContainsString('vendor_total_idr', $payload);

        $this->assertDatabaseHas('stock_notifications', [
            'product_id' => $product->id,
            'guest_email' => 'buyer@example.com',
            'type' => ProductAlertService::TYPE_STOCK,
            'is_notified' => false,
        ]);
    }

    public function test_logged_in_user_can_create_stock_notification_without_guest_email(): void
    {
        $buyer = User::factory()->create(['email' => 'buyer@example.com']);
        $product = $this->createProduct(['stock' => 0]);

        $this->actingAs($buyer)
            ->postJson(route('notifications.stock.store'), [
                'product_id' => $product->id,
            ])->assertCreated()
            ->assertJsonPath('alert.type', ProductAlertService::TYPE_STOCK);

        $this->assertDatabaseHas('stock_notifications', [
            'user_id' => $buyer->id,
            'guest_email' => null,
            'product_id' => $product->id,
            'type' => ProductAlertService::TYPE_STOCK,
        ]);
    }

    public function test_cannot_create_stock_notification_for_in_stock_product_or_variant(): void
    {
        $product = $this->createProduct(['stock' => 5]);

        $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'guest_email' => 'buyer@example.com',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Color' => 'Red'],
            'sku' => 'IN-STOCK-VARIANT',
            'price' => 39,
            'vendor_price' => 20,
            'stock' => 2,
        ]);

        $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'guest_email' => 'buyer@example.com',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');
    }

    public function test_guest_and_logged_in_user_can_create_price_alert_below_current_price(): void
    {
        $product = $this->createProduct(['selling_price' => 50]);
        $buyer = User::factory()->create();

        $this->postJson(route('notifications.price-alert.store'), [
            'product_id' => $product->id,
            'guest_email' => 'guest@example.com',
            'target_price_usd' => 45,
        ])->assertCreated()
            ->assertJsonPath('alert.type', ProductAlertService::TYPE_PRICE);

        $this->actingAs($buyer)
            ->postJson(route('notifications.price-alert.store'), [
                'product_id' => $product->id,
                'target_price_usd' => 40,
            ])->assertCreated()
            ->assertJsonPath('alert.type', ProductAlertService::TYPE_PRICE);

        $this->assertDatabaseHas('stock_notifications', [
            'guest_email' => 'guest@example.com',
            'target_price_usd' => 45,
            'type' => ProductAlertService::TYPE_PRICE,
        ]);
        $this->assertDatabaseHas('stock_notifications', [
            'user_id' => $buyer->id,
            'guest_email' => null,
            'target_price_usd' => 40,
            'type' => ProductAlertService::TYPE_PRICE,
        ]);
    }

    public function test_cannot_create_price_alert_at_or_above_current_price(): void
    {
        $product = $this->createProduct(['selling_price' => 50]);

        $this->postJson(route('notifications.price-alert.store'), [
            'product_id' => $product->id,
            'guest_email' => 'buyer@example.com',
            'target_price_usd' => 50,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('target_price_usd');
    }

    public function test_variant_must_belong_to_the_selected_product(): void
    {
        $product = $this->createProduct(['slug' => 'alert-product-a', 'stock' => 0]);
        $otherProduct = $this->createProduct(['slug' => 'alert-product-b', 'stock' => 0]);
        $otherVariant = ProductVariant::query()->create([
            'product_id' => $otherProduct->id,
            'combination' => ['Color' => 'Blue'],
            'sku' => 'OTHER-ALERT-VARIANT',
            'price' => 25,
            'vendor_price' => 10,
            'stock' => 0,
        ]);

        $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $otherVariant->id,
            'guest_email' => 'buyer@example.com',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('product_variant_id');
    }

    public function test_duplicate_alert_updates_existing_row_and_reactivates_notified_alert(): void
    {
        $product = $this->createProduct(['stock' => 0, 'selling_price' => 50]);

        $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'guest_email' => 'same@example.com',
        ])->assertCreated();

        $existing = StockNotification::query()->firstOrFail();
        $existing->forceFill(['is_notified' => true])->save();

        $this->postJson(route('notifications.stock.store'), [
            'product_id' => $product->id,
            'guest_email' => 'SAME@example.com',
        ])->assertOk();

        $this->assertSame(1, StockNotification::query()->count());
        $this->assertFalse($existing->fresh()->is_notified);

        $this->postJson(route('notifications.price-alert.store'), [
            'product_id' => $product->id,
            'guest_email' => 'same@example.com',
            'target_price_usd' => 45,
        ])->assertCreated();
        $this->postJson(route('notifications.price-alert.store'), [
            'product_id' => $product->id,
            'guest_email' => 'same@example.com',
            'target_price_usd' => 40,
        ])->assertOk();

        $this->assertSame(2, StockNotification::query()->count());
        $this->assertDatabaseHas('stock_notifications', [
            'type' => ProductAlertService::TYPE_PRICE,
            'target_price_usd' => 40,
        ]);
    }

    public function test_stock_command_sends_notification_and_marks_row_notified(): void
    {
        Notification::fake();
        $buyer = User::factory()->create();
        $product = $this->createProduct(['stock' => 3]);
        $alert = StockNotification::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'type' => ProductAlertService::TYPE_STOCK,
            'is_notified' => false,
        ]);

        $this->artisan('notifications:check-stock')
            ->expectsOutput('Checked 1 stock alert(s); sent 1; skipped 0.')
            ->assertExitCode(0);

        $this->assertTrue($alert->fresh()->is_notified);
        Notification::assertSentTo($buyer, StockAvailableNotification::class);
    }

    public function test_price_command_sends_guest_notification_and_marks_row_notified(): void
    {
        Notification::fake();
        $product = $this->createProduct(['selling_price' => 20]);
        $alert = StockNotification::query()->create([
            'guest_email' => 'guest@example.com',
            'product_id' => $product->id,
            'type' => ProductAlertService::TYPE_PRICE,
            'target_price_usd' => 25,
            'is_notified' => false,
        ]);

        $this->artisan('notifications:check-price-drops')
            ->expectsOutput('Checked 1 price alert(s); sent 1; skipped 0.')
            ->assertExitCode(0);

        $this->assertTrue($alert->fresh()->is_notified);
        Notification::assertSentOnDemand(PriceDropNotification::class);
    }

    private function createProduct(array $overrides = []): Product
    {
        $suffix = uniqid();
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Alert Vendor '.$suffix,
            'slug' => 'alert-vendor-'.$suffix,
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Alert Category '.$suffix,
            'slug' => 'alert-category-'.$suffix,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Product::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Alert Product '.$suffix,
            'slug' => 'alert-product-'.$suffix,
            'description' => 'A product used to validate stock and price alerts.',
            'vendor_price' => 15,
            'selling_price' => 35,
            'compare_price' => 45,
            'stock' => 0,
            'weight' => 1,
            'sku' => 'ALERT-SKU-'.$suffix,
            'is_active' => true,
            'is_featured' => false,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ], $overrides));
    }
}
