<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_sees_totals_and_per_user_task_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Task::factory()->for($user)->count(3)->create(['status' => 'pending']);
        Task::factory()->for($admin)->count(2)->create(['status' => 'completed']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalUsers', 2);
        $response->assertViewHas('totalTasks', 5);
        $response->assertViewHas(
            'users',
            fn ($users) => $users->firstWhere('id', $user->id)->tasks_count === 3
                && $users->firstWhere('id', $admin->id)->tasks_count === 2
        );
    }
}
