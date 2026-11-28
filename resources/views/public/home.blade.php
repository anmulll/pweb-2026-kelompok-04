<x-layouts.app title="iStore — Retailer Independen Perangkat Apple Baru & Second Terinspeksi">
    @php
        $promoList = $promotions ?? collect();
        $categoryList = $categories ?? collect();
        $productList = $featuredProducts ?? collect();
        $heroProduct = $productList->first();
        $heroSecondary = $productList->skip(1)->first();
        $heroUsed = $productList->first(fn ($p) => method_exists($p, 'isUsed') && $p->isUsed());

        $categoryThumbnails = [
            'iphone' => asset('storage/products/iphone-15-pro-max.jpg'),
            'macbook' => asset('storage/products/macbook-pro-14.jpg'),
            'ipad' => asset('storage/products/ipad-air-m2.jpg'),
            'apple-watch' => asset('storage/products/apple-watch-s9.jpg'),
            'airpods' => asset('storage/products/airpods-pro-2.jpg'),
        ];
    @endphp

    <div class="relative overflow-hidden">
        {{-- Ambient Background Glow --}}
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[480px] w-[780px] -translate-x-1/2 rounded-full bg-blue-600/15 blur-[140px]"></div>

        <div class="mx-auto max-w-[1200px] space-y-16 px-4 py-8 sm:px-6 sm:py-12">

            {{-- 1. HERO BENTO SHOWCASE --}}
            <section class="relative overflow-hidden rounded-3xl glass-surface border border-white/10 p-6 sm:p-10 lg:p-12">
                <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-blue-500/15 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>

                <div class="relative grid items-center gap-10 lg:grid-cols-12">
                    {{-- Left Copy --}}
                    <div class="space-y-6 text-center lg:col-span-7 lg:text-left">
                        <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/25 bg-blue-500/10 px-3.5 py-1 text-xs font-medium text-blue-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                            Retailer Independen Apple • Baru &amp; Second Terinspeksi
                        </div>

                        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-[3.25rem] lg:leading-[1.08]">
                            Perangkat Apple Pilihan,
                            <span class="bg-gradient-to-r from-white via-zinc-200 to-blue-400 bg-clip-text text-transparent">
                                Transparan Tanpa Kompromi.
                            </span>
                        </h1>

                        <p class="mx-auto max-w-xl text-sm leading-relaxed text-zinc-400 sm:text-base lg:mx-0">
                            Temukan iPhone, MacBook, iPad, Apple Watch, dan AirPods original. Setiap unit second memiliki SKU tunggal dengan laporan inspeksi fisik, Battery Health asli, dan garansi toko 1 bulan.
                        </p>

                        <div class="flex flex-wrap items-center justify-center gap-3 pt-1 lg:justify-start">
                            <x-ui.button href="{{ route('catalog.index') }}" variant="primary" class="px-6 py-2.5 text-sm">
                                Jelajahi Katalog
                            </x-ui.button>
                            <x-ui.button href="{{ route('catalog.index', ['condition' => 'used']) }}" variant="secondary" class="px-6 py-2.5 text-sm">
                                Unit Second Terinspeksi
                            </x-ui.button>
                            @guest
                                <x-ui.button href="{{ route('register') }}" variant="secondary" class="px-5 py-2.5 text-sm">
                                    Daftar Pembeli
                                </x-ui.button>
                            @endguest
                        </div>

                        {{-- Key Trust Metrics --}}
                        <div class="grid grid-cols-3 gap-4 border-t border-white/10 pt-6 text-left">
                            <div>
                                <div class="text-lg font-bold text-white sm:text-xl">32 Titik</div>
                                <div class="text-[11px] text-zinc-400">Inspeksi Unit Second</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-white sm:text-xl">100%</div>
                                <div class="text-[11px] text-zinc-400">Transparansi BH &amp; Grade</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-white sm:text-xl">1 Bulan</div>
                                <div class="text-[11px] text-zinc-400">Garansi Tukar Unit</div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Visual Bento Collage --}}
                    <div class="grid grid-cols-2 gap-3.5 lg:col-span-5">
                        {{-- Primary Flagship Visual --}}
                        <a href="{{ $heroProduct ? route('products.show', $heroProduct->id) : route('catalog.index') }}"
                           class="group relative col-span-2 overflow-hidden rounded-2xl border border-white/10 bg-[#111113] aspect-[16/10]">
                            <img
                                src="{{ $heroProduct?->imageUrl() ?? asset('storage/products/iphone-15-pro-max.jpg') }}"
                                alt="{{ $heroProduct->name ?? 'iPhone 15 Pro Max' }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></div>
                            <div class="absolute bottom-3.5 left-4 right-4 flex items-end justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="inline-block rounded-md border border-blue-500/30 bg-blue-950/80 px-2 py-0.5 text-[10px] font-semibold text-blue-300">
                                        Unggulan
                                    </span>
                                    <p class="mt-1 truncate text-sm font-semibold text-white">
                                        {{ $heroProduct->name ?? 'iPhone 15 Pro Max 256GB Natural Titanium' }}
                                    </p>
                                </div>
                                @if ($heroProduct)
                                    <span class="shrink-0 rounded-lg bg-white/10 px-2.5 py-1 text-xs font-bold text-white backdrop-blur">
                                        Rp {{ number_format($heroProduct->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        {{-- Secondary Visual 1 --}}
                        <a href="{{ $heroSecondary ? route('products.show', $heroSecondary->id) : route('catalog.index') }}"
                           class="group relative overflow-hidden rounded-2xl border border-white/10 bg-[#111113] aspect-square">
                            <img
                                src="{{ $heroSecondary?->imageUrl() ?? asset('storage/products/macbook-pro-14.jpg') }}"
                                alt="{{ $heroSecondary->name ?? 'MacBook Pro 14 M3 Pro' }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-2.5 left-3 right-3">
                                <p class="truncate text-xs font-semibold text-white">
                                    {{ $heroSecondary->model ?? 'MacBook Pro 14"' }}
                                </p>
                                <span class="text-[10px] text-zinc-300">Perangkat Baru</span>
                            </div>
                        </a>

                        {{-- Secondary Visual 2 (Inspected Used Highlight) --}}
                        <a href="{{ $heroUsed ? route('products.show', $heroUsed->id) : route('catalog.index', ['condition' => 'used']) }}"
                           class="group relative overflow-hidden rounded-2xl border border-white/10 bg-[#111113] aspect-square">
                            <img
                                src="{{ $heroUsed?->imageUrl() ?? asset('storage/products/iphone-14-pro-purple.jpg') }}"
                                alt="{{ $heroUsed->name ?? 'Unit Second Terinspeksi' }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute top-2.5 left-2.5">
                                <span class="rounded-md border border-amber-500/30 bg-amber-950/85 px-2 py-0.5 text-[10px] font-semibold text-amber-300">
                                    Second Grade {{ $heroUsed && $heroUsed->condition_grade ? (is_object($heroUsed->condition_grade) ? $heroUsed->condition_grade->value : $heroUsed->condition_grade) : 'A' }}
                                </span>
                            </div>
                            <div class="absolute bottom-2.5 left-3 right-3">
                                <p class="truncate text-xs font-semibold text-white">
                                    {{ $heroUsed->model ?? 'iPhone 14 Pro' }}
                                </p>
                                <span class="text-[10px] text-emerald-300">
                                    BH {{ $heroUsed->battery_health ?? 91 }}% • Terinspeksi
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </section>

            {{-- 2. ACTIVE PROMOTIONS --}}
            @if ($promoList->isNotEmpty())
                <section class="space-y-5">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Penawaran Aktif</span>
                            <h2 class="mt-0.5 text-xl font-bold tracking-tight text-white sm:text-2xl">Promosi Spesial</h2>
                        </div>
                        <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                            Lihat Produk Promo
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($promoList as $index => $promo)
                            <div class="relative overflow-hidden rounded-2xl glass-card p-6 flex flex-col justify-between gap-4">
                                <div class="pointer-events-none absolute -right-12 -top-12 h-36 w-36 rounded-full {{ $index % 2 === 0 ? 'bg-blue-500/15' : 'bg-emerald-500/15' }} blur-2xl"></div>

                                <div class="relative space-y-2.5">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="inline-flex items-center rounded-full border border-emerald-500/30 bg-emerald-950/70 px-2.5 py-0.5 text-[11px] font-medium text-emerald-300">
                                            Berlaku s.d. {{ $promo->end_date->format('d M Y') }}
                                        </span>
                                        <span class="text-[11px] font-medium text-zinc-500">Promo Terverifikasi</span>
                                    </div>
                                    <h3 class="text-lg font-bold tracking-tight text-white">{{ $promo->title }}</h3>
                                    <p class="text-xs leading-relaxed text-zinc-400 sm:text-sm">{{ $promo->description }}</p>
                                </div>

                                @if ($promo->imageUrl())
                                    <div class="h-40 overflow-hidden rounded-xl border border-white/10">
                                        <img src="{{ $promo->imageUrl() }}" alt="{{ $promo->title }}" loading="lazy" class="h-full w-full object-cover">
                                    </div>
                                @else
                                    <div class="flex items-center justify-between border-t border-white/10 pt-3.5">
                                        <span class="text-xs text-zinc-400">Tersedia di seluruh katalog aktif</span>
                                        <x-ui.button href="{{ route('catalog.index') }}" variant="secondary" class="px-3.5 py-1.5 text-xs">
                                            Cek Katalog
                                        </x-ui.button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 3. ACTIVE CATEGORIES WITH PRODUCT PREVIEWS --}}
            @if ($categoryList->isNotEmpty())
                <section class="space-y-5">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-blue-400">Ekosistem Apple</span>
                            <h2 class="mt-0.5 text-xl font-bold tracking-tight text-white sm:text-2xl">Kategori Pilihan</h2>
                        </div>
                        <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                            Semua Kategori
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach ($categoryList as $category)
                            @php
                                $catThumb = $categoryThumbnails[$category->slug] ?? null;
                            @endphp
                            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                               class="group glass-card overflow-hidden rounded-2xl p-4 flex flex-col items-center text-center">
                                <div class="mb-3 h-24 w-24 overflow-hidden rounded-2xl border border-white/10 bg-[#18181B] flex items-center justify-center">
                                    @if ($catThumb)
                                        <img src="{{ $catThumb }}" alt="{{ $category->name }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                                    @else
                                        <span class="text-3xl text-zinc-400 group-hover:text-blue-400 transition"></span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-semibold text-white group-hover:text-blue-400 transition">
                                    {{ $category->name }}
                                </h3>
                                <span class="mt-0.5 text-[11px] text-zinc-500">
                                    {{ $category->products_count }} Perangkat Aktif
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 4. FEATURED PRODUCTS BENTO GRID --}}
            <section class="space-y-5">
                <div class="flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-blue-400">Kurasi Terbaik</span>
                        <h2 class="mt-0.5 text-xl font-bold tracking-tight text-white sm:text-2xl">Produk Unggulan</h2>
                        <p class="mt-1 text-xs text-zinc-400">Pilihan unit Baru &amp; Second terinspeksi siap kirim hari ini</p>
                    </div>
                    <x-ui.button href="{{ route('catalog.index') }}" variant="secondary" class="px-4 py-2 text-xs">
                        Lihat Semua Produk
                    </x-ui.button>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @forelse ($productList as $product)
                        <div class="group glass-card flex flex-col justify-between overflow-hidden rounded-2xl">
                            <a href="{{ route('products.show', $product->id) }}"
                               class="relative block aspect-square overflow-hidden border-b border-white/10 bg-[#18181B]">
                                <x-product-image
                                    :src="$product->imageUrl()"
                                    :alt="$product->name"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                />
                                <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
                                    @if ($product->isUsed())
                                        <span class="rounded-md border border-amber-500/35 bg-amber-950/85 px-2 py-0.5 text-[10px] font-semibold text-amber-300 backdrop-blur">
                                            Second
                                        </span>
                                        @if ($product->condition_grade)
                                            <span class="rounded-md border border-white/15 bg-zinc-900/85 px-2 py-0.5 text-[10px] font-semibold text-zinc-200 backdrop-blur">
                                                Grade {{ is_object($product->condition_grade) ? $product->condition_grade->value : $product->condition_grade }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="rounded-md border border-blue-500/35 bg-blue-950/85 px-2 py-0.5 text-[10px] font-semibold text-blue-300 backdrop-blur">
                                            Baru
                                        </span>
                                    @endif
                                </div>

                                @if ($product->isUsed() && $product->battery_health)
                                    <div class="absolute bottom-2.5 right-2.5 rounded-md border border-emerald-500/30 bg-black/75 px-2 py-0.5 text-[10px] font-medium text-emerald-300 backdrop-blur">
                                        BH {{ $product->battery_health }}%
                                    </div>
                                @endif
                            </a>

                            <div class="flex flex-grow flex-col justify-between space-y-3 p-4">
                                <div class="space-y-1">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-500">
                                        {{ $product->category->name ?? 'Apple' }}
                                    </span>
                                    <a href="{{ route('products.show', $product->id) }}" class="block">
                                        <h3 class="line-clamp-2 text-sm font-semibold text-white transition group-hover:text-blue-400">
                                            {{ $product->name }}
                                        </h3>
                                    </a>
                                    <p class="line-clamp-1 text-xs text-zinc-400">
                                        {{ $product->model }}{{ $product->storage ? ' • ' . $product->storage : '' }}{{ $product->color ? ' • ' . $product->color : '' }}
                                    </p>
                                </div>

                                <div class="flex items-end justify-between border-t border-white/10 pt-3">
                                    <div>
                                        <span class="block text-[10px] text-zinc-500">Harga</span>
                                        <span class="text-sm font-bold text-white">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <x-ui.button href="{{ route('products.show', $product->id) }}" variant="secondary" class="px-3 py-1.5 text-xs">
                                        Detail
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-2xl glass-card py-14 text-center text-sm text-zinc-500">
                            Belum ada produk aktif yang tersedia saat ini.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- 5. KEUNGGULAN & STANDAR INSPEKSI ISTORE (BENTO) --}}
            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="glass-surface rounded-2xl p-6 space-y-2.5">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-500/30 bg-blue-500/10 text-sm font-bold text-blue-400">01</span>
                    <h3 class="text-base font-semibold text-white">Transparansi Unit Second</h3>
                    <p class="text-xs leading-relaxed text-zinc-400">
                        Setiap perangkat bekas memiliki kode SKU unik dengan stok tunggal, laporan Grade A/B/C, kesehatan baterai asli, dan catatan fisik jujur.
                    </p>
                </div>

                <div class="glass-surface rounded-2xl p-6 space-y-2.5">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-sm font-bold text-emerald-400">02</span>
                    <h3 class="text-base font-semibold text-white">Checkout Atomik &amp; Aman</h3>
                    <p class="text-xs leading-relaxed text-zinc-400">
                        Stok dan harga divalidasi langsung di server saat checkout dengan perlindungan penguncian stok agar tidak terjadi pembelian ganda.
                    </p>
                </div>

                <div class="glass-surface rounded-2xl p-6 space-y-2.5">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-amber-500/30 bg-amber-500/10 text-sm font-bold text-amber-400">03</span>
                    <h3 class="text-base font-semibold text-white">Verifikasi DOKU Real-Time</h3>
                    <p class="text-xs leading-relaxed text-zinc-400">
                        Status pembayaran diperbarui otomatis melalui verifikasi server DOKU Checkout Sandbox sehingga pesanan segera diproses tanpa konfirmasi manual.
                    </p>
                </div>
            </section>

            {{-- 6. BOTTOM CTA --}}
            <section class="relative overflow-hidden rounded-3xl glass-surface border border-white/10 p-8 text-center sm:p-12">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-blue-600/10 via-transparent to-indigo-600/10"></div>
                <div class="relative mx-auto max-w-xl space-y-4">
                    <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Siap Memilih Perangkat Apple Berikutnya?
                    </h2>
                    <p class="text-sm text-zinc-400">
                        Bandingkan perangkat Baru dan unit Second terinspeksi secara transparan dalam satu katalog lengkap.
                    </p>
                    <div class="flex flex-wrap justify-center gap-3 pt-2">
                        <x-ui.button href="{{ route('catalog.index') }}" variant="primary" class="px-6 py-2.5 text-sm">
                            Mulai Belanja
                        </x-ui.button>
                        <x-ui.button href="{{ route('catalog.index', ['condition' => 'used']) }}" variant="secondary" class="px-6 py-2.5 text-sm">
                            Lihat Unit Second
                        </x-ui.button>
                    </div>
                </div>
            </section>

        </div>
    </div>
</x-layouts.app>
