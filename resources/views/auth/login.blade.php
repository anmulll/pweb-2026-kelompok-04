
@extends('layouts.app')

@section('title', 'Login - iStore')

@section('content')

<div class="flex min-h-[75vh] items-center justify-center px-4 py-12">

    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold tracking-tight">
                Welcome back
            </h1>

            <p class="mt-2 text-gray-500">
                Sign in to your iStore account
            </p>
        </div>

        {{-- Login Card --}}
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

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <x-input
                        label="Email Address"
                        name="email"
                        type="email"
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <x-input
                        label="Password"
                        name="password"
                        type="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    />
                </div>

                {{-- Remember Me --}}
                <div class="mb-6 flex items-center justify-between text-sm">

                    <label class="flex items-center gap-2 text-gray-600">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                            class="rounded border-gray-300"
                        >
                        Remember me
                    </label>

                </div>

                {{-- Login Button --}}
                <button
                    type="submit"
                    class="w-full rounded-full bg-black px-6 py-3 text-sm font-medium text-white transition hover:bg-gray-800"
                >
                    Sign In
                </button>

            </form>

            {{-- Register Link --}}
            <div class="mt-6 text-center text-sm text-gray-500">

                Don't have an account?

                <a href="{{ route('register') }}" class="font-medium text-black hover:underline">
                    Create account
                </a>

            </div>

        </div>

    </div>

</div>

@endsection
