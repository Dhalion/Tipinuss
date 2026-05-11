<div class="min-h-screen flex items-center justify-center bg-zinc-50 dark:bg-zinc-900 px-4">
    <flux:card class="w-full max-w-md">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('auth.register_title') }}</h2>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm mt-1">{{ __('auth.have_account') }}</p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <flux:input wire:model="name" label="{{ __('auth.name') }}" placeholder="John Doe" required />

            <flux:input wire:model="email" label="{{ __('auth.email') }}" type="email" placeholder="name@example.com"
                required />

            <flux:input wire:model="password" label="{{ __('auth.password') }}" type="password" required />

            <flux:input wire:model="password_confirmation" label="{{ __('auth.password_confirmation') }}"
                type="password" required />

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('auth.register_submit') }}
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