@php
    $isEdit = isset($promotion) && !empty($promotion->id);
    $pageTitle = $isEdit ? 'Ubah Promosi' : 'Tambah Promosi';
    $formAction = $isEdit
        ? (Route::has('seller.promotions.update') ? route('seller.promotions.update', $promotion) : '#')
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
    $selectedType = old('discount_type', $promotion->discount_type ?? ($promotion->type ?? 'percentage'));
@endphp

<x-seller-layout :title="$pageTitle">
    <x-seller.page-header
        :title="$pageTitle"
        :subtitle="$isEdit ? 'Perbarui detail potongan, periode aktif, dan status promosi.' : 'Buat program promosi baru dengan periode tanggal mulai dan berakhir.'"
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
        <form method="POST" action="{{ $formAction }}" class="space-y-6">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Nama Promosi --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-200">
                        Nama Promosi <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $promotion->name ?? ($promotion->title ?? '')) }}"
                        required
                        placeholder="Contoh: Promo Akhir Tahun iStore"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('name') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode Promosi --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-zinc-200">
                        Kode Promosi
                    </label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        value="{{ old('code', $promotion->code ?? '') }}"
                        placeholder="Contoh: ISTORE26"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('code') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm font-mono uppercase text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('code')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Potongan --}}
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-zinc-200">
                        Jenis Potongan <span class="text-rose-400">*</span>
                    </label>
                    <select
                        id="discount_type"
                        name="discount_type"
                        required
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('discount_type') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 focus:outline-none focus:border-zinc-400 transition"
                    >
                        <option value="percentage" @selected($selectedType === 'percentage')>Persentase (%)</option>
                        <option value="fixed" @selected($selectedType === 'fixed')>Nominal Tetap (Rp)</option>
                    </select>
                    @error('discount_type')
                        <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nilai Potongan --}}
                <div>
                    <label for="discount_value" class="block text-sm font-medium text-zinc-200">
                        Nilai Potongan <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="number"
                        id="discount_value"
                        name="discount_value"
                        min="0"
                        step="any"
                        value="{{ old('discount_value', $promotion->discount_value ?? ($promotion->value ?? '')) }}"
                        required
                        placeholder="Contoh: 10 atau 500000"
                        class="mt-1.5 w-full rounded-xl bg-[#18181B] border @error('discount_value') border-rose-500/70 @else border-white/15 @enderror px-3.5 py-2.5 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-400 transition"
                    >
                    @error('discount_value')
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
                        Tanggal Akhir <span class="text-rose-400">*</span>
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
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-medium text-zinc-200">
                    Keterangan Promosi
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Catatan syarat &amp; ketentuan promosi untuk pengelolaan internal toko."
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
                    <span class="text-sm text-zinc-200">Promosi aktif dalam rentang tanggal berlaku</span>
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