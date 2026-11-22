@extends('layouts.app')

@section('title', 'Products - iStore')

@section('content')

<section class="mx-auto max-w-7xl px-6 py-12">

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-4xl font-bold tracking-tight">Explore Products</h1>
        <p class="mt-3 text-gray-500">
            Find the perfect Apple device for you.
        </p>
    </div>

    {{-- Search --}}
    <form action="/products" method="GET" class="mb-8">
        <x-input
            name="search"
            placeholder="Search iPhone, iPad, Mac..."
            value="{{ request('search') }}"
        />
    </form>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

        {{-- Filter Sidebar --}}
        <aside class="rounded-2xl border border-gray-200 p-6">

            <h2 class="mb-6 text-lg font-semibold">Filters</h2>

            <form action="/products" method="GET" class="space-y-6">

                <div>
                    <label class="mb-2 block text-sm font-medium">Category</label>
                    <select name="category" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="">All Categories</option>
                        <option value="iphone">iPhone</option>
                        <option value="ipad">iPad</option>
                        <option value="mac">Mac</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Condition</label>
                    <select name="condition" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="">All Conditions</option>
                        <option value="new">New</option>
                        <option value="used">Used</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Grade</label>
                    <select name="grade" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="">All Grades</option>
                        <option value="A">Grade A</option>
                        <option value="B">Grade B</option>
                        <option value="C">Grade C</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Minimum Price</label>
                    <input type="number" name="min_price" placeholder="Rp 0"
                        class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">Maximum Price</label>
                    <input type="number" name="max_price" placeholder="Rp 50.000.000"
                        class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                </div>

                <x-button class="w-full">
                    Apply Filters
                </x-button>

                <a href="/products" class="block text-center text-sm text-gray-500 hover:text-black">
                    Reset Filters
                </a>

            </form>

        </aside>

        {{-- Product Grid --}}
        <div class="lg:col-span-3">

            <div class="mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">Featured Products</p>

                <select class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <option>Sort by</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
            </div>

            {{-- Products --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">

                <x-product-card
                    name="iPhone 17 Pro"
                    specification="256GB · Silver"
                    price="Rp 21.999.000"
                    condition="New"
                    href="/products/1"
                />

                <x-product-card
                    name="iPhone 15"
                    specification="128GB · Black"
                    price="Rp 10.499.000"
                    condition="Used"
                    grade="A"
                    href="/products/2"
                />

                <x-product-card
                    name="MacBook Air"
                    specification="M4 · 16GB · 256GB"
                    price="Rp 18.499.000"
                    condition="New"
                    href="/products/3"
                />

                <x-product-card
                    name="iPad Air"
                    specification="M2 · 128GB · Wi-Fi"
                    price="Rp 8.499.000"
                    condition="Used"
                    grade="A"
                    href="/products/4"
                />

            </div>

        </div>

    </div>

</section>

@endsection