<div>
    <div class="space-y-6">
        <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('bets.odds') }}</span>
                <span class="font-bold text-lg text-zinc-900 dark:text-white">
                    {{ number_format($odds, 2) }}x
                </span>
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3 flex justify-between items-center">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('bets.potential_winnings') }}</span>
                <span class="font-bold text-lg text-zinc-900 dark:text-white">
                    {{ number_format($amount * $odds, 0) }}
                </span>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                {{ __('bets.amount') }}
            </label>
            <flux:input
                type="number"
                wire:model.live="amount"
                placeholder="Min. 1"
                min="1"
            />
            @error('amount')
            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex gap-3">
            <flux:button
                variant="outline"
                class="flex-1"
                wire:click="closeModal"
            >
                {{ __('bets.cancel') }}
            </flux:button>
            <flux:button
                class="flex-1"
                wire:click="placeBet"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>{{ __('bets.place_bet') }}</span>
                <span wire:loading>{{ __('bets.placing') }}...</span>
            </flux:button>
        </div>
    </div>
</div>
