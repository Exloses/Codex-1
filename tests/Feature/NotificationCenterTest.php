<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notification_center_endpoints(): void
    {
        $notification = $this->createNotification(User::factory()->create());

        $this->get(route('account.notifications'))
            ->assertRedirect(route('login'));

        $this->getJson(route('account.notifications.feed'))
            ->assertUnauthorized();

        $this->postJson(route('account.notifications.read', $notification))
            ->assertUnauthorized();

        $this->postJson(route('account.notifications.read-all'))
            ->assertUnauthorized();
    }

    public function test_authenticated_user_sees_only_their_own_notifications(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $ownNotification = $this->createNotification($buyer, ['title' => 'Your order shipped']);
        $this->createNotification($otherBuyer, ['title' => 'Other buyer update']);

        $this->actingAs($buyer)
            ->getJson(route('account.notifications.feed'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.id', $ownNotification->id)
            ->assertSee('Your order shipped')
            ->assertDontSee('Other buyer update');
    }

    public function test_feed_returns_latest_notifications_and_unread_count(): void
    {
        $buyer = User::factory()->create();
        $oldNotification = $this->createNotification($buyer, [
            'title' => 'Older notification',
        ], ['created_at' => now()->subHour()]);
        $newNotification = $this->createNotification($buyer, [
            'title' => 'Fresh notification',
        ], ['created_at' => now()]);
        $oldNotification->markAsRead();

        $this->actingAs($buyer)
            ->getJson(route('account.notifications.feed'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.id', $newNotification->id)
            ->assertJsonPath('notifications.0.title', 'Fresh notification')
            ->assertJsonPath('notifications.1.id', $oldNotification->id);
    }

    public function test_user_can_mark_one_own_notification_as_read(): void
    {
        $buyer = User::factory()->create();
        $notification = $this->createNotification($buyer);

        $this->actingAs($buyer)
            ->postJson(route('account.notifications.read', $notification))
            ->assertOk()
            ->assertJsonPath('unread_count', 0)
            ->assertJsonPath('notification.id', $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $notification = $this->createNotification($owner);

        $this->actingAs($otherBuyer)
            ->postJson(route('account.notifications.read', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_own_notifications_as_read(): void
    {
        $buyer = User::factory()->create();
        $otherBuyer = User::factory()->create();
        $this->createNotification($buyer, ['title' => 'First']);
        $this->createNotification($buyer, ['title' => 'Second']);
        $otherNotification = $this->createNotification($otherBuyer, ['title' => 'Other']);

        $this->actingAs($buyer)
            ->postJson(route('account.notifications.read-all'))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $buyer->fresh()->unreadNotifications()->count());
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_notification_account_page_renders_successfully(): void
    {
        $buyer = User::factory()->create();
        $this->createNotification($buyer, [
            'title' => 'Loyalty points earned',
            'message' => 'You earned 500 points.',
            'action_url' => '/account/loyalty-points',
        ]);

        $this->actingAs($buyer)
            ->get(route('account.notifications'))
            ->assertOk()
            ->assertSee('Loyalty points earned', false)
            ->assertSee('You earned 500 points.', false);
    }

    public function test_feed_payload_does_not_expose_vendor_internal_fields(): void
    {
        $buyer = User::factory()->create();
        $this->createNotification($buyer, [
            'title' => 'Order update',
            'message' => 'Your order has a new update.',
            'action_url' => 'https://malicious.example/orders/1',
            'vendor_price' => 12.34,
            'vendor_total_idr' => 200000,
            'product_variant' => ['vendor_price' => 10],
            'vendor' => ['bank_account' => '1234567890'],
        ]);

        $response = $this->actingAs($buyer)
            ->getJson(route('account.notifications.feed'))
            ->assertOk()
            ->assertJsonPath('notifications.0.action_url', null);

        $payload = $response->getContent();

        $this->assertStringNotContainsString('vendor_price', $payload);
        $this->assertStringNotContainsString('vendor_total_idr', $payload);
        $this->assertStringNotContainsString('bank_account', $payload);
        $this->assertStringNotContainsString('product_variant', $payload);
    }

    private function createNotification(User $user, array $data = [], array $overrides = []): DatabaseNotification
    {
        return $user->notifications()->create(array_merge([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\WelcomeNotification',
            'data' => array_replace([
                'type' => 'welcome',
                'title' => 'Welcome to GlobalDrop',
                'message' => 'Your account is ready.',
                'action_url' => '/account',
            ], $data),
            'read_at' => null,
        ], $overrides));
    }
}
