@extends('layouts.app')

@section('title', 'Product Details - iStore')

@section('content')

<section class="mx-auto max-w-7xl px-6 py-12">

    {{-- Breadcrumb --}}
    <div class="mb-8 text-sm text-gray-500">
        <a href="/" class="hover:text-black">Home</a>
        /
        <a href="/products" class="hover:text-black">Products</a>
        / iPhone 17 Pro
    </div>

    <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">

        {{-- Product Image --}}
        <div class="flex min-h-96 items-center justify-center rounded-3xl bg-gray-50">
            <span class="text-9xl">📱</span>
        </div>

        {{-- Product Information --}}
        <div>

            <x-badge variant="success">New</x-badge>

            <h1 class="mt-4 text-4xl font-bold tracking-tight">
                iPhone 17 Pro
            </h1>

            <p class="mt-3 text-gray-500">
                256GB · Silver
            </p>

            <p class="mt-6 text-3xl font-semibold">
                Rp 21.999.000
            </p>

            <div class="mt-8 border-t border-gray-200 pt-6">

                <h2 class="font-semibold">Product Description</h2>

                <p class="mt-3 leading-7 text-gray-600">
                    Experience powerful performance, advanced camera
                    capabilities, and premium design with the iPhone 17 Pro.
                </p>

            </div>

            {{-- Specifications --}}
            <div class="mt-8">

                <h2 class="mb-4 font-semibold">Specifications</h2>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between border-b pb-3">
                        <span class="text-gray-500">Model</span>
                        <span>iPhone 17 Pro</span>
                    </div>

                    <div class="flex justify-between border-b pb-3">
                        <span class="text-gray-500">Storage</span>
                        <span>256GB</span>
                    </div>

                    <div class="flex justify-between border-b pb-3">
                        <span class="text-gray-500">Color</span>
                        <span>Silver</span>
                    </div>

                    <div class="flex justify-between border-b pb-3">
                        <span class="text-gray-500">Condition</span>
                        <span>New</span>
                    </div>

                </div>

            </div>

            {{-- Quantity --}}
            <div class="mt-8">
                <label for="quantity" class="mb-2 block text-sm font-medium">
                    Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="1"
                    value="1"
                    class="w-24 rounded-xl border border-gray-300 px-4 py-3"
                >
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">

                <x-button href="/cart" class="flex-1">
                    Add to Cart
                </x-button>

                <x-button href="/products" variant="secondary">
                    Back to Products
                </x-button>

            </div>

        </div>

    </div>

</section>

@endsection