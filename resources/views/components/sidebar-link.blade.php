@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2 rounded-lg bg-brand-600 text-white font-semibold text-sm shadow-md shadow-brand-900/30'
    : 'flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700/70 hover:text-white font-medium text-sm transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
