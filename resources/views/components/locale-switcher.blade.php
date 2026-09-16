<div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-slate-100 p-0.5 text-xs font-semibold']) }}>
    @foreach (config('app.supported_locales') as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
            class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === $code ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
            {{ strtoupper($code) }}
        </a>
    @endforeach
</div>
