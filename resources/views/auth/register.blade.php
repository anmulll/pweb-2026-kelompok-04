
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

            {{-- Full Name --}}
            <div class="mb-5">
                <x-input
                    label="Full Name"
                    name="name"
                    placeholder="Your full name"
                />
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <x-input
                    label="Email Address"
                    name="email"
                    type="email"
                    placeholder="you@example.com"
                />
            </div>

            {{-- Password --}}
            <div class="mb-5">
                <x-input
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="Create a password"
                />
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <x-input
                    label="Confirm Password"
                    name="password_confirmation"
                    type="password"
                    placeholder="Confirm your password"
                />
            </div>

            {{-- Register Button --}}
            <button
                type="button"
                class="w-full rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800"
            >
                Create Account
            </button>

            {{-- Login Link --}}
            <div class="mt-6 text-center text-sm text-gray-500">

                Already have an account?

                <a href="/login" class="font-medium text-black hover:underline">
                    Sign in
                </a>

            </div>

        </div>

    </div>

</div>

@endsection
