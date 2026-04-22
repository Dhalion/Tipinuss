<div class="max-w-5xl mx-auto pt-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
        @forelse ($bets as $bet)
            @include('components.bets.bet-listing-component', ['bet' => $bet])
        @empty
            <flux:callout icon="information-circle">
                {{ __('bets.empty') }}
            </flux:callout>
        @endforelse
    </div>
</div>
