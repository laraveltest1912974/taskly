<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl space-y-4">
        <div class="p-6 sm:p-8 bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-6 sm:p-8 bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-6 sm:p-8 bg-zinc-950/40 backdrop-blur-md ring-1 ring-white/[0.08] rounded-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
