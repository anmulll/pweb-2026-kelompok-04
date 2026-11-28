@extends('layouts.app', ['title' => 'Pesanan Saya — iStore'])

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">

    {{-- Breadcrumb & Title --}}
    <div>
        <nav class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <span class="text-zinc-200">Pesanan Saya</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                    Pesanan Saya
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    Lacak status pesanan, verifikasi pembayaran, dan pantau pengiriman produk Apple Anda.
                </p>
            </div>
            <x-ui.button href="{{ route('catalog.index') }}" variant="secondary">
                Belanja Lagi
            </x-ui.button>
        </div>
    </div>

    {{-- Filter Status Tabs (Client / URL Friendly) --}}
    @php
        $currentFilter = request('status', 'all');
        $tabs = [
            'all' => 'Semua',
            'pending_payment' => 'Menunggu Pembayaran',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs sm:text-sm">
        @foreach($tabs as $key => $label)
            <a
                href="{{ $key === 'all' ? route('buyer.orders.index') : route('buyer.orders.index', ['status' => $key]) }}"
                class="px-3.5 py-1.5 rounded-xl border whitespace-nowrap transition {{ $currentFilter === $key ? 'bg-blue-600 border-blue-500 text-white font-medium shadow-sm' : 'bg-[#18181B] border-white/10 text-zinc-400 hover:text-white hover:border-white/20' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Orders List --}}
    @if($orders->isEmpty())
        <div class="glass-surface rounded-2xl border border-white/10 p-12 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-800/60 flex items-center justify-center text-3xl">
                📦
            </div>
            <div>
                <h2 class="text-base font-semibold text-white">Belum Ada Pesanan</h2>
                <p class="text-xs sm:text-sm text-zinc-400 mt-1 max-w-md mx-auto">
                    @if($currentFilter !== 'all')
                        Tidak ditemukan pesanan dengan status "{{ $tabs[$currentFilter] ?? $currentFilter }}".
                    @else
                        Anda belum memiliki riwayat pesanan. Mulai belanja perangkat Apple berkualitas sekarang.
                    @endif
                </p>
            </div>
            <div>
                <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                    Jelajahi Produk
                </x-ui.button>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $orderStatusValue = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;
                    $paymentStatusValue = $order->payment?->status instanceof \BackedEnum ? $order->payment->status->value : (string) ($order->payment?->status ?? 'pending');

                    $orderBadgeClass = match ($orderStatusValue) {
                        'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                        'processing' => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
                        'shipped' => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30',
                        'completed' => 'bg-emerald-500/20 text-emerald-200 border-emerald-500/40',
                        'cancelled' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                    };

                    $orderLabel = match ($orderStatusValue) {
                        'paid' => 'Dibayar',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => 'Menunggu Pembayaran',
                    };

                    $paymentBadgeClass = match ($paymentStatusValue) {
                        'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                        'failed', 'expired', 'cancelled' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                    };

                    $paymentLabel = match ($paymentStatusValue) {
                        'paid' => 'Dibayar',
                        'failed' => 'Gagal',
                        'expired' => 'Kedaluwarsa',
                        'cancelled' => 'Dibatalkan',
                        default => 'Pending',
                    };

                    $canPay = $orderStatusValue === 'pending_payment' && $order->payment?->gateway_checkout_url;
                    $canConfirm = $orderStatusValue === 'shipped';
                @endphp

                <div class="glass-surface rounded-2xl border border-white/10 overflow-hidden hover:border-white/20 transition">
                    {{-- Order Header --}}
                    <div class="p-4 sm:p-5 border-b border-white/10 bg-zinc-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-mono text-sm font-semibold text-white">
                                {{ $order->order_code }}
                            </span>
                            <span class="text-zinc-400">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $orderBadgeClass }}">
                                {{ $orderLabel }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium border {{ $paymentBadgeClass }}">
                                Pembayaran: {{ $paymentLabel }}
                            </span>
                        </div>

                        <div class="text-zinc-400">
                            Penerima: <span class="text-zinc-200 font-medium">{{ $order->recipient_name }}</span>
                        </div>
                    </div>

                    {{-- Items Preview --}}
                    <div class="p-4 sm:p-5 space-y-3">
                        @foreach($order->items as $item)
                            @php
                                $itemCond = strtolower((string) ($item->condition_snapshot ?? ($item->condition ?? 'new')));
                                $isItemUsed = in_array($itemCond, ['used', 'second', 'bekas'], true);
                            @endphp
                            <div class="flex items-center justify-between gap-4 text-xs sm:text-sm">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-lg bg-zinc-800/80 border border-white/10 shrink-0 flex items-center justify-center text-sm text-zinc-500">
                                        
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-white truncate">
                                            {{ $item->product_name_snapshot ?? ($item->product?->name ?? 'Produk') }}
                                        </div>
                                        <div class="text-[11px] text-zinc-400">
                                            {{ $item->quantity }}x &bull; Rp {{ number_format($item->unit_price_snapshot ?? ($item->price ?? 0), 0, ',', '.') }}
                                            @if($isItemUsed)
                                                &bull; <span class="text-amber-400">Second (Grade {{ $item->condition_grade_snapshot ?? '-' }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right font-medium text-zinc-200 shrink-0">
                                    Rp {{ number_format($item->line_total ?? (($item->unit_price_snapshot ?? 0) * $item->quantity), 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Order Footer & Actions --}}
                    <div class="p-4 sm:p-5 border-t border-white/10 bg-zinc-950/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-xs text-zinc-400">
                            Total Pembayaran:
                            <span class="text-sm sm:text-base font-bold text-white ml-1">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] text-zinc-500 block sm:inline sm:ml-2">
                                (termasuk ongkir Rp {{ number_format($order->shipping_cost, 0, ',', '.') }})
                            </span>
                        </div>

                        <div class="flex items-center gap-2.5 flex-wrap justify-end">
                            <x-ui.button href="{{ route('buyer.orders.show', $order) }}" variant="secondary">
                                Rincian Pesanan
                            </x-ui.button>

                            @if($canPay)
                                <form action="{{ route('buyer.orders.payment.pay', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <x-ui.button type="submit" variant="primary">
                                        Bayar Sekarang
                                    </x-ui.button>
                                </form>
                            @endif

                            @if($canConfirm)
                                <form action="{{ route('buyer.orders.confirm-receipt', $order) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi bahwa pesanan ini sudah Anda terima dengan baik?');">
                                    @csrf
                                    <x-ui.button type="submit" variant="success">
                                        Konfirmasi Terima
                                    </x-ui.button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Pagination Links --}}
            <div class="pt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
