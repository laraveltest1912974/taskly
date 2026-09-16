@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/[0.04] border-white/10 text-zinc-100 placeholder-zinc-500 focus:border-brand-500 focus:ring-brand-500/40 rounded-lg [color-scheme:dark]']) }}>
