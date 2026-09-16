<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-6 sm:p-8">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')

                @include('tasks._form', ['task' => $task, 'statuses' => $statuses, 'priorities' => $priorities])

                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-white/[0.06]">
                    <a href="{{ route('tasks.index') }}" class="text-[13px] font-medium text-zinc-400 hover:text-zinc-200">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>{{ __('Update Task') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
