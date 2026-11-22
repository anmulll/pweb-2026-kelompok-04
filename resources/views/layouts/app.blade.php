<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', $title ?? 'iStore — Retailer Independen Apple')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-white text-gray-900 antialiased">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}"
               class="text-xl font-bold tracking-tight text-gray-900">
                iStore
            </a>

            {{-- Navigation --}}
            <div class="flex flex-wrap items-center gap-4 text-sm sm:gap-6">

                <a href="{{ route('home') }}"
                   class="text-gray-700 transition hover:text-black">
                    Beranda
                </a>

                <a href="{{ route('catalog.index') }}"
                   class="text-gray-700 transition hover:text-black">
                    Katalog
                </a>

                <a href="{{ route('catalog.index', ['condition' => 'used']) }}"
                   class="hidden text-gray-700 transition hover:text-black md:inline">
                    Unit Second
                </a>

                {{-- Guest --}}
                @guest
                    <a href="{{ route('login') }}"
                       class="text-gray-700 transition hover:text-black">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-full bg-black px-4 py-2 text-white transition hover:bg-gray-800">
                        Daftar
                    </a>
                @endguest

                {{-- Authenticated User --}}
                @auth
                    {{-- Buyer --}}
                    @if (auth()->user()->isBuyer())
                        <a href="{{ route('buyer.dashboard') }}"
                           class="text-gray-700 transition hover:text-black">
                            Dashboard
                        </a>

                        <a href="{{ route('buyer.orders.index') }}"
                           class="text-gray-700 transition hover:text-black">
                            Pesanan
                        </a>

                        <a href="{{ route('buyer.cart.index') }}"
                           class="text-gray-700 transition hover:text-black">
                            Keranjang
                        </a>

                        <a href="{{ route('buyer.profile') }}"
                           class="text-gray-700 transition hover:text-black">
                            Profil
                        </a>
                    @endif

                    {{-- Seller --}}
                    @if (auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}"
                           class="text-gray-700 transition hover:text-black">
                            Dashboard
                        </a>

                        <a href="{{ route('seller.products.index') }}"
                           class="text-gray-700 transition hover:text-black">
                            Panel Seller
                        </a>
                    @endif

                    {{-- User Name --}}
                    <span class="hidden font-medium text-gray-700 sm:inline">
                        {{ auth()->user()->name }}
                    </span>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                                class="rounded-full border border-gray-300 px-4 py-2 text-gray-800 transition hover:bg-gray-100">
                            Keluar
                        </button>
                    </form>
                @endauth

            </div>
        </nav>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow">
        @if (session('success'))
            <div class="mx-auto max-w-7xl px-6 pt-4">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mx-auto max-w-7xl px-6 pt-4">
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto max-w-7xl px-6 pt-4">
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    {{-- Footer with Mandatory Independent Retailer Disclaimer --}}
    <footer class="mt-16 border-t border-gray-200 bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl space-y-2 px-6 text-center text-xs text-gray-500">
            <p class="font-medium text-gray-700">
                iStore — Retailer Independen Perangkat Apple Baru &amp; Second Terinspeksi
            </p>
            <p class="mx-auto max-w-2xl leading-relaxed">
                iStore adalah retailer independen perangkat Apple, bukan Apple Store resmi, dan tidak berafiliasi dengan Apple Inc. Seluruh merek dagang dan hak cipta milik Apple Inc.
            </p>
            <p>
                &copy; {{ date('Y') }} iStore. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

</body>
</html>
