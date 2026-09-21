<x-guest-layout>
    <div class="mb-4 text-[13px] text-zinc-400">
        {{ __('You are signed in as :name.', ['name' => auth()->user()->name]) }}
    </div>

    <form method="POST" action="{{ route('logout') }}" class="flex items-center justify-end gap-2">
        @csrf

        <a href="{{ $backUrl }}" class="inline-flex items-center rounded-lg border border-white/10 px-3 py-2 text-[13px] font-medium text-zinc-300 transition hover:bg-white/5">
            {{ __('Cancel') }}
        </a>

        <x-primary-button>
            {{ __('Log Out') }}
        </x-primary-button>
    </form>
</x-guest-layout>
