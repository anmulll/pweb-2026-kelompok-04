@props([
    'label' => '',
    'name',
    'type' => 'text',
    'placeholder' => '',
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="mb-2 block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black'
        ]) }}
    >
</div>