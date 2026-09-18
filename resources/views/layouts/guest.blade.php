<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zinc-200 antialiased bg-app-grid">
        <div class="relative min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 overflow-hidden">
            <svg class="bg-blob" style="top: 45%; left: 50%;" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="guestBlobGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#8b5cf6" />
                        <stop offset="50%" stop-color="#6366f1" />
                        <stop offset="100%" stop-color="#ec4899" />
                    </linearGradient>
                </defs>
                <path fill="url(#guestBlobGradient)" d="M52.7,-63.4C66.9,-53.7,76.6,-37.1,79.9,-19.7C83.2,-2.3,80.1,15.9,71.6,30.8C63.1,45.7,49.2,57.3,33.7,65.2C18.2,73.1,1.1,77.3,-16.4,75.8C-33.9,74.3,-51.8,67.1,-64.3,54.5C-76.8,41.9,-83.9,23.9,-84.5,5.6C-85.1,-12.7,-79.2,-31.3,-67.8,-45.3C-56.4,-59.3,-39.5,-68.7,-22.3,-76.1C-5.1,-83.5,12.4,-88.9,28.4,-84.4C44.4,-79.9,58.9,-65.5,52.7,-63.4Z" transform="translate(100 100)" />
            </svg>

            <div class="relative z-10">
                <x-locale-switcher class="mb-4" />
            </div>

            <div class="relative z-10 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold text-sm">
                    T
                </div>
                <span class="font-semibold text-lg text-zinc-100 tracking-tight">{{ config('app.name', 'Laravel') }}</span>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-6 bg-zinc-950/60 backdrop-blur-md ring-1 ring-white/[0.08] overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>

            <div class="relative z-10 mt-4 pb-6">
                <a href="{{ route('privacy') }}" class="text-[12px] text-zinc-500 hover:text-zinc-300 underline">{{ __('Privacy Policy') }}</a>
            </div>
        </div>
    </body>
</html>
