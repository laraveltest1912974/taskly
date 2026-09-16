<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="p-6 sm:p-8 bg-white rounded-2xl shadow-lg shadow-slate-300/40 ring-1 ring-slate-900/5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
