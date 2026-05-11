<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">{{ __('app.bet.create.title') }}</h1>
        <p class="text-zinc-600 dark:text-zinc-400 mt-2">{{ __('app.bet.create.description') }}</p>
    </div>

    <flux:card>
        <form wire:submit="createBet" class="space-y-6">
            <div class="space-y-4">
                <flux:input wire:model="title" label="{{ __('app.bet.title') }}"
                    placeholder="{{ __('app.bet.title_placeholder') }}}" wire:model.live.debounce.250ms required />

                <flux:textarea wire:model="description" label="{{ __('app.bet.description') }}"
                    placeholder="{{ __('app.bet.description_placeholder') }}" rows="4" />

                <flux:input type="datetime-local" wire:model="expires_at" label="{{ __('app.bet.expiration_date') }}" />

                <flux:separator />

                <div class="flex items-center justify-between">
                    <flux:heading size="sm">{{ __('bets.options') }}</flux:heading>
                    <flux:button type="button" wire:click="toggleManualOdds" variant="ghost" size="sm" icon="{{ $manualOdds ? 'lock-open' : 'lock-closed' }}">
                        {{ $manualOdds ? __('app.bet.odds_auto') : __('app.bet.odds_manual') }}
                    </flux:button>
                </div>

                <div class="space-y-4">
                    @foreach ($options as $index => $option)
                        @include('components.bets.bet-option', ['index' => $index, 'key' => $index, 'optionCount' => $optionCount, 'manual_odds' => $manualOdds])
                    @endforeach
                </div>
                <flux:button type="button" wire:click="addOption" variant="outline" class="w-full">
                    {{ __('app.bet.create.add_option') }}
                </flux:button>
            </div>

            <flux:separator />

            <div class="pt-4">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('app.bet.create.submit') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>