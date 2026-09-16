<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900">
                {{ __('Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-brand-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('New Task') }}
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    @if ($tasks->isEmpty())
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-12 text-center">
            <div class="mx-auto w-12 h-12 rounded-full bg-brand-50 text-brand-500 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-slate-500 font-medium">{{ __('No tasks yet.') }}</p>
            <p class="text-slate-400 text-sm mt-1">{{ __('Create your first task to get started.') }}</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 divide-y divide-slate-100">
            @foreach ($tasks as $task)
                <div class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50/80 transition-colors duration-100">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-slate-800 truncate {{ $task->status === App\TaskStatus::Completed ? 'line-through text-slate-400' : '' }}">
                            {{ $task->title }}
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-3">
                            @if ($task->relationLoaded('user'))
                                <span class="font-medium text-slate-500">{{ $task->user->name }}</span>
                            @endif
                            <span>{{ $task->due_date?->format('M j, Y') ?? __('No due date') }}</span>
                        </div>
                    </div>

                    <x-status-badge :status="$task->status" />
                    <x-priority-badge :priority="$task->priority" class="hidden sm:inline-flex" />

                    <div class="flex items-center gap-3 pl-2">
                        <a href="{{ route('tasks.edit', $task) }}" class="text-slate-400 hover:text-brand-600 transition-colors" title="{{ __('Edit') }}">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                            onsubmit="return confirm('{{ __('Delete this task?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="{{ __('Delete') }}">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
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
