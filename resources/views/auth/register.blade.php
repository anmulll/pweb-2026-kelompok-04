
@extends('layouts.app')

@section('title', 'Create Account - iStore')

@section('content')

<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight">
                Create account
            </h1>

            <p class="mt-2 text-gray-500">
                Join iStore and find your favorite device
            </p>
        </div>

        {{-- Register Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Register Form --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Full Name --}}
                <div class="mb-5">
                    <x-input
                        label="Full Name"
                        name="name"
                        placeholder="Your full name"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name"
                    />
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <x-input
                        label="Email Address"
                        name="email"
                        type="email"
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                    />
                </div>

                {{-- Phone --}}
                <div class="mb-5">
                    <x-input
                        label="Phone Number (Optional)"
                        name="phone"
                        type="text"
                        placeholder="Your phone number"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                    />
                </div>

                {{-- Address --}}
                <div class="mb-5">
                    <label for="address" class="mb-2 block text-sm font-medium text-gray-700">
                        Shipping Address (Optional)
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        placeholder="Your shipping address"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-black focus:ring-1 focus:ring-black"
                    >{{ old('address') }}</textarea>
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <x-input
                        label="Password"
                        name="password"
                        type="password"
                        placeholder="Create a password"
                        required
                        autocomplete="new-password"
                    />
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <x-input
                        label="Confirm Password"
                        name="password_confirmation"
                        type="password"
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password"
                    />
                </div>

                {{-- Register Button --}}
                <button
                    type="submit"
                    class="w-full rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800"
                >
                    Create Account
                </button>

            </form>

            {{-- Login Link --}}
            <div class="mt-6 text-center text-sm text-gray-500">
                Already have an account?

                <a href="{{ route('login') }}" class="font-medium text-black hover:underline">
                    Sign in
                </a>
            </div>

        </div>

    </div>

</div>

@endsection
