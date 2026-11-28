@extends('layouts.app', ['title' => 'Daftar Pesanan'])

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white mb-6">Daftar Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="p-8 text-center text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-xl">
            Belum ada riwayat pesanan.
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="p-5 bg-zinc-900 border border-zinc-800 rounded-xl flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3">
                            <span class="font-medium text-white">{{ $order->order_code }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium bg-zinc-800 text-zinc-300">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                        <p class="text-sm text-zinc-400 mt-1">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('buyer.orders.show', $order->id) }}" class="px-4 py-2 text-sm bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition">
                        Detail Pesanan
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
