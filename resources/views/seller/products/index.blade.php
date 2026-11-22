<x-layouts.seller title="Manajemen Produk — Seller iStore">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Manajemen Produk Perangkat</h1>
                <p class="text-xs text-zinc-400">Kelola inventaris produk Apple Baru & Second Terinspeksi</p>
            </div>
            <x-ui.button href="{{ route('seller.products.create') }}" variant="primary">
                + Tambah Produk
            </x-ui.button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="glass-surface p-4 rounded-2xl border border-zinc-800">
            <form action="{{ route('seller.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, model, SKU..."
                    class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">

                <select name="category_id" class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="condition" class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">Semua Kondisi</option>
                    <option value="new" {{ request('condition') === 'new' ? 'selected' : '' }}>Baru</option>
                    <option value="used" {{ request('condition') === 'used' ? 'selected' : '' }}>Second</option>
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="secondary" class="w-full text-xs py-1.5">
                        Filter
                    </x-ui.button>
                    @if(request()->hasAny(['search', 'category_id', 'condition']))
                        <a href="{{ route('seller.products.index') }}" class="text-xs text-zinc-400 hover:text-white px-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Product Table -->
        <div class="glass-surface rounded-2xl border border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="bg-zinc-900/80 border-b border-zinc-800 text-zinc-400 uppercase font-semibold">
                        <tr>
                            <th class="py-3.5 px-4">Produk</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Kondisi & Grade</th>
                            <th class="py-3.5 px-4">Harga</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($products as $product)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-zinc-900 border border-zinc-800 overflow-hidden flex-shrink-0">
                                            <x-product-image :src="$product->imageUrl()" :alt="$product->name" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">{{ $product->name }}</div>
                                            <div class="text-[11px] text-zinc-500 font-mono">SKU: {{ $product->sku }} • {{ $product->model }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    {{ $product->category->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($product->isUsed())
                                        <div class="space-y-0.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-950 text-amber-300 border border-amber-800">
                                                Second
                                            </span>
                                            @if($product->condition_grade)
                                                <div class="text-[11px] text-zinc-400">Grade {{ is_object($product->condition_grade) ? $product->condition_grade->value : $product->condition_grade }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-950 text-blue-300 border border-blue-800">
                                            Baru
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-medium text-white">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="{{ $product->stock <= 1 ? 'text-amber-400 font-semibold' : 'text-zinc-300' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($product->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-950 text-emerald-400 border border-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-800 text-zinc-400 border border-zinc-700">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <!-- Toggle -->
                                    <form action="{{ route('seller.products.toggle', $product) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-zinc-400 hover:text-white transition">
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <span>•</span>
                                    <!-- Edit -->
                                    <a href="{{ route('seller.products.edit', $product) }}" class="text-xs text-blue-400 hover:underline">
                                        Edit
                                    </a>
                                    <span>•</span>
                                    <!-- Delete -->
                                    <form action="{{ route('seller.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-400 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-zinc-500">
                                    Belum ada produk yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.seller>
