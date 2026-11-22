@extends('layouts.app', ['title' => 'Keranjang Belanja — iStore'])

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">

    {{-- Breadcrumb & Title --}}
    <div>
        <nav class="flex items-center gap-2 text-xs text-zinc-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-zinc-200">Keranjang Belanja</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Keranjang Belanja
        </h1>
        <p class="mt-1 text-sm text-zinc-400">
            Periksa kembali perangkat Apple pilihan Anda sebelum melanjutkan ke proses checkout.
        </p>
    </div>

    {{-- Peringatan Item Bermasalah (Stale Items) --}}
    @if(isset($staleItems) && $staleItems->isNotEmpty())
        <div class="rounded-2xl border border-amber-500/30 bg-amber-950/20 p-4 sm:p-5 text-amber-200 text-xs sm:text-sm flex items-start gap-3">
            <span class="text-amber-400 text-base mt-0.5">⚠️</span>
            <div class="space-y-1">
                <div class="font-semibold text-amber-300">
                    Beberapa produk di keranjang Anda mengalami perubahan ketersediaan
                </div>
                <p class="text-amber-200/90 leading-relaxed">
                    Terdapat {{ $staleItems->count() }} item yang stoknya berkurang atau sudah tidak aktif. Silakan sesuaikan kuantitas atau hapus produk tersebut agar tombol checkout dapat diaktifkan kembali.
                </p>
            </div>
        </div>
    @endif

    @if($cart->items->isEmpty())
        {{-- Empty State --}}
        <div class="glass-surface rounded-2xl border border-white/10 p-12 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-800/60 flex items-center justify-center text-3xl">
                🛍️
            </div>
            <div>
                <h2 class="text-base font-semibold text-white">Keranjang Anda Masih Kosong</h2>
                <p class="text-xs sm:text-sm text-zinc-400 mt-1 max-w-md mx-auto">
                    Belum ada produk yang ditambahkan. Silakan jelajahi katalog produk Baru dan Second kami.
                </p>
            </div>
            <div>
                <x-ui.button href="{{ route('catalog.index') }}" variant="primary">
                    Jelajahi Katalog Produk
                </x-ui.button>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">

            {{-- Kolom Kiri: Daftar Item Keranjang --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="glass-surface rounded-2xl border border-white/10 overflow-hidden divide-y divide-white/10">
                    @foreach($cart->items as $item)
                        @php
                            $product = $item->product;
                            $isStale = method_exists($item, 'isStale') ? $item->isStale() : false;
                            $isUsed = $product && method_exists($product, 'isUsed') ? $product->isUsed() : ($product?->condition?->value === 'used' || $product?->condition === 'used');
                            $maxQty = $isUsed ? 1 : ($product?->stock ?? 1);
                        @endphp

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isStale ? 'bg-rose-950/10 border-l-4 border-l-rose-500' : '' }}">
                            {{-- Info Produk --}}
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 rounded-xl bg-zinc-800/80 border border-white/10 overflow-hidden flex items-center justify-center">
                                    @if($product && $product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-2xl text-zinc-600"></span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1 space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-semibold text-white truncate">
                                            {{ $product?->name ?? 'Produk Tidak Ditemukan' }}
                                        </h3>
                                        @if($isUsed)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                Second {{ $product?->condition_grade?->value ?? $product?->condition_grade ?? '' }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                                Baru
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-zinc-400 font-mono">
                                        SKU: {{ $product?->sku ?? '-' }}
                                    </div>

                                    <div class="text-sm font-semibold text-zinc-200">
                                        Rp {{ number_format($product?->price ?? 0, 0, ',', '.') }}
                                    </div>

                                    @if($isStale)
                                        <div class="text-[11px] text-rose-400 font-medium">
                                            @if(!$product || !$product->is_active || !$product->category?->is_active)
                                                Produk ini saat ini tidak lagi tersedia untuk dibeli.
                                            @elseif($product->stock < $item->quantity)
                                                Stok tersisa hanya {{ $product->stock }} unit (kuantitas di keranjang: {{ $item->quantity }}).
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Kuantitas & Aksi Hapus --}}
                            <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/5">
                                {{-- Form Update Kuantitas --}}
                                <form method="POST" action="{{ route('buyer.cart.update', $item->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <div class="flex items-center">
                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item->quantity }}"
                                            min="1"
                                            max="{{ $maxQty }}"
                                            class="w-16 px-2.5 py-1.5 rounded-lg bg-zinc-900 border border-zinc-700 text-white text-center text-xs sm:text-sm font-medium focus:outline-none focus:border-blue-500"
                                            {{ $isUsed ? 'readonly' : '' }}
                                        >
                                    </div>

                                    @if(!$isUsed)
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-medium border border-white/10 transition">
                                            Ubah
                                        </button>
                                    @endif
                                </form>

                                {{-- Subtotal Line --}}
                                <div class="text-right min-w-[100px] hidden sm:block">
                                    <div class="text-xs text-zinc-400">Subtotal</div>
                                    <div class="text-sm font-bold text-white">
                                        Rp {{ number_format(($product?->price ?? 0) * $item->quantity, 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- Form Hapus Item --}}
                                <form method="POST" action="{{ route('buyer.cart.destroy', $item->id) }}" onsubmit="return confirm('Hapus produk ini dari keranjang belanja?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-zinc-500 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus produk">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan Belanja --}}
            <div class="glass-surface rounded-2xl border border-white/10 p-5 sm:p-6 space-y-5 sticky top-24">
                <h2 class="text-base font-semibold text-white border-b border-white/10 pb-3">
                    Ringkasan Pesanan
                </h2>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Total Item</span>
                        <span class="text-white font-medium">{{ $cart->items->sum('quantity') }} unit</span>
                    </div>

                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Subtotal Belanja</span>
                        <span class="text-white font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Ongkos Kirim Tetap</span>
                        <span class="text-emerald-400 font-medium">Rp 50.000</span>
                    </div>

                    <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">Total Estimasi</span>
                        <span class="text-base sm:text-lg font-bold text-white">
                            Rp {{ number_format($subtotal + 50000, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Checkout CTA --}}
                <div class="pt-2">
                    @if($canCheckout)
                        <x-ui.button href="{{ route('buyer.checkout.index') }}" variant="primary" class="w-full py-3 text-sm">
                            Lanjut ke Pengiriman
                        </x-ui.button>
                    @else
                        <button disabled class="w-full py-3 px-4 rounded-xl bg-zinc-800 text-zinc-500 font-medium text-xs sm:text-sm cursor-not-allowed border border-white/5">
                            Checkout Dinonaktifkan
                        </button>
                        <p class="text-[11px] text-amber-400/90 text-center mt-2">
                            Perbaiki atau hapus produk yang tidak tersedia untuk melanjutkan ke checkout.
                        </p>
                    @endif
                </div>

                <div class="text-[11px] text-zinc-500 text-center leading-relaxed">
                    Pembayaran aman didukung oleh DOKU Checkout Sandbox. Stok produk baru direservasi secara atomik saat pesanan dibuat.
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
