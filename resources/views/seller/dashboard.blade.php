<x-seller-layout title="Ringkasan">
    <x-seller.page-header
        title="Ringkasan Penjual"
        subtitle="Pantau kinerja penjualan terverifikasi, pesanan, dan ketersediaan stok katalog."
    >
        <x-slot:actions>
            @if (Route::has('seller.products.create'))
                <a
                    href="{{ route('seller.products.create') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 transition"
                >
                    Tambah Produk
                </a>
            @endif

            @if (Route::has('seller.reports.index'))
                <a
                    href="{{ route('seller.reports.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/10 transition"
                >
                    Lihat Laporan
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    @php
        $verifiedRevenue = $verifiedRevenue ?? ($totalRevenue ?? ($kpis['verified_revenue'] ?? 0));
        $totalOrders = $totalOrders ?? ($kpis['total_orders'] ?? 0);
        $activeProducts = $activeProducts ?? ($kpis['active_products'] ?? 0);
        $lowStockItems = $lowStockProducts instanceof \Illuminate\Support\Collection ? $lowStockProducts : collect();
        $lowStockCount = $lowStockProducts instanceof \Countable
            ? $lowStockProducts->count()
            : (int) ($lowStockProducts ?? ($kpis['low_stock_products'] ?? 0));
        $recentOrderList = $recentOrders ?? collect();

        $chartLabels = $chartLabels ?? ($chartData['labels'] ?? []);
        $chartValues = $chartValues ?? ($chartData['values'] ?? []);
        $hasChartData = is_array($chartLabels) && count($chartLabels) > 0 && is_array($chartValues) && count($chartValues) > 0;
    @endphp

    {{-- 4 KPI Utama (Bento Grid Matching Buyer Dashboard) --}}
    <section aria-label="Ringkasan indikator utama" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('seller.reports.index') }}" class="glass-card rounded-2xl p-5 block space-y-2">
            <span class="text-xs font-medium text-zinc-400">Omzet Terverifikasi (Paid)</span>
            <div class="text-2xl font-bold tracking-tight text-white">
                Rp {{ number_format((float) $verifiedRevenue, 0, ',', '.') }}
            </div>
            <p class="text-xs text-emerald-400">Hanya menghitung transaksi Paid</p>
        </a>

        <a href="{{ route('seller.orders.index') }}" class="glass-card rounded-2xl p-5 block space-y-2">
            <span class="text-xs font-medium text-zinc-400">Jumlah Pesanan</span>
            <div class="text-2xl font-bold tracking-tight text-white">
                {{ number_format((int) $totalOrders, 0, ',', '.') }}
            </div>
            <p class="text-xs text-blue-400">Lihat seluruh pesanan masuk</p>
        </a>

        <a href="{{ route('seller.products.index') }}" class="glass-card rounded-2xl p-5 block space-y-2">
            <span class="text-xs font-medium text-zinc-400">Jumlah Produk Aktif</span>
            <div class="text-2xl font-bold tracking-tight text-white">
                {{ number_format((int) $activeProducts, 0, ',', '.') }}
            </div>
            <p class="text-xs text-blue-400">Produk tayang di katalog pembeli</p>
        </a>

        <a href="{{ route('seller.products.index') }}" class="glass-card rounded-2xl p-5 block space-y-2">
            <span class="text-xs font-medium text-zinc-400">Stok Rendah</span>
            <div class="text-2xl font-bold tracking-tight {{ $lowStockCount > 0 ? 'text-amber-300' : 'text-white' }}">
                {{ number_format($lowStockCount, 0, ',', '.') }}
            </div>
            <p class="text-xs text-amber-400">Unit dengan stok &le; 1</p>
        </a>
    </section>

    {{-- Grafik Penjualan Terverifikasi (Chart.js) --}}
    <section aria-label="Grafik penjualan terverifikasi" class="glass-surface rounded-2xl p-5 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
            <div>
                <h2 class="text-base font-semibold text-white">Tren Omzet Terverifikasi (7 Hari Terakhir)</h2>
                <p class="text-xs text-zinc-400 mt-0.5">
                    Visualisasi transaksi berstatus Paid dari layanan laporan backend.
                </p>
            </div>
            <a href="{{ route('seller.reports.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                Buka Laporan Lengkap
            </a>
        </div>

        @if ($hasChartData)
            <div class="relative w-full h-72 sm:h-80">
                <canvas
                    id="seller-revenue-chart"
                    data-labels='@json(array_values($chartLabels))'
                    data-values='@json(array_values($chartValues))'
                    aria-label="Grafik tren omzet terverifikasi"
                    role="img"
                ></canvas>
            </div>
        @else
            <div class="w-full rounded-xl bg-[#18181B]/70 border border-white/10 px-4 py-12 text-center">
                <p class="text-sm font-medium text-zinc-200">Belum ada data grafik penjualan</p>
                <p class="mt-1 text-xs text-zinc-400">
                    Grafik akan tampil otomatis setelah tersedia transaksi berstatus Paid dari layanan laporan.
                </p>
            </div>
        @endif
    </section>

    {{-- Pesanan Terbaru & Peringatan Stok Rendah --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pesanan Terbaru --}}
        <section class="lg:col-span-2 glass-surface rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-sm font-semibold text-white">Pesanan Terbaru</h2>
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-medium text-blue-400 hover:text-blue-300 transition">
                    Lihat Semua
                </a>
            </div>

            @if ($recentOrderList->isEmpty())
                <div class="px-5 py-12 text-center text-xs text-zinc-400">
                    Belum ada pesanan masuk saat ini.
                </div>
            @else
                <div class="divide-y divide-white/10">
                    @foreach ($recentOrderList as $order)
                        @php
                            $rawStatus = $order->status ?? 'pending_payment';
                            $statusVal = strtolower($rawStatus instanceof \BackedEnum ? $rawStatus->value : (string) $rawStatus);
                        @endphp
                        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-3.5 hover:bg-zinc-900/50 transition">
                            <div class="space-y-0.5 min-w-0">
                                <div class="font-mono text-xs font-semibold text-white">{{ $order->order_code ?? ('#' . $order->id) }}</div>
                                <div class="text-xs text-zinc-400 truncate">
                                    {{ $order->recipient_name ?? ($order->user->name ?? 'Pembeli') }} • {{ $order->created_at?->format('d M Y, H:i') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="text-right">
                                    <div class="text-xs font-bold text-white">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
                                    <div class="text-[10px] uppercase tracking-wide text-zinc-400">{{ str_replace('_', ' ', $statusVal) }}</div>
                                </div>
                                <a href="{{ route('seller.orders.show', $order) }}" class="rounded-xl border border-white/10 bg-[#18181B] px-3 py-1.5 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:text-white transition">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Peringatan Stok Rendah --}}
        <section class="glass-surface rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="text-sm font-semibold text-white">Peringatan Stok Rendah</h2>
                <span class="rounded-full border border-amber-500/30 bg-amber-950/60 px-2 py-0.5 text-[10px] font-semibold text-amber-300">
                    {{ $lowStockCount }} unit
                </span>
            </div>

            @if ($lowStockItems->isEmpty())
                <div class="px-5 py-12 text-center text-xs text-zinc-400">
                    Seluruh produk aktif memiliki stok aman.
                </div>
            @else
                <div class="divide-y divide-white/10">
                    @foreach ($lowStockItems->take(6) as $prod)
                        <div class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-zinc-900/50 transition">
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-white truncate">{{ $prod->name }}</div>
                                <div class="text-[11px] font-mono text-zinc-400">{{ $prod->sku }} • Stok: <span class="text-amber-300 font-semibold">{{ $prod->stock }}</span></div>
                            </div>
                            <a href="{{ route('seller.products.edit', $prod) }}" class="shrink-0 rounded-xl border border-white/10 bg-[#18181B] px-2.5 py-1.5 text-[11px] font-medium text-zinc-200 hover:bg-zinc-800 hover:text-white transition">
                                Ubah
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    @if ($hasChartData)
        <script>
            (function initRevenueChart() {
                function renderChart() {
                    const canvas = document.getElementById('seller-revenue-chart');
                    const ChartConstructor = window.Chart;

                    if (!canvas || typeof ChartConstructor === 'undefined') {
                        return false;
                    }

                    let labels = [];
                    let values = [];

                    try {
                        labels = JSON.parse(canvas.dataset.labels || '[]');
                        values = JSON.parse(canvas.dataset.values || '[]');
                    } catch (e) {
                        return true;
                    }

                    if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0) {
                        return true;
                    }

                    new ChartConstructor(canvas, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Omzet Terverifikasi (Rp)',
                                data: values,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.16)',
                                borderWidth: 2.5,
                                tension: 0.35,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: '#60a5fa',
                                pointBorderColor: '#09090B',
                                pointBorderWidth: 1.5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: { color: '#d4d4d8' }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: '#a1a1aa' },
                                    grid: { color: 'rgba(255, 255, 255, 0.06)' }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { color: '#a1a1aa' },
                                    grid: { color: 'rgba(255, 255, 255, 0.06)' }
                                }
                            }
                        }
                    });

                    return true;
                }

                if (!renderChart()) {
                    window.addEventListener('DOMContentLoaded', renderChart);
                }
            })();
        </script>
    @endif
</x-seller-layout>
