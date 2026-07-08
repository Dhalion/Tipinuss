<footer class="[grid-area:footer] border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col items-center gap-3">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                <a href="{{ route('legal.datenschutz') }}" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">{{ __('app.legal.datenschutz') }}</a>
                <a href="{{ route('legal.agb') }}" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">{{ __('app.legal.agb') }}</a>
                <span class="text-xs text-zinc-400 dark:text-zinc-500">·</span>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    &copy; {{ date('Y') }} {{ __('app.title') }}@appVersion
                </span>
                @if ($showBetaBadge)
                    <span class="inline-flex items-center gap-1 rounded-full bg-gold-100 dark:bg-gold-900/30 px-2.5 py-0.5 text-xs font-medium text-gold-700 dark:text-gold-300">
                        <flux:icon name="beaker" class="h-3 w-3" />
                        {{ __('app.beta_badge') }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 text-center max-w-5xl leading-relaxed">
                {{ __('app.legal.disclaimer') }}
            </p>
        </div>
    </div>
</footer>
