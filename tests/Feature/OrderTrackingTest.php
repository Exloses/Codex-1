<?php

namespace Tests\Feature;

use App\Enums\OrderTrackingSource;
use App\Enums\OrderTrackingStatus;
use App\Models\DropshipOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Services\OrderTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_event_can_be_created_for_order_and_status_is_validated(): void
    {
        $order = $this->createOrder();
        $service = app(OrderTrackingService::class);

        $event = $service->record($order, OrderTrackingStatus::Processing, [
            'source' => OrderTrackingSource::Admin,
            'location' => 'Jakarta fulfillment center',
        ]);

        $this->assertSame($order->id, $event->order_id);
        $this->assertSame(OrderTrackingStatus::Processing, $event->status);
        $this->assertSame(OrderTrackingSource::Admin, $event->source);
        $this->assertSame('processing', $order->fresh()->status);

        $this->expectException(\InvalidArgumentException::class);
        $service->record($order, 'not-a-real-status');
    }

    public function test_tracking_events_are_returned_chronologically(): void
    {
        $order = $this->createOrder();
        $service = app(OrderTrackingService::class);

        $service->record($order, OrderTrackingStatus::Shipped, ['occurred_at' => now()->addHour()]);
        $service->record($order, OrderTrackingStatus::Paid, ['occurred_at' => now()->subHour()]);

        $payload = $service->payload($order->fresh());

        $this->assertSame(['paid', 'shipped'], collect($payload['tracking_events'])->pluck('status')->all());
    }

    public function test_guest_can_view_tracking_with_matching_order_number_and_email_only(): void
    {
        $order = $this->createOrder(['guest_email' => 'guest@example.com', 'guest_name' => 'Guest Buyer']);
        app(OrderTrackingService::class)->record($order, OrderTrackingStatus::Paid);

        $this->postJson(route('track.order'), [
            'order_number' => $order->order_number,
            'email' => 'guest@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('order.order_number', $order->order_number)
            ->assertJsonPath('order.tracking_events.0.status', 'paid')
            ->assertDontSee('vendor_price', false);

        $this->postJson(route('track.order'), [
            'order_number' => $order->order_number,
            'email' => 'wrong@example.com',
        ])->assertNotFound();
    }

    public function test_logged_in_user_can_only_poll_their_own_order_tracking(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $order = $this->createOrder(['user_id' => $buyer->id, 'guest_email' => null]);
        $otherOrder = $this->createOrder(['user_id' => $otherBuyer->id, 'guest_email' => null]);

        app(OrderTrackingService::class)->record($order, OrderTrackingStatus::Shipped);

        $this->actingAs($buyer)
            ->getJson(route('account.orders.tracking', $order))
            ->assertOk()
            ->assertJsonPath('order.latest_tracking_status', 'shipped');

        $this->actingAs($buyer)
            ->getJson(route('account.orders.tracking', $otherOrder))
            ->assertForbidden();
    }

    public function test_vendor_can_update_only_their_own_dropship_order_tracking(): void
    {
        Notification::fake();
        Role::findOrCreate('vendor');

        [$vendorUser, $vendor, $dropshipOrder] = $this->createVendorDropshipOrder();
        [, , $otherDropshipOrder] = $this->createVendorDropshipOrder(['dropship_number' => 'DS-OTHER']);

        $vendorUser->assignRole('vendor');

        $this->actingAs($vendorUser)
            ->post(route('vendor.orders.tracking.store', $dropshipOrder), [
                'status' => 'shipped',
                'tracking_number' => 'TRACK-123',
                'carrier' => 'DHL',
                'location' => 'Jakarta',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_tracking_events', [
            'dropship_order_id' => $dropshipOrder->id,
            'status' => 'shipped',
            'source' => 'vendor',
            'location' => 'Jakarta',
        ]);
        $this->assertSame('shipped', $dropshipOrder->fresh()->status);
        $this->assertSame('TRACK-123', $dropshipOrder->fresh()->tracking_number);
        $this->assertSame($vendor->id, $dropshipOrder->vendor_id);

        $this->actingAs($vendorUser)
            ->post(route('vendor.orders.tracking.store', $otherDropshipOrder), [
                'status' => 'delivered',
            ])
            ->assertForbidden();
    }

    public function test_tracking_polling_endpoint_uses_guest_order_number_and_email(): void
    {
        $order = $this->createOrder(['guest_email' => 'guest@example.com']);
        app(OrderTrackingService::class)->record($order, OrderTrackingStatus::InTransit);

        $this->postJson(route('track.status'), [
            'order_number' => $order->order_number,
            'email' => 'guest@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('order.latest_tracking_status', 'in_transit');

        $this->postJson(route('track.status'), [
            'order_number' => $order->order_number,
            'email' => 'wrong@example.com',
        ])->assertNotFound();
    }

    private function createOrder(array $overrides = []): Order
    {
        return Order::query()->create(array_merge([
            'user_id' => null,
            'order_number' => 'ORD-TRACK-'.uniqid(),
            'guest_email' => 'buyer@example.com',
            'guest_name' => 'Buyer',
            'status' => 'pending',
            'subtotal_usd' => 50,
            'shipping_cost_usd' => 12,
            'discount_usd' => 0,
            'total_usd' => 62,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 62,
            'payment_status' => 'unpaid',
        ], $overrides));
    }

    private function createVendorDropshipOrder(array $overrides = []): array
    {
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Tracking Vendor '.uniqid(),
            'slug' => 'tracking-vendor-'.uniqid(),
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $order = $this->createOrder(['order_number' => 'ORD-DS-'.uniqid()]);
        $dropshipOrder = DropshipOrder::query()->create(array_merge([
            'order_id' => $order->id,
            'vendor_id' => $vendor->id,
            'dropship_number' => 'DS-'.uniqid(),
            'status' => 'pending',
            'vendor_total_idr' => 100000,
            'is_paid_to_vendor' => false,
        ], $overrides));

        return [$vendorUser, $vendor, $dropshipOrder];
    }
}
