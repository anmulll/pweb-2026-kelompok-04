<x-seller-layout title="Promosi">
    <x-seller.page-header
        title="Promosi & Diskon"
        subtitle="Atur banner promosi beranda, periode berlaku, dan status aktif tayang."
    >
        <x-slot:actions>
            @if (Route::has('seller.promotions.create'))
                <a
                    href="{{ route('seller.promotions.create') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                >
                    Tambah Promosi
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    @php
        $items = $promotions ?? collect();
    @endphp

    <div class="glass-surface rounded-2xl overflow-hidden">
        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Banner &amp; Program</th>
                            <th class="py-3.5 px-4">Periode Berlaku</th>
                            <th class="py-3.5 px-4">Dibuat Oleh</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $promotion)
                            @php
                                $isActive = (bool) ($promotion->is_active ?? true);
                                $promoTitle = $promotion->title ?? ($promotion->name ?? '-');
                                $imagePath = method_exists($promotion, 'imageUrl')
                                    ? $promotion->imageUrl()
                                    : ($promotion->image_url ?? ($promotion->image ? asset('storage/' . ltrim($promotion->image, '/')) : null));

                                $startCarbon = !empty($promotion->start_date)
                                    ? \Illuminate\Support\Carbon::parse($promotion->start_date)
                                    : (!empty($promotion->starts_at) ? \Illuminate\Support\Carbon::parse($promotion->starts_at) : null);

                                $endCarbon = !empty($promotion->end_date)
                                    ? \Illuminate\Support\Carbon::parse($promotion->end_date)
                                    : (!empty($promotion->ends_at) ? \Illuminate\Support\Carbon::parse($promotion->ends_at) : null);
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="flex items-center gap-3 min-w-[200px]">
                                        <div class="h-10 w-16 shrink-0 rounded-lg bg-[#18181B] border border-white/10 overflow-hidden flex items-center justify-center">
                                            @if ($imagePath)
                                                <img
                                                    src="{{ $imagePath }}"
                                                    alt="{{ $promoTitle }}"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                >
                                            @else
                                                <span class="text-[10px] text-zinc-500">No Banner</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-zinc-100 truncate">{{ $promoTitle }}</div>
                                            @if (!empty($promotion->description))
                                                <div class="text-xs text-zinc-400 line-clamp-1 mt-0.5">{{ $promotion->description }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-zinc-300">
                                    <div>{{ $startCarbon ? $startCarbon->format('d M Y') : '-' }} — {{ $endCarbon ? $endCarbon->format('d M Y') : '-' }}</div>
                                    @if ($startCarbon && $endCarbon)
                                        @if (today()->between($startCarbon, $endCarbon) && $isActive)
                                            <span class="text-[10px] text-emerald-400">Sedang Berjalan</span>
                                        @elseif (today()->gt($endCarbon))
                                            <span class="text-[10px] text-zinc-500">Sudah Berakhir</span>
                                        @else
                                            <span class="text-[10px] text-amber-400">Akan Datang</span>
                                        @endif
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-zinc-400">
                                    {{ $promotion->creator->name ?? 'Seller' }}
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
                                        @if (Route::has('seller.promotions.toggle'))
                                            <form method="POST" action="{{ route('seller.promotions.toggle', $promotion->id ?? $promotion) }}" class="m-0">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition cursor-pointer"
                                                >
                                                    {{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif

                                        @if (Route::has('seller.promotions.edit'))
                                            <a
                                                href="{{ route('seller.promotions.edit', $promotion->id ?? $promotion) }}"
                                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                            >
                                                Ubah
                                            </a>
                                        @endif

                                        @if (Route::has('seller.promotions.destroy'))
                                            <form
                                                method="POST"
                                                action="{{ route('seller.promotions.destroy', $promotion->id ?? $promotion) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus promosi ini? Tindakan ini tidak dapat dibatalkan.');"
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
                <p class="text-sm font-medium text-zinc-200">Belum ada promosi yang dibuat</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Tambahkan banner promosi beranda dengan periode tanggal berlaku dan status aktif tayang.
                </p>
                @if (Route::has('seller.promotions.create'))
                    <div class="mt-4">
                        <a
                            href="{{ route('seller.promotions.create') }}"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                        >
                            Tambah Promosi
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-seller-layout>
