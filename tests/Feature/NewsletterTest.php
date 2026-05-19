<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\NewsletterBroadcastNotification;
use App\Notifications\NewsletterWelcomeNotification;
use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_subscribe_with_normalized_email(): void
    {
        Notification::fake();

        $this->post(route('newsletter.subscribe'), [
            'email' => '  Buyer@Example.COM ',
        ])->assertRedirect()
            ->assertSessionHas('status', 'You are subscribed to GlobalDrop updates.');

        $subscriber = NewsletterSubscriber::query()->firstOrFail();

        $this->assertSame('buyer@example.com', $subscriber->email);
        $this->assertSame(NewsletterService::STATUS_ACTIVE, $subscriber->status);
        $this->assertNotEmpty($subscriber->token);
        $this->assertNotNull($subscriber->subscribed_at);
        Notification::assertSentTo($subscriber, NewsletterWelcomeNotification::class);
    }

    public function test_authenticated_user_can_subscribe_and_is_linked(): void
    {
        Notification::fake();
        $buyer = User::factory()->create(['email' => 'buyer@example.com']);

        $this->actingAs($buyer)
            ->post(route('newsletter.subscribe'), [
                'email' => 'buyer@example.com',
            ])->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'buyer@example.com',
            'user_id' => $buyer->id,
            'status' => NewsletterService::STATUS_ACTIVE,
        ]);
    }

    public function test_duplicate_active_email_does_not_create_another_row_or_resend_welcome(): void
    {
        Notification::fake();

        $this->post(route('newsletter.subscribe'), ['email' => 'same@example.com'])->assertRedirect();
        $this->post(route('newsletter.subscribe'), ['email' => 'SAME@example.com'])->assertRedirect()
            ->assertSessionHas('status', 'You are already subscribed to GlobalDrop updates.');

        $this->assertSame(1, NewsletterSubscriber::query()->where('email', 'same@example.com')->count());
        Notification::assertSentTimes(NewsletterWelcomeNotification::class, 1);
    }

    public function test_unsubscribed_email_can_resubscribe_with_new_token(): void
    {
        Notification::fake();
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'return@example.com',
            'status' => NewsletterService::STATUS_UNSUBSCRIBED,
            'token' => Str::random(64),
            'subscribed_at' => now()->subMonth(),
            'unsubscribed_at' => now()->subDay(),
        ]);
        $oldToken = $subscriber->token;

        $this->post(route('newsletter.subscribe'), ['email' => 'return@example.com'])->assertRedirect();

        $subscriber->refresh();

        $this->assertSame(NewsletterService::STATUS_ACTIVE, $subscriber->status);
        $this->assertNotSame($oldToken, $subscriber->token);
        $this->assertNull($subscriber->unsubscribed_at);
        Notification::assertSentTo($subscriber, NewsletterWelcomeNotification::class);
    }

    public function test_unsubscribe_token_marks_active_subscriber_unsubscribed(): void
    {
        Notification::fake();
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'leave@example.com',
            'status' => NewsletterService::STATUS_ACTIVE,
            'token' => Str::random(64),
            'subscribed_at' => now(),
        ]);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))
            ->assertOk()
            ->assertSee('Newsletter preferences updated', false);

        $this->assertSame(NewsletterService::STATUS_UNSUBSCRIBED, $subscriber->fresh()->status);
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_invalid_unsubscribe_token_is_safe_and_does_not_change_subscribers(): void
    {
        $subscriber = NewsletterSubscriber::query()->create([
            'email' => 'safe@example.com',
            'status' => NewsletterService::STATUS_ACTIVE,
            'token' => Str::random(64),
            'subscribed_at' => now(),
        ]);

        $this->get(route('newsletter.unsubscribe', Str::random(64)))
            ->assertNotFound()
            ->assertSee('Newsletter preferences updated', false)
            ->assertDontSee('safe@example.com', false);

        $this->assertSame(NewsletterService::STATUS_ACTIVE, $subscriber->fresh()->status);
    }

    public function test_welcome_json_response_does_not_expose_internal_subscriber_fields(): void
    {
        Notification::fake();

        $response = $this->postJson(route('newsletter.subscribe'), [
            'email' => 'privacy@example.com',
        ])->assertOk()
            ->assertJsonPath('subscribed', true);

        $payload = $response->getContent();

        $this->assertStringNotContainsString('token', $payload);
        $this->assertStringNotContainsString('user_id', $payload);
        $this->assertStringNotContainsString('vendor_price', $payload);
        $this->assertStringNotContainsString('vendor_total_idr', $payload);
    }

    public function test_newsletter_broadcast_only_sends_to_active_subscribers(): void
    {
        Notification::fake();
        $active = NewsletterSubscriber::query()->create([
            'email' => 'active@example.com',
            'status' => NewsletterService::STATUS_ACTIVE,
            'token' => Str::random(64),
            'subscribed_at' => now(),
        ]);
        $unsubscribed = NewsletterSubscriber::query()->create([
            'email' => 'quiet@example.com',
            'status' => NewsletterService::STATUS_UNSUBSCRIBED,
            'token' => Str::random(64),
            'subscribed_at' => now()->subMonth(),
            'unsubscribed_at' => now(),
        ]);

        $sent = app(NewsletterService::class)->broadcast('Spring drops', 'Fresh products are ready.');

        $this->assertSame(1, $sent);
        Notification::assertSentTo($active, NewsletterBroadcastNotification::class);
        Notification::assertNotSentTo($unsubscribed, NewsletterBroadcastNotification::class);
    }
}
