<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use App\Notifications\SupportTicketReplyNotification;
use App\Support\TawkSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_buyer_can_create_a_support_ticket(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $order = $this->createOrderFor($buyer);

        $this->actingAs($buyer)
            ->post(route('support.store'), [
                'subject' => 'Where is my package?',
                'message' => 'The tracking page has not updated yet.',
                'priority' => SupportTicket::PRIORITY_HIGH,
                'order_id' => $order->id,
            ])
            ->assertRedirect();

        $ticket = SupportTicket::query()->firstOrFail();

        $this->assertSame($buyer->id, $ticket->user_id);
        $this->assertSame($order->id, $ticket->order_id);
        $this->assertSame(SupportTicket::STATUS_OPEN, $ticket->status);
        $this->assertSame(SupportTicket::PRIORITY_HIGH, $ticket->priority);
        $this->assertMatchesRegularExpression('/^TCK-\d{8}-[A-Z0-9]{8}$/', $ticket->ticket_number);

        Notification::assertSentTo($buyer, SupportTicketCreatedNotification::class);
    }

    public function test_buyer_can_list_and_view_their_own_tickets(): void
    {
        $buyer = User::factory()->create();
        $other = User::factory()->create();

        $ownTicket = SupportTicket::query()->create([
            'user_id' => $buyer->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => 'Own support request',
            'message' => 'Please help with my order.',
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_NORMAL,
        ]);

        SupportTicket::query()->create([
            'user_id' => $other->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => 'Another buyer request',
            'message' => 'This should stay private.',
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_NORMAL,
        ]);

        $this->actingAs($buyer)
            ->get(route('support.index'))
            ->assertOk()
            ->assertSee('Own support request')
            ->assertDontSee('Another buyer request');

        $this->actingAs($buyer)
            ->get(route('support.show', $ownTicket))
            ->assertOk()
            ->assertSee('Please help with my order.');
    }

    public function test_buyer_cannot_view_or_reply_to_another_users_ticket(): void
    {
        $buyer = User::factory()->create();
        $other = User::factory()->create();
        $ticket = SupportTicket::query()->create([
            'user_id' => $other->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => 'Private request',
            'message' => 'Only the owner should see this.',
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_NORMAL,
        ]);

        $this->actingAs($buyer)
            ->get(route('support.show', $ticket))
            ->assertForbidden();

        $this->actingAs($buyer)
            ->post(route('support.reply', $ticket), ['message' => 'Trying to reply'])
            ->assertForbidden();
    }

    public function test_buyer_can_reply_to_their_own_ticket(): void
    {
        Notification::fake();

        $buyer = User::factory()->create();
        $ticket = SupportTicket::query()->create([
            'user_id' => $buyer->id,
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => 'Need help',
            'message' => 'Initial ticket message.',
            'status' => SupportTicket::STATUS_OPEN,
            'priority' => SupportTicket::PRIORITY_NORMAL,
        ]);

        $this->actingAs($buyer)
            ->post(route('support.reply', $ticket), ['message' => 'Adding more context.'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id' => $buyer->id,
            'message' => 'Adding more context.',
            'is_staff' => false,
        ]);

        Notification::assertNotSentTo($buyer, SupportTicketReplyNotification::class);
    }

    public function test_ticket_numbers_are_generated_uniquely_with_stable_format(): void
    {
        $numbers = collect(range(1, 20))
            ->map(fn () => SupportTicket::generateTicketNumber());

        $this->assertCount(20, $numbers->unique());

        $numbers->each(fn (string $number) => $this->assertMatchesRegularExpression('/^TCK-\d{8}-[A-Z0-9]{8}$/', $number));
    }

    public function test_tawk_settings_disable_missing_placeholder_and_test_values(): void
    {
        $this->assertFalse(TawkSettings::credentialsAreUsable(null, null));
        $this->assertFalse(TawkSettings::credentialsAreUsable('YOUR_TAWK_PROPERTY_ID', 'YOUR_TAWK_WIDGET_ID'));
        $this->assertFalse(TawkSettings::enabled('real-property-id', 'default'));
        $this->assertFalse(TawkSettings::publicConfig()['enabled']);
    }

    private function createOrderFor(User $buyer): Order
    {
        return Order::query()->create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-SUPPORT-'.str_pad((string) $buyer->id, 4, '0', STR_PAD_LEFT),
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
    }
}
