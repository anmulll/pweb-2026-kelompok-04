
@extends('layouts.app')

@section('title', 'Products - iStore')

@section('content')

@php
    $products = [
        [
            'name' => 'iPhone 17 Pro',
            'specification' => '256GB · Silver',
            'price' => 21999000,
            'condition' => 'New',
            'grade' => '',
            'category' => 'iphone',
            'href' => '/products/1',
        ],
        [
            'name' => 'iPhone 15',
            'specification' => '128GB · Black',
            'price' => 10499000,
            'condition' => 'Used',
            'grade' => 'A',
            'category' => 'iphone',
            'href' => '/products/2',
        ],
        [
            'name' => 'MacBook Air',
            'specification' => 'M4 · 16GB · 256GB',
            'price' => 18499000,
            'condition' => 'New',
            'grade' => '',
            'category' => 'mac',
            'href' => '/products/3',
        ],
        [
            'name' => 'iPad Air',
            'specification' => 'M2 · 128GB · Wi-Fi',
            'price' => 8499000,
            'condition' => 'Used',
            'grade' => 'A',
            'category' => 'ipad',
            'href' => '/products/4',
        ],
    ];

    $category = request('category');
    $condition = request('condition');
    $grade = request('grade');
    $search = strtolower(request('search', ''));
    $minPrice = request('min_price');
    $maxPrice = request('max_price');
    $sort = request('sort');

    $filteredProducts = collect($products)->filter(function ($product) use (
        $category, $condition, $grade, $search, $minPrice, $maxPrice
    ) {
        if ($category && $product['category'] !== $category) {
            return false;
        }

        if ($condition && strtolower($product['condition']) !== $condition) {
            return false;
        }

        if ($grade && $product['grade'] !== $grade) {
            return false;
        }

        if ($search && !str_contains(strtolower($product['name'] . ' ' . $product['specification']), $search)) {
            return false;
        }

        if ($minPrice !== null && $minPrice !== '' && $product['price'] < $minPrice) {
            return false;
        }

        if ($maxPrice !== null && $maxPrice !== '' && $product['price'] > $maxPrice) {
            return false;
        }

        return true;
    });

    if ($sort === 'price_asc') {
        $filteredProducts = $filteredProducts->sortBy('price');
    } elseif ($sort === 'price_desc') {
        $filteredProducts = $filteredProducts->sortByDesc('price');
    }

    $filteredProducts = $filteredProducts->values();
@endphp

<section class="mx-auto max-w-7xl px-6 py-12">

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-4xl font-bold tracking-tight">Explore Products</h1>
        <p class="mt-3 text-gray-500">
            Find the perfect Apple device for you.
        </p>
    </div>

    {{-- Search --}}
    <div class="mb-8">
        <form action="{{ route('products') }}" method="GET">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <input type="hidden" name="condition" value="{{ request('condition') }}">
            <input type="hidden" name="grade" value="{{ request('grade') }}">
            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">

            <x-input
                name="search"
                placeholder="Search iPhone, iPad, Mac..."
                value="{{ request('search') }}"
            />
        </form>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

        {{-- Filter Sidebar --}}
        <aside class="rounded-2xl border border-gray-200 p-6">

            <h2 class="mb-6 text-lg font-semibold">Filters</h2>

            <form action="{{ route('products') }}" method="GET" class="space-y-6">

                {{-- Search preservation --}}
                <input type="hidden" name="search" value="{{ request('search') }}">

                {{-- Category --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Category</label>

                    <select name="category" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="" @selected(!$category)>All Categories</option>
                        <option value="iphone" @selected($category === 'iphone')>iPhone</option>
                        <option value="ipad" @selected($category === 'ipad')>iPad</option>
                        <option value="mac" @selected($category === 'mac')>Mac</option>
                    </select>
                </div>

                {{-- Condition --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Condition</label>

                    <select name="condition" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="" @selected(!$condition)>All Conditions</option>
                        <option value="new" @selected($condition === 'new')>New</option>
                        <option value="used" @selected($condition === 'used')>Used</option>
                    </select>
                </div>

                {{-- Grade --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Grade</label>

                    <select name="grade" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="" @selected(!$grade)>All Grades</option>
                        <option value="A" @selected($grade === 'A')>Grade A</option>
                        <option value="B" @selected($grade === 'B')>Grade B</option>
                        <option value="C" @selected($grade === 'C')>Grade C</option>
                    </select>
                </div>

                {{-- Minimum Price --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Minimum Price</label>

                    <input
                        type="number"
                        name="min_price"
                        value="{{ request('min_price') }}"
                        placeholder="Rp 0"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 p-3 text-sm"
                    >
                </div>

                {{-- Maximum Price --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Maximum Price</label>

                    <input
                        type="number"
                        name="max_price"
                        value="{{ request('max_price') }}"
                        placeholder="Rp 50.000.000"
                        min="0"
                        class="w-full rounded-lg border border-gray-300 p-3 text-sm"
                    >
                </div>

                {{-- Sorting --}}
                <div>
                    <label class="mb-2 block text-sm font-medium">Sort By</label>

                    <select name="sort" class="w-full rounded-lg border border-gray-300 p-3 text-sm">
                        <option value="" @selected(!$sort)>Default</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Price: High to Low</option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-full bg-black px-5 py-3 text-sm font-medium text-white transition hover:bg-gray-800"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('products') }}"
                    class="block text-center text-sm text-gray-500 hover:text-black"
                >
                    Reset Filters
                </a>

            </form>

        </aside>

        {{-- Product Grid --}}
        <div class="lg:col-span-3">

            <div class="mb-6 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    {{ $filteredProducts->count() }} products found
                </p>
            </div>

            {{-- Products --}}
            @if ($filteredProducts->isNotEmpty())

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">

                    @foreach ($filteredProducts as $product)

                        <x-product-card
                            name="{{ $product['name'] }}"
                            specification="{{ $product['specification'] }}"
                            price="Rp {{ number_format($product['price'], 0, ',', '.') }}"
                            condition="{{ $product['condition'] }}"
                            grade="{{ $product['grade'] }}"
                            href="{{ $product['href'] }}"
                        />

                    @endforeach

                </div>

            @else

                {{-- Empty State --}}
                <div class="rounded-2xl border border-gray-200 px-6 py-16 text-center">

                    <div class="mb-4 text-4xl">🔍</div>

                    <h2 class="text-lg font-semibold">No products found</h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Try adjusting your search or filters.
                    </p>

                    <a
                        href="{{ route('products') }}"
                        class="mt-6 inline-block rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800"
                    >
                        Reset Filters
                    </a>

                </div>

            @endif

        </div>

    </div>

</section>

@endsection
