@extends('layouts.app', ['title' => 'Detail Pesanan ' . $order->order_code])

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Pesanan #{{ $order->order_code }}</h1>
            <p class="text-sm text-zinc-400 mt-1">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <span id="order-status-badge" class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-800 text-zinc-300">
                {{ $order->status->label() }}
            </span>
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
            {{-- Order Items --}}
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

            {{-- Shipping Info --}}
            <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-2 text-sm">
                <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Informasi Pengiriman</h3>
                <p class="text-zinc-300"><span class="text-zinc-500">Penerima:</span> {{ $order->recipient_name }} ({{ $order->recipient_phone }})</p>
                <p class="text-zinc-300"><span class="text-zinc-500">Alamat:</span> {{ $order->shipping_address }}</p>
                @if($order->courier_name)
                    <p class="text-zinc-300"><span class="text-zinc-500">Kurir & Resi:</span> {{ $order->courier_name }} - {{ $order->tracking_number }}</p>
                @endif
            </div>
        </div>

        {{-- Payment Summary & Actions --}}
        <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-6 h-fit">
            <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Status Pembayaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-400">Status Bayar</span>
                    <span class="text-white font-medium">{{ $order->payment?->status->label() ?? 'Menunggu' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Metode</span>
                    <span class="text-white">{{ $order->payment?->isHistorical() ? 'Midtrans (Arsip)' : 'DOKU Sandbox' }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-zinc-800">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($order->status === \App\Enums\OrderStatus::PENDING_PAYMENT && $order->payment?->gateway_checkout_url)
                <a href="{{ $order->payment->gateway_checkout_url }}" target="_blank" class="block text-center w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg text-sm transition">
                    Buka Halaman DOKU
                </a>
            @endif

            @if($order->canBeCancelled() && ! $order->isCompleted())
                <form method="POST" action="{{ route('buyer.orders.cancel', $order->id) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" class="w-full py-2 bg-rose-600/20 border border-rose-500/30 hover:bg-rose-600/30 text-rose-400 font-medium rounded-lg text-sm transition">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif

            @if($order->status === \App\Enums\OrderStatus::SHIPPED)
                <form method="POST" action="{{ route('buyer.orders.confirm-receipt', $order->id) }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg text-sm transition">
                        Konfirmasi Pesanan Diterima
                    </button>
                </form>
            @endif

            {{-- Auto-refresh & Sync data attribute --}}
            <div id="order-meta"
                 data-order-id="{{ $order->id }}"
                 data-order-status="{{ $order->status->value }}"
                 data-payment-status="{{ $order->payment?->status->value ?? 'none' }}"
                 data-status-url="{{ route('buyer.orders.payment.status', $order->id) }}">
            </div>
        </div>
    </div>
</div>
@endsection
