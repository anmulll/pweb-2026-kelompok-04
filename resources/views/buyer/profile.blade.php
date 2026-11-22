<x-layouts.app title="Profil Saya — iStore">
    <div class="max-w-[1200px] mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Profil Pengguna</h1>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-[#111113] border border-white/10 rounded-xl p-6">
            <form action="{{ route('buyer.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Email (Tidak dapat diubah)</label>
                    <input type="email" value="{{ $user->email }}" disabled class="w-full bg-[#18181B] border border-white/10 rounded-lg px-4 py-2 text-zinc-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-[#18181B] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-white/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-[#18181B] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-white/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-1">Alamat Pengiriman Default</label>
                    <textarea name="default_shipping_address" rows="3" class="w-full bg-[#18181B] border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-white/30">{{ old('default_shipping_address', $user->default_shipping_address) }}</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-5 py-2.5 bg-white text-black font-semibold rounded-lg hover:bg-zinc-200 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
