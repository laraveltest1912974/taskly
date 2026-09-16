@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center gap-2.5 px-2 py-[5px] rounded-md bg-white/[0.07] text-zinc-100 font-medium text-[13px]'
    : 'flex items-center gap-2.5 px-2 py-[5px] rounded-md text-zinc-400 hover:bg-white/[0.04] hover:text-zinc-100 font-medium text-[13px] transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
