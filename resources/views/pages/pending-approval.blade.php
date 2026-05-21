<div class="flex-1 flex items-center justify-center px-4 py-4">
    <div class="w-full max-w-sm text-center">

        <div class="mb-6">
            <a href="{{ route('main') }}" wire:navigate>
                <img src="{{ asset('images/tipinuss-waschnusskönig.webp') }}"
                     alt="{{ __('app.title') }}"
                     class="mx-auto h-16 w-auto mb-4" />
            </a>
            <flux:icon name="clock" variant="solid" class="mx-auto h-12 w-12 text-gold-400" />
        </div>

        <flux:card class="p-6">
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">
                {{ __('auth.pending_title') }}
            </h1>

            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 leading-relaxed">
                {{ __('auth.pending_description') }}
            </p>

            <div class="bg-gold-50 dark:bg-gold-900/20 border border-gold-200 dark:border-gold-800 rounded-lg p-3 mb-4">
                <p class="text-xs text-gold-700 dark:text-gold-300">
                    {{ __('auth.pending_hint') }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" icon="arrow-right-start-on-rectangle">
                    {{ __('app.navigation.logout') }}
                </flux:button>
            </form>
        </flux:card>

        <p class="mt-6 text-xs text-zinc-400 dark:text-zinc-500">
            {{ __('app.title') }} &mdash; {{ __('auth.pending_footer') }}
        </p>

    </div>
</div>
