@php /** @var \App\Models\Bet $bet */ @endphp

<div class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 px-4 lg:px-6 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex-1">
                <div class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-2">
                    Tipinuss – Online Waschnusswetten
                </div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">{{ $bet->title }}</h1>
                @if($bet->description)
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">{{ $bet->description }}</p>
                @endif
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <flux:badge :color="$bet->status->color()" size="lg">
                    {{ $bet->status->label() }}
                </flux:badge>
                
                @if(auth()->check() && auth()->id() === $bet->creator_id)
                    <div class="flex gap-2">
                        <flux:button 
                            variant="outline" 
                            size="sm"
                            wire:click="closeBet"
                            wire:confirm="Wirklich schließen?"
                        >
                            {{ __('bets.close') }}
                        </flux:button>
                        <flux:button 
                            variant="danger" 
                            size="sm"
                            wire:click="deleteBet"
                            wire:confirm="Wirklich löschen?"
                        >
                            {{ __('bets.delete') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="flex gap-6 text-sm text-zinc-500 dark:text-zinc-400 flex-wrap">
            <span>{{ __('bets.created_by') }} <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $bet->creator->name }}</span></span>
            <span>{{ $bet->created_at->format('d.m.Y H:i') }}</span>
            @if($bet->expires_at)
                <span>{{ __('bets.expires') }} {{ $bet->expires_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>
</div>
