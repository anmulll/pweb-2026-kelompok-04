@extends('layouts.app', ['title' => 'Profil Saya — iStore'])

@section('content')
@php
    $profileUser = $user ?? auth()->user();
@endphp

<div class="max-w-[800px] mx-auto px-4 sm:px-6 py-8 sm:py-10 space-y-6">

    {{-- Breadcrumb & Header --}}
    <div>
        <a href="{{ route('buyer.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-white transition mb-3">
            <span>Kembali ke Dashboard</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">
            Profil Pengguna
        </h1>
        <p class="mt-1 text-sm text-zinc-400">
            Kelola data akun, nomor telepon, dan alamat pengiriman default Anda.
        </p>
    </div>

    {{-- Error Alert jika ada validasi gagal --}}
    @if ($errors->any())
        <div class="rounded-xl border border-rose-500/30 bg-rose-950/40 p-4 text-xs sm:text-sm text-rose-300">
            <div class="font-semibold mb-1">Terdapat kesalahan pengisian data:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="glass-surface rounded-2xl border border-white/10 overflow-hidden">
        {{-- Profile Header / Avatar --}}
        <div class="p-6 sm:p-8 border-b border-white/10 bg-zinc-900/40 flex items-center gap-4">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-xl sm:text-2xl font-bold text-blue-400">
                {{ strtoupper(substr($profileUser->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-semibold text-white">{{ $profileUser->name }}</h2>
                <p class="text-xs sm:text-sm text-zinc-400 font-mono">{{ $profileUser->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        {{ ucfirst($profileUser->role->value ?? $profileUser->role) }}
                    </span>
                    <span class="text-xs text-zinc-500">
                        Bergabung sejak {{ $profileUser->created_at?->format('d M Y') ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form Edit Data (FR-PROF-01: Name, Phone, Address editable; Email & Role immutable) --}}
        <form method="POST" action="{{ route('buyer.profile.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                        Nama Lengkap <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $profileUser->name) }}"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                        placeholder="Masukkan nama lengkap Anda"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (Immutable) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="email" class="block text-xs font-medium uppercase tracking-wider text-zinc-300">
                            Alamat Email
                        </label>
                        <span class="text-xs text-zinc-500">Email tidak dapat diubah</span>
                    </div>
                    <input
                        type="email"
                        id="email"
                        value="{{ $profileUser->email }}"
                        disabled
                        class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/40 border border-zinc-800 text-zinc-500 text-sm cursor-not-allowed font-mono"
                    >
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label for="phone" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                        Nomor Telepon
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $profileUser->phone) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                        placeholder="Contoh: 081234567890"
                    >
                    @error('phone')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat Pengiriman Default --}}
                <div>
                    <label for="default_shipping_address" class="block text-xs font-medium uppercase tracking-wider text-zinc-300 mb-1.5">
                        Alamat Pengiriman Default
                    </label>
                    <textarea
                        id="default_shipping_address"
                        name="default_shipping_address"
                        rows="3"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-900/80 border border-zinc-700/80 text-white text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                        placeholder="Tuliskan alamat lengkap beserta jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, dan kode pos"
                    >{{ old('default_shipping_address', $profileUser->default_shipping_address ?? $profileUser->address) }}</textarea>
                    @error('default_shipping_address')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                <x-ui.button type="submit" variant="primary">
                    Simpan Perubahan
                </x-ui.button>
                <x-ui.button href="{{ route('buyer.dashboard') }}" variant="secondary">
                    Batal
                </x-ui.button>
            </div>
        </form>
    </div>

</div>
@endsection
