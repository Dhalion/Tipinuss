<footer class="[grid-area:footer] border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                &copy; {{ date('Y') }} {{ __('app.title') }}@appVersion.
            </p>

            <div class="flex items-center gap-4">
                @if ($showBetaBadge)
                    <span class="inline-flex items-center gap-1 rounded-full bg-gold-100 dark:bg-gold-900/30 px-2.5 py-0.5 text-xs font-medium text-gold-700 dark:text-gold-300">
                        <flux:icon name="beaker" class="h-3 w-3" />
                        {{ __('app.beta_badge') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</footer>
