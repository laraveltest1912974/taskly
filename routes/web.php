<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\TaskStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
});

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(array_key_exists($locale, config('app.supported_locales')), 404);

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

Route::get('/dashboard', function () {
    $user = Auth::user();

    $tasksByStatus = $user->tasks()->toBase()
        ->select('status')
        ->selectRaw('count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

    $upcomingTasks = $user->tasks()
        ->whereNotIn('status', [TaskStatus::Completed->value, TaskStatus::Cancelled->value])
        ->orderByRaw('due_date IS NULL')
        ->orderBy('due_date')
        ->limit(5)
        ->get();

    return view('dashboard', [
        'totalTasks' => $tasksByStatus->sum(),
        'tasksByStatus' => $tasksByStatus,
        'upcomingTasks' => $upcomingTasks,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tutorial', function () {
        $videos = [
            ['file' => '01-login.gif', 'title' => __('Log In'), 'description' => __('Sign in with your email and password.')],
            ['file' => '02-tasks-overview.gif', 'title' => __('Browse Tasks'), 'description' => __('View all your tasks with their status and priority at a glance.')],
            ['file' => '03-create-task.gif', 'title' => __('Create a Task'), 'description' => __('Add a new task with a title, description, due date, status, and priority.')],
            ['file' => '04-edit-task.gif', 'title' => __('Edit a Task'), 'description' => __('Update any task, including marking it as completed.')],
            ['file' => '05-delete-task.gif', 'title' => __('Delete a Task'), 'description' => __('Remove a task you no longer need.')],
            ['file' => '06-language-switch.gif', 'title' => __('Switch Language'), 'description' => __('Toggle the entire interface between English and Serbian.')],
            ['file' => '07-admin-dashboard.gif', 'title' => __('Admin Dashboard'), 'description' => __("Admins can see every user's tasks and account stats.")],
        ];

        return view('tutorial.index', ['videos' => $videos]);
    })->name('tutorial');

    Route::resource('tasks', TaskController::class)->except('show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
