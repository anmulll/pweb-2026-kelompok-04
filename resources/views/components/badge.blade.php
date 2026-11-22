@props([
    'variant' => 'default'
])

@php
    $styles = match($variant) {
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-yellow-100 text-yellow-700',
        'danger' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-3 py-1 text-xs font-medium ' . $styles]) }}>
    {{ $slot }}
</span>