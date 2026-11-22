@php
    $isEdit = isset($product) && !empty($product->id);
    $pageTitle = $isEdit ? 'Ubah Produk' : 'Tambah Produk';
    $formAction = $isEdit
        ? (Route::has('seller.products.update') ? route('seller.products.update', $product) : '#')
        : (Route::has('seller.products.store') ? route('seller.products.store') : '#');

    $categoryOptions = $categories ?? collect();
    $currentImage = $isEdit
        ? ($product->image_url ?? ($product->image ? asset('storage/' . ltrim($product->image, '/')) : null))
        : null;
@endphp

<x-seller-layout :title="$pageTitle">
    <x-seller.page-header
        :title="$pageTitle"
        :subtitle="$isEdit ? 'Perbarui detail perangkat, harga, ketersediaan stok, dan foto produk.' : 'Lengkapi informasi produk baru untuk ditayangkan pada katalog toko.'"
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
                        placeholder="Contoh: iPhone 15 Pro Max 256GB"
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
                        SKU <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="sku"
                        name="sku"
                        value="{{ old('sku', $product->sku ?? '') }}"
                        required
                        placeholder="Contoh: IP15PM-256-NAT"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('sku') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('sku')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
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
                        placeholder="Contoh: A3106 / MacBook Air M3"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('model') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('model')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kondisi --}}
                <div>
                    <label for="condition" class="block text-sm font-medium text-zinc-200">
                        Kondisi <span class="text-rose-400">*</span>
                    </label>
                    @php
                        $selectedCondition = old('condition', $product->condition ?? 'Baru');
                    @endphp
                    <select
                        id="condition"
                        name="condition"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('condition') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                        <option value="Baru" @selected($selectedCondition === 'Baru')>Baru (Segel / BNIB)</option>
                        <option value="Bekas Mulus" @selected($selectedCondition === 'Bekas Mulus')>Bekas Mulus (Pre-owned)</option>
                        <option value="Refurbished" @selected($selectedCondition === 'Refurbished')>Refurbished</option>
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
                        min="0"
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
                        value="{{ old('stock', $product->stock ?? 0) }}"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('stock') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('stock')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Unggah Gambar --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-zinc-200">
                        Gambar Produk {{ $isEdit ? '(opsional jika tidak diganti)' : '' }}
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
                            <span class="text-xs text-zinc-400">Gambar saat ini terpasang</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-200">
                    Deskripsi &amp; Spesifikasi Singkat
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Tuliskan kapasitas penyimpanan, warna, kelengkapan boks, atau garansi perangkat."
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
                    <span class="text-sm text-zinc-200">Tayangkan produk ini di katalog pembeli</span>
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
</x-seller-layout>