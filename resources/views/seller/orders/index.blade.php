<x-seller-layout title="Pesanan">
    <x-seller.page-header
        title="Daftar Pesanan"
        subtitle="Pantau pesanan masuk berdasarkan status dan kelola pemenuhan pesanan pembeli."
    />

    @php
        $items = $orders ?? collect();
        $currentStatus = request('status', $status ?? '');
        $statusOptions = $statusOptions ?? [
            '' => 'Semua Status',
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    {{-- Filter Status --}}
    <div class="glass-surface rounded-2xl p-4 sm:p-5 mb-6">
        <form
            method="GET"
            action="{{ Route::has('seller.orders.index') ? route('seller.orders.index') : url()->current() }}"
            class="flex flex-col sm:flex-row sm:items-end gap-3"
        >
            <div class="w-full sm:w-64">
                <label for="status" class="block text-xs font-medium text-zinc-400 mb-1.5">
                    Filter Status Pesanan
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl bg-[#18181B] border border-white/15 px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                >
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected((string) $currentStatus === (string) $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                >
                    Terapkan Filter
                </button>

                @if ($currentStatus !== '')
                    <a
                        href="{{ Route::has('seller.orders.index') ? route('seller.orders.index') : url()->current() }}"
                        class="inline-flex items-center justify-center rounded-xl px-3.5 py-2 text-xs sm:text-sm font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Pesanan --}}
    <div class="glass-surface rounded-2xl overflow-hidden">
        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Pesanan</th>
                            <th class="py-3.5 px-4">Pembeli</th>
                            <th class="py-3.5 px-4">Pembayaran</th>
                            <th class="py-3.5 px-4">Status Pesanan</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $order)
                            @php
                                $orderStatus = strtolower((string) ($order->status ?? 'pending'));
                                $paymentStatus = strtolower((string) ($order->payment_status ?? ($order->payment->status ?? 'pending')));
                                $buyerName = $order->buyer_name ?? ($order->user->name ?? ($order->customer_name ?? 'Pembeli'));
                                $totalAmount = $order->total_amount ?? ($order->total ?? 0);
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-mono text-xs font-medium text-zinc-100">
                                        {{ $order->order_number ?? ($order->code ?? ('#' . $order->id)) }}
                                    </div>
                                    @if (!empty($order->created_at))
                                        <div class="text-xs text-zinc-400 mt-0.5">
                                            {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-zinc-200">
                                    {{ $buyerName }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $paymentStatus === 'paid' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-zinc-800 text-zinc-300 border-white/10' }}">
                                        {{ $order->payment_status ?? ($order->payment->status ?? 'Pending') }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-zinc-800 text-zinc-200 border border-white/15">
                                        {{ $order->status ?? 'pending' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-zinc-100">
                                    Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-right">
                                    @if (Route::has('seller.orders.show'))
                                        <a
                                            href="{{ route('seller.orders.show', $order) }}"
                                            class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                        >
                                            Detail
                                        </a>
                                    @endif
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
                <p class="text-sm font-medium text-zinc-200">Tidak ada pesanan yang ditemukan</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Belum ada data pesanan untuk filter atau periode yang dipilih.
                </p>
            </div>
        @endif
    </div>
</x-seller-layout>