<div class="flex items-center gap-2 px-2 py-2 mb-1">
    <div class="w-5 h-5 rounded bg-gradient-to-br from-brand-400 to-brand-600 shrink-0"></div>
    <a href="{{ route('dashboard') }}" class="font-semibold text-[13px] text-zinc-200">{{ config('app.name', 'Laravel') }}</a>
</div>

<nav class="mt-3 flex-1 flex flex-col gap-0.5">
    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-[15px] h-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l8.25-8.25L20.25 12M4.5 9.75V21h15V9.75" />
        </svg>
        {{ __('Dashboard') }}
    </x-sidebar-link>

    <x-sidebar-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
        <svg class="w-[15px] h-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('Tasks') }}
    </x-sidebar-link>

    <x-sidebar-link :href="route('tutorial')" :active="request()->routeIs('tutorial')">
        <svg class="w-[15px] h-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 8.5l6 3.5-6 3.5v-7z" />
        </svg>
        {{ __('Tutorial') }}
    </x-sidebar-link>

    @if (Auth::user()->isAdmin())
        <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7.5 3v5.25c0 4.556-3.03 8.813-7.5 10.125-4.47-1.312-7.5-5.569-7.5-10.125V6L12 3z" />
            </svg>
            {{ __('Admin') }}
        </x-sidebar-link>
    @endif
</nav>

<div class="mt-auto flex items-center gap-2 px-2 py-2 border-t border-white/[0.06] pt-3">
    <div class="w-6 h-6 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center text-[10px] font-semibold shrink-0">
        {{ Str::of(Auth::user()->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
    </div>
    <span class="text-[13px] text-zinc-400 truncate">{{ Auth::user()->name }}</span>
</div>
