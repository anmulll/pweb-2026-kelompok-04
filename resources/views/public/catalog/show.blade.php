<x-layouts.app :title="$product->name . ' — iStore'">
    <div class="max-w-[1200px] mx-auto px-4 py-8 space-y-8">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-white transition">Katalog</a>
            <span>/</span>
            <a href="{{ route('catalog.index', ['category' => $product->category->slug]) }}" class="hover:text-white transition">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-zinc-200 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Product Image Showcase -->
            <div class="space-y-4">
                <div class="aspect-square rounded-3xl overflow-hidden glass-surface border border-zinc-800 bg-zinc-950 p-6 flex items-center justify-center">
                    <x-product-image :src="$product->imageUrl()" :alt="$product->name" class="w-full h-full object-contain" />
                </div>
            </div>

            <!-- Product Specs & Purchase Info -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        @if($product->isUsed())
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-amber-950 text-amber-300 border border-amber-800">
                                Unit Second Terinspeksi
                            </span>
                            @if($product->condition_grade)
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-zinc-800 text-zinc-300 border border-zinc-700">
                                    Grade {{ is_object($product->condition_grade) ? $product->condition_grade->value : $product->condition_grade }}
                                </span>
                            @endif
                        @else
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-950 text-blue-300 border border-blue-800">
                                Perangkat Baru (Brand New)
                            </span>
                        @endif
                        <span class="text-xs text-zinc-500">SKU: {{ $product->sku }}</span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">{{ $product->name }}</h1>
                    <p class="text-sm text-zinc-400">Model: {{ $product->model }} {{ $product->storage ? '• ' . $product->storage : '' }} {{ $product->color ? '• ' . $product->color : '' }}</p>
                </div>

                <!-- Price Box -->
                <div class="p-5 rounded-2xl glass-surface border border-zinc-800 space-y-1">
                    <span class="text-xs text-zinc-400">Harga Perangkat</span>
                    <div class="text-3xl font-black text-white">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    <div class="pt-2 flex items-center gap-2 text-xs">
                        @if($product->stock > 0)
                            <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Stok Tersedia ({{ $product->stock }} unit)
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-rose-400 font-medium">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Stok Habis
                            </span>
                        @endif
                    </div>
                </div>

                <!-- SECOND-HAND INSPECTION PANEL (Mandatory for Used Units) -->
                @if($product->isUsed())
                    <div class="rounded-2xl glass-surface border border-amber-900/50 p-6 space-y-4">
                        <div class="flex items-center gap-2 border-b border-zinc-800 pb-3">
                            <span class="text-amber-400 text-lg">🔍</span>
                            <h2 class="font-bold text-white text-sm">Hasil Inspeksi Unit Second</h2>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="space-y-1">
                                <span class="text-zinc-500">Grade Fisik</span>
                                <p class="text-zinc-200 font-semibold">{{ $product->condition_grade ? (is_object($product->condition_grade) ? $product->condition_grade->label() : 'Grade ' . $product->condition_grade) : '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-zinc-500">Kesehatan Baterai (BH)</span>
                                <p class="text-zinc-200 font-semibold">{{ $product->battery_health ? $product->battery_health . '%' : '-' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-zinc-500">Status Fungsi</span>
                                <p class="text-zinc-200 font-semibold">{{ $product->functional_status ?? 'Semua Berfungsi Normal' }}</p>
                            </div>
                            <div class="space-y-1">
                                <span class="text-zinc-500">Garansi Toko</span>
                                <p class="text-zinc-200 font-semibold">{{ $product->warranty_info ?? 'Garansi Toko 1 Bulan' }}</p>
                            </div>
                        </div>

                        @if($product->included_accessories)
                            <div class="pt-2 border-t border-zinc-800 text-xs space-y-1">
                                <span class="text-zinc-500">Kelengkapan Paket</span>
                                <p class="text-zinc-300">{{ $product->included_accessories }}</p>
                            </div>
                        @endif

                        @if($product->defect_notes)
                            <div class="pt-2 border-t border-zinc-800 text-xs space-y-1">
                                <span class="text-zinc-500">Catatan Kekurangan Fisik / Fungsi</span>
                                <p class="text-amber-300/90 leading-relaxed">{{ $product->defect_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Description -->
                @if($product->description)
                    <div class="space-y-2 text-xs text-zinc-300 leading-relaxed">
                        <h3 class="font-bold text-white text-sm">Deskripsi Produk</h3>
                        <p class="whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Call to Action -->
                <div class="pt-4 flex items-center gap-3">
                    <x-ui.button variant="primary" class="w-full py-3 text-sm">
                        Tambah ke Keranjang
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
