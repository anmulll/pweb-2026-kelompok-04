<x-layouts.seller :title="'Edit Promosi: ' . $promotion->title">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Edit Promosi</h1>
            <p class="text-xs text-zinc-400">Perbarui rincian kampanye promosi</p>
        </div>

        <form action="{{ route('seller.promotions.update', $promotion) }}" method="POST" enctype="multipart/form-data" class="glass-surface p-6 rounded-2xl border border-zinc-800 space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="title" class="text-xs font-semibold text-zinc-300">Judul Promosi <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $promotion->title) }}" required
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="start_date" class="text-xs font-semibold text-zinc-300">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>

                <div class="space-y-1.5">
                    <label for="end_date" class="text-xs font-semibold text-zinc-300">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}" required
                        class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="image" class="text-xs font-semibold text-zinc-300">Ganti Banner Promosi</label>
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full px-3 py-1.5 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-zinc-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-zinc-800 file:text-white hover:file:bg-zinc-700">
                @if($promotion->image)
                    <p class="text-[11px] text-zinc-500">Banner saat ini: {{ $promotion->image }}</p>
                @endif
            </div>

            <div class="space-y-1.5">
                <label for="description" class="text-xs font-semibold text-zinc-300">Deskripsi / Ketentuan Promosi</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">{{ old('description', $promotion->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                    class="rounded bg-zinc-900 border-zinc-700 text-blue-600 focus:ring-0">
                <label for="is_active" class="text-xs font-semibold text-zinc-300">Status Aktif (Tampilkan di Beranda)</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-800">
                <x-ui.button href="{{ route('seller.promotions.index') }}" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    Perbarui Promosi
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.seller>
