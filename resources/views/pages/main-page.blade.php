<div class="min-h-screen bg-gradient-to-b from-zinc-50 to-white dark:from-zinc-900 dark:to-zinc-800">
    @include('components.flash-notification')

    <div class="px-4 lg:px-6">
        <div class="max-w-6xl mx-auto">
            <div class="py-20 text-center space-y-8">
                <div class="space-y-4">
                    <img
                        class="w-40 h-40 mx-auto object-contain"
                        src="{{ URL::asset('images/logo-full.webp') }}"
                        alt="{{ __('app.title') }}" />
                    <div>
                        <h1 class="text-7xl font-black text-zinc-900 dark:text-white">
                            Tipinuss
                        </h1>
                        <p class="text-2xl text-zinc-600 dark:text-zinc-400 font-semibold mt-2">
                            Online Waschnusswetten
                        </p>
                    </div>
                    <p class="text-lg text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed">
                        Tippe deine Wetten, verdiene Waschnüsse. Die moderne Wettplatform für Enthusiasten.
                    </p>
                </div>

                <div class="flex gap-3 justify-center pt-6">
                    <a href="{{ route('bets.list') }}" wire:navigate class="inline-flex items-center px-8 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-lg font-semibold hover:shadow-lg transition">
                        Wetten entdecken →
                    </a>
                    @guest
                        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center px-8 py-3 border-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white rounded-lg font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                            Anmelden
                        </a>
                    @endguest
                </div>
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 py-20 space-y-12">
                @if($recentBets->count() > 0)
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mb-3">
                                🎯 Letzte Wetten
                            </h2>
                            <p class="text-lg text-zinc-600 dark:text-zinc-400">
                                Die 5 neuesten offenen Wetten – jetzt kannst du mittippen!
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($recentBets as $bet)
                                @include('components.bets.bet-listing-component', ['bet' => $bet])
                            @endforeach
                        </div>

                        <div class="text-center pt-4">
                            <a href="{{ route('bets.list') }}" wire:navigate class="inline-flex items-center text-zinc-900 dark:text-white font-semibold hover:underline">
                                Alle Wetten ansehen →
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-20 space-y-4">
                        <p class="text-xl text-zinc-600 dark:text-zinc-400">
                            {{ __('bets.empty') }}
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-500">
                            Schau bald wieder vorbei oder erstelle selbst eine neue Wette!
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
