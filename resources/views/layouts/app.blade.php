<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'iStore — Retailer Independen Apple')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-[#09090B] text-zinc-100 antialiased overflow-x-hidden selection:bg-blue-600 selection:text-white">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 glass-surface border-b border-white/10">
        <nav class="max-w-[1200px] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

            {{-- Brand Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg sm:text-xl font-bold tracking-tight text-white hover:text-zinc-200 transition">
                <span class="text-blue-500 font-semibold text-xl"></span>
                <span>iStore</span>
            </a>

            {{-- Main Navigation --}}
            <div class="hidden md:flex items-center gap-6 text-sm text-zinc-300">
                <a href="{{ route('home') }}" class="hover:text-white transition {{ request()->routeIs('home') ? 'text-white font-medium' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition {{ request()->routeIs('catalog.index') && !request()->filled('condition') ? 'text-white font-medium' : '' }}">
                    Katalog Perangkat
                </a>
                <a href="{{ route('catalog.index', ['condition' => 'used']) }}" class="hover:text-white transition {{ request()->get('condition') === 'used' ? 'text-white font-medium' : '' }}">
                    Unit Second Terinspeksi
                </a>
            </div>

            {{-- User Actions --}}
            <div class="flex items-center gap-3 text-sm">
                @guest
                    <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white transition px-3 py-1.5 text-xs sm:text-sm font-medium">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-blue-600 hover:bg-blue-500 px-3.5 py-1.5 text-xs sm:text-sm font-medium text-white transition shadow-sm">
                        Daftar
                    </a>
                @endguest

                @auth
                    @if (auth()->user()->isBuyer())
                        <div class="hidden sm:flex items-center gap-4 text-xs sm:text-sm text-zinc-300">
                            <a href="{{ route('buyer.dashboard') }}" class="hover:text-white transition {{ request()->routeIs('buyer.dashboard') ? 'text-white font-medium' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('buyer.orders.index') }}" class="hover:text-white transition {{ request()->routeIs('buyer.orders.*') ? 'text-white font-medium' : '' }}">
                                Pesanan
                            </a>
                            <a href="{{ route('buyer.cart.index') }}" class="hover:text-white transition relative {{ request()->routeIs('buyer.cart.*') ? 'text-white font-medium' : '' }}">
                                Keranjang
                            </a>
                            <a href="{{ route('buyer.profile') }}" class="hover:text-white transition {{ request()->routeIs('buyer.profile') ? 'text-white font-medium' : '' }}">
                                Profil
                            </a>
                        </div>
                    @endif

                    @if (auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}" class="rounded-xl bg-[#18181B] hover:bg-zinc-800 border border-white/10 px-3 py-1.5 text-xs sm:text-sm font-medium text-zinc-200 transition">
                            Panel Seller
                        </a>
                    @endif

                    {{-- User Greeting & Logout --}}
                    <span class="hidden lg:inline text-xs font-medium text-zinc-400 border-l border-white/10 pl-3">
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-xl border border-white/10 bg-[#18181B] hover:bg-zinc-800 px-3 py-1.5 text-xs font-medium text-zinc-300 hover:text-white transition">
                            Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </nav>

        {{-- Mobile Sub-Nav for Authenticated Buyer --}}
        @auth
            @if (auth()->user()->isBuyer())
                <div class="sm:hidden border-t border-white/5 bg-[#111113]/80 px-4 py-2 flex items-center justify-around text-xs text-zinc-300">
                    <a href="{{ route('buyer.dashboard') }}" class="{{ request()->routeIs('buyer.dashboard') ? 'text-blue-400 font-semibold' : '' }}">Dashboard</a>
                    <a href="{{ route('buyer.orders.index') }}" class="{{ request()->routeIs('buyer.orders.*') ? 'text-blue-400 font-semibold' : '' }}">Pesanan</a>
                    <a href="{{ route('buyer.cart.index') }}" class="{{ request()->routeIs('buyer.cart.*') ? 'text-blue-400 font-semibold' : '' }}">Keranjang</a>
                    <a href="{{ route('buyer.profile') }}" class="{{ request()->routeIs('buyer.profile') ? 'text-blue-400 font-semibold' : '' }}">Profil</a>
                </div>
            @endif
        @endauth
    </header>

    {{-- Main Content --}}
    <main class="flex-grow">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-4">
                <div class="rounded-xl border border-emerald-500/30 bg-emerald-950/40 p-4 text-xs sm:text-sm text-emerald-300 flex items-center gap-3">
                    <span class="text-emerald-400 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-4">
                <div class="rounded-xl border border-amber-500/30 bg-amber-950/40 p-4 text-xs sm:text-sm text-amber-300 flex items-center gap-3">
                    <span class="text-amber-400 font-bold">!</span>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 pt-4">
                <div class="rounded-xl border border-rose-500/30 bg-rose-950/40 p-4 text-xs sm:text-sm text-rose-300 flex items-center gap-3">
                    <span class="text-rose-400 font-bold">✕</span>
                    <span>{{ session('error') }}</span>
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
    <footer class="mt-16 border-t border-white/10 bg-[#111113]/60 py-10 text-zinc-400 text-xs">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 text-center space-y-3">
            <p class="font-medium text-zinc-300">
                iStore — Retailer Independen Perangkat Apple Baru &amp; Second Terinspeksi
            </p>
            <p class="text-zinc-500 max-w-2xl mx-auto leading-relaxed">
                iStore adalah retailer independen perangkat Apple, bukan Apple Store resmi, dan tidak berafiliasi dengan Apple Inc. Seluruh merek dagang dan hak cipta milik Apple Inc.
            </p>
            <p class="text-zinc-600">
                &copy; {{ date('Y') }} iStore. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

</body>
</html>
