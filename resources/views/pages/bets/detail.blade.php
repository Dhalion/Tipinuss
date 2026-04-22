<div class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-900" x-data="{ openModal: false, modalName: '' }" @showModal.window="openModal = true; modalName = $event.detail.modalName" @closeModal.window="openModal = false; modalName = ''">

    @include('components.flash-notification')

    @include('components.bets.detail-header-with-controls', ['bet' => $bet])
    
    <div class="flex-1 px-4 lg:px-6 py-8">
        <div class="max-w-6xl mx-auto space-y-8">
            
            @include('components.bets.detail-odds-interactive', ['bet' => $bet])
            
            <div class="border-t border-zinc-200 dark:border-zinc-800 pt-8">
                @livewire('bets.placed-bets-feed', ['bet' => $bet], key('placed-bets-feed-' . $bet->id))
            </div>
            
        </div>
    </div>

    @foreach($bet->betOptions->sortByDesc('odds') as $option)
        <flux:modal name="place-bet-{{ $option->id }}" class="md:w-96" x-show="modalName === 'place-bet-{{ $option->id }}' && openModal" @close="openModal = false; modalName = ''">
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                        {{ __('bets.place_bet') }}
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        {{ $option->title }}
                    </p>
                </div>

                @livewire('bets.place-bet-modal', ['optionId' => $option->id, 'optionTitle' => $option->title, 'odds' => $option->odds], key('place-bet-modal-' . $option->id))
            </div>
        </flux:modal>
    @endforeach

</div>
