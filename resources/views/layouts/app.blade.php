<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'iStore — Retailer Independen Apple')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-[#09090B] text-zinc-100 antialiased selection:bg-blue-500/30 selection:text-white">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 glass-surface border-b border-white/10">
        <nav class="mx-auto flex max-w-[1200px] flex-wrap items-center justify-between gap-4 px-4 py-3.5 sm:px-6">

            {{-- Brand --}}
            <a href="{{ route('home') }}"
               class="flex items-center gap-2 text-lg font-bold tracking-tight text-white">
                <span class="text-blue-500"></span>
                <span>iStore</span>
            </a>

            {{-- Center Storefront Links --}}
            <div class="hidden items-center gap-6 text-sm text-zinc-300 md:flex">
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'font-medium text-white' : 'hover:text-white' }} transition">
                    Beranda
                </a>

                <a href="{{ route('catalog.index') }}"
                   class="{{ request()->routeIs('catalog.index') && request('condition') !== 'used' ? 'font-medium text-white' : 'hover:text-white' }} transition">
                    Katalog Perangkat
                </a>

                <a href="{{ route('catalog.index', ['condition' => 'used']) }}"
                   class="{{ request()->routeIs('catalog.index') && request('condition') === 'used' ? 'font-medium text-white' : 'hover:text-white' }} transition">
                    Unit Second Terinspeksi
                </a>
            </div>

            {{-- Right Navigation --}}
            <div class="flex flex-wrap items-center gap-2.5 text-xs sm:gap-3 sm:text-sm">
                <a href="{{ route('catalog.index') }}"
                   class="text-zinc-300 transition hover:text-white md:hidden">
                    Katalog
                </a>

                @guest
                    <a href="{{ route('login') }}"
                       class="rounded-xl px-3.5 py-2 text-zinc-300 transition hover:bg-zinc-800/80 hover:text-white">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-500">
                        Daftar
                    </a>
                @endguest

                @auth
                    @if (auth()->user()->isBuyer())
                        <a href="{{ route('buyer.dashboard') }}"
                           class="{{ request()->routeIs('buyer.dashboard') ? 'text-white font-medium' : 'text-zinc-300 hover:text-white' }} transition">
                            Dashboard
                        </a>

                        <a href="{{ route('buyer.cart.index') }}"
                           class="{{ request()->routeIs('buyer.cart.*') ? 'text-white font-medium' : 'text-zinc-300 hover:text-white' }} transition">
                            Keranjang
                        </a>

                        <a href="{{ route('buyer.orders.index') }}"
                           class="{{ request()->routeIs('buyer.orders.*') ? 'text-white font-medium' : 'text-zinc-300 hover:text-white' }} transition">
                            Pesanan
                        </a>

                        <a href="{{ route('buyer.profile') }}"
                           class="{{ request()->routeIs('buyer.profile*') ? 'text-white font-medium' : 'text-zinc-300 hover:text-white' }} transition">
                            Profil
                        </a>
                    @endif

                    @if (auth()->user()->isSeller())
                        <a href="{{ route('seller.dashboard') }}"
                           class="rounded-xl border border-white/10 bg-zinc-800/90 px-3.5 py-2 text-xs font-medium text-white transition hover:bg-zinc-700">
                            Panel Seller
                        </a>
                    @endif

                    <span class="hidden text-xs font-medium text-zinc-400 lg:inline">
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                                class="cursor-pointer rounded-xl border border-white/10 bg-[#18181B] px-3.5 py-2 text-xs font-medium text-zinc-200 transition hover:bg-zinc-800 hover:text-white">
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
            <div class="mx-auto max-w-[1200px] px-4 pt-5 sm:px-6">
                <div class="rounded-2xl border border-emerald-500/30 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mx-auto max-w-[1200px] px-4 pt-5 sm:px-6">
                <div class="rounded-2xl border border-amber-500/30 bg-amber-950/50 px-4 py-3 text-sm text-amber-300">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto max-w-[1200px] px-4 pt-5 sm:px-6">
                <div class="rounded-2xl border border-rose-500/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-300">
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
    <footer class="mt-16 border-t border-white/10 bg-[#111113]/80 py-8 text-xs text-zinc-400">
        <div class="mx-auto max-w-[1200px] space-y-2.5 px-4 text-center sm:px-6">
            <p class="font-medium text-zinc-200">
                iStore — Retailer Independen Perangkat Apple Baru &amp; Second Terinspeksi
            </p>
            <p class="mx-auto max-w-2xl leading-relaxed text-zinc-500">
                iStore adalah retailer independen perangkat Apple, bukan Apple Store resmi, dan tidak berafiliasi dengan Apple Inc. Seluruh merek dagang dan hak cipta milik Apple Inc.
            </p>
            <p class="text-zinc-600">
                &copy; {{ date('Y') }} iStore. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>

</body>
</html>
