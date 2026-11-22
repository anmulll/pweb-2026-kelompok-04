<x-layouts.seller title="Tambah Produk — Seller iStore">
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Tambah Produk Baru</h1>
            <p class="text-xs text-zinc-400">Daftarkan unit perangkat Apple baru atau second dengan transparansi data</p>
        </div>

        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="glass-surface p-6 rounded-2xl border border-zinc-800 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-white border-b border-zinc-800 pb-2">Informasi Dasar</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="category_id" class="text-xs font-semibold text-zinc-300">Kategori <span class="text-rose-500">*</span></label>
                        <select id="category_id" name="category_id" required class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="condition" class="text-xs font-semibold text-zinc-300">Kondisi Perangkat <span class="text-rose-500">*</span></label>
                        <select id="condition" name="condition" required onchange="toggleUsedFields(this.value)"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="new" {{ old('condition', 'new') === 'new' ? 'selected' : '' }}>Baru (Brand New)</option>
                            <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Second (Used)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-semibold text-zinc-300">Nama Produk <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: iPhone 15 Pro 256GB Natural Titanium"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="model" class="text-xs font-semibold text-zinc-300">Model Perangkat <span class="text-rose-500">*</span></label>
                        <input type="text" id="model" name="model" value="{{ old('model') }}" required placeholder="Contoh: iPhone 15 Pro, MacBook Pro M3"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="sku" class="text-xs font-semibold text-zinc-300">SKU Unik <span class="text-rose-500">*</span></label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required placeholder="Contoh: IP15P-256-NT-01"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="storage" class="text-xs font-semibold text-zinc-300">Kapasitas Penyimpanan</label>
                        <input type="text" id="storage" name="storage" value="{{ old('storage') }}" placeholder="Contoh: 128GB, 256GB, 512GB, 1TB"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="color" class="text-xs font-semibold text-zinc-300">Warna</label>
                        <input type="text" id="color" name="color" value="{{ old('color') }}" placeholder="Contoh: Natural Titanium, Space Black"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="price" class="text-xs font-semibold text-zinc-300">Harga (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" required min="1" step="1000" placeholder="Contoh: 18500000"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="stock" class="text-xs font-semibold text-zinc-300">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 1) }}" required min="0"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                        <p id="stock-hint" class="text-[11px] text-zinc-500">Untuk produk second, 1 unit = 1 SKU unik (stok 0 atau 1).</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="image" class="text-xs font-semibold text-zinc-300">Foto Produk</label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-zinc-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="text-xs font-semibold text-zinc-300">Deskripsi Lengkap</label>
                    <textarea id="description" name="description" rows="3" placeholder="Jelaskan spesifikasi dan keunggulan perangkat..."
                        class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- SECOND-HAND INSPECTION SECTION (Conditional) -->
            <div id="used-inspection-section" class="space-y-4 pt-4 border-t border-zinc-800 {{ old('condition') === 'used' ? '' : 'hidden' }}">
                <div class="flex items-center gap-2">
                    <span class="text-amber-400">🔍</span>
                    <h2 class="text-sm font-bold text-amber-400">Panel Inspeksi Fisik & Fungsi Unit Second</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="condition_grade" class="text-xs font-semibold text-zinc-300">Grade Fisik</label>
                        <select id="condition_grade" name="condition_grade" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Pilih Grade</option>
                            <option value="A" {{ old('condition_grade') === 'A' ? 'selected' : '' }}>Grade A — Mulus seperti baru, tanpa lecet terlihat</option>
                            <option value="B" {{ old('condition_grade') === 'B' ? 'selected' : '' }}>Grade B — Lecet pemakaian halus wajar</option>
                            <option value="C" {{ old('condition_grade') === 'C' ? 'selected' : '' }}>Grade C — Ada dent atau baret fisik jelas</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="battery_health" class="text-xs font-semibold text-zinc-300">Kesehatan Baterai / Battery Health (0-100%)</label>
                        <input type="number" id="battery_health" name="battery_health" value="{{ old('battery_health') }}" min="0" max="100" placeholder="Contoh: 88"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="functional_status" class="text-xs font-semibold text-zinc-300">Status Fungsi</label>
                        <input type="text" id="functional_status" name="functional_status" value="{{ old('functional_status', 'Normal') }}" placeholder="Contoh: Semua Fungsi Normal, FaceID On, TrueTone On"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="warranty_info" class="text-xs font-semibold text-zinc-300">Garansi</label>
                        <input type="text" id="warranty_info" name="warranty_info" value="{{ old('warranty_info', 'Garansi Toko 1 Bulan') }}" placeholder="Contoh: Garansi Resmi iBox s.d. Des 2026 / Garansi Toko 1 Bulan"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="included_accessories" class="text-xs font-semibold text-zinc-300">Kelengkapan Paket (Aksesori Termasuk)</label>
                        <input type="text" id="included_accessories" name="included_accessories" value="{{ old('included_accessories', 'Fullset Box + Kabel Original') }}" placeholder="Contoh: Fullset Box + Kabel Type-C Original / Unit Only"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="defect_notes" class="text-xs font-semibold text-zinc-300">Catatan Kekurangan (Defect Notes)</label>
                        <textarea id="defect_notes" name="defect_notes" rows="2" placeholder="Tuliskan secara jujur jika ada baret halus di bezel, jamur tipis, dsb..."
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">{{ old('defect_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-2 pt-2 border-t border-zinc-800">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded bg-zinc-900 border-zinc-700 text-blue-600 focus:ring-0">
                <label for="is_active" class="text-xs font-semibold text-zinc-300">Status Aktif (Tampilkan di Katalog Publik)</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-800">
                <x-ui.button href="{{ route('seller.products.index') }}" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    Simpan Produk
                </x-ui.button>
            </div>
        </form>
    </div>

    <script>
        function toggleUsedFields(condition) {
            const section = document.getElementById('used-inspection-section');
            const stockInput = document.getElementById('stock');
            const stockHint = document.getElementById('stock-hint');

            if (condition === 'used') {
                section.classList.remove('hidden');
                stockInput.value = 1;
                stockInput.max = 1;
                stockHint.innerText = 'Unit Second dikunci maksimal 1 unit per SKU unik.';
            } else {
                section.classList.add('hidden');
                stockInput.removeAttribute('max');
                stockHint.innerText = 'Jumlah stok unit baru yang tersedia.';
            }
        }
    </script>
</x-layouts.seller>
