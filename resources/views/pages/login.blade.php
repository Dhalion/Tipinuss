<div class="min-h-screen flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 px-4">
    <div class="w-full max-w-md">
        <flux:card>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('auth.login_title') }}</h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">{{ __('auth.have_account') }}</p>
            </div>

            <form wire:submit="authenticate" class="space-y-4">
                <flux:input wire:model="email" label="{{ __('auth.email') }}" type="email"
                    placeholder="name@example.com" required />
                @error('email') <div class="text-sm text-tipinuss-red-600">{{ $message }}</div> @enderror

                <flux:input wire:model="password" label="{{ __('auth.password') }}" type="password" required />
                @error('password') <div class="text-sm text-tipinuss-red-600">{{ $message }}</div> @enderror

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('auth.login_submit') }}
                </flux:button>
            </form>

            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}"
                        class="font-semibold text-tipinuss-red-600 dark:text-tipinuss-red-400 hover:underline">
                        {{ __('auth.register_here') }}
                    </a>
                </p>
            </div>
        </flux:card>
    </div>
</div>