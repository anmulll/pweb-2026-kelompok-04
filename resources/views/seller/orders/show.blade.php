@php
    $orderNumber = $order->order_number ?? ($order->order_code ?? ($order->code ?? ('#' . ($order->id ?? ''))));
    $orderStatus = strtolower((string) ($order->status ?? 'pending_payment'));
    $paymentStatus = strtolower((string) ($order->payment_status ?? ($order->payment->status ?? 'pending')));

    $buyerName = $order->buyer_name ?? ($order->user->name ?? ($order->customer_name ?? '-'));
    $buyerEmail = $order->buyer_email ?? ($order->user->email ?? '-');
    $buyerPhone = $order->buyer_phone ?? ($order->shipping_phone ?? ($order->phone ?? '-'));

    $shippingAddress = $order->shipping_address ?? ($order->address ?? '-');
    $courierName = old('courier', $order->courier ?? ($order->shipping_courier ?? ''));
    $trackingNumber = old('tracking_number', $order->tracking_number ?? ($order->resi ?? ''));

    $orderItems = $order->items ?? ($order->orderItems ?? collect());
    $totalAmount = $order->total_amount ?? ($order->total ?? 0);

    // Aturan State Machine Pemenuhan Pesanan
    $canProcess = $orderStatus === 'paid';
    $canShip = $orderStatus === 'processing' || $orderStatus === 'paid';
    $canCancel = in_array($orderStatus, ['pending_payment', 'paid', 'processing'], true);
    $isPendingPayment = $orderStatus === 'pending_payment' && $paymentStatus === 'pending';

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

    $statusEndpoint = Route::has('seller.orders.payment.status')
        ? route('seller.orders.payment.status', $order->id ?? 0)
        : null;
@endphp

<x-seller-layout :title="'Pesanan ' . $orderNumber">
    <x-seller.page-header
        :title="'Detail Pesanan ' . $orderNumber"
        subtitle="Periksa informasi pembeli, verifikasi pembayaran DOKU, rincian unit terinspeksi, dan pemenuhan pesanan."
    >
        <x-slot:actions>
            @if ($isPendingPayment && $statusEndpoint)
                <button
                    type="button"
                    id="manual-status-check-btn"
                    class="inline-flex items-center justify-center rounded-xl px-3.5 py-2 text-xs sm:text-sm font-medium text-amber-200 bg-amber-950/40 hover:bg-amber-900/50 border border-amber-500/30 transition cursor-pointer"
                >
                    Cek Status Pembayaran
                </button>
            @endif

            @if (Route::has('seller.orders.index'))
                <a
                    href="{{ route('seller.orders.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                >
                    Kembali ke Daftar
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    {{-- Notifikasi Auto-Refresh Polling Aktif --}}
    @if ($isPendingPayment)
        <div id="polling-banner" class="mb-6 rounded-2xl glass-surface bg-amber-950/20 border border-amber-500/30 px-4 py-3 flex items-center justify-between text-xs sm:text-sm text-amber-200">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <span>Memantau pembayaran pembeli secara otomatis... Halaman akan memuat ulang seketika setelah pembayaran terverifikasi di DOKU.</span>
            </div>
            <span id="polling-timer" class="text-xs text-amber-400/80 font-mono hidden sm:inline">5s</span>
        </div>
    @endif

    {{-- Status Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="glass-surface rounded-2xl p-4 sm:p-5">
            <div class="text-xs text-zinc-400">Status Pesanan</div>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium border {{ $orderBadgeClass }}">
                    {{ $orderLabel }}
                </span>
            </div>
        </div>

        <div class="glass-surface rounded-2xl p-4 sm:p-5">
            <div class="text-xs text-zinc-400">Status Pembayaran Gateway DOKU</div>
            <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium border {{ $paymentBadgeClass }}">
                    {{ strtoupper($paymentStatus) }}
                </span>
                <span class="text-xs text-zinc-400">Sandbox Terverifikasi</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Item Pesanan & Tindakan --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-surface rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-zinc-100">Item Pesanan (Snapshot Permanen)</h2>
                    <span class="text-xs text-zinc-400">{{ count($orderItems) }} item</span>
                </div>

                @if (count($orderItems) > 0)
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                                    <th class="py-3 px-5">Produk &amp; Kondisi</th>
                                    <th class="py-3 px-4">Harga Satuan</th>
                                    <th class="py-3 px-4 text-center">Qty</th>
                                    <th class="py-3 px-5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10 text-sm">
                                @foreach ($orderItems as $item)
                                    @php
                                        $itemName = $item->product_name ?? ($item->name ?? ($item->product->name ?? 'Item'));
                                        $itemSku = $item->sku ?? ($item->product->sku ?? null);
                                        $itemPrice = $item->price ?? ($item->unit_price ?? 0);
                                        $itemQty = $item->quantity ?? ($item->qty ?? 1);
                                        $itemSubtotal = $item->subtotal ?? ($item->total ?? ((float) $itemPrice * (int) $itemQty));

                                        $itemCond = strtolower((string) ($item->condition ?? ($item->product->condition ?? 'new')));
                                        $isItemUsed = in_array($itemCond, ['used', 'second', 'bekas', 'bekas mulus'], true);
                                        $itemGrade = $item->condition_grade ?? ($item->grade ?? ($item->product->condition_grade ?? null));
                                        $itemBH = $item->battery_health ?? ($item->product->battery_health ?? null);
                                    @endphp
                                    <tr>
                                        <td class="py-3.5 px-5">
                                            <div class="font-medium text-zinc-100">{{ $itemName }}</div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                                @if ($itemSku)
                                                    <span class="font-mono text-zinc-400">{{ $itemSku }}</span>
                                                @endif

                                                @if ($isItemUsed)
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.2 text-[11px] font-medium bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                        Second @if ($itemGrade) — Grade {{ strtoupper($itemGrade) }} @endif
                                                    </span>
                                                    @if ($itemBH)
                                                        <span class="text-zinc-400 text-[11px]">BH {{ $itemBH }}%</span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.2 text-[11px] font-medium bg-zinc-800 text-zinc-300 border border-white/10">
                                                        Baru
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-zinc-300">
                                            Rp {{ number_format((float) $itemPrice, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-center text-zinc-300">
                                            {{ $itemQty }}
                                        </td>
                                        <td class="py-3.5 px-5 whitespace-nowrap text-right font-medium text-zinc-100">
                                            Rp {{ number_format((float) $itemSubtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-xs text-zinc-400">
                        Rincian item pesanan belum tersedia.
                    </div>
                @endif

                <div class="px-5 py-4 border-t border-white/10 bg-[#18181B]/50 flex items-center justify-between">
                    <span class="text-sm font-medium text-zinc-300">Total Pembayaran</span>
                    <span class="text-base font-semibold text-zinc-100">
                        Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Aksi Pemenuhan Pesanan (Fulfillment Actions) --}}
            <div class="glass-surface rounded-2xl p-5 space-y-5">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-100">Tindakan Pemenuhan Pesanan</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Transisi status mengikuti siklus: Dibayar &rarr; Diproses &rarr; Dikirim (wajib kurir &amp; resi).
                    </p>
                </div>

                {{-- Aksi 1: Tandai Sedang Diproses --}}
                @if ($canProcess && Route::has('seller.orders.process'))
                    <div class="pt-2">
                        <form method="POST" action="{{ route('seller.orders.process', $order->id ?? $order) }}" class="m-0">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                            >
                                Tandai Diproses
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Aksi 2: Kirim Pesanan (Wajib Kurir & Resi) --}}
                @if ($canShip && Route::has('seller.orders.ship'))
                    <div class="pt-3 border-t border-white/10 space-y-4">
                        <div class="text-xs font-medium text-zinc-200">Kirim Pesanan ke Ekspedisi:</div>
                        <form method="POST" action="{{ route('seller.orders.ship', $order->id ?? $order) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="courier" class="block text-xs font-medium text-zinc-300 mb-1.5">
                                        Nama Kurir Pengiriman <span class="text-rose-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="courier"
                                        name="courier"
                                        value="{{ $courierName }}"
                                        required
                                        placeholder="Contoh: JNE YES, SiCepat BEST, Paxel"
                                        class="w-full rounded-xl bg-[#18181B] border @error('courier') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                                    >
                                    @error('courier')
                                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tracking_number" class="block text-xs font-medium text-zinc-300 mb-1.5">
                                        Nomor Resi Pengiriman <span class="text-rose-400">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="tracking_number"
                                        name="tracking_number"
                                        value="{{ $trackingNumber }}"
                                        required
                                        placeholder="Contoh: JNE1234567890"
                                        class="w-full rounded-xl bg-[#18181B] border @error('tracking_number') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                                    >
                                    @error('tracking_number')
                                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                            >
                                Tandai Dikirim &amp; Simpan Resi
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Aksi 3: Batalkan Pesanan (Sebelum Dikirim) --}}
                @if ($canCancel && Route::has('seller.orders.cancel'))
                    <div class="pt-3 border-t border-white/10">
                        <form
                            method="POST"
                            action="{{ route('seller.orders.cancel', $order->id ?? $order) }}"
                            onsubmit="return confirm('Yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan secara otomatis.');"
                            class="m-0"
                        >
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-rose-300 bg-rose-950/40 hover:bg-rose-900/50 border border-rose-500/30 transition cursor-pointer"
                            >
                                Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Detail Pembeli, Alamat & Pembayaran --}}
        <div class="space-y-6">
            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Informasi Pembeli</h2>
                <div class="text-sm font-medium text-zinc-200">{{ $buyerName }}</div>
                <div class="text-xs text-zinc-400">{{ $buyerEmail }}</div>
                <div class="text-xs text-zinc-400">{{ $buyerPhone }}</div>
            </div>

            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Alamat Pengiriman</h2>
                <p class="text-sm text-zinc-300 whitespace-pre-line">{{ $shippingAddress }}</p>

                @if (!empty($order->courier) || !empty($order->shipping_courier))
                    <div class="pt-2 text-xs text-zinc-400">
                        Kurir: <span class="font-medium text-zinc-200">{{ $order->courier ?? $order->shipping_courier }}</span>
                    </div>
                @endif

                @if (!empty($order->tracking_number) || !empty($order->resi))
                    <div class="text-xs text-zinc-400">
                        Nomor Resi: <span class="font-mono text-zinc-200">{{ $order->tracking_number ?? $order->resi }}</span>
                    </div>
                @endif
            </div>

            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Rincian Pembayaran (DOKU Sandbox)</h2>
                <div class="text-xs text-zinc-400">
                    Metode: <span class="text-zinc-200">{{ $order->payment_method ?? ($order->payment->method ?? 'DOKU Checkout') }}</span>
                </div>
                <div class="text-xs text-zinc-400">
                    Status: <span class="font-medium text-zinc-200">{{ strtoupper($paymentStatus) }}</span>
                </div>
                @if (!empty($order->payment_reference) || !empty($order->payment->reference))
                    <div class="text-xs text-zinc-400">
                        Referensi Gateway: <span class="font-mono text-zinc-200">{{ $order->payment_reference ?? $order->payment->reference }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Script Auto-Refresh Polling Status Pembayaran (ADR-04 & FR-PAY-05) --}}
    @if ($isPendingPayment && $statusEndpoint)
        <script>
            (function () {
                const statusEndpoint = @json($statusEndpoint);
                const initialOrderStatus = @json($orderStatus);
                const initialPaymentStatus = @json($paymentStatus);
                const timerEl = document.getElementById('polling-timer');
                const manualCheckBtn = document.getElementById('manual-status-check-btn');

                let countdown = 5;
                let pollInterval = null;

                async function checkStatus() {
                    try {
                        const response = await fetch(statusEndpoint, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) return;

                        const data = await response.json();
                        const newOrderStatus = String(data.order_status || '').toLowerCase();
                        const newPaymentStatus = String(data.payment_status || '').toLowerCase();

                        // Reload otomatis hanya jika status berubah
                        if (
                            (newOrderStatus && newOrderStatus !== initialOrderStatus) ||
                            (newPaymentStatus && newPaymentStatus !== initialPaymentStatus)
                        ) {
                            window.location.reload();
                        }
                    } catch (e) {
                        // Silent catch untuk menjaga kelancaran antarmuka
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

                // Berhenti saat tab browser tidak aktif, cek langsung saat kembali aktif
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stopPolling();
                    } else {
                        countdown = 5;
                        checkStatus();
                        startPolling();
                    }
                });

                if (manualCheckBtn) {
                    manualCheckBtn.addEventListener('click', function () {
                        manualCheckBtn.disabled = true;
                        manualCheckBtn.textContent = 'Memeriksa...';
                        checkStatus().finally(() => {
                            setTimeout(() => {
                                manualCheckBtn.disabled = false;
                                manualCheckBtn.textContent = 'Cek Status Pembayaran';
                            }, 1500);
                        });
                    });
                }

                startPolling();
            })();
        </script>
    @endif
</x-seller-layout>
