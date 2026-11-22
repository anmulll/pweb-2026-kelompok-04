@props([
    'type' => 'info'
])

@php
    $styles = match($type) {
        'success' => 'bg-green-50 border-green-200 text-green-700',
        'error' => 'bg-red-50 border-red-200 text-red-700',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-700',
        default => 'bg-blue-50 border-blue-200 text-blue-700',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border p-4 text-sm ' . $styles]) }}>
    {{ $slot }}
</div>