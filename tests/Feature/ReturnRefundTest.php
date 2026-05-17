<?php

namespace Tests\Feature;

use App\Enums\ReturnRequestStatus;
use App\Models\Category;
use App\Models\DropshipOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\ReturnRequestUpdateNotification;
use App\Services\ReturnRefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReturnRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_create_return_for_own_paid_shipped_order_with_images(): void
    {
        Notification::fake();
        Storage::fake('public');

        $buyer = User::factory()->create();
        $order = $this->createReturnableOrder($buyer, ['status' => 'shipped']);

        $response = $this->actingAs($buyer)->postJson(route('returns.store'), [
            'order_id' => $order->id,
            'reason' => 'damaged',
            'description' => 'The package arrived damaged.',
            'refund_method' => 'original_payment',
            'images' => [UploadedFile::fake()->image('damage.jpg', 900, 900)->size(512)],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('return.status', 'pending')
            ->assertJsonPath('return.order.order_number', $order->order_number)
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);

        $returnRequest = ReturnRequest::query()->firstOrFail();

        $this->assertSame($buyer->id, $returnRequest->user_id);
        $this->assertSame($order->id, $returnRequest->order_id);
        $this->assertCount(1, $returnRequest->images);
        $this->assertStringStartsWith('returns/', $returnRequest->images[0]);
        Storage::disk('public')->assertExists($returnRequest->images[0]);

        Notification::assertSentTo($buyer, ReturnRequestUpdateNotification::class);
    }

    public function test_buyer_cannot_create_return_for_someone_elses_order(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $order = $this->createReturnableOrder($otherBuyer, ['status' => 'delivered']);

        $this->actingAs($buyer)
            ->postJson(route('returns.store'), [
                'order_id' => $order->id,
                'reason' => 'wrong_item',
                'description' => 'Wrong item.',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('order_id');

        $this->assertDatabaseCount('return_requests', 0);
    }

    public function test_unpaid_pending_and_cancelled_orders_cannot_be_returned(): void
    {
        $buyer = User::factory()->create();

        $blockedOrders = [
            $this->createReturnableOrder($buyer, ['status' => 'pending', 'payment_status' => 'paid']),
            $this->createReturnableOrder($buyer, ['status' => 'cancelled', 'payment_status' => 'paid']),
            $this->createReturnableOrder($buyer, ['status' => 'delivered', 'payment_status' => 'unpaid']),
        ];

        foreach ($blockedOrders as $order) {
            $this->actingAs($buyer)
                ->postJson(route('returns.store'), [
                    'order_id' => $order->id,
                    'reason' => 'other',
                    'description' => 'Return request.',
                ])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('order_id');
        }

        $this->assertDatabaseCount('return_requests', 0);
    }

    public function test_duplicate_active_return_request_is_rejected(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createReturnableOrder($buyer, ['status' => 'delivered']);

        ReturnRequest::query()->create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'return_number' => 'RET-DUPLICATE',
            'reason' => 'damaged',
            'description' => 'Already opened.',
            'status' => ReturnRequestStatus::Pending,
        ]);

        $this->actingAs($buyer)
            ->postJson(route('returns.store'), [
                'order_id' => $order->id,
                'reason' => 'damaged',
                'description' => 'Duplicate request.',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('order_id');

        $this->assertDatabaseCount('return_requests', 1);
    }

    public function test_buyer_can_view_own_return_but_not_another_users_return(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $ownReturn = $this->createReturnRequest($buyer);
        $otherReturn = $this->createReturnRequest($otherBuyer);

        $this->actingAs($buyer)
            ->get(route('returns.show', $ownReturn))
            ->assertOk()
            ->assertSee($ownReturn->return_number, false)
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);

        $this->actingAs($buyer)
            ->get(route('returns.show', $otherReturn))
            ->assertForbidden();
    }

    public function test_admin_can_approve_reject_and_process_safe_refund(): void
    {
        Notification::fake();
        Role::findOrCreate('admin');

        $buyer = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $returnRequest = $this->createReturnRequest($buyer, [
            'status' => ReturnRequestStatus::UnderReview,
        ], [
            'payment_method' => 'stripe',
            'stripe_payment_id' => 'pi_local_placeholder',
        ]);

        $this->assertTrue($admin->can('update', $returnRequest));
        $this->assertTrue($admin->can('processRefund', $returnRequest));

        $service = app(ReturnRefundService::class);
        $service->approve($returnRequest, 25.50, 'Approved after photo review.');
        $returnRequest->refresh();

        $this->assertSame(ReturnRequestStatus::Approved, $returnRequest->status);
        $this->assertSame('25.50', $returnRequest->refund_amount_usd);

        $service->processRefund($returnRequest, 'Refund processed locally.');
        $returnRequest->refresh();

        $this->assertSame(ReturnRequestStatus::Refunded, $returnRequest->status);
        $this->assertStringStartsWith('LOCAL-STRIPE-', $returnRequest->refund_reference);
        $this->assertNotNull($returnRequest->refund_processed_at);
        $this->assertSame('returned', $returnRequest->order->fresh()->status);

        $rejectedReturn = $this->createReturnRequest($buyer, [
            'return_number' => 'RET-REJECT',
        ]);

        $service->reject($rejectedReturn, 'Not eligible after inspection.');
        $this->assertSame(ReturnRequestStatus::Rejected, $rejectedReturn->fresh()->status);
        $this->assertNotNull($rejectedReturn->fresh()->resolved_at);

        Notification::assertSentTo($buyer, ReturnRequestUpdateNotification::class);
    }

    public function test_customer_return_and_order_payloads_do_not_expose_vendor_financial_fields(): void
    {
        $buyer = User::factory()->create();
        $order = $this->createReturnableOrder($buyer, ['status' => 'delivered']);
        $returnRequest = ReturnRequest::query()->create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'return_number' => 'RET-PRIVATE',
            'reason' => 'quality_issue',
            'description' => 'Quality issue.',
            'status' => ReturnRequestStatus::Pending,
        ]);

        DropshipOrder::query()->create([
            'order_id' => $order->id,
            'vendor_id' => $order->items()->first()->vendor_id,
            'dropship_number' => 'DS-PRIVATE',
            'status' => 'shipped',
            'vendor_total_idr' => 999999,
            'is_paid_to_vendor' => false,
        ]);

        $this->actingAs($buyer)
            ->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee($returnRequest->return_number, false)
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);

        $this->actingAs($buyer)
            ->get(route('returns.show', $returnRequest))
            ->assertOk()
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);
    }

    private function createReturnRequest(User $buyer, array $returnOverrides = [], array $orderOverrides = []): ReturnRequest
    {
        $order = $this->createReturnableOrder($buyer, array_merge(['status' => 'delivered'], $orderOverrides));

        return ReturnRequest::query()->create(array_merge([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'return_number' => 'RET-'.uniqid(),
            'reason' => 'damaged',
            'description' => 'Return request description.',
            'status' => ReturnRequestStatus::Pending,
            'refund_method' => 'original_payment',
        ], $returnOverrides));
    }

    private function createReturnableOrder(User $buyer, array $overrides = []): Order
    {
        [$product, $variant, $vendor] = $this->createProductWithVariant();

        $order = Order::query()->create(array_merge([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-RET-'.uniqid(),
            'status' => 'delivered',
            'subtotal_usd' => 50,
            'shipping_cost_usd' => 10,
            'discount_usd' => 0,
            'total_usd' => 60,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 60,
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'stripe_payment_id' => 'pi_local_placeholder',
        ], $overrides));

        $order->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'vendor_id' => $vendor->id,
            'quantity' => 1,
            'price_usd' => 50,
            'subtotal_usd' => 50,
        ]);

        return $order;
    }

    private function createProductWithVariant(): array
    {
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Return Vendor '.uniqid(),
            'slug' => 'return-vendor-'.uniqid(),
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);

        $category = Category::query()->create([
            'name' => 'Return Category '.uniqid(),
            'slug' => 'return-category-'.uniqid(),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Return Product '.uniqid(),
            'slug' => 'return-product-'.uniqid(),
            'description' => 'Return test product.',
            'vendor_price' => 25,
            'selling_price' => 50,
            'compare_price' => 60,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'RET-SKU-'.uniqid(),
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'combination' => ['Color' => 'Black', 'Size' => 'M'],
            'sku' => 'RET-VAR-'.uniqid(),
            'price' => 50,
            'vendor_price' => 20,
            'stock' => 5,
        ]);

        return [$product, $variant, $vendor];
    }
}
