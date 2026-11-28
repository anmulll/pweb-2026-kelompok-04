@extends('layouts.app', ['title' => 'Detail Pesanan ' . $order->order_code . ' — iStore'])

@section('content')
@php
    $orderNumber = $order->order_code ?? ('#' . $order->id);
    $rawOrderStatus = $order->status ?? 'pending_payment';
    $orderStatus = strtolower($rawOrderStatus instanceof \BackedEnum ? $rawOrderStatus->value : (string) $rawOrderStatus);

    $rawPaymentStatus = $order->payment?->status ?? 'pending';
    $paymentStatus = strtolower($rawPaymentStatus instanceof \BackedEnum ? $rawPaymentStatus->value : (string) $rawPaymentStatus);

    $isPendingPayment = $orderStatus === 'pending_payment' && $paymentStatus === 'pending';
    $isShipped = $orderStatus === 'shipped';
    $canCancel = method_exists($order, 'canBeCancelled') ? $order->canBeCancelled() : in_array($orderStatus, ['pending_payment', 'paid'], true);
    $hasCheckoutUrl = (bool) ($order->payment?->gateway_checkout_url);
    $isHistoricalArchive = $order->payment?->provider === 'midtrans_sandbox';

    $orderBadgeClass = match ($orderStatus) {
        'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        'processing' => 'bg-sky-500/15 text-sky-300 border-sky-500/30',
        'shipped' => 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30',
        'completed' => 'bg-emerald-500/20 text-emerald-200 border-emerald-500/40',
        'cancelled' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
    };

    $orderLabel = match ($orderStatus) {
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        default => 'Menunggu Pembayaran',
    };

    $paymentBadgeClass = match ($paymentStatus) {
        'paid' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        'failed', 'expired', 'cancelled' => 'bg-rose-500/15 text-rose-300 border-rose-500/30',
        default => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
    };

    $paymentLabel = match ($paymentStatus) {
        'paid' => 'Dibayar',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'cancelled' => 'Dibatalkan',
        default => 'Menunggu Verifikasi',
    };

    $statusEndpoint = route('buyer.orders.payment.status', $order);
@endphp

<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">

    {{-- Breadcrumb --}}
    <div>
        <nav class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('buyer.orders.index') }}" class="hover:text-white transition">Pesanan</a>
            <span>/</span>
            <span class="text-zinc-200 font-mono">{{ $orderNumber }}</span>
        </nav>

        {{-- Page Header & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white font-mono">
                        {{ $orderNumber }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $orderBadgeClass }}">
                        {{ $orderLabel }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $paymentBadgeClass }}">
                        Pembayaran: {{ $paymentLabel }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-zinc-400">
                    Dibuat pada {{ $order->created_at->format('d M Y, H:i') }} &bull; ID Transaksi #{{ $order->id }}
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                @if($isPendingPayment)
                    <button
                        type="button"
                        id="manual-status-check-btn"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-medium text-amber-200 bg-amber-950/40 hover:bg-amber-900/50 border border-amber-500/30 transition cursor-pointer"
                    >
                        Cek Status Pembayaran
                    </button>
                @endif

                <x-ui.button href="{{ route('buyer.orders.index') }}" variant="secondary">
                    Kembali ke Daftar
                </x-ui.button>
            </div>
        </div>
    </div>

    {{-- Notifikasi Auto-Refresh Polling Aktif (ADR-04) --}}
    @if ($isPendingPayment)
        <div id="polling-banner" class="rounded-2xl glass-surface bg-amber-950/20 border border-amber-500/30 p-4 flex items-center justify-between text-xs sm:text-sm text-amber-200">
            <div class="flex items-center gap-3">
                <span class="relative flex h-2.5 w-2.5 shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <span>Memantau pembayaran Anda secara otomatis... Halaman akan memuat ulang seketika setelah pembayaran terverifikasi oleh server DOKU.</span>
            </div>
            <span id="polling-timer" class="text-xs text-amber-400/80 font-mono hidden sm:inline ml-3">5s</span>
        </div>
    @endif

    {{-- Banner Arsip Historis Midtrans jika berlaku --}}
    @if ($isHistoricalArchive)
        <div class="rounded-2xl glass-surface bg-zinc-900/60 border border-zinc-700/60 p-4 text-xs text-zinc-400">
            ℹ️ <strong>Arsip Historis:</strong> Transaksi ini berasal dari payment gateway terdahulu (Midtrans Sandbox) dan disimpan secara read-only demi integritas data audit.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

        {{-- Kolom Kiri: Item Pesanan & Pengiriman --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card Daftar Item (Snapshot Permanen) --}}
            <div class="glass-surface rounded-2xl border border-white/10 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-white">Item Pesanan (Snapshot Permanen)</h2>
                        <p class="text-xs text-zinc-400">Spesifikasi dan kondisi unit saat pembelian dilakukan</p>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium">{{ $order->items->count() }} item</span>
                </div>

                <div class="divide-y divide-white/10">
                    @foreach($order->items as $item)
                        @php
                            $cond = strtolower((string) ($item->condition_snapshot ?? 'new'));
                            $isUsed = in_array($cond, ['used', 'second', 'bekas'], true);
                        @endphp

                        <div class="p-5 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-semibold text-white">
                                            {{ $item->product_name_snapshot }}
                                        </h3>
                                        @if($isUsed)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                Second Grade {{ $item->condition_grade_snapshot ?? '-' }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                                Baru
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-zinc-400 font-mono mt-0.5">
                                        SKU: {{ $item->sku_snapshot ?? '-' }} &bull; Kuantitas: {{ $item->quantity }} unit
                                    </div>
                                </div>

                                <div class="text-left sm:text-right">
                                    <div class="text-xs text-zinc-400">Harga Satuan</div>
                                    <div class="text-sm font-bold text-white">
                                        Rp {{ number_format($item->unit_price_snapshot, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Panel Rincian Inspeksi Khusus Unit Second --}}
                            @if($isUsed)
                                <div class="rounded-xl bg-[#111113]/80 border border-white/5 p-3.5 text-xs grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-zinc-300">
                                    <div>
                                        <span class="text-zinc-500 block text-[10px] uppercase">Kesehatan Baterai</span>
                                        <span class="font-semibold text-emerald-400">{{ $item->battery_health_snapshot ? $item->battery_health_snapshot . '%' : '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500 block text-[10px] uppercase">Status Fungsi</span>
                                        <span class="font-medium text-zinc-200">{{ $item->functional_status_snapshot ?? 'Normal' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-zinc-500 block text-[10px] uppercase">Garansi Toko</span>
                                        <span class="font-medium text-zinc-200">{{ $item->warranty_info_snapshot ?? '1 Bulan' }}</span>
                                    </div>
                                    <div class="col-span-2 sm:col-span-3 pt-1 border-t border-white/5">
                                        <span class="text-zinc-500 block text-[10px] uppercase">Kelengkapan Unit</span>
                                        <span class="text-zinc-300">{{ $item->included_accessories_snapshot ?? 'Unit only' }}</span>
                                    </div>
                                    @if($item->defect_notes_snapshot)
                                        <div class="col-span-2 sm:col-span-3 pt-1 border-t border-white/5">
                                            <span class="text-zinc-500 block text-[10px] uppercase">Catatan Fisik</span>
                                            <span class="text-amber-300/90">{{ $item->defect_notes_snapshot }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Card Informasi Pengiriman --}}
            <div class="glass-surface rounded-2xl border border-white/10 p-5 sm:p-6 space-y-4">
                <div class="border-b border-white/10 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-white">Informasi Pengiriman</h2>
                    <span class="text-xs text-zinc-400">Dikirim via Kurir Ekspedisi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                    <div>
                        <div class="text-zinc-400 text-xs">Penerima Paket</div>
                        <div class="font-semibold text-white mt-1">{{ $order->recipient_name }}</div>
                        <div class="text-zinc-400 text-xs mt-0.5">{{ $order->recipient_phone }}</div>
                    </div>

                    <div>
                        <div class="text-zinc-400 text-xs">Status Pelacakan Resi</div>
                        @if($order->tracking_number)
                            <div class="mt-1">
                                <span class="font-semibold text-blue-400 font-mono">{{ $order->courier_name ?? 'Kurir' }}: {{ $order->tracking_number }}</span>
                            </div>
                        @else
                            <div class="text-zinc-500 mt-1 italic">
                                Nomor resi akan diperbarui oleh penjual saat paket dikirim.
                            </div>
                        @endif
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-white/5">
                        <div class="text-zinc-400 text-xs">Alamat Tujuan</div>
                        <div class="text-zinc-200 mt-1 leading-relaxed">
                            {{ $order->shipping_address }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Rincian Pembayaran & Aksi --}}
        <div class="space-y-6">

            <div class="glass-surface rounded-2xl border border-white/10 p-5 sm:p-6 space-y-5">
                <h3 class="text-base font-semibold text-white border-b border-white/10 pb-3">
                    Rincian Pembayaran
                </h3>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Subtotal Produk</span>
                        <span class="text-white font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Ongkos Kirim</span>
                        <span class="text-white font-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">Total Pembayaran</span>
                        <span class="text-lg font-bold text-white">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-2 text-xs text-zinc-400 space-y-1">
                        <div>Metode: <span class="text-zinc-200 font-medium">{{ strtoupper($order->payment?->provider ?? 'DOKU_SANDBOX') }}</span></div>
                        @if($order->payment?->paid_at)
                            <div>Waktu Bayar: <span class="text-emerald-400 font-medium">{{ $order->payment->paid_at->format('d M Y, H:i') }}</span></div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-white/10 space-y-3">
                    {{-- Tombol Bayar Sekarang --}}
                    @if($isPendingPayment && $hasCheckoutUrl && !$isHistoricalArchive)
                        <form action="{{ route('buyer.orders.payment.pay', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-sm cursor-pointer text-center">
                                Lanjutkan Pembayaran DOKU
                            </button>
                        </form>
                    @endif

                    {{-- Tombol Konfirmasi Terima (Hanya saat status Shipped) --}}
                    @if($isShipped)
                        <form action="{{ route('buyer.orders.confirm-receipt', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah menerima paket pesanan ini dengan baik? Pesanan akan ditandai selesai.');">
                            @csrf
                            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-sm transition shadow-sm cursor-pointer text-center">
                                Konfirmasi Pesanan Diterima
                            </button>
                        </form>
                    @endif

                    {{-- Tombol Batalkan Pesanan (Sebelum dikirim) --}}
                    @if($canCancel && !$isHistoricalArchive)
                        <form action="{{ route('buyer.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.');">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-950/40 hover:bg-rose-900/50 text-rose-300 border border-rose-500/30 font-medium text-xs sm:text-sm transition cursor-pointer text-center">
                                Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Keamanan & Jaminan --}}
            <div class="glass-surface rounded-2xl border border-white/10 p-4 text-xs text-zinc-400 space-y-2">
                <div class="flex items-center gap-2 text-zinc-200 font-semibold">
                    <span>🛡️</span>
                    <span>Jaminan Keaslian &amp; Transparansi</span>
                </div>
                <p class="leading-relaxed text-[11px] text-zinc-400">
                    Setiap unit second telah melewati pengecekan fungsi 100% dan kondisi fisik sesuai grade transparan iStore.
                </p>
            </div>

        </div>

    </div>

</div>

{{-- Script Auto-Refresh Polling Status Pembayaran DOKU (ADR-04) --}}
@if ($isPendingPayment)
    <script>
        (function () {
            const statusUrl = @json($statusEndpoint);
            const currentOrderStatus = @json($orderStatus);
            const currentPaymentStatus = @json($paymentStatus);
            const timerEl = document.getElementById('polling-timer');
            const manualBtn = document.getElementById('manual-status-check-btn');

            let countdown = 5;
            let pollInterval = null;

            async function checkStatus() {
                try {
                    const response = await fetch(statusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;

                    const data = await response.json();

                    if (
                        (data.order_status && data.order_status !== currentOrderStatus) ||
                        (data.payment_status && data.payment_status !== currentPaymentStatus)
                    ) {
                        window.location.reload();
                    }
                } catch (e) {
                    // Silent catch untuk kelancaran background polling
                }
            }

            function tick() {
                countdown--;
                if (timerEl) {
                    timerEl.textContent = countdown + 's';
                }

                if (countdown <= 0) {
                    countdown = 5;
                    if (!document.hidden) {
                        checkStatus();
                    }
                }
            }

            function startPolling() {
                if (!pollInterval) {
                    pollInterval = setInterval(tick, 1000);
                }
            }

            function stopPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            }

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopPolling();
                } else {
                    countdown = 5;
                    checkStatus();
                    startPolling();
                }
            });

            if (manualBtn) {
                manualBtn.addEventListener('click', function () {
                    manualBtn.disabled = true;
                    manualBtn.textContent = 'Memeriksa...';
                    checkStatus().finally(() => {
                        setTimeout(() => {
                            manualBtn.disabled = false;
                            manualBtn.textContent = 'Cek Status Pembayaran';
                        }, 1500);
                    });
                });
            }

            startPolling();
        })();
    </script>
@endif
@endsection
