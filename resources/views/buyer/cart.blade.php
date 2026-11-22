
@extends('layouts.app')

@section('title', 'Shopping Cart - iStore')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <div class="mb-8">
        <a href="{{ route('products') }}"
           class="text-sm text-gray-500 transition hover:text-black">
            ← Continue Shopping
        </a>
    </div>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">
            Shopping Cart
        </h1>

        <p class="mt-2 text-gray-500">
            Review your items before checkout.
        </p>
    </div>

    {{-- Cart Layout --}}
    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Cart Items --}}
        <div class="lg:col-span-2">

            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-16 text-center">

                {{-- Empty Cart Icon --}}
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl">
                    🛒
                </div>

                <h2 class="mt-6 text-xl font-semibold">
                    Your cart is empty
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                    Looks like you haven't added anything to your cart yet.
                    Explore our collection and find your perfect device.
                </p>

                <a href="{{ route('products') }}"
                   class="mt-6 inline-flex items-center justify-center rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
                    Explore Products
                </a>

            </div>

        </div>

        {{-- Order Summary --}}
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">

                <h2 class="text-lg font-semibold">
                    Order Summary
                </h2>

                <div class="mt-6 space-y-4 text-sm">

                    {{-- Subtotal --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Subtotal
                        </span>

                        <span class="font-medium">
                            Rp0
                        </span>
                    </div>

                    {{-- Shipping --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Shipping
                        </span>

                        <span class="font-medium">
                            Calculated at checkout
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between">
                            <span class="font-semibold">
                                Total
                            </span>

                            <span class="text-lg font-bold">
                                Rp0
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Checkout --}}
                <button
                    type="button"
                    disabled
                    class="mt-6 w-full cursor-not-allowed rounded-full bg-gray-200 px-6 py-3 text-sm font-medium text-gray-400">
                    Proceed to Checkout
                </button>

                <p class="mt-3 text-center text-xs text-gray-400">
                    Add items to your cart to continue.
                </p>

            </div>

        </div>

    </div>

</div>
@endsection
