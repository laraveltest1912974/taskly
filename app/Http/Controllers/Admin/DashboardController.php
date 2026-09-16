<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $tasksByStatus = Task::toBase()
            ->select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $users = User::withCount('tasks')->orderByDesc('tasks_count')->get();

        return view('admin.dashboard', [
            'totalUsers' => $users->count(),
            'totalTasks' => $tasksByStatus->sum(),
            'tasksByStatus' => $tasksByStatus,
            'users' => $users,
        ]);
    }
}
