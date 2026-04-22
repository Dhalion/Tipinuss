<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                {{ __('app.navigation.bets.list') }}
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-2">
                Entdecke die neuesten Wetten und platziere deine Tipps
            </p>
        </div>

        <div class="space-y-4">
            @forelse ($bets as $bet)
                @include('components.bets.bet-listing-component', ['bet' => $bet])
            @empty
                <flux:callout icon="information-circle" class="bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700">
                    <div class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ __('bets.empty') }}
                    </div>
                </flux:callout>
            @endforelse
        </div>

        @if($bets->hasPages())
            <div class="mt-8">
                {{ $bets->links() }}
            </div>
        @endif
    </div>
</div>
