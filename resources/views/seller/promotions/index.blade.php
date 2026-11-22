<x-seller-layout title="Promosi">
    <x-seller.page-header
        title="Promosi & Diskon"
        subtitle="Atur program potongan harga, kode promo, periode berlaku, dan status aktif promosi."
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
                            <th class="py-3.5 px-4 sm:px-6">Program &amp; Kode</th>
                            <th class="py-3.5 px-4">Potongan</th>
                            <th class="py-3.5 px-4">Periode Berlaku</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $promotion)
                            @php
                                $isActive = (bool) ($promotion->is_active ?? true);
                                $discountType = $promotion->discount_type ?? $promotion->type ?? 'fixed';
                                $discountValue = (float) ($promotion->discount_value ?? $promotion->value ?? 0);

                                $startDate = !empty($promotion->start_date)
                                    ? \Illuminate\Support\Carbon::parse($promotion->start_date)->format('d M Y')
                                    : (!empty($promotion->starts_at) ? \Illuminate\Support\Carbon::parse($promotion->starts_at)->format('d M Y') : '-');

                                $endDate = !empty($promotion->end_date)
                                    ? \Illuminate\Support\Carbon::parse($promotion->end_date)->format('d M Y')
                                    : (!empty($promotion->ends_at) ? \Illuminate\Support\Carbon::parse($promotion->ends_at)->format('d M Y') : '-');
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6">
                                    <div class="font-medium text-zinc-100">{{ $promotion->name ?? ($promotion->title ?? '-') }}</div>
                                    @if (!empty($promotion->code))
                                        <div class="text-xs font-mono text-zinc-400 mt-0.5">{{ $promotion->code }}</div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-zinc-200">
                                    @if (in_array($discountType, ['percentage', 'percent', 'persen'], true))
                                        {{ rtrim(rtrim(number_format($discountValue, 2, ',', '.'), '0'), ',') }}%
                                    @else
                                        Rp {{ number_format($discountValue, 0, ',', '.') }}
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-zinc-300">
                                    {{ $startDate }} — {{ $endDate }}
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
                                            <form method="POST" action="{{ route('seller.promotions.toggle', $promotion) }}" class="m-0">
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

                                        @if (Route::has('seller.promotions.edit'))
                                            <a
                                                href="{{ route('seller.promotions.edit', $promotion) }}"
                                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                            >
                                                Ubah
                                            </a>
                                        @endif

                                        @if (Route::has('seller.promotions.destroy'))
                                            <form
                                                method="POST"
                                                action="{{ route('seller.promotions.destroy', $promotion) }}"
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

            @if (method_exists($items, 'links'))
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $items->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-14 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada promosi yang dibuat</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Tambahkan program promosi dengan tanggal mulai/akhir dan status aktif atau nonaktif.
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