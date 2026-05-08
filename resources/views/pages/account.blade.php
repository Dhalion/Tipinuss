<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-12">
    @include('components.flash-notification')

    <div class="max-w-4xl mx-auto px-4">
        <!-- Profile Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white mb-2">
                {{ __('account.profile') }}
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('account.manage_profile') }}
            </p>
        </div>

        <!-- Profile Card -->
        <flux:card class="mb-8">
            <div class="space-y-6">
                <!-- User Info -->
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-4">
                        {{ auth()->user()->name }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-zinc-100 dark:bg-zinc-800 rounded-lg p-4">
                            <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-1">
                                {{ __('account.email') }}
                            </div>
                            <div class="font-semibold text-zinc-900 dark:text-white">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800">
                            <div class="text-sm text-amber-700 dark:text-amber-300 mb-1">
                                {{ __('account.balance') }}
                            </div>
                            <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                                {{ number_format(auth()->user()->soapnuts) }}
                                <span class="text-lg ml-1">🌰</span>
                            </div>
                        </div>
                    </div>
                </div>

                <flux:separator />

                <!-- Account Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-1">
                            {{ __('account.member_since') }}
                        </div>
                        <div class="text-zinc-900 dark:text-white">
                            {{ auth()->user()->created_at->format('d. F Y') }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-1">
                            {{ __('account.total_bets') }}
                        </div>
                        <div class="text-zinc-900 dark:text-white font-semibold">
                            {{ auth()->user()->userBets()->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>

        <!-- Bet History -->
        @if($userBets->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-4">
                    📊 {{ __('account.recent_bets') }}
                </h2>

                <div class="space-y-3">
                    @foreach($userBets as $userBet)
                        <flux:card class="hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-zinc-900 dark:text-white truncate">
                                        {{ $userBet->betOption->bet->title }}
                                    </h3>
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400 mt-1 flex flex-wrap gap-2">
                                        <span>{{ __('account.option') }}: <strong>{{ $userBet->betOption->title }}</strong></span>
                                        <span>•</span>
                                        <span>{{ __('account.stake') }}: <strong>{{ number_format($userBet->amount) }} 🌰</strong></span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <flux:badge size="sm" color="indigo" class="mb-2">
                                        Quote: {{ number_format($userBet->betOption->odds, 2) }}x
                                    </flux:badge>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $userBet->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @else
            <flux:callout icon="information-circle" class="bg-zinc-100 dark:bg-zinc-800 border-zinc-300 dark:border-zinc-700">
                <div class="text-sm text-zinc-700 dark:text-zinc-300">
                    {{ __('account.no_bets') }}
                </div>
            </flux:callout>
        @endif
    </div>
</div>
