<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Tutorial') }}
        </h2>
    </x-slot>

    <div class="space-y-6" x-data="{
        open: null,
        videos: @js($videos),
        get current() { return this.videos.find(v => v.file === this.open) ?? null; },
    }">
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-xl shadow-lg shadow-brand-900/30 p-6 sm:p-8 text-white">
            <h3 class="text-xl font-semibold">{{ __('See Taskly in action') }}</h3>
            <p class="text-brand-100/90 text-[13px] mt-1">{{ __('Short clips showing each feature — no reading required.') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($videos as $video)
                <div class="group bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl overflow-hidden">
                    <button type="button" class="relative block w-full cursor-zoom-in" @click="open = '{{ $video['file'] }}'">
                        <img src="{{ asset('tutorial-clips/'.$video['file']) }}" alt="{{ $video['title'] }}" loading="lazy" class="w-full h-auto border-b border-white/[0.06]">
                        <span class="absolute inset-0 border-b border-white/[0.06] bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="flex items-center gap-1.5 text-[12px] font-medium text-white bg-black/60 px-3 py-1.5 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16zM11 8v6M8 11h6" />
                                </svg>
                                {{ __('Enlarge') }}
                            </span>
                        </span>
                    </button>
                    <div class="p-4">
                        <h3 class="text-[13.5px] font-medium text-zinc-100">{{ $video['title'] }}</h3>
                        <p class="text-[12px] text-zinc-500 mt-0.5">{{ $video['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lightbox -->
        <div x-show="open" x-cloak
            class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm flex items-center justify-center p-6"
            x-transition.opacity
            @click="open = null"
            @keydown.escape.window="open = null">
            <div class="max-w-[112rem] w-full" @click.stop>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-zinc-100 text-[13.5px] font-medium" x-text="current?.title"></h3>
                    <button type="button" class="text-zinc-400 hover:text-white" @click="open = null">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <template x-if="open">
                    <img :src="'{{ asset('tutorial-clips') }}/' + open" :alt="current?.title" class="w-full rounded-xl ring-1 ring-white/[0.1]">
                </template>
                <p class="text-zinc-400 text-[12.5px] mt-3" x-text="current?.description"></p>
            </div>
        </div>
    </div>
</x-app-layout>
