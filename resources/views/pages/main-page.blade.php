<div class="min-h-screen bg-gradient-to-b from-zinc-50 to-white dark:from-zinc-900 dark:to-zinc-800">

    <div class="px-4 lg:px-6">
        <div class="max-w-6xl mx-auto">
            <div class="py-12 sm:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                    <div class="flex flex-col items-center lg:items-start text-center lg:text-left order-first">
                        <img
                            class="w-48 sm:w-64 lg:w-72 object-contain drop-shadow-2xl"
                            src="{{ URL::asset('images/tipinuss-thron.webp') }}"
                            alt="{{ __('app.title') }}" />
                        <p class="text-lg sm:text-xl font-semibold text-gold-400 mt-4">
                            {{ __('app.hero_tagline') }}
                        </p>
                    </div>

                    <div class="space-y-6 text-center lg:text-left">
                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-zinc-900 dark:text-white">
                            Tipinuss
                        </h1>
                        <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-300 max-w-lg leading-relaxed">
                            {{ __('app.hero_subtitle') }}
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start pt-2">
                            <a href="{{ route('bets.list') }}" wire:navigate
                               class="inline-flex items-center justify-center px-8 py-3 rounded-lg font-semibold text-white hover:opacity-90 transition shadow-lg bg-primary-600">
                                {{ __('app.hero_cta') }}
                            </a>
                            @guest
                                <a href="{{ route('login') }}" wire:navigate
                                   class="inline-flex items-center justify-center px-8 py-3 border-2 rounded-lg font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition border-primary-600 text-primary-600">
                                    {{ __('app.navigation.login') }}
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-200 dark:border-zinc-700 py-20 space-y-16">
                @if($recentBets->count() > 0)
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mb-3">
                                🎯 {{ __('app.recent_bets_title') }}
                            </h2>
                            <p class="text-lg text-zinc-600 dark:text-zinc-400">
                                {{ __('app.recent_bets_description') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($recentBets as $bet)
                                @include('components.bets.bet-listing-component', ['bet' => $bet])
                            @endforeach
                        </div>

                        <div class="text-center pt-4">
                            <a href="{{ route('bets.list') }}" wire:navigate class="inline-flex items-center text-zinc-900 dark:text-white font-semibold hover:underline">
                                {{ __('bets.view_all') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-20 space-y-4">
                        <p class="text-xl text-zinc-600 dark:text-zinc-400">
                            {{ __('bets.empty') }}
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-500">
                            {{ __('bets.empty_suggestion') }}
                        </p>
                    </div>
                @endif

                <div class="max-w-2xl">
                    @livewire('bets.leaderboard')
                </div>
            </div>

        </div>
    </div>
</div>
