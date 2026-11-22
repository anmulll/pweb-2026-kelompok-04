@props([
    'src' => null,
    'alt' => 'Gambar Produk',
    'class' => 'w-full h-full object-cover',
])

@if(!empty($src))
    <img src="{{ $src }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@else
    <div class="w-full h-full flex flex-col items-center justify-center bg-zinc-800/60 text-zinc-400 text-xs p-4 text-center select-none">
        <span class="text-2xl mb-1 text-zinc-500"></span>
        <span>Foto belum tersedia</span>
    </div>
@endif
