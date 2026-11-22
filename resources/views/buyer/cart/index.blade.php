@extends('layouts.app', ['title' => 'Keranjang Belanja'])

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white mb-6">Keranjang Belanja</h1>

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

    @if($cart->items->isEmpty())
        <div class="p-8 text-center text-zinc-400 bg-zinc-900 border border-zinc-800 rounded-xl">
            Keranjang belanja Anda masih kosong.
        </div>
    @else
        <div class="space-y-4">
            @foreach($cart->items as $item)
                <div class="flex items-center justify-between p-4 bg-zinc-900 border border-zinc-800 rounded-xl {{ $item->isStale() ? 'opacity-50 border-rose-800/50' : '' }}">
                    <div class="flex-1">
                        <h3 class="text-white font-medium">{{ $item->product?->name ?? 'Produk Tidak Ditemukan' }}</h3>
                        <p class="text-sm text-zinc-400">Rp {{ number_format($item->product?->price ?? 0, 0, ',', '.') }}</p>
                        @if($item->isStale())
                            <span class="inline-block mt-1 text-xs text-rose-400">Tidak tersedia / stok tidak mencukupi</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        <form method="POST" action="{{ route('buyer.cart.update', $item->id) }}" class="flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 px-2 py-1 text-sm bg-zinc-800 border border-zinc-700 rounded text-white text-center">
                            <button type="submit" class="text-xs px-2 py-1 bg-zinc-700 text-white rounded hover:bg-zinc-600">Update</button>
                        </form>
                        <form method="POST" action="{{ route('buyer.cart.destroy', $item->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 p-6 bg-zinc-900 border border-zinc-800 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-sm text-zinc-400">Total Pembayaran</p>
                <p class="text-2xl font-bold text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
            </div>
            @if($canCheckout)
                <a href="{{ route('buyer.checkout.index') }}" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition">
                    Lanjut ke Checkout
                </a>
            @else
                <button disabled class="px-6 py-2.5 rounded-lg bg-zinc-800 text-zinc-500 cursor-not-allowed font-medium text-sm">
                    Checkout Diblokir (Perbaiki Item)
                </button>
            @endif
        </div>
    @endif
</div>
@endsection
