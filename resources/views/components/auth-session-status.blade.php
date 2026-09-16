@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-[13px] text-emerald-400']) }}>
        {{ $status }}
    </div>
@endif
