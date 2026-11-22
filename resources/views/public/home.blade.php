@extends('layouts.app')

@section('title', 'iStore - Apple Devices')

@section('content')

{{-- Hero Section --}}
<section class="bg-gray-50 px-6 py-20 text-center md:py-32">

    <p class="mb-4 text-sm font-medium text-gray-500">
        Discover the Apple Experience
    </p>

    <h1 class="text-4xl font-bold tracking-tight md:text-7xl">
        The future is here.
    </h1>

    <p class="mx-auto mt-6 max-w-xl text-lg text-gray-600">
        Discover your favorite Apple devices.
        Find the perfect technology for your everyday life.
    </p>

    <div class="mt-8 flex justify-center gap-4">

        <x-button href="/products">
            Explore Products
        </x-button>

        <x-button href="/login" variant="secondary">
            Sign In
        </x-button>

    </div>

</section>


{{-- Category Section --}}
<section class="mx-auto max-w-7xl px-6 py-16">

    <h2 class="mb-10 text-center text-2xl font-semibold">
        Explore our products
    </h2>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

        <x-card
            title="iPhone"
            description="Innovation in your hands."
            href="/products"
        />

        <x-card
            title="iPad"
            description="Creativity without limits."
            href="/products"
        />

        <x-card
            title="Mac"
            description="Power for everything you do."
            href="/products"
        />

    </div>

</section>


{{-- CTA Section --}}
<section class="bg-black px-6 py-16 text-center text-white">

    <h2 class="text-3xl font-semibold">
        Find your next device.
    </h2>

    <p class="mt-4 text-gray-400">
        Your favorite Apple products, all in one place.
    </p>

    <a href="/products"
       class="mt-6 inline-block rounded-full bg-white px-6 py-3 text-sm font-medium text-black transition hover:bg-gray-200">
        Shop Now
    </a>

</section>

@endsection