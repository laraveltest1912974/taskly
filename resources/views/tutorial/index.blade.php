<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Tutorial') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-xl shadow-lg shadow-brand-900/30 p-6 sm:p-8 text-white">
            <h3 class="text-xl font-semibold">{{ __('See Taskly in action') }}</h3>
            <p class="text-brand-100/90 text-[13px] mt-1">{{ __('Short clips showing each feature — no reading required.') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($videos as $video)
                <div class="bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl overflow-hidden">
                    <img src="{{ asset('tutorial-clips/'.$video['file']) }}" alt="{{ $video['title'] }}" loading="lazy" class="w-full h-auto border-b border-white/[0.06]">
                    <div class="p-4">
                        <h3 class="text-[13.5px] font-medium text-zinc-100">{{ $video['title'] }}</h3>
                        <p class="text-[12px] text-zinc-500 mt-0.5">{{ $video['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
