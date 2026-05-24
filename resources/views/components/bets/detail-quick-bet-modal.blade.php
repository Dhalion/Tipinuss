<!-- Quick Bet Modal -->
@php /** @var \App\Models\Bet $bet */ @endphp

<div
    x-data="{
        show: false,
        selectedOptionId: '',
        selectedOptionTitle: '',
        selectedOdds: 0,
        betAmount: '',
        init() {
            this.$watch('betAmount', () => {});
        },
        open(optionId, optionTitle, odds) {
            this.selectedOptionId = optionId;
            this.selectedOptionTitle = optionTitle;
            this.selectedOdds = odds;
            this.betAmount = '';
            this.show = true;
        }
    }"
    x-show="show"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    style="display: none;"
>
    <flux:card class="w-full max-w-md">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">{{ __('bets.place_bet') }}</h3>

        <form @submit.prevent="$wire.placeBet(selectedOptionId, betAmount)" class="space-y-4">
            <div class="rounded-lg bg-linear-to-r from-primary-600 to-primary-500 text-white p-4 text-center">
                <div class="text-sm text-primary-200">{{ __('bets.selected_option') }}</div>
                <div class="text-2xl font-bold mt-1" x-text="selectedOptionTitle"></div>
                <div class="text-sm font-semibold text-primary-200 mt-1" x-text="`${selectedOdds.toFixed(2)}x {{ __('bets.odds') }}`"></div>
            </div>

            <flux:input
                type="number"
                label="{{ __('bets.stake_amount') }}"
                placeholder="10"
                min="1"
                x-model="betAmount"
            />

            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('bets.potential_winnings') }}</span>
                    <span class="font-semibold text-zinc-900 dark:text-white" x-text="`${(betAmount * selectedOdds || 0).toFixed(2)} 🌰`"></span>
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <flux:button variant="outline" class="flex-1" @click="show = false" type="button">
                    {{ __('bets.cancel') }}
                </flux:button>
                <flux:button variant="primary" class="flex-1" type="submit">
                    {{ __('bets.place_bet') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
