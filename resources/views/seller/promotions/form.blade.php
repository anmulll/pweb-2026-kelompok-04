@php
    $isEdit = isset($promotion) && !empty($promotion->id);
    $pageTitle = $isEdit ? 'Ubah Promosi' : 'Tambah Promosi';
    $formAction = $isEdit
        ? (Route::has('seller.promotions.update') ? route('seller.promotions.update', $promotion->id ?? $promotion) : '#')
        : (Route::has('seller.promotions.store') ? route('seller.promotions.store') : '#');

    $rawStart = $promotion->start_date ?? ($promotion->starts_at ?? null);
    $rawEnd = $promotion->end_date ?? ($promotion->ends_at ?? null);

    $formattedStart = old(
        'start_date',
        $rawStart ? \Illuminate\Support\Carbon::parse($rawStart)->format('Y-m-d') : ''
    );
    $formattedEnd = old(
        'end_date',
        $rawEnd ? \Illuminate\Support\Carbon::parse($rawEnd)->format('Y-m-d') : ''
    );

    $currentImage = $isEdit
        ? (method_exists($promotion, 'imageUrl')
            ? $promotion->imageUrl()
            : ($promotion->image_url ?? (!empty($promotion->image) ? asset('storage/' . ltrim($promotion->image, '/')) : null)))
        : null;
@endphp

<x-seller-layout :title="$pageTitle">
    <x-seller.page-header
        :title="$pageTitle"
        :subtitle="$isEdit ? 'Perbarui banner promosi, periode tayang, dan status aktif.' : 'Buat program promosi baru untuk ditampilkan pada showcase beranda iStore.'"
    >
        <x-slot:actions>
            @if (Route::has('seller.promotions.index'))
                <a
                    href="{{ route('seller.promotions.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-200 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                >
                    Kembali
                </a>
            @endif
        </x-slot:actions>
    </x-seller.page-header>

    <div class="glass-surface rounded-2xl p-5 sm:p-6 max-w-3xl">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama Promosi --}}
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-zinc-200">
                        Judul Promosi <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title', $promotion->title ?? ($promotion->name ?? '')) }}"
                        required
                        placeholder="Contoh: Promo Spesial Akhir Tahun — Diskon Ekosistem Apple"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('title') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('title')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug URL --}}
                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-zinc-200">
                        Slug URL
                    </label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $promotion->slug ?? '') }}"
                        placeholder="Contoh: promo-spesial-akhir-tahun (kosongkan jika dibuat otomatis)"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('slug') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('slug')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label for="start_date" class="block text-sm font-medium text-zinc-200">
                        Tanggal Mulai <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ $formattedStart }}"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('start_date') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('start_date')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Akhir --}}
                <div>
                    <label for="end_date" class="block text-sm font-medium text-zinc-200">
                        Tanggal Berakhir <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ $formattedEnd }}"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('end_date') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('end_date')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Banner / Gambar Promosi --}}
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-zinc-200">
                        Banner Promosi {{ $isEdit ? '(opsional jika tidak diganti)' : '' }}
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
                                alt="{{ $promotion->name ?? 'Banner' }}"
                                class="h-16 w-32 rounded-lg object-cover border border-white/10"
                            >
                            <span class="text-xs text-zinc-400">Banner saat ini terpasang</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Keterangan / Deskripsi Promosi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-200">
                    Keterangan &amp; Syarat Promosi
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Tuliskan informasi ringkas mengenai cakupan promosi atau syarat pembelian."
                    class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('description') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                >{{ old('description', $promotion->description ?? '') }}</textarea>
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
                        @checked((bool) old('is_active', $promotion->is_active ?? true))
                        class="h-4 w-4 rounded border-white/20 bg-[#18181B] text-zinc-100 focus:ring-0"
                    >
                    <span class="text-sm text-zinc-200">Tayangkan promosi ini di beranda selama periode tanggal berlaku</span>
                </label>
                @error('is_active')
                    <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-end gap-3">
                @if (Route::has('seller.promotions.index'))
                    <a
                        href="{{ route('seller.promotions.index') }}"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs sm:text-sm font-medium text-zinc-300 bg-[#18181B] hover:bg-zinc-800 border border-white/15 transition"
                    >
                        Batal
                    </a>
                @endif

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl px-5 py-2 text-xs sm:text-sm font-medium text-zinc-950 bg-zinc-100 hover:bg-white transition cursor-pointer"
                >
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Promosi' }}
                </button>
            </div>
        </form>
    </div>
</x-seller-layout>
