@php /** @var \App\Models\Bet $bet */ @endphp

<div class="{{ $bet->betOptions->count() > 3 ? 'grid grid-cols-2 sm:grid-cols-4' : 'grid grid-cols-2 sm:grid-cols-3' }} gap-3">
    @foreach($bet->betOptions as $option)
        <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-4 py-3 text-center">
            <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate mb-1">{{ $option->title }}</div>
            <div class="text-lg font-bold text-zinc-900 dark:text-white">{{ number_format($option->odds, 2) }}x</div>
        </div>
    @endforeach
</div>
