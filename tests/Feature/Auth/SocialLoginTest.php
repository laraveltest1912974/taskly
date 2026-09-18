<?php

namespace Tests\Feature\Auth;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_register_screens_show_social_buttons(): void
    {
        foreach (['/login', '/register'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee(route('social.redirect', 'google'), false)
                ->assertSee(route('social.redirect', 'facebook'), false);
        }
    }

    public function test_redirect_sends_the_user_to_the_provider(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')->once()->andReturn(new RedirectResponse('https://provider.test/oauth'));
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $this->get(route('social.redirect', 'google'))->assertRedirect('https://provider.test/oauth');
    }

    public function test_unsupported_provider_returns_not_found(): void
    {
        $this->get('/auth/twitter/redirect')->assertNotFound();
        $this->get('/auth/twitter/callback')->assertNotFound();
    }

    public function test_new_user_is_registered_and_logged_in(): void
    {
        $this->mockProviderUser('google', id: 'g-123', email: 'new@example.com', name: 'New Person');

        $this->get(route('social.callback', 'google'))
            ->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('New Person', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'g-123',
        ]);
    }

    public function test_existing_user_with_same_email_is_linked_not_duplicated(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        $this->mockProviderUser('facebook', id: 'fb-9', email: 'existing@example.com', name: 'Someone');

        $this->get(route('social.callback', 'facebook'))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::count());
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'facebook',
            'provider_user_id' => 'fb-9',
        ]);
    }

    public function test_returning_social_user_is_logged_in_without_creating_records(): void
    {
        $socialAccount = SocialAccount::factory()->create([
            'provider' => 'google',
            'provider_user_id' => 'g-1',
        ]);
        $this->mockProviderUser('google', id: 'g-1', email: 'changed@example.com', name: 'Whoever');

        $this->get(route('social.callback', 'google'))
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($socialAccount->user);
        $this->assertSame(1, User::count());
        $this->assertSame(1, SocialAccount::count());
    }

    public function test_provider_failure_redirects_to_login_with_error(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andThrow(new RuntimeException('Invalid state'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('social.callback', 'google'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_provider_without_email_is_rejected(): void
    {
        $this->mockProviderUser('facebook', id: 'fb-1', email: null, name: 'No Email');

        $this->get(route('social.callback', 'facebook'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(0, User::count());
    }

    private function mockProviderUser(string $provider, string $id, ?string $email, string $name): void
    {
        $socialUser = (new SocialiteUser)->map([
            'id' => $id,
            'name' => $name,
            'email' => $email,
        ]);

        $driver = Mockery::mock(Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);
    }
}
