@php /** @var \Illuminate\Support\Collection $latestNotifications */ @endphp

<flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" size="sm" class="relative" wire:click aria-label="{{ __('app.notifications') }}">
        <flux:icon name="bell" class="h-5 w-5" />
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </flux:button>

    <flux:menu class="w-80">
        <flux:menu.heading>{{ __('app.notifications') }}</flux:menu.heading>

        @forelse($latestNotifications as $notification)
            <flux:menu.item
                wire:key="notification-{{ $notification->id }}"
                href="{{ route('bets.detail', ['bet' => $notification->data['bet_slug_url']]) }}"
                wire:navigate
                wire:click="markAsRead('{{ $notification->id }}')"
            >
                <div class="min-w-0">
                    <div class="text-sm font-medium truncate">
                        @if(($notification->data['user_bet_status'] ?? '') === 'won')
                            🏆 {{ __('bets.bet_won') }}
                        @else
                            😢 {{ __('bets.bet_lost') }}
                        @endif
                    </div>
                    <div class="text-xs text-zinc-500 truncate">
                        {{ $notification->data['bet_title'] }}
                    </div>
                    <div class="text-xs text-zinc-400">
                        {{ number_format($notification->data['amount_wagered']) }} 🌰
                        @if(($notification->data['user_bet_status'] ?? '') === 'won')
                            → +{{ number_format($notification->data['potential_winnings']) }} 🌰
                        @endif
                    </div>
                </div>
            </flux:menu.item>
        @empty
            <flux:menu.item disabled>
                {{ __('app.no_notifications') }}
            </flux:menu.item>
        @endforelse
    </flux:menu>
</flux:dropdown>
