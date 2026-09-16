<?php

namespace Tests\Unit\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->assertTrue($user->can('view', $task));
    }

    public function test_non_owner_cannot_view_others_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertFalse($otherUser->can('view', $task));
    }

    public function test_owner_can_update_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->assertTrue($user->can('update', $task));
    }

    public function test_non_owner_cannot_update_others_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertFalse($otherUser->can('update', $task));
    }

    public function test_owner_can_delete_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->assertTrue($user->can('delete', $task));
    }

    public function test_non_owner_cannot_delete_others_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertFalse($otherUser->can('delete', $task));
    }

    public function test_admin_can_view_others_task(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertTrue($admin->can('view', $task));
    }

    public function test_admin_can_update_others_task(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertTrue($admin->can('update', $task));
    }

    public function test_admin_can_delete_others_task(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertTrue($admin->can('delete', $task));
    }

    public function test_any_authenticated_user_can_create_tasks(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('create', Task::class));
    }
}
