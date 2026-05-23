@php /** @var \App\Models\BetOption|null $option */ @endphp

<flux:card class="w-full max-w-md">
    @if($option !== null)
        @php /** @var \App\Models\BetOption $option */ @endphp
        <div class="space-y-4">
            <div class="rounded-lg bg-primary-100 dark:bg-primary-900 border border-primary-200 dark:border-primary-800 p-4">
                <div class="text-sm text-primary-600 dark:text-primary-400">{{ __('bets.selected_option') }}</div>
                <div class="text-xl font-bold text-zinc-900 dark:text-white mt-1">{{ $option->title }}</div>
                <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-400 mt-1">{{ number_format($option->odds, 2) }}x {{ __('bets.odds') }}</div>
            </div>

            <flux:input
                type="number"
                wire:model.live="amount"
                label="{{ __('bets.stake_amount') }}"
                placeholder="10"
                min="1"
            />

            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('bets.potential_winnings') }}</span>
                    <span class="font-semibold text-zinc-900 dark:text-white">{{ number_format($amount * $option->odds, 2) }} 🌰</span>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:button variant="outline" class="flex-1">
                    {{ __('bets.cancel') }}
                </flux:button>
                <flux:button variant="primary" class="flex-1" wire:click="placeBet" wire:loading.attr="disabled">
                    {{ __('bets.place_bet') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="p-4 text-center text-zinc-500 dark:text-zinc-400">
            {{ __('bets.option_not_found') }}
        </div>
    @endif
</flux:card>
