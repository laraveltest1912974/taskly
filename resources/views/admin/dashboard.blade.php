<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-4">
                <div class="text-[12px] text-zinc-500">{{ __('Users') }}</div>
                <div class="text-xl font-semibold text-zinc-100 mt-1">{{ $totalUsers }}</div>
            </div>
            <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-4">
                <div class="text-[12px] text-zinc-500">{{ __('Tasks') }}</div>
                <div class="text-xl font-semibold text-zinc-100 mt-1">{{ $totalTasks }}</div>
            </div>
            @foreach (App\TaskStatus::cases() as $status)
                <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl p-4">
                    <div class="text-[12px] text-zinc-500">{{ $status->label() }}</div>
                    <div class="text-xl font-semibold text-zinc-100 mt-1">{{ $tasksByStatus->get($status->value, 0) }}</div>
                </div>
            @endforeach
        </div>

        <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="text-[13px] font-medium text-zinc-200">{{ __('Users') }}</h3>
            </div>
            <table class="min-w-full divide-y divide-white/[0.05]">
                <thead>
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-medium text-zinc-500 uppercase tracking-wider">{{ __('User') }}</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-medium text-zinc-500 uppercase tracking-wider">{{ __('Email') }}</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-medium text-zinc-500 uppercase tracking-wider">{{ __('Role') }}</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-medium text-zinc-500 uppercase tracking-wider">{{ __('Tasks') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.05]">
                    @foreach ($users as $user)
                        <tr class="hover:bg-white/[0.03]">
                            <td class="px-5 py-3 whitespace-nowrap text-[13px] font-medium text-zinc-200">{{ $user->name }}</td>
                            <td class="px-5 py-3 whitespace-nowrap text-[13px] text-zinc-500">{{ $user->email }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center text-[11px] font-medium px-2 py-1 rounded-md {{ $user->isAdmin() ? 'bg-brand-400/10 text-brand-300' : 'bg-zinc-400/10 text-zinc-400' }}">
                                    {{ $user->role->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-[13px] text-zinc-500">{{ $user->tasks_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
