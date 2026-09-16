<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-5">
                <div class="text-sm text-slate-500">{{ __('Users') }}</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalUsers }}</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-5">
                <div class="text-sm text-slate-500">{{ __('Tasks') }}</div>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalTasks }}</div>
            </div>
            @foreach (App\TaskStatus::cases() as $status)
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 p-5">
                    <div class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $status->value)) }}</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1">{{ $tasksByStatus->get($status->value, 0) }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">{{ __('Users') }}</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-100">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">{{ __('Email') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">{{ __('Role') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">{{ __('Tasks') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-3.5 whitespace-nowrap text-sm font-medium text-slate-800">{{ $user->name }}</td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-sm text-slate-500">{{ $user->email }}</td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full {{ $user->isAdmin() ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ ucfirst($user->role->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-sm text-slate-500">{{ $user->tasks_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
