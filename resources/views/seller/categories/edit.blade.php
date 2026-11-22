<x-layouts.seller title="Edit Kategori — Seller iStore">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Edit Kategori</h1>
            <p class="text-xs text-zinc-400">Perbarui detail kategori {{ $category->name }}</p>
        </div>

        <form action="{{ route('seller.categories.update', $category) }}" method="POST" class="glass-surface p-6 rounded-2xl border border-zinc-800 space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="name" class="text-xs font-semibold text-zinc-300">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="space-y-1.5">
                <label for="slug" class="text-xs font-semibold text-zinc-300">Slug</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}"
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="space-y-1.5">
                <label for="description" class="text-xs font-semibold text-zinc-300">Deskripsi Kategori</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-xs text-white focus:outline-none focus:border-blue-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                    class="rounded bg-zinc-900 border-zinc-700 text-blue-600 focus:ring-0">
                <label for="is_active" class="text-xs font-semibold text-zinc-300">Status Aktif (Tampilkan di Toko)</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-800">
                <x-ui.button href="{{ route('seller.categories.index') }}" variant="secondary">
                    Batal
                </x-ui.button>
                <x-ui.button type="submit" variant="primary">
                    Perbarui Kategori
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.seller>
