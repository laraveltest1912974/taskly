<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-[13px] font-medium text-zinc-200">
                {{ __('Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center gap-1.5 text-[12px] font-medium text-zinc-950 bg-zinc-100 hover:bg-white px-2.5 py-1.5 rounded-md transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('New Task') }}
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 text-[13px] text-emerald-300 bg-emerald-400/10 ring-1 ring-emerald-400/20 rounded-lg px-4 py-2.5">
            {{ session('status') }}
        </div>
    @endif

    @if ($tasks->isEmpty())
        <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-12 text-center">
            <div class="mx-auto w-11 h-11 rounded-full bg-brand-500/10 text-brand-300 flex items-center justify-center mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-zinc-300 text-[13px] font-medium">{{ __('No tasks yet.') }}</p>
            <p class="text-zinc-500 text-[12px] mt-1">{{ __('Create your first task to get started.') }}</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach ($tasks as $task)
                @php
                    $ringClass = match ($task->priority) {
                        App\TaskPriority::Low => 'border-zinc-600',
                        App\TaskPriority::Medium => 'border-orange-400 bg-orange-400/10',
                        App\TaskPriority::High => 'border-red-400 bg-red-400/10',
                    };
                    $isResolved = in_array($task->status, [App\TaskStatus::Completed, App\TaskStatus::Cancelled]);
                @endphp
                <div class="flex items-center gap-3.5 px-4 py-3 bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] hover:ring-white/[0.14] rounded-xl transition-shadow {{ $isResolved ? 'opacity-60' : '' }}">
                    @if ($task->status === App\TaskStatus::Completed)
                        <span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center shrink-0" title="{{ $task->priority->label() }}">
                            <svg class="w-2.5 h-2.5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    @else
                        <span class="w-4 h-4 rounded-full border {{ $ringClass }} shrink-0" title="{{ $task->priority->label() }}"></span>
                    @endif

                    <a href="{{ route('tasks.edit', $task) }}" class="block flex-1 min-w-0 -my-3 py-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        <div class="text-[13.5px] font-medium text-zinc-200 truncate {{ $isResolved ? 'line-through text-zinc-500' : '' }}">
                            {{ $task->title }}
                        </div>
                        <div class="text-[11.5px] text-zinc-500 mt-0.5 flex items-center gap-2">
                            @if ($task->relationLoaded('user'))
                                <span class="font-medium text-zinc-400">{{ $task->user->name }}</span>
                            @endif
                            <span>{{ $task->due_date?->format('M j, Y') ?? __('No due date') }}</span>
                        </div>
                    </a>

                    <x-status-badge :status="$task->status" />

                    <div class="flex items-center gap-2">
                        <a href="{{ route('tasks.edit', $task) }}" class="p-2.5 rounded-lg text-zinc-500 hover:text-brand-300 hover:bg-white/[0.04] active:bg-white/[0.08] transition-colors" title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                            onsubmit="return confirm('{{ __('Delete this task?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2.5 rounded-lg text-zinc-500 hover:text-red-400 hover:bg-white/[0.04] active:bg-white/[0.08] transition-colors" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    @endif
</x-app-layout>
