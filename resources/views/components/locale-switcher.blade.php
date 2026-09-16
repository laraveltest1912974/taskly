<div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-white/[0.06] p-0.5 text-[11px] font-semibold']) }}>
    @foreach (config('app.supported_locales') as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
            class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === $code ? 'bg-white/10 text-brand-300' : 'text-zinc-500 hover:text-zinc-300' }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach
</div>
