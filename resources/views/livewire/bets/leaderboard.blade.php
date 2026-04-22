<div class="space-y-4">
    <div>
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">🏆 Top Bettors</h3>
        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">Die erfolgreichsten Wettenden</p>
    </div>

    <flux:card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300">Bettor</th>
                    <th class="px-4 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-300">Wetten</th>
                    <th class="px-4 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-300">Waschnüsse</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topBettors as $index => $bettor)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition">
                        <td class="px-4 py-3">
                            <span class="font-bold text-zinc-900 dark:text-white">
                                @if($index === 0)
                                    🥇
                                @elseif($index === 1)
                                    🥈
                                @elseif($index === 2)
                                    🥉
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $bettor->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-right text-zinc-600 dark:text-zinc-400">
                            {{ $bettor->user_bets_count }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-amber-600 dark:text-amber-400">
                                {{ number_format($bettor->soapnuts) }}
                                <span class="text-lg">🌰</span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-zinc-600 dark:text-zinc-400">
                            Noch keine Bettors vorhanden
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </flux:card>
</div>
