<div @if(!$betIsClosed) wire:poll.5s @endif class="space-y-3">

    <div class="flex items-center gap-2">
        @if($betIsClosed)
            <span class="relative flex size-2">
                <span class="relative inline-flex rounded-full size-2 bg-zinc-400"></span>
            </span>
            <h3 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">{{ __('bets.closed_bets') }}</h3>
        @else
            <span class="relative flex size-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full size-2 bg-green-500"></span>
            </span>
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ __('bets.recent_bets') }}</h3>
        @endif
    </div>

    <flux:card class="p-0 overflow-hidden">
        @forelse($placedBets as $userBet)
            <div
                wire:key="{{ $userBet->id }}"
                x-data
                x-init="
                    const ageMs = Date.now() - {{ $userBet->created_at->timestamp * 1000 }};
                    if (ageMs < 10000) {
                        $el.classList.add('bet-new-highlight');
                    }
                "
                class="flex items-center gap-3 px-4 py-3 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}"
            >

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200 truncate">
                            {{ $userBet->user->name }}
                        </span>
                        <flux:badge :color="$userBet->status->badgeColor()" size="sm">
                            {{ $userBet->status->label() }}
                        </flux:badge>
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">
                        {{ $userBet->betOption->title }}
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                        {{ number_format($userBet->amount_wagered, 0) }} 🌰
                    </div>
                    <div class="text-xs text-zinc-400">
                        {{ $userBet->created_at->diffForHumans() }}
                    </div>
                </div>

            </div>
        @empty
            <div class="px-4 py-6 text-center text-sm text-zinc-400 dark:text-zinc-500">
                {{ __('bets.empty') }}
            </div>
        @endforelse
    </flux:card>

</div>
