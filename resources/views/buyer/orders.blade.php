
@extends('layouts.app')

@section('title', 'My Orders - iStore')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('buyer.dashboard') }}"
           class="text-sm text-gray-500 hover:text-black">
            ← Back to Dashboard
        </a>

        <h1 class="mt-4 text-3xl font-bold tracking-tight">
            My Orders
        </h1>

        <p class="mt-2 text-gray-500">
            Track and manage your purchases.
        </p>
    </div>

    {{-- Order Tabs --}}
    <div class="mb-6 flex flex-wrap gap-3">
        <button class="rounded-full bg-black px-5 py-2.5 text-sm font-medium text-white">
            All Orders
        </button>

        <button class="rounded-full border border-gray-300 px-5 py-2.5 text-sm text-gray-600">
            Processing
        </button>

        <button class="rounded-full border border-gray-300 px-5 py-2.5 text-sm text-gray-600">
            Completed
        </button>

        <button class="rounded-full border border-gray-300 px-5 py-2.5 text-sm text-gray-600">
            Cancelled
        </button>
    </div>

    {{-- Empty State --}}
    <div class="rounded-2xl border border-gray-200 bg-white px-6 py-16 text-center">

        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-4xl">
            📦
        </div>

        <h2 class="mt-6 text-xl font-semibold">
            No orders yet
        </h2>

        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
            Your purchases will appear here once you place your first order.
            Start exploring our products and find something you love.
        </p>

        <div class="mt-6">
            <a href="{{ route('products') }}"
               class="inline-flex items-center justify-center rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
                Continue Shopping
            </a>
        </div>

    </div>

</div>
@endsection
