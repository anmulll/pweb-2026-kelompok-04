@extends('layouts.app', ['title' => 'Dashboard Pembeli — iStore'])

@section('content')
@php
    $user = auth()->user();
    $ordersQuery = $user ? $user->orders() : null;

    $totalOrders = $totalOrders ?? ($ordersQuery ? (clone $ordersQuery)->count() : 0);
    $activeOrders = $activeOrders ?? ($ordersQuery ? (clone $ordersQuery)->whereIn('status', [
        \App\Enums\OrderStatus::PENDING_PAYMENT,
        \App\Enums\OrderStatus::PAID,
        \App\Enums\OrderStatus::PROCESSING,
        \App\Enums\OrderStatus::SHIPPED,
    ])->count() : 0);
    $completedOrders = $completedOrders ?? ($ordersQuery ? (clone $ordersQuery)->where('status', \App\Enums\OrderStatus::COMPLETED)->count() : 0);
    $recentOrders = $recentOrders ?? ($ordersQuery ? (clone $ordersQuery)->with(['items.product', 'payment'])->latest()->take(5)->get() : collect());
@endphp

<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-8">

    {{-- Welcome Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-medium uppercase tracking-wider text-blue-400 mb-1">
                Dashboard Pembeli
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="mt-1 text-sm text-zinc-400">
                Pantau aktivitas belanja, status pengiriman, dan kelola akun iStore Anda.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                Jelajahi Produk
            </x-ui.button>
            <x-ui.button href="{{ route('buyer.cart.index') }}" variant="secondary">
                Lihat Keranjang
            </x-ui.button>
        </div>
    </div>

    {{-- Bento Grid Ringkasan Pesanan (KPI Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="glass-surface rounded-2xl p-5 sm:p-6 border border-white/10 hover:border-white/20 transition">
            <div class="flex items-center justify-between text-zinc-400 text-xs font-medium mb-3">
                <span>TOTAL PESANAN</span>
                <span class="text-base text-zinc-500">🛍️</span>
            </div>
            <div class="text-3xl font-bold text-white tracking-tight">
                {{ number_format($totalOrders, 0, ',', '.') }}
            </div>
            <p class="mt-2 text-xs text-zinc-400">
                Semua pesanan yang pernah Anda buat
            </p>
        </div>

        <div class="glass-surface rounded-2xl p-5 sm:p-6 border border-amber-500/20 bg-amber-950/10 hover:border-amber-500/40 transition">
            <div class="flex items-center justify-between text-amber-300 text-xs font-medium mb-3">
                <span>PESANAN AKTIF</span>
                <span class="text-base text-amber-400">📦</span>
            </div>
            <div class="text-3xl font-bold text-amber-200 tracking-tight">
                {{ number_format($activeOrders, 0, ',', '.') }}
            </div>
            <p class="mt-2 text-xs text-amber-300/80">
                Menunggu pembayaran, diproses, atau dikirim
            </p>
        </div>

        <div class="glass-surface rounded-2xl p-5 sm:p-6 border border-emerald-500/20 bg-emerald-950/10 hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between text-emerald-300 text-xs font-medium mb-3">
                <span>PESANAN SELESAI</span>
                <span class="text-base text-emerald-400">✓</span>
            </div>
            <div class="text-3xl font-bold text-emerald-200 tracking-tight">
                {{ number_format($completedOrders, 0, ',', '.') }}
            </div>
            <p class="mt-2 text-xs text-emerald-300/80">
                Pesanan yang telah sukses diterima
            </p>
        </div>
    </div>

    {{-- Bento Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('buyer.orders.index') }}" class="glass-surface rounded-2xl p-5 border border-white/10 hover:border-blue-500/40 hover:bg-zinc-900/60 transition group">
            <div class="text-lg mb-2">📋</div>
            <h3 class="text-sm font-semibold text-white group-hover:text-blue-400 transition">Pesanan Saya</h3>
            <p class="text-xs text-zinc-400 mt-1">Lacak riwayat belanja dan status pengiriman secara rinci.</p>
        </a>

        <a href="{{ route('buyer.profile') }}" class="glass-surface rounded-2xl p-5 border border-white/10 hover:border-blue-500/40 hover:bg-zinc-900/60 transition group">
            <div class="text-lg mb-2">👤</div>
            <h3 class="text-sm font-semibold text-white group-hover:text-blue-400 transition">Profil Pengguna</h3>
            <p class="text-xs text-zinc-400 mt-1">Perbarui nomor telepon dan alamat pengiriman default.</p>
        </a>

        <a href="{{ route('catalog.index', ['condition' => 'used']) }}" class="glass-surface rounded-2xl p-5 border border-white/10 hover:border-blue-500/40 hover:bg-zinc-900/60 transition group">
            <div class="text-lg mb-2">🔍</div>
            <h3 class="text-sm font-semibold text-white group-hover:text-blue-400 transition">Unit Second Terinspeksi</h3>
            <p class="text-xs text-zinc-400 mt-1">Eksplorasi unit second Apple dengan transparansi kondisi fisik &amp; fungsi.</p>
        </a>
    </div>

    {{-- Pesanan Terbaru --}}
    <div class="glass-surface rounded-2xl border border-white/10 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-white/10 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">Pesanan Terbaru</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Daftar transaksi pesanan terkini pada akun Anda</p>
            </div>
            @if ($recentOrders->isNotEmpty())
                <a href="{{ route('buyer.orders.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                    Lihat Semua Pesanan
                </a>
            @endif
        </div>

        @if ($recentOrders->isEmpty())
            <div class="p-10 text-center space-y-4">
                <div class="w-12 h-12 mx-auto rounded-full bg-zinc-800/80 flex items-center justify-center text-xl text-zinc-400">
                    📦
                </div>
                <div>
                    <h3 class="text-sm font-medium text-zinc-200">Belum Ada Pesanan</h3>
                    <p class="text-xs text-zinc-400 mt-1 max-w-sm mx-auto">
                        Anda belum pernah membuat pesanan di iStore. Temukan perangkat Apple impian Anda di katalog sekarang.
                    </p>
                </div>
                <div>
                    <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                        Mulai Belanja
                    </x-ui.button>
                </div>
            </div>
        @else
            <div class="divide-y divide-white/10">
                @foreach ($recentOrders as $order)
                    @php
                        $statusValue = $order->status instanceof \BackedEnum ? $order->status->value : (string) $order->status;
                        $paymentStatusValue = $order->payment?->status instanceof \BackedEnum ? $order->payment->status->value : (string) ($order->payment?->status ?? 'pending');

                        $statusBadgeClass = match ($statusValue) {
                            'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                            'processing' => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
                            'shipped' => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30',
                            'completed' => 'bg-emerald-500/20 text-emerald-200 border-emerald-500/40',
                            'cancelled' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
                            default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                        };

                        $statusLabel = match ($statusValue) {
                            'paid' => 'Dibayar',
                            'processing' => 'Diproses',
                            'shipped' => 'Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => 'Menunggu Pembayaran',
                        };
                    @endphp

                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-zinc-900/40 transition">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-sm font-semibold text-white">
                                    {{ $order->order_code }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusBadgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-400">
                                Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} &bull; {{ $order->items->count() }} item produk
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-4">
                            <div class="text-left sm:text-right">
                                <div class="text-xs text-zinc-400">Total Pembayaran</div>
                                <div class="text-sm font-semibold text-white">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </div>
                            </div>

                            <x-ui.button href="{{ route('buyer.orders.show', $order) }}" variant="secondary">
                                Rincian Pesanan
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
