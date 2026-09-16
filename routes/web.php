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

    Route::resource('tasks', TaskController::class)->except('show');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
