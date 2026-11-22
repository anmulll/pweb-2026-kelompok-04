@props([
    'title',
    'subtitle' => null,
])

<div class="w-full flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-white/10 pb-5 mb-6">
    <div class="min-w-0">
        <h1 class="text-xl sm:text-2xl font-semibold tracking-tight text-zinc-100 truncate">
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-zinc-400">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>