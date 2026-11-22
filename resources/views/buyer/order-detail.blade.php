
@extends('layouts.app')

@section('title', 'Order Details - iStore')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <div class="mb-8 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('buyer.orders') }}" class="transition hover:text-black">
            My Orders
        </a>
        <span>/</span>
        <span class="font-medium text-gray-900">Order Details</span>
    </div>

    {{-- Header --}}
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Order Details
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Review your order information and status.
            </p>
        </div>

        <a href="{{ route('buyer.orders') }}"
           class="inline-flex items-center justify-center rounded-full border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Back to Orders
        </a>
    </div>

    {{-- Order Status --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-gray-500">Order Number</p>
                <h2 class="mt-1 text-lg font-semibold text-gray-900">
                    #ORD-000000
                </h2>
            </div>

            <div>
                <p class="text-sm text-gray-500">Order Date</p>
                <p class="mt-1 text-sm font-medium text-gray-900">
                    -
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Order Status</p>
                <span class="mt-2 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                    Pending
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500">Payment Status</p>
                <span class="mt-2 inline-flex rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700">
                    Unpaid
                </span>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Main Content --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Order Items --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Order Items
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Products included in this order
                </p>

                <div class="mt-6">

                    {{-- Product --}}
                    <div class="flex flex-col gap-4 border-b border-gray-100 pb-6 sm:flex-row">

                        {{-- Product Image --}}
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
                            <h3 class="font-medium text-gray-900">
                                Product Name
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Condition: Used
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Quantity: 1
                            </p>
                        </div>

                        <div class="sm:text-right">
                            <p class="text-sm font-semibold text-gray-900">
                                Rp0
                            </p>
                        </div>

                    </div>

                    {{-- Empty placeholder notice --}}
                    <div class="mt-5 rounded-xl bg-gray-50 p-4">
                        <p class="text-sm text-gray-500">
                            Order item details will appear here once connected to the order system.
                        </p>
                    </div>

                </div>
            </div>

            {{-- Shipping Information --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6">

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
                            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Shipping Information
                        </h2>
                        <p class="text-sm text-gray-500">
                            Delivery address for this order
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Receiver</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            -
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Phone Number</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            -
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Address</p>
                        <p class="mt-1 text-sm text-gray-700">
                            Shipping address will appear here.
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Order Notes</p>
                        <p class="mt-1 text-sm text-gray-700">
                            No additional notes.
                        </p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">

            <div class="rounded-2xl border border-gray-200 bg-white p-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Order Summary
                </h2>

                <div class="mt-6 space-y-3">

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">
                            Rp0
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Shipping Fee</span>
                        <span class="font-medium text-gray-900">
                            Rp0
                        </span>
                    </div>

                </div>

                <div class="mt-5 border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-gray-900">
                            Total
                        </span>

                        <span class="text-xl font-bold text-gray-900">
                            Rp0
                        </span>
                    </div>
                </div>

                {{-- Actions --}}
                <button
                    type="button"
                    disabled
                    class="mt-6 w-full cursor-not-allowed rounded-full bg-gray-200 px-6 py-3 text-sm font-semibold text-gray-400">
                    Pay Now
                </button>

                <button
                    type="button"
                    disabled
                    class="mt-3 w-full cursor-not-allowed rounded-full border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-400">
                    Cancel Order
                </button>

                <p class="mt-4 text-center text-xs text-gray-400">
                    Order actions will be available once the backend is connected.
                </p>

            </div>

        </div>

    </div>

</div>
@endsection
