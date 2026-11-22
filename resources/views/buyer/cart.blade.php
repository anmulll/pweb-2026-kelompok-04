
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

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Shopping Cart
        </h1>
        <p class="mt-2 text-gray-500">
            Review your items before checkout.
        </p>
    </div>

    {{-- Cart Layout --}}
    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Cart Items --}}
        <div class="space-y-4 lg:col-span-2">

            {{-- Demo Cart Item --}}
            <div id="cart-item"
                 class="rounded-2xl border border-gray-200 bg-white p-6">

                <div class="flex flex-col gap-5 sm:flex-row">

                    {{-- Product Image --}}
                    <div class="flex h-32 w-full shrink-0 items-center justify-center rounded-xl bg-gray-100 sm:w-32">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="40" height="40"
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

                    {{-- Product Details --}}
                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-4">

                            <div>
                                <h2 class="font-semibold text-gray-900">
                                    Demo Product
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Condition: Used
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Grade: A
                                </p>
                            </div>

                            {{-- Remove --}}
                            <button
                                type="button"
                                onclick="removeCartItem()"
                                class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-500"
                                aria-label="Remove item">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width="20" height="20"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M8 6V4h8v2"/>
                                    <path d="m19 6-1 14H6L5 6"/>
                                    <path d="M10 11v5"/>
                                    <path d="M14 11v5"/>
                                </svg>
                            </button>

                        </div>

                        {{-- Quantity and Price --}}
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-4">

                            {{-- Quantity Control --}}
                            <div class="inline-flex items-center rounded-full border border-gray-200">

                                <button
                                    type="button"
                                    onclick="changeQuantity(-1)"
                                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100"
                                    aria-label="Decrease quantity">
                                    −
                                </button>

                                <span id="quantity"
                                      class="w-8 text-center text-sm font-medium">
                                    1
                                </span>

                                <button
                                    type="button"
                                    onclick="changeQuantity(1)"
                                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 transition hover:bg-gray-100"
                                    aria-label="Increase quantity">
                                    +
                                </button>

                            </div>

                            {{-- Price --}}
                            <div class="text-right">
                                <p class="text-xs text-gray-500">
                                    Demo price
                                </p>
                                <p id="item-total"
                                   class="font-semibold text-gray-900">
                                    Rp1.000.000
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Demo Notice --}}
                <div class="mt-5 rounded-xl bg-amber-50 px-4 py-3">
                    <p class="text-xs leading-5 text-amber-700">
                        Demo item for UI preview only. Cart data is not connected to the database.
                    </p>
                </div>

            </div>

            {{-- Empty Cart State --}}
            <div id="empty-cart"
                 class="hidden rounded-2xl border border-gray-200 bg-white px-6 py-16 text-center">

                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl">
                    🛒
                </div>

                <h2 class="mt-6 text-xl font-semibold text-gray-900">
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

                <h2 class="text-lg font-semibold text-gray-900">
                    Order Summary
                </h2>

                <div class="mt-6 space-y-4 text-sm">

                    {{-- Subtotal --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span id="subtotal" class="font-medium text-gray-900">
                            Rp1.000.000
                        </span>
                    </div>

                    {{-- Shipping --}}
                    <div class="flex justify-between gap-4">
                        <span class="text-gray-500">Shipping</span>
                        <span class="font-medium text-gray-900">
                            Calculated at checkout
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900">
                                Total
                            </span>

                            <span id="total"
                                  class="text-lg font-bold text-gray-900">
                                Rp1.000.000
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Checkout --}}
                <a id="checkout-button"
                   href="{{ route('buyer.checkout') }}"
                   class="mt-6 block w-full rounded-full bg-black px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800">
                    Proceed to Checkout
                </a>

                <p id="checkout-note"
                   class="mt-3 text-center text-xs text-gray-400">
                    Demo cart — checkout is not connected to a real order.
                </p>

                {{-- Continue Shopping --}}
                <a href="{{ route('products') }}"
                   class="mt-4 block text-center text-sm font-medium text-gray-600 transition hover:text-black">
                    Continue Shopping
                </a>

            </div>
        </div>

    </div>

</div>

{{-- Demo Cart Interaction --}}
<script>
    let quantity = 1;
    const demoPrice = 1000000;
    let cartHasItem = true;

    function formatPrice(amount) {
        return 'Rp' + amount.toLocaleString('id-ID');
    }

    function updateCart() {
        const subtotal = quantity * demoPrice;

        document.getElementById('quantity').textContent = quantity;
        document.getElementById('item-total').textContent = formatPrice(subtotal);
        document.getElementById('subtotal').textContent = formatPrice(subtotal);
        document.getElementById('total').textContent = formatPrice(subtotal);
    }

    function changeQuantity(amount) {
        if (!cartHasItem) return;

        quantity += amount;

        if (quantity < 1) {
            quantity = 1;
        }

        updateCart();
    }

    function removeCartItem() {
        cartHasItem = false;

        document.getElementById('cart-item').classList.add('hidden');
        document.getElementById('empty-cart').classList.remove('hidden');

        document.getElementById('subtotal').textContent = 'Rp0';
        document.getElementById('total').textContent = 'Rp0';

        document.getElementById('checkout-button').classList.add('pointer-events-none', 'bg-gray-200', 'text-gray-400');
        document.getElementById('checkout-button').classList.remove('bg-black', 'text-white', 'hover:bg-gray-800');

        document.getElementById('checkout-note').textContent = 'Add items to your cart to continue.';
    }
</script>

@endsection
