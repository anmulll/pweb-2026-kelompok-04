@extends('layouts.seller', ['title' => 'Catatan Pembayaran'])

@section('content')
<div class="w-full py-8 px-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Catatan Pembayaran</h1>
            <p class="text-xs text-zinc-400 mt-1">Halaman ini bersifat read-only untuk menjaga integritas transaksi keuangan.</p>
        </div>
    </div>

    @if($payments->isEmpty())
        <div class="p-8 text-center text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-xl">
            Belum ada catatan pembayaran.
        </div>
    @else
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-zinc-800/50 text-zinc-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Order Code</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach($payments as $payment)
                        <tr>
                            <td class="px-4 py-3 font-medium text-white">{{ $payment->gateway_order_id }}</td>
                            <td class="px-4 py-3">{{ $payment->order?->recipient_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-zinc-800 text-zinc-300">
                                    {{ $payment->isHistorical() ? 'Midtrans (Arsip)' : 'DOKU Sandbox' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-zinc-800 text-zinc-200">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-400">
                                {{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
