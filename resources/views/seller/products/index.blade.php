<x-seller-layout title="Produk">
    <x-seller.page-header
        title="Katalog Produk"
        subtitle="Kelola daftar perangkat, spesifikasi model, harga, stok, dan status tayang."
    >
        <x-slot:actions>
            @if (Route::has('seller.products.create'))
                <a
                    href="{{ route('seller.products.create') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                >
                    Tambah Produk
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    {{-- Filter & Pencarian --}}
    <div class="mb-6 glass-surface rounded-2xl border border-white/10 p-4">
        <form action="{{ route('seller.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, model, SKU..."
                class="w-full rounded-xl bg-[#18181B] border border-white/10 px-3.5 py-2 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-white/30"
            >

            <select
                name="category_id"
                class="w-full rounded-xl bg-[#18181B] border border-white/10 px-3.5 py-2 text-xs text-white focus:outline-none focus:border-white/30"
            >
                <option value="">Semua Kategori</option>
                @foreach (($categories ?? collect()) as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select
                name="condition"
                class="w-full rounded-xl bg-[#18181B] border border-white/10 px-3.5 py-2 text-xs text-white focus:outline-none focus:border-white/30"
            >
                <option value="">Semua Kondisi</option>
                <option value="new" @selected(request('condition') === 'new')>Baru</option>
                <option value="used" @selected(request('condition') === 'used')>Second</option>
            </select>

            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition cursor-pointer"
                >
                    Filter
                </button>
                @if (request()->hasAny(['search', 'category_id', 'condition']))
                    <a href="{{ route('seller.products.index') }}" class="text-xs text-zinc-400 hover:text-white px-2">Reset</a>
                @endif
            </div>
        </form>
    </div>

    @php
        $items = $products ?? collect();
    @endphp

    <div class="glass-surface rounded-2xl overflow-hidden">
        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Produk</th>
                            <th class="py-3.5 px-4">SKU &amp; Model</th>
                            <th class="py-3.5 px-4">Kondisi</th>
                            <th class="py-3.5 px-4">Harga</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $product)
                            @php
                                $isActive = (bool) ($product->is_active ?? true);
                                $stock = (int) ($product->stock ?? 0);
                                $imagePath = method_exists($product, 'imageUrl')
                                    ? $product->imageUrl()
                                    : ($product->image_url ?? ($product->image ? asset('storage/' . ltrim($product->image, '/')) : null));
                                $isUsed = method_exists($product, 'isUsed')
                                    ? $product->isUsed()
                                    : in_array(strtolower((string) ($product->condition instanceof \BackedEnum ? $product->condition->value : ($product->condition ?? 'new'))), ['used', 'second'], true);
                                $gradeValue = $product->condition_grade instanceof \BackedEnum
                                    ? $product->condition_grade->value
                                    : $product->condition_grade;
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3 min-w-[200px]">
                                        <div class="h-12 w-12 shrink-0 rounded-xl bg-[#18181B] border border-white/10 overflow-hidden flex items-center justify-center">
                                            @if ($imagePath)
                                                <img
                                                    src="{{ $imagePath }}"
                                                    alt="{{ $product->name }}"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <span class="text-[10px] text-zinc-500">Tanpa Foto</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-zinc-100 truncate">{{ $product->name }}</div>
                                            <div class="text-xs text-zinc-400 truncate mt-0.5">
                                                {{ $product->category->name ?? 'Tanpa Kategori' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="text-xs font-mono text-zinc-200">{{ $product->sku ?? '-' }}</div>
                                    <div class="text-xs text-zinc-400 mt-0.5">{{ $product->model ?? '-' }}</div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                    @if ($isUsed)
                                        <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 font-medium bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                            <span>Second</span>
                                            @if (!empty($gradeValue))
                                                <span class="font-bold">Grade {{ strtoupper((string) $gradeValue) }}</span>
                                            @endif
                                        </div>
                                        @if (!empty($product->battery_health))
                                            <div class="text-[11px] text-zinc-400 mt-0.5">BH: {{ $product->battery_health }}%</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 font-medium bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                            Baru
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-zinc-100">
                                    Rp {{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($stock <= 0)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                            Habis (0)
                                        </span>
                                    @elseif ($stock <= 1)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                            Menipis ({{ $stock }})
                                        </span>
                                    @else
                                        <span class="text-sm text-zinc-200">{{ $stock }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if ($isActive)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-zinc-800 text-zinc-400 border border-white/10">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (Route::has('seller.products.toggle'))
                                            <form method="POST" action="{{ route('seller.products.toggle', $product->id ?? $product) }}" class="m-0">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition cursor-pointer"
                                                >
                                                    {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif

                                        @if (Route::has('seller.products.edit'))
                                            <a
                                                href="{{ route('seller.products.edit', $product->id ?? $product) }}"
                                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                            >
                                                Ubah
                                            </a>
                                        @endif

                                        @if (Route::has('seller.products.destroy'))
                                            <form
                                                method="POST"
                                                action="{{ route('seller.products.destroy', $product->id ?? $product) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dikembalikan.');"
                                                class="m-0"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-rose-300 bg-rose-950/40 hover:bg-rose-900/50 border border-rose-500/30 transition cursor-pointer"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (method_exists($items, 'hasPages') && $items->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-14 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada produk di katalog</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Tambahkan produk pertama lengkap dengan SKU, model, kondisi, harga, dan stok.
                </p>
                @if (Route::has('seller.products.create'))
                    <div class="mt-4">
                        <a
                            href="{{ route('seller.products.create') }}"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                        >
                            Tambah Produk
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-seller-layout>
