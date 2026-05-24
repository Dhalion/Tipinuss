@php
    /** @var \App\Models\UserBet $userBet */
    /** @var \App\Models\Bet $bet */

    $isWinner = $userBet->status === \App\Enums\UserBetStatus::Won;
@endphp

<div class="rounded-2xl border overflow-hidden {{ $isWinner
    ? 'border-gold-400/50 bg-linear-to-br from-gold-50 via-white to-gold-100 dark:from-gold-900/30 dark:via-zinc-800 dark:to-gold-900/20'
    : 'border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800' }}">
    <div class="flex flex-col sm:flex-row items-center gap-6 p-6 sm:p-8">
        <div class="shrink-0">
            <img
                src="{{ URL::asset($isWinner ? 'images/tipinuss-waschnusskönig-winning.webp' : 'images/tipinuss-waschnusskönig-sad.webp') }}"
                alt=""
                class="w-36 sm:w-48 object-contain drop-shadow-md"
            />
        </div>

        <div class="flex-1 min-w-0 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 mb-2">
                <flux:badge :color="$userBet->status->badgeColor()" size="lg">
                    {{ $userBet->status->label() }}
                </flux:badge>
                @if($isWinner)
                    <span class="text-2xl">🏆</span>
                @endif
            </div>

            <h3 class="text-lg font-bold text-zinc-900 dark:text-white">
                {{ $isWinner ? __('bets.result_won_title') : __('bets.result_lost_title') }}
            </h3>

            <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <div class="rounded-lg bg-zinc-100 dark:bg-zinc-700/50 px-3 py-2">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('bets.your_bet') }}</span>
                    <p class="font-semibold text-zinc-900 dark:text-white">{{ number_format($userBet->amount_wagered, 0) }} 🌰</p>
                </div>
                <div class="rounded-lg bg-zinc-100 dark:bg-zinc-700/50 px-3 py-2">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('bets.odds') }}</span>
                    <p class="font-semibold text-zinc-900 dark:text-white">{{ number_format($userBet->betOption->odds, 2) }}x</p>
                </div>
                <div class="rounded-lg px-3 py-2 {{ $isWinner
                    ? 'bg-gold-100 dark:bg-gold-900/30'
                    : 'bg-red-100 dark:bg-red-900/30' }}">
                    <span class="{{ $isWinner ? 'text-gold-600 dark:text-gold-400' : 'text-red-500 dark:text-red-400' }} font-medium">
                        {{ $isWinner ? __('bets.winnings') : __('bets.loss') }}
                    </span>
                    <p class="font-bold {{ $isWinner ? 'text-gold-800 dark:text-gold-200' : 'text-red-800 dark:text-red-200' }}">
                        @if($isWinner)
                            +{{ number_format($userBet->potential_winnings, 0) }} 🌰
                        @else
                            -{{ number_format($userBet->amount_wagered, 0) }} 🌰
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
