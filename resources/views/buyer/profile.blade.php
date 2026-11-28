@extends('layouts.app')

@section('title', 'Profil Saya — iStore')

@section('content')
@php
    $profileUser = $user ?? auth()->user();
@endphp

<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 sm:py-10 space-y-6">
    <div>
        <nav class="mb-2 flex items-center gap-2 text-xs text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-white transition">Dashboard</a>
            <span>/</span>
            <span class="text-zinc-200">Profil</span>
        </nav>

        <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
            Profil Pengguna
        </h1>
        <p class="mt-1 text-sm text-zinc-400">
            Kelola nama lengkap, nomor telepon, dan alamat pengiriman default Anda.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-500/30 bg-rose-950/50 px-4 py-3 text-xs text-rose-300">
            <p class="font-semibold">Mohon periksa kembali data yang dimasukkan:</p>
            <ul class="mt-1.5 list-disc space-y-1 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass-surface overflow-hidden rounded-3xl">
        {{-- Profile Header --}}
        <div class="border-b border-white/10 bg-[#18181B]/60 p-6 sm:p-8">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-xl font-bold text-white">
                    {{ strtoupper(substr($profileUser->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ $profileUser->name }}</h2>
                    <p class="text-xs text-zinc-400">{{ $profileUser->email }}</p>
                    <span class="mt-1.5 inline-block rounded-full border border-white/10 bg-zinc-800 px-2.5 py-0.5 text-[11px] font-medium text-zinc-300">
                        {{ ucfirst($profileUser->role->value ?? $profileUser->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form (FR-PROF-01: Email & Role immutable) --}}
        <div class="p-6 sm:p-8">
            <h3 class="mb-5 text-base font-semibold text-white">Informasi Pribadi</h3>

            <form method="POST" action="{{ route('buyer.profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $profileUser->name) }}"
                            required
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-medium text-zinc-400">
                            Email (Tidak dapat diubah)
                        </label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $profileUser->email }}"
                            disabled
                            class="w-full cursor-not-allowed rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2.5 text-sm text-zinc-500"
                        >
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Nomor Telepon
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $profileUser->phone) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >
                        @error('phone')
                            <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="default_shipping_address" class="mb-1.5 block text-xs font-medium text-zinc-300">
                            Alamat Pengiriman Default
                        </label>
                        <textarea
                            id="default_shipping_address"
                            name="default_shipping_address"
                            rows="3"
                            placeholder="Masukkan alamat pengiriman lengkap"
                            class="w-full rounded-xl border border-white/10 bg-[#18181B] px-4 py-2.5 text-sm text-white placeholder-zinc-500 outline-none transition focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        >{{ old('default_shipping_address', $profileUser->default_shipping_address) }}</textarea>
                        @error('default_shipping_address')
                            <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-white/10 pt-5">
                    <button
                        type="submit"
                        class="cursor-pointer rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
                        Simpan Perubahan
                    </button>

                    <a
                        href="{{ route('buyer.dashboard') }}"
                        class="rounded-xl border border-white/10 bg-[#18181B] px-6 py-2.5 text-sm font-medium text-zinc-300 transition hover:bg-zinc-800 hover:text-white">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="glass-surface rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white">Informasi Akun</h3>
        <div class="mt-4 space-y-2.5 text-xs">
            <div class="flex justify-between gap-4">
                <span class="text-zinc-400">Peran Akun</span>
                <span class="font-medium text-zinc-200">{{ ucfirst($profileUser->role->value ?? $profileUser->role) }}</span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-zinc-400">Bergabung Sejak</span>
                <span class="font-medium text-zinc-200">{{ $profileUser->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
