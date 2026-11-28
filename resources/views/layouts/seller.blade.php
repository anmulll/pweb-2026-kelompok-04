<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — Konsol Penjual iStore' : 'Konsol Penjual iStore' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#09090B] text-zinc-100 antialiased overflow-x-hidden selection:bg-blue-500/30 selection:text-white">
    @php
        $sellerName = auth()->user()->name ?? 'Akun Penjual';
        $sellerEmail = auth()->user()->email ?? 'seller@istore.local';
        $sellerInitial = strtoupper(substr($sellerName, 0, 1));

        $navItems = [
            [
                'label' => 'Ringkasan',
                'route' => 'seller.dashboard',
                'fallback' => '#',
                'pattern' => 'seller.dashboard',
            ],
            [
                'label' => 'Kategori',
                'route' => 'seller.categories.index',
                'fallback' => '#',
                'pattern' => 'seller.categories.*',
            ],
            [
                'label' => 'Produk',
                'route' => 'seller.products.index',
                'fallback' => '#',
                'pattern' => 'seller.products.*',
            ],
            [
                'label' => 'Promosi',
                'route' => 'seller.promotions.index',
                'fallback' => '#',
                'pattern' => 'seller.promotions.*',
            ],
            [
                'label' => 'Pesanan',
                'route' => 'seller.orders.index',
                'fallback' => '#',
                'pattern' => 'seller.orders.*',
            ],
            [
                'label' => 'Pembayaran',
                'route' => 'seller.payments.index',
                'fallback' => '#',
                'pattern' => 'seller.payments.*',
            ],
            [
                'label' => 'Laporan',
                'route' => 'seller.reports.index',
                'fallback' => '#',
                'pattern' => 'seller.reports.*',
            ],
        ];
    @endphp

    <div class="relative min-h-screen w-full flex flex-col">
        {{-- Ambient Blue Glow (Matching Storefront & Buyer Theme) --}}
        <div class="pointer-events-none fixed -top-40 left-1/2 -z-10 h-[420px] w-[720px] -translate-x-1/2 rounded-full bg-blue-600/12 blur-[140px]"></div>

        {{-- Top Header --}}
        <header class="sticky top-0 z-30 w-full h-16 glass-surface border-b border-white/10">
            <div class="w-full h-full px-4 sm:px-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        id="seller-drawer-open"
                        aria-controls="seller-mobile-drawer"
                        aria-expanded="false"
                        aria-label="Buka menu navigasi"
                        class="lg:hidden inline-flex items-center justify-center rounded-xl p-2 text-zinc-300 hover:text-white hover:bg-zinc-800/80 border border-white/10 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <a href="{{ Route::has('seller.dashboard') ? route('seller.dashboard') : url('/') }}" class="flex items-center gap-2 text-lg font-bold tracking-tight text-white min-w-0">
                        <span class="text-blue-500"></span>
                        <span class="truncate">iStore</span>
                        <span class="hidden sm:inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium bg-blue-500/15 text-blue-300 border border-blue-500/30">
                            Panel Seller
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 shrink-0">
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center rounded-xl border border-white/10 bg-[#18181B] px-3.5 py-2 text-xs font-medium text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                        Kunjungi Toko
                    </a>

                    <div class="hidden md:flex items-center gap-2.5 pl-1">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600 text-xs font-bold text-white">
                            {{ $sellerInitial }}
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-semibold text-zinc-100 leading-tight">{{ $sellerName }}</div>
                            <div class="text-[11px] text-zinc-400 leading-tight">{{ $sellerEmail }}</div>
                        </div>
                    </div>

                    @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-3.5 py-2 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 hover:text-white border border-white/10 transition cursor-pointer"
                            >
                                Keluar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </header>

        {{-- Body Container (Sidebar + Main Content) --}}
        <div class="flex-1 w-full flex min-w-0">
            {{-- Desktop Sidebar --}}
            <aside class="hidden lg:flex lg:w-64 lg:shrink-0 flex-col bg-[#111113]/85 backdrop-blur border-r border-white/10 p-4">
                <div class="mb-5 rounded-2xl border border-white/10 bg-[#18181B]/70 p-3.5 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">
                        {{ $sellerInitial }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-white truncate">{{ $sellerName }}</div>
                        <div class="text-[11px] text-zinc-400 truncate">{{ $sellerEmail }}</div>
                    </div>
                </div>

                <div class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                    Menu Pengelolaan
                </div>

                <nav aria-label="Navigasi utama penjual" class="space-y-1">
                    @foreach ($navItems as $item)
                        @php
                            $href = Route::has($item['route']) ? route($item['route']) : $item['fallback'];
                            $isActive = Route::has($item['route']) && request()->routeIs($item['pattern']);
                        @endphp
                        <x-seller.nav-link :href="$href" :active="$isActive">
                            {{ $item['label'] }}
                        </x-seller.nav-link>
                    @endforeach
                </nav>
            </aside>

            {{-- Main Content Column --}}
            <div class="flex-1 min-w-0 w-full flex flex-col">
                <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl glass-surface border border-white/15 px-4 py-3 text-sm text-zinc-100" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-300" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-300" role="alert">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </main>

                {{-- Footer --}}
                <footer class="w-full border-t border-white/10 bg-[#111113]/80 px-4 sm:px-6 lg:px-8 py-5 text-xs text-zinc-400">
                    <div class="flex flex-col gap-1.5 sm:flex-row sm:items-center sm:justify-between">
                        <span class="font-medium text-zinc-300">iStore — Portal Pengelolaan Toko &amp; Katalog</span>
                        <span class="text-zinc-500">iStore merupakan peritel independen dan tidak berafiliasi secara resmi dengan Apple Inc.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    {{-- Mobile Drawer --}}
    <div
        id="seller-mobile-drawer"
        class="fixed inset-0 z-50 hidden lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Menu navigasi penjual"
    >
        <div id="seller-drawer-backdrop" class="fixed inset-0 bg-black/75 backdrop-blur-sm"></div>

        <div
            id="seller-drawer-panel"
            tabindex="-1"
            class="relative z-10 w-72 max-w-[85vw] h-full bg-[#111113] border-r border-white/10 p-4 flex flex-col justify-between overflow-y-auto focus:outline-none"
        >
            <div>
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-blue-500 text-lg"></span>
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-white truncate">iStore Seller</div>
                            <div class="text-xs text-zinc-400 truncate">{{ $sellerName }}</div>
                        </div>
                    </div>
                    <button
                        type="button"
                        id="seller-drawer-close"
                        aria-label="Tutup menu navigasi"
                        class="inline-flex items-center justify-center rounded-xl p-2 text-zinc-300 hover:text-white hover:bg-zinc-800 border border-white/10 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav aria-label="Navigasi seluler penjual" class="space-y-1">
                    @foreach ($navItems as $item)
                        @php
                            $href = Route::has($item['route']) ? route($item['route']) : $item['fallback'];
                            $isActive = Route::has($item['route']) && request()->routeIs($item['pattern']);
                        @endphp
                        <x-seller.nav-link :href="$href" :active="$isActive">
                            {{ $item['label'] }}
                        </x-seller.nav-link>
                    @endforeach
                </nav>
            </div>

            <div class="pt-4 mt-6 border-t border-white/10 space-y-2 text-xs text-zinc-400">
                <a href="{{ route('home') }}" class="block text-blue-400 hover:text-blue-300 transition">Kunjungi Toko</a>
                <div class="truncate">{{ $sellerEmail }}</div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const drawer = document.getElementById('seller-mobile-drawer');
            const panel = document.getElementById('seller-drawer-panel');
            const openBtn = document.getElementById('seller-drawer-open');
            const closeBtn = document.getElementById('seller-drawer-close');
            const backdrop = document.getElementById('seller-drawer-backdrop');

            if (!drawer || !panel || !openBtn || !closeBtn || !backdrop) return;

            function openDrawer() {
                drawer.classList.remove('hidden');
                openBtn.setAttribute('aria-expanded', 'true');
                requestAnimationFrame(() => panel.focus());
            }

            function closeDrawer() {
                drawer.classList.add('hidden');
                openBtn.setAttribute('aria-expanded', 'false');
                openBtn.focus();
            }

            openBtn.addEventListener('click', openDrawer);
            closeBtn.addEventListener('click', closeDrawer);
            backdrop.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !drawer.classList.contains('hidden')) {
                    closeDrawer();
                }
            });
        })();
    </script>
</body>
</html>
