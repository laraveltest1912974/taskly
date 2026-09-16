@props(['status'])

@php
$classes = match ($status) {
    App\TaskStatus::Pending => 'text-amber-300 bg-amber-400/10',
    App\TaskStatus::InProgress => 'text-blue-300 bg-blue-400/10',
    App\TaskStatus::Completed => 'text-emerald-300 bg-emerald-400/10',
    App\TaskStatus::Cancelled => 'text-zinc-400 bg-zinc-400/10',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center text-[11px] font-medium px-2 py-1 rounded-md whitespace-nowrap $classes"]) }}>
    {{ $status->label() }}
</span>
