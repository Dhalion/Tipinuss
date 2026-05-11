@php /** @var \App\Models\Bet $bet */ @endphp

<div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-4 lg:px-6 py-6 sm:py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4 sm:mb-6">
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-2">
                    {{ __('app.title') }} – {{ __('app.hero_tagline') }}
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white mb-2 break-words">{{ $bet->title }}</h1>
                @if($bet->description)
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">{{ $bet->description }}</p>
                @endif
            </div>
            
            <div class="flex flex-wrap items-center gap-3 sm:shrink-0">
                <flux:badge :color="$bet->status->color()" size="lg">
                    {{ $bet->status->label() }}
                </flux:badge>
                
                @if(auth()->user()?->isAdmin())
                    <flux:select
                        wire:change="changeOrganisation($event.target.value)"
                        size="sm"
                        class="max-w-[180px]"
                    >
                        <option value="">{{ __('admin.organisations.no_group') }}</option>
                        @foreach($organisations as $org)
                            <option value="{{ $org->id }}" {{ $bet->organisation_id === $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </flux:select>
                @endif

                @if(auth()->check() && (auth()->id() === $bet->user_id || auth()->user()?->isAdmin()))
                    @if($bet->isOpen())
                        <div class="flex gap-2 flex-wrap">
                            <div x-data x-tooltip.raw="{{ $canCloseBet ? '' : __('bets.close_disabled_hint') }}">
                                <button 
                                    type="button"
                                    @if($canCloseBet)
                                        @click="$dispatch('open-close-bet-modal')"
                                    @endif
                                    @class([
                                        'inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg border-2 font-medium transition text-sm',
                                        'border-gold-400 bg-gold-50 dark:bg-gold-900/20 text-gold-700 dark:text-gold-300 hover:bg-gold-100 dark:hover:bg-gold-900/40 cursor-pointer' => $canCloseBet,
                                        'border-zinc-400 bg-zinc-50 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 cursor-not-allowed opacity-60' => !$canCloseBet,
                                    ])
                                >
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    {{ __('bets.close') }}
                                </button>
                            </div>
                            <button 
                                type="button"
                                @click="$dispatch('open-delete-bet-modal')"
                                class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg border-2 border-red-400 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 font-medium hover:bg-red-100 dark:hover:bg-red-900/40 transition text-sm"
                            >
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                {{ __('bets.delete') }}
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        
        <div class="flex gap-4 sm:gap-6 text-sm text-zinc-500 dark:text-zinc-400 flex-wrap">
            <span>{{ __('bets.created_by') }} <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $bet->creator->name }}</span></span>
            <span>{{ $bet->created_at->format('d.m.Y H:i') }}</span>
            @if($bet->expires_at)
                <span>{{ __('bets.expires') }} {{ $bet->expires_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>
</div>
