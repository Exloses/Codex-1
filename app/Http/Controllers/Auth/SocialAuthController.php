<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED_PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        $provider = strtolower($provider);

        if (! $this->isSupportedProvider($provider)) {
            return $this->redirectToLoginWithError();
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider, LoyaltyService $loyaltyService): RedirectResponse
    {
        $provider = strtolower($provider);

        if (! $this->isSupportedProvider($provider)) {
            return $this->redirectToLoginWithError();
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            Log::warning('Social login failed.', [
                'provider' => $provider,
                'exception' => $exception::class,
            ]);

            return $this->redirectToLoginWithError();
        }

        $email = $socialUser->getEmail();

        if (! $email) {
            return $this->redirectToLoginWithError('We could not read an email address from that social account.');
        }

        $user = User::query()->where('email', $email)->first();
        $isNewUser = false;

        if (! $user) {
            $user = User::query()->create([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Social User',
                'email' => $email,
                'password' => Hash::make(Str::random(48)),
                'country' => 'US',
                'currency' => 'USD',
                'language' => 'en',
                'is_active' => true,
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $isNewUser = true;
        } elseif (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        if ($isNewUser && $buyerRole = Role::query()->where('name', 'buyer')->where('guard_name', 'web')->first()) {
            $user->assignRole($buyerRole);
        }

        if ($isNewUser) {
            $loyaltyService->addBonusPoints($user, 'register');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function isSupportedProvider(string $provider): bool
    {
        return in_array(strtolower($provider), self::SUPPORTED_PROVIDERS, true);
    }

    private function redirectToLoginWithError(string $message = 'Social login is unavailable. Please try again or use email and password.'): RedirectResponse
    {
        return redirect()->route('login')->with('error', $message);
    }
}
