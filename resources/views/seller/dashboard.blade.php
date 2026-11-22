<x-seller-layout title="Ringkasan">
    <x-seller.page-header
        title="Ringkasan Penjual"
        subtitle="Pantau kinerja penjualan terverifikasi, pesanan, dan ketersediaan stok katalog."
    >
        <x-slot:actions>
            @if (Route::has('seller.products.create'))
                <a
                    href="{{ route('seller.products.create') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition"
                >
                    Tambah Produk
                </a>
            @endif

            @if (Route::has('seller.reports.index'))
                <a
                    href="{{ route('seller.reports.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                >
                    Lihat Laporan
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    @php
        // Nilai KPI berasal langsung dari controller / ReportService (tidak dihitung ulang di Blade atau JS)
        $verifiedRevenue = $verifiedRevenue ?? ($totalRevenue ?? ($kpis['verified_revenue'] ?? 0));
        $totalOrders = $totalOrders ?? ($kpis['total_orders'] ?? 0);
        $activeProducts = $activeProducts ?? ($kpis['active_products'] ?? 0);
        $lowStockRaw = $lowStockProducts ?? ($kpis['low_stock_products'] ?? 0);
        $lowStockProducts = $lowStockRaw instanceof \Countable ? $lowStockRaw->count() : (int) $lowStockRaw;

        // Dataset grafik berasal dari ReportService
        $chartLabels = $chartLabels ?? ($chartData['labels'] ?? []);
        $chartValues = $chartValues ?? ($chartData['values'] ?? []);
        $hasChartData = is_array($chartLabels) && count($chartLabels) > 0 && is_array($chartValues) && count($chartValues) > 0;
    @endphp

    {{-- 4 KPI Utama --}}
    <section aria-label="Ringkasan indikator utama" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Omzet Terverifikasi (Paid)</div>
            <div class="mt-2.5 text-2xl font-semibold tracking-tight text-zinc-100">
                Rp {{ number_format((float) $verifiedRevenue, 0, ',', '.') }}
            </div>
            <p class="mt-1.5 text-xs text-zinc-400">Hanya menghitung transaksi berstatus Paid</p>
        </div>

        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Jumlah Pesanan</div>
            <div class="mt-2.5 text-2xl font-semibold tracking-tight text-zinc-100">
                {{ number_format((int) $totalOrders, 0, ',', '.') }}
            </div>
            <p class="mt-1.5 text-xs text-zinc-400">Total pesanan masuk pada toko</p>
        </div>

        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Jumlah Produk Aktif</div>
            <div class="mt-2.5 text-2xl font-semibold tracking-tight text-zinc-100">
                {{ number_format((int) $activeProducts, 0, ',', '.') }}
            </div>
            <p class="mt-1.5 text-xs text-zinc-400">Produk yang tayang di katalog pembeli</p>
        </div>

        <div class="glass-surface rounded-2xl p-5">
            <div class="text-xs font-medium text-zinc-400">Stok Rendah</div>
            <div class="mt-2.5 text-2xl font-semibold tracking-tight text-zinc-100">
                {{ number_format((int) $lowStockProducts, 0, ',', '.') }}
            </div>
            <p class="mt-1.5 text-xs text-zinc-400">Produk yang perlu segera ditambah stoknya</p>
        </div>
    </section>

    {{-- Grafik Penjualan Terverifikasi (Chart.js) --}}
    <section aria-label="Grafik penjualan terverifikasi" class="glass-surface rounded-2xl p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
            <div>
                <h2 class="text-base font-semibold text-zinc-100">Tren Omzet Terverifikasi</h2>
                <p class="text-xs text-zinc-400 mt-0.5">
                    Visualisasi berdasarkan dataset layanan laporan backend (status pembayaran Paid).
                </p>
            </div>
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
                                borderColor: '#f4f4f5',
                                backgroundColor: 'rgba(244, 244, 245, 0.12)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#ffffff'
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