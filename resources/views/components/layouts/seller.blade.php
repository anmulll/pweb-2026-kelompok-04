<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Panel Seller — iStore' }}</title>
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
        .glass-surface { background: rgba(17, 17, 19, 0.9); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-canvas text-zinc-100 antialiased">
    <!-- Seller Navigation Bar (Full Width) -->
    <header class="sticky top-0 z-50 glass-surface border-b border-border">
        <div class="w-full px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('seller.products.index') }}" class="flex items-center gap-2 text-lg font-bold tracking-tight text-white">
                    <span class="text-blue-500"></span> iStore <span class="text-xs px-2 py-0.5 rounded bg-blue-900/60 text-blue-300 font-normal border border-blue-700/50">Seller</span>
                </a>
                <nav class="hidden md:flex items-center gap-6 text-sm text-zinc-300">
                    <a href="{{ route('seller.products.index') }}" class="{{ request()->routeIs('seller.products.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }} transition">Produk</a>
                    <a href="{{ route('seller.categories.index') }}" class="{{ request()->routeIs('seller.categories.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }} transition">Kategori</a>
                    <a href="{{ route('seller.promotions.index') }}" class="{{ request()->routeIs('seller.promotions.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }} transition">Promosi</a>
                </nav>
            </div>
            <div class="flex items-center gap-4 text-xs text-zinc-400">
                <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1.5 text-zinc-300">
                    Kunjungi Toko
                </a>
                <span class="h-4 w-px bg-zinc-700"></span>
                <span class="text-zinc-300 font-medium">{{ auth()->user()->name ?? 'Seller' }}</span>
            </div>
        </div>
    </header>

    <!-- Content (Full Width) -->
    <main class="w-full flex-grow px-6 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-border bg-surface/40 text-zinc-500 text-xs py-4 px-6 text-center">
        Panel Manajemen Seller iStore &copy; {{ date('Y') }}
    </footer>
</body>
</html>
