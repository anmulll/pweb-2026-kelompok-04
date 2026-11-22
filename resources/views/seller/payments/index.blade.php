<x-seller-layout title="Pembayaran">
    <x-seller.page-header
        title="Pemantauan Pembayaran"
        subtitle="Pantau status pembayaran pesanan dan lakukan sinkronisasi/inquiry status transaksi."
    />

    @php
        $items = $payments ?? collect();
        $syncRouteName = Route::has('seller.payments.sync')
            ? 'seller.payments.sync'
            : (Route::has('seller.payments.inquiry') ? 'seller.payments.inquiry' : null);
    @endphp

    <div class="glass-surface rounded-2xl overflow-hidden">
        @if ($items->count() > 0)
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                            <th class="py-3.5 px-4 sm:px-6">Referensi &amp; Pesanan</th>
                            <th class="py-3.5 px-4">Metode</th>
                            <th class="py-3.5 px-4">Nominal</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-sm">
                        @foreach ($items as $payment)
                            @php
                                $statusRaw = (string) ($payment->status ?? 'pending');
                                $statusLower = strtolower($statusRaw);
                                $isPaid = $statusLower === 'paid';
                                $orderRef = $payment->order_number ?? ($payment->order->order_number ?? ($payment->order_id ?? '-'));
                                $referenceCode = $payment->reference ?? ($payment->transaction_id ?? ($payment->code ?? ('PAY-' . $payment->id)));
                                $amount = $payment->amount ?? ($payment->total ?? 0);
                            @endphp
                            <tr class="hover:bg-zinc-900/60 transition">
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap">
                                    <div class="font-mono text-xs font-medium text-zinc-100">{{ $referenceCode }}</div>
                                    <div class="text-xs text-zinc-400 mt-0.5">Pesanan: {{ $orderRef }}</div>
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap text-zinc-300">
                                    {{ $payment->method ?? ($payment->payment_method ?? ($payment->channel ?? '-')) }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-zinc-100">
                                    Rp {{ number_format((float) $amount, 0, ',', '.') }}
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $isPaid ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-zinc-800 text-zinc-200 border-white/15' }}">
                                        {{ $statusRaw }}
                                    </span>
                                    @if (!empty($payment->updated_at))
                                        <div class="text-[11px] text-zinc-500 mt-1">
                                            Update: {{ \Illuminate\Support\Carbon::parse($payment->updated_at)->format('d M Y H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        @if ($syncRouteName)
                                            <form method="POST" action="{{ route($syncRouteName, $payment) }}" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition cursor-pointer"
                                                >
                                                    Sync / Inquiry Status
                                                </button>
                                            </form>
                                        @endif

                                        @if (Route::has('seller.orders.show') && !empty($payment->order))
                                            <a
                                                href="{{ route('seller.orders.show', $payment->order) }}"
                                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                                            >
                                                Pesanan
                                            </a>
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
                    {{ $items->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-14 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada data pembayaran</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Riwayat pembayaran dan status verifikasi transaksi akan tampil di sini.
                </p>
            </div>
        @endif
    </div>
</x-seller-layout>