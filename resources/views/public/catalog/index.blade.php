<x-layouts.app title="Katalog Perangkat — iStore">
    <div class="max-w-[1200px] mx-auto px-4 py-8">
        <div class="mb-8 space-y-2">
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Katalog Perangkat Apple</h1>
            <p class="text-sm text-zinc-400">Pilihan lengkap perangkat baru dan unit second bersertifikat inspeksi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filter Sidebar -->
            <aside class="lg:col-span-1">
                <form action="{{ route('catalog.index') }}" method="GET" class="glass-surface rounded-2xl p-5 border border-zinc-800 space-y-6 sticky top-20">
                    <!-- Search Input -->
                    <div class="space-y-1.5">
                        <label for="search" class="text-xs font-semibold text-zinc-300">Cari Nama / Model</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Contoh: iPhone 15, MacBook M3..."
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div class="space-y-1.5">
                        <label for="category" class="text-xs font-semibold text-zinc-300">Kategori</label>
                        <select id="category" name="category" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Condition Filter -->
                    <div class="space-y-1.5">
                        <label for="condition" class="text-xs font-semibold text-zinc-300">Kondisi</label>
                        <select id="condition" name="condition" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Semua Kondisi</option>
                            <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>Baru (New)</option>
                            <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Second Terinspeksi</option>
                        </select>
                    </div>

                    <!-- Condition Grade Filter -->
                    <div class="space-y-1.5">
                        <label for="grade" class="text-xs font-semibold text-zinc-300">Grade Fisik (Unit Second)</label>
                        <select id="grade" name="grade" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Semua Grade</option>
                            <option value="A" {{ request('grade') === 'A' ? 'selected' : '' }}>Grade A (Mulus)</option>
                            <option value="B" {{ request('grade') === 'B' ? 'selected' : '' }}>Grade B (Lecet Halus)</option>
                            <option value="C" {{ request('grade') === 'C' ? 'selected' : '' }}>Grade C (Dent/Baret)</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-zinc-300">Rentang Harga (Rp)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Maks"
                                class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Sorting -->
                    <div class="space-y-1.5">
                        <label for="sort" class="text-xs font-semibold text-zinc-300">Urutkan</label>
                        <select id="sort" name="sort" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga: Terendah ke Tertinggi</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga: Tertinggi ke Terendah</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 flex flex-col gap-2">
                        <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                            Terapkan Filter
                        </x-ui.button>
                        @if(request()->hasAny(['search', 'category', 'condition', 'grade', 'min_price', 'max_price', 'sort']))
                            <a href="{{ route('catalog.index') }}" class="text-xs text-center text-zinc-400 hover:text-white transition py-1">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </aside>

            <!-- Product Grid -->
            <div class="lg:col-span-3 space-y-6">
                <div class="flex items-center justify-between text-xs text-zinc-400">
                    <span>Menampilkan <strong>{{ $products->total() }}</strong> perangkat</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($products as $product)
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
                        <div class="col-span-full py-16 text-center text-zinc-500 text-sm glass-card rounded-2xl space-y-2">
                            <span class="text-3xl text-zinc-600 block"></span>
                            <p>Tidak ada perangkat yang sesuai dengan kriteria filter.</p>
                            <a href="{{ route('catalog.index') }}" class="text-xs text-blue-400 hover:underline">Lihat Semua Perangkat</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
