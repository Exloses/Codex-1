<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_social_provider_redirects_to_login_with_error(): void
    {
        $response = $this->get('/auth/github/redirect');

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    public function test_google_and_facebook_social_redirect_routes_are_available(): void
    {
        foreach (['google', 'facebook'] as $providerName) {
            $provider = Mockery::mock();
            $provider->shouldReceive('redirect')
                ->once()
                ->andReturn(new RedirectResponse("https://example.com/oauth/{$providerName}"));

            Socialite::shouldReceive('driver')
                ->once()
                ->with($providerName)
                ->andReturn($provider);

            $this->get(route('social.redirect', $providerName))
                ->assertRedirect("https://example.com/oauth/{$providerName}");
        }
    }

    public function test_social_callback_creates_and_authenticates_new_buyer(): void
    {
        Role::query()->create(['name' => 'buyer', 'guard_name' => 'web']);

        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($this->socialiteUser());

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('social.callback', 'google'));

        $user = User::query()->where('email', 'buyer.social@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('buyer'));
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('Social Buyer', $user->name);
        $this->assertSame('US', $user->country);
        $this->assertSame('USD', $user->currency);
        $this->assertSame('en', $user->language);
        $this->assertTrue($user->is_active);
    }

    public function test_social_callback_marks_existing_user_email_as_verified(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'buyer.social@example.com',
        ]);

        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($this->socialiteUser());

        Socialite::shouldReceive('driver')
            ->once()
            ->with('facebook')
            ->andReturn($provider);

        $this->get(route('social.callback', 'facebook'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    private function socialiteUser(): SocialiteUser
    {
        return (new SocialiteUser())->map([
            'id' => 'provider-user-123',
            'nickname' => 'socialbuyer',
            'name' => 'Social Buyer',
            'email' => 'buyer.social@example.com',
            'avatar' => null,
        ]);
    }
}
