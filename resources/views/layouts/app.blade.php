<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'iStore — Retailer Independen Apple' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        canvas: '#09090B',
                        surface: '#111113',
                        elevated: '#18181B',
                        border: '#27272A',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #09090B; color: #F4F4F5; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .glass-surface { background: rgba(17, 17, 19, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .glass-card { background: #111113; border: 1px solid #27272A; transition: all 0.2s ease-in-out; }
        .glass-card:hover { border-color: #3F3F46; transform: translateY(-2px); }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-canvas text-zinc-100 antialiased">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 glass-surface border-b border-border">
        <div class="max-w-[1200px] mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold tracking-tight text-white">
                <span class="text-blue-500"></span> iStore
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm text-zinc-300">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <a href="{{ route('catalog.index') }}" class="hover:text-white transition">Katalog Perangkat</a>
                <a href="{{ route('catalog.index', ['condition' => 'used']) }}" class="hover:text-white transition">Unit Second Terinspeksi</a>
            </nav>
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isSeller())
                        <a href="{{ route('seller.products.index') }}" class="px-3.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-xs font-medium text-white transition border border-zinc-700">Panel Seller</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-xs font-medium text-white transition">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-[1200px] mx-auto px-4 pt-4">
                <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="max-w-[1200px] mx-auto px-4 pt-4">
                <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer with Mandatory Independent Retailer Disclaimer -->
    <footer class="mt-16 border-t border-border bg-surface/50 text-zinc-400 text-xs py-8">
        <div class="max-w-[1200px] mx-auto px-4 text-center space-y-3">
            <p class="font-medium text-zinc-300">
                iStore — Retailer Independen Perangkat Apple Baru & Second Terinspeksi
            </p>
            <p class="text-zinc-500 max-w-2xl mx-auto leading-relaxed">
                iStore adalah retailer independen perangkat Apple, bukan Apple Store resmi, dan tidak berafiliasi dengan Apple Inc. Seluruh merek dagang dan hak cipta milik Apple Inc.
            </p>
            <p class="text-zinc-600">
                &copy; {{ date('Y') }} iStore. Hak cipta dilindungi undang-undang.
            </p>
        </div>
    </footer>
</body>
</html>
