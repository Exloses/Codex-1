<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrderAfterPayment;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_awards_idempotent_welcome_bonus(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'Loyal Buyer',
            'email' => 'loyal@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country' => 'US',
            'currency' => 'USD',
            'language' => 'en',
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'loyal@example.com')->firstOrFail();

        $this->assertSame(100, $user->loyaltyPoint->balance);
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => 100,
            'type' => 'bonus',
            'reference' => "user:{$user->id}:register_bonus",
        ]);

        app(LoyaltyService::class)->addBonusPoints($user, 'register');

        $this->assertSame(100, $user->loyaltyPoint()->first()->balance);
        $this->assertSame(1, LoyaltyTransaction::query()->where('reference', "user:{$user->id}:register_bonus")->count());
    }

    public function test_social_registration_awards_bonus_only_for_new_user(): void
    {
        Notification::fake();
        Role::query()->create(['name' => 'buyer', 'guard_name' => 'web']);
        $this->mockSocialiteUser();

        $this->get(route('social.callback', 'google'))->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'social.loyal@example.com')->firstOrFail();

        $this->assertSame(100, $user->loyaltyPoint->balance);

        auth()->logout();
        $this->mockSocialiteUser();

        $this->get(route('social.callback', 'google'))->assertRedirect(route('dashboard'));

        $this->assertSame(100, $user->fresh()->loyaltyPoint->balance);
        $this->assertSame(1, LoyaltyTransaction::query()->where('reference', "user:{$user->id}:register_bonus")->count());
    }

    public function test_review_bonus_is_awarded_once_per_review(): void
    {
        Notification::fake();
        $buyer = User::factory()->create();
        [$product] = $this->createCheckoutProduct();

        $this->actingAs($buyer)
            ->postJson(route('reviews.store'), [
                'product_id' => $product->id,
                'rating' => 5,
                'title' => 'Wonderful',
                'comment' => 'Exactly as expected.',
            ])
            ->assertCreated();

        $transaction = LoyaltyTransaction::query()->where('type', 'bonus')->firstOrFail();

        $this->assertSame(50, $buyer->fresh()->loyaltyPoint->balance);
        $this->assertStringStartsWith('review:', $transaction->reference);
        $this->assertStringEndsWith(':bonus', $transaction->reference);
        app(LoyaltyService::class)->addBonusPoints($buyer, str($transaction->reference)->beforeLast(':bonus')->toString());

        $this->assertSame(50, $buyer->fresh()->loyaltyPoint->balance);
        $this->assertSame(1, LoyaltyTransaction::query()->where('reference', $transaction->reference)->count());
    }

    public function test_paid_order_reward_is_idempotent_through_processing_job(): void
    {
        Notification::fake();
        Queue::fake();
        Mail::fake();

        $buyer = User::factory()->create();
        $order = $this->createPaidOrder($buyer, ['total_usd' => 62, 'payment_status' => 'unpaid']);

        (new ProcessOrderAfterPayment($order))->handle(
            app(\App\Services\DropshipService::class),
            app(\App\Services\AffiliateService::class),
            app(LoyaltyService::class),
        );
        app(LoyaltyService::class)->earnPoints($buyer, $order);

        $this->assertSame(620, $buyer->fresh()->loyaltyPoint->balance);
        $this->assertSame(1, LoyaltyTransaction::query()->where('reference', "order:{$order->id}:earned")->count());
    }

    public function test_redeem_requires_minimum_points_and_available_balance(): void
    {
        Notification::fake();
        $buyer = User::factory()->create();
        $service = app(LoyaltyService::class);
        $service->addBonusPoints($buyer, 'manual', 400);

        $this->assertSame(0.0, $service->redeemPoints($buyer, 400));
        $this->assertSame(400, $buyer->fresh()->loyaltyPoint->balance);

        $service->addBonusPoints($buyer, 'manual-top-up', 200);

        $this->assertSame(6.0, $service->redeemPoints($buyer, 900));
        $this->assertSame(0, $buyer->fresh()->loyaltyPoint->balance);
    }

    public function test_checkout_redemption_is_calculated_server_side(): void
    {
        Notification::fake();
        $buyer = User::factory()->create(['currency' => 'USD']);
        [$product, $variant] = $this->createCheckoutProduct(variantOverrides: ['price' => 49]);
        app(LoyaltyService::class)->addBonusPoints($buyer, 'checkout-balance', 1000);

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
                'discount_usd' => 999,
                'loyalty_points' => 700,
            ])
            ->assertRedirect();

        $order = Order::query()->firstOrFail();

        $this->assertSame('7.00', $order->discount_usd);
        $this->assertSame('54.00', $order->total_usd);
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'points' => -700,
            'reference' => "order:{$order->id}:redeem",
        ]);
    }

    public function test_checkout_redemption_cannot_exceed_balance_or_reduce_total_below_zero(): void
    {
        Notification::fake();
        $buyer = User::factory()->create(['currency' => 'USD']);
        [$product, $variant] = $this->createCheckoutProduct(variantOverrides: ['price' => 6]);
        app(LoyaltyService::class)->addBonusPoints($buyer, 'small-order-balance', 1000);

        CartItem::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($buyer)
            ->post(route('checkout.store'), [
                'payment_method' => 'stripe',
                'shipping_cost_usd' => 0,
                'buyer_currency' => 'USD',
                'loyalty_points' => 1000,
            ])
            ->assertRedirect();

        $order = Order::query()->firstOrFail();

        $this->assertSame('6.00', $order->discount_usd);
        $this->assertSame('0.00', $order->total_usd);
        $this->assertSame(400, $buyer->fresh()->loyaltyPoint->balance);
    }

    public function test_guest_checkout_cannot_redeem_loyalty_points(): void
    {
        [$product, $variant] = $this->createCheckoutProduct();

        $this->postJson(route('cart.store'), [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ])->assertCreated();

        $this->postJson(route('checkout.guest'), array_merge($this->guestCheckoutPayload(), [
            'loyalty_points' => 500,
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors('loyalty_points');
    }

    public function test_loyalty_pages_do_not_expose_vendor_financial_fields(): void
    {
        Notification::fake();
        $buyer = User::factory()->create();
        [$product, $variant] = $this->createCheckoutProduct();
        app(LoyaltyService::class)->addBonusPoints($buyer, 'privacy-check', 500);

        CartItem::query()->create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($buyer)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('loyalty', false)
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);

        $this->actingAs($buyer)
            ->get(route('account.loyalty'))
            ->assertOk()
            ->assertDontSee('vendor_price', false)
            ->assertDontSee('vendor_total_idr', false);
    }

    private function mockSocialiteUser(): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn((new SocialiteUser())->map([
                'id' => 'social-loyal-123',
                'nickname' => 'socialloyal',
                'name' => 'Social Loyal',
                'email' => 'social.loyal@example.com',
                'avatar' => null,
            ]));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);
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

    private function createPaidOrder(User $buyer, array $overrides = []): Order
    {
        [$product, $variant, $vendor] = $this->createCheckoutProduct();
        $order = Order::query()->create(array_merge([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-LOYAL-'.uniqid(),
            'status' => 'pending',
            'subtotal_usd' => 50,
            'shipping_cost_usd' => 12,
            'discount_usd' => 0,
            'total_usd' => 62,
            'buyer_currency' => 'USD',
            'exchange_rate' => 1,
            'total_buyer_currency' => 62,
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
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

    private function createCheckoutProduct(array $productOverrides = [], array $variantOverrides = []): array
    {
        $suffix = uniqid();
        $vendorUser = User::factory()->create();
        $vendor = Vendor::query()->create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Loyalty Vendor '.$suffix,
            'slug' => 'loyalty-vendor-'.$suffix,
            'is_approved' => true,
            'commission_rate' => 10,
            'balance_idr' => 0,
        ]);
        $category = Category::query()->create([
            'name' => 'Loyalty Category '.$suffix,
            'slug' => 'loyalty-category-'.$suffix,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product = Product::query()->create(array_merge([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Loyalty Product '.$suffix,
            'slug' => 'loyalty-product-'.$suffix,
            'description' => 'A product used to test loyalty points.',
            'vendor_price' => 25,
            'selling_price' => 49,
            'compare_price' => 69,
            'stock' => 10,
            'weight' => 1,
            'sku' => 'SKU-LOYALTY-'.$suffix,
            'is_active' => true,
            'is_featured' => true,
            'total_sales' => 0,
            'average_rating' => 0,
            'videos' => [],
        ], $productOverrides));
        $variant = ProductVariant::query()->create(array_merge([
            'product_id' => $product->id,
            'combination' => ['Size' => 'M'],
            'sku' => 'SKU-LOYALTY-'.$suffix.'-M',
            'price' => 56,
            'vendor_price' => 25,
            'stock' => 5,
        ], $variantOverrides));

        return [$product, $variant, $vendor];
    }
}
