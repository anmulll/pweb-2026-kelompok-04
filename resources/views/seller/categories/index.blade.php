<x-layouts.seller title="Manajemen Kategori — Seller iStore">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Manajemen Kategori</h1>
                <p class="text-xs text-zinc-400">Kelola kelompok kategori perangkat Apple di toko Anda</p>
            </div>
            <x-ui.button href="{{ route('seller.categories.create') }}" variant="primary">
                + Tambah Kategori
            </x-ui.button>
        </div>

        <!-- Filter & Search -->
        <div class="glass-surface p-4 rounded-2xl border border-zinc-800 flex items-center justify-between gap-4">
            <form action="{{ route('seller.categories.index') }}" method="GET" class="flex items-center gap-3 w-full max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..."
                    class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                <x-ui.button type="submit" variant="secondary" class="text-xs py-1.5 px-3">
                    Cari
                </x-ui.button>
            </form>
        </div>

        <!-- Category Table -->
        <div class="glass-surface rounded-2xl border border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="bg-zinc-900/80 border-b border-zinc-800 text-zinc-400 uppercase font-semibold">
                        <tr>
                            <th class="py-3.5 px-4">Nama Kategori</th>
                            <th class="py-3.5 px-4">Slug</th>
                            <th class="py-3.5 px-4">Jumlah Produk</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($categories as $category)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <td class="py-3.5 px-4 font-medium text-white">
                                    {{ $category->name }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-zinc-400">
                                    {{ $category->slug }}
                                </td>
                                <td class="py-3.5 px-4">
                                    {{ $category->products_count }} produk
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($category->is_active)
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
                                    <form action="{{ route('seller.categories.toggle', $category) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-zinc-400 hover:text-white transition">
                                            {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <span>•</span>
                                    <!-- Edit -->
                                    <a href="{{ route('seller.categories.edit', $category) }}" class="text-xs text-blue-400 hover:underline">
                                        Edit
                                    </a>
                                    <span>•</span>
                                    <!-- Delete -->
                                    <form action="{{ route('seller.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
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
                                <td colspan="5" class="py-8 text-center text-zinc-500">
                                    Belum ada kategori yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.seller>
