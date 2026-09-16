<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\TaskPriority;
use App\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_tasks_index(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_lists_only_the_authenticated_users_tasks(): void
    {
        $user = User::factory()->create();
        $ownTask = Task::factory()->for($user)->create();
        $othersTask = Task::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee($ownTask->title);
        $response->assertDontSee($othersTask->title);
    }

    public function test_admin_sees_all_users_tasks_on_index(): void
    {
        $admin = User::factory()->admin()->create();
        $othersTask = Task::factory()->create();

        $response = $this->actingAs($admin)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertSee($othersTask->title);
    }

    public function test_valid_payload_creates_task_and_redirects_to_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Buy milk',
            'description' => 'Whole milk, two liters',
            'due_date' => '2026-10-01',
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::High->value,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Buy milk',
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::High->value,
        ]);
    }

    public function test_empty_payload_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), []);

        $response->assertSessionHasErrors(['title', 'status', 'priority']);
    }

    public function test_owner_can_update_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Old title']);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'New title',
            'status' => TaskStatus::InProgress->value,
            'priority' => TaskPriority::Medium->value,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'New title',
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function test_non_owner_cannot_update_others_task(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $response = $this->actingAs($otherUser)->put(route('tasks.update', $task), [
            'title' => 'Hijacked',
            'status' => TaskStatus::Pending->value,
            'priority' => TaskPriority::Low->value,
        ]);

        $response->assertForbidden();
    }

    public function test_owner_can_delete_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
