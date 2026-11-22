
@extends('layouts.app')

@section('title', 'My Dashboard - iStore')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Welcome Header --}}
    <div class="mb-10">
        <p class="text-sm text-gray-500">
            Buyer Dashboard
        </p>

        <h1 class="mt-2 text-3xl font-bold tracking-tight">
            Welcome back, {{ auth()->user()->name }}!
        </h1>

        <p class="mt-2 text-gray-500">
            Manage your orders and account from here.
        </p>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Orders --}}
        <a href="/buyer/orders"
           class="rounded-2xl border border-gray-200 p-6 transition hover:shadow-md">

            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-2xl">
                📦
            </div>

            <h2 class="font-semibold">
                My Orders
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Track and manage your purchases.
            </p>

            <span class="mt-4 inline-block text-sm font-medium">
                View Orders →
            </span>

        </a>

        {{-- Profile --}}
        <a href="/buyer/profile"
           class="rounded-2xl border border-gray-200 p-6 transition hover:shadow-md">

            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-2xl">
                👤
            </div>

            <h2 class="font-semibold">
                My Profile
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Update your personal information.
            </p>

            <span class="mt-4 inline-block text-sm font-medium">
                Edit Profile →
            </span>

        </a>

        {{-- Shopping --}}
        <a href="/products"
           class="rounded-2xl border border-gray-200 p-6 transition hover:shadow-md">

            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-2xl">
                🛍️
            </div>

            <h2 class="font-semibold">
                Continue Shopping
            </h2>

            <p class="mt-2 text-sm text-gray-500">
                Discover your next Apple device.
            </p>

            <span class="mt-4 inline-block text-sm font-medium">
                Explore Products →
            </span>

        </a>

    </div>

    {{-- Recent Orders --}}
    <div class="rounded-2xl border border-gray-200 bg-white">

        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">

            <h2 class="text-lg font-semibold">
                Recent Orders
            </h2>

            <a href="/buyer/orders"
               class="text-sm font-medium hover:underline">
                View All
            </a>

        </div>

        {{-- Empty State --}}
        <div class="px-6 py-16 text-center">

            <div class="mb-4 text-5xl">
                📦
            </div>

            <h3 class="font-semibold">
                No orders yet
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Your recent purchases will appear here.
            </p>

            <x-button href="/products" class="mt-6">
                Start Shopping
            </x-button>

        </div>

    </div>

</div>

@endsection
