@extends('layouts.app', ['title' => 'Checkout'])

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-white mb-6">Pengiriman & Pembayaran</h1>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <form method="POST" action="{{ route('buyer.checkout.store') }}" class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl space-y-4">
                @csrf
                <input type="hidden" name="expected_total" value="{{ $totalAmount }}">

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Nama Penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', $user->name) }}" required class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Nomor Telepon</label>
                    <input type="tel" name="recipient_phone" value="{{ old('recipient_phone', $user->phone) }}" required class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Alamat Pengiriman</label>
                    <textarea name="shipping_address" rows="3" required class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white text-sm">{{ old('shipping_address', $user->address) }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg text-sm transition">
                        Buat Pesanan & Bayar
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 bg-zinc-900 border border-zinc-800 rounded-xl h-fit space-y-4">
            <h3 class="text-base font-medium text-white border-b border-zinc-800 pb-3">Ringkasan Pesanan</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-zinc-400">
                    <span>Subtotal</span>
                    <span class="text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-zinc-400">
                    <span>Ongkos Kirim</span>
                    <span class="text-white">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-zinc-800">
                    <span>Total</span>
                    <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
