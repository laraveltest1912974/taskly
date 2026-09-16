@props(['priority'])

@php
$classes = match ($priority) {
    App\TaskPriority::Low => 'bg-slate-100 text-slate-500',
    App\TaskPriority::Medium => 'bg-orange-100 text-orange-600',
    App\TaskPriority::High => 'bg-red-100 text-red-600',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap $classes"]) }}>
    {{ ucfirst($priority->value) }}
</span>
