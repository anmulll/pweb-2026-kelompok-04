@extends('layouts.app')

@section('title', 'Masuk — iStore')

@section('content')
<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">
    <div class="w-full max-w-md space-y-6">
        <div class="text-center space-y-2">
            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-[#111113] text-2xl text-blue-500">
                
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                Masuk ke iStore
            </h1>
            <p class="text-sm text-zinc-400">
                Kelola keranjang belanja, pesanan, dan pembayaran Anda
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

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-xs font-medium text-zinc-300">
                        Alamat Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block text-xs font-medium text-zinc-300">
                        Kata Sandi
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan kata sandi"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-3 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    >
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-zinc-400 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                            class="rounded border-white/20 bg-[#18181B] text-blue-600 focus:ring-blue-500"
                        >
                        Ingat saya
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full cursor-pointer rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-500"
                >
                    Masuk
                </button>
            </form>

            <div class="mt-6 border-t border-white/10 pt-5 text-center text-xs text-zinc-400">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-semibold text-blue-400 hover:text-blue-300 transition">
                    Daftar sebagai Pembeli
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
