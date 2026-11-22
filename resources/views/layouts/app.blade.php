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
    <header class="border-b border-gray-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

            <a href="/" class="text-xl font-bold tracking-tight">
                iStore
            </a>

            <div class="flex items-center gap-6 text-sm">
                <a href="/" class="hover:text-gray-500">Home</a>
                <a href="/products" class="hover:text-gray-500">Products</a>
                <a href="/login" class="hover:text-gray-500">Login</a>
                <a href="/cart" class="hover:text-gray-500">Cart</a>
            </div>

        </nav>
    </header>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 py-6">
        <div class="mx-auto max-w-7xl px-6 text-sm text-gray-500">
            © {{ date('Y') }} iStore. All rights reserved.
        </div>
    </footer>

</body>
</html>