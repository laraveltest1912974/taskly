<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedDemoTasksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gives_a_user_a_realistic_mix_of_tasks(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])->assertSuccessful();

        $tasks = $user->tasks()->get();

        $this->assertCount(12, $tasks);
        $this->assertSame(3, $tasks->where('status', TaskStatus::Completed)->count());
        $this->assertSame(2, $tasks->where('status', TaskStatus::InProgress)->count());
        $this->assertSame(1, $tasks->where('status', TaskStatus::Cancelled)->count());
        $this->assertSame(6, $tasks->where('status', TaskStatus::Pending)->count());
        $this->assertCount(12, $tasks->pluck('title')->unique(), 'Demo task titles should be distinct');
    }

    public function test_the_due_dates_are_relative_to_today_and_include_an_overdue_task(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])->assertSuccessful();

        $open = $user->tasks()->whereIn('status', [TaskStatus::Pending, TaskStatus::InProgress])->whereNotNull('due_date')->get();

        $this->assertTrue($open->contains(fn (Task $task): bool => $task->due_date->isPast() && ! $task->due_date->isToday()));
        $this->assertTrue($open->contains(fn (Task $task): bool => $task->due_date->isFuture()));
        $this->assertTrue($user->tasks()->whereNull('due_date')->exists());
    }

    public function test_it_is_idempotent(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])->assertSuccessful();
        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])
            ->expectsOutputToContain('already has tasks')
            ->assertSuccessful();

        $this->assertCount(12, $user->tasks()->get());
    }

    public function test_a_user_who_has_only_a_few_tasks_still_gets_the_demo_list(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);
        Task::factory()->for($user)->create();

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])->assertSuccessful();

        $this->assertCount(13, $user->tasks()->get());
    }

    public function test_it_only_touches_the_given_users(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);
        $other = User::factory()->create(['email' => 'marko@example.com']);

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com']])->assertSuccessful();

        $this->assertCount(0, $other->tasks()->get());
    }

    public function test_it_handles_several_users_and_reports_unknown_emails(): void
    {
        $ana = User::factory()->create(['email' => 'ana@example.com']);
        $marko = User::factory()->create(['email' => 'marko@example.com']);

        $this->artisan('taskly:seed-demo', ['emails' => ['ana@example.com', 'nema@example.com', 'marko@example.com']])
            ->expectsOutputToContain('nema@example.com')
            ->assertFailed();

        $this->assertCount(12, $ana->tasks()->get());
        $this->assertCount(12, $marko->tasks()->get());
    }
}
