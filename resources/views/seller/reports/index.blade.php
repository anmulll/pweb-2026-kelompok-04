<x-seller-layout title="Laporan">
    @php
        $startDate = request('start_date',$startDate ?? '');
        $endDate = request('end_date',$endDate ?? '');

        // Seluruh angka ringkasan berasal dari controller / ReportService (tidak dihitung ulang di Blade)
        $verifiedRevenue = $verifiedRevenue ?? ($total_revenue ?? ($summary['verified_revenue'] ?? 0));
        $paidOrdersCount = $paidOrdersCount ?? ($total_orders ?? ($summary['paid_orders_count'] ?? 0));
        $items = $transactions ?? ($orders ?? ($reports ?? collect()));

        $pdfUrl = Route::has('seller.reports.pdf')
            ? route('seller.reports.pdf', array_filter([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]))
            : '#';
    @endphp

    <x-seller.page-header
        title="Laporan Penjualan"
        subtitle="Ringkasan omzet terverifikasi berdasarkan rentang tanggal transaksi."
    >
        <x-slot:actions>
            @if (Route::has('seller.reports.pdf'))
                <a
                    href="{{ $pdfUrl }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 transition"
                >
                    Unduh Laporan PDF
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    {{-- Filter Rentang Tanggal --}}
    <div class="glass-surface rounded-2xl p-4 sm:p-5 mb-6">
        <form
            method="GET"
            action="{{ Route::has('seller.reports.index') ? route('seller.reports.index') : url()->current() }}"
            class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end"
        >
            <div>
                <label for="start_date" class="block text-xs font-medium text-zinc-400 mb-1.5">
                    Tanggal Mulai
                </label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ $startDate }}"
                    class="w-full rounded-xl bg-[#18181B] border border-white/15 px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                >
            </div>

            <div>
                <label for="end_date" class="block text-xs font-medium text-zinc-400 mb-1.5">
                    Tanggal Akhir
                </label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ $endDate }}"
                    class="w-full rounded-xl bg-[#18181B] border border-white/15 px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                >
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                >
                    Tampilkan Laporan
                </button>

                @if ($startDate !== '' || $endDate !== '')
                    <a
                        href="{{ Route::has('seller.reports.index') ? route('seller.reports.index') : url()->current() }}"
                        class="inline-flex items-center justify-center rounded-xl px-3.5 py-2 text-xs sm:text-sm font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Catatan Kebijakan Perhitungan Omzet --}}
    <div class="mb-6 rounded-2xl glass-surface bg-[#18181B]/80 border border-white/15 px-4 py-3.5 text-xs sm:text-sm text-zinc-300">
        <strong class="font-medium text-zinc-100">Catatan Perhitungan Omzet:</strong>
        Nilai omzet terverifikasi pada laporan ini hanya menghitung transaksi dengan status pembayaran <strong class="font-semibold text-emerald-300">Paid</strong>. Transaksi yang masih menunggu pembayaran, kedaluwarsa, atau dibatalkan tidak dimasukkan ke dalam total omzet.
    </div>

    {{-- Ringkasan Omzet Terverifikasi --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Omzet Terverifikasi (Paid)</div>
            <div class="mt-2 text-2xl font-semibold tracking-tight text-zinc-100">
                Rp {{ number_format((float) $verifiedRevenue, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-xs text-zinc-400">
                Periode:
                {{ $startDate !== '' ? \Illuminate\Support\Carbon::parse($startDate)->format('d M Y') : 'Awal' }}
                —
                {{ $endDate !== '' ? \Illuminate\Support\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
            </p>
        </div>

        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Jumlah Transaksi Terverifikasi (Paid)</div>
            <div class="mt-2 text-2xl font-semibold tracking-tight text-zinc-100">
                {{ number_format((int) $paidOrdersCount, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-xs text-zinc-400">
                Total pesanan dengan pembayaran berstatus Paid
            </p>
        </div>
    </div>

    {{-- Tabel Rincian Transaksi Terverifikasi --}}
    <div class="glass-surface rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-zinc-100">Rincian Transaksi Terverifikasi (Paid)</h2>
        </div>

        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Nomor Pesanan</th>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Pembeli</th>
                            <th class="py-3.5 px-4">Status Pembayaran</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $row)
                            @php
                                $orderCode = $row->order_code ?? ($row->order_number ?? ($row->code ?? ('#' . $row->id)));
                                $txDate = $row->payment?->paid_at ?? ($row->paid_at ?? ($row->created_at ?? null));
                                $buyerName = $row->recipient_name ?? ($row->buyer_name ?? ($row->user->name ?? ($row->customer_name ?? 'Pembeli')));
                                $amount = $row->total_amount ?? ($row->amount ?? ($row->total ?? 0));
                                $rawStatus = $row->payment_status ?? ($row->payment?->status ?? ($row->status ?? 'Paid'));
                                $statusLabel = $rawStatus instanceof \BackedEnum ? $rawStatus->value : (string) $rawStatus;
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap font-mono text-xs text-zinc-100">
                                    {{ $orderCode }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-xs text-zinc-300">
                                    {{ $txDate ? \Illuminate\Support\Carbon::parse($txDate)->format('d M Y H:i') : '-' }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-zinc-200">
                                    {{ $buyerName }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        {{ strtoupper($statusLabel) }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-right font-medium text-zinc-100">
                                    Rp {{ number_format((float) $amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (method_exists($items, 'links'))
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $items->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-14 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada transaksi Paid pada periode ini</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Ubah rentang tanggal filter di atas untuk melihat data penjualan terverifikasi lainnya.
                </p>
            </div>
        @endif
    </div>
</x-seller-layout>