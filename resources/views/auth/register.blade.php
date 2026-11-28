@extends('layouts.app')

@section('title', 'Daftar Akun — iStore')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg space-y-6">
        <div class="text-center space-y-2">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-[#111113] text-2xl text-blue-500">
                
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                Daftar Akun Pembeli
            </h1>
            <p class="text-sm text-zinc-400">
                Buat akun untuk menyimpan keranjang belanja dan memantau pesanan Anda
            </p>
        </div>

        <div class="glass-surface rounded-3xl p-7 sm:p-8 shadow-2xl">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-950/50 p-4 text-xs text-rose-300">
                    <ul class="list-disc space-y-1 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-xs font-medium text-zinc-300">
                        Nama Lengkap
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap"
                        required
                        autocomplete="name"
                        class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            autocomplete="username"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Nomor Telepon (Opsional)
                        </label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx"
                            autocomplete="tel"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <div>
                    <label for="address" class="mb-1.5 block text-xs font-medium text-zinc-300">
                        Alamat Pengiriman Default (Opsional)
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        rows="2"
                        placeholder="Jalan, nomor rumah, kecamatan, kota, kode pos"
                        class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >{{ old('address') }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Konfirmasi Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi kata sandi"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="mt-2 w-full cursor-pointer rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500"
                >
                    Buat Akun Pembeli
                </button>
            </form>

            <div class="mt-6 border-t border-white/10 pt-5 text-center text-xs text-zinc-400">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-400 hover:text-blue-300 transition">
                    Masuk di sini
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
