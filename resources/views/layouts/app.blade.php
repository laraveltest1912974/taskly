<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('layouts.partials.pwa-head')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-app-grid text-zinc-200" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen lg:flex">
            <!-- Desktop sidebar -->
            <aside class="hidden lg:flex lg:flex-col lg:w-60 lg:fixed lg:inset-y-0 lg:left-0 lg:z-20 bg-black/20 border-r border-white/[0.06] px-3 py-3">
                @include('layouts.partials.sidebar-nav')
            </aside>

            <!-- Mobile sidebar (slide-over) -->
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
                <div class="fixed inset-0 bg-black/60" @click="sidebarOpen = false"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <aside class="relative flex flex-col w-60 h-full bg-zinc-950 border-r border-white/[0.06] px-3 py-3"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    @include('layouts.partials.sidebar-nav')
                </aside>
            </div>

            <div class="relative flex-1 flex flex-col min-w-0 lg:pl-60 overflow-hidden">
                <!-- Abstract background centerpiece -->
                <svg class="bg-blob" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="bgBlobGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#8b5cf6" />
                            <stop offset="50%" stop-color="#6366f1" />
                            <stop offset="100%" stop-color="#ec4899" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#bgBlobGradient)" d="M52.7,-63.4C66.9,-53.7,76.6,-37.1,79.9,-19.7C83.2,-2.3,80.1,15.9,71.6,30.8C63.1,45.7,49.2,57.3,33.7,65.2C18.2,73.1,1.1,77.3,-16.4,75.8C-33.9,74.3,-51.8,67.1,-64.3,54.5C-76.8,41.9,-83.9,23.9,-84.5,5.6C-85.1,-12.7,-79.2,-31.3,-67.8,-45.3C-56.4,-59.3,-39.5,-68.7,-22.3,-76.1C-5.1,-83.5,12.4,-88.9,28.4,-84.4C44.4,-79.9,58.9,-65.5,52.7,-63.4Z" transform="translate(100 100)" />
                </svg>

                <!-- Topbar -->
                <header class="relative z-10 sticky top-0 flex items-center gap-4 bg-black/10 backdrop-blur border-b border-white/[0.06] px-4 sm:px-6 py-3">
                    <button class="lg:hidden text-zinc-400 hover:text-zinc-200" @click="sidebarOpen = true">
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

                    <x-dropdown align="right" width="48" content-classes="py-1 bg-zinc-900">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-[13px] font-medium text-zinc-400 hover:text-zinc-100 focus:outline-none">
                                <span class="w-7 h-7 rounded-full bg-brand-500/20 text-brand-300 flex items-center justify-center text-[11px] font-semibold">
                                    {{ Str::of(Auth::user()->name)->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
