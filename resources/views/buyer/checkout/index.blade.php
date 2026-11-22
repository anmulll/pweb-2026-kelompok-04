@extends('layouts.app', ['title' => 'Pengiriman & Pembayaran — iStore'])

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">

    {{-- Breadcrumb & Title --}}
    <div>
        <nav class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('buyer.cart.index') }}" class="hover:text-white transition">Keranjang</a>
            <span>/</span>
            <span class="text-zinc-200">Checkout</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Pengiriman &amp; Pembayaran
        </h1>
        <p class="mt-1 text-sm text-zinc-400">
            Lengkapi alamat pengiriman dan konfirmasi pesanan Anda untuk melanjutkan pembayaran.
        </p>
    </div>

    {{-- Alert Error Global jika ada kesalahan --}}
    @if($errors->any())
        <div class="rounded-xl border border-rose-500/30 bg-rose-950/40 p-4 text-xs sm:text-sm text-rose-300">
            <div class="font-semibold mb-1">Mohon lengkapi formulir dengan benar:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

        {{-- Kolom Kiri: Formulir Alamat Pengiriman --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-surface rounded-2xl border border-white/10 p-6 sm:p-8">
                <div class="flex items-center gap-3 border-b border-white/10 pb-4 mb-6">
                    <span class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold text-sm">
                        1
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-white">Informasi Penerima &amp; Pengiriman</h2>
                        <p class="text-xs text-zinc-400">Pastikan data pengiriman sudah akurat agar pesanan sampai tanpa kendala</p>
                    </div>
                </div>

                <form id="checkout-form" method="POST" action="{{ route('buyer.checkout.store') }}" class="space-y-5">
                    @csrf
                    {{-- Nilai expected_total untuk verifikasi Two-Phase Checkout --}}
                    <input type="hidden" name="expected_total" value="{{ $totalAmount }}">

                    {{-- Nama Penerima --}}
                    <div>
                        <label for="recipient_name" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                            Nama Penerima <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="recipient_name"
                            name="recipient_name"
                            value="{{ old('recipient_name', $user->name) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Nama penerima paket"
                        >
                        @error('recipient_name')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor Telepon Penerima --}}
                    <div>
                        <label for="recipient_phone" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                            Nomor Telepon / WhatsApp <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="tel"
                            id="recipient_phone"
                            name="recipient_phone"
                            value="{{ old('recipient_phone', $user->phone) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Contoh: 081234567890"
                        >
                        @error('recipient_phone')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat Pengiriman Lengkap --}}
                    <div>
                        <label for="shipping_address" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                            Alamat Pengiriman Lengkap <span class="text-rose-400">*</span>
                        </label>
                        <textarea
                            id="shipping_address"
                            name="shipping_address"
                            rows="3"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Tuliskan nama jalan, nomor bangunan, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos"
                        >{{ old('shipping_address', $user->default_shipping_address ?? $user->address) }}</textarea>
                        @error('shipping_address')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan Pengiriman (Opsional) --}}
                    <div>
                        <label for="notes" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                            Catatan untuk Penjual / Kurir (Opsional)
                        </label>
                        <input
                            type="text"
                            id="notes"
                            name="notes"
                            value="{{ old('notes') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                            placeholder="Contoh: Titipkan di pos satpam jika tidak ada orang di rumah"
                        >
                    </div>

                    {{-- Info Gateway Sandbox --}}
                    <div class="pt-4 border-t border-white/10 flex items-center gap-3 text-xs text-zinc-400">
                        <span class="text-blue-400 text-base">🔒</span>
                        <span>Metode Pembayaran: <strong>DOKU Checkout Sandbox</strong> (Virtual Account, QRIS, Kartu Kredit Sandbox).</span>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Rincian Produk & Total Checkout --}}
        <div class="glass-surface rounded-2xl border border-white/10 p-5 sm:p-6 space-y-5 sticky top-24">
            <h3 class="text-base font-semibold text-white border-b border-white/10 pb-3">
                Ringkasan Belanja
            </h3>

            {{-- Snapshot Produk --}}
            <div class="space-y-3 max-h-60 overflow-y-auto pr-1 divide-y divide-white/5">
                @foreach($cart->items as $item)
                    @php
                        $product = $item->product;
                        $isUsed = $product && method_exists($product, 'isUsed') ? $product->isUsed() : ($product?->condition?->value === 'used' || $product?->condition === 'used');
                    @endphp
                    <div class="pt-3 first:pt-0 flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-white truncate">
                                {{ $product?->name ?? 'Produk' }}
                            </div>
                            <div class="text-zinc-400 text-[11px]">
                                {{ $item->quantity }}x &bull; {{ $isUsed ? 'Second' : 'Baru' }}
                            </div>
                        </div>
                        <div class="text-right font-medium text-zinc-200">
                            Rp {{ number_format(($product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Breakdown Harga --}}
            <div class="pt-3 border-t border-white/10 space-y-2.5 text-xs sm:text-sm">
                <div class="flex items-center justify-between text-zinc-400">
                    <span>Subtotal Produk</span>
                    <span class="text-white font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between text-zinc-400">
                    <span>Ongkos Kirim Tetap</span>
                    <span class="text-white font-medium">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                </div>

                <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                    <span class="text-sm font-semibold text-white">Total Pembayaran</span>
                    <span class="text-lg font-bold text-white">
                        Rp {{ number_format($totalAmount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Action Submit --}}
            <div class="pt-2">
                <button
                    type="submit"
                    form="checkout-form"
                    class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-sm cursor-pointer"
                >
                    Buat Pesanan &amp; Bayar
                </button>
                <div class="mt-2 text-center">
                    <a href="{{ route('buyer.cart.index') }}" class="text-xs text-zinc-400 hover:text-white transition">
                        Kembali ke Keranjang
                    </a>
                </div>
            </div>

            <div class="text-[11px] text-zinc-500 text-center leading-relaxed">
                Dengan membuat pesanan, Anda menyetujui ketentuan pemesanan perangkat di iStore.
            </div>
        </div>

    </div>

</div>
@endsection
