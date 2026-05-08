<div 
    class="min-h-screen bg-gradient-to-b from-zinc-900 to-zinc-950 text-white"
    x-data="{
        placeBet: {
            show: false,
            amount: '',
            odds: 0,
            optionId: '',
            optionTitle: '',
            get potentialWinnings() {
                const a = parseFloat(this.amount) || 0;
                const o = parseFloat(this.odds) || 0;
                return Math.floor(a * o).toLocaleString('de-DE');
            },
            open(optionId, optionTitle, odds) {
                this.optionId = optionId;
                this.optionTitle = optionTitle;
                this.odds = parseFloat(odds);
                this.amount = '';
                this.show = true;
            },
            close() {
                this.show = false;
                this.amount = '';
            }
        }
    }"
    @bet-placed.window="placeBet.close()"
>

    @include('components.flash-notification')

    @include('components.bets.detail-header-with-controls', ['bet' => $bet, 'canCloseBet' => $canCloseBet])
    
    <div class="flex-1 px-4 lg:px-6 py-12">
        <div class="max-w-6xl mx-auto">
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6">{{ __('bets.available_odds') }}</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($bet->betOptions->sortByDesc('odds') as $option)
                        <button 
                            type="button"
                            @click="placeBet.open('{{ $option->id }}', '{{ addslashes($option->title) }}', {{ $option->odds }})"
                            class="group relative overflow-hidden rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-900/20 to-orange-900/20 px-6 py-8 text-center transition-all hover:border-amber-400 hover:shadow-2xl hover:shadow-amber-500/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-zinc-900"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/0 via-amber-500/5 to-orange-500/0 opacity-0 transition-opacity group-hover:opacity-100"></div>
                            
                            <div class="relative">
                                <div class="text-sm font-medium text-amber-200 mb-3">{{ $option->title }}</div>
                                <div class="text-4xl font-bold text-amber-300 mb-2">
                                    {{ number_format($option->odds, 2) }}<span class="text-lg text-amber-400">x</span>
                                </div>
                                
                                <div class="flex items-center justify-center gap-2 text-xs text-zinc-400 mt-4">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    {{ count($option->userBets) }} {{ __('bets.bets_placed') }}
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-zinc-700 pt-8">
                @livewire('bets.placed-bets-feed', ['bet' => $bet], key('placed-bets-feed-' . $bet->id))
            </div>
            
        </div>
    </div>

    {{-- ============================================================
         Place Bet Modal – Alpine-driven, zero server roundtrip on input
         ============================================================ --}}
    <div
        x-show="placeBet.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        @keydown.escape.window="placeBet.close()"
        x-trap="placeBet.show"
        aria-modal="true"
        role="dialog"
    >
        <div 
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            @click="placeBet.close()"
            aria-hidden="true"
        ></div>

        <div
            x-show="placeBet.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-md"
        >
            <div class="rounded-2xl border border-amber-500/30 bg-gradient-to-b from-zinc-800 to-zinc-900 shadow-2xl shadow-black/70 overflow-hidden">

                <div class="border-b border-zinc-700/50 px-6 py-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-xl font-bold text-white">{{ __('bets.place_bet') }}</h2>
                            <p class="mt-1 text-sm font-medium text-amber-300 truncate" x-text="placeBet.optionTitle"></p>
                        </div>
                        <button
                            type="button"
                            @click="placeBet.close()"
                            class="shrink-0 rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                            aria-label="Schließen"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-5">

                    <div class="flex items-center justify-between rounded-lg bg-zinc-800/60 px-4 py-3 text-sm">
                        <span class="text-zinc-400">{{ __('bets.odds') }}</span>
                        <span class="font-bold text-amber-300" x-text="placeBet.odds.toFixed(2) + 'x'"></span>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-zinc-200 mb-2">
                            {{ __('bets.stake_amount') }}
                        </label>
                        <div class="relative">
                            <input
                                x-ref="amountInput"
                                x-model="placeBet.amount"
                                type="number"
                                min="1"
                                max="100000"
                                step="1"
                                placeholder="100"
                                class="w-full rounded-xl border border-zinc-600 bg-zinc-800/50 px-4 py-3 pr-10 text-white placeholder-zinc-500 transition focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/30"
                                autocomplete="off"
                            />
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xl">🌰</span>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm font-medium text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-amber-500/20 bg-gradient-to-br from-amber-900/40 to-orange-900/40 px-5 py-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-amber-300/70 mb-1.5">
                            {{ __('bets.potential_winnings') }}
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold text-amber-300" x-text="placeBet.potentialWinnings"></span>
                            <span class="text-2xl text-amber-400">🌰</span>
                        </div>
                        <div class="mt-2 text-xs text-amber-300/60">
                            <span x-text="parseFloat(placeBet.amount || 0).toLocaleString('de-DE')"></span>
                            ×
                            <span x-text="placeBet.odds.toFixed(2)"></span>x
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button
                            type="button"
                            @click="placeBet.close()"
                            class="flex-1 rounded-xl border border-zinc-600 bg-zinc-800 px-4 py-3 font-medium text-zinc-300 transition hover:bg-zinc-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                        >
                            {{ __('bets.cancel') }}
                        </button>
                        <button
                            type="button"
                            @click="$wire.placeBet(placeBet.optionId, placeBet.amount)"
                            :disabled="!placeBet.amount || parseFloat(placeBet.amount) < 1"
                            wire:loading.attr="disabled"
                            wire:target="placeBet"
                            class="flex-1 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3 font-bold text-white shadow-lg shadow-amber-500/30 transition hover:from-amber-600 hover:to-orange-600 disabled:cursor-not-allowed disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-amber-400"
                        >
                            <span wire:loading.remove wire:target="placeBet">{{ __('bets.place_bet') }}</span>
                            <span wire:loading wire:target="placeBet" class="inline-flex items-center justify-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                {{ __('bets.placing') }}…
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         Close Bet Modal – Livewire state + Alpine transitions (@entangle)
         ============================================================ --}}
    <div
        x-data="{ show: $wire.entangle('showCloseBetModal').defer }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        @keydown.escape.window="show = false"
        x-trap="show"
        aria-modal="true"
        role="dialog"
    >
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            @click="show = false"
            aria-hidden="true"
        ></div>

        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-md"
        >
            <div class="overflow-hidden rounded-2xl border border-red-500/30 bg-gradient-to-b from-zinc-800 to-zinc-900 shadow-2xl shadow-black/70">

                <div class="border-b border-zinc-700/50 px-6 py-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ __('bets.close') }}</h2>
                            <p class="mt-1 text-sm font-medium text-red-300">{{ __('bets.select_winning_option') }}</p>
                        </div>
                        <button
                            type="button"
                            @click="show = false"
                            class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                            aria-label="Schließen"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6">
                    @error('closeBet')
                        <div class="mb-4 rounded-lg border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm font-medium text-red-300">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="max-h-80 space-y-2.5 overflow-y-auto pr-1">
                        @foreach($bet->betOptions->sortByDesc('odds') as $option)
                            <button
                                type="button"
                                wire:click="executeCloseBet('{{ $option->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="executeCloseBet('{{ $option->id }}')"
                                class="group w-full rounded-xl border border-zinc-600 bg-zinc-800/50 px-4 py-4 text-left transition hover:border-red-400/60 hover:bg-red-900/20 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus:ring-2 focus:ring-red-500/50"
                            >
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-semibold text-white transition group-hover:text-red-200">{{ $option->title }}</span>
                                    <span class="shrink-0 text-sm font-bold text-amber-300">{{ number_format($option->odds, 2) }}x</span>
                                </div>
                                <div class="mt-1 text-xs text-zinc-400">
                                    {{ $option->userBets->count() }} {{ __('bets.bets_placed') }}
                                    <span wire:loading wire:target="executeCloseBet('{{ $option->id }}')" class="ml-2 inline-flex items-center gap-1 text-red-400">
                                        <svg class="h-3 w-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Schließe…
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        @click="show = false"
                        class="mt-4 w-full rounded-xl border border-zinc-600 bg-zinc-800 px-4 py-3 font-medium text-zinc-300 transition hover:bg-zinc-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                    >
                        {{ __('bets.cancel') }}
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================
         Delete Bet Modal – Livewire state + Alpine transitions (@entangle)
         ============================================================ --}}
    <div
        x-data="{ show: $wire.entangle('showDeleteBetModal').defer }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        @keydown.escape.window="show = false"
        x-trap="show"
        aria-modal="true"
        role="dialog"
    >
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            @click="show = false"
            aria-hidden="true"
        ></div>

        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-sm"
        >
            <div class="overflow-hidden rounded-2xl border border-red-500/40 bg-gradient-to-b from-zinc-800 to-zinc-900 shadow-2xl shadow-black/70">

                <div class="px-6 pt-6 pb-2 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-500/10 ring-1 ring-red-500/30">
                        <svg class="h-7 w-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-white">{{ __('bets.delete') }}</h2>
                    <p class="mt-2 text-sm text-zinc-400">{{ __('bets.confirm_delete') }}</p>
                </div>

                <div class="flex gap-3 px-6 py-5">
                    <button
                        type="button"
                        @click="show = false"
                        class="flex-1 rounded-xl border border-zinc-600 bg-zinc-800 px-4 py-3 font-medium text-zinc-300 transition hover:bg-zinc-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                    >
                        {{ __('bets.cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="deleteBet"
                        wire:loading.attr="disabled"
                        wire:target="deleteBet"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-3 font-bold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-lg shadow-red-500/20"
                    >
                        <span wire:loading.remove wire:target="deleteBet">{{ __('bets.delete') }}</span>
                        <span wire:loading wire:target="deleteBet" class="inline-flex items-center justify-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            Lösche…
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
