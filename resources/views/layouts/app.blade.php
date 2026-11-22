
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'iStore')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white text-gray-900 antialiased">

    {{-- Navbar --}}
    <header class="border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            {{-- Logo --}}
            <a href="{{ route('home') }}"
               class="text-xl font-bold tracking-tight">
                iStore
            </a>

            {{-- Navigation --}}
            <div class="flex items-center gap-4 text-sm sm:gap-6">

                <a href="{{ route('home') }}"
                   class="hover:text-gray-500">
                    Home
                </a>

                <a href="{{ route('products') }}"
                   class="hover:text-gray-500">
                    Products
                </a>

                {{-- Guest --}}
                @guest
                    <a href="{{ route('login') }}"
                       class="hover:text-gray-500">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-full bg-black px-4 py-2 text-white transition hover:bg-gray-800">
                        Register
                    </a>
                @endguest

                {{-- Login --}}
                @auth
                    @php
                        $userRole = auth()->user()->role->value
                            ?? auth()->user()->role;
                    @endphp

                    {{-- Buyer --}}
                    @if ($userRole === 'buyer')
                        <a href="{{ route('buyer.dashboard') }}"
                           class="hover:text-gray-500">
                            Dashboard
                        </a>

                        <a href="{{ route('buyer.orders') }}"
                           class="hover:text-gray-500">
                            Orders
                        </a>

                        <a href="{{ route('buyer.cart') }}"
                           class="hover:text-gray-500">
                            Cart
                        </a>

                        <a href="{{ route('buyer.profile') }}"
                           class="hover:text-gray-500">
                            Profile
                        </a>
                    @endif

                    {{-- Seller --}}
                    @if ($userRole === 'seller')
                        <a href="{{ route('seller.dashboard') }}"
                           class="hover:text-gray-500">
                            Dashboard
                        </a>
                    @endif

                    {{-- User --}}
                    <span class="hidden font-medium text-gray-700 sm:inline">
                        {{ auth()->user()->name }}
                    </span>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                                class="rounded-full border border-gray-300 px-4 py-2 transition hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                @endauth

            </div>
        </nav>
    </header>

    {{-- Main --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 py-6">
        <div class="mx-auto max-w-7xl px-6 text-sm text-gray-500">
            &copy; {{ date('Y') }} iStore. All rights reserved.
        </div>
    </footer>

</body>
</html>
