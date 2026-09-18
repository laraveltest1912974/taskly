<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the OAuth provider's authentication page.
     */
    public function redirect(string $provider): SymfonyRedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth provider callback and log the user in.
     */
    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return $this->failed(__('Could not log in with :provider. Please try again.', [
                'provider' => ucfirst($provider),
            ]));
        }

        if (blank($socialUser->getEmail())) {
            return $this->failed(__(':provider did not share your email address, so we could not log you in.', [
                'provider' => ucfirst($provider),
            ]));
        }

        Auth::login($this->resolveUser($provider, $socialUser), remember: true);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Find the user linked to the social account, link an existing user with
     * the same email, or register a new one.
     */
    private function resolveUser(string $provider, SocialiteUser $socialUser): User
    {
        $socialAccount = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($socialAccount !== null) {
            return $socialAccount->user;
        }

        return DB::transaction(function () use ($provider, $socialUser): User {
            $user = User::query()->where('email', $socialUser->getEmail())->first();

            if ($user === null) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? Str::before($socialUser->getEmail(), '@'),
                    'email' => $socialUser->getEmail(),
                    'password' => Str::password(),
                ]);
            }

            if ($user->email_verified_at === null) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
            ]);

            return $user;
        });
    }

    private function failed(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['email' => $message]);
    }
}
