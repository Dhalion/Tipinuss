<div class="space-y-4">
    <div>
        <flux:heading size="lg">{{ __('leaderboard.title') }}</flux:heading>
        <flux:text class="mt-1">{{ __('leaderboard.description') }}</flux:text>
    </div>

    <flux:card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table class="w-full">
                <flux:table.columns>
                    <flux:table.column class="w-8">{{ __('leaderboard.table.rank') }}</flux:table.column>
                    <flux:table.column>{{ __('leaderboard.table.bettor') }}</flux:table.column>
                    <flux:table.column class="text-right hidden sm:table-cell">{{ __('leaderboard.table.bets') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('leaderboard.table.winnings') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($topBettors as $index => $bettor)
                        <flux:table.row wire:key="bettor-{{ $bettor->id }}">
                            <flux:table.cell class="align-middle">
                                <span class="font-bold text-zinc-900 dark:text-white">
                                    @if($index === 0) 🥇
                                    @elseif($index === 1) 🥈
                                    @elseif($index === 2) 🥉
                                    @else {{ $index + 1 }}
                                    @endif
                                </span>
                            </flux:table.cell>
                            <flux:table.cell class="align-middle">
                                <span class="font-medium text-zinc-900 dark:text-white truncate max-w-[120px] sm:max-w-none">{{ $bettor->name }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="text-right align-middle hidden sm:table-cell text-zinc-600 dark:text-zinc-400">
                                {{ $bettor->user_bets_count }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right align-middle">
                                <span class="font-semibold text-gold-600 dark:text-gold-400 whitespace-nowrap">
                                    {{ number_format($bettor->soapnuts) }} 🌰
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="text-center py-8 text-zinc-600 dark:text-zinc-400">
                                {{ __('leaderboard.empty') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
</div>
