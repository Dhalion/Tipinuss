<div class="space-y-4">
    <div>
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('leaderboard.title') }}</h3>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ __('leaderboard.description') }}</p>
    </div>

    <flux:card class="overflow-hidden p-0">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="px-3 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300 w-8">{{ __('leaderboard.table.rank') }}</th>
                    <th class="px-3 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300">{{ __('leaderboard.table.bettor') }}</th>
                    <th class="px-3 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-300 hidden sm:table-cell">{{ __('leaderboard.table.bets') }}</th>
                    <th class="px-3 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-300">{{ __('leaderboard.table.winnings') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topBettors as $index => $bettor)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                        <td class="px-3 py-2.5 w-8">
                            <span class="font-bold text-zinc-900 dark:text-white">
                                @if($index === 0) 🥇
                                @elseif($index === 1) 🥈
                                @elseif($index === 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </span>
                        </td>
                        <td class="px-3 py-2.5">
                            <div class="font-medium text-zinc-900 dark:text-white truncate max-w-[120px] sm:max-w-none">{{ $bettor->name }}</div>
                        </td>
                        <td class="px-3 py-2.5 text-right text-zinc-600 dark:text-zinc-400 hidden sm:table-cell">
                            {{ $bettor->user_bets_count }}
                        </td>
                        <td class="px-3 py-2.5 text-right">
                            <span class="font-semibold text-gold-600 dark:text-gold-400 whitespace-nowrap">
                                {{ number_format($bettor->soapnuts) }} 🌰
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-zinc-600 dark:text-zinc-400">
                            {{ __('leaderboard.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </flux:card>
</div>
