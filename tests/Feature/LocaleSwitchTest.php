<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_switching_to_a_supported_locale_stores_it_in_the_session(): void
    {
        $response = $this->get(route('locale.switch', 'sr'));

        $response->assertRedirect();
        $this->assertSame('sr', session('locale'));
    }

    public function test_switching_to_an_unsupported_locale_is_not_found(): void
    {
        $response = $this->get(route('locale.switch', 'fr'));

        $response->assertNotFound();
        $this->assertNull(session('locale'));
    }

    public function test_login_page_renders_in_serbian_once_locale_is_set(): void
    {
        $this->withSession(['locale' => 'sr'])
            ->get(route('login'))
            ->assertSee('Prijava');
    }

    public function test_tasks_page_renders_in_serbian_once_locale_is_set(): void
    {
        $user = User::factory()->create();

        $this->withSession(['locale' => 'sr'])
            ->actingAs($user)
            ->get(route('tasks.index'))
            ->assertSee('Zadaci');
    }
}
