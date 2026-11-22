
@extends('layouts.app')

@section('title', 'My Profile - iStore')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('buyer.dashboard') }}"
           class="text-sm text-gray-500 hover:text-black">
            ← Back to Dashboard
        </a>

        <h1 class="mt-4 text-3xl font-bold tracking-tight">
            My Profile
        </h1>

        <p class="mt-2 text-gray-500">
            Manage your personal information and account details.
        </p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please check your information:</p>

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

                {{-- Avatar --}}
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-black text-2xl font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>
                    <h2 class="text-xl font-semibold">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </p>

                    <span class="mt-2 inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                        {{ ucfirst(auth()->user()->role->value ?? auth()->user()->role) }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Profile Form --}}
        <div class="p-6 sm:p-8">

            <h3 class="mb-6 text-lg font-semibold">
                Personal Information
            </h3>

            {{-- Update Profile Form --}}
            <form method="POST" action="{{ route('buyer.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">

                    {{-- Full Name --}}
                    <div>
                        <label for="name"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', auth()->user()->name) }}"
                            required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Phone Number
                        </label>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', auth()->user()->phone) }}"
                            placeholder="Enter your phone number"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >

                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="address"
                               class="mb-2 block text-sm font-medium text-gray-700">
                            Address
                        </label>

                        <input
                            type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', auth()->user()->address) }}"
                            placeholder="Enter your address"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                        >

                        @error('address')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Buttons --}}
                <div class="mt-8 flex flex-wrap gap-3 border-t border-gray-200 pt-6">

                    <button
                        type="submit"
                        class="rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
                        Save Changes
                    </button>

                    <a
                        href="{{ route('buyer.dashboard') }}"
                        class="rounded-full border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                        Cancel
                    </a>

                </div>

            </form>

        </div>
    </div>

    {{-- Account Information --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6">

        <h3 class="font-semibold">
            Account Information
        </h3>

        <div class="mt-4 space-y-3 text-sm">

            {{-- Role --}}
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">
                    Account Role
                </span>

                <span class="font-medium">
                    {{ ucfirst(auth()->user()->role->value ?? auth()->user()->role) }}
                </span>
            </div>

            {{-- Member Since --}}
            <div class="flex justify-between gap-4">
                <span class="text-gray-500">
                    Member Since
                </span>

                <span class="font-medium">
                    {{ auth()->user()->created_at->format('d M Y') }}
                </span>
            </div>

        </div>

    </div>

</div>
@endsection
