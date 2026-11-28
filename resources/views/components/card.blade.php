@props([
    'title',
    'description' => '',
    'href' => '#',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-gray-50 p-8 text-center transition hover:shadow-md']) }}>

    <h3 class="text-xl font-semibold">
        {{ $title }}
    </h3>

    <p class="mt-2 text-sm text-gray-500">
        {{ $description }}
    </p>

    <x-button href="{{ $href }}" class="mt-5">
        Explore
    </x-button>

</div>