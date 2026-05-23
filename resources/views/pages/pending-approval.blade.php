<div class="flex-1 flex items-center justify-center px-4 py-4" wire:poll.10s="checkStatus">
    <div class="w-full max-w-sm text-center">

        <div class="mb-6">
            <a href="{{ route('main') }}" wire:navigate>
                <img src="{{ asset('images/tipinuss-waschnusskönig.webp') }}"
                     alt="{{ __('app.title') }}"
                     class="mx-auto h-16 w-auto mb-4" />
            </a>

            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="relative flex size-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-gold-400 opacity-75"></span>
                    <span class="relative inline-flex size-3 rounded-full bg-gold-500"></span>
                </span>
                <flux:heading size="sm" class="text-gold-500">{{ __('auth.pending_title') }}</flux:heading>
            </div>
        </div>

        <flux:card class="p-6">
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
                {{ __('auth.pending_description') }}
            </p>

            <div class="bg-gold-50 dark:bg-gold-900/20 border border-gold-200 dark:border-gold-800 rounded-lg p-3 mb-4">
                <div class="flex items-start gap-2">
                    <flux:icon name="clock" variant="solid" class="mt-0.5 h-4 w-4 shrink-0 text-gold-500" />
                    <p class="text-xs text-gold-700 dark:text-gold-300 text-left">
                        {{ __('auth.pending_hint') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-1.5 mb-4">
                <flux:icon name="arrow-path" class="h-3 w-3 animate-spin text-zinc-400" />
                <span class="text-xs text-zinc-400">{{ __('auth.pending_checking') }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('app.navigation.logout') }}
                </flux:button>
            </form>
        </flux:card>

        <p class="mt-6 text-xs text-zinc-400 dark:text-zinc-500">
            {{ __('app.title') }} &mdash; {{ __('auth.pending_footer') }}
        </p>

    </div>
</div>
