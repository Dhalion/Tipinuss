<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900">
    @include('components.flash-notification')

    <div class="px-4 lg:px-6">
        <div class="max-w-6xl mx-auto">
            
            <div class="py-20 text-center space-y-8">
                <div class="space-y-4">
                    <img
                        class="w-32 h-32 mx-auto object-contain"
                        src="{{ URL::asset('images/logo-full.webp') }}"
                        alt="{{ __('app.title') }}" />
                    <h1 class="text-6xl font-bold text-zinc-900 dark:text-white">
                        Tipinuss
                    </h1>
                    <p class="text-2xl text-zinc-600 dark:text-zinc-400 font-semibold">
                        Online Waschnusswetten
                    </p>
                    <p class="text-lg text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto">
                        Tippe deine Wetten, verdiene Waschnüsse. Die moderne Wettplatform für Enthusiasten.
                    </p>
                </div>

                <div class="flex gap-3 justify-center pt-4">
                    <a href="{{ route('bets.list') }}" wire:navigate class="inline-flex items-center px-6 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-lg font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-100 transition">
                        Wetten entdecken →
                    </a>
                </div>
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-800 py-16 space-y-12">
                @if($recentBets->count() > 0)
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">
                                Letzte Wetten
                            </h2>
                            <p class="text-base text-zinc-600 dark:text-zinc-400">
                                Die 5 neuesten offenen Wetten – jetzt kannst du mittippen!
                            </p>
                        </div>

                        <div class="space-y-4">
                            @foreach($recentBets as $bet)
                                @include('components.bets.bet-listing-component', ['bet' => $bet])
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-20 space-y-4">
                        <p class="text-zinc-600 dark:text-zinc-400 text-lg">
                            {{ __('bets.empty') }}
                        </p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-500">
                            Schau bald wieder vorbei oder erstelle selbst eine neue Wette!
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
