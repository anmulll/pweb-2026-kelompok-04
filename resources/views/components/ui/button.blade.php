@props([
    'variant' => 'primary', // primary, secondary, danger, success, muted
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg text-sm px-4 py-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-zinc-900 disabled:opacity-50 disabled:pointer-events-none';
    $variants = [
        'primary' => 'bg-blue-600 hover:bg-blue-500 text-white focus:ring-blue-500 shadow-sm',
        'secondary' => 'bg-zinc-800 hover:bg-zinc-700 text-zinc-100 border border-zinc-700 focus:ring-zinc-500',
        'danger' => 'bg-rose-600 hover:bg-rose-500 text-white focus:ring-rose-500 shadow-sm',
        'success' => 'bg-emerald-600 hover:bg-emerald-500 text-white focus:ring-emerald-500 shadow-sm',
        'muted' => 'bg-transparent hover:bg-zinc-800 text-zinc-400 hover:text-white',
    ];
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
