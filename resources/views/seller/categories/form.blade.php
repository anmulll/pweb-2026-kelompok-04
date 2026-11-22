@php
    $isEdit = isset($category) && !empty($category->id);
    $pageTitle = $isEdit ? 'Ubah Kategori' : 'Tambah Kategori';
    $formAction = $isEdit
        ? (Route::has('seller.categories.update') ? route('seller.categories.update', $category->id ?? $category) : '#')
        : (Route::has('seller.categories.store') ? route('seller.categories.store') : '#');
@endphp

<x-seller-layout :title="$pageTitle">
    <x-seller.page-header
        :title="$pageTitle"
        :subtitle="$isEdit ? 'Perbarui informasi dan status visibilitas kategori produk.' : 'Buat kategori baru untuk pengelompokan produk di toko.'"
    >
        <x-slot:actions>
            @if (Route::has('seller.categories.index'))
                <a
                    href="{{ route('seller.categories.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                >
                    Kembali
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    <div class="glass-surface rounded-2xl p-5 sm:p-6 max-w-2xl">
        <form method="POST" action="{{ $formAction }}" class="space-y-5">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- Nama Kategori --}}
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-200">
                    Nama Kategori <span class="text-rose-400">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name ?? '') }}"
                    required
                    placeholder="Contoh: iPhone, MacBook, iPad, Aksesoris"
                    class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('name') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                >
                @error('name')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug (Opsional jika diisi manual atau otomatis dari backend) --}}
            <div>
                <label for="slug" class="block text-sm font-medium text-zinc-200">
                    Slug URL
                </label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $category->slug ?? '') }}"
                    placeholder="Contoh: iphone (kosongkan jika dibuat otomatis)"
                    class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('slug') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                >
                @error('slug')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi Singkat --}}
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-200">
                    Deskripsi
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Keterangan singkat mengenai kelompok produk dalam kategori ini."
                    class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('description') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                >{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div class="pt-1">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @checked((bool) old('is_active', $category->is_active ?? true))
                        class="h-4 w-4 rounded border-white/20 bg-[#18181B] text-zinc-100 focus:ring-0"
                    >
                    <span class="text-sm text-zinc-200">Kategori aktif dan dapat dipilih pada produk</span>
                </label>
                @error('is_active')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-end gap-3">
                @if (Route::has('seller.categories.index'))
                    <a
                        href="{{ route('seller.categories.index') }}"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                    >
                        Batal
                    </a>
                @endif

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-5 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' }}
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>