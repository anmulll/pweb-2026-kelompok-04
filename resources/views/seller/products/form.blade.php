@php
    $isEdit = isset($product) && !empty($product->id);
    $pageTitle = $isEdit ? 'Ubah Produk' : 'Tambah Produk';
    $formAction = $isEdit
        ? (Route::has('seller.products.update') ? route('seller.products.update', $product->id ?? $product) : '#')
        : (Route::has('seller.products.store') ? route('seller.products.store') : '#');

    $categoryOptions = $categories ?? collect();
    $currentImage = $isEdit
        ? ($product->image_url ?? (!empty($product->image) ? asset('storage/' . ltrim($product->image, '/')) : null))
        : null;

    $rawCondition = old('condition', $product->condition ?? 'new');
    $rawConditionValue = $rawCondition instanceof \BackedEnum ? $rawCondition->value : (string) $rawCondition;
    $isUsed = in_array(strtolower($rawConditionValue), ['used', 'second', 'bekas', 'bekas mulus'], true);
@endphp

<x-seller-layout :title="$pageTitle">
    <x-seller.page-header
        :title="$pageTitle"
        :subtitle="$isEdit ? 'Perbarui detail perangkat, harga, inspeksi unit second, dan foto produk.' : 'Lengkapi informasi produk baru atau unit second terinspeksi untuk ditayangkan di katalog.'"
    >
        <x-slot:actions>
            @if (Route::has('seller.products.index'))
                <a
                    href="{{ route('seller.products.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                >
                    Kembali
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    <div class="glass-surface rounded-2xl p-5 sm:p-6 max-w-4xl">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama Produk --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-200">
                        Nama Produk <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $product->name ?? '') }}"
                        required
                        placeholder="Contoh: iPhone 15 Pro Max 256GB Natural Titanium"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('name') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-zinc-200">
                        Kategori <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="category_id"
                        name="category_id"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('category_id') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                        <option value="">Pilih kategori</option>
                        @foreach ($categoryOptions as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-zinc-200">
                        SKU (Kode Unik) <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="sku"
                        name="sku"
                        value="{{ old('sku', $product->sku ?? '') }}"
                        required
                        placeholder="Contoh: IP15PM-256-NAT-001"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('sku') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    <p class="mt-1 text-[11px] text-zinc-400">1 unit Second wajib memiliki 1 kode SKU individual unik.</p>
                    @error('sku')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model --}}
                <div>
                    <label for="model" class="block text-sm font-medium text-zinc-200">
                        Model Perangkat <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="model"
                        name="model"
                        value="{{ old('model', $product->model ?? '') }}"
                        required
                        placeholder="Contoh: A3106 / MacBook Pro M3 Pro"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('model') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('model')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kondisi --}}
                <div>
                    <label for="condition" class="block text-sm font-medium text-zinc-200">
                        Kondisi Perangkat <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="condition"
                        name="condition"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('condition') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                        <option value="new" @selected(!$isUsed)>Baru (Segel Pabrik / Brand New)</option>
                        <option value="used" @selected($isUsed)>Second (Unit Terinspeksi)</option>
                    </select>
                    @error('condition')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-zinc-200">
                        Harga (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        min="1"
                        step="1"
                        value="{{ old('price', isset($product->price) ? (int) $product->price : '') }}"
                        required
                        placeholder="Contoh: 21999000"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('price') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('price')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok --}}
                <div>
                    <label for="stock" class="block text-sm font-medium text-zinc-200">
                        Stok Tersedia <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        min="0"
                        step="1"
                        value="{{ old('stock', $product->stock ?? ($isUsed ? 1 : 0)) }}"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('stock') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    <p id="stock-hint" class="mt-1 text-[11px] text-zinc-400">
                        {{ $isUsed ? 'Unit Second individual dibatasi stok 0 atau 1.' : 'Jumlah unit baru yang tersedia di gudang.' }}
                    </p>
                    @error('stock')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unggah Gambar --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-zinc-200">
                        Foto Produk {{ $isEdit ? '(opsional jika tidak diganti)' : '' }}
                    </label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        class="mt-1.5 block w-full rounded-xl bg-[#18181B] border @error('image') border-rose-500/70 @else border-white/15 @enderror px-3 py-2 text-xs sm:text-sm text-zinc-300 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-800 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-zinc-100 hover:file:bg-zinc-700 transition"
                    >
                    @error('image')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror

                    @if ($currentImage)
                        <div class="mt-2.5 flex items-center gap-3">
                            <img
                                src="{{ $currentImage }}"
                                alt="{{ $product->name }}"
                                class="h-12 w-12 rounded-lg object-cover border border-white/10"
                            >
                            <span class="text-xs text-zinc-400">Foto saat ini terpasang</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Panel Inspeksi Unit Second (Wajib untuk Unit Second) --}}
            <div id="used-inspection-panel" class="rounded-2xl border border-amber-500/30 bg-amber-950/10 p-5 space-y-5 {{ $isUsed ? '' : 'hidden' }}">
                <div class="border-b border-amber-500/20 pb-3">
                    <h3 class="text-sm font-semibold text-amber-300 flex items-center gap-2">
                        <span>Panel Inspeksi Unit Second</span>
                    </h3>
                    <p class="text-xs text-zinc-400 mt-1">
                        Sesuai standar iStore, unit Second wajib memiliki data inspeksi fisik, baterai, fungsi, kelengkapan, dan garansi sebelum diaktifkan.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Grade Fisik --}}
                    <div>
                        <label for="condition_grade" class="block text-xs font-medium text-zinc-300">
                            Grade Fisik <span class="text-rose-400">*</span>
                        </label>
                        @php
                            $selectedGrade = old('condition_grade', $product->condition_grade ?? 'A');
                        @endphp
                        <select
                            id="condition_grade"
                            name="condition_grade"
                            class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('condition_grade') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                        >
                            <option value="A" @selected($selectedGrade === 'A')>Grade A — Mulus Sempurna (Like New)</option>
                            <option value="B" @selected($selectedGrade === 'B')>Grade B — Baret Halus Wajar</option>
                            <option value="C" @selected($selectedGrade === 'C')>Grade C — Tanda Pemakaian Jelas</option>
                        </select>
                        @error('condition_grade')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kesehatan Baterai (Battery Health) --}}
                    <div>
                        <label for="battery_health" class="block text-xs font-medium text-zinc-300">
                            Kesehatan Baterai (BH %)
                        </label>
                        <input
                            type="number"
                            id="battery_health"
                            name="battery_health"
                            min="50"
                            max="100"
                            value="{{ old('battery_health', $product->battery_health ?? '') }}"
                            placeholder="Contoh: 92"
                            class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('battery_health') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                        >
                        @error('battery_health')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Fungsi --}}
                    <div>
                        <label for="functional_status" class="block text-xs font-medium text-zinc-300">
                            Status Fungsional <span class="text-rose-400">*</span>
                        </label>
                        @php
                            $selectedFunc = old('functional_status', $product->functional_status ?? 'normal');
                        @endphp
                        <select
                            id="functional_status"
                            name="functional_status"
                            class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('functional_status') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                        >
                            <option value="normal" @selected($selectedFunc === 'normal')>Normal — Seluruh Fitur Berfungsi</option>
                            <option value="minus" @selected($selectedFunc === 'minus')>Minus — Ada Kendala Fungsi</option>
                        </select>
                        @error('functional_status')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Kelengkapan --}}
                    <div>
                        <label for="accessories" class="block text-xs font-medium text-zinc-300">
                            Kelengkapan Aksesori
                        </label>
                        <input
                            type="text"
                            id="accessories"
                            name="accessories"
                            value="{{ old('accessories', $product->accessories ?? '') }}"
                            placeholder="Contoh: Fullset Box Original + Kabel USB-C"
                            class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('accessories') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                        >
                        @error('accessories')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Garansi --}}
                    <div>
                        <label for="warranty_info" class="block text-xs font-medium text-zinc-300">
                            Informasi Garansi
                        </label>
                        <input
                            type="text"
                            id="warranty_info"
                            name="warranty_info"
                            value="{{ old('warranty_info', $product->warranty_info ?? '') }}"
                            placeholder="Contoh: Garansi Resmi iBox s.d. Okt 2027 / Garansi Toko 30 Hari"
                            class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('warranty_info') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                        >
                        @error('warranty_info')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan Minus / Kekurangan Fisik --}}
                <div>
                    <label for="defect_notes" class="block text-xs font-medium text-zinc-300">
                        Catatan Kekurangan Fisik &amp; Fungsi
                    </label>
                    <textarea
                        id="defect_notes"
                        name="defect_notes"
                        rows="2"
                        placeholder="Contoh: Baret halus di sudut bezel kanan bawah, layar bebas gores, Face ID dan True Tone aktif sempurna."
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('defect_notes') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >{{ old('defect_notes', $product->defect_notes ?? '') }}</textarea>
                    @error('defect_notes')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Deskripsi Produk Umum --}}
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-200">
                    Deskripsi &amp; Spesifikasi Produk
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Tuliskan kapasitas penyimpanan, chipset prosesor, warna bodi, dan spesifikasi penting lainnya."
                    class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('description') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                >{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div>
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked((bool) old('is_active', $product->is_active ?? true))
                        class="h-4 w-4 rounded border-white/20 bg-[#18181B] text-zinc-100 focus:ring-0"
                    >
                    <span class="text-sm text-zinc-200">Tayangkan produk ini di katalog toko</span>
                </label>
                @error('is_active')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-end gap-3">
                @if (Route::has('seller.products.index'))
                    <a
                        href="{{ route('seller.products.index') }}"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                    >
                        Batal
                    </a>
                @endif

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-5 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const conditionSelect = document.getElementById('condition');
            const inspectionPanel = document.getElementById('used-inspection-panel');
            const stockInput = document.getElementById('stock');
            const stockHint = document.getElementById('stock-hint');

            if (!conditionSelect || !inspectionPanel) return;

            function updateConditionState() {
                const isUsed = conditionSelect.value === 'used';
                if (isUsed) {
                    inspectionPanel.classList.remove('hidden');
                    if (stockInput && (!stockInput.value || parseInt(stockInput.value, 10) > 1)) {
                        stockInput.value = '1';
                    }
                    if (stockInput) {
                        stockInput.setAttribute('max', '1');
                    }
                    if (stockHint) {
                        stockHint.textContent = 'Unit Second individual dibatasi stok 0 atau 1.';
                    }
                } else {
                    inspectionPanel.classList.add('hidden');
                    if (stockInput) {
                        stockInput.removeAttribute('max');
                    }
                    if (stockHint) {
                        stockHint.textContent = 'Jumlah unit baru yang tersedia di gudang.';
                    }
                }
            }

            conditionSelect.addEventListener('change', updateConditionState);
            updateConditionState();
        })();
    </script>
</x-seller-layout>
