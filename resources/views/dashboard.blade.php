<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl shadow-lg shadow-brand-900/20 p-6 sm:p-8 text-white">
            <h3 class="text-2xl font-bold">{{ __('Welcome back, :name!', ['name' => explode(' ', Auth::user()->name)[0]]) }}</h3>
            <p class="text-brand-100 mt-1">{{ __('Here is what is on your plate today.') }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-5">
                <div class="text-sm text-slate-500">{{ __('Total') }}</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalTasks }}</div>
            </div>
            @foreach (App\TaskStatus::cases() as $status)
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-5">
                    <div class="text-sm text-slate-500">{{ $status->label() }}</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1">{{ $tasksByStatus->get($status->value, 0) }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">{{ __('Upcoming Tasks') }}</h3>
                <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">{{ __('View all') }}</a>
            </div>

            @if ($upcomingTasks->isEmpty())
                <p class="px-6 py-8 text-center text-slate-400 text-sm">{{ __('Nothing pending — enjoy the calm.') }}</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($upcomingTasks as $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="flex items-center gap-4 px-6 py-3.5 hover:bg-slate-50/80 transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate">{{ $task->title }}</div>
                                <div class="text-xs text-slate-400">{{ $task->due_date?->format('M j, Y') ?? __('No due date') }}</div>
                            </div>
                            <x-status-badge :status="$task->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
