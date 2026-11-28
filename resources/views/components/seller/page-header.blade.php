@props([
    'title',
    'subtitle' => null,
])

<div class="w-full glass-surface rounded-3xl p-5 sm:p-7 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0 space-y-1">
        <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-400">
            Konsol Penjual iStore
        </span>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white truncate">
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="text-xs sm:text-sm text-zinc-400">
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
