@extends('layouts.seller', ['title' => 'Detail Pesanan ' . $order->order_code])

@section('content')
<div class="w-full py-8 px-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Pesanan #{{ $order->order_code }}</h1>
            <p class="text-sm text-zinc-400 mt-1">Pembeli: {{ $order->recipient_name }} | Status: {{ $order->status->label() }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-4">
                <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Item Pesanan</h3>
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium text-white">{{ $item->product_name_snapshot }}</p>
                            <p class="text-xs text-zinc-400">SKU: {{ $item->sku_snapshot }} | Qty: {{ $item->quantity }}</p>
                        </div>
                        <p class="text-white">Rp {{ number_format($item->line_total, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-2 text-sm">
                <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Alamat Pengiriman</h3>
                <p class="text-zinc-300">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                <p class="text-zinc-400">{{ $order->shipping_address }}</p>
            </div>
        </div>

        <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-6 h-fit">
            <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Tindakan Pemrosesan</h3>

            @if($order->status === \App\Enums\OrderStatus::PAID)
                <form method="POST" action="{{ route('seller.orders.process', $order->id) }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg text-sm transition">
                        Tandai Sedang Diproses
                    </button>
                </form>
            @endif

            @if($order->status === \App\Enums\OrderStatus::PROCESSING)
                <form method="POST" action="{{ route('seller.orders.ship', $order->id) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Kurir</label>
                        <input type="text" name="courier_name" placeholder="cth: JNE / SiCepat" required class="w-full px-3 py-1.5 text-sm bg-zinc-800 border border-zinc-700 rounded text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-400 mb-1">Nomor Resi</label>
                        <input type="text" name="tracking_number" placeholder="Nomor Resi Pengiriman" required class="w-full px-3 py-1.5 text-sm bg-zinc-800 border border-zinc-700 rounded text-white">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg text-sm transition">
                        Tandai Dikirim
                    </button>
                </form>
            @endif

            @if($order->canBeCancelled() && ! $order->isCompleted())
                <form method="POST" action="{{ route('seller.orders.cancel', $order->id) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" class="w-full py-2 bg-rose-600/20 border border-rose-500/30 hover:bg-rose-600/30 text-rose-400 font-medium rounded-lg text-sm transition">
                        Batalkan Pesanan (Restock)
                    </button>
                </form>
            @endif

            {{-- Auto-refresh meta attribute --}}
            <div id="order-meta"
                 data-order-id="{{ $order->id }}"
                 data-order-status="{{ $order->status->value }}"
                 data-payment-status="{{ $order->payment?->status->value ?? 'none' }}"
                 data-status-url="{{ route('seller.orders.payment.status', $order->id) }}">
            </div>
        </div>
    </div>
</div>
@endsection
