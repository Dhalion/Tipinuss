<div class="flex items-center gap-4">
    <flux:input id="bet-create-option-{{ $index }}-title" wire:model="options.{{ $index }}.title" label="{{ __('app.bet.option') }} {{ $index + 1 }}"
        placeholder="{{ __('app.bet.option_placeholder') }}" required />

    <flux:input id="bet-create-option-{{ $index }}-odds" type="number" wire:model="options.{{ $index }}.odds" label="{{ __('bets.odds') }}" step="0.01"
        min="1.01" :disabled="!$manual_odds" required />

    @if($optionCount > 2)
        <flux:button id="bet-remove-option-{{ $index }}" type="button" variant="danger" wire:click="removeOption({{ $index }})">
            {{ __('app.bet.create.remove_option') }}
        </flux:button>
    @endif
</div>