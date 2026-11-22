@props([
    'active' => false,
])

<button
    type="button"
    {{ $attributes->merge([
        'class' => 'border-b-2 px-4 py-3 text-sm font-medium transition ' .
        ($active ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black')
    ]) }}
>
    {{ $slot }}
</button>