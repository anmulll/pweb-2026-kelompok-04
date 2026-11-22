<x-layouts.app title="iStore — Retailer Independen Perangkat Apple Baru & Second Terinspeksi">
    <div class="max-w-[1200px] mx-auto px-4 py-8 space-y-12">
        <!-- Hero Section -->
        <section class="relative rounded-3xl overflow-hidden glass-surface border border-zinc-800 p-8 md:p-14 text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-xl space-y-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    Retailer Independen Apple
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Pengalaman Apple Terbaik, Baru &amp; Terinspeksi.
                </h1>
                <p class="text-zinc-400 text-sm md:text-base leading-relaxed">
                    Menyediakan iPhone, MacBook, iPad, dan aksesori Apple original. Setiap unit second melewati inspeksi ketat dengan transparansi kondisi 100%.
                </p>
                <div class="pt-2 flex flex-wrap gap-3 justify-center md:justify-start">
                    <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                        Jelajahi Katalog
                    </x-ui.button>
                    <x-ui.button href="{{ route('catalog.index', ['condition' => 'used']) }}" variant="secondary">
                        Lihat Unit Second
                    </x-ui.button>
                    @guest
                        <x-ui.button href="{{ route('login') }}" variant="secondary">
                            Masuk
                        </x-ui.button>
                    @endguest
                </div>
            </div>
            <div class="w-full md:w-80 aspect-square rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center p-6 text-zinc-500">
                <div class="text-center space-y-2">
                    <span class="text-6xl text-zinc-400 block"></span>
                    <span class="text-xs uppercase tracking-widest text-zinc-500 font-semibold block">Original Apple Devices</span>
                </div>
            </div>
        </section>

        <!-- Active Promotions Banner (If Any) -->
        @if(($promotions ?? collect())->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold tracking-tight text-white">Promosi Spesial</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($promotions as $promo)
                        <div class="rounded-2xl glass-card p-6 flex flex-col justify-between space-y-4 border border-zinc-800">
                            <div class="space-y-2">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-medium bg-emerald-950 text-emerald-400 border border-emerald-800">
                                    Berlaku s.d. {{ $promo->end_date->format('d M Y') }}
                                </span>
                                <h3 class="text-lg font-bold text-white">{{ $promo->title }}</h3>
                                <p class="text-zinc-400 text-xs leading-relaxed">{{ $promo->description }}</p>
                            </div>
                            @if($promo->imageUrl())
                                <div class="h-36 rounded-xl overflow-hidden border border-zinc-800">
                                    <img src="{{ $promo->imageUrl() }}" alt="{{ $promo->title }}" loading="lazy" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Active Categories Section -->
        @if(($categories ?? collect())->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-white">Kategori Pilihan</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                        <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="glass-card rounded-2xl p-5 text-center group">
                            <div class="text-2xl mb-2 text-zinc-400 group-hover:text-blue-400 transition"></div>
                            <h3 class="font-semibold text-white text-sm group-hover:text-blue-400 transition">{{ $category->name }}</h3>
                            <span class="text-[11px] text-zinc-500">{{ $category->products_count }} Perangkat Aktif</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Featured Products (Bento Grid) -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white">Produk Unggulan</h2>
                    <p class="text-xs text-zinc-400">Unit pilihan dengan jaminan kualitas terbaik</p>
                </div>
                <a href="{{ route('catalog.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($featuredProducts ?? collect() as $product)
                    <div class="glass-card rounded-2xl overflow-hidden flex flex-col justify-between border border-zinc-800 group">
                        <div class="aspect-square bg-zinc-900 border-b border-zinc-800 relative overflow-hidden">
                            <x-product-image :src="$product->imageUrl()" :alt="$product->name" />
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                @if($product->isUsed())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-950/80 text-amber-300 border border-amber-800">
                                        Second
                                    </span>
                                    @if($product->condition_grade)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700">
                                            Grade {{ is_object($product->condition_grade) ? $product->condition_grade->value : $product->condition_grade }}
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-950/80 text-blue-300 border border-blue-800">
                                        Baru
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                            <div class="space-y-1">
                                <span class="text-[11px] text-zinc-500 uppercase tracking-wider block">{{ $product->category->name ?? 'Apple' }}</span>
                                <h3 class="font-semibold text-sm text-white group-hover:text-blue-400 transition line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-zinc-400 line-clamp-1">{{ $product->model }} {{ $product->storage ? '• ' . $product->storage : '' }}</p>
                            </div>

                            <div class="pt-2 border-t border-zinc-800/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-zinc-500 block">Harga</span>
                                    <span class="text-sm font-bold text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                <x-ui.button href="{{ route('products.show', $product->id) }}" variant="secondary" class="text-xs py-1.5 px-3">
                                    Detail
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-zinc-500 text-sm glass-card rounded-2xl">
                        Belum ada produk aktif yang tersedia saat ini.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Bottom CTA Section -->
        <section class="rounded-3xl glass-surface border border-zinc-800 p-8 md:p-12 text-center space-y-4">
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white">
                Temukan Perangkat Apple Pilihan Anda
            </h2>
            <p class="text-sm text-zinc-400 max-w-xl mx-auto">
                Bandingkan unit Baru dan Second terinspeksi secara transparan dalam satu katalog.
            </p>
            <div class="pt-2 flex justify-center gap-3">
                <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                    Belanja Sekarang
                </x-ui.button>
            </div>
        </section>
    </div>
</x-layouts.app>
