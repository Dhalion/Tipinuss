 @php /** @var \App\Models\User|null $authUser */ $authUser = auth()->user(); @endphp

<div class="py-8">
    <div class="max-w-4xl mx-auto px-4">

        <div class="mb-8">
            <flux:heading size="xl">{{ __('account.profile') }}</flux:heading>
            <flux:text class="mt-1">{{ __('account.manage_profile') }}</flux:text>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
            <flux:card class="lg:col-span-2">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/40 ring-2 ring-primary-300/50 dark:ring-primary-700/50">
                        <span class="text-xl font-bold text-primary-600 dark:text-primary-300">
                            {{ $authUser?->initials() ?? '?' }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                            {{ $authUser?->name ?? '' }}
                        </h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                            {{ $authUser?->email ?? '' }}
                        </p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 flex items-center gap-1.5">
                            <flux:icon name="calendar" class="h-3 w-3" />
                            {{ __('account.member_since') }} {{ $authUser?->created_at?->format('d. F Y') ?? '' }}
                        </p>
                    </div>
                </div>
            </flux:card>

            <flux:card class="bg-white dark:bg-zinc-800">
                <div class="text-center py-1">
                    <div class="text-sm font-medium text-gold-600 dark:text-gold-400 mb-1.5">
                        <flux:icon name="wallet" class="h-4 w-4 inline -mt-0.5 mr-1" />
                        {{ __('account.balance') }}
                    </div>
                    <div class="text-4xl font-black text-gold-600 dark:text-gold-400">
                        {{ number_format($authUser?->soapnuts ?? 0) }}
                    </div>
                    <div class="text-xl mt-1">🌰</div>
                </div>
            </flux:card>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                        <flux:icon name="chart-bar" class="h-5 w-5 text-primary-600 dark:text-primary-300" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                            {{ $totalBetsCount }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('account.total_bets_stat') }}
                        </div>
                    </div>
                </div>
            </flux:card>
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gold-100 dark:bg-gold-900/30">
                        <flux:icon name="trophy" class="h-5 w-5 text-gold-600 dark:text-gold-300" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-gold-600 dark:text-gold-400">
                            {{ $wonBetsCount }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('account.won_bets_stat') }}
                        </div>
                    </div>
                </div>
            </flux:card>
            <flux:card>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                        <flux:icon name="x-mark" class="h-5 w-5 text-red-600 dark:text-red-300" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $lostBetsCount }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ __('account.lost_bets_stat') }}
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>

        <flux:card class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                <flux:icon name="presentation-chart-bar" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">
                    {{ __('account.balance_chart_title') }}
                </h3>
            </div>
            <div
                data-balance-chart
                data-chart-data="{{ $chartDataJson }}"
                data-series-name="{{ __('account.balance') }}"
                data-empty-text="{{ __('account.balance_chart_empty') }}"
                class="w-full"
            >
                <div class="flex items-center justify-center h-80">
                    <svg class="h-6 w-6 animate-spin text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
            </div>
        </flux:card>

        @if ($historyEntries !== [])
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <flux:icon name="list-bullet" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                    <flux:heading size="lg">{{ __('account.transaction_history') }}</flux:heading>
                </div>

                <div class="space-y-2">
                    @php /** @var \App\DTOs\Account\TransactionHistoryEntry $entry */ @endphp
                    @foreach ($historyEntries as $entry)
                        @if($entry->betRoute)
                            <a href="{{ $entry->betRoute }}" wire:navigate.hover class="block transition hover:opacity-80">
                        @endif
                            <flux:card wire:key="entry-{{ $entry->id }}"
                                @class(['hover:shadow-md hover:border-primary-500/50' => $entry->betRoute])>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-zinc-900 dark:text-white truncate">
                                            {{ $entry->description }}
                                        </p>
                                        <p class="text-xs text-zinc-400 mt-0.5 flex items-center gap-1">
                                            <flux:icon name="clock" class="h-3 w-3" />
                                            {{ $entry->createdAt ? \Carbon\Carbon::parse($entry->createdAt)->diffForHumans() : '' }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0 space-y-1">
                                        <div class="font-bold {{ $entry->amount >= 0 ? 'text-gold-600 dark:text-gold-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $entry->amount >= 0 ? '+' : '' }}{{ number_format($entry->amount) }} 🌰
                                        </div>
                                        <flux:badge size="sm" :color="$entry->badgeColor">
                                            {{ $entry->badgeLabel }}
                                        </flux:badge>
                                    </div>
                                </div>
                            </flux:card>
                        @if($entry->betRoute)
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <flux:callout icon="information-circle" class="mb-8">
                <div class="text-sm text-zinc-700 dark:text-zinc-300">
                    {{ __('account.no_activity') }}
                </div>
            </flux:callout>
        @endif
    </div>
</div>
