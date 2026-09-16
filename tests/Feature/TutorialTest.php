<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class TutorialTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('tutorial'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_tutorial(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tutorial'));

        $response->assertOk();
        $response->assertSee('Log In');
        $response->assertSee('Admin Dashboard');
    }
}
