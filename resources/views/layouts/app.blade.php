<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-200 text-slate-900" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen lg:flex">
            <!-- Desktop sidebar -->
            <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-slate-900 px-4 py-4">
                @include('layouts.partials.sidebar-nav')
            </aside>

            <!-- Mobile sidebar (slide-over) -->
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
                <div class="fixed inset-0 bg-slate-900/60" @click="sidebarOpen = false"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <aside class="relative flex flex-col w-64 h-full bg-slate-900 px-4 py-4"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    @include('layouts.partials.sidebar-nav')
                </aside>
            </div>

            <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
                <!-- Topbar -->
                <header class="sticky top-0 z-30 flex items-center gap-4 bg-white/90 backdrop-blur border-b border-slate-200 px-4 sm:px-6 py-3">
                    <button class="lg:hidden text-slate-500 hover:text-slate-700" @click="sidebarOpen = true">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex-1 min-w-0">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    <x-locale-switcher />

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 focus:outline-none">
                                <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-bold">
                                    {{ Str::of(Auth::user()->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
