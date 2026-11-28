@extends('layouts.app')

@section('title', 'Dashboard Pembeli — iStore')

@section('content')
@php
    $buyer = auth()->user();
    $recentOrders = $buyer->orders()->with(['items', 'payment'])->latest()->take(5)->get();
    $ordersCount = $buyer->orders()->count();
    $cartCount = $buyer->cart?->items()->sum('quantity') ?? 0;
@endphp

<div class="mx-auto max-w-[1200px] space-y-8 px-4 py-8 sm:px-6 sm:py-10">
    {{-- Header --}}
    <div class="glass-surface rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="text-xs font-medium uppercase tracking-wider text-blue-400">
                Dashboard Pembeli
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                Selamat datang, {{ $buyer->name }}
            </h1>
            <p class="text-sm text-zinc-400">
                Kelola keranjang belanja, pantau status pembayaran DOKU, dan lacak pengiriman perangkat Apple Anda.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                Jelajahi Katalog
            </x-ui.button>
            <x-ui.button href="{{ route('buyer.cart.index') }}" variant="secondary">
                Keranjang ({{ $cartCount }})
            </x-ui.button>
        </div>
    </div>

    {{-- Quick Access Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('buyer.orders.index') }}" class="glass-card rounded-2xl p-5 space-y-2 block">
            <span class="text-xs text-zinc-400">Total Pesanan</span>
            <div class="text-2xl font-bold text-white">{{ $ordersCount }}</div>
            <p class="text-xs text-blue-400">Lihat riwayat pesanan</p>
        </a>

        <a href="{{ route('buyer.cart.index') }}" class="glass-card rounded-2xl p-5 space-y-2 block">
            <span class="text-xs text-zinc-400">Item di Keranjang</span>
            <div class="text-2xl font-bold text-white">{{ $cartCount }}</div>
            <p class="text-xs text-blue-400">Buka keranjang belanja</p>
        </a>

        <a href="{{ route('buyer.profile') }}" class="glass-card rounded-2xl p-5 space-y-2 block">
            <span class="text-xs text-zinc-400">Profil &amp; Alamat</span>
            <div class="text-base font-semibold text-white truncate">{{ $buyer->phone ?: 'Belum diisi' }}</div>
            <p class="text-xs text-blue-400">Perbarui informasi pengiriman</p>
        </a>

        <a href="{{ route('catalog.index', ['condition' => 'used']) }}" class="glass-card rounded-2xl p-5 space-y-2 block">
            <span class="text-xs text-zinc-400">Unit Second Terinspeksi</span>
            <div class="text-base font-semibold text-white">Grade A / B / C</div>
            <p class="text-xs text-blue-400">Lihat katalog unit second</p>
        </a>
    </div>

    {{-- Recent Orders --}}
    <div class="glass-surface overflow-hidden rounded-2xl">
        <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
            <h2 class="text-base font-semibold text-white">Pesanan Terbaru</h2>
            <a href="{{ route('buyer.orders.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                Lihat Semua
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="px-6 py-14 text-center space-y-3">
                <p class="text-sm font-medium text-zinc-200">Belum ada pesanan</p>
                <p class="text-xs text-zinc-400">Riwayat transaksi Anda akan muncul di sini setelah melakukan checkout.</p>
                <div class="pt-2">
                    <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                        Mulai Belanja
                    </x-ui.button>
                </div>
            </div>
        @else
            <div class="divide-y divide-white/10">
                @foreach ($recentOrders as $order)
                    @php
                        $statusVal = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;
                    @endphp
                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 hover:bg-zinc-900/50 transition">
                        <div class="space-y-0.5">
                            <div class="font-mono text-sm font-semibold text-white">{{ $order->order_code }}</div>
                            <div class="text-xs text-zinc-400">
                                {{ $order->created_at->format('d M Y, H:i') }} • {{ $order->items->count() }} item
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-sm font-bold text-white">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] uppercase tracking-wide text-zinc-400">{{ str_replace('_', ' ', $statusVal) }}</div>
                            </div>
                            <x-ui.button href="{{ route('buyer.orders.show', $order) }}" variant="secondary" class="text-xs py-1.5 px-3">
                                Detail
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
