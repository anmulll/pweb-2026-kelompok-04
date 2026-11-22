@props([
    'href' => '#',
    'active' => false,
])

@php
    $classes = $active
        ? 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white bg-zinc-800/90 border border-white/15 transition'
        : 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-normal text-zinc-400 hover:text-zinc-100 hover:bg-zinc-900/80 border border-transparent transition';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-current' => $active ? 'page' : null]) }}>
    {{ $slot }}
</a>