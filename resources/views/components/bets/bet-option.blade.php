<div class="flex items-center gap-4">
    <flux:input wire:model="options.{{ $index }}.title" label="{{ __('app.bet.option') }} {{ $index + 1 }}"
        placeholder="{{ __('app.bet.option_placeholder') }}" required />
    @error("options.$index.title") <div class="text-sm text-red-600">{{ $message }}</div> @enderror

    <flux:input type="number" wire:model="options.{{ $index }}.odds" label="{{ __('bets.odds') }}" step="0.01"
        min="1.01" :disabled="!$manual_odds" required />
    @error("options.{$index}.odds") <div class="text-sm text-red-600">{{ $message }}</div> @enderror

    @if($optionCount > 2)
        <flux:button type="button" variant="danger" wire:click="removeOption({{ $index }})">
            {{ __('app.bet.create.remove_option') }}
        </flux:button>
    @endif
</div>