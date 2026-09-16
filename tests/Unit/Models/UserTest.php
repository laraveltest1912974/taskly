<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_mass_assignment_ignores_role_and_falls_back_to_the_default(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->assertSame(UserRole::User, $user->fresh()->role);
    }

    public function test_is_admin_returns_true_for_an_admin_role(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_for_a_regular_user(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
    }
}
