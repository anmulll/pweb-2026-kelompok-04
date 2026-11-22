@extends('layouts.app')

@section('title', 'Checkout - iStore')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <div class="mb-8 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('products') }}" class="transition hover:text-black">
            Products
        </a>
        <span>/</span>
        <a href="/buyer/cart" class="transition hover:text-black">
            Cart
        </a>
        <span>/</span>
        <span class="font-medium text-gray-900">Checkout</span>
    </div>

    {{-- Heading --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Checkout
        </h1>
        <p class="mt-2 text-sm text-gray-500">
            Complete your shipping information to proceed with your order.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Checkout Form --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Shipping Information --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">

                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Shipping Information
                        </h2>
                        <p class="text-sm text-gray-500">
                            Where should we deliver your order?
                        </p>
                    </div>
                </div>

                <div class="space-y-5">

                    {{-- Receiver Name --}}
                    <div>
                        <label for="receiver_name"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="receiver_name"
                            name="receiver_name"
                            placeholder="Enter receiver's full name"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label for="phone"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="e.g. 081234567890"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="address"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Complete Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            placeholder="Street name, house number, district, city, province, postal code"
                            class="w-full resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        ></textarea>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Order Notes
                            <span class="font-normal text-gray-400">(Optional)</span>
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            placeholder="Additional instructions for your order..."
                            class="w-full resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        ></textarea>
                    </div>

                </div>
            </div>

            {{-- Payment Information --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">

                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.8"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <path d="M2 10h20"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Payment Method
                        </h2>
                        <p class="text-sm text-gray-500">
                            Available payment options
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-black">
                            <div class="h-2.5 w-2.5 rounded-full bg-black"></div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                Payment Gateway
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Payment options will be available during order processing.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-1">

            <div class="sticky top-24 rounded-2xl border border-gray-200 bg-white p-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Order Summary
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Review your order details
                </p>

                {{-- Product --}}
                <div class="mt-6 flex gap-4 border-b border-gray-100 pb-6">

                    {{-- Product Image Placeholder --}}
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="32" height="32"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.5"
                             stroke-linecap="round"
                             stroke-linejoin="round"
                             class="text-gray-400">
                            <rect width="14" height="20" x="5" y="2" rx="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-900">
                            Product Name
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            Condition: Used
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Quantity: 1
                        </p>

                        <p class="mt-3 text-sm font-semibold text-gray-900">
                            Rp0
                        </p>
                    </div>

                </div>

                {{-- Price Details --}}
                <div class="mt-5 space-y-3">

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">Rp0</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Shipping Fee</span>
                        <span class="font-medium text-gray-900">Rp0</span>
                    </div>

                </div>

                {{-- Total --}}
                <div class="mt-5 border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-semibold text-gray-900">
                            Total
                        </span>

                        <span class="text-xl font-bold text-gray-900">
                            Rp0
                        </span>
                    </div>
                </div>

                {{-- Submit --}}
                <button
                    type="button"
                    disabled
                    class="mt-6 w-full cursor-not-allowed rounded-full bg-gray-200 px-6 py-3.5 text-sm font-semibold text-gray-400">
                    Place Order
                </button>

                <p class="mt-3 text-center text-xs text-gray-400">
                    Checkout will be available once the system is connected.
                </p>

                {{-- Back to Cart --}}
                <a href="/buyer/cart"
                   class="mt-4 block text-center text-sm font-medium text-gray-600 transition hover:text-black">
                    ← Back to Cart
                </a>

            </div>

        </div>

    </div>

</div>
@endsection