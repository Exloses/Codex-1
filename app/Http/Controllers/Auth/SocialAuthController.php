<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::query()->firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Social User',
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ],
        );

        Role::findOrCreate('buyer');
        $user->assignRole('buyer');

        Auth::login($user, remember: true);

        return redirect()->route('home');
    }
}
