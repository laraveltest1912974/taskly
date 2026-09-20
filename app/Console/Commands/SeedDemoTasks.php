<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\TaskPriority;
use App\TaskStatus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('taskly:seed-demo {emails* : Emails of existing users}')]
#[Description('Give existing users a realistic list of demo tasks (skipped for users who already have several tasks)')]
class SeedDemoTasks extends Command
{
    /**
     * A user with at least this many tasks is left alone, so the command is safe to run repeatedly.
     */
    private const ENOUGH_TASKS = 6;

    /**
     * Demo tasks in English and Serbian: title, description, status, priority and the due date in days from today.
     *
     * @var list<array{0: string, 1: ?string, 2: TaskStatus, 3: TaskPriority, 4: ?int}>
     */
    private const DEMO_TASKS = [
        ['Finish the quarterly report', 'Include Q3 sales numbers and next steps.', TaskStatus::InProgress, TaskPriority::High, 1],
        ['Pozvati zubara', 'Zakazati pregled.', TaskStatus::Pending, TaskPriority::High, 0],
        ['Platiti račun za struju', null, TaskStatus::Pending, TaskPriority::High, -1],
        ['Prepare presentation for Monday', 'Cover the new project roadmap.', TaskStatus::InProgress, TaskPriority::Medium, 3],
        ['Kupiti namirnice', 'Mleko, jaja, hleb i povrće za nedelju dana.', TaskStatus::Pending, TaskPriority::Medium, 2],
        ['Book flight tickets', 'For the trip to Belgrade in October.', TaskStatus::Pending, TaskPriority::Medium, 5],
        ['Zakazati servis automobila', 'Zamena ulja i rotacija guma.', TaskStatus::Pending, TaskPriority::Low, 7],
        ['Read 20 pages of the new book', null, TaskStatus::Pending, TaskPriority::Low, null],
        ['Ažurirati CV', 'Dodati najnovije radno iskustvo.', TaskStatus::Completed, TaskPriority::Medium, -4],
        ['Backup important files', 'Photos and work documents.', TaskStatus::Completed, TaskPriority::High, -2],
        ['Zaliti biljke', null, TaskStatus::Completed, TaskPriority::Low, -6],
        ['Fix the leaking faucet', null, TaskStatus::Cancelled, TaskPriority::Low, -3],
    ];

    public function handle(): int
    {
        $missing = [];

        foreach ((array) $this->argument('emails') as $email) {
            $user = User::query()->where('email', $email)->first();

            if ($user === null) {
                $this->components->warn("There is no user with the email \"{$email}\".");
                $missing[] = $email;

                continue;
            }

            if ($user->tasks()->count() >= self::ENOUGH_TASKS) {
                $this->components->info("{$user->name} already has tasks, nothing to do.");

                continue;
            }

            foreach (self::DEMO_TASKS as [$title, $description, $status, $priority, $dueInDays]) {
                Task::query()->create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $dueInDays === null ? null : today()->addDays($dueInDays),
                ]);
            }

            $this->components->info('Added '.count(self::DEMO_TASKS)." demo tasks for {$user->name}.");
        }

        return $missing === [] ? self::SUCCESS : self::FAILURE;
    }
}
