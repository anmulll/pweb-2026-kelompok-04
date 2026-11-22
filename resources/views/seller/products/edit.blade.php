<x-layouts.seller :title="'Edit Produk: ' . $product->name">
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Edit Produk</h1>
            <p class="text-xs text-zinc-400">Perbarui spesifikasi atau data inspeksi perangkat {{ $product->name }}</p>
        </div>

        <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="glass-surface p-6 rounded-2xl border border-zinc-800 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-white border-b border-zinc-800 pb-2">Informasi Dasar</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="category_id" class="text-xs font-semibold text-zinc-300">Kategori <span class="text-rose-500">*</span></label>
                        <select id="category_id" name="category_id" required class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="condition" class="text-xs font-semibold text-zinc-300">Kondisi Perangkat <span class="text-rose-500">*</span></label>
                        <select id="condition" name="condition" required onchange="toggleUsedFields(this.value)"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="new" {{ old('condition', is_object($product->condition) ? $product->condition->value : $product->condition) === 'new' ? 'selected' : '' }}>Baru (Brand New)</option>
                            <option value="used" {{ old('condition', is_object($product->condition) ? $product->condition->value : $product->condition) === 'used' ? 'selected' : '' }}>Second (Used)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-semibold text-zinc-300">Nama Produk <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="model" class="text-xs font-semibold text-zinc-300">Model Perangkat <span class="text-rose-500">*</span></label>
                        <input type="text" id="model" name="model" value="{{ old('model', $product->model) }}" required
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="sku" class="text-xs font-semibold text-zinc-300">SKU Unik <span class="text-rose-500">*</span></label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="storage" class="text-xs font-semibold text-zinc-300">Kapasitas Penyimpanan</label>
                        <input type="text" id="storage" name="storage" value="{{ old('storage', $product->storage) }}"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="color" class="text-xs font-semibold text-zinc-300">Warna</label>
                        <input type="text" id="color" name="color" value="{{ old('color', $product->color) }}"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="price" class="text-xs font-semibold text-zinc-300">Harga (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" id="price" name="price" value="{{ old('price', (int)$product->price) }}" required min="1" step="1000"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="stock" class="text-xs font-semibold text-zinc-300">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="image" class="text-xs font-semibold text-zinc-300">Ganti Foto Produk</label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-zinc-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                        @if($product->image)
                            <p class="text-[11px] text-zinc-500">Foto saat ini: {{ $product->image }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="text-xs font-semibold text-zinc-300">Deskripsi Lengkap</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- SECOND-HAND INSPECTION SECTION (Conditional) -->
            <div id="used-inspection-section" class="space-y-4 pt-4 border-t border-zinc-800 {{ $product->isUsed() ? '' : 'hidden' }}">
                <div class="flex items-center gap-2">
                    <span class="text-amber-400">🔍</span>
                    <h2 class="text-sm font-bold text-amber-400">Panel Inspeksi Fisik & Fungsi Unit Second</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="condition_grade" class="text-xs font-semibold text-zinc-300">Grade Fisik</label>
                        <select id="condition_grade" name="condition_grade" class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Pilih Grade</option>
                            @php $currGrade = is_object($product->condition_grade) ? $product->condition_grade->value : $product->condition_grade; @endphp
                            <option value="A" {{ old('condition_grade', $currGrade) === 'A' ? 'selected' : '' }}>Grade A — Mulus seperti baru</option>
                            <option value="B" {{ old('condition_grade', $currGrade) === 'B' ? 'selected' : '' }}>Grade B — Lecet pemakaian halus</option>
                            <option value="C" {{ old('condition_grade', $currGrade) === 'C' ? 'selected' : '' }}>Grade C — Ada dent atau baret fisik jelas</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="battery_health" class="text-xs font-semibold text-zinc-300">Kesehatan Baterai (0-100%)</label>
                        <input type="number" id="battery_health" name="battery_health" value="{{ old('battery_health', $product->battery_health) }}" min="0" max="100"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="functional_status" class="text-xs font-semibold text-zinc-300">Status Fungsi</label>
                        <input type="text" id="functional_status" name="functional_status" value="{{ old('functional_status', $product->functional_status) }}"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5">
                        <label for="warranty_info" class="text-xs font-semibold text-zinc-300">Garansi</label>
                        <input type="text" id="warranty_info" name="warranty_info" value="{{ old('warranty_info', $product->warranty_info) }}"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="included_accessories" class="text-xs font-semibold text-zinc-300">Kelengkapan Paket</label>
                        <input type="text" id="included_accessories" name="included_accessories" value="{{ old('included_accessories', $product->included_accessories) }}"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <label for="defect_notes" class="text-xs font-semibold text-zinc-300">Catatan Kekurangan</label>
                        <textarea id="defect_notes" name="defect_notes" rows="2"
                            class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">{{ old('defect_notes', $product->defect_notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-2 pt-2 border-t border-zinc-800">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="rounded bg-zinc-900 border-zinc-700 text-blue-600 focus:ring-0">
                <label for="is_active" class="text-xs font-semibold text-zinc-300">Status Aktif (Tampilkan di Katalog Publik)</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-800">
                <x-ui.button href="{{ route('seller.products.index') }}" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    Perbarui Produk
                </x-ui.button>
            </div>
        </form>
    </div>

    <script>
        function toggleUsedFields(condition) {
            const section = document.getElementById('used-inspection-section');
            if (condition === 'used') {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        }
    </script>
</x-layouts.seller>
