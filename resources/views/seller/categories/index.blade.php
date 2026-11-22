<x-seller-layout title="Kategori">
    <x-seller.page-header
        title="Kategori Produk"
        subtitle="Kelola pengelompokan produk untuk mempermudah navigasi katalog pembeli."
    >
        <x-slot:actions>
            @if (Route::has('seller.categories.create'))
                <a
                    href="{{ route('seller.categories.create') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                >
                    Tambah Kategori
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    @php
        $items = $categories ?? collect();
    @endphp

    <div class="glass-surface rounded-2xl overflow-hidden">
        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Nama Kategori</th>
                            <th class="py-3.5 px-4">Slug</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $category)
                            @php
                                $isActive = (bool) ($category->is_active ?? true);
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="font-medium text-zinc-100">{{ $category->name }}</div>
                                    @if (!empty($category->description))
                                        <div class="text-xs text-zinc-400 line-clamp-1 mt-0.5">{{ $category->description }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-xs text-zinc-400 font-mono">
                                    {{ $category->slug ?? '-' }}
                                </td>

                                <td class="py-3.5 px-4">
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

                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        {{-- Toggle Aktif / Nonaktif --}}
                                        @if (Route::has('seller.categories.toggle'))
                                            <form method="POST" action="{{ route('seller.categories.toggle', $category) }}" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition cursor-pointer"
                                                >
                                                    {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Ubah --}}
                                        @if (Route::has('seller.categories.edit'))
                                            <a
                                                href="{{ route('seller.categories.edit', $category) }}"
                                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                            >
                                                Ubah
                                            </a>
                                        @endif

                                        {{-- Hapus dengan Konfirmasi --}}
                                        @if (Route::has('seller.categories.destroy'))
                                            <form
                                                method="POST"
                                                action="{{ route('seller.categories.destroy', $category) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.');"
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

            @if (method_exists($items, 'links'))
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-14 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada kategori produk</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Tambahkan kategori pertama untuk mulai mengelompokkan produk di katalog iStore.
                </p>
                @if (Route::has('seller.categories.create'))
                    <div class="mt-4">
                        <a
                            href="{{ route('seller.categories.create') }}"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                        >
                            Tambah Kategori
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-seller-layout>