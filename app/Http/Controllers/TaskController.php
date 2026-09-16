<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\TaskPriority;
use App\TaskStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[Authorize('viewAny', Task::class)]
    public function index(): View
    {
        $user = request()->user();

        $tasks = $user->isAdmin()
            ? Task::with('user')->latest()->paginate(15)
            : $user->tasks()->latest()->paginate(15);

        return view('tasks.index', ['tasks' => $tasks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    #[Authorize('create', Task::class)]
    public function create(): View
    {
        return view('tasks.create', [
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Task created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    #[Authorize('update', 'task')]
    public function edit(Task $task): View
    {
        return view('tasks.edit', [
            'task' => $task,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    #[Authorize('update', 'task')]
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')->with('status', 'Task updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    #[Authorize('delete', 'task')]
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }
}
