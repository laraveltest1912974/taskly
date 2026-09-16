<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900">
            {{ __('Edit Task') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-6 sm:p-8">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')

                @include('tasks._form', ['task' => $task, 'statuses' => $statuses, 'priorities' => $priorities])

                <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('tasks.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>{{ __('Update Task') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
