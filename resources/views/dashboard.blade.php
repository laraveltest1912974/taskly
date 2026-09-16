<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-xl shadow-lg shadow-brand-900/30 p-6 sm:p-8 text-white">
            <h3 class="text-xl font-semibold">{{ __('Welcome back, :name!', ['name' => explode(' ', Auth::user()->name)[0]]) }}</h3>
            <p class="text-brand-100/90 text-[13px] mt-1">{{ __('Here is what is on your plate today.') }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-4">
                <div class="text-[12px] text-zinc-500">{{ __('Total') }}</div>
                <div class="text-xl font-semibold text-zinc-100 mt-1">{{ $totalTasks }}</div>
            </div>
            @foreach (App\TaskStatus::cases() as $status)
                <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-4">
                    <div class="text-[12px] text-zinc-500">{{ $status->label() }}</div>
                    <div class="text-xl font-semibold text-zinc-100 mt-1">{{ $tasksByStatus->get($status->value, 0) }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="text-[13px] font-medium text-zinc-200">{{ __('Upcoming Tasks') }}</h3>
                <a href="{{ route('tasks.index') }}" class="text-[12px] font-medium text-brand-300 hover:text-brand-200">{{ __('View all') }}</a>
            </div>

            @if ($upcomingTasks->isEmpty())
                <p class="px-6 py-8 text-center text-zinc-500 text-[13px]">{{ __('Nothing pending — enjoy the calm.') }}</p>
            @else
                <div class="divide-y divide-white/[0.05]">
                    @foreach ($upcomingTasks as $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-white/[0.03] transition-colors">
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-medium text-zinc-200 truncate">{{ $task->title }}</div>
                                <div class="text-[11.5px] text-zinc-500">{{ $task->due_date?->format('M j, Y') ?? __('No due date') }}</div>
                            </div>
                            <x-status-badge :status="$task->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
