@extends('layouts.seller', ['title' => 'Manajemen Pesanan'])

@section('content')
<div class="w-full py-8 px-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Manajemen Pesanan</h1>
    </div>

    @if($orders->isEmpty())
        <div class="p-8 text-center text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-xl">
            Belum ada pesanan masuk.
        </div>
    @else
        <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-zinc-300">
                <thead class="bg-zinc-800/50 text-zinc-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Kode Pesanan</th>
                        <th class="px-4 py-3">Pembeli</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status Pesanan</th>
                        <th class="px-4 py-3">Status Pembayaran</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium text-white">{{ $order->order_code }}</td>
                            <td class="px-4 py-3">{{ $order->recipient_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-zinc-800 text-zinc-200">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-zinc-800 text-zinc-200">
                                    {{ $order->payment?->status->label() ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="text-blue-400 hover:text-blue-300">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
