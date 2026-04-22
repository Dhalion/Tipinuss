<!-- Quick Bet Modal -->
<div x-show="showQuickBetModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <flux:card class="w-full max-w-md">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">{{ __('bets.place_bet') }}</h3>
        
        <div class="space-y-4">
            <!-- Selected option preview -->
            <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 text-center">
                <div class="text-sm text-blue-100">{{ __('bets.selected_option') }}</div>
                <div class="text-2xl font-bold mt-1" x-text="selectedOptionTitle"></div>
                <div class="text-sm font-semibold text-blue-200 mt-1" x-text="`${selectedOdds.toFixed(2)}x Quote`"></div>
            </div>
            
            <!-- Amount input -->
            <flux:input 
                type="number" 
                label="{{ __('bets.stake_amount') }}" 
                placeholder="10" 
                min="1"
                x-model="betAmount"
                @input="$watch('betAmount', () => {})"
            />
            
            <!-- Potential winnings preview -->
            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600 dark:text-zinc-400">{{ __('bets.potential_winnings') }}</span>
                    <span class="font-semibold text-zinc-900 dark:text-white" x-text="`${(betAmount * selectedOdds || 0).toFixed(2)} 🌰`"></span>
                </div>
            </div>
            
            <!-- Action buttons -->
            <div class="flex gap-2 pt-2">
                <flux:button variant="outline" class="flex-1" @click="showQuickBetModal = false">
                    {{ __('bets.cancel') }}
                </flux:button>
                <flux:button variant="primary" class="flex-1" wire:click="placeBet">
                    {{ __('bets.place_bet') }}
                </flux:button>
            </div>
        </div>
    </flux:card>
</div>
