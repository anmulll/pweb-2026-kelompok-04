@props([
    'href' => '#',
    'active' => false,
])

@php
    $classes = $active
        ? 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white bg-blue-600/20 border border-blue-500/35 shadow-sm transition'
        : 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-normal text-zinc-400 hover:text-white hover:bg-white/5 border border-transparent transition';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes, 'aria-current' => $active ? 'page' : null]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-blue-400' : 'bg-zinc-600' }}"></span>
    <span>{{ $slot }}</span>
</a>
