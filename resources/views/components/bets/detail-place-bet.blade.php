@php /** @var \App\Models\Bet $bet */ @endphp

<flux:card class="lg:sticky lg:top-6 space-y-5">
    <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Wette platzieren</h2>

    @if($bet->status === \App\Enums\BetStatus::Open)

        <form class="space-y-4">
            <div class="space-y-2">
                <flux:label>Option wählen</flux:label>
                <div class="space-y-2">
                    @foreach($bet->betOptions as $option)
                        <label class="flex items-center justify-between rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2.5 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950">
                            <span class="flex items-center gap-2">
                                <input type="radio" name="bet_option_id" value="{{ $option->id }}" class="accent-blue-500">
                                <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $option->title }}</span>
                            </span>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ number_format($option->odds, 2) }}x</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <flux:input
                type="number"
                name="amount"
                min="1"
                label="Einsatz in 🌰"
                placeholder="z.B. 10"
            />

            <div class="flex items-center justify-between rounded-lg bg-zinc-50 dark:bg-zinc-800 px-3 py-2.5 text-sm">
                <span class="text-zinc-500 dark:text-zinc-400">Möglicher Gewinn</span>
                <span class="font-semibold text-zinc-900 dark:text-white">— 🌰</span>
            </div>

            <flux:button variant="primary" class="w-full">Wette platzieren</flux:button>
        </form>

    @else

        <flux:callout variant="warning" icon="lock-closed">
            <flux:callout.heading>Wette geschlossen</flux:callout.heading>
            <flux:callout.text>Auf diese Wette können keine neuen Einsätze mehr platziert werden.</flux:callout.text>
        </flux:callout>

    @endif
</flux:card>
