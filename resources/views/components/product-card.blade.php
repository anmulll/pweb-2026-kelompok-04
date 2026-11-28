@props([
    'name',
    'specification' => '',
    'price',
    'condition' => 'New',
    'grade' => null,
    'image' => null,
    'href' => '#',
])

<div class="overflow-hidden rounded-2xl border border-gray-200 transition hover:shadow-md">

    {{-- Product Image --}}
    <div class="flex h-56 items-center justify-center bg-gray-50">

        @if($image)
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                class="h-full w-full object-contain p-6"
            >
        @else
            <span class="text-7xl">📱</span>
        @endif

    </div>

    {{-- Product Information --}}
    <div class="p-5">

        <x-badge>
            {{ $condition }}{{ $grade ? ' · Grade ' . $grade : '' }}
        </x-badge>

        <h3 class="mt-3 font-semibold">
            {{ $name }}
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            {{ $specification }}
        </p>

        <p class="mt-4 font-semibold">
            {{ $price }}
        </p>

        <x-button href="{{ $href }}" class="mt-4 w-full">
            View Details
        </x-button>

    </div>

</div>