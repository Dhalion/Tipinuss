<div class="flex-1 flex items-center justify-center px-4 py-8">
    <flux:card class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ route('main') }}" wire:navigate>
                <img src="{{ asset('images/tipinuss-waschnusskönig.webp') }}"
                     alt="{{ __('app.title') }}"
                     class="mx-auto h-20 w-auto" />
            </a>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('auth.register_title') }}</h2>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">{{ __('auth.have_account') }}</p>
        </div>

        <form wire:submit="register" class="space-y-5">

            @if ((bool) config('app.beta_mode', false))
                <div class="space-y-3">
                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('auth.register_path_label') }}</p>

                    <label
                        class="flex items-start gap-3 rounded-lg border p-4 cursor-pointer transition {{ $hasBetaKey ? 'border-primary-400 bg-primary-50/50 dark:bg-primary-900/20 dark:border-primary-600' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800' }}"
                        wire:click="$set('hasBetaKey', true)"
                    >
                        <input type="radio" name="regPath" class="mt-1 accent-primary-600" {{ $hasBetaKey ? 'checked' : '' }}>
                        <div class="min-w-0">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ __('auth.register_path_key_title') }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ __('auth.register_path_key_desc') }}</div>
                        </div>
                    </label>

                    @if ($hasBetaKey)
                        <div class="ml-7 pl-1 border-l-2 border-primary-300 dark:border-primary-700">
                            <flux:input wire:model="betaKey" label="{{ __('auth.beta_key_label') }}"
                                placeholder="{{ __('auth.beta_key_placeholder') }}" class="mb-2" />

                            <div class="flex items-start gap-2 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700/60 px-3 py-2.5">
                                <flux:icon name="check-circle" variant="solid" class="mt-0.5 h-4 w-4 shrink-0 text-green-600 dark:text-green-300" />
                                <p class="text-xs text-green-700 dark:text-green-200">{{ __('auth.register_path_key_benefit') }}</p>
                            </div>
                        </div>
                    @endif

                    <label
                        class="flex items-start gap-3 rounded-lg border p-4 cursor-pointer transition {{ ! $hasBetaKey ? 'border-primary-400 bg-primary-50/50 dark:bg-primary-900/20 dark:border-primary-600' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800' }}"
                        wire:click="$set('hasBetaKey', false)"
                    >
                        <input type="radio" name="regPath" class="mt-1 accent-primary-600" {{ ! $hasBetaKey ? 'checked' : '' }}>
                        <div class="min-w-0">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ __('auth.register_path_no_key_title') }}</div>
                            <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ __('auth.register_path_no_key_desc') }}</div>
                        </div>
                    </label>

                    @if (! $hasBetaKey)
                        <div class="flex items-start gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 px-3 py-2.5">
                            <flux:icon name="clock" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400" />
                            <p class="text-xs text-amber-700 dark:text-amber-300">{{ __('auth.register_path_no_key_benefit') }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <flux:input wire:model="name" label="{{ __('auth.name') }}" placeholder="John Doe" required />

            <flux:input wire:model="email" label="{{ __('auth.email') }}" type="email" placeholder="name@example.com"
                required />

            <flux:input wire:model="password" label="{{ __('auth.password') }}" type="password" required />

            <flux:input wire:model="password_confirmation" label="{{ __('auth.password_confirmation') }}"
                type="password" required />

            <flux:button type="submit" variant="primary" class="w-full">
                @if ((bool) config('app.beta_mode', false) && $hasBetaKey)
                    {{ __('auth.register_submit_key') }}
                @elseif ((bool) config('app.beta_mode', false))
                    {{ __('auth.register_submit_pending') }}
                @else
                    {{ __('auth.register_submit') }}
                @endif
            </flux:button>
        </form>

        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
            <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('auth.have_account') }}
                <a href="{{ route('login') }}" wire:navigate
                    class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                    {{ __('auth.login_here') }}
                </a>
            </p>
        </div>
    </flux:card>
</div>