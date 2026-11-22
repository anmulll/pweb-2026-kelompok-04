<x-layouts.seller title="Manajemen Promosi — Seller iStore">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Manajemen Promosi Toko</h1>
                <p class="text-xs text-zinc-400">Atur kampanye promosi dan banner diskon untuk pelanggan</p>
            </div>
            <x-ui.button href="{{ route('seller.promotions.create') }}" variant="primary">
                + Tambah Promosi
            </x-ui.button>
        </div>

        <!-- Promotions Table -->
        <div class="glass-surface rounded-2xl border border-zinc-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead class="bg-zinc-900/80 border-b border-zinc-800 text-zinc-400 uppercase font-semibold">
                        <tr>
                            <th class="py-3.5 px-4">Banner & Judul Promosi</th>
                            <th class="py-3.5 px-4">Periode Berlaku</th>
                            <th class="py-3.5 px-4">Dibuat Oleh</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60">
                        @forelse($promotions as $promo)
                            <tr class="hover:bg-zinc-800/30 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-16 h-10 rounded-lg bg-zinc-900 border border-zinc-800 overflow-hidden flex-shrink-0">
                                            @if($promo->imageUrl())
                                                <img src="{{ $promo->imageUrl() }}" alt="{{ $promo->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-[10px] text-zinc-500">No Image</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">{{ $promo->title }}</div>
                                            <div class="text-[11px] text-zinc-500 line-clamp-1">{{ $promo->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-mono text-zinc-300">{{ $promo->start_date->format('d/m/Y') }} – {{ $promo->end_date->format('d/m/Y') }}</div>
                                    @if(today()->between($promo->start_date, $promo->end_date) && $promo->is_active)
                                        <span class="text-[10px] text-emerald-400">Sedang Berjalan</span>
                                    @elseif(today()->gt($promo->end_date))
                                        <span class="text-[10px] text-zinc-500">Sudah Berakhir</span>
                                    @else
                                        <span class="text-[10px] text-amber-400">Akan Datang</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-zinc-400">
                                    {{ $promo->creator->name ?? 'Seller' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($promo->is_active)
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
                                    <form action="{{ route('seller.promotions.toggle', $promo) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-zinc-400 hover:text-white transition">
                                            {{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <span>•</span>
                                    <!-- Edit -->
                                    <a href="{{ route('seller.promotions.edit', $promo) }}" class="text-xs text-blue-400 hover:underline">
                                        Edit
                                    </a>
                                    <span>•</span>
                                    <!-- Delete -->
                                    <form action="{{ route('seller.promotions.destroy', $promo) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promosi ini?');">
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
                                    Belum ada promosi yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($promotions->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.seller>
