@php
    $orderNumber = $order->order_number ?? ($order->code ?? ('#' . ($order->id ?? '')));
    $orderStatus = strtolower((string) ($order->status ?? 'pending'));
    $paymentStatus = strtolower((string) ($order->payment_status ?? ($order->payment->status ?? 'pending')));

    $buyerName = $order->buyer_name ?? ($order->user->name ?? ($order->customer_name ?? '-'));
    $buyerEmail = $order->buyer_email ?? ($order->user->email ?? '-');
    $buyerPhone = $order->buyer_phone ?? ($order->shipping_phone ?? ($order->phone ?? '-'));

    $shippingAddress = $order->shipping_address ?? ($order->address ?? '-');
    $courierName = old('courier', $order->courier ?? ($order->shipping_courier ?? ''));
    $trackingNumber = old('tracking_number', $order->tracking_number ?? ($order->resi ?? ''));

    $orderItems = $order->items ?? ($order->orderItems ?? collect());
    $totalAmount = $order->total_amount ?? ($order->total ?? 0);

    $canProcess = in_array($orderStatus, ['pending', 'paid', 'confirmed'], true);
    $canShip = in_array($orderStatus, ['pending', 'paid', 'processing', 'confirmed'], true);
    $canCancel = !in_array($orderStatus, ['shipped', 'completed', 'cancelled'], true);
@endphp

<x-seller-layout :title="'Pesanan ' . $orderNumber">
    <x-seller.page-header
        :title="'Detail Pesanan ' . $orderNumber"
        subtitle="Periksa informasi pembeli, pembayaran, item pesanan, alamat kirim, dan tindakan pemenuhan."
    >
        <x-slot:actions>
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

    {{-- Status Ringkas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="glass-surface rounded-2xl p-4 sm:p-5">
            <div class="text-xs text-zinc-400">Status Pesanan</div>
            <div class="mt-1.5 text-base font-semibold text-zinc-100">
                {{ $order->status ?? 'pending' }}
            </div>
        </div>

        <div class="glass-surface rounded-2xl p-4 sm:p-5">
            <div class="text-xs text-zinc-400">Status Pembayaran</div>
            <div class="mt-1.5 text-base font-semibold text-zinc-100">
                {{ $order->payment_status ?? ($order->payment->status ?? 'Pending') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Item Pesanan --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-surface rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10">
                    <h2 class="text-sm font-semibold text-zinc-100">Item Pesanan</h2>
                </div>

                @if (count($orderItems) > 0)
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-white/10 bg-[#18181B]/70 text-xs font-medium text-zinc-400">
                                    <th class="py-3 px-5">Produk</th>
                                    <th class="py-3 px-4">Harga Satuan</th>
                                    <th class="py-3 px-4">Qty</th>
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
                                    @endphp
                                    <tr>
                                        <td class="py-3.5 px-5">
                                            <div class="font-medium text-zinc-100">{{ $itemName }}</div>
                                            @if ($itemSku)
                                                <div class="text-xs font-mono text-zinc-400 mt-0.5">{{ $itemSku }}</div>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-zinc-300">
                                            Rp {{ number_format((float) $itemPrice, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap text-zinc-300">
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
                    <span class="text-sm font-medium text-zinc-300">Total Pesanan</span>
                    <span class="text-base font-semibold text-zinc-100">
                        Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Aksi Fulfillment: Process, Ship (Kurir & Resi), Cancel --}}
            <div class="glass-surface rounded-2xl p-5 space-y-5">
                <h2 class="text-sm font-semibold text-zinc-100">Tindakan Pesanan</h2>

                {{-- Tombol Process --}}
                @if ($canProcess && Route::has('seller.orders.process'))
                    <form method="POST" action="{{ route('seller.orders.process', $order) }}" class="m-0">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                        >
                            Proses Pesanan
                        </button>
                    </form>
                @endif

                {{-- Form Ship (Input Kurir & Resi) --}}
                @if ($canShip && Route::has('seller.orders.ship'))
                    <form method="POST" action="{{ route('seller.orders.ship', $order) }}" class="space-y-4 pt-2 border-t border-white/10">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="courier" class="block text-xs font-medium text-zinc-300 mb-1.5">
                                    Kurir Pengiriman <span class="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="courier"
                                    name="courier"
                                    value="{{ $courierName }}"
                                    required
                                    placeholder="Contoh: JNE, J&T, SiCepat"
                                    class="w-full rounded-xl bg-[#18181B] border @error('courier') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                                >
                                @error('courier')
                                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tracking_number" class="block text-xs font-medium text-zinc-300 mb-1.5">
                                    Nomor Resi <span class="text-rose-400">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="tracking_number"
                                    name="tracking_number"
                                    value="{{ $trackingNumber }}"
                                    required
                                    placeholder="Masukkan nomor resi pengiriman"
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
                            Kirim Pesanan &amp; Simpan Resi
                        </button>
                    </form>
                @endif

                {{-- Tombol Cancel --}}
                @if ($canCancel && Route::has('seller.orders.cancel'))
                    <form
                        method="POST"
                        action="{{ route('seller.orders.cancel', $order) }}"
                        onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');"
                        class="pt-2 border-t border-white/10 m-0"
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
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Buyer, Payment & Alamat --}}
        <div class="space-y-6">
            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Informasi Pembeli</h2>
                <div class="text-sm text-zinc-200">{{ $buyerName }}</div>
                <div class="text-xs text-zinc-400">{{ $buyerEmail }}</div>
                <div class="text-xs text-zinc-400">{{ $buyerPhone }}</div>
            </div>

            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Alamat Pengiriman</h2>
                <p class="text-sm text-zinc-300 whitespace-pre-line">{{ $shippingAddress }}</p>
                @if (!empty($order->courier) || !empty($order->shipping_courier))
                    <div class="pt-2 text-xs text-zinc-400">
                        Kurir: <span class="text-zinc-200">{{ $order->courier ?? $order->shipping_courier }}</span>
                    </div>
                @endif
                @if (!empty($order->tracking_number) || !empty($order->resi))
                    <div class="text-xs text-zinc-400">
                        Resi: <span class="font-mono text-zinc-200">{{ $order->tracking_number ?? $order->resi }}</span>
                    </div>
                @endif
            </div>

            <div class="glass-surface rounded-2xl p-5 space-y-2">
                <h2 class="text-sm font-semibold text-zinc-100 mb-2">Informasi Pembayaran</h2>
                <div class="text-xs text-zinc-400">
                    Metode: <span class="text-zinc-200">{{ $order->payment_method ?? ($order->payment->method ?? '-') }}</span>
                </div>
                <div class="text-xs text-zinc-400">
                    Status: <span class="text-zinc-200">{{ $order->payment_status ?? ($order->payment->status ?? 'Pending') }}</span>
                </div>
                @if (!empty($order->payment_reference) || !empty($order->payment->reference))
                    <div class="text-xs text-zinc-400">
                        Referensi: <span class="font-mono text-zinc-200">{{ $order->payment_reference ?? $order->payment->reference }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-seller-layout>