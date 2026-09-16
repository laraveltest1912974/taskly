<div class="flex items-center gap-2 px-2 py-3 mb-6">
    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm">
        T
    </div>
    <a href="{{ route('dashboard') }}" class="font-bold text-white tracking-tight">{{ config('app.name', 'Laravel') }}</a>
</div>

<nav class="flex-1 flex flex-col gap-1">
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l8.25-8.25L20.25 12M4.5 9.75V21h15V9.75" />
        </svg>
        {{ __('Dashboard') }}
    </x-sidebar-link>

    <x-sidebar-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('Tasks') }}
    </x-sidebar-link>

    @if (Auth::user()->isAdmin())
        <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7.5 3v5.25c0 4.556-3.03 8.813-7.5 10.125-4.47-1.312-7.5-5.569-7.5-10.125V6L12 3z" />
            </svg>
            {{ __('Admin') }}
        </x-sidebar-link>
    @endif
</nav>
