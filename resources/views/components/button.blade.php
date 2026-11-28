@props([
    'href' => '#',
    'variant' => 'primary'
])

@php
    $styles = match($variant) {
        'secondary' => 'border border-gray-300 bg-white text-gray-900 hover:bg-gray-100',
        default => 'bg-black text-white hover:bg-gray-800',
    };
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'inline-flex items-center justify-center rounded-full px-6 py-3 text-sm font-medium transition ' . $styles
   ]) }}>
    {{ $slot }}
</a>