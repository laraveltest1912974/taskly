<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_opening_the_logout_address_goes_to_the_login_page(): void
    {
        $this->get('/logout')->assertRedirect(route('login'));
    }

    public function test_a_signed_in_user_gets_a_confirmation_and_is_not_signed_out_by_a_get(): void
    {
        $user = User::factory()->create(['name' => 'Ana Anić']);

        $this->actingAs($user)
            ->get('/logout')
            ->assertOk()
            ->assertSee('Ana Anić')
            ->assertSee('Log Out')
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('name="_token"', false);

        $this->assertAuthenticatedAs($user);
    }

    public function test_the_confirmation_is_translated(): void
    {
        $user = User::factory()->create(['name' => 'Ana Anić']);

        $this->actingAs($user)
            ->withSession(['locale' => 'sr'])
            ->get('/logout')
            ->assertOk()
            ->assertSee('Prijavljeni ste kao Ana Anić.')
            ->assertSee('Odjava')
            ->assertSee('Otkaži');
    }

    public function test_the_post_still_signs_the_user_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }
}
