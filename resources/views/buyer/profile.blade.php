@extends('layouts.app')

@section('title', 'Profil Saya — iStore')

@section('content')
@php
    $profileUser = $user ?? auth()->user();
@endphp

<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('buyer.dashboard') }}"
           class="text-sm text-gray-500 hover:text-black">
            Kembali ke Dashboard
        </a>

        <h1 class="mt-4 text-3xl font-bold tracking-tight">
            Profil Pengguna
        </h1>

        <p class="mt-2 text-gray-500">
            Kelola nama lengkap, nomor telepon, dan alamat pengiriman default Anda.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Mohon periksa kembali data yang dimasukkan:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Profile Card --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">

        {{-- Profile Header --}}
        <div class="border-b border-gray-200 bg-gray-50 p-6 sm:p-8">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-black text-2xl font-semibold text-white">
                    {{ strtoupper(substr($profileUser->name, 0, 1)) }}
                </div>

                <div>
                    <h2 class="text-xl font-semibold">{{ $profileUser->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $profileUser->email }}</p>
                    <span class="mt-2 inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                        {{ ucfirst($profileUser->role->value ?? $profileUser->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Profile Form (FR-PROF-01: Email & Role immutable) --}}
        <div class="p-6 sm:p-8">
            <h3 class="mb-6 text-lg font-semibold">Informasi Pribadi</h3>

            <form method="POST" action="{{ route('buyer.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $profileUser->name) }}"
                            required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                            Email (Tidak dapat diubah)
                        </label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $profileUser->email }}"
                            disabled
                            class="w-full cursor-not-allowed rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-gray-500"
                        >
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">
                            Nomor Telepon
                        </label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $profileUser->phone) }}"
                            placeholder="Masukkan nomor telepon"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="default_shipping_address" class="mb-2 block text-sm font-medium text-gray-700">
                            Alamat Pengiriman Default
                        </label>
                        <textarea
                            id="default_shipping_address"
                            name="default_shipping_address"
                            rows="3"
                            placeholder="Masukkan alamat pengiriman lengkap"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >{{ old('default_shipping_address', $profileUser->default_shipping_address) }}</textarea>
                        @error('default_shipping_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-gray-200 pt-6">
                    <button
                        type="submit"
                        class="rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
                        Simpan Perubahan
                    </button>

                    <a
                        href="{{ route('buyer.dashboard') }}"
                        class="rounded-full border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Account Information --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">
        <h3 class="font-semibold">Informasi Akun</h3>
        <div class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Peran Akun</span>
                <span class="font-medium">{{ ucfirst($profileUser->role->value ?? $profileUser->role) }}</span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">Bergabung Sejak</span>
                <span class="font-medium">{{ $profileUser->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
