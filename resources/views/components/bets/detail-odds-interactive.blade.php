@php 
    $sorted = $bet->betOptions->sortByDesc('odds');
@endphp

<div class="space-y-4">
    <h2 class="text-base font-semibold text-zinc-700 dark:text-zinc-300">
        {{ __('bets.available_odds') }}
    </h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($sorted as $option)
            <button
                wire:key="option-{{ $option->id }}"
                type="button"
                wire:click="selectOption('{{ $option->id }}', {{ json_encode($option->title) }}, {{ $option->odds }})"
                class="group relative rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-6 py-6 text-center transition-all hover:border-zinc-400 dark:hover:border-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-400 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
            >
                <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-2">{{ $option->title }}</div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-white">
                    {{ number_format($option->odds, 2) }}<span class="text-xl">x</span>
                </div>
                
                <div class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">
                    {{ count($option->userBets) }} {{ __('bets.bets_placed') }}
                </div>
            </button>
        @endforeach
    </div>
</div>
