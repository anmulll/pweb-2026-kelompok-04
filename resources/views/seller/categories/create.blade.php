<x-layouts.seller title="Tambah Kategori — Seller iStore">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Tambah Kategori Baru</h1>
            <p class="text-xs text-zinc-400">Masukkan detail kategori perangkat baru</p>
        </div>

        <form action="{{ route('seller.categories.store') }}" method="POST" class="glass-surface p-6 rounded-2xl border border-zinc-800 space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="name" class="text-xs font-semibold text-zinc-300">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: iPhone, MacBook, iPad"
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
            </div>

            <div class="space-y-1.5">
                <label for="slug" class="text-xs font-semibold text-zinc-300">Slug (Opsional)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="Otomatis dibuat dari nama jika dikosongkan"
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">
            </div>

            <div class="space-y-1.5">
                <label for="description" class="text-xs font-semibold text-zinc-300">Deskripsi Kategori</label>
                <textarea id="description" name="description" rows="3" placeholder="Deskripsi singkat kategori..."
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded bg-zinc-900 border-zinc-700 text-blue-600 focus:ring-0">
                <label for="is_active" class="text-xs font-semibold text-zinc-300">Status Aktif (Tampilkan di Toko)</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-800">
                <x-ui.button href="{{ route('seller.categories.index') }}" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    Simpan Kategori
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.seller>
