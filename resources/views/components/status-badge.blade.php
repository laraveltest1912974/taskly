@props(['status'])

@php
$classes = match ($status) {
    App\TaskStatus::Pending => 'bg-amber-100 text-amber-700',
    App\TaskStatus::InProgress => 'bg-blue-100 text-blue-700',
    App\TaskStatus::Completed => 'bg-emerald-100 text-emerald-700',
    App\TaskStatus::Cancelled => 'bg-slate-100 text-slate-500',
};
$label = ucfirst(str_replace('_', ' ', $status->value));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap $classes"]) }}>
    {{ $label }}
</span>
